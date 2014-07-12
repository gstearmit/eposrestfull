<?php

namespace Template\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class TemplateTable extends AbstractTableGateway
{
    protected $table = 'appsatellite';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Template());

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

    public function getTemplate($id)
    {
        $id  = (int) $id;
        $rowset = $this->select(array('id' => $id));
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
    }
    
   
    
    public function getPhototamtayvn($id)
    {
    	
    	$id = (int) $id;
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array('src'=>'src')); //,'idforeign'=>'idforeign'
    	$select->from ('contentdetailfull')
    	       ->join('appsatellite', 'contentdetailfull.idforeign= appsatellite.id',array()); //array('id'=>'id','nameapp'=>'nameapp','title'=>'title','link'=>'link','image_thumbnail'=>'image_thumbnail','content_detail'=>'content_detail','content_detail_full'=>'content_detail_full')
    	 
    	$select->where(array('contentdetailfull.idforeign'=>$id));
    	$selectString = $sql->prepareStatementForSqlObject($select);
    	
    	//return $selectString ;die;
    	
    	$results = $selectString->execute();
    	// swap
    	$array = array();
    	foreach ($results as $result)
    	{
    		$array[] = $result;
    	}
    
    	return $array;
    
    }
    
    public function getIgirlxinhid($id)
    {
    	 
    	$id = (int) $id;
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array('src'=>'src')); //,'idforeign'=>'idforeign'
    	$select->from ('contentdetailfull')
    	->join('appsatellite', 'contentdetailfull.idforeign= appsatellite.id',array()); //array('id'=>'id','nameapp'=>'nameapp','title'=>'title','link'=>'link','image_thumbnail'=>'image_thumbnail','content_detail'=>'content_detail','content_detail_full'=>'content_detail_full')
    
    	$select->where(array('contentdetailfull.idforeign'=>$id));
    	$selectString = $sql->prepareStatementForSqlObject($select);
    	 
    	//return $selectString ;die;
    	 
    	$results = $selectString->execute();
    	// swap
    	$array = array();
    	foreach ($results as $result)
    	{
    		$array[] = $result;
    	}
    
    	return $array;
    
    }
    
    public function saveTemplate(Template $Template)
    {
        $data = array(
	            'nameapp' => $Template->nameapp,
	            'title'  => $Template->title,
	        	'link'=>$Template->link,
        		'image_thumbnail'=>$Template->image_thumbnail,
        		'content_detail'=>$Template->content_detail,
        		'content_detail_full'=>$Template->content_detail_full,
        		'extend'=>$Template->extend,
        );

        $id = (int)$Template->id;
        if ($id == 0) {
            $this->insert($data);
            return $this->lastInsertValue;
        } else {
            if ($this->getTemplate($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }
    
    public function saveContent_detail_full($content_detail_full = Array() , $id_tamtay)
    {
    	//return $content_detail_full;
    	
    	$idtamtay = (int) $id_tamtay;
    	if (is_array($content_detail_full) and !empty($content_detail_full))
    	{
    		
    		foreach ($content_detail_full as $key => $srcurl)
    		{
    			
    			$dbAdapter = $this->adapter;
    			$sql       = "INSERT INTO contentdetailfull (idforeign,src)
                              VALUES ('".$idtamtay."','".$srcurl."')";
    
    			$statement = $dbAdapter->query($sql);
    			//return $statement; die;
    			$result    = $statement->execute();
    			
    		}
    
    		return $result = 1;
    	}else
    		return $result = Null;
    
    }

    public function deleteTemplate($id)
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
    
    
   
    public function fetchAllTemplate(Select $select = null) {
    	
    	//return $select="asasasas";die;
    	
    	if (null === $select)
    	$select = new Select();
    	$select->from('appsatellite');
    	$conditions = array('appsatellite.nameapp = \'igirlxinhcom\'','appsatellite.nameapp = \'phototamtayvn\'');
    	$select->where($conditions,\Zend\Db\Sql\Predicate\PredicateSet::OP_OR);
    	
    	$select->order('id DESC');
    	$resultSet = $this->selectWith($select);
    	//return $resultSet;die;
    	$resultSet->buffer();
    	return $resultSet;
    }
    
    public function fetchAllVIEW(Select $select = null) {
    	//return $selectString="Hoang Phuc" ;die;
    	if (null === $select)
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	//$select->columns(array()); //,'idforeign'=>'idforeign'
    	$select->from('appsatellite');
    	$conditions = array('appsatellite.nameapp = \'igirlxinhcom\'','appsatellite.nameapp = \'phototamtayvn\'');
    	$select->where($conditions,\Zend\Db\Sql\Predicate\PredicateSet::OP_OR);
    	$select->order('id DESC');
    	
    	$selectString = $sql->prepareStatementForSqlObject($select);
    	 
    	return $selectString ;die;
    	 
    	$results = $selectString->execute();
    	// swap
    	$array = array();
    	foreach ($results as $result)
    	{
    		$array[] = $result;
    	}
    
    	return $array;
    }
    
    
    public function fetchhotgirl(Select $select = null) {
    	if (null === $select)
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	//$select->columns(array()); //,'idforeign'=>'idforeign'
    	$select->from('appsatellite');
    	$conditions = array('appsatellite.nameapp = \'igirlxinhcom\'');
    	$select->where($conditions);
    	$select->order('id DESC');
    	 
    	$selectString = $sql->prepareStatementForSqlObject($select);
    
    	//return $selectString ;die;
    
    	$results = $selectString->execute();
    	// swap
    	$array = array();
    	foreach ($results as $result)
    	{
    		$array[] = $result;
    	}
    
    	return $array;
    }
    
    //$phototamtay
    public function fetchphototamtay(Select $select = null) {
    	if (null === $select)
    		$limit = 1;
    	$sql = new Sql($this->adapter);
    	
    	$select = $sql->select();
    	//$select->columns(array()); //,'idforeign'=>'idforeign'
    	$select->from('appsatellite');
    	$conditions = array('appsatellite.nameapp = \'phototamtayvn\'');
    	//$sql = "id >= (SELECT FLOOR( MAX(id) * RAND()) FROM `appsatellite` )";
    	$rand = new \Zend\Db\Sql\Expression('RAND()');
    	$select->where($conditions);
    	$select->order($rand);
    
    	$selectString = $sql->prepareStatementForSqlObject($select);
    
    	//return $selectString ;die;
    
    	$results = $selectString->execute();
    	// swap
    	$array = array();
    	foreach ($results as $result)
    	{
    		$array[] = $result;
    	}
    
    	return $array;
    }
    
    //$phototamtay
    public function fetchgetid($id) {
    	$id = (int) $id;
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array('src'=>'src')); //,'idforeign'=>'idforeign'
    	$select->from ('contentdetailfull')
    	->join('appsatellite', 'contentdetailfull.idforeign= appsatellite.id',array('title'=>'title','content_detail'=>'content_detail')); //array('id'=>'id','nameapp'=>'nameapp','title'=>'title','link'=>'link','image_thumbnail'=>'image_thumbnail','content_detail'=>'content_detail','content_detail_full'=>'content_detail_full')
    
    	$select->where(array('contentdetailfull.idforeign'=>$id));
    	$selectString = $sql->prepareStatementForSqlObject($select);
    	 
    	//return $selectString ;die;
    	 
    	$results = $selectString->execute();
    	// swap
    	$array = array();
    	foreach ($results as $result)
    	{
    		$array[] = $result;
    	}
    
    	return $array;
    }
    
    
    public function fetch_All_Phototamtayvnrest_Rest_Orderbyiddesc(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from('appsatellite');
    	$select->where(array('appsatellite.nameapp = \'phototamtayvn\''));
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
