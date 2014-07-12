<?php

namespace Uploadlibrarybooks\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Uploadlibrarybooks\Model\Uploadlibrarybooks as UploadlibrarybooksModel;
use Uploadlibrarybooks\Model\UploadlibrarybooksTable;
use Uploadlibrarybooks\Form\UploadlibrarybooksForm;
use Zend\Filter\Compress\zip;
use Storydetail\Model\Storydetail;
use Zend\Filter\Exception;
use ZipArchive;
use Zend\Db\Sql\Select;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;

class UploadlibrarybooksController extends AbstractActionController {
	protected $magazinepublish;
	
	public function indexAction() {
		$form = new UploadForm ();
		$view = new ViewModel ( array (
				'form' => $form
		) );
		$request = $this->getRequest ();
	
		if ($request->isPost ()) {
				
			$files = $request->getFiles ()->toArray ();
			$fileName = $files ['picture'] ['name'];
				
			$uploadObj = new \Zend\File\Transfer\Adapter\Http ();
			$uploadObj->setDestination ( MZIMG_PATH );
				
			if ($uploadObj->receive ( $fileName )) {
				echo "<br>Upload success";
			}
		}
	
		return $view;
	}
	public function abcAction() {
		$form = new UploadForm ();
		$view = new ViewModel ( array (
				'form' => $form
		) );
	
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			echo 'Tap tin vua upload len server la: ';
			echo $form->upload ( $request->getFiles ()->toArray (), MZIMG_PATH );
		}
	
