<?php

namespace Phototamtayvnrest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;


use Igirlxinhcom\Model\Igirlxinhcom;          // <-- Add this import
use Igirlxinhcom\Form\IgirlxinhcomForm;       // <-- Add this import
use Igirlxinhcom\Model\IgirlxinhcomTable;     // <-- Add this import
use Zend\View\Model\JsonModel;

class PhototamtayvnrestController extends AbstractRestfulController
{
    protected $igirlxinhcomTable;

    public function getList()
    {
    	//echo 'get list';
    	$results = $this->getIgirlxinhcomTable()->fetch_All_Phototamtayvnrest_Rest_Orderbyiddesc();
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

        $Phototamtayvn = $this->getIgirlxinhcomTable()->getPhototamtayvn($id);
      
        // print_r($Phototamtayvn); die();
         
        return new JsonModel(array(
            'data' => $Phototamtayvn,
        ));
    }
    
    

    public function create($data)
    {
        $form = new IgirlxinhcomForm();
        $igirlxinhcom = new Igirlxinhcom();
        $form->setInputFilter($igirlxinhcom->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $igirlxinhcom->exchangeArray($form->getData());
            $id = $this->getIgirlxinhcomTable()->saveIgirlxinhcom($igirlxinhcom);
        }

        return new JsonModel(array(
            'data' => $this->get($id),
        ));
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $igirlxinhcom = $this->getIgirlxinhcomTable()->getIgirlxinhcom($id);
        $form  = new IgirlxinhcomForm();
        $form->bind($igirlxinhcom);
        $form->setInputFilter($igirlxinhcom->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $id = $this->getIgirlxinhcomTable()->saveIgirlxinhcom($form->getData());
        }

        return new JsonModel(array(
            'data' => $this->get($id),
        ));
    }

    public function delete($id)
    {
        $this->getIgirlxinhcomTable()->deleteIgirlxinhcom($id);

        return new JsonModel(array(
            'data' => 'deleted',
        ));
    }

    public function getIgirlxinhcomTable()
    {
        if (!$this->igirlxinhcomTable) {
            $sm = $this->getServiceLocator();
            $this->igirlxinhcomTable = $sm->get('Igirlxinhcom\Model\IgirlxinhcomTable');
        }
        return $this->igirlxinhcomTable;
    }
}