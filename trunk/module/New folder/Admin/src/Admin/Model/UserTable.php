<?php
namespace Admin\Model;

use Zend\Db\TableGateway\TableGateway;

class UserTable
{
	protected $tableGateway;

	public function __construct(TableGateway $tableGateway)
	{
		$this->tableGateway = $tableGateway;
	
	}

	public function fetchAll()
	{
		//echo __METHOD__;
		return $this->tableGateway->select();
	}

}