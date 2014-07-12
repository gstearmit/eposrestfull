<?php

namespace Contendetail\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Contendetail implements InputFilterAwareInterface
{
    public $id;
    public $idforeign;
    public $src;
    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->idforeign = (isset($data['idforeign'])) ? $data['idforeign'] : null;
		$this->src = (isset($data['src'])) ? $data['src'] : null;
		
    }
    
    public function dataArray($data)
    {
    	$this->id     = (isset($data['id'])) ? $data['id'] : null;
    	$this->idforeign = (isset($data['idforeign'])) ? $data['idforeign'] : null;
    	$this->src = (isset($data['src']['name'])) ? $data['src']['name'] : null;
    }
    
    public function dataArraySwap($data , $Renamefile)
    {
    	$this->id     = (isset($data['id'])) ? $data['id'] : null;
    	$this->idforeign = (isset($data['idforeign'])) ? $data['idforeign'] : null;
    	$this->src = $Renamefile;
    	
    }
    
    
 
    
    
    public function dataPost($data)
    {
    	$this->id     = (isset($data['id'])) ? $data['id'] : null;
    	$this->idforeign = (isset($data['idforeign'])) ? $data['idforeign'] : null;
    	$this->src = (isset($data['src']['name'])) ? $data['src']['name'] : null;
    }

    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    public function getInputFilter()
    {
        if (!$this->inputFilter) {
            $inputFilter = new InputFilter();

            $factory = new InputFactory();

            $inputFilter->add($factory->createInput(array(
                'name'     => 'id',
                'required' => false,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
			
			$inputFilter->add($factory->createInput(array(
                'name'     => 'idforeign',
                  'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            

           $inputFilter->add ( $factory->createInput ( array (
					'name' => 'src',
					'required' => false,
					'validators' => array (
							array (
									'name' => 'FileExtension',
									'options' => array (
											'extension' => 'jpg, jpeg, png' 
									) 
							),
							array (
									'name' => 'FileSize',
									'options' => array (
											'min' => 1000,
											'max' => 4000000 
									) 
							)
						
					) ,
			) ) );
            
			

            $this->inputFilter = $inputFilter;        
        }

        return $this->inputFilter;
    }
}
