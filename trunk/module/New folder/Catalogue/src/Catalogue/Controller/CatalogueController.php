<?php

namespace Catalogue\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Mylibrary\Feed;

// use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Catalogue\Model\Catalogue;
use Catalogue\Form\CatalogueForm;
// use Catalogue\Form\MagazineForm;
use Catalogue\Form\CatalogueSearchForm as SearchFromCatalogue;
use Mzimg\Model\Mzimg;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
// check login
use ZfcUser\Service\User as UserService;
use ZfcUser\Options\UserControllerOptionsInterface;
use Zend\Validator\File\Size;
use Zend\Validator\File\Extension;
use Mzimg\Model\MzimgTable;

class CatalogueController extends AbstractActionController {
	protected $catalogueTable;
	public function searchAction() {
		$request = $this->getRequest ();
		
		$url = 'index';
		
		if ($request->isPost ()) {
			$formdata = ( array ) $request->getPost ();
			$search_data = array ();
			foreach ( $formdata as $key => $value ) {
				if ($key != 'submit') {
					if (! empty ( $value )) {
						$search_data [$key] = $value;
					}
				}
			}
			if (! empty ( $search_data )) {
				$search_by = json_encode ( $search_data );
				$url .= '/search_by/' . $search_by;
			}
		}
		$this->redirect ()->toUrl ( $url );
	}
	public function indexAction() {
		// check login
		// if (! $this->zfcUserAuthentication ()->hasIdentity ()) {
		// return $this->redirect ()->toRoute ( 'zfcuser/login' );
		// } else {
		
		// $crawler = new Feed();
		// $arr = "phuc";
		// $crawler->phuc();
		// die;
		$searchform = new SearchFromCatalogue ();
		$searchform->get ( 'submit' )->setValue ( 'Search' );
		
		$select = new Select ();
		
		$order_by = $this->params ()->fromRoute ( 'order_by' ) ? $this->params ()->fromRoute ( 'order_by' ) : 'id';
		$order = $this->params ()->fromRoute ( 'order' ) ? $this->params ()->fromRoute ( 'order' ) : Select::ORDER_DESCENDING;
		$page = $this->params ()->fromRoute ( 'page' ) ? ( int ) $this->params ()->fromRoute ( 'page' ) : 1;
		$search_by = $this->params ()->fromRoute ( 'search_by' ) ? $this->params ()->fromRoute ( 'search_by' ) : '';
		$select->order ( $order_by . ' ' . $order );
		$where = new \Zend\Db\Sql\Where ();
		$formdata = array ();
		if (! empty ( $search_by )) {
			$formdata = ( array ) json_decode ( $search_by );
			if (! empty ( $formdata ['descriptionkey'] )) {
				$where->addPredicate ( new \Zend\Db\Sql\Predicate\Like ( 'descriptionkey', '%' . $formdata ['descriptionkey'] . '%' ) );
			}
			if (! empty ( $formdata ['title'] )) {
				$where->addPredicate ( new \Zend\Db\Sql\Predicate\Like ( 'title', '%' . $formdata ['title'] . '%' ) );
			}
		}
		if (! empty ( $where )) {
			$select->where ( $where );
		}
		
		$catalogues = $this->getCatalogueTable ()->fetchAll ( $select );
		$itemsPerPage = 10; // is Number record/page
		$totalRecord = $catalogues->count ();
		$catalogues->current ();
		$paginator = new Paginator ( new paginatorIterator ( $catalogues ) );
		$paginator->setCurrentPageNumber ( $page )->setItemCountPerPage ( $itemsPerPage )->setPageRange ( 4 ); // is number page want view
		
		return new ViewModel ( array (
				'search_by' => $search_by,
				'order_by' => $order_by,
				'order_by' => $order_by,
				'order' => $order,
				'page' => $page,
				'paginatorcatalogue' => $paginator,
				'pageAction' => 'catalogues ',
				'form' => $searchform,
				'totalRecord' => $totalRecord 
		) );
		// } // login
	}
	public function addAction() {
		$dbAdapter = $this->getServiceLocator ()->get ( 'Zend\Db\Adapter\Adapter' );
		$form = new CatalogueForm ( $dbAdapter ); // include Form Class
		$form->get ( 'submit' )->setAttribute ( 'value', 'Add' );
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$catalogue = new Catalogue ();
			
			$form->setInputFilter ( $catalogue->getInputFilter () ); // check validate
			
			$data = array_merge_recursive ( $this->getRequest ()->getPost ()->toArray (), $this->getRequest ()->getFiles ()->toArray () );
			
			$form->setData ( $data ); // get all post
			
// 			echo 'validate ';
// 			var_dump ( $form->isValid () );
// 			echo '<pre>';
// 			print_r ( $data );
// 			echo '</pre>';
// 			die ();
			
			if ($form->isValid ()) {
				
				$renname_file_img = $this->uploadImageAlatca ( $data ['imgkey'] );
				$catalogue->dataArraySwap ( $data, $renname_file_img );
				
				$this->getCatalogueTable ()->saveCatalogue ( $catalogue );
				// Redirect to list of catalogues
				return $this->redirect ()->toRoute ( 'catalogue' );
			} else {
				// echo('Magazine is Form Not Validate');
			}
		}
		