		return $view;
	}
	public function multiAction() {
		$form = new \Upload\Form\MultiUploadForm ();
		$view = new ViewModel ( array (
				'form' => $form
		) );
	
		$request = $this->getRequest ();
		if ($request->isPost ()) {
				
			echo 'Tap tin vua upload len server la: ';
			$files = $form->upload ( $request->getFiles ()->toArray (), FILES_PATH );
				
			// 			echo "<pre>";
			// 			print_r ( $files );
			// 			echo "</pre>";
		}
	
		return $view;
	}
	
	public function uploadnewAction() 
	{
		$dbAdapter = $this->getServiceLocator()->get('Zend\Db\Adapter\Adapter');
		$form = new UploadlibrarybooksForm ($dbAdapter);
		//Upload
		$form->get( 'submit' )->setAttribute( 'value', 'Upload' );
		$backUrl = Null;
		$request = $this->getRequest ();
			
		if ($request->isPost ())
		{
				
			$upload = new UploadlibrarybooksModel();
				
			$form->setInputFilter( $upload->getInputFilter() ); // check validate
				
			$data = array_merge_recursive ( $this->getRequest ()->getPost ()->toArray (), $this->getRequest ()->getFiles ()->toArray () );
				
			$form->setData ( $data ); // get all post
	
			// Validate the form
			if ($form->isValid()) {

// 				echo 'data';
// 				echo '<pre>';
// 				print_r($data);
// 				echo '</pre>';
				
// 				echo '</br>';
// 				echo 'data zip';
// 				echo '<pre>';
// 				print_r($data['zip_file']);
// 				echo '</pre>';
				

				   // Move and Upload Imgkey ,upload + creat thumbnail
				    $renamefile = $this->uploadImageAlatca($data['imgkey']); // Name  imgkey i s renmane
				  // Save Database 
				    $upload->dataArraySwap($form->getData(),$renamefile);
					$id_curent_row_upload = $this->getUploadlibrarybooksTable()->saveUploadlibrarybooks($upload);
					
				  if($id_curent_row_upload == Null)
				  {
				  	die('Oop! Error , Not Save Database . Please try again!');
				  }
				
				  
					
				 if (!empty($data['zip_file']))
					 { 		
						if ($data ["zip_file"] ["name"]) {
							$filename = $data ["zip_file"] ["name"];
							$source = $data ["zip_file"] ["tmp_name"];
							$type = $data ["zip_file"] ["type"];
						
					
							$name = explode ( ".", $filename );
							$accepted_types = array (
									'application/zip',
									'application/x-zip-compressed',
									'multipart/x-zip',
									'application/x-compressed' 
							);
							foreach ( $accepted_types as $mime_type ) {
								if ($mime_type == $type) 
								{
									$okay = true;
									break;
								}
							}
							//check file zip
							$continue = strtolower ( $name [1] ) == 'zip' ? true : false;
							
				
							if (! $continue) {
								$myMsg = "Please upload a valid .zip file.";
								//$backUrl = $this->url()->fromRoute('upload', array('action' => 'uploadnew', 'id' => null), array(), true);
								die("Please upload a valid .zip file. Oops! This project has not Support!" );
							}
							
						
							$path = ROOT_PATH.PATH_ZIP;
						
							$filenoext = basename ( $filename, '.zip' );
							$filenoext = basename ( $filenoext, '.ZIP' );
							
							$myDir = $path . $filenoext; // target directory
							$myFile = $path . $filename; // target zip file
							
							if (is_dir( $myDir ))
							{
								try {
							    	$this->recursive_dir ( $myDir );
							    	mkdir ( $myDir, 0777 );
							    } catch (Exception $e) {
							    }
							}
							elseif(file_exists( $myDir ))
							{
								//var_dump($path);
								die("Oop ! Error , Directoty of Project upload  is Exits !");
							}
							
							
							$dirimg = $path.$name[0];
				
							if (move_uploaded_file ( $source, $myFile ))
							 {
								$zip = new ZipArchive ();
								
								$x = $zip->open ( $myFile ); // open the zip file to extract
								if ($x === true) {
									$zip->extractTo ( $myDir ); // place in the directory with same name
									$zip->close ();
									unlink ( $myFile );
								}
							
								//Rename All 
								$rename = $this->sequentialImages($dirimg,false);
							
								// Read File 
								$imgArray = $this->readallimg($dirimg);
								
// 								echo '</br>';
// 								echo 'All Img';
// 								echo '<pre>';
// 								print_r($imgArray);
// 								echo '</pre>';
							   
								// save data base Upload detail 
								$returnResult = $this->getUploadlibrarybooksTable()->getInsertUploadDetail($imgArray , $id_curent_row_upload, $name[0]);
								
							    if($returnResult === 1)
							    {
							    	$backUrl = $this->url()->fromRoute('uploadlibrarybooks', array('action' => 'readdetail', 'id' => $id_curent_row_upload), array(), true);
							    }else {
							    	die('Oop! Error . Please try again');
							    	
							    }
							   
							    
								echo $myMsg = "<h3 style ='text-align: center;color: red;'>Your .zip file uploaded and unziped.</h3>";
								
								
							} else {
								echo $myMsg = "There was a problem with the upload.";
							}
						}
						
					 }//end if empty	
					
					
			} else {
				$messages = $form->getMessages();
				//die($messages);
			}
				
				
		}
	
		
		
	 $view = new ViewModel ( array (
	 		'form' => $form,
	 		'backUrl'=>$backUrl,
	 ) );
	 
		
		 return $view;
	
	}
	
	
	public function readdetailAction()
	{
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'uploadlibrarybooks' );
		}
	
		$read = $this->getUploadlibrarybooksTable ()->getReadUploadlibrarybooksdetail( $id ) ;
		return array (
				'id' => $id,
				'readdetail' => $read,
		);
	}
	
	public function getUploadlibrarybooksTable() {
		if (! $this->magazinepublish) {
			$sm = $this->getServiceLocator ();
			$this->magazinepublish = $sm->get ( 'Uploadlibrarybooks\Model\UploadlibrarybooksTable' );
		}
		return $this->magazinepublish;
	}
	
	public function recursive_dir($dir) {
		foreach ( scandir ( $dir ) as $file ) {
			if ('.' === $file || '..' === $file)
				continue;
			if (is_dir ( "$dir/$file" ))
				recursive_dir ( "$dir/$file" );
			else
				unlink ( "$dir/$file" );
		}
		rmdir ( $dir );
	}
	
	public function sequentialImages($path, $sort=false) {
		$i = 1;
		
		//burg only suport file .JPG
		//$files = glob($path.'{*.gif,*.jpg,*.jpeg,*.png}',GLOB_BRACE|GLOB_NOSORT);
		
		$files = glob($path.'/*.JPG',GLOB_BRACE|GLOB_NOSORT);
	
		if ( $sort !== false ) {
			usort($files, $sort);
		}
	
		$count = count($files);
		
		//return $files;
		
		foreach ( $files as $file ) {
			$newname = str_pad($i, strlen($count)+1, '0', STR_PAD_LEFT);
			$ext = substr(strrchr($file, '.'), 1);
			$newname = $path.'/'.$newname.'.'.$ext;
			if ( $file != $newname ) {
				rename($file, $newname);
			}
			$i++;
		}
	}
	
	public function sort_by_mtime($file1, $file2) {
		$time1 = filemtime($file1);
		$time2 = filemtime($file2);
		if ( $time1 == $time2 ) {
			return 0;
		}
		return ($time1 < $time2) ? 1 : -1;
	}
	
	
	public function readallimg($dir = null)
	{
		if($dir !== Null)
		{
			if ($opendir = opendir($dir))
			{
			
				$images=array();
				while (($file = readdir($opendir)) !==FALSE)
				{
					if($file != "." && $file != "..")
					{
						$images["$file"]=$file;
					}
				}
			}
			sort($images,SORT_STRING);
			
			// Renname
			foreach ($images as $image)
			{
				//rename($image, "/home/user/login/docs/my_file.txt");
			}
			
	   }
		
		return $images;
		
// 		foreach($images as $image)
// 		{
// 			echo "<img src='$dir/$image'><br>\n";
// 		}
		
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
	
	
	public function deleteImage($image, $dir) {
		try {
			$this->deleteFile($dir .'/'. $image);
			$this->deleteFile($dir .'/thumb_/thumb_'. $image);
	
			$logger->writeLog("DEBUG", $userEmail, $arrLog[0], $arrLog[1], "Delete image, file : " . $dir .'/'. $image, ">>");
			$logger->writeLog("INFO", $userEmail, $arrLog[0], $arrLog[1], "Delete image, file : " . $dir .'/thumb_/thumb_'. $image, ">>");
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
