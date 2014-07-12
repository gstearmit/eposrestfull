<?php

namespace Contendetail\Controller;


use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Contendetail\Model\Contendetail;
use Contendetail\Model\ContendetailTable;
use Contendetail\Form\ContendetailForm;
use Contendetail\Form\AddContendetailForm as FromClass;
use Contendetail\Form\ContendetailSearchForm as SearchFromContendetail ;


use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

use ZfcUser\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;

use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;

use Zend\View\Model\JsonModel;

class ContendetailController extends AbstractActionController {

    protected $contendetailTable;

    public function searchAction()
    {
    
    	$request = $this->getRequest();
    
    	$url = 'index';
    
    	if ($request->isPost()) {
    		$formdata    = (array) $request->getPost();
    		$search_data = array();
    		foreach ($formdata as $key => $value) {
    			if ($key != 'submit') {
    				if (!empty($value)) {
    					$search_data[$key] = $value;
    				}
    			}
    		}
    		if (!empty($search_data)) {
    			$search_by = json_encode($search_data);
    			$url .= '/search_by/' . $search_by;
    		}
    	}
    	$this->redirect()->toUrl($url);
    }
    
    
    public function indexAction() {
    	// check login
//     	if (!$this->zfcUserAuthentication()->hasIdentity()) {
//     		return $this->redirect()->toRoute('zfcuser/login');
//     	}

    	$id = (int)$this->params()->fromRoute('id');
    	    	if ($id == 0) {
    	    		return $this->redirect()->toRoute('igirlxinhcom');
    	    	}
    	
    	//SearchFromcontendetail
    	$searchform = new SearchFromContendetail();
    	$searchform->get('submit')->setValue('Search');
    	
        $select = new Select();

        $order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
        $order = $this->params()->fromRoute('order') ?  $this->params()->fromRoute('order') : Select::ORDER_DESCENDING;
        $page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
        $search_by = $this->params()->fromRoute('search_by') ? $this->params()->fromRoute('search_by') : '';
        $select->order($order_by . ' ' . $order);
        
        
        
        $contendetails = $this->getContendetailTable()->fetchAll($select);
       
        
        $totalRecord  = $contendetails->count();
        $itemsPerPage = 10;        // is Number record/page
        $contendetails->current();
        $paginator = new Paginator(new paginatorIterator($contendetails));
        $paginator->setCurrentPageNumber($page)
                  ->setItemCountPerPage($itemsPerPage)
                  ->setPageRange(4);  // is number page want view

        return new ViewModel(array(
        		    'search_by'  => $search_by,
                    'order_by' => $order_by,
                    'order' => $order,
                    'page' => $page,
                    'paginatorimg' => $paginator,
	        		'pageAction' => 'contendetail',
	        		'form'       => $searchform,
	        		'totalRecord' => $totalRecord,
        		   
                ));
    }
    
    public function ajaxdetailAction(){
    	$data = array_merge_recursive(
    			$this->getRequest()->getPost()->toArray(),
    			$this->getRequest()->getFiles()->toArray()
    	);
    	$test = json_encode($data);
    	if(isset($_POST['id']) and isset($_POST['src']) and isset($_POST['idforeign'] ))
    	{
    		$arrayDetail = array(
    				'id'=>(int)$_POST['id'],
    				'src'=>$_POST['src'],
    				'idforeign'=>$_POST['idforeign'], // id save
    		);
    	
    		$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    		$comments = new Comments();
    		$renname_file_img = $this->uploadImageAlatca($_POST['src']);
    		$comments->dataArraySwap($arrayDetail,WEB_PATH_IMG.'/'.$renname_file_img);
    		$resultsave = $this->getCommentsTable()->saveComments($comments);
    		if($resultsave > 0){ $text = "successfully processed";} else {$text = "False";}
    	
    	}
    	$result = new JsonModel ( array (
    			'result' =>$test
    	) );
    	
    	return $result;
    }
    
    
    
    public function pationAction() {
    	// check login
    	//     	if (!$this->zfcUserAuthentication()->hasIdentity()) {
    	//     		return $this->redirect()->toRoute('zfcuser/login');
    	//     	}
    		 
    	 
    	$select = new Select();
    
    	$order_by = $this->params()->fromRoute('order_by') ? $this->params()->fromRoute('order_by') : 'id';
    	$order = $this->params()->fromRoute('order') ?  $this->params()->fromRoute('order') : Select::ORDER_DESCENDING;
    	$page = $this->params()->fromRoute('page') ? (int) $this->params()->fromRoute('page') : 1;
    
    	$contendetails = $this->getContendetailTable()->fetchAllJoih($select->order($order_by . ' ' . $order));
    
    	return new ViewModel(array(
    			'order_by' => $order_by,
    			'order' => $order,
    			'page' => $page,
    			//'paginatorimg' => $paginator,
    			'paginatorimg' =>$contendetails,
    	));
    }

