<?php

namespace Comments\Model;

use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Comments implements InputFilterAwareInterface
{
    public $id;
    public $title;
    public $name;
	
	public $content;
    public $appsatellite_id;
    //public $contentdetailull_id;
	public $user_id;
    public $email;
	public $created;
    public $status;
    
    protected $inputFilter;

    /**
     * Used by ResultSet to pass each database row to the entity
     */
    public function exchangeArray($data)
    {
        $this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		
		$this->content     = (isset($data['content'])) ? $data['content'] : null;
        $this->appsatellite_id = (isset($data['appsatellite_id'])) ? $data['appsatellite_id'] : null;
		//$this->contentdetailull_id = (isset($data['contentdetailull_id'])) ? $data['contentdetailull_id'] : null;
		$this->user_id     = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
		$this->created = (isset($data['created'])) ? $data['created'] : null;
		$this->status = (isset($data['status'])) ? $data['status'] : null;
	
    }
    
    public function dataArray($data)
    {
    	$this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		
		$this->content     = (isset($data['content'])) ? $data['content'] : null;
        $this->appsatellite_id = (isset($data['appsatellite_id'])) ? $data['appsatellite_id'] : null;
		//$this->contentdetailull_id = (isset($data['contentdetailull_id'])) ? $data['contentdetailull_id'] : null;
		$this->user_id     = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
		$this->created = (isset($data['created'])) ? $data['created'] : null;
		$this->status = (isset($data['status'])) ? $data['status'] : null;
    }
    
   
    
    public function dataPost($data)
    {
    	$this->id     = (isset($data['id'])) ? $data['id'] : null;
        $this->title = (isset($data['title'])) ? $data['title'] : null;
		$this->name = (isset($data['name'])) ? $data['name'] : null;
		
		$this->content     = (isset($data['content'])) ? $data['content'] : null;
        $this->appsatellite_id = (isset($data['appsatellite_id'])) ? $data['appsatellite_id'] : null;
		//$this->contentdetailull_id = (isset($data['contentdetailull_id'])) ? $data['contentdetailull_id'] : null;
		$this->user_id     = (isset($data['user_id'])) ? $data['user_id'] : null;
        $this->email = (isset($data['email'])) ? $data['email'] : null;
		$this->created = (isset($data['created'])) ? $data['created'] : null;
		$this->status = (isset($data['status'])) ? $data['status'] : null;
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
                'name'     => 'user_id',
                  'required' => true,
                'filters'  => array(
                    array('name' => 'Int'),
                ),
            )));
            
			$inputFilter->add($factory->createInput(array(
					'name'     => 'appsatellite_id',
					'required' => true,
					'filters'  => array(
							array('name' => 'Int'),
					),
			)));

			$inputFilter->add($factory->createInput(array(
					'name'     => 'name',
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
				
			$inputFilter->add($factory->createInput(array(
					'name'     => 'content',
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
					'name'     => 'email',
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
					'name'     => 'created',
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
					'name'     => 'status',
					'required' => true,
					'filters'  => array(
							array('name' => 'Int'),
					),
			)));
			
			
            $this->inputFilter = $inputFilter;        
        }

        return $this->inputFilter;
    }
}
