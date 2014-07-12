<?php

namespace Igirlxinhcom;

use Igirlxinhcom\Model\IgirlxinhcomTable;

class Module
{
// 	    public function onBootstrap(MvcEvent $e)
// 	    {
// 	        $eventManager        = $e->getApplication()->getEventManager();
// 	        $moduleRouteListener = new ModuleRouteListener();
// 	        $moduleRouteListener->attach($eventManager);
// 	    }
    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/autoload_classmap.php',
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__ . '/src/' . __NAMESPACE__,
                ),
            ),
        );
    }
    
    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                'Igirlxinhcom\Model\IgirlxinhcomTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new IgirlxinhcomTable($dbAdapter);
                    return $table;
                },
            ),
        );
    }    

    public function getConfig()
    {
        return include __DIR__ . '/config/module.config.php';
    }
}