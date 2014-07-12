<?php
namespace Chapter\Model;
// namespace Chapter\Form;
use Zend\InputFilter\InputFilter;
use Zend\InputFilter\Factory as InputFactory;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;

class Chapter implements InputFilterAwareInterface {
	public $id;
	public $name;
	public $descriptionkey;
	protected $inputFilter;
	
	/**
	 * Used by ResultSet to pass each database row to the entity
	 */
	public function exchangeArray($data) {
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->name = (isset ( $data ['name'] )) ? $data ['name'] : null;
	}
	
	public function dataArray($data) {
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->name = (isset ( $data ['name'] )) ? $data ['name'] : null;
	}
	
	public function dataArraySwap($data , $Renamefile)
	{
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->name = (isset ( $data ['name'] )) ? $data ['name'] : null;

	}
	
	
	public function dataPost($data) {
		$this->id = (isset ( $data ['id'] )) ? $data ['id'] : null;
		$this->descriptionkey = (isset ( $data ['descriptionkey'] )) ? $data ['descriptionkey'] : null;
		$this->name = (isset ( $data ['name'] )) ? $data ['name'] : null;
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
					'required' => true,
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
					'name' => 'name',
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
