<?php
namespace Application\Model;
//use Application\Model\Acl;
use Zend\Mvc\ModuleRouteListener;
use Zend\Permissions\Acl\Acl as ZendAcl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;
class Acl extends ZendAcl
{
	public function __construct()
	{
		$this->addRole(new Role("Guest"))
		->addRole(new Role("User"),"Guest")
		->addRole(new Role("Admin","User"));

		$this->allow("Guest",null,array("index_index","login_index"));//login_index; Contrller_action
		$this->allow("Admin",null,array("index_new"));

		$this->deny("Guest",null,array("index_new","login_logout"));
	}
}
?>