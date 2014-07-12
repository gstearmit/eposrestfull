<?php

namespace Rssget\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Rssget\Model\Rssget;
use Rssget\Form\RssgetForm;
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

class RssgetController extends AbstractActionController {
	protected $rssgetTable;
	public function indexAction() {
		
		// if (!$this->zfcUserAuthentication()->hasIdentity()) {
		// return $this->redirect()->toRoute('zfcuser/login');
		// }
		$select = new Select ();
		$order_by = $this->params ()->fromRoute ( 'order_by' ) ? $this->params ()->fromRoute ( 'order_by' ) : 'id';
		$order = $this->params ()->fromRoute ( 'order' ) ? $this->params ()->fromRoute ( 'order' ) : Select::ORDER_ASCENDING;
		$page = $this->params ()->fromRoute ( 'page' ) ? ( int ) $this->params ()->fromRoute ( 'page' ) : 1;
		
		$rssgets = $this->getRssgetTable ()->fetchAll ( $select->order ( $order_by . ' ' . $order ) );
		$itemsPerPage = 3;
		
		$rssgets->current ();
		$paginator = new Paginator ( new paginatorIterator ( $rssgets ) );
		$paginator->setCurrentPageNumber ( $page )->setItemCountPerPage ( $itemsPerPage )->setPageRange ( 4 );
		
		return new ViewModel ( array (
				// 'rssgets' => $this->getRssgetTable()->fetchAll(),
				'order_by' => $order_by,
				'order' => $order,
				'page' => $page,
				'paginator' => $paginator 
		) );
	}
	public function addAction() {
		$form = new RssgetForm ();
		$form->get ( 'submit' )->setAttribute ( 'value', 'Add' );
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$rssget = new Rssget ();
			$form->setInputFilter ( $rssget->getInputFilter () );
			$form->setData ( $request->getPost () );
			if ($form->isValid ()) {
				$rssget->exchangeArray ( $form->getData () );
				$this->getRssgetTable ()->saveRssget ( $rssget );
				
				// Redirect to list of rssgets
				return $this->redirect ()->toRoute ( 'rssget' );
			}
		}
		
