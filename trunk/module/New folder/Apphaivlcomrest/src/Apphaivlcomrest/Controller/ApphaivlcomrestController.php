<?php

namespace Apphaivlcomrest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;


use Rssget\Model\Rssget;          // <-- Add this import
use Rssget\Form\RssgetForm;       // <-- Add this import
use Rssget\Model\RssgetTable;     // <-- Add this import
use Zend\View\Model\JsonModel;

class ApphaivlcomrestController extends AbstractRestfulController
{
    protected $rssgetTable;

    public function getList()
    {
    	//echo 'get list';
    	$results = $this->getRssgetTable()->fetch_All_Apphaivlcom_Rest_Orderbyiddesc();
        $data = array();
        foreach($results as $result) 
        {
            $data[] = $result;
        }

        return new JsonModel(array(
            'data' => $data,
        ));
    }

    public function get($id)
    {
        $rssget = $this->getRssgetTable()->getRssget($id);
        //var_dump($rssget);die();
        return new JsonModel(array(
            'data' => $rssget,
        ));
    }
    
    

   

    public function getRssgetTable()
    {
        if (!$this->rssgetTable) {
            $sm = $this->getServiceLocator();
            $this->rssgetTable = $sm->get('Rssget\Model\RssgetTable');
        }
        return $this->rssgetTable;
    }
}