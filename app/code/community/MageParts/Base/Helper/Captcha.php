<?php
/**
 * MageParts
 *
 * NOTICE OF LICENSE
 *
 * This code is copyrighted by MageParts and may not be reproduced
 * and/or redistributed without a written permission by the copyright
 * owners. If you wish to modify and/or redistribute this file please
 * contact us at info@mageparts.com for confirmation before doing
 * so. Please note that you are free to modify this file for personal
 * use only.
 *
 * If you wish to make modifications to this file we advice you to use
 * the "local" file scope in order to aviod conflicts with future updates.
 * For information regarding modifications see http://www.magentocommerce.com.
 *
 * DISCLAIMER
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT OWNER OR CONTRIBUTORS BE LIABLE
 * FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES
 * (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF
 * USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY
 * OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE
 * OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED
 * OF THE POSSIBILITY OF SUCH DAMAGE.
 *
 * @category   MageParts
 * @package    MageParts_Base
 * @copyright  Copyright (c) 2009 MageParts (http://www.mageparts.com/)
 * @author     MageParts Crew
 */

class MageParts_Base_Helper_Captcha extends MageParts_Base_Helper_Data
{

    /**
     * Retrieve captcha API public key
     *
     * @return string
     */
    public function getPublicKey()
    {
        return Mage::getStoreConfig('mageparts_base/captcha/public_key');
    }

    /**
     * Retrieve captcha API private key
     *
     * @return string
     */
    public function getPrivateKey()
    {
        return Mage::getStoreConfig('mageparts_base/captcha/private_key');
    }

    /**
     * Validate captcha
     *
     * @param string $challenge
     * @param string $input
     * @return array
     */
    public function validate($challenge, $input)
    {
        $result = array(
            'valid' => true,
            'error' => ''
        );

        $call = recaptcha_check_answer($this->getPrivateKey(), $this->getClientIp(), $challenge, $input);

        if (!$call->is_valid) {
            $result['valid'] = false;
            $result['error'] = $call->error == 'incorrect-captcha-sol' ? $this->__(Mage::getStoreConfig('mageparts_base/captcha/err_wrong_code')) : $this->__(Mage::getStoreConfig('mageparts_base/captcha/err_default'));
        }

        return $result;
    }

    /**
     * Retrieve configured theme for reCaptcha box.
     *
     * @return string
     */
    public function getTheme()
    {
        return Mage::getStoreConfig('mageparts_base/captcha/theme');
    }

    /**
     * Check if debugger is enabled or not for captcha functions.
     *
     * @return bool
     */
    public function isDebugEnabled()
    {
        return parent::isDebugEnabled() && Mage::getStoreConfigFlag('mageparts_base/debug/captcha');
    }

}
