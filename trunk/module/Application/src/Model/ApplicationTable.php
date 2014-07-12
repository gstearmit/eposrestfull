<?php

namespace Application\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class ApplicationTable extends AbstractTableGateway
{
    protected $table = 'appsatellite';

    public function __construct(Adapter $adapter)
    {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Application());

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

    public function getApplication($id)
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
    
    public function saveApplication(Application $Application)
    {
        $data = array(
	            'nameapp' => $Application->nameapp,
	            'title'  => $Application->title,
	        	'link'=>$Application->link,
        		'image_thumbnail'=>$Application->image_thumbnail,
        		'content_detail'=>$Application->content_detail,
        		'content_detail_full'=>$Application->content_detail_full,
        		'extend'=>$Application->extend,
        );

        $id = (int)$Application->id;
        if ($id == 0) {
            $this->insert($data);
            return $this->lastInsertValue;
        } else {
            if ($this->getApplication($id)) {
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

    public function deleteApplication($id)
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
    
    
   
    public function fetch_All_Applicationrest_Rest_Orderbyiddesc(Select $select = null) {
    	if (null === $select)
    		$select = new Select();
    	$select->from('appsatellite');
    	$select->where(array('appsatellite.nameapp = \'igirlxinhcom\''));
    	$select->order('id DESC');
    	$resultSet = $this->selectWith($select);
    	//return $resultSet;die;
    	$resultSet->buffer();
    	return $resultSet;
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
