<?php

class Tobihille_ApiModelBridge_Model_Api extends Mage_Api_Model_Resource_Abstract
{

  public function fetchData($json)
  {
    $data = array();

    if ( !Mage::getStoreConfig('api/apimodelbridge/enable') )
    {
      return json_encode($data);
    }

    $input = json_decode($json);

    /**
     * @var Varien_Db_Select $select
     * @var Mage_Core_Model_Resource_Db_Collection_Abstract $collection
     */
    $collection = Mage::getModel($input->main_table)->getCollection();

    if ( !empty($input->join) )
    {
      if ( is_array($input->join) )
      {
        foreach ($input->join as $joinEntity)
        {
          //                      alias             entity           condition
          $collection->join(array($joinEntity[1] => $joinEntity[0]), $joinEntity[2]);
        }
      }
    }

    $select = $collection->getSelect();

    if ( !empty($input->where) )
    {
      foreach($input->where as $field => $condition)
      {
        foreach ($condition as $mode => $value)
        {
          $collection->addFieldToFilter($field, array($mode => $value));
          //$select->where( $field, $value, $mode );
        }
      }
    }

    if ( !empty($input->limit) )
    {
      $select->limit($input->limit);
    }

    $data = $select.' ';

    return json_encode($data);
  }

  public function putData($json)
  {
    $result = false;

    if ( !Mage::getStoreConfig('api/apimodelbridge/enable') )
    {
      return $result;
    }

    return $result;
  }

  public function deleteData($json)
  {
    $result = false;

    if ( !Mage::getStoreConfig('api/apimodelbridge/enable') )
    {
      return $result;
    }

    return $result;
  }

}