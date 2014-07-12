<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Model\Acl;
//use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class IndexController extends AbstractActionController
{
// 	public function _getAcl()
// 	{
// 		if(!$this->_acl)
// 		{
// 			$this->_acl = $this->getServiceLocator()->get("Acl");
// 		}
// 		return $this->_acl;
// 	}
	
	 
	
    public function indexAction()
    {
//     	$quyen = "Guest";//se dc lay khi nguoi dung dang nhap
//     	if(!$this->_getAcl()->isAllowed($quyen,null,"index_index"))
//     	{
//     		echo "ban khong co quyen truy cap action nay";
//     	}
//     	else
//     	{
//     		echo "ban co quyen de truy cap vao action nay";
//     	}
    	
    	
//     	var_dump($this->commonHelper()->uploadImage($data = Null));
//     	die;
    	$product = array(
    			'id'      => 'cod_123abc',
    			'qty'     => 1,
    			'price'   => 39.95,
    			'name'    => 'T-Shirt',
    			'options' => array('Size' => 'M', 'Color' => 'Black')
    	);
    	$this->ZendCart()->insert($product);
    	
    	// add 2
    	$product2 = array(
    			'id'      => 'Ao Dai Tay',
    			'qty'     => 3,
    			'price'   => 80,
    			'name'    => 'T-Shirt-TQ',
    			'options' => array('Size' => 'M', 'Color' => 'Black')
    	);
    	$this->ZendCart()->insert($product2);
    	//$this->ZendCart()->destroy();
    	
    	// add 2 product
//     	$products = array(
//     			array(
//     					'id'      => 'XYZ',
//     					'qty'     => 1,
//     					'price'   => 15.15,
//     					'product' => 'Book: ZF2 for beginners',
//     			),
//     			array(
//     					'id'      => 'ZYX',
//     					'qty'     => 3,
//     					'price'   => 19.99,
//     					'product' => 'Book: ZF2 for advanced users',
//     			)
//     	);
//     	$this->ShoppingCart()->insert($products);
    	
    	
    	
       return new ViewModel(array(
		    'items' => $this->ZendCart()->cart(),
		    'total_items' => $this->ZendCart()->total_items(),
		    'total' => $this->ZendCart()->total(),
		));
    }
    
 
    
    public function newAction()
    {
    	$quyen = "Guest";//se dc lay khi nguoi dung dang nhap
    	if(!$this->_getAcl()->isAllowed($quyen,null,"index_new"))
    	{
    		echo "ban khng dc phep truy cap vao action nay";
    	}
    }
}
