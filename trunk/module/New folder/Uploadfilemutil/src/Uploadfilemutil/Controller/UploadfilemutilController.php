<?php

namespace Uploadfilemutil\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Uploadfilemutil\Model\Uploadfilemutil;
use Uploadfilemutil\Form\UploadfilemutilForm;
//use Uploadfilemutil\Form\Uploadfilemutil;
//use Uploadfilemutil\Form\UploadfilemutilFormValidator;
//use Uploadfilemutil\Form\UploadfilemutilValidator;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
// check login

use ZfcUser\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;


class UploadfilemutilController extends AbstractActionController {
    
    protected $uploadfilemutilTable;

    public function indexAction() {
    	// check login
    	if (!$this->zfcUserAuthentication()->hasIdentity()) {
    		return $this->redirect()->toRoute('zfcuser/login');
    	}else 
    	{
    	
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ? $this->params()->fromRoute('order') : Select::ORDER_ASCENDING;
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;

        $uploadfilemutils = $this->getUploadfilemutilTable()->fetchAll($select->order($order_by . ' ' . $order));
        $itemsPerPage = 10;        // is Number record/page

        $uploadfilemutils->current();
        $paginator = new Paginator(new paginatorIterator($uploadfilemutils));
        $paginator->setCurrentPageNumber($page)
                ->setItemCountPerPage($itemsPerPage)
                ->setPageRange(4);  // is number page want view

        return new ViewModel(array(
                    'order_by' => $order_by,
                    'order' => $order,
                    'page' => $page,
                    'paginator' => $paginator,
                ));
    	}//login
    	
    	
    }

    public function addAction() {
    	
        $form = new UploadfilemutilForm(); // include Form Class
        $form->get('submit')->setAttribute('value', 'Add');
       
        $request = $this->getRequest();
       
        if ($request->isPost()) {
        	
            $uploadfilemutil = new Uploadfilemutil();

            $form->setInputFilter($uploadfilemutil->getInputFilter());  // check validate
           
            $form->setData($request->getPost());  // get all post
            
            $data = array_merge_recursive(
            		$this->getRequest()->getPost()->toArray(),
            		$this->getRequest()->getFiles()->toArray()
            );
			
//             echo '<pre>';
//             print_r($data);
//             echo '<pre>';
//             die;
            if ($form->isValid()) {
                $uploadfilemutil->exchangeArray($form->getData());
                $this->getUploadfilemutilTable()->saveUploadfilemutil($uploadfilemutil);
                // Redirect to list of uploadfilemutils
                return $this->redirect()->toRoute('uploadfilemutil');
            }
        }

        return array('form' => $form);
    }

    public function editAction() {
        $id = (int) $this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('uploadfilemutil', array('action' => 'add'));
        }
        $uploadfilemutil = $this->getUploadfilemutilTable()->getUploadfilemutil($id);

        $form = new UploadfilemutilForm();
        $form->bind($uploadfilemutil);
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        if ($request->isPost()) {
            $form->setData($request->getPost());
            if ($form->isValid()) {
                $this->getUploadfilemutilTable()->saveUploadfilemutil($uploadfilemutil);

                // Redirect to list of uploadfilemutils
                return $this->redirect()->toRoute('uploadfilemutil');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params('id');
        if (!$id) {
            return $this->redirect()->toRoute('uploadfilemutil');
        }

        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost()->get('id');
                $this->getUploadfilemutilTable()->deleteUploadfilemutil($id);
            }

            // Redirect to list of uploadfilemutils
            return $this->redirect()->toRoute('uploadfilemutil');
        }

        return array(
            'id' => $id,
            'uploadfilemutil' => $this->getUploadfilemutilTable()->getUploadfilemutil($id)
        );
    }

    public function getUploadfilemutilTable() {
        if (!$this->uploadfilemutilTable) {
            $sm = $this->getServiceLocator();
            $this->uploadfilemutilTable = $sm->get('Uploadfilemutil\Model\UploadfilemutilTable');
        }
        return $this->uploadfilemutilTable;
    }

}
