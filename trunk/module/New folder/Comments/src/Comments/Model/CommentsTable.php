<?php


// public $id;
// public $title;
// public $name;

// public $content;
// public $appsatellite_id;
// //public $contentdetailull_id;
// public $user_id;
// public $email;
// public $created;
// public $status;

namespace Comments\Model;

use Zend\Db\TableGateway\AbstractTableGateway;
use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

class CommentsTable extends AbstractTableGateway {

    protected $table = 'comments';
    

    public function __construct(Adapter $adapter) {
        $this->adapter = $adapter;
        $this->resultSetPrototype = new ResultSet();
        $this->resultSetPrototype->setArrayObjectPrototype(new Comments());

        $this->initialize();
    }

    public function fetchAll(Select $select = null) 
    {
        if (null === $select)
        $select = new Select();
        $select->from($this->table);
        $resultSet = $this->selectWith($select);
        $resultSet->buffer();
        return $resultSet;
    }
    
    public function fetchAllJoih(Select $select = null)
    {
    	
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	
    	$select->from($this->table);
    	$select->columns(array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    
    	$select->join('magazinevietnam', 'mgvndetail.idmz=magazinevietnam.id',array('titlemagazinevietnam'=>'title','descriptionkeymagazinevietnam'=>'descriptionkey'));
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
    
    public function fetchAllDetailComments( $id)
    {
    	$id = (int) $id;
    	 
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	$select->columns(array('title'=>'title','descriptionkey'=>'descriptionkey'));//,'patient_id'=>'patient_id'
    	//$select->columns(array());
    	$select->from ('magazinevietnam')
    	->join('mgvndetail', 'mgvndetail.idmz=magazinevietnam.id',array('id'=>'id','img'=>'img','description'=>'description','title'=>'title','page'=>'page'));
    	$select->where(array('magazinevietnam.id'=>$id));
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

    public function getComments($id) {
        $id = (int) $id;
        $rowset = $this->select(array('id' => $id));
        
        $row = $rowset->current();
        if (!$row) {
            throw new \Exception("Could not find row $id");
        }
        return $row;
       
       
    }

    public function saveComments(Comments $comments) {
        $data = array(
            'idforeign' => $comments->idforeign,
        	'src' => $comments->src,
        );
        

        $id = (int) $comments->id;
        if ($id == 0) {
            $this->insert($data);
            return $this->lastInsertValue;
        } else {
            if ($this->getComments($id)) {
                $this->update($data, array('id' => $id));
            } else {
                throw new \Exception('Form id does not exist');
            }
        }
    }

    public function deleteComments($id) 
    {
        $this->delete(array('id' => $id));
    }
    

}
