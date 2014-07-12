<?php


namespace Mgvndetail\Form;


use Zend\Form\Form;

class MultiUploadForm extends Form
{
    public function __construct($name = null)
    {
        parent::__construct('appForm');
	
        $this->setAttribute('method', 'post');       
      
        $this->setAttribute('enctype','multipart/form-data');

        $this->add(array(
            'name' => 'picture1',
            'attributes' => array(
                'type'  => 'file',
				'required' => 'required',
            	'class' => 'txtInput txtMedium'
            ),
            'options' => array(
                'label' => 'File upload 1:',
            ),
        ));
        
        $this->add(array(
        		'name' => 'picture2',
        		'attributes' => array(
        				'type'  => 'file',
        				'required' => 'required',
        				'class' => 'txtInput txtMedium'
        		),
        		'options' => array(
        				'label' => 'File upload 2:',
        		),
        ));
     
        $this->add(array(
        		'name' => 'submit',
        		'attributes' => array(
        				'type'  => 'submit',
        				'value' => 'Send data'
        		),
        ));
        
    }
    
    public function upload($files = array(),$file_path = ''){
    	
    	$fileName = array();
    	
    	if(count($files) != 0 && $file_path != ''){
    			    	
	    	$uploadObj = new \Zend\File\Transfer\Adapter\Http();
	    	$uploadObj->setDestination($file_path);
	    	foreach ($files as $key => $val){
	    		$uploadObj->receive($key);
	    		$fileName[$key]= $val['name'];
	    	}
    	}
    	
    	return $fileName;
    }
}
