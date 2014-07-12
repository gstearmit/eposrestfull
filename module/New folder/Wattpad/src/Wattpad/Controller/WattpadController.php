<?php

namespace Wattpad\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Wattpad\Model\Wattpad;
use Wattpad\Form\WattpadForm;
use Zend\Paginator\Paginator;
use Zend\Paginator\Adapter\Iterator as paginatorIterator;
use Zend\Db\Sql\Select;

// rss
use Zend\Feed\Reader as feed;
use Zend\View\Model\JsonModel;
use Zend\Http\Client as HttpClient;
// DOM
 use DOMDocument;
 use DOMXPath;
 use DOMNode;
 use Zend\Stdlib\ErrorHandler;
use Zend\Dom\Query;

class WattpadController extends AbstractActionController {
	protected $wattpadTable;
	public function indexAction() {
		
		// if (!$this->zfcUserAuthentication()->hasIdentity()) {
		// return $this->redirect()->toRoute('zfcuser/login');
		// }
		$select = new Select ();
		$order_by = $this->params ()->fromRoute ( 'order_by' ) ? $this->params ()->fromRoute ( 'order_by' ) : 'id';
		$order = $this->params ()->fromRoute ( 'order' ) ? $this->params ()->fromRoute ( 'order' ) : Select::ORDER_ASCENDING;
		$page = $this->params ()->fromRoute ( 'page' ) ? ( int ) $this->params ()->fromRoute ( 'page' ) : 1;
		
		$wattpads = $this->getWattpadTable ()->fetchAll ( $select->order ( $order_by . ' ' . $order ) );
		$itemsPerPage = 3;
		
		$wattpads->current ();
		$paginator = new Paginator ( new paginatorIterator ( $wattpads ) );
		$paginator->setCurrentPageNumber ( $page )->setItemCountPerPage ( $itemsPerPage )->setPageRange ( 4 );
		
		return new ViewModel ( array (
				// 'wattpads' => $this->getWattpadTable()->fetchAll(),
				'order_by' => $order_by,
				'order' => $order,
				'page' => $page,
				'paginator' => $paginator 
		) );
	}
	public function addAction() {
		$form = new WattpadForm ();
		$form->get ( 'submit' )->setAttribute ( 'value', 'Add' );
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$wattpad = new Wattpad ();
			$form->setInputFilter ( $wattpad->getInputFilter () );
			$form->setData ( $request->getPost () );
			if ($form->isValid ()) {
				$wattpad->exchangeArray ( $form->getData () );
				$this->getWattpadTable ()->saveWattpad ( $wattpad );
				
				// Redirect to list of wattpads
				return $this->redirect ()->toRoute ( 'wattpad' );
			}
		}
		
