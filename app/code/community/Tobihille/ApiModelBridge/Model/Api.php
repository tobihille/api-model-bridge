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
          if ( !empty($joinEntity->fields) )
          {
            $collection->join(
              array($joinEntity->alias => $joinEntity->table),
              $joinEntity->condition,
              $joinEntity->fields
            );
          }
          else
          {
            $collection->join(
                array($joinEntity->alias => $joinEntity->table),
                $joinEntity->condition
            );
          }
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
        }
      }
    }

    if ( !empty($input->group) )
    {
      $select->group($input->group);
    }

    if ( !empty($input->limit) )
    {
      $select->limit($input->limit);
    }

    $data = Mage::getSingleton('core/resource')->
        getConnection('core_read')->
        fetchAll( $collection->getSelectSql() );

    return json_encode( $data );
  }

  public function putData($json)
  {
    $result = array();

    if ( !Mage::getStoreConfig('api/apimodelbridge/enable') )
    {
      return json_encode($result);
    }

    if ( !Mage::getStoreConfig('api/apimodelbridge/enable_put') )
    {
      return json_encode($result);
    }

    $input = json_decode($json);

    $ids = array();

    /**
     * @var Mage_Core_Model_Abstract $model
     */
    $model = Mage::getModel($input->main_table);

    foreach ($input->entity_data as $entity)
    {
      $model->clearInstance();

      foreach ($entity as $key => $value)
      {
        $model->setData($key, $value);
      }

      $model->save();

      $ids[] = $model->getId();
    }

    return json_encode($ids);
  }

  public function deleteData($json)
  {
    $result = array();

    if ( !Mage::getStoreConfig('api/apimodelbridge/enable') )
    {
      return json_encode($result);
    }

    if ( !Mage::getStoreConfig('api/apimodelbridge/enable_delete') )
    {
      return json_encode($result);
    }

    $input = json_decode($json);

    Mage::register('isSecureArea', true, true);

    $result = array();

    /**
     * @var Mage_Core_Model_Abstract $model
     */
    $model = Mage::getModel($input->main_table);

    foreach ($input->ids as $id)
    {
      try
      {
        $model->load($id);

        $model->delete();

        $result[] = true;
      }
      catch (Exception $e)
      {
        $result[] = false;
      }
    }

    return json_encode($result);
  }

}