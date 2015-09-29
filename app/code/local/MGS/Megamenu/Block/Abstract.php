<?php

abstract class MGS_Megamenu_Block_Abstract extends Mage_Catalog_Block_Navigation {

    public function getClass($item) {
        $type = $item->getMenuType();
        $class = "level0 parent ";
        $class .= $item->getSpecialClass();
        $class .=' ' . $item->getAlignMenu();
        if ($item->getColumns() > 1) {
            $class.= " mega-menu-item mega-menu-fullwidth";
        }
        if ($type == 2) {
            $class.= " static-menu";
            $currentUrl = Mage::helper("core/url")->getCurrentUrl();
            if ($currentUrl == $item->getUrl()) {
                $class.= " active";
            }

            if ($item->getStaticContent() != '') {
                $class.= ' dropdown';
            }
        } else {

            $categoryId = $item->getCategoryId();
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $subCatAccepp = $this->getSubCategoryAccepp($categoryId, $item);

            $class.= " category-menu";

            if (count($subCatAccepp) > 0) {
                $class.= ' dropdown';
            }
            if ($this->isCategoryActive($category)) {
                if (Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId() != $category->getId()) {
                    $class.= " active";
                }
            }
        }
        return $class;
    }

    public function getMenuHtml($item) {
        $type = $item->getMenuType();
        if ($type == 2) {
            return $this->getStaticMenu($item);
        } else {
            return $this->getCategoryMenu($item);
        }
    }

    public function getStaticMenu($item) {
        $html = '<span class="links-menu';
        if ($item->getStaticContent() != '') {
            $html.= ' dropdown-toggle';
        }
        $html.= '">';
        if ($item->getHtmlLabel() != '') {
            $html.=$item->getHtmlLabel();
        }
        $html.='<a href="' . $item->getUrl() . '">';
        $html.=$item->getTitle();
        $html.='</a>';
        if ($item->getStaticContent() != '') {
            //$html.= ' <span class="icon-next"><i class="fa fa-long-arrow-right"></i></span>';
        }
        $html.='</span>';
        if ($item->getStaticContent() != '') {
            $html.='<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="toggleEl(\'mobile-menu-' . $item->getId() . $this->getMenuId() . '\'); mgsjQuery(this).toggleClass(\'collapse\'); mgsjQuery(\'#mobile-menu-' . $item->getId() . $this->getMenuId() . '\').toggleClass(\'active\');" href="javascript:void(0)" class=""><i class="fa fa-sort-down"></i></a></span>';
            $html .= '<i class="fa fa-angle-right"></i>';
            $html .= '<div class="mega-content-wrap dropdown-menu" id="mobile-menu-' . $item->getId() . $this->getMenuId() . '">';
            $html .= '<div class="submenu toggler-menu">';
            $col = $item->getColumns();
            $percentOfWidth = 100 / Mage::getStoreConfig('megamenu/general/max_column');

            $helper = Mage::helper('cms');
            $processor = $helper->getPageTemplateProcessor();
            $staticContent = $processor->filter($item->getStaticContent());

            $html.= $staticContent;
            $html .= '</div>';
            $html .= '</div>';
        }
        return $html;
    }

    public function getColumnByCol($col) {
        switch ($col) {
            case 1:
                return 12;
                break;
            case 2:
                return 6;
                break;
            case 3:
                return 4;
                break;
            case 4:
                return 3;
                break;
            case 6:
                return 2;
                break;
            default:
                return 1;
                break;
        }
    }

    public function getCategoryMenu($item) {
        $categoryId = $item->getCategoryId();
        $subCatAccepp = $this->getSubCategoryAccepp($categoryId, $item);
        $html = '<span class="links-menu';
        if (count($subCatAccepp) > 0) {
            $html.= ' dropdown-toggle';
        }
        $html.='">';
        if ($item->getHtmlLabel() != '') {
            $html.=$item->getHtmlLabel();
        }
        $html.='<a';
        //$html.=$item->getTitle();

        if ($categoryId) {
            $category = Mage::getModel('catalog/category')->load($categoryId);
            $html.=' href="';
            if ($item->getUrl() != '') {
                $html.= $item->getUrl() . '"';
            } else {
                if (Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId() == $category->getId()) {
                    $html.='#" onclick="return false"';
                } else {
                    $html.= $this->getCategoryUrl($category) . '"';
                }
            }
        }

        $html.='>';

        $html.=$item->getTitle();
        $html.='</a>';
        if (count($subCatAccepp) > 0) {
            //$html.= ' <span class="icon-next"><i class="fa fa-long-arrow-right"></i></span>';
        }
        $html.= '</span>';

        if (count($subCatAccepp) > 0 || $item->getTopContent() != '' || $item->getBottomContent() != '') {
            $html.='<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="toggleEl(\'mobile-menu-' . $item->getId() .'-'. $this->getMenuId() . '\'); mgsjQuery(this).toggleClass(\'collapse\'); mgsjQuery(\'#mobile-menu-' . $item->getId() .'-'. $this->getMenuId() . '\').toggleClass(\'active\');" href="javascript:void(0)" class=""><i class="fa fa-sort-down"></i></a></span>';
            $html .= '<i class="fa fa-angle-right"></i>';
            $html .= '<div class="mega-content-wrap dropdown-menu" id="mobile-menu-' . $item->getId() .'-'. $this->getMenuId() . '">';
            $html .= '<div class="submenu toggler-menu">';
            $columnAccepp = count($subCatAccepp);
            if ($columnAccepp > 0) {
                $columns = $item->getColumns();

                $arrOneElement = array_chunk($subCatAccepp, 1);
                $countCat = count($subCatAccepp);
                $count = 0;
                while ($countCat > 0) {
                    for ($i = 0; $i < $columns; $i++) {
                        if (isset($subCatAccepp[$count])) {
                            $arrColumn[$i][] = $subCatAccepp[$count];
                            $count++;
                        }
                    }
                    $countCat--;
                }

                $newArrColumn = array();
                $newCount = 0;

                for ($i = 0; $i < count($arrColumn); $i++) {
                    $newColumn = count($arrColumn[$i]);
                    for ($j = 0; $j < $newColumn; $j++) {
                        $newArrColumn[$i][$j] = $subCatAccepp[$newCount];
                        $newCount++;
                    }
                }

                $arrColumn = $newArrColumn;

                $helper = Mage::helper('cms');
                $processor = $helper->getPageTemplateProcessor();

                if ($columns > 1) {
                    $html.= '<div class="mega-menu-content">';

                    if ($item->getTopContent() != '') {
                        $html.='<div class="top_content static-content col-md-12">';
                        $html.= $processor->filter($item->getTopContent());
                        $html.='</div>';
                    }
                } else {
                    $html.= '<div class="nav-category"><ul>';
                }
                foreach ($arrColumn as $_arrColumn) {
                    $html.= $this->drawListSub($item, $_arrColumn);
                }

                if ($columns > 1) {

                    if ($item->getBottomContent() != '') {
                        $html.='<div class="bottom_content static-content col-md-12">';
                        $html.= $processor->filter($item->getBottomContent());
                        $html.='</div>';
                    }

                    $html.= '</div>';
                } else {
                    $html.= '</ul></div>';
                }
            }


            $html .= '</div>';
            $html .= '</div>';
        }

        return $html;
    }