    public function addAction() {
    	$id = (int)$this->params()->fromRoute('id');
    	if ($id == 0) {
    		return $this->redirect()->toRoute('igirlxinhcom');
    	}
    	$ig = (int)$this->params()->fromRoute('ig');
    	
    	
    	
    	$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    
        $form = new ContendetailForm($dbAdapter,$id); // include Form Class
       
        $form->get('submit')->setAttribute('value', 'Add');
       
        $request = $this->getRequest();
       
        if ($request->isPost()) {
        	
            $contendetail = new Contendetail();

            $form->setInputFilter($contendetail->getInputFilter());  // check validate
            
            $data = array_merge_recursive(
            		$this->getRequest()->getPost()->toArray(),
            		$this->getRequest()->getFiles()->toArray()
            );
          
            $form->setData($data);  // get all post

            if ($form->isValid()) {
            	
            	$renname_file_img = $this->uploadImageAlatca($data ['src']);
                $contendetail->dataArraySwap($data,$renname_file_img);

                $this->getContendetailTable()->saveContendetail($contendetail);
                // Redirect to list of contendetails
                return $this->redirect()->toRoute('contendetail');
            }
            
           
            
        }
        
       

        return array(
        		'form' => $form,
        		
                    );
    }
    
    
    
    
    public function adddetailAction() {
    	
    	$id = (int)$this->params ()->fromRoute ( 'id', 0 );

    	$contendetailArray  = $this->getContendetailTable ()->fetchAllDetailContendetail ($id);
    	
    	
    	$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    
    	$form = new FromClass($dbAdapter,$id); // include Form Class
    	 
    	$form->get('submit')->setAttribute('value', 'Add');
    	 
    	$request = $this->getRequest();
    	 
    	if ($request->isPost()) 
    	{
    	 
    	$contendetail = new Contendetail();
    
    	$form->setInputFilter($contendetail->getInputFilter());  // check validate
    
    	$data = array_merge_recursive(
    			$this->getRequest()->getPost()->toArray(),
    	        $this->getRequest()->getFiles()->toArray()
    	);
    
  
    	$form->setData($data);  // get all post
    
    	if ($form->isValid()) {
    	
    		$renname_file_img = $this->uploadImageAlatca($data ['src']);
    	    $contendetail->dataArraySwap($data,$renname_file_img);
    		$this->getContendetailTable()->saveContendetail($contendetail);
    		// Redirect to list of contendetails
    		return $this->redirect()->toRoute('contendetail');
    	}
    	}
    
    	return new ViewModel ( array (
    			'paginatorimg' => $contendetailArray,
    			'form' => $form,
    			'id' => $id,
    	) );
  	}
    
   
  
  
    public function editAction() {
    	
    	$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
    	
        $id = (int) $this->params('id');
        
        if (!$id) {
            return $this->redirect()->toRoute('contendetail', array('action' => 'add'));
        }

        echo "hien view content";
        var_dump($id);
        die;
        
        $contendetail = $this->getContendetailTable()->getContendetail($id);
        
        $form = new FromClass($dbAdapter,$id);
        
        $form->bind($contendetail);
        
        $form->get('submit')->setAttribute('value', 'Edit');

        $request = $this->getRequest();
        
        if ($request->isPost()) {
        	
        	$data = array_merge_recursive(
        			$this->getRequest()->getPost()->toArray(),
        			$this->getRequest()->getFiles()->toArray()
        	);
        	
            $form->setData($data);
            
            if (!$form->isValid()) {
            	
            	$size = new Size(array('min'=>2000000)); //minimum bytes filesize
            	 
            	$adapter = new \Zend\File\Transfer\Adapter\Http();
            	$adapter->setValidators(array($size), $data['src']['size']);
            	$extension = new \Zend\Validator\File\Extension(array('extension' => array('gif', 'jpg', 'png')));
            	
            			if ($adapter->isValid())
            			{
            				
            	
            				$adapter->setDestination(MZIMG_PATH);
            				if ($adapter->receive($data['src']['name'])) {
            					$profile = new Contendetail();
            					
            				}
            				 
            			}
            			 
            	
            	$contendetail2 = new Contendetail();
            	$contendetail2->dataPost($data);
                $this->getContendetailTable()->saveContendetail($contendetail2);

                // Redirect to list of contendetails
                return $this->redirect()->toRoute('contendetail');
            }
        }

        return array(
            'id' => $id,
            'form' => $form,
        );
    }

    public function deleteAction() {
        $id = (int) $this->params('id');
        $igirl_id = (int) $this->params('ig');
        if (!$id) {
            return $this->redirect()->toRoute('contendetail');
        }
		
//         echo "delete action";
//         var_dump($id);
//         die;
      
        $request = $this->getRequest();
        if ($request->isPost()) {
            $del = $request->getPost()->get('del', 'No');
            if ($del == 'Yes') {
                $id = (int) $request->getPost()->get('id');
                $this->getContendetailTable()->deleteContendetail($id);
                
                // delete src
              // $del = $this->deleteImage($image, $dir);
            }

            // Redirect to list of contendetails
            return $this->redirect()->toRoute('igirlxinhcom',
				  array('controller'=>'Igirlxinhcom',
				        'action' => 'readdetail',
				        'id' =>$igirl_id));

            
        }

        return array(
            'id' => $id,
        	'igirl_id'=>$igirl_id,
            'contendetail' => $this->getContendetailTable()->getContendetail($id)
        );
    }

