<?php
namespace Comments\Form;

use Zend\Form\Form;
// use Zend\Db\TableGateway\AbstractTableGateway;
// use Zend\Db\Adapter\Adapter;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class CommentsForm extends Form
{
	protected $adapter;
    public function __construct(AdapterInterface $dbAdapter)
    {
    	$this->adapter =$dbAdapter;
        // we want to ignore the name passed
        parent::__construct('comments');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

    //
        $this->add(array(
        		'name' => 'user_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'status',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
        
        $this->add(array(
        		'name' => 'appsatellite_id',
        		'attributes' => array(
        				'type'  => 'hidden',
        		),
        ));
      
        $this->add(array(
        		'name' => 'content',
        		'attributes' => array(
        				'type'  => 'textarea',
        		),
        		'options' => array(
        				'label' => 'Description',
        		),
        ));
        
        $this->add(array(
        		'name' => 'title',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => 'Title',
        		),
        ));
        
        
        $this->add(array(
        		'name' => 'name',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => 'Name',
        		),
        ));
        
        
        $this->add(array(
        		'name' => 'email',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => 'Email',
        		),
        ));
        
       
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            	'class' => 'btn btn-primary',
            ),
        ));

    }
    
    public function getOptionsForSelect()
    {
    	$dbAdapter = $this->adapter;
    	$sql       = 'SELECT * FROM `magazinevietnam` WHERE 1';
    	$statement = $dbAdapter->query($sql);
    	$result    = $statement->execute();
    
    	$selectData = array();
    
    	foreach ($result as $res) {
    		$selectData[$res['id']] = $res['title'];
    	}
    	return $selectData;
    }
    
    
    public function fetchAllCatalogue() {
    	$sql = new Sql($this->adapter);
    	$select = $sql->select();
    	//$select->columns(array('id'=>'id','title'=>'title','descriptionkey'=>'descriptionkey','imgkey'=>'imgkey'));
    	$select->columns(array());
    	$select->from ('mgvndetail')
    	->join('magazinevietnam', 'mgvndetail.idmz=magazinevietnam.id',array('id'=>'id','title'=>'title'));
    	//$select->where(array('magazinevietnam.id'=>$id));
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
    		$tmp[$result['id']]= $result['title'];
    		$array[] = $tmp;
    	}

    	return $array;
    
    }
}
