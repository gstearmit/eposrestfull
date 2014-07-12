<?php

namespace Apphaivltvrest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;


use Rssget\Model\Rssget;          // <-- Add this import
use Rssget\Form\RssgetForm;       // <-- Add this import
use Rssget\Model\RssgetTable;     // <-- Add this import
use Zend\View\Model\JsonModel;

class ApphaivltvrestController extends AbstractRestfulController
{
    protected $rssgetTable;

    public function getList()
    {
    	//echo 'get list';
    	$results = $this->getRssgetTable()->fetch_All_Apphaivltv_Rest_Orderbyiddesc();
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
//         var_dump($rssget);die();
        return new JsonModel(array(
            'data' => $rssget,
        ));
    }
    
    

    public function create($data)
    {
        $form = new RssgetForm();
        $rssget = new Rssget();
        $form->setInputFilter($rssget->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $rssget->exchangeArray($form->getData());
            $id = $this->getRssgetTable()->saveRssget($rssget);
        }

        return new JsonModel(array(
            'data' => $this->get($id),
        ));
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $rssget = $this->getRssgetTable()->getRssget($id);
        $form  = new RssgetForm();
        $form->bind($rssget);
        $form->setInputFilter($rssget->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $id = $this->getRssgetTable()->saveRssget($form->getData());
        }

        return new JsonModel(array(
            'data' => $this->get($id),
        ));
    }

    public function delete($id)
    {
        $this->getRssgetTable()->deleteRssget($id);

        return new JsonModel(array(
            'data' => 'deleted',
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