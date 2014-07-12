<?php

namespace Uploadfilemutil\Model;
//namespace Magazinepublish\Form;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Uploadfilemutil implements InputFilterAwareInterface
{
    public $id;
    public $imgkey;
    public $descriptionkey;
    public $title;

    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->descriptionkey = (isset($data['descriptionkey'])) ? $data['descriptionkey'] : null;
        $this->title  = (isset($data['title'])) ? $data['title'] : null;
        $this->imgkey  = (isset($data['imgkey'])) ? $data['imgkey'] : null;
      
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
                'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            

            $inputFilter->add($factory->createInput(array(
                'name'     => 'descriptionkey',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
            
            $inputFilter->add($factory->createInput(array(
            		'name'     => 'imgkey',
            		'required' => true,
            		'filters'  => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            		'validators' => array(
            				array(
            						'name'    => 'StringLength',
            						'options' => array(
            								'encoding' => 'UTF-8',
            								'min'      => 1,
            								'max'      => 100,
            						),
            				),
            		),
            )));
            
            $inputFilter->add($factory->createInput(array(
                'name'     => 'title',
                'required' => true,
                'filters'  => array(
                    array('name' => 'StripTags'),
                    array('name' => 'StringTrim'),
                ),
                'validators' => array(
                    array(
                        'name'    => 'StringLength',
                        'options' => array(
                            'encoding' => 'UTF-8',
                            'min'      => 1,
                            'max'      => 100,
                        ),
                    ),
                ),
            )));
        /*    
            $inputFilter->add($factory->createInput(array(
            		'name' => 'imgupload',
            		'filters' => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            		'validators' => array(
            		),
            		)));
            
            $inputFilter->add($factory->createInput(array(
            		'name' => 'namemagazine',
            		'required' => true,
            		'filters' => array(
            				array('name' => 'StripTags'),
            				array('name' => 'StringTrim'),
            		),
            		'validators' => array(
            				array (
            						'name' => 'StringLength',
            						'options' => array(
            								'encoding' => 'UTF-8',
            								'min' => '1',
            								'max' => '50',
            						),
            				),
            		),
            		)));
            
           */ 
            
            
            

            $this->inputFilter = $inputFilter;        
        }

        return $this->inputFilter;
    }
}
