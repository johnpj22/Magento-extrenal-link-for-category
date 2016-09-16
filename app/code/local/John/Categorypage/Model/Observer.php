<?php
/**
 * Observer for handling Top menu items
 * it overrides the default magento top menu observer
 * This is to use cms page url as category url for adding features like landing page of a category
 * 
 * 
 * @author John Varghese
 * @package John_Categorypage
 */

class John_Categorypage_Model_Observer
{

    /**
     * Adds catalog categories to top menu
     *
     * @param Varien_Event_Observer $observer            
     */
    public function addCatalogToTopmenuItems(Varien_Event_Observer $observer)
    {
        $block = $observer->getEvent()->getBlock();
        $block->addCacheTag(Mage_Catalog_Model_Category::CACHE_TAG);
        $this->_addCategoriesToMenu(Mage::helper('catalog/category')->getStoreCategories(), $observer->getMenu(), $block, true);
    }

    /**
     * Recursively adds categories to top menu
     *
     * @param Varien_Data_Tree_Node_Collection|array $categories            
     * @param Varien_Data_Tree_Node $parentCategoryNode            
     * @param Mage_Page_Block_Html_Topmenu $menuBlock            
     * @param bool $addTags            
     */
    protected function _addCategoriesToMenu($categories, $parentCategoryNode, $menuBlock, $addTags = false)
    {
        $categoryModel = Mage::getModel('catalog/category');
        foreach ($categories as $category) {
            if (! $category->getIsActive()) {
                continue;
            }
            
            $nodeId = 'category-node-' . $category->getId();
            
            $categoryModel->setId($category->getId());
            if ($addTags) {
                $menuBlock->addModelTags($categoryModel);
            }
            
            $tree = $parentCategoryNode->getTree();  
            $categoryM = Mage::getModel('catalog/category')->load($category->getId());            
            $caturl = $categoryM->getCmsPageUrlKey();
            if (! $caturl) {
                $caturl = Mage::helper('catalog/category')->getCategoryUrl($category);
            } else {
                $caturl = Mage::getUrl($caturl);
            }
            $categoryData = array(
                'name' => $category->getName(),
                'id' => $nodeId,
                'url' => $caturl,
                'is_active' => $this->_isActiveMenuCategory($category)
            );
            
            $categoryNode = new Varien_Data_Tree_Node($categoryData, 'id', $tree, $parentCategoryNode);
            $parentCategoryNode->addChild($categoryNode);
            
            $flatHelper = Mage::helper('catalog/category_flat');
            if ($flatHelper->isEnabled() && $flatHelper->isBuilt(true)) {
                $subcategories = (array) $category->getChildrenNodes();
            } else {
                $subcategories = $category->getChildren();
            }
            
            $this->_addCategoriesToMenu($subcategories, $categoryNode, $menuBlock, $addTags);
        }
    }

    /**
     * Checks whether category belongs to active category's path
     *
     * @param Varien_Data_Tree_Node $category            
     * @return bool
     */
    protected function _isActiveMenuCategory($category)
    {
        $catalogLayer = Mage::getSingleton('catalog/layer');
        if (! $catalogLayer) {
            return false;
        }
        
        $currentCategory = $catalogLayer->getCurrentCategory();
        if (! $currentCategory) {
            return false;
        }
        
        $categoryPathIds = explode(',', $currentCategory->getPathInStore());
        return in_array($category->getId(), $categoryPathIds);
    }
}