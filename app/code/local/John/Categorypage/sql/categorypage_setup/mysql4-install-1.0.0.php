<?php
/**
 * For Creating Category Attribute
 * it creates cms_page_url_key attributes to category 
 * Need to Reindex category flat data
 *
 *
 * @author John Varghese
 * @package John_Categorypage
 */
$installer = $this;
$installer->startSetup();

$installer->addAttribute('catalog_category', 'cms_page_url_key', array(
    'type' => 'varchar',
    'backend' => '',
    'frontend' => '',
    'label' => 'CMS Page URL key',
    'input' => 'text',
    'class' => '',
    'source' => '',
    'global' => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
    'visible' => true,
    'required' => false,
    'user_defined' => true,
    'default' => '',
    'searchable' => false,
    'filterable' => false,
    'comparable' => false,
    'visible_on_front' => true,
    'unique' => false,
    'group' => 'General Information'
));

$entityTypeId = $installer->getEntityTypeId('catalog_category');
$attributeSetId = $installer->getDefaultAttributeSetId($entityTypeId);
$attributeGroupId = $installer->getDefaultAttributeGroupId($entityTypeId, $attributeSetId);

$installer->addAttributeToGroup($entityTypeId, $attributeSetId, $attributeGroupId, 'cms_page_url_key', '11');

$installer->endSetup();