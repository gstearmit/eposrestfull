<?php

namespace Application\Controller\Plugin;

use Zend\View\Model\ViewModel;
use Zend\Mvc\Controller\Plugin\AbstractPlugin;

use Zend\Form\View\HelperConfig;
use Zend\View\Helper\Doctype;
use Zend\View\Renderer\PhpRenderer;

use Zend\Form\Element;
use Zend\Form\Element\MultiCheckbox as MultiCheckboxElement;
use Zend\Form\View\Helper\FormMultiCheckbox as FormMultiCheckboxHelper;

/** 
 * System Models
 */


class CommonHelper extends AbstractPlugin {
	protected $helper;
    protected $renderer;
	protected $errorMessage;

	public function removeDirectory($dir = '') {
		if (!empty($dir)) {
			if (is_dir($dir)) {
				$objects = scandir($dir);
				foreach ($objects as $object) {
					if ($object != "." && $object != "..") {
						if (filetype($dir . "/" . $object) == "dir")
							$this->removeDirectory($dir . "/" . $object);
						else
							$this->deleteFile($dir . "/" . $object);
					}
				}
				reset($objects);
				rmdir($dir);
			} 
		}
	}
	
	public function deleteFile($file_path) {
        if (!empty($file_path) && file_exists($file_path)) {
            return @unlink($file_path);
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
	
	public function createDatabase($dbName) {
        
        $check = $this->isExistDatabase($dbName);
        if (false === $check) {
            $params = $this->getParamsConnection();
            $dbObj = $sm->get("db_mysql");
            $adapter = $sm->get("db_adapter")->getAdapter(array('username' => $params['user']
                                                                , 'password' => $params['password']
                                                                , 'database' => $params['dbname']
                                                                , 'hostname' => $params['host']));
            $dbObj->setAdapter($adapter);
            $arr = $dbObj->createDb($dbName, self::generateCode(10), 'localhost');
            $arr["@host"] = $this->getEcosystemDbHost();
            
         
            return $arr;
        }
        
        $logger->writeLog("INFO", $userEmail, $arrLog[0], $arrLog[1], "Create Database", "<<");
        return false;
    }
	
    public function dropDatabase($dbName) {
      
        if (!empty($dbName)) {
            $dbObj = $sm->get("db_mysql");
            /**
             * get parameters of master database (perseed database)
             */
            $params = $this->getParamsConnection();
            /**
             * Set adapter to connect to master database (perseed database)
             */
            $adapter = $sm->get("db_adapter")->getAdapter(array('username' => $params['user']
                                                                , 'password' => $params['password']
                                                                , 'database' => $params['dbname']
                                                                , 'hostname' => $params['host']));
            $dbObj->setAdapter($adapter);
            $dbObj->dropDb($params, $dbName);
            
          
            return true;
        }
        
      
        return false;
    }
    
    public function dropDbUser($dbUser, $dbHost) {
      
        
        if (!empty($dbUser) && !empty($dbHost)) {
            /**
             * get parameters of master database (perseed database)
             */
            $params = $this->getParamsConnection();
            $sm = $this->getController()->getServiceLocator();

            $dbObj = $sm->get("db_mysql");
            /**
             * Set adapter to connect to master database (perseed database)
             */
            $adapter = $sm->get("db_adapter")->getAdapter(array('username' => $params['user']
                                                                , 'password' => $params['password']
                                                                , 'database' => $params['dbname']
                                                                , 'hostname' => $params['host']));
            $dbObj->setAdapter($adapter);
            $dbObj->dropDbUser($dbUser, $dbHost);
            
            $logger->writeLog("DEBUG", $userEmail, $arrLog[0], $arrLog[1], "Drop database's user, data: [user: {$dbUser}, host: {$dbHost}]", ">>");
            return true;
        }
        
        $logger->writeLog("INFO", $userEmail, $arrLog[0], $arrLog[1], "Drop database's user", "<<");
        return false;
    }
	
    public function isExistDatabase($dbName) {
        if (!empty($dbName)) {
            $sm = $this->getController()->getServiceLocator();
            $dbObj = $sm->get("db_mysql");
            /**
             * get parameters of master database (perseed database)
             */
            $params = $this->getParamsConnection();
            /**
             * Set adapter to connect to master database (perseed database)
             */
            $adapter = $sm->get("db_adapter")->getAdapter(array('username' => $params['user']
                                                                , 'password' => $params['password']
                                                                , 'database' => $params['dbname']
                                                                , 'hostname' => $params['host']));
            $dbObj->setAdapter($adapter);
            return $dbObj->isExistDatabase($dbName);
        }
        return false;
    }
    

    public function getPrepareMenu() {
//         $controller = $this->getController();
//         $sm = $controller->getServiceLocator();
//         $userEmail = null;
//         $arrLog = explode("::", __METHOD__);
//         $logger = $sm->get("PerseedUtils\Services\PerseedLogger");
//         $logger->writeLog("INFO", $userEmail, $arrLog[0], $arrLog[1], "Get Menus", ">>");
        
        $controller = $this->getController();        
        $ecosystem = $controller->params()->fromRoute ( 'ecosystem','');
        $projectName = $controller->params()->fromRoute ( 'project','');
                
		// define value for prepare menu. 
		// But you can coding other function to get variable as below
		$prepare_menu = array(
			'title' => 'Ecosystem', 
			'dropdown' => array( 
				array('url' => '/ecosystem/'.$ecosystem.'/project/'.$projectName, 'name' => 'Project Dashboard'),				
			),
			'settings' => array('url' => '#', 'name' => 'Settings'),
			'tree_menu' => array('url' => '#', 'name' => 'Tree menu'),
			'notice' => array('url' => '#', 'name' => 'Notice'),
			'search' => array('url' => '#', 'name' => 'Search'),
			'seed' => array('url' => '#', 'name' => 'Seed'),
		);
        
      //  $logger->writeLog("INFO", $userEmail, $arrLog[0], $arrLog[1], "Get Menus", "<<");
		return $prepare_menu;
	}
	
	
	/**
	 *
	 * @param $question
	 *   The question to ask the user (e.g. "Are you sure you want to delete the
	 *   block <em>foo</em>?"). The page title will be set to this value.
	 * @param $path
	 *   The page to go to if the user cancels the action. This is:
	 *   - A router name and Array param. e.g.: array('ecosystem_admin_project', array('ecosystem' => 'ecosystem name', 'action' => 'index'))
	 * @param $description
	 *   Additional text to display. Defaults to Null.
	 * @param $yes
	 *   A caption for the button that confirms the action (e.g. "Delete",
	 *   "Replace", ...). Defaults to 'Confirm'.
	 * @param $no
	 *   A caption for the link which cancels the action (e.g. "Cancel"). Defaults
	 *   to 'Cancel'.
	 * @param $name
	 *   The internal name used to refer to the confirmation item.
	 *
	 * @return
	 *   The form array.
	 */
	function confirmForm($question, $path, $description = NULL, $yes = NULL, $no = NULL, $name = 'confirm') {
				
		$modelView = new \Zend\View\Model\ViewModel(array(
					'question' => $question,
					'path' => $path,
					'description' => $description,
					'yes' => $yes ? $yes : 'Confirm',
					'no' => $no ? $no : 'Cancel',
					'name' => $name,
				));
		
		$modelView->setTemplate('helper/delete-form');
		
		return $modelView;
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
	
	public function arrProjectType(){
		$config = $this->getController()
					   ->getServiceLocator()
					   ->get('config');
		return $config["project_type"];
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
	
	public function getColorProjectType($type){
		$arr = array(
				'eTrial' => 'color_button_1',
				'EHR' => 'color_button_2',
				'Management' => 'color_button_3',
				'SOP' => 'color_button_4',
			);
		return ($type && isset($arr[$type])) ?  $arr[$type] : null;
	}
	
	
	
	public static function generateCode($length = 6){
        $az = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $azr = rand(0, 51);
        $azs = substr($az, $azr, 10);
        $stamp = hash('sha256', time());
        $mt = hash('sha256', mt_rand(5, 20));
        $alpha = hash('sha256', $azs);
        $hash = str_shuffle($stamp . $mt . $alpha);
        $code = ucfirst(substr($hash, $azr, $length));
        return $code;
    }
	
	public function firstdateOfMonth($dateValue) {
        return date('Y-m', strtotime($dateValue)) . '-01';
    }
	
	public function firstdateOfPrevMonth($dateValue) {
        $Y = (int) date('Y', strtotime($dateValue));
        $m = (int) date('m', strtotime($dateValue));

        if ($m == 1) {
            $Y = $Y - 1;
            $m = 12;
        } else {
            $m = $m - 1;
        }
        return "{$Y}-{$m}-01";
    }
	
	public function firstdateOfNextMonth($dateValue) {
        $Y = (int) date('Y', strtotime($dateValue));
        $m = (int) date('m', strtotime($dateValue));

        if ($m == 12) {
            $Y = $Y + 1;
            $m = '01';
        } else {
            $m = $m + 1;
        }
        return "{$Y}-{$m}-01";
    }
	
	public function lastdateOfMonth($dateValue) {
        $Y = (int) date('Y', strtotime($dateValue));
        $m = (int) date('m', strtotime($dateValue));
        $d = cal_days_in_month(CAL_GREGORIAN, $m, $Y);
        return "{$Y}-{$m}-{$d}";
    }
	
	/**
	 * Compare if $date1 >(<, =) $date2 
	 * @param type $date1
	 * @param type $date2
	 * @return (1:greater, 2:equal, 3:less than)
	 */
	public function dateCompare($date1, $date2){
		$date1 = new \DateTime($date1);
		$date2 = new \DateTime($date2);
		
		if($date1 > $date2){
			return "1";
		}
		if($date1 == $date2){
			return "2";
		}
		if($date1 < $date2){
			return "3";
		}
	}
	
	public function getBaseURL() {
        $uri = $this->getController()->getRequest()->getUri();
        $scheme = $uri->getScheme();
        $host = $uri->getHost();
        $port = $uri->getPort();
        $base = sprintf('%s://%s', $scheme, $host);
        return $base . ":" . $port;
    }
	
	public function generateCheckListElement($options, $name, $class_style, $checked_value=null, $class_element_block="div_multicheckbox"){
		$str = null;
        $this->setMulticheckHelper();
        $element = $this->getElementCheckList($options, $name, $class_style, $checked_value);
		try {
			$str = $this->helper->render($element);
		} catch (\Exception $exc) {
			$this->errorMessage = $exc->getMessage();
		}
		
        //$str = str_replace("<label>", "<div class='{$class_element_block}'>", $str);
        //$str = str_replace("</label>", "</div>", $str);
        return $str;
    }
    
    public function sqlLog($seed) {
        $sm = $this->getController()->getServiceLocator();
        $adapter = null;
        $controller = $this->getController();
        
        $controllerName = $controller->params('controller');
        $actionName = $controller->params('action');
        
        switch ($seed) {
            case "designer":
                $adapter = $sm->get('DesignerAdapter');
                break;
            case "displayer":
                $adapter = $sm->get('DisplayerAdapter');
                break;
        }
        
        if(!empty($adapter)){
            $sqlLogger = $sm->get('PerseedUtils\Services\SQLLogger');
            $sqlLogger->setController($controllerName);
            $sqlLogger->setAction($actionName);
            $sqlLogger->setSeed($seed);
            $sqlLogger->writeSQLLog($adapter->getProfiler());
        }
    }
	
	public function getErrorMessage(){
		return $this->errorMessage;
	}

	protected function setMulticheckHelper(){
        $this->helper = new FormMultiCheckboxHelper();
        $this->renderer = new PhpRenderer;
        $helpers = $this->renderer->getHelperPluginManager();
        $config  = new HelperConfig();
        $config->configureServiceManager($helpers);
        $this->helper->setView($this->renderer);
    }
	
	protected function getElementCheckList($options, $name, $class_style, $checked_value=null){
        $element = new MultiCheckboxElement($name);
		if(!empty($options)){
			$element->setValueOptions($options);
		}
        $element->setAttribute("class", $class_style);
        $element->setValue($checked_value);
        return $element;
    }
}

?>