<?php

namespace Wattpad\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;

class WattpadTable extends AbstractTableGateway
{
    protected $table = 'appsatellite';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Wattpad());

        $this->initialize();
    }

    /*
    public function fetchAll()
    {
        $resultSet = $this->select();
        return $resultSet;
    }
    */
    public function fetchAll(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from($this->table);
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    	return $resultSet;
    }

    public function getWattpad($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }

    public function saveWattpad(Wattpad $wattpad)
    {
        $data = array(
	            'nameapp' => $wattpad->nameapp,
	            'title'  => $wattpad->title,
	        	'link'=>$wattpad->link,
        		'image_thumbnail'=>$wattpad->image_thumbnail,
        		'content_detail'=>$wattpad->content_detail,
        		'content_detail_full'=>$wattpad->content_detail_full,
        		'extend'=>$wattpad->extend,
        );

        $id = (int)$wattpad->id;
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getWattpad($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteWattpad($id)
    {
        $this->delete(array('id' => $id));
    }
    //zf2
    public function fetch_All_zf2_Rest_Orderbyiddesc(Select $select = null) {
    	if (null === $select)
    	$select = new Select();
    	$select->from('appsatellite');
    	$select->where(array('appsatellite.nameapp = \'zf2\''));
    	$select->order('id DESC');
    	$resultSet = $this->selectWith($select);
    	//return $resultSet;die;
    	$resultSet->buffer();
    	return $resultSet;
    }
    //Mincecraft
    public function fetch_All_Appmincecraftrest_Rest_Orderbyiddesc(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from('appsatellite');
    	$select->where(array('appsatellite.nameapp = \'minecraftmodscom\''));
    	$select->order('id DESC');
    	$resultSet = $this->selectWith($select);
    	//return $resultSet;die;
    	$resultSet->buffer();
    	return $resultSet;
    }
    
    
    //fetch_All_Apphaivltv_Rest_Orderbyiddesc
    public function fetch_All_Apphaivltv_Rest_Orderbyiddesc(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from('appsatellite');
    	$select->where(array('appsatellite.nameapp = \'haivltv\''));
    	$select->order('id DESC');
    	$resultSet = $this->selectWith($select);
    	//return $resultSet;die;
    	$resultSet->buffer();
    	return $resultSet;
    }
    
    // rest haivl.com
    public function fetch_All_Apphaivlcom_Rest_Orderbyiddesc(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from('appsatellite');
    	$select->where(array('appsatellite.nameapp = \'haivlcom\''));
    	$select->order('id DESC');
    	$resultSet = $this->selectWith($select);
    	//return $resultSet;die;
    	$resultSet->buffer();
    	return $resultSet;
    }
  
    
    
}
