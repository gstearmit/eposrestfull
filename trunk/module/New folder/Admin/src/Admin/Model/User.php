<?php

namespace Admin\Model;

class User
{
	public $user_id;
	public $username;
	public $email;
	public $display_name;
	public $password;
	public $state;
	public $role;

	public function exchangeArray($data)
	{
		$this->user_id     	= (!empty($data['user_id'])) ? $data['user_id'] : null;
		$this->username = (!empty($data['username'])) ? $data['username'] : null;
		$this->email  	= (!empty($data['email'])) ? $data['email'] : null;
		$this->password = (!empty($data['password'])) ? $data['password'] : null;
		$this->state 	= (!empty($data['state'])) ? $data['state'] : null;
		$this->role 	= (!empty($data['role'])) ? $data['role'] : null;
	}
}