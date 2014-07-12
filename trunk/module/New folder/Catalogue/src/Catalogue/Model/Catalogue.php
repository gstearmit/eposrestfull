<?php

namespace Catalogue\Model;
// namespace Catalogue\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Catalogue implements InputFilterAwareInterface {
	public $id;
	public $imgkey;
	public $descriptionkey;
	public $title;
	public $patient_id;
	public $url_catalogue;
	public $url_rest;
	protected $inputFilter;
	
	
	/**
	 * Used by ResultSet to pass each database row to the entity
	 */
	public function exchangeArray($data) {
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->title = (isset ( $data ['title'] )) ? $data ['title'] : null;
		$this->imgkey = (isset ( $data ['imgkey'] )) ? $data ['imgkey'] : null;
		$this->patient_id = (isset (  $data ['id'] )) ? $data ['patient_id'] : null;
		$this->url_catalogue = (isset ( $data ['url_catalogue'])) ? $data ['url_catalogue'] : null;
		$this->url_rest = (isset ( $data ['url_rest'])) ? $data ['url_rest'] : null;
	}
	
	public function dataArray($data) {
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->title = (isset ( $data ['title'] )) ? $data ['title'] : null;
		$this->imgkey = (isset ( $data ['imgkey']['name'] )) ? $data ['imgkey']['name'] : null;
		$this->patient_id = (isset (  $data ['id'] )) ? $data ['patient_id'] : null;
		$this->url_catalogue = (isset ( $data ['url_catalogue'])) ? $data ['url_catalogue'] : null;
		$this->url_rest = (isset ( $data ['url_rest'])) ? $data ['url_rest'] : null;
		
	}
	
	public function dataArraySwap($data , $Renamefile)
	{
	
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->title = (isset ( $data ['title'] )) ? $data ['title'] : null;
		$this->imgkey = $Renamefile;
		$this->patient_id = (isset (  $data ['id'] )) ? $data ['patient_id'] : null;
		$this->url_catalogue = (isset ( $data ['url_catalogue'])) ? $data ['url_catalogue'] : null;
		$this->url_rest = (isset ( $data ['url_rest'])) ? $data ['url_rest'] : null;
	
	}
	
	public function dataPost($data) {
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->title = (isset ( $data ['title'] )) ? $data ['title'] : null;
		$this->imgkey = (isset ( $data ['imgkey']['name'] )) ? $data ['imgkey']['name'] : null;
		$this->patient_id = (isset (  $data ['id'] )) ? $data ['patient_id'] : null;
		$this->url_catalogue = (isset ( $data ['url_catalogue'])) ? $data ['url_catalogue'] : null;
		$this->url_rest = (isset ( $data ['url_rest'])) ? $data ['url_rest'] : null;
	
	}
	public function getArrayCopy() {
		return get_object_vars ( $this );
	}
	public function setInputFilter(InputFilterInterface $inputFilter) {
		throw new \Exception ( "Not used" );
	}
	public function getInputFilter() {
		if (! $this->inputFilter) {
			$inputFilter = new InputFilter ();
			
			$factory = new InputFactory ();
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'id',
					'required' => false,
					'filters' => array (
							array (
									'name' => 'Int' 
							) 
					) 
			) ) );
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'descriptionkey',
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
	
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'imgkey',
					'required' =>false,
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
									),
									array (
											'name' => 'StringLength',
											'options' => array (
													'encoding' => 'UTF-8',
													'min' => 1,
													'max' => 100
											),
							) 
					) 
			) ) )
			);
			
			//public $patient_id;
	        //public $url_catalogue;
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'title',
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
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'url_catalogue',
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
			
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'url_rest',
					'required' => false,
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
			
			
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'patient_id',
					'required' => true,
					'filters' => array (
							array (
									'name' => 'Int'
							)
					)
			) ) );
			
			$this->inputFilter = $inputFilter;
		}
		
		return $this->inputFilter;
	}
	
	public function getInputFiltermzimg()
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
					'name'     => 'idmz',
					'required' => true,
					'filters'  => array(
							array('name' => 'Int'),
					),
			)));
	
	
			$inputFilter->add ( $factory->createInput ( array (
					'name' => 'img',
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
	
			$inputFilter->add($factory->createInput(array(
					'name'     => 'description',
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
					'name'     => 'page',
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
	
			$this->inputFilter = $inputFilter;
		}
	
		return $this->inputFilter;
	}
	
	
	
}
