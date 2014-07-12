<?php

namespace Uploadmagazinevietnam\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class UploadmagazinevietnamTable extends AbstractTableGateway {

    protected $table = 'magazinevietnam';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Uploadmagazinevietnam());

        $this->initialize();
    }

    public function fetchAll(Select $select = null) 
    {
        if (null === $select)
        $select = new Select();
        $select->from($this->table);
        $select->order('id ASC');
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
    
 
    public function fetchAllDetailuploaddetail( $id)
    {
    	$id = (int) $id;
	
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array('title'=>'title','descriptionkey'=>'descriptionkey','patient_id'=>'patient_id'));
    	$select->columns(array());
    	$select->from ('upload')
    	       ->join('uploaddetail', 'uploaddetail.idmz=upload.id',array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    	$select->where(array('upload.id'=>$id));
    	$select->order('id ASC');
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
    
    public function fetchAllOrderbyiddesc(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from($this->table);
    	$select->order('id DESC'); 
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    	return $resultSet;
    }

    public function getUpload($id) {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
       
       
    }
    
    
    public function getRestUpload($id) {
    	$id = (int) $id;
    
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	//$select->columns(array('id'=>'id','title'=>'title','descriptionkey'=>'descriptionkey','imgkey'=>'imgkey'));
    	$select->columns(array());
    	$select->from ('upload')
    	->join('uploaddetail', 'uploaddetail.idmz=upload.id',array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    	$select->where(array('upload.id'=>$id));
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
    
    public function getReadUploadmagazinevietnamdetail($id)
    {
    	$id = (int) $id;
    	
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	//$select->columns(array('id'=>'id','title'=>'title','descriptionkey'=>'descriptionkey','imgkey'=>'imgkey'));
    	$select->columns(array());
    	$select->from ('magazinevietnam')
    	->join('mgvndetail', 'mgvndetail.idmz=magazinevietnam.id',array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    	$select->where(array('magazinevietnam.id'=>$id));
    	$sort = 'id ASC';
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

    public function saveUploadmagazinevietnam(Uploadmagazinevietnam $upload) {
        $data = array(
            'descriptionkey' => $upload->descriptionkey,
        	'imgkey' => $upload->imgkey,
            'title' => $upload->title,
        	'patient_id' => $upload->patient_id,
        		
        );

        $id = (int) $upload->id;
        if ($id == 0) {
            $this->insert($data);
            return $this->lastInsertValue;    
        } else {
            if ($this->getUpload($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    
    public function saveUpload2(Upload $upload) {
    	
    	
    	$data = array(
    			'descriptionkey' => $upload->descriptionkey,
    			'imgkey' => $upload->imgkey,
    			'title' => $upload->title,
    			'patient_id' => $upload->patient_id,
    	);
    	
    	$id = (int) $upload->id;
    	if ($id == 0) {
    		$this->insert($data);
    	} else {
    		if ($this->getUpload($id)) {
    			$this->update($data, array('id' => $id));
    		} else {
    			throw new \Exception('Form id does not exist');
    		}
    	}
    	

    	
    }

    public function deleteUpload($id) {
        $this->delete(array('id' => $id));
    }

    
    public function getInsertUploadDetail($arrayImages = Array() , $id , $namefolder)
    {
    	$id = (int) $id;
    	if (is_array($arrayImages) and !empty($arrayImages))
    	{
    		$i = 1;
    		foreach ($arrayImages as $key => $imgvalue)
    		{
    			$img = $namefolder.'/'.$imgvalue;
    			$dbAdapter = $this->adapter;
    			$sql       = "INSERT INTO mgvndetail (idmz,img,description,title,page)
                              VALUES ('".$id."','".$img."','','','".$i."')";
                          
    		    $statement = $dbAdapter->query($sql);
    			//return $statement; die; 
    			$result    = $statement->execute();
    			$i++;
    		}
    		
    		return $result = 1;
    	}else 
    		return $result = Null;
    
    }
    
    public function getReadUploaddetail($id)
    {
    	$id = (int) $id;
    
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array());
    	$select->from ('upload')
    	->join('uploaddetail', 'uploaddetail.idmz=upload.id',array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    	$select->where(array('upload.id'=>$id));
    	$sort[] = 'id ASC';
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
    
}
