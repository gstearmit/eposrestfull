<?php
namespace Admin\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use  Admin\Form\UserForm;

class IndexController extends AbstractActionController
{

	public function indexAction()
    {
    	
    	$userTable = $this->getServiceLocator()->get('Admin\Model\UserTable');  
		
		//echo '<pre>';
		//print_r($userTable->fetchAll());
		//echo '</pre>';
		
    	return new ViewModel (array('users' => $userTable->fetchAll()));
    	
    }
	
	public function addAction()
    {
    	
    	echo 'Update .......';
    }
	
	public function editAction()
    {
    	echo 'Update .......';
    	
    }
	
	public function deleteAction()
    {
    	echo 'Update .......';
    	
    }
 
}
