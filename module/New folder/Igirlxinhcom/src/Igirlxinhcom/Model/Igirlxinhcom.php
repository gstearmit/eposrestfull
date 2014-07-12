<?php

namespace Igirlxinhcom\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Igirlxinhcom implements InputFilterAwareInterface
{
    public $id;
    public $nameapp;
    public $title;
    public $link;
    public $image_thumbnail;
    public $content_detail;
    public $content_detail_full;
    public $extend;


    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->nameapp = (isset($data['nameapp'])) ? $data['nameapp'] : null;
        $this->title  = (isset($data['title'])) ? $data['title'] : null;
        $this->link  = (isset($data['link'])) ? $data['link'] : null;
        $this->image_thumbnail  = (isset($data['image_thumbnail'])) ? $data['image_thumbnail'] : null;
        $this->content_detail  = (isset($data['content_detail'])) ? $data['content_detail'] : null;
        $this->content_detail_full  = (isset($data['content_detail_full'])) ? $data['content_detail_full'] : null;
        $this->extend  = (isset($data['extend'])) ? $data['extend'] : null;

    }
    
    
    public function exchangeArrayigril($data,$nameppp,$titlep,$linkp)
    {
    	$this->id     = (isset($data['id'])) ? $data['id'] : null;
    	$this->nameapp = $nameppp;
    	$this->title  = $titlep;
    	$this->link  = $linkp;
    	$this->image_thumbnail  = (isset($data['image_thumbnail'])) ? $data['image_thumbnail'] : null;
    	$this->content_detail  = (isset($data['content_detail'])) ? $data['content_detail'] : null;
    	$this->content_detail_full  = (isset($data['content_detail_full'])) ? $data['content_detail_full'] : null;
    	$this->extend  = (isset($data['extend'])) ? $data['extend'] : null;
    
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
                'name'     => 'nameapp',
                'required' => fasle,
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
                'required' => false,
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
            		'name'     => 'link',
            		'required' => false,
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

            
            $inputFilter->add ( $factory->createInput ( array (
            		'name' => 'image_thumbnail',
            		'required' => true,
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
            								'max' => 10485760
            						),
            						array (
            								'name' => 'StringLength',
            								'options' => array (
            										'encoding' => 'UTF-8',
            										'min' => 1,
            										'max' => 100
            								)
            						)
            				)
            		)
            ) ) );
            
            //content_detail
            $inputFilter->add ( $factory->createInput ( array (
            		'name' => 'content_detail',
            		'required' => true,
            		'filters' => array (
            				array (
            						'name' => 'StripTags'
            				),
            				array (
            						'name' => 'StringTrim'
            				)
            		),
            		'validators' => array (
            				array (
            						'name' => 'StringLength',
            						'options' => array (
            								'encoding' => 'UTF-8',
            								'min' => 1,
            								'max' => 100
            						)
            				)
            		)
            ) ) );
            
            //content_detail_full
            $inputFilter->add ( $factory->createInput ( array (
            		'name' => 'content_detail_full',
            		'required' => true,
            		'filters' => array (
            				array (
            						'name' => 'StripTags'
            				),
            				array (
            						'name' => 'StringTrim'
            				)
            		),
            		'validators' => array (
            				array (
            						'name' => 'StringLength',
            						'options' => array (
            								'encoding' => 'UTF-8',
            								'min' => 1,
            								'max' => 100
            						)
            				)
            		)
            ) ) );
            
            
            
            
            $this->inputFilter = $inputFilter;        
        }

        return $this->inputFilter;
    }
}
