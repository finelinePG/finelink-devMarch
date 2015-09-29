<?php
class MGS_Megamenu_Block_Vertical extends MGS_Megamenu_Block_Abstract
{
	public function getMegamenuItems(){
		$store = Mage::app()->getStore();
		$menuCollection = Mage::getModel('megamenu/megamenu')
			->getCollection()
			->addStoreFilter($store)
			->addFieldToFilter('parent_id', $this->getMenuId())
			->addFieldToFilter('status', 1)
			->setOrder('position', 'ASC')
		;
		return $menuCollection;
	}
	
	public function getClass($item){
		$type = $item->getMenuType();
		$class = $item->getSpecialClass();
		$class.=' '.$item->getAlignMenu();
		if($item->getColumns()>1){
			$class.= " mega-menu-item mega-menu-fullwidth";
		}
		if($type==2){
			$class.= " static-menu level0 parent";
			$currentUrl = Mage::helper("core/url")->getCurrentUrl();
			if($currentUrl==$item->getUrl()){
				$class.= " active";
			}
			
			if($item->getStaticContent()!=''){
				$class.= ' dropdown';
			}
		}
		else{

			$categoryId = $item->getCategoryId();
			$category = Mage::getModel('catalog/category')->load($categoryId);
			$subCatAccepp = $this->getSubCategoryAccepp($categoryId, $item);
			
			$class.= " category-menu level0 parent";
			
			if(count($subCatAccepp)>0){
				$class.= ' dropdown';
			}
			if ($this->isCategoryActive($category)) {
				if(Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId() != $category->getId()){
					$class.= " active";
				}
			}
			
		}
		return $class;
	}
	
	public function getStaticMenu($item){
		$html = '<a href="'.$item->getUrl().'" class="level0';
		if($item->getStaticContent()!=''){
			$html.= ' dropdown-toggle';
		}
		$html.= '">';
		
		if($item->getHtmlLabel()!=''){
			$html.=$item->getHtmlLabel();
		}
		
		$html.='<span class="menu-title">';
		$html.=$item->getTitle();
		$html.='</span>';
		
		if($item->getStaticContent()!=''){
			$html.= ' <span class="icon-next hidden-sm hidden-xs"><i class="fa fa-angle-right"></i></span>';
		}
		$html.='</a>';
		if($item->getStaticContent()!=''){
			$html.='<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="toggleEl(\'mobile-menu-'.$item->getId().$this->getMenuId().'\'); mgsjQuery(this).toggleClass(\'collapse\'); mgsjQuery(\'#mobile-menu-'.$item->getId().$this->getMenuId().'\').toggleClass(\'active\');" href="javascript:void(0)" class=""><i class="fa fa-sort-down"></i></a></span>';
			$html .= '<div class="mega-content-wrap dropdown-menu" id="mobile-menu-'.$item->getId().$this->getMenuId().'">';
			$html .= '<div class="submenu">';
			$col = $item->getColumns();
			$percentOfWidth = 100/Mage::getStoreConfig('megamenu/general/max_column');

			$helper = Mage::helper('cms');
			$processor = $helper->getPageTemplateProcessor();
			$staticContent = $processor->filter($item->getStaticContent());
			
			$html.= $staticContent;

			$html .= '</div>';
			$html .= '</div>';
		}
		return $html;
	}
	
