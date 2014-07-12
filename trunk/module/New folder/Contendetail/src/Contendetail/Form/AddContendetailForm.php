<?php
namespace Contendetail\Form;

use Zend\Form\Form;

use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class AddContendetailForm extends Form
{
	protected $adapter;
	protected $id;
	
    public function __construct(AdapterInterface $dbAdapter , $id = Null)
    {
    	$this->adapter =$dbAdapter;
    	$this->id = (int)$id;
       
        parent::__construct('contendetail');

        $this->setAttribute('method', 'post');
        $this->setAttribute('enctype', 'multipart/form-data');
        
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));

       $id_default  = ($this->id)== 0 ? '1' : $this->id;
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Select',
        		'name' => 'idforeign',
        		'options' => array(
        				'label' => 'Magazine Name',
        				'empty_option' => 'Select an Magazine',
        				//'value_options' => $this->fetchAllCatalogue()
        				'value_options' => $this->getOptionsForSelect()
        		),
        		'attributes' => array(
        				'value' => $id_default, //set selected to '1'
        				'inarrayvalidator' => true,
        				//'required' => 'required',
        		)
        ));
        
        $this->add(array(
            'name' => 'src',
            'attributes' => array(
                'type'  => 'file',
            ),
            'options' => array(
                'label' => 'Upload images',
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
    
    public function setdefault($id)
    {
    	$result ;
    	if(!$id)
    	{
    		return $id;
    	}else {
    		$result = 1; // default is 1;
    	}
    }
    
    
    public function getDefault($id)
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
    	->join('magazinevietnam', 'mgvndetail.idmz = magazinevietnam.id',array('id'=>'id','title'=>'title'));
    	//$select->where(array('magazinevietnam.id'=>$id));
    
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
