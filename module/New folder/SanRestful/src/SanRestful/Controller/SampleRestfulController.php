<?php

namespace SanRestful\Controller;

use Zend\Mvc\Controller\AbstractRestfulController;
use Zend\View\Model\JsonModel;

class SampleRestfulController extends AbstractRestfulController
{    
    public function get($id)
    {
        $response = $this->getResponseWithHeader()
                         ->setContent( __METHOD__.' get current data with id =  '.$id);
        return $response;
    }
    
    public function getList()
    {
//         $response = $this->getResponseWithHeader()
//                          ->setContent(' get the list of data');
//                          //->setContent( __METHOD__.' get the list of data');
//         return $response;

    	$data = array(
    		'name'=>'hoang phuc',
    		'id'=>1,
    			'nguoiyeu'=>"thu",
    	);
    	
    	return new JsonModel(array(
    			'data' => $data,
    	));
    }
    
    public function create($data)
    {
        $response = $this->getResponseWithHeader()
                         ->setContent( __METHOD__.' create new item of data :
                                                    <b>'.$data['name'].'</b>');
        return $response;
    }
    
    public function update($id, $data)
    {
       $response = $this->getResponseWithHeader()
                        ->setContent(__METHOD__.' update current data with id =  '.$id.
                                            ' with data of name is '.$data['name'].'</br>') ;
     return $response;
     
    }
    
    public function delete($id)
    {
        $response = $this->getResponseWithHeader()
                        ->setContent(__METHOD__.' delete current data with id =  '.$id) ;
        return $response;
    }
    
    public function getResponseWithHeader()
    {
        $response = $this->getResponse();
        $response->getHeaders()
                 ->addHeaderLine('Access-Control-Allow-Origin','*')
                 ->addHeaderLine('Access-Control-Allow-Methods','POST PUT DELETE GET');
        
        return $response;
    }
}