		return array (
				'form' => $form 
		);
	}
	public function adddetailAction() {
		$id = $this->params ()->fromRoute ( 'id', 0 );
		
		$mzimgArray = $this->getCatalogueTable ()->fetchAllDetailMzimg ( $id );
	
		$dbAdapter = $this->getServiceLocator ()->get ( 'Zend\Db\Adapter\Adapter' );
		
		$form2 = new CatalogueForm ( $dbAdapter ); // include Form Class
		
		$form2->get ( 'submit' )->setAttribute ( 'value', 'Add' );
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$mzimg = new Mzimg ();
			
			$form2->setInputFilter ( $mzimg->getInputFilter () ); // check validate
			
			$data2 = array_merge_recursive ( $this->getRequest ()->getPost ()->toArray (), $this->getRequest ()->getFiles ()->toArray () );
		
			$form2->setData ( $data2 ); // get all post
			
		
			
			if (! $form2->isValid ()) {
				
				$size = new Size ( array (
						'min' => 2000000 
				) ); // minimum bytes filesize
				
				$adapter = new \Zend\File\Transfer\Adapter\Http ();
				$adapter->setValidators ( array (
						$size 
				), $data2 ['img'] ['size'] );
				$extension = new \Zend\Validator\File\Extension ( array (
						'extension' => array (
								'gif',
								'jpg',
								'png' 
						) 
				) );
				
				if (! $adapter->isValid ()) {
					
					echo 'is not valid';
					
					$dataError = $adapter->getMessages ();
					
					$error = array ();
					foreach ( $dataError as $key => $row ) {
						$error [] = $row;
					}
					
					$form->setMessages ( array (
							'img' => $error 
					) );
				}
				if ($adapter->isValid ()) {
					
					// echo 'is valid';
					// var_dump(MZIMG_PATH);
					// var_dump($data2['img']);
					// die;
					
					$adapter->setDestination ( MZIMG_PATH );
					if ($adapter->receive ( $data2 ['img'] ['name'] )) {
						$profile = new Mzimg ();
						// $profile->exchangeArray($form2->getData());
					}
				}
				
				$mzimg->dataArray ( $form2->getData () );
				
				die ( " Error Connectting  Action save Modul Mzimg " );
				
				$mzimgTable = new MzimgTable ();
				
				$mzimgTable->saveMzimg ( $mzimg );
				
				// Redirect to list of Mzimgs
				return $this->redirect ()->toRoute ( 'mzimg' );
			} else {
				die ( 'is not not valid Dedatil' );
			}
		}
		
		return new ViewModel ( array (
				'paginatorimg' => $mzimgArray,
				'form' => $form2,
				'id' => $id 
		) );
	}
	public function editAction() {
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'catalogue', array (
					'action' => 'add' 
			) );
		}
		$catalogue = $this->getCatalogueTable ()->getCatalogue ( $id );
		$nameimg = $catalogue->imgkey;
		
		$dbAdapter = $this->getServiceLocator ()->get ( 'Zend\Db\Adapter\Adapter' );
		$form = new CatalogueForm ( $dbAdapter );
		$form->bind ( $catalogue );
		$form->get ( 'submit' )->setAttribute ( 'value', 'Edit' );
		
		$request = $this->getRequest ();
		
		if ($request->isPost ()) {
			
			$data = array_merge_recursive ( $this->getRequest ()->getPost ()->toArray (), $this->getRequest ()->getFiles ()->toArray () );
	
			$form->setData ( $data );
			
// 			echo 'validate ';
// 			var_dump ( $form->isValid () );
// 			echo '<pre>';
// 			print_r ( $data );
// 			echo '</pre>';
// 			die ();
			
			if ($form->isValid ()) {
				if ($data ['imgkeyedit'] ['name'] !== '') {
						
					$catalogue2 = new Catalogue ();
					$renname_file_img = $this->uploadImageAlatca ( $data ['imgkeyedit'] );
					$catalogue2->dataArraySwap ( $data, $renname_file_img );
					$this->getCatalogueTable ()->saveCatalogue2 ( $catalogue2 );
				} else {
						
					$catalogue2 = new Catalogue ();
					$catalogue2->dataArraySwap ( $data, $nameimg );
					$this->getCatalogueTable ()->saveCatalogue2 ( $catalogue2 );
				}
				
			
				// Redirect to list of catalogues
				return $this->redirect ()->toRoute ( 'catalogue' );
			}
		}
		
		return array (
				'id' => $id,
				'form' => $form ,
				'nameimg' => $nameimg
		);
	}
	public function deleteAction() {
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'catalogue' );
		}
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$del = $request->getPost ()->get ( 'del', 'No' );
			if ($del == 'Yes') {
				$id = ( int ) $request->getPost ()->get ( 'id' );
				$this->getCatalogueTable ()->deleteCatalogue ( $id );
			}
			
			// Redirect to list of catalogues
			return $this->redirect ()->toRoute ( 'catalogue' );
		}
		
		return array (
				'id' => $id,
				'catalogue' => $this->getCatalogueTable ()->getCatalogue ( $id ) 
		);
	}
	public function getCatalogueTable() {
		if (! $this->catalogueTable) {
			$sm = $this->getServiceLocator ();
			$this->catalogueTable = $sm->get ( 'Catalogue\Model\CatalogueTable' );
		}
		return $this->catalogueTable;
	}
	public function createImageThumbnail($filePathName, $destinationPath, $options) {
		$arr = explode ( '/', $filePathName );
		$file_name = end ( $arr );
		
		$new_file_name = 'thumb_' . $file_name;
		$new_file_path = $destinationPath . '/' . $new_file_name;
		
		list ( $img_width, $img_height ) = @getimagesize ( $filePathName );
		if (! $img_width || ! $img_height) {
			return false;
		}
		$scale = min ( $options ['max_width'] / $img_width, $options ['max_height'] / $img_height );
		$new_width = $img_width * $scale;
		$new_height = $img_height * $scale;
		$new_img = @imagecreatetruecolor ( $new_width, $new_height );
		switch (strtolower ( substr ( strrchr ( $file_name, '.' ), 1 ) )) {
			case 'jpg' :
			case 'jpeg' :
				$src_img = @imagecreatefromjpeg ( $filePathName );
				$write_image = 'imagejpeg';
				$image_quality = isset ( $options ['jpeg_quality'] ) ? $options ['jpeg_quality'] : 75;
				break;
			case 'gif' :
				@imagecolortransparent ( $new_img, @imagecolorallocate ( $new_img, 0, 0, 0 ) );
				$src_img = @imagecreatefromgif ( $filePathName );
				$write_image = 'imagegif';
				$image_quality = null;
				break;
			case 'png' :
				@imagecolortransparent ( $new_img, @imagecolorallocate ( $new_img, 0, 0, 0 ) );
				@imagealphablending ( $new_img, false );
				@imagesavealpha ( $new_img, true );
				$src_img = @imagecreatefrompng ( $filePathName );
				$write_image = 'imagepng';
				$image_quality = isset ( $options ['png_quality'] ) ? $options ['png_quality'] : 9;
				break;
			default :
				$src_img = null;
		}
		$success = $src_img && @imagecopyresampled ( $new_img, $src_img, 0, 0, 0, 0, $new_width, $new_height, $img_width, $img_height ) && $write_image ( $new_img, $new_file_path, $image_quality );
		// Free up memory (imagedestroy does not delete files):
		@imagedestroy ( $src_img );
		@imagedestroy ( $new_img );
		if ($success)
			return $new_file_name;
		return $success;
	}
	public function deleteImage($image, $dir) {
		try {
			$this->deleteFile ( $dir . '/' . $image );
			$this->deleteFile ( $dir . '/thumb_/thumb_' . $image );
			
			// $logger->writeLog("DEBUG", $userEmail, $arrLog[0], $arrLog[1], "Delete image, file : " . $dir .'/'. $image, ">>");
			// $logger->writeLog("INFO", $userEmail, $arrLog[0], $arrLog[1], "Delete image, file : " . $dir .'/thumb_/thumb_'. $image, ">>");
		} catch ( \Exception $exc ) {
			$this->errorMessage = $exc->getMessage ();
		}
	}
	public function uploadImage($imageData = array(), $dir, $createThumb = true, $options = array()) {
		if (! empty ( $imageData )) {
			$fileName = time () . '.jpg';
			$dirFileName = $dir . '/' . $fileName;
			
			$filter = new \Zend\Filter\File\RenameUpload ( $dirFileName );
			if ($filter->filter ( $imageData )) {
				if ($createThumb) {
					$options = (! empty ( $options )) ? $options : array (
							'max_width' => 65,
							'max_height' => 65,
							'jpeg_quality' => 100 
					);
					$this->createImageThumbnail ( $dirFileName, $dir . '/thumb_', $options );
				}
				
				return $fileName;
			}
		}
		
		return false;
	}
	public function uploadImageAlatca($imageData = array()) {
		if (! empty ( $imageData )) {
			$fileName = time () . '.jpg';
			$dir = ROOT_PATH . UPLOAD_PATH_IMG;
			$dirFileName = $dir . '/' . $fileName;
			
			$filter = new \Zend\Filter\File\RenameUpload ( $dirFileName );
			if ($filter->filter ( $imageData )) {
				$options = array (
						'max_width' => 102,
						'max_height' => 102,
						'jpeg_quality' => 100 
				);
				$this->createImageThumbnail ( $dirFileName, $dir . '/thumb_', $options );
				return $fileName;
			}
		}
		return false;
	}
	public function deleteFile($file_path) {
		if (! empty ( $file_path ) && file_exists ( $file_path )) {
			return @unlink ( $file_path );
		}
		return false;
	}
}
