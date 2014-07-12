<?php
namespace Admin;

use Zend\Mvc\ModuleRouteListener;
use Zend\Mvc\MvcEvent;

// Add these import statements:
use Admin\Model\UserTable;
use Admin\Model\User;
use Zend\Db\ResultSet\ResultSet;
use Zend\Db\TableGateway\TableGateway;


class Module
{
	public $tmp;
    public function onBootstrap(MvcEvent $e)
    {
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
    }
    
    // Add this method:
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					// DB
    					'UserTableGateway' => function ($sm) {
    						
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    					
    						
    						$resultSetPrototype = new ResultSet();
    						$resultSetPrototype->setArrayObjectPrototype(new User());
    						//$resultSetPrototype = null;
    						
    						return new TableGateway('user', $dbAdapter, null, $resultSetPrototype);
    					},
    					'Admin\Model\UserTable' =>  function($sm) {
    					
    						$tableGateway = $sm->get('UserTableGateway');
    						
    					
    						$table = new UserTable($tableGateway);
    						return $table;
    					},
    					
    						
    			),
    	);
    }
    
    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }

    public function getAutoloaderConfig()
    {
    	
        return array(
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }

    
    
}
