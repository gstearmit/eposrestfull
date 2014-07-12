<?php

namespace CatalogueRest\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;


use Catalogue\Model\Catalogue;          // <-- Add this import
use Catalogue\Form\CatalogueForm;       // <-- Add this import
use Catalogue\Model\CatalogueTable;     // <-- Add this import
use Zend\View\Model\JsonModel;

class CatalogueRestController extends AbstractRestfulController
{
    protected $catalogueTable;

    public function getList()
    {
    	//echo 'get list';
    	$results = $this->getCatalogueTable()->fetchAllOrderbyiddesc();
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
//     	echo "Get Id tra ve";
//     	echo "</br>";
    	
        //$catalogue = $this->getCatalogueTable()->getRestCatalogue($id);
        $catalogue = $this->getCatalogueTable()->getRestCatalogueNewsReport($id);
        //var_dump($catalogue);die();
        return new JsonModel(array(
            'data' => $catalogue,
        ));
    }
    
    

    public function create($data)
    {
        $form = new CatalogueForm();
        $catalogue = new Catalogue();
        $form->setInputFilter($catalogue->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $catalogue->exchangeArray($form->getData());
            $id = $this->getCatalogueTable()->saveCatalogue($catalogue);
        }

        return new JsonModel(array(
            'data' => $this->get($id),
        ));
    }

    public function update($id, $data)
    {
        $data['id'] = $id;
        $catalogue = $this->getCatalogueTable()->getCatalogue($id);
        $form  = new CatalogueForm();
        $form->bind($catalogue);
        $form->setInputFilter($catalogue->getInputFilter());
        $form->setData($data);
        if ($form->isValid()) {
            $id = $this->getCatalogueTable()->saveCatalogue($form->getData());
        }

        return new JsonModel(array(
            'data' => $this->get($id),
        ));
    }

    public function delete($id)
    {
        $this->getCatalogueTable()->deleteCatalogue($id);

        return new JsonModel(array(
            'data' => 'deleted',
        ));
    }

    public function getCatalogueTable()
    {
        if (!$this->catalogueTable) {
            $sm = $this->getServiceLocator();
            $this->catalogueTable = $sm->get('Catalogue\Model\CatalogueTable');
        }
        return $this->catalogueTable;
    }
}