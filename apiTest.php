<?php

require_once('app/Mage.php');

Mage::app('admin');

$input = array(
  'main_table' => 'sales/order',
  'join' => array(
    array(
      'table' => 'sales/order_item',
      'alias' => 'sfoi',
      'condition' => 'sfoi.order_id = main_table.entity_id',
      'fields' =>
        array('parent_item_id',
          'product_id',
          'name'
        )
    ),
    array(
      'table' => 'catalog/product',
      'alias' => 'cpe',
      'condition' => 'cpe.sku = sfoi.sku')
  ),
  'where' => array(
    'sfoi.product_type' => array('eq' => 'simple'),
    'cpe.type_id' => array('nin' => array('bunde', 'configurable'))
  ),
  'limit' => 42
);

$data = Mage::getModel('apimodelbridge/api')->fetchData( json_encode($input) );

echo $data;