	public function getCategoryMenu($item){
		$html = '<a';
		$categoryId = $item->getCategoryId();
		$subCatAccepp = $this->getSubCategoryAccepp($categoryId, $item);
		if($categoryId){
			$category = Mage::getModel('catalog/category')->load($categoryId);
			$html.=' href="';
			if($item->getUrl()!=''){
				$html.= $item->getUrl().'"';
			}
			else{
				if(Mage::app()->getWebsite(true)->getDefaultStore()->getRootCategoryId() == $category->getId()){
					$html.='#" onclick="return false"';
				}
				else{
					$html.= $this->getCategoryUrl($category).'"';
				}
			}
		}
		
		if(count($subCatAccepp)>0){
			$html.= ' class="dropdown-toggle';
		}
		
		$html.='">';
		if($item->getHtmlLabel()!=''){
			$html.=$item->getHtmlLabel();
		}
		$html.='<span class="menu-title">';
		$html.=$item->getTitle();
		$html.='</span>';
		if(count($subCatAccepp)>0){
			$html.= ' <span class="icon-next hidden-sm hidden-xs"><i class="fa fa-angle-right"></i></span>';
		}
		$html.= '</a>';
		if(count($subCatAccepp)>0 || $item->getTopContent()!='' || $item->getBottomContent()!=''){
			$html.='<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="toggleEl(\'mobile-menu-'.$item->getId().'-'.$this->getMenuId().'\'); mgsjQuery(this).toggleClass(\'collapse\'); mgsjQuery(\'#mobile-menu-'.$item->getId().'-'.$this->getMenuId().'\').toggleClass(\'active\');" href="javascript:void(0)" class=""><i class="fa fa-sort-down"></i></a></span>';
			$html .= '<div class="mega-content-wrap dropdown-menu" id="mobile-menu-'.$item->getId().'-'.$this->getMenuId().'">';
			$html .= '<div class="submenu">';
			$columnAccepp = count($subCatAccepp);
			if($columnAccepp>0){
				$columns = $item->getColumns();

				$arrOneElement = array_chunk($subCatAccepp, 1);
				$countCat = count($subCatAccepp);
				$count = 0;
				while ($countCat > 0) {
					for($i=0; $i<$columns; $i++){
						if(isset($subCatAccepp[$count])){
							$arrColumn[$i][] = $subCatAccepp[$count];
							$count++;
						}
					}
					$countCat--;
				}
				
				$newArrColumn = array();
				$newCount = 0;
				
				for($i=0; $i<count($arrColumn); $i++){
					$newColumn = count($arrColumn[$i]);
					for($j=0; $j<$newColumn; $j++){
						$newArrColumn[$i][$j] = $subCatAccepp[$newCount];
						$newCount++;
					}
				}
				
				$arrColumn = $newArrColumn;
				
				$helper = Mage::helper('cms');
				$processor = $helper->getPageTemplateProcessor();
				
				if($columns>1){
					$html.= '<div class="mega-menu-content">';
					
					if($item->getTopContent()!=''){
						$html.='<div class="top_content static-content col-md-12">';
						$html.= $processor->filter($item->getTopContent());
						$html.='</div>';
					}
					
				}
				/*else {
					$html.= '<ul>';
				}*/
				foreach($arrColumn as $_arrColumn){
					$html.= $this->drawListSub($item, $_arrColumn);
				}
				
				if($columns>1){
					
					if($item->getBottomContent()!=''){
						$html.='<div class="bottom_content static-content col-md-12">';
						$html.= $processor->filter($item->getBottomContent());
						$html.='</div>';
					}
					
					$html.= '</div>';
				}
				/*else {
					$html.= '</ul>';
				}*/

			}

			$html .= '</div>';
			$html .= '</div>';
		}
		return $html;
	}
	
	public function drawListSub($item, $catIds){
		$html = '';
	
		$html.='<div class="col-md-'.$this->getColumnByCol($item->getColumns()).' col-sm-'.$this->getColumnByCol($item->getColumns()).' col-xs-12 block nav-category"><ul>';

		if(count($catIds)>0){
			foreach($catIds as $categoryId){
				$category = Mage::getModel('catalog/category')->load($categoryId);
				$html.= $this->drawList($category, $item);
			}
		}
		
		$html.='</ul></div>';
		
		return $html;
	}
	
	public function drawList($category, $item, $level=1){
		$maxLevel = $item->getMaxLevel();
		if($maxLevel=='' || $maxLevel==NULL){
			$maxLevel = Mage::getStoreConfig('megamenu/general/max_level');
		}
		
		if($maxLevel==0 || $maxLevel=='' || $maxLevel==NULL){
			$maxLevel = 100;
		}
		
		$children = $this->getSubCategoryAccepp($category->getId(), $item);
		$childrenCount = count($children);
		
		$htmlLi = '<li';
		$htmlLi .= ' class="';
		if($childrenCount>0 && $item->getColumns()==1){
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
		$html[] = '<a href="'.$this->getCategoryUrl($category).'">';
		
		$html[] = $category->getName();
		
		$html[] = '</a>';
		
		if($level<$maxLevel){
			
			
			$maxSub = Mage::getStoreConfig('megamenu/general/max_subcat');
			if($maxSub==0 || $maxSub==''){
				$maxSub = 100;
			}
			$htmlChildren = '';
			if($childrenCount>0){
				$i=0;
				foreach ($children as $child) {
					$i++;
					if($i<=$maxSub){
						$_child = Mage::getModel('catalog/category')->load($child);
						$htmlChildren .= $this->drawList($_child, $item, ($level + 1));
					}
				}
			}
			if (!empty($htmlChildren)) {
				$html[] = '<span class="toggle-menu visible-xs-block visible-sm-block"><a onclick="toggleEl(\'mobile-menu-cat-'.$category->getId().$this->getMenuId().'\'); mgsjQuery(this).toggleClass(\'collapse\'); mgsjQuery(\'#mobile-menu-cat-'.$category->getId().$this->getMenuId().'\').toggleClass(\'active\');" href="javascript:void(0)" class=""></a></span>';
				
				$html[] = '<ul id="mobile-menu-cat-'.$category->getId().$this->getMenuId().'">';				
				$html[] = $htmlChildren;
				$html[] = '</ul>';
			}
		}
        $html[] = '</li>';
        $html = implode("\n", $html);
        return $html;
	}
}