		return array (
				'form' => $form 
		);
	}
	public function editAction() {
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'wattpad', array (
					'action' => 'add' 
			) );
		}
		$wattpad = $this->getWattpadTable ()->getWattpad ( $id );
		
		$form = new WattpadForm ();
		$form->bind ( $wattpad );
		$form->get ( 'submit' )->setAttribute ( 'value', 'Edit' );
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$form->setData ( $request->getPost () );
			if ($form->isValid ()) {
				$this->getWattpadTable ()->saveWattpad ( $wattpad );
				
				// Redirect to list of wattpads
				return $this->redirect ()->toRoute ( 'wattpad' );
			}
		}
		
		return array (
				'id' => $id,
				'form' => $form 
		);
	}
	public function deleteAction() {
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'wattpad' );
		}
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$del = $request->getPost ()->get ( 'del', 'No' );
			if ($del == 'Yes') {
				$id = ( int ) $request->getPost ()->get ( 'id' );
				$this->getWattpadTable ()->deleteWattpad ( $id );
			}
			
			// Redirect to list of Wattpads
			return $this->redirect ()->toRoute ( 'wattpad' );
		}
		
		return array (
				'id' => $id,
				'wattpad' => $this->getWattpadTable ()->getWattpad ( $id ) 
		);
	}
	
	
	//http://www.wattpad.com/rss?mode=2&language=19
	public function wattpadAction() {
		try {
			// lay theo catalogue truyen : http://www.wattpad.com/rss?mode=1&language=19&category=5 
			// lay theo rss languege viet nam	
			$wattpad = feed\Reader::import ( 'http://www.wattpad.com/rss?mode=2&language=19' );
		} catch ( feed\Exception\RuntimeException $e ) {
			echo "error : " . $e->getMessage ();
			exit ();
		}
	
		$wattpad_array  = array (
				'title' => $wattpad->getTitle(),
				'description' => $wattpad->getDescription(),
				'link' => $wattpad->getLink(),
				'items' => array ()
		);
	
	
		foreach ( $wattpad as $item )
		{

			//Zend/Feed/Reader/Entry/rss.php
			$wattpad_array ['items'] [] = array (
					'title' => $item->getTitle (),
					'link' => $item->getLink (),
					'image' => '/default.png',
					'description' => $item->getDescription(),
			);
		}
		
	
	
		return new ViewModel ( array (
				'data' => $wattpad_array
		) );
	}
	
	
	public function rssgetAction() {
		try {
			
			$rss = feed\Reader::import ( 'http://www.wdcdn.net/rss/presentation/library/client/skunkus/id/cc3d06c1cc3834464aef22836c55d13a' );
		} catch ( feed\Exception\RuntimeException $e ) {
			echo "error : " . $e->getMessage ();
			exit ();
		}
		
		$channel = array (
				'title' => $rss->getTitle (),
				//'date'=>$rss->getDateModified(),
				'description' => $rss->getDescription (),
				'link' => $rss->getLink (),
				'items' => array () 
		);
		
	
	
		foreach ( $rss as $item ) 
		{

			
			//Zend/Feed/Reader/Entry/rss.php
			$channel ['items'] [] = array (
					'title' => $item->getTitle (),
					//'date'=>$item->getDateModified(),
					'link' => $item->getLink (),
					'description' => $item->getDescription () ,
			        'image' => $item->getMedia()->url,
						);
		}
		
		return new ViewModel ( array (
				'channel' => $channel 
		) );
	}
	
	public function runtimerssAction()
	{
		
		// PRINT "Elapsed time was $time seconds.";
		//echo '<script>window.open("http://127.0.0.1:1913/wattpad/haivltv", "_blank", "width=400,height=500")</script>';
		$time = time();
		$starttime = date("Y-m-d", mktime(0,0,0,date("n", $time),date("j",$time) ,date("Y", $time)));
		$endtime = date("Y-m-d", mktime(0,0,0,date("n", $time),date("j",$time)+ 1 ,date("Y", $time)));
		var_dump($starttime);
		var_dump($endtime);
		
		// $totaltime = ($endtime - $starttime);
		// echo "This page was created in ".$totaltime." seconds";
		
		$startime = "2014-06-18 12:05";
		$starttime = strtotime($starttime);
		$oneday = 60*60*0.1;
		if( $starttime < (time()-$oneday) ) {
			echo 'more than one day since start';
			echo '<script>window.open("http://127.0.0.1:1913/wattpad/haivltv", "_blank", "width=400,height=500")</script>';
		} else {
			echo 'started within the last day';
		}
		
		
	}
	
	

	public function innerHTML( $contentdiv ) {
		$r = '';
		$elements = $contentdiv->childNodes;
		foreach( $elements as $element ) {
			if ( $element->nodeType == XML_TEXT_NODE ) {
				$text = $element->nodeValue;
				// IIRC the next line was for working around a
				// WordPress bug
				//$text = str_replace( '<', '&lt;', $text );
				$r .= $text;
			}
			// FIXME we should return comments as well
			elseif ( $element->nodeType == XML_COMMENT_NODE ) {
				$r .= '';
			}
			else {
				$r .= '<';
				$r .= $element->nodeName;
				if ( $element->hasAttributes() ) {
					$attributes = $element->attributes;
					foreach ( $attributes as $attribute )
						$r .= " {$attribute->nodeName}='{$attribute->nodeValue}'" ;
				}
				$r .= '>';
				$r .= $this->innerHTML( $element );
				$r .= "</{$element->nodeName}>";
			}
		}
		return $r;
	}
	


	
	public function get_data($url) {
		// return data: not html
		
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		//curl_setopt($ch, CURLOPT_POST, TRUE);             // Use POST method
		//curl_setopt($ch, CURLOPT_POSTFIELDS, "var1=1&var2=2&var3=3");  // Define POST data values
		curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)");
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,false);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER,false);
		curl_setopt($ch, CURLOPT_MAXREDIRS, 10);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		$data = curl_exec($ch);
		curl_close($ch);
		return $data;
	}
	
	public function domzf2Action()
	{
		$tags = array();
		$site = $this->fetch('http://sussex.academia.edu/');
		$sdom = new Query($site);
		
		foreach ($sdom->execute('div#department_list ul li a') as $href) {
			$url = $href->getAttribute('href');
			$ddom = new Query($this->fetch($url));// gencache
			$page = $ddom->execute('h1')->current()->nodeValue;
			foreach ($ddom->execute('div#user_list fieldset') as $fieldset) {
				$xml = simplexml_import_dom($fieldset);
				if (strtolower((string)$xml->legend) == 'faculty') {
					$subd = new Query($xml->asXml());
					foreach ($subd->execute('div.user_strip') as $userNode) {
						$userXml = simplexml_import_dom($userNode);
						$linkd = new Query($userXml->asXml());
						$links = array();
						foreach ($linkd->execute('div.user_research_interests a.research_interest_link') as $link) {
							$links[] = $link->nodeValue;
						}
						if (count($links)) {
							$tags[$page][(string)$userXml->h3->a] = $links;
						}
					}
				}
			}
		}
		
		var_dump($tags);
		die;
	}
	
	/**
	 * Fetch the page source and cache it, ensuring it's saved as UTF-8
	 *
	 * @param  string $url
	 * @return string
	 */
	public function fetch($url)
	{
		$content = '';
		$md5 = md5($url);
		$path = __DIR__.'/cache/' . $md5;
		if (!file_exists($path)) {
			$content = file_get_contents($url);
			$content = mb_convert_encoding($content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
			file_put_contents($path, $content);
		} else {
			$content = file_get_contents($path);
		}
		return $content;
	}
	
	
	
	public function getWattpadTable() {
		if (! $this->wattpadTable) {
			$sm = $this->getServiceLocator ();
			$this->wattpadTable = $sm->get ( 'Wattpad\Model\WattpadTable' );
		}
		return $this->wattpadTable;
	}
}
