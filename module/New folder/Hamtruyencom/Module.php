<?php

namespace Hamtruyencom;

use Hamtruyencom\Model\HamtruyencomTable;

class Module
{
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
                'Hamtruyencom\Model\HamtruyencomTable' =>  function($sm) {
                    $dbAdapter = $sm->get('Zend\Db\Adapter\Adapter');
                    $table = new HamtruyencomTable($dbAdapter);
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