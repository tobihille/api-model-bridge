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