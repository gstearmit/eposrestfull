<?php

namespace Uploadfilemutil\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class UploadfilemutilTable extends AbstractTableGateway {

    protected $table = 'magazinepublish';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Uploadfilemutil());

        $this->initialize();
    }

    public function fetchAll(Select $select = null) {
        if (null === $select)
        $select = new Select();
        $select->from($this->table);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
    
    public function fetchAllOrderbyiddesc(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from($this->table);
    	$select->order('id DESC'); 
       // $sort[] = 'sort_order DESC';
    	//     	$sort[] = 'value ASC';
    	//     	$select->order($sort);
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    	return $resultSet;
    }

    public function getUploadfilemutil($id) {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
       
       
    }
    
    
    public function getRestUploadfilemutil($id) {
    	$id = (int) $id;
    
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	//$select->columns(array('id'=>'id','title'=>'title','descriptionkey'=>'descriptionkey','imgkey'=>'imgkey'));
    	$select->columns(array());
    	$select->from ('magazinepublish')
    	->join('mzimg', 'mzimg.idmz=magazinepublish.id',array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    	$select->where(array('magazinepublish.id'=>$id));
 //   	$sort[] = 'id DESC';
//     	$sort[] = 'value ASC';
 //    	$select->order($sort);
    
    	$selectString = $sql->prepareStatementForSqlObject($select);
    	//return $selectString;die;
    	$results = $selectString->execute();
    	 
    	// swap
    	$array = array();
    	foreach ($results as $result)
    	{
    		$tmp = array();
    		$tmp= $result;
    		$array[] = $tmp;
    	}
    
    	return $array;
    	 
    }

    public function saveUploadfilemutil(Uploadfilemutil $uploadfilemutil) {
        $data = array(
            'descriptionkey' => $uploadfilemutil->descriptionkey,
        	'imgkey' => $uploadfilemutil->imgkey,
            'title' => $uploadfilemutil->title,
        );

        $id = (int) $uploadfilemutil->id;
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getUploadfilemutil($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteUploadfilemutil($id) {
        $this->delete(array('id' => $id));
    }

}
