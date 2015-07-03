<?php

require_once('app/Mage.php');

Mage::app('admin');

$input = array(
  'main_table' => 'sales/order',
  'join' => array(
    array('sales/order_item', 'sfoi', 'sfoi.order_id = main_table.entity_id'),
    array('catalog/product', 'cpe', 'cpe.sku = sfoi.sku')
  ),
  'where' => array(
    'sfoi.product_type' => array('eq' => 'simple'),
    'cpe.type_id' => array('nin' => array('bunde', 'configurable'))
  ),
  'limit' => 42
);

$data = Mage::getModel('apimodelbridge/api')->fetchData( json_encode($input) );

echo $data;