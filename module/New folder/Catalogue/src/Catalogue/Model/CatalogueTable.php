<?php

namespace Catalogue\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class CatalogueTable extends AbstractTableGateway {

    protected $table = 'catalogue';

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Catalogue());

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
    
    //fetchAllDetailMzimg
   // public function fetchAllDetailMzimg( $id , Select $select = null)
    public function fetchAllDetailMzimg( $id)
    {
    	$id = (int) $id;
		    //	if (null === $select) $select = new Select();
		    	
		//     	$select->from($this->table);
		//     	$select->order('id ASC');
		//     	$resultSet = $this->selectWith($select);
		//     	$resultSet->buffer();
		//     	return $resultSet;
		    	
    	

    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array('title'=>'title','descriptionkey'=>'descriptionkey'));
    	$select->columns(array());
    	$select->from ('catalogue')
    	       ->join('mzimg', 'mzimg.idmz=catalogue.id',array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    	$select->where(array('catalogue.id'=>$id));
    	$select->order('id ASC');
       // $resultSet = $this->selectWith($select);
    	//$resultSet->buffer();
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
	    	$select->where(array('url_rest = \'\''));
	    	$select->order('id DESC'); 
	       // $sort[] = 'sort_order DESC';
	    	//     	$sort[] = 'value ASC';
	    	//     	$select->order($sort);
	    	$resultSet = $this->selectWith($select);
	    	$resultSet->buffer();
	    	return $resultSet;
    }
    
    public function fetchAllOrderbyidDESCUrlRest(Select $select = null) 
    {
    	if (null === $select)
    		$select = new Select();
    	$select->from($this->table);
    	$select->where(array('url_rest != \'\''));
    	$select->order('id DESC');
    
    	$resultSet = $this->selectWith($select);
    	$resultSet->buffer();
    	return $resultSet;
    }

    public function getCatalogue($id) {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
       
       
    }
    
    
    public function getRestCatalogueNewsReport($id)
     {
    	
    	$id = (int) $id;
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array('id'=>'id','name'=>'name','brief'=>'brief','description'=>'description','img'=>'image_url'));
    	$select->from (array('e' => 'news'))
    	       ->join(array('r' => 'news'), 'e.category_id= r.id',array(),'left')//; //->group('e.id');
    	       ->join(array('c' => 'catalogue'), 'e.category_id= c.id',array('name_category'=>'title'),'right'); //->group('e.id');
    	$select->where(array('e.category_id'=>$id));
  	    $sort = 'e.id DESC';
     	$select->order($sort);
    
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
    
    
    public function getRestCatalogue($id) {
    	$id = (int) $id;
        $url_rest = 'url_rest != \'\' ';
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	
    	$select->columns(array('id'=>'id','title'=>'title','descriptionkey'=>'descriptionkey','imgkey'=>'imgkey','url_catalogue'=>'url_catalogue','url_rest'=>'url_rest','patient_id'=>'patient_id'));
    	$select->from ('catalogue');
    	$select->where(array('catalogue.id'=>$id,'url_rest != \'\''));
    
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


    public function saveCatalogue(Catalogue $catalogue) {
        $data = array(
            'descriptionkey' => $catalogue->descriptionkey,
        	'imgkey' => $catalogue->imgkey,
            'title' => $catalogue->title,
        	'patient_id' => $catalogue->patient_id,
        	'url_catalogue' => $catalogue->url_catalogue,
        	'url_rest' => $catalogue->url_rest,
        );

        $id = (int) $catalogue->id;
        if ($id == 0) {
            $this->insert($data);
        } else {
            if ($this->getCatalogue($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    
    public function saveCatalogue2(Catalogue $catalogue) {
    	
    	
    	$data = array(
    			'descriptionkey' => $catalogue->descriptionkey,
    			'imgkey' => $catalogue->imgkey,
    			'title' => $catalogue->title,
    			'patient_id' => $catalogue->patient_id,
    			'url_catalogue' => $catalogue->url_catalogue,
    			'url_rest' => $catalogue->url_rest,
    	);
    	
    	$id = (int) $catalogue->id;
    	if ($id == 0) {
    		$this->insert($data);
    	} else {
    		if ($this->getCatalogue($id)) {
    			$this->update($data, array('id' => $id));
    		} else {
    			throw new \Exception('Form id does not exist');
    		}
    	}
    	
//  	die;
//     	return var_dump(is_array($catalogue->imgkey));
//     	die;
    	
    	
//     	if (is_array($catalogue->imgkey)) 
//     	{
//     	  	foreach ($catalogue->imgkey as $key)
//     	  	{
//     	  		$arrayMa_Img = array();
//     	  		$arrayMa_Img = $key['name'];
//     	  	}
    	  	
//     	  	$data = array(
//     	  			'descriptionkey' => $catalogue->descriptionkey,
//     	  			'imgkey' =>$arrayMa_Img,
//     	  			'title' => $catalogue->title,
//     	  	);
    	  	
//     	  	$id = (int) $catalogue->id;
//     	  	if ($id == 0) {
//     	  		$this->insert($data);
//     	  	} else {
//     	  		if ($this->getCatalogue($id)) {
//     	  			$this->update($data, array('id' => $id));
//     	  		} else {
//     	  			throw new \Exception('Form id does not exist');
//     	  		}
//     	  	}
//     	}else 
//     	{
//     		$data = array(
//     				'descriptionkey' => $catalogue->descriptionkey,
//     				'imgkey' => 'detaa',
//     				'title' => $catalogue->title,
//     		);
    		
//     		$id = (int) $catalogue->id;
//     		if ($id == 0) {
//     			$this->insert($data);
//     		} else {
//     			if ($this->getCatalogue($id)) {
//     				$this->update($data, array('id' => $id));
//     			} else {
//     				throw new \Exception('Form id does not exist');
//     			}
//     		}
//     	}
    	
    	//return 
    	
    	
    	
    }

    public function deleteCatalogue($id) {
        $this->delete(array('id' => $id));
    }

}