    public function drawListSub($item, $catIds) {
        $html = '';

        if ($item->getColumns() > 1) {
            $html.='<div class="col-md-' . $this->getColumnByCol($item->getColumns()) . ' col-sm-12 col-xs-12 block nav-category"><ul>';
        }

        if (count($catIds) > 0) {
            foreach ($catIds as $categoryId) {
                $category = Mage::getModel('catalog/category')->load($categoryId);
                $html.= $this->drawList($category, $item);
            }
        }

        if ($item->getColumns() > 1) {
            $html.='</ul></div>';
        }

        return $html;
    }

    public function drawList($category, $item, $level = 1) {
        $maxLevel = $item->getMaxLevel();
        if ($maxLevel == '' || $maxLevel == NULL) {
            $maxLevel = Mage::getStoreConfig('megamenu/general/max_level');
        }

        if ($maxLevel == 0 || $maxLevel == '' || $maxLevel == NULL) {
            $maxLevel = 100;
        }

        $children = $this->getSubCategoryAccepp($category->getId(), $item);
        $childrenCount = count($children);

        $htmlLi = '<li';

        $htmlLi .= ' class="';
        if ($childrenCount > 0 && $item->getColumns() == 1) {
            $htmlLi .= 'dropdown-submenu ';
        }
        if($item->getColumns()==1){
			$level = $level + 1;
			$htmlLi .= 'level'.$level;
		}
		else {
			$htmlLi .= 'level'.$level;
		}

        $htmlLi .= '">';

        $html[] = $htmlLi;
        $html[] = '<a href="' . $this->getCategoryUrl($category) . '">';

        $html[] = $category->getName();

        $html[] = '</a>';

        if ($level < $maxLevel) {


            $maxSub = Mage::getStoreConfig('megamenu/general/max_subcat');
            if ($maxSub == 0 || $maxSub == '') {
                $maxSub = 100;
            }
            $htmlChildren = '';
            if ($childrenCount > 0) {
                $i = 0;
                foreach ($children as $child) {
                    $i++;
                    if ($i <= $maxSub) {
                        $_child = Mage::getModel('catalog/category')->load($child);
                        $htmlChildren .= $this->drawList($_child, $item, ($level + 1));
                    }
                }
            }
            if (!empty($htmlChildren)) {
                $html[] = '<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="toggleEl(\'mobile-menu-cat-' . $category->getId() . $this->getMenuId() . '\'); mgsjQuery(this).toggleClass(\'collapse\'); mgsjQuery(\'#mobile-menu-cat-' . $category->getId() . $this->getMenuId() . '\').toggleClass(\'active\');" href="javascript:void(0)" class=""></a></span>';
                $html[] = '<i class="fa fa-long-arrow-right"></i>';
                $html[] = '<ul id="mobile-menu-cat-' . $category->getId() . $this->getMenuId() . '">';
                $html[] = $htmlChildren;
                $html[] = '</ul>';
            }
        }
        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
    }

    public function getSubCategoryAccepp($categoryId, $item) {
        $subCatExist = explode(',', $item->getSubCategoryIds());

        $category = Mage::getModel('catalog/category')->load($categoryId);

        $children = $category->getChildrenCategories();
        $childrenCount = count($children);

        $subCatId = array();
        if ($childrenCount > 0) {
            foreach ($children as $child) {
                if (in_array($child->getId(), $subCatExist)) {
                    $subCatId[] = $child->getId();
                }
            }
        }
        return $subCatId;
    }

}