    public function getContendetailTable() {
        if (!$this->contendetailTable) {
            $sm = $this->getServiceLocator();
            $this->contendetailTable = $sm->get('Contendetail\Model\ContendetailTable');
        }
        return $this->contendetailTable;
    }
    
    
    public function createImageThumbnail($filePathName, $destinationPath, $options) {
    	$arr = explode('/', $filePathName);
    	$file_name = end($arr);
    
    	$new_file_name = 'thumb_' . $file_name;
    	$new_file_path = $destinationPath . '/' . $new_file_name;
    
    	list($img_width, $img_height) = @getimagesize($filePathName);
    	if (!$img_width || !$img_height) {
    		return false;
    	}
    	$scale = min(
    			$options['max_width'] / $img_width, $options['max_height'] / $img_height
    	);
    	$new_width = $img_width * $scale;
    	$new_height = $img_height * $scale;
    	$new_img = @imagecreatetruecolor($new_width, $new_height);
    	switch (strtolower(substr(strrchr($file_name, '.'), 1))) {
    		case 'jpg':
    		case 'jpeg':
    			$src_img = @imagecreatefromjpeg($filePathName);
    			$write_image = 'imagejpeg';
    			$image_quality = isset($options['jpeg_quality']) ?
    			$options['jpeg_quality'] : 75;
    			break;
    		case 'gif':
    			@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
    			$src_img = @imagecreatefromgif($filePathName);
    			$write_image = 'imagegif';
    			$image_quality = null;
    			break;
    		case 'png':
    			@imagecolortransparent($new_img, @imagecolorallocate($new_img, 0, 0, 0));
    			@imagealphablending($new_img, false);
    			@imagesavealpha($new_img, true);
    			$src_img = @imagecreatefrompng($filePathName);
    			$write_image = 'imagepng';
    			$image_quality = isset($options['png_quality']) ?
    			$options['png_quality'] : 9;
    			break;
    		default:
    			$src_img = null;
    	}
    	$success = $src_img && @imagecopyresampled(
    			$new_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height
    	) && $write_image($new_img, $new_file_path, $image_quality);
    	// Free up memory (imagedestroy does not delete files):
    	@imagedestroy($src_img);
    	@imagedestroy($new_img);
    	if ($success)
    		return $new_file_name;
    	return $success;
    }
    
    
    public function deleteImage($image, $dir)
     {
    	try {
    		$this->deleteFile($dir .'/'. $image);
    		$this->deleteFile($dir .'/thumb_/thumb_'. $image);
    
    		//$logger->writeLog("DEBUG", $userEmail, $arrLog[0], $arrLog[1], "Delete image, file : " . $dir .'/'. $image, ">>");
    		//$logger->writeLog("INFO", $userEmail, $arrLog[0], $arrLog[1], "Delete image, file : " . $dir .'/thumb_/thumb_'. $image, ">>");
    	} catch (\Exception $exc) {
    		$this->errorMessage = $exc->getMessage();
    	}
    
    
    }
    public function uploadImage($imageData = array(), $dir, $createThumb = true, $options = array()) {
    
    	if (!empty($imageData)) {
    		$fileName = time() . '.jpg';
    		$dirFileName = $dir .'/'. $fileName;
    
    		$filter = new \Zend\Filter\File\RenameUpload($dirFileName);
    		if ($filter->filter($imageData)) {
    			if($createThumb){
    				$options = (!empty($options)) ? $options : array('max_width' => 65, 'max_height' => 65, 'jpeg_quality' => 100);
    				$this->createImageThumbnail($dirFileName, $dir . '/thumb_', $options);
    			}
    
    
    			return $fileName;
    		}
    	}
    
    	return false;
    }
    
    public function uploadImageAlatca($imageData = array()) {
    	if (!empty($imageData)) {
    		$fileName = time() . '.jpg';
    		$dir = ROOT_PATH . UPLOAD_PATH_IMG;
    		$dirFileName = $dir .'/'. $fileName;
    
    		$filter = new \Zend\Filter\File\RenameUpload($dirFileName);
    		if ($filter->filter($imageData)) {
    			$options = array('max_width' => 102, 'max_height' => 102, 'jpeg_quality' => 100);
    			$this->createImageThumbnail($dirFileName, $dir . '/thumb_', $options);
    			return $fileName;
    		}
    	}
    	return false;
    }
    
    public function deleteFile($file_path) {
    	if (!empty($file_path) && file_exists($file_path)) {
    		return @unlink($file_path);
    	}
    	return false;
    }
    

}
