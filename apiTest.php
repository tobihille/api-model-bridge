<?php

$argv = $_GET;

require_once('app/Mage.php');

Mage::app('admin');

Mage::register('custom_entry_point', true, true);
$apihost = Mage::helper('core/url')->getHomeUrl().'api/v2_soap?wsdl=1';

$file = fopen( dirname(__FILE__).'/image.png', 'r');
$imageData = base64_encode( fgets($file) );
fclose($file);

$select = array(
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
  'limit' => 42,
  'group' => array(
    'main_table.entity_id',
    'cpe.sku'
  )
);

$putInsert = array(
  'main_table' => 'catalog/product',
    //note that you can not join other tables/models in insert or update-mode
  'entity_data' => array(
    array(
      'sku' => 'abc0001', //please note that in case of catalog/product you don't even need an entity_id to update, but this is not usual
      'type' => 'simple',
      'attribute_set_id' => 4,
      'small_image' => $imageData,
      'thumbnail' => $imageData,
      'visibility' => 4,
      'price' => 19.99,
      'name' => 'Sample Product',
      'short_description' => 'foo',
      'description' => 'bar',
      'status' => 1,
      'tax_class_id' => 0,
      'qty' => 5,
      'is_in_stock' => 1,
      'website_ids' => array(1),
    ),
    array(
      'sku' => 'abcdef001',
      'type' => 'simple',
      'attribute_set_id' => 4,
      'small_image' => $imageData,
      'thumbnail' => $imageData,
      'visibility' => 1,
      'price' => 42.23,
      'name' => 'Sample Product 2',
      'short_description' => 'fooX',
      'description' => 'barX',
      'status' => 2,
      'tax_class_id' => 1,
      'qty' => 1,
      'is_in_stock' => 0,
    )
  ),
);

$putUpdate = $putInsert;
//the only difference - magento finds the data by their id which is in most cases in entity_id
$putUpdate['entity_data'][0]['entity_id'] = 1000;
$putUpdate['entity_data'][1]['entity_id'] = 1042;

$delete = array(
  'main_table' => 'catalog/product',
  'ids' => array(
    '1000',
    '1042'
  )
);

if ( $argv['mode'] == 'model')
{
  if ( $argv['method'] == 'get' )
  {
    $data = Mage::getModel('apimodelbridge/api')->fetchData( json_encode($select) );
    echo $data;
  }
  elseif ($argv['method'] == 'put')
  {
    $data = Mage::getModel('apimodelbridge/api')->putData( json_encode($putInsert) );
    echo $data;

    $data = Mage::getModel('apimodelbridge/api')->putData( json_encode($putUpdate) );
    echo $data;
  }
  elseif ($argv['method'] == 'delete')
  {
    $data = Mage::getModel('apimodelbridge/api')->deleteData( json_encode($delete) );
    echo $data;
  }
  else
  {
    echo "Method not provided or incorrect, allowed: get, put, delete";
  }
}
else //API
{
  $username = $argv["user"];
  $password = $argv["pass"];

  try
  {
    $soap = new SoapClient($apihost, array('cache_wsdl' => WSDL_CACHE_NONE));
    $sess = $soap->login($username, $password);

    if ( $argv['method'] == 'get' )
    {
      $result = $soap->apimodelbridgeGet($sess, json_encode($select) );
      echo $result;
    }
    elseif ($argv['method'] == 'put')
    {
      $result = $soap->apimodelbridgePut($sess, json_encode($putInsert) );
      echo $result;

      $result = $soap->apimodelbridgePut($sess, json_encode($putUpdate) );
      echo $result;
    }
    elseif ($argv['method'] == 'delete')
    {
      $result = $soap->apimodelbridgeDelete($sess, json_encode($delete) );
      echo $result;
    }
    else
    {
      echo "Method not provided or incorrect, allowed: get, put, delete";
    }

    echo "\ncomplete\n";
  }
  catch (Exception $e)
  {
    unset($e->xdebug_message);
    var_dump($e);
  }
}