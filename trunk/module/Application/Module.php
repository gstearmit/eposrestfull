<?php

namespace Application;

use Application\Model\Acl;
use Zend\Mvc\ModuleRouteListener;
use Application\Controller\Plugin;

class Module
{
    public function onBootstrap($e)
    {
        $e->getApplication()->getServiceManager()->get('translator');
        $eventManager        = $e->getApplication()->getEventManager();
        $moduleRouteListener = new ModuleRouteListener();
        $moduleRouteListener->attach($eventManager);
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
                	'Mylibrary' => __DIR__ . '/../../vendor/lib',
                ),
            ),
        		
        );
    }
    
    public function getServiceConfig()
    {
    	return array(
    			'factories' => array(
    					'Acl' => function($sm)
    					{
    						$acl = new Acl();
    						return $acl;
    					},
    					'Application\Model\ApplicationTable' =>  function($sm) {
    						$dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
    						$table = new ApplicationTable($dbAdapter);
    						return $table;
    					},
    			),
    	);
    }
    
//     public function getControllerPluginConfig() {
//     	return array(
//     			'invokables' => array(
//     					//'changeConnection' => 'PerseedApplication\Controller\Plugin\ChangeConnection',
//     					'commonHelper' => 'Application\Controller\Plugin\CommonHelper',
//     			),
//     	);
//     }
    

}
