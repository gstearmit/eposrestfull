<?php
namespace Catalogue\Form;

use Zend\Form\Form;

// use Zend\Captcha;
// use Zend\Form\Element;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\Sql\Select;
use Zend\Db\Sql\Sql;
use Zend\Db\Sql\Where;

use Zend\Db\Adapter\AdapterInterface;
use Zend\Db\Adapter\Adapter;

class CatalogueForm extends Form
{
	protected $id;
	protected $adapter;
    public function __construct(AdapterInterface $dbAdapter ,$id = Null)
    {
        $this->adapter =$dbAdapter;
        $this->id = (int)$id;
        parent::__construct('catalogue');

        $this->setAttribute('method', 'post');
       // $this->setAttribute('class', 'form-horizontal');
        $this->setAttribute('enctype', 'multipart/form-data');
        $this->add(array(
            'name' => 'id',
            'attributes' => array(
                'type'  => 'hidden',
            ),
        ));
       

        $this->add(array(
            'name' => 'descriptionkey',
            'attributes' => array(
                'type'  => 'textarea',
            ),
            'options' => array(
                'label' => 'Description',
            ),
        ));
        
        $this->add(array(
        		'name' => 'url_rest',
        		'attributes' => array(
        				'type'  => 'text',
        				'readonly' => TRUE,
        		),
        		'options' => array(
        				'label' => 'url Rest ',
        		),
        ));
        
        $this->add(array(
        		'name' => 'url_catalogue',
        		'attributes' => array(
        				'type'  => 'text',
        		),
        		'options' => array(
        				'label' => 'Url catalogue ',
        		),
        ));

        $this->add(array(
            'name' => 'title',
            'attributes' => array(
                'type'  => 'text',
            ),
            'options' => array(
                'label' => 'Name',
            ),
        ));
        
        $this->add(array(
        		'type' => 'Zend\Form\Element\Select',
        		'name' => 'patient_id',
        		'options' => array(
        				'label' => 'Select a category father ',
        				'empty_option' => 'Please Select a category father',
        				'value_options' => $this->getNameCatalogueForSelect()
        		),
        		'attributes' => array(
        				'value' => '0', //set selected to '1'
        				'inarrayvalidator' => true,
        				
        		)
        ));
        $this->add(array(
        		'name' => 'imgkey',
        		'attributes' => array(
        				'type'  => 'file',
        		),
        		'options' => array(
        				'label' => 'Upload images description ',
        		),
        ));
   
        $this->add(array(
        		'name' => 'imgkeyedit',
        		'attributes' => array(
        				'type'  => 'file',
        		),
        		'options' => array(
        				'label' => 'Upload images description ',
        		),
        ));
       
        
        
        $this->add(array(
            'name' => 'submit',
            'attributes' => array(
                'type'  => 'submit',
                'value' => 'Go',
                'id' => 'submitbutton',
            	'class'=>"btn btn-primary",
            ),
        ));

    }
    
    public function getNameCatalogueForSelect()
    {
    	$dbAdapter = $this->adapter;
    	$sql       = 'SELECT * FROM `catalogue`';
    	$statement = $dbAdapter->query($sql);
    	$result    = $statement->execute();
    
    	$selectData = array();
    
    	foreach ($result as $res) {
    		$selectData[$res['id']] = $res['title'];
    	}
    	return $selectData;
    }
}