		return array (
				'form' => $form 
		);
	}
	public function editAction() {
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'rssget', array (
					'action' => 'add' 
			) );
		}
		$rssget = $this->getRssgetTable ()->getRssget ( $id );
		
		$form = new RssgetForm ();
		$form->bind ( $rssget );
		$form->get ( 'submit' )->setAttribute ( 'value', 'Edit' );
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$form->setData ( $request->getPost () );
			if ($form->isValid ()) {
				$this->getRssgetTable ()->saveRssget ( $rssget );
				
				// Redirect to list of rssgets
				return $this->redirect ()->toRoute ( 'rssget' );
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
			return $this->redirect ()->toRoute ( 'rssget' );
		}
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$del = $request->getPost ()->get ( 'del', 'No' );
			if ($del == 'Yes') {
				$id = ( int ) $request->getPost ()->get ( 'id' );
				$this->getRssgetTable ()->deleteRssget ( $id );
			}
			
			// Redirect to list of Rssgets
			return $this->redirect ()->toRoute ( 'rssget' );
		}
		
		return array (
				'id' => $id,
				'rssget' => $this->getRssgetTable ()->getRssget ( $id ) 
		);
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
	
	public function rssjsonAction() {
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
	
			$channel['items'][] = array (
					'title' => $item->getTitle (),
					//'date'=>$item->getDateModified(),
					'link' => $item->getLink (),
					'description' => $item->getDescription () ,
					'image' => $item->getMedia()->url,               // function tu dinh nghia
			);
		}
	
		 return new JsonModel(array(
            'channel' => $channel,
        ));
	}
	
	public function zf2listAction()
	{

		$zf2array = array (
				'title' =>'Zf2 Document',
				'description' => 'App Hưỡng dẫn học Zend Framework 2',
				'link' =>__METHOD__,
				'items' => array ()
		);
		
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri('http://samsonasik.wordpress.com/');
		$result                 = $client->send();
		$body                   = $result->getBody();//content of the web
			
		$dom = new Query($body);
		//get div with id="content" and h2's NodeList
		$title = $dom->execute('#content h2');
	  
		foreach($title as $key=>$r)
		{
			$aelement     = $r->getElementsByTagName("a")->item(0);
	
			if ($aelement->hasAttributes()) {
				$zf2array['items'][] = array (
						'title' => $aelement->textContent,
						'link' => $aelement->getAttributeNode('href')->nodeValue,
						'image' => '',               // function tu dinh nghia
				);
			}

		}

		return new JsonModel(array(
				'list' => $zf2array,
		));
	}
	
	
	
	public function zf2detailAction()
	{
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri('http://samsonasik.wordpress.com/');
		$result                 = $client->send();
		$body                   = $result->getBody();//content of the web
			
		$dom = new Query($body);
	
		$mainbody = $dom->execute('#content .main');
	
		$mainContent = '';
		foreach ($mainbody as $keydo => $mainelemen)
		{
	
			$mainContent .= '* ';
			$mainContent .= $this->innerHTML($mainelemen);// get content element div.main
			//$mainContent .= $mainelemen->textContent;
			$mainContent .= "<br/>";
		}
			
		
	
		$response->setContent($mainContent);
	
		return $response;
	}
	
	public function havltvAction()
	{
		
		$zf2array = array (
				'title' =>'haivl.tv',
				'description' => 'Video Clip Giaỉ Trí',
				'link' =>__METHOD__,
				'items' => array ()
		);
		
		$haivltv = array (
				'img' => array ()
		);
		
		
		
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri('http://haivl.tv');
		$result                 = $client->send();
		$body                   = $result->getBody();  //content of the web
			
		$dom = new Query($body);
		//get div with id="content" and id=leftColumn NodeList
		$title = $dom->execute('#content .videoListItem h2');
		
		
		// get Thumbnail img
		$img = $dom->execute('#content .videoListItem .thumb img');
		
		foreach($title as $key=>$r)
		{
			$aelement     = $r->getElementsByTagName("a")->item(0);

			if ($aelement->hasAttributes())
			 {
								$zf2array['items'][] = array (
										'title' => $aelement->textContent,
										'link' => $aelement->getAttributeNode('href')->nodeValue,
										'image' => '',               // function tu dinh nghia
								);
			 }
		
		}
		
		$count = count($img);
		foreach ($img as $keydo => $mainelemen)
		{
			
			
			if ($mainelemen->hasAttributes())
			{
				$haivltv['img'][] = array ( 
					//'image'=>$this->innerHTML($mainelemen),
					'src'=>$mainelemen->getAttributeNode('src')->nodeValue,
			       );
			 }
				
		}
		
		
		return new JsonModel(array(
				'list' =>$haivltv, //$zf2array,
		));
	}
	
	
	public function zf2domAction()
	{
		$zf2array = array (
				'title' =>'Zf2 Document',
				'description' => 'App Hưỡng dẫn học Zend Framework 2',
				'link' =>__METHOD__,
				'items' => array ()
		);
	
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri('http://samsonasik.wordpress.com/');
		$result                 = $client->send();
		$body                   = $result->getBody();//content of the web
			
		$dom = new Query($body);
		//get div with id="content" and h2's NodeList
		$title = $dom->execute('#content h2');
		// get Content
		$mainbody = $dom->execute('#content .main');
		
		$a= array();
		
		$i=0;
		//for( $i = 0; ;$i++)
		//{
			foreach($title as $key=>$r)
			{
				//$a[$i]=array();
 				$aelement     = $r->getElementsByTagName("a")->item(0);
 				//var_dump($aelement);
 				$a[$i]->title= $aelement->textContent;
 				
 				$i++;
		
// 				if ($aelement->hasAttributes()) {
// 	// 				$zf2array['items'][] = array (
// 	// 						'title' => $aelement->textContent,
// 	// 						'link' => $aelement->getAttributeNode('href')->nodeValue,
// 	// 						'image' => '',               // function tu dinh nghia
// 	// 				);
// 				}
		
			}
			$i=0;
			foreach ($mainbody as $keydo => $mainelemen)
			{
				$a[$i]->content= $this->innerHTML($mainelemen);
				$i++;
				
// 				$zf2array['items'][] = array (
// 						'title' => $aelement->textContent,
// 						'link' => $aelement->getAttributeNode('href')->nodeValue,
// 						'image' => '',               // function tu dinh nghia
// 						'content'=>$this->innerHTML($mainelemen),
// 				);
				
			}
			echo '99';
			echo '<pre>';
			print_r($a);
			echo '</pre>';
			die;
		//}	
	
		return new JsonModel(array(
				'list' => $zf2array,
		));
	
	
	
	}
	
	
	public function zf2dom_backupAction()
	{
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri('http://samsonasik.wordpress.com/');
		$result                 = $client->send();
		$body                   = $result->getBody();//content of the web
			
		$dom = new Query($body);
		//get div with id="content" and h2's NodeList
		$title = $dom->execute('#content h2');
	    //#content #leftColumn"
		$content = '';
		foreach($title as $key=>$r)
		{
			//per h2 NodeList, has element with tagName = 'a'
			//DOMElement get Element with tagName = 'a'
			$aelement     = $r->getElementsByTagName("a")->item(0);
	
			if ($aelement->hasAttributes()) {
				$content .= '* ';
				$content .= '<a href='.$aelement->getAttributeNode('href')->nodeValue.'>';
				$content .= $aelement->textContent;
				$content .= '</a>';
					
				$content .= "<br />";
			}
		}
	
		$mainbody = $dom->execute('#content .main');
	
		$mainContent = '';
		foreach ($mainbody as $keydo => $mainelemen)
		{
	
			$mainContent .= '* ';
			$mainContent .= $this->innerHTML($mainelemen);// get content element div.main
			//$mainContent .= $mainelemen->textContent;
			$mainContent .= "<br/>";
		}
			
		//$response->setContent($content);
	
		$response->setContent($mainContent);
	
		return $response;
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
	

	public function htmldomAction()
	{
		// 		$client = new HttpClient();
		// 		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
	
		// 		$method = $this->params()->fromQuery('method', 'get');
		// 		$client->setUri('http://skunkus.wiredrive.com/present-library-detail/token/cc3d06c1cc3834464aef22836c55d13a/assetId/1541484');
	
		// 		switch($method) {
		// 			case 'get' :
		// 				$client->setMethod('GET');
		// 				//$client->setParameterGET(array('id'=>1));
		// 				break;
			
	
		// 				return $response;
		// 		}
	
		// 		//if get/get-list/create
		// 		$response = $client->send();
		// 		if (!$response->isSuccess()) {
		// 			// report failure
		// 			$message = $response->getStatusCode() . ': ' . $response->getReasonPhrase();
	
		// 			$response = $this->getResponse();
		// 			$response->setContent($message);
		// 			return $response;
		// 		}
		// 		$body = $response->getBody();
	
		// 		$response = $this->getResponse();
		// 		$response->setContent($body);
	
		// 		return $response;
	}
	
	public function curlgetAction()
	{
	
		//$variablee = $this->get_data('http://www.dantri.com.vn');
		//echo $variablee;
	
	
		$html = '
		<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
		    "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
		<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en-US" dir="ltr">
		<head>
		<title>PHPRO.ORG</title>
		</head>
		<body>
		<h2>Forecast for Saturday</h2>
		<!-- Issued at 0828 UTC Friday 23 May 2008 -->
		<table border="0" summary="Capital Cities Precis Forecast">
		   <tbody>
		      <tr>
		         <td><a href="/products/IDN10064.shtml" title="Link to Sydney forecast">Sydney</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">19&deg;</td>
		         <td>Fine. Mostly sunny.</td>
		      </tr>
	
		      <tr>
		         <td><a href="/products/IDV10450.shtml" title="Link to Melbourne forecast">Melbourne</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">16&deg;</td>
		         <td>Fog then fine.</td>
		      </tr>
	
		      <tr>
		         <td><a href="/products/IDQ10095.shtml" title="Link to Brisbane forecast">Brisbane</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">24&deg;</td>
		         <td>Mostly fine</td>
		      </tr>
	
		      <tr>
		         <td><a href="/products/IDW12300.shtml" title="Link to Perth forecast">Perth</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">21&deg;</td>
		         <td>Few showers, increasing later.</td>
		      </tr>
	
		      <tr>
		         <td><a href="/products/IDS10034.shtml" title="Link to Adelaide forecast">Adelaide</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">20&deg;</td>
		         <td>Fine. Mostly sunny.</td>
		      </tr>
	
		      <tr>
		         <td><a href="/products/IDT65061.shtml" title="Link to Hobart forecast">Hobart</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">13&deg;</td>
		         <td>Mainly fine.</td>
		      </tr>
	
		      <tr>
		         <td><a href="/products/IDN10035.shtml" title="Link to Canberra forecast">Canberra</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">15&deg;</td>
		         <td>Fine, mostly sunny.</td>
		      </tr>
	
		      <tr>
		         <td><a href="/products/IDD10150.shtml" title="Link to Darwin forecast">Darwin</a></td>
		         <td title="Maximum temperature in degrees Celsius" class="max alignright">32&deg;</td>
		         <td>Fine and sunny.</td>
		      </tr>
	
		   </tbody>
		</table>
	
		</body>
		</html>
		';
	
		$dom = new DOMDocument;
		$dom->loadHTML($html);
	
		//$dom->preserveWhiteSpace = false;
	
	
		$tables = $dom->getElementsByTagName('table');
		$rows = $tables->item(0)->getElementsByTagName('tr');
	
		foreach ($rows as $row)
		{
				
			$cols = $row->getElementsByTagName('td');
			echo $cols->item(0)->nodeValue.'<br />';
			echo $cols->item(1)->nodeValue.'<br />';
			echo $cols->item(2)->nodeValue;
			echo '<hr />';
		}
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
			$ddom = new Query($this->fetch($url));
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
		
		//Debug::dump($tags);
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
	
	
	
	public function getRssgetTable() {
		if (! $this->rssgetTable) {
			$sm = $this->getServiceLocator ();
			$this->rssgetTable = $sm->get ( 'Rssget\Model\RssgetTable' );
		}
		return $this->rssgetTable;
	}
}
