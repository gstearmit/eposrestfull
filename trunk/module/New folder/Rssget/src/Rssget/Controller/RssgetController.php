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
			echo "error : " . $e-> getMessage ();
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
		//echo '<script>window.open("http://127.0.0.1:1913/rssget/haivltv", "_blank", "width=400,height=500")</script>';
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
			echo '<script>window.open("http://127.0.0.1:1913/rssget/haivltv", "_blank", "width=400,height=500")</script>';
		} else {
			echo 'started within the last day';
		}
		
		
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
				'data' => $zf2array,
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
	//haivlcom
	public function haivlcomAction()
	{
	
		$url_haivlcom = "http://haivl.com";
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri($url_haivlcom);
		$result                 = $client->send();
		$body                   = $result->getBody();  //content of the web
	
		$dom = new Query($body);
	
		//get div with id="content" and id=leftColumn NodeList
		$title = $dom->execute('#content .info h2');
	
		$haivlcom = array ();
		$arrayLoop_link = array();
		$i = 0;
		foreach($title as $key=>$r)
		{
			$aelement     = $r->getElementsByTagName("a")->item(0);
				
			if ($aelement->hasAttributes())
			{
				$haivlcom[$i]->nameapp = 'haivlcom';
				$haivlcom[$i]->title =  $aelement->textContent;
				$haivlcom[$i]->link  = $url_haivlcom.$aelement->getAttributeNode('href')->nodeValue;
				$arrayLoop_link[$i]->link = $url_haivlcom.$aelement->getAttributeNode('href')->nodeValue;
				$i++;
					
			}
	
		}
	
		// get Thumbnail img
		$img = $dom->execute('#content .thumbnail img.thumbImg');
	
// 		var_dump($img->count());
// 		die;
		
		$i = 0;
		foreach ($img as $keydo => $mainelemen)
		{
				
				
			if ($mainelemen->hasAttributes())
			{
				$haivlcom[$i]->image_thumbnail = $mainelemen->getAttributeNode('src')->nodeValue;
				$i++;
			}
				
		}
	
// 		var_dump($content_detail = $this->getcontent_url_haivlcom_detail('http://www.haivl.com/photo/3515630'));// http://www.haivl.com/photo/3515630
// 		//http://www.haivl.com/photo/3512750 
// 		die;
		
		//get Content save table
		$i= 0;
		foreach ($arrayLoop_link as $keyhai => $valuehaivl)
		{
			$link_detail = $valuehaivl->link;
// 			$content_detail = $this->getcontent_url_haivlcom_detail($link_detail);
// 			$haivlcom[$i]->content_detail = $content_detail;
			$haivlcom[$i]->content_detail = '';
			$i++;
		}
			
		//GetConTentFull Deatil
		$i=0;
		foreach ($arrayLoop_link as $keyhai => $valuehaivl)
		{
			$link_detail_full = $valuehaivl->link;
// 			$content_detail_full = $this->getContent_full_url_detail($link_detail_full);
// 			$haivlcom[$i]->content_detail_full = $content_detail_full;
			$haivlcom[$i]->content_detail_full = '';
			$i++;
		}
	
		// save  database
		foreach ($haivlcom as $keysave =>$valuehaivlSave)
		{
			$arry_tmp = array();
			$arry_tmp = (array)$valuehaivlSave;
			$rssget = New Rssget();
			$rssget->exchangeArray($arry_tmp);
			$this->getRssgetTable()->saveRssget($rssget);
		}
	
	
// 							echo 'haivlcom';
// 							echo '<pre>';
// 							print_r($arrayLoop_link);
// 							echo '</pre>';
// 							echo '<pre>';
// 							print_r($haivlcom);
// 							echo '</pre>';
// 							die;
	
	
		return new JsonModel(array(
				'data' =>$haivlcom,
		));
	}
	
	public function haivltvAction()
	{
	
		$url_haivltv = "http://haivl.tv";
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
		
		$haivltv = array ();
		$arrayLoop_link = array();
		$i = 0;
		foreach($title as $key=>$r)
		{
			$aelement     = $r->getElementsByTagName("a")->item(0);
			
			if ($aelement->hasAttributes())
			 {
			 	$haivltv[$i]->nameapp = 'haivltv';
			 	$haivltv[$i]->title =  $aelement->textContent;
			 	$haivltv[$i]->link  = $url_haivltv.$aelement->getAttributeNode('href')->nodeValue;
			 	$arrayLoop_link[$i]->link = $url_haivltv.$aelement->getAttributeNode('href')->nodeValue;
			 	$i++;
			 	
			 }
		
		}
		
		// get Thumbnail img
		$img = $dom->execute('#content .videoListItem .thumb img');

		$i = 0;
		foreach ($img as $keydo => $mainelemen)
		{
			
			
			if ($mainelemen->hasAttributes())
			{
				$haivltv[$i]->image_thumbnail = $mainelemen->getAttributeNode('src')->nodeValue;
				$i++;
			 }
			
		}
		
		// get Content save table
		$i= 0;
		 foreach ($arrayLoop_link as $keyhai => $valuehaivl)
		 {
		 	$link_detail = $valuehaivl->link;
		 	$content_detail = $this->getcontent_url_detail($link_detail);
		 	$haivltv[$i]->content_detail = $content_detail;
		    $i++;
		 }
		 
		// GetConTentFull Deatil
		$i=0;
		foreach ($arrayLoop_link as $keyhai => $valuehaivl)
		{
			$link_detail_full = $valuehaivl->link;
			$content_detail_full = $this->getContent_full_url_detail($link_detail_full);
			$haivltv[$i]->content_detail_full = $content_detail_full;
			$i++;
		}
		
		// save
		foreach ($haivltv as $keysave =>$valuehaivlSave)
		{
			$arry_tmp = array();
			$arry_tmp = (array)$valuehaivlSave;
			$rssget = New Rssget();
			$rssget->exchangeArray($arry_tmp);
			$this->getRssgetTable()->saveRssget($rssget);
		}
		
		
// 					echo 'haivltv';
// 					echo '<pre>';
// 					print_r($haivltv);
// 					echo '</pre>';
// 					die;
		

		return new JsonModel(array(
				'data' =>$haivltv, 
		));
	}
	
	
	public function minecraftmodscomAction()
	{
	
		$url_minecraftmodscom = "http://www.minecraftmods.com/toomanyitems/";
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri($url_minecraftmodscom);
		$result                 = $client->send();
		$body                   = $result->getBody();  //content of the web
	   // echo $body;die;
		$dom = new Query($body);
	
		//get div with id="content" and id=leftColumn NodeList
		$toomanyitems = $dom->execute('#wrap .post');
	   
// 	    var_dump($toomanyitems->count());
// 	    die();
		
		$minecraftmodscom = array ();
		$arrayLoop_link = array();
		
		// catalogue toomanyitems
		$i = 0;
		foreach ($toomanyitems as $keyvd => $valuevd)
		{
			
			$minecraftmodscom[$i]->nameapp = 'minecraftmodscom';
			$minecraftmodscom[$i]->title = 'minecraftmodscom - toomanyitems ';
			$minecraftmodscom[$i]->link  = 'http://www.minecraftmods.com/toomanyitems/';
			$minecraftmodscom[$i]->content_detail = $this->innerHTML($valuevd);
			$minecraftmodscom[$i]->content_detail_full = '';
			// 				$i++;
		}
	
		// get Thumbnail img
		$img = $dom->execute('#wrap .post img.alignnone');
		$i = 0;
		foreach ($img as $keydo => $mainelemen)
		{
				
				
			if ($mainelemen->hasAttributes())
			{
				$minecraftmodscom[$i]->image_thumbnail = $mainelemen->getAttributeNode('src')->nodeValue;
				//$i++;
			}
				
		}
		
		// catalogue 2 : 
		$url_furniture_mod = "http://www.minecraftmods.com/furniture-mod";
		$client2 = new HttpClient();
		$client2->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response2 = $this->getResponse();
		$response2->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client2->setUri($url_furniture_mod);
		$result2                 = $client2->send();
		$body2                   = $result2->getBody();  //content of the web
		// echo $body;die;
		$dom2 = new Query($body2);
		
		//get div with id="content" and id=leftColumn NodeList
		$furniture_mod = $dom2->execute('#wrap .post');
		$i = 1;
		foreach ($furniture_mod as $keyvd => $valuevd)
		{
				
			$minecraftmodscom[$i]->nameapp = 'minecraftmodscom';
			$minecraftmodscom[$i]->title = 'minecraftmodscom - furniture-mod ';
			$minecraftmodscom[$i]->link  = 'http://www.minecraftmods.com/furniture-mod';
			$minecraftmodscom[$i]->content_detail = $this->innerHTML($valuevd);
			$minecraftmodscom[$i]->content_detail_full = '';
			//$i++;
		}
		// get Thumbnail img
		$img2 = $dom->execute('#wrap .post img.alignnone');
		$i = 1;
		foreach ($img2 as $keydo2 => $mainelemen2)
		{
			if ($mainelemen2->hasAttributes())
			{
				$minecraftmodscom[$i]->image_thumbnail = $mainelemen2->getAttributeNode('src')->nodeValue;
				//$i++;
			}
		
		}
		
		// catalogue 3 :
		$url_voxelmap = "http://www.minecraftmods.com/voxelmap";
		$client3 = new HttpClient();
		$client3->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response3 = $this->getResponse();
		$response3->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client3->setUri($url_voxelmap);
		$result3                 = $client3->send();
		$body3                   = $result3->getBody();  //content of the web
		// echo $body;die;
		$dom3 = new Query($body3);
		
		//get div with id="content" and id=leftColumn NodeList
		$voxelmap = $dom3->execute('#wrap .post');
		$i = 2;
		foreach ($voxelmap as $keyvd => $valuevd)
		{
		
			$minecraftmodscom[$i]->nameapp = 'minecraftmodscom';
			$minecraftmodscom[$i]->title = 'minecraftmodscom - voxelmap ';
			$minecraftmodscom[$i]->link  = 'http://www.minecraftmods.com/voxelmap';
			$minecraftmodscom[$i]->content_detail = $this->innerHTML($valuevd);
			$minecraftmodscom[$i]->content_detail_full = '';
			//$i++;
		}
		// get Thumbnail img
		$img3 = $dom3->execute('#wrap .post img.alignnone');
		$i = 2;
		foreach ($img3 as $keydo3 => $mainelemen3)
		{
			if ($mainelemen3->hasAttributes())
			{
				$minecraftmodscom[$i]->image_thumbnail = $mainelemen3->getAttributeNode('src')->nodeValue;
				//$i++;
			}
		
		}
		
		// catalogue 4 :
		$url_millenaire = "http://www.minecraftmods.com/millenaire";
		$client4 = new HttpClient();
		$client4->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response4 = $this->getResponse();
		$response4->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client3->setUri($url_millenaire);
		$result3                 = $client3->send();
		$body3                   = $result3->getBody();  //content of the web
		// echo $body;die;
		$dom3 = new Query($body3);
		
		//get div with id="content" and id=leftColumn NodeList
		$millenaire = $dom3->execute('#wrap .post');
		$i = 3;
		foreach ($millenaire as $keyvd => $valuevd)
		{
		
			$minecraftmodscom[$i]->nameapp = 'minecraftmodscom';
			$minecraftmodscom[$i]->title = 'minecraftmodscom - millenaire ';
			$minecraftmodscom[$i]->link  = 'http://www.minecraftmods.com/millenaire';
			$minecraftmodscom[$i]->content_detail = $this->innerHTML($valuevd);
			$minecraftmodscom[$i]->content_detail_full = '';
			//$i++;
		}
		// get Thumbnail img
		$img4 = $dom3->execute('#wrap .post img.alignnone');
		$i = 3;
		foreach ($img4 as $keydo3 => $mainelemen3)
		{
			if ($mainelemen3->hasAttributes())
			{
				$minecraftmodscom[$i]->image_thumbnail = $mainelemen3->getAttributeNode('src')->nodeValue;
				//$i++;
			}
		
		}
		
		// catalogue 5 :
		$url_oceancraft = "http://www.minecraftmods.com/oceancraft";
		$client5 = new HttpClient();
		$client5->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response5 = $this->getResponse();
		$response5->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client5->setUri($url_oceancraft);
		$result5                 = $client5->send();
		$body5                   = $result5->getBody();  //content of the web
		// echo $body;die;
		$dom5 = new Query($body5);
		
		//get div with id="content" and id=leftColumn NodeList
		$oceancraft = $dom5->execute('#wrap .post');
		$i = 4;
		foreach ($oceancraft as $keyvd => $valuevd)
		{
		
			$minecraftmodscom[$i]->nameapp = 'minecraftmodscom';
			$minecraftmodscom[$i]->title = 'minecraftmodscom - oceancraft ';
			$minecraftmodscom[$i]->link  = 'http://www.minecraftmods.com/oceancraft';
			$minecraftmodscom[$i]->content_detail = $this->innerHTML($valuevd);
			$minecraftmodscom[$i]->content_detail_full = '';
			//$i++;
		}
		// get Thumbnail img
		$img5 = $dom5->execute('#wrap .post img.alignnone');
		$i = 4;
		foreach ($img5 as $keydo3 => $mainelemen3)
		{
			if ($mainelemen3->hasAttributes())
			{
				$minecraftmodscom[$i]->image_thumbnail = $mainelemen3->getAttributeNode('src')->nodeValue;
				//$i++;
			}
		
		}
	
		// save
		foreach ($minecraftmodscom as $keysave =>$minecraftmodscomvl)
		{
			$arry_tmp = array();
			$arry_tmp = (array)$minecraftmodscomvl;
			$rssget = New Rssget();
			$rssget->exchangeArray($arry_tmp);
			$this->getRssgetTable()->saveRssget($rssget);
		}
	
	
// 							echo 'minecraftmodscom';
// 							echo '<pre>';
// 							print_r($minecraftmodscom);
// 							echo '</pre>';
// 							die;
	
	
		return new JsonModel(array(
				'data' =>$minecraftmodscom,
		));
	}
	
	public function getcontent_url_haivlcom_detail($url = null)
	{
		// get Content
		if($url === null)
		{
			return $haivltv_content = null;
		}
		else {
			$url_haivltv_detail = $url;
	
			$client2 = new HttpClient();
			$client2->setAdapter('Zend\Http\Client\Adapter\Curl');
	
			$response2 = $this->getResponse();
			$response2->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
	
			$client2->setUri($url_haivltv_detail);
			$result2                 = $client2->send();
			$body2                   = $result2->getBody();  //content of the web
	
			//echo $body2;
			$dom2 = new Query($body2);
	
			//get video
			$video = $dom2->execute('#content .photoImg img');
			
			//$arra = (array)$video;
	        $count = $video->count();
			
// 			return $count;
// 			die;
			if($count > 0)
			{
				// truong hop lay anh img ->src
				
				foreach ($video as $keyvd => $valuevd)
				{
					//$haivltv_content = $this->innerHTML($valuevd);
					if ($valuevd->hasAttributes())
					{
						$haivltv_content = $valuevd->getAttributeNode('src')->nodeValue; // get images
					}
						
				}
			}
			else 
			{
				// Nguoc Lai la truong hop iframe
				$video3 = $dom2->execute('#content .photoImg');
				$count2 = $video3->count();
				
				if($count2 > 0)
				{
					foreach ($video3 as $keyvd => $valuevd3)
					{
						$haivltv_content = $this->innerHTML($valuevd3);
						
					}
				}
			}	
	
			return $haivltv_content;
		}
	}
	
	
	public function getcontent_url_detail($url = null)
	{
		// get Content
	if($url === null)
	{
		return $haivltv_content = null;
	}
	else {		
			$url_haivltv_detail = $url;
		
			$client2 = new HttpClient();
			$client2->setAdapter('Zend\Http\Client\Adapter\Curl');
				
			$response2 = $this->getResponse();
			$response2->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
				
			$client2->setUri($url_haivltv_detail);
			$result2                 = $client2->send();
			$body2                   = $result2->getBody();  //content of the web
		
			//echo $body2;
			$dom2 = new Query($body2);
		
			//get video
			$video = $dom2->execute('#content .player');
			//$arra = (array)$video;
		
			$count = $video->count();
			$video->getXpathQuery();  // //*[@id='content']//*[contains(concat(' ', normalize-space(@class), ' '), ' player ')]|//*[@id='content'][contains(concat(' ', normalize-space(@class), ' '), ' player ')]
		
			foreach ($video as $keyvd => $valuevd)
			{
				$haivltv_content = $this->innerHTML($valuevd);
					
			}
				
			return $haivltv_content;
	  }
	}
	
	public function getComment_url_detail($url = null)
	{
		// get Content
		if($url === null)
		{
			return $haivltv_content = null;
		}
		else {
			$url_haivltv_detail = $url;
	
			$client2 = new HttpClient();
			$client2->setAdapter('Zend\Http\Client\Adapter\Curl');
	
			$response2 = $this->getResponse();
			$response2->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
	
			$client2->setUri($url_haivltv_detail);
			$result2                 = $client2->send();
			$body2                   = $result2->getBody();  //content of the web
	
			//echo $body2;
			$dom2 = new Query($body2);
	
			//get video
			$video = $dom2->execute('#content .commentContainer');
			//$arra = (array)$video;
	
			$count = $video->count();
			$video->getXpathQuery();  // //*[@id='content']//*[contains(concat(' ', normalize-space(@class), ' '), ' player ')]|//*[@id='content'][contains(concat(' ', normalize-space(@class), ' '), ' player ')]
	
			foreach ($video as $keyvd => $valuevd)
			{
				$haivltv_content = $this->innerHTML($valuevd);
					
			}
	
			return $haivltv_content;
		}
	}
	
	public function getContent_full_url_detail($url = null)
	{
		// get Content
		if($url === null)
		{
			return $haivltv_content = null;
		}
		else {
			$url_haivltv_detail = $url;
	
			$client2 = new HttpClient();
			$client2->setAdapter('Zend\Http\Client\Adapter\Curl');
	
			$response2 = $this->getResponse();
			$response2->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
	
			$client2->setUri($url_haivltv_detail);
			$result2                 = $client2->send();
			$body2                   = $result2->getBody();  //content of the web
	
			//echo $body2;
			$dom2 = new Query($body2);
	
			//get video
			$video = $dom2->execute('#content');
			//$arra = (array)$video;
	
			$count = $video->count();
			$video->getXpathQuery();  // //*[@id='content']//*[contains(concat(' ', normalize-space(@class), ' '), ' player ')]|//*[@id='content'][contains(concat(' ', normalize-space(@class), ' '), ' player ')]
	
			foreach ($video as $keyvd => $valuevd)
			{
				$haivltv_content = $this->innerHTML($valuevd);
					
			}
	
			return $haivltv_content;
		}
	}
	
	public function contentdetailAction($url = null)
	{
				// get Content
				$haivltv_content = array ();
		
				$url_haivltv_detail = 'http://haivl.tv/video/8514';
		
				$client2 = new HttpClient();
				$client2->setAdapter('Zend\Http\Client\Adapter\Curl');
					
				$response2 = $this->getResponse();
				$response2->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
					
				$client2->setUri($url_haivltv_detail);
				$result2                 = $client2->send();
				$body2                   = $result2->getBody();  //content of the web
				
				//echo $body2;
				$dom2 = new Query($body2);
				
				//get video
				$video = $dom2->execute('#content .player');
				//$arra = (array)$video;
				
				$count = $video->count();
				$video->getXpathQuery();  // //*[@id='content']//*[contains(concat(' ', normalize-space(@class), ' '), ' player ')]|//*[@id='content'][contains(concat(' ', normalize-space(@class), ' '), ' player ')]
				
				foreach ($video as $keyvd => $valuevd)
				{
				  $haivltv_content = $this->innerHTML($valuevd);
				 
				}
			
		return new JsonModel(array(
				'data' =>$haivltv_content, //$zf2array,
		));
	}
	
	
	
	public function zf2domAction()
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
		// get Content
		$mainbody = $dom->execute('#content .main');
		
		$a= array();
		
		$i=0;
		
			foreach($title as $key=>$r)
			{
				
 				$aelement     = $r->getElementsByTagName("a")->item(0);
 				
		//php.ini  --> An loi tien dinh 
			// display_startup_errors = Off
			// display_errors = Off
		
				if ($aelement->hasAttributes()) 
				{
					$a[$i]->nameapp = 'zf2';
					$a[$i]->title = $aelement->textContent;
					$a[$i]->link = $aelement->getAttributeNode('href')->nodeValue;
					$a[$i]->image_thumbnail = '';
					$i++;
				}
		
			}
			
			$i=0; // reset
			foreach ($mainbody as $keydo => $mainelemen)
			{
				$a[$i]->content_detail= $this->innerHTML($mainelemen);
				$haivltv[$i]->content_detail_full = '';
				$i++;
		
			}

// 			// get Content save table
// 			$i= 0;
// 			foreach ($arrayLoop_link as $keyhai => $valuehaivl)
// 			{
// 				$link_detail = $valuehaivl->link;
// 				$content_detail = $this->getcontent_url_detail($link_detail);
// 				$haivltv[$i]->content_detail = $content_detail;
// 				$i++;
// 			}
				
// 			// GetConTentFull Deatil
// 			$i=0;
// 			foreach ($arrayLoop_link as $keyhai => $valuehaivl)
// 			{
// 				$link_detail_full = $valuehaivl->link;
// 				$content_detail_full = $this->getContent_full_url_detail($link_detail_full);
// 				$haivltv[$i]->content_detail_full = $content_detail_full;
// 				$i++;
// 			}
			
			// save
			foreach ($a as $keysave =>$valueSave)
			{
				$arry_tmp = array();
				$arry_tmp = (array)$valueSave;
				$rssget = New Rssget();
				$rssget->exchangeArray($arry_tmp);
				$this->getRssgetTable()->saveRssget($rssget);
			}
			
// 			echo '99';
// 			echo '<pre>';
// 			print_r($a);
// 			echo '</pre>';
// 			die;
		
	
		return new JsonModel(array(
				'data' =>$a,
		));
	
	
	
	}
	
	public function appAction()
	{
		// Hieu nang cua app ko cao
	
		$haivltv = array ();
		$url_haivltv = "http://haivl.tv";
		$client = new HttpClient();
		$client->setAdapter('Zend\Http\Client\Adapter\Curl');
			
		$response = $this->getResponse();
		$response->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
			
		$client->setUri('http://haivl.tv');
		$result                 = $client->send();
		$body                   = $result->getBody();  //content of the web
	
		return new JsonModel(array(
				'data' =>$body, //$zf2array,
		));
	}
	
	public function grab_image($url,$saveto){
		$ch = curl_init ($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_BINARYTRANSFER,1);
		$raw=curl_exec($ch);
		curl_close ($ch);
		if(file_exists($saveto)){
			unlink($saveto);
		}
		$fp = fopen($saveto,'x');
		fwrite($fp, $raw);
		fclose($fp);
	}
	
	public function downloadFile($url, $path, $file)
	{
		//if (!file_exists($path)) mkdir($path, 0755, true);
		$filePath = $path . '/' . $file;
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
		curl_setopt($ch, CURLOPT_TIMEOUT, 15);
		$fp = fopen($filePath, "w");
		$options = array(
				CURLOPT_URL => $url,
				CURLOPT_FOLLOWLOCATION => 1,
				CURLOPT_FILE => $fp);
		curl_setopt_array($ch, $options);
	
		curl_exec($ch);
		curl_close($ch);
		fclose($fp);
	}
	public function GetImageFromUrl($link)
	{
	
		$ch = curl_init();
	
		curl_setopt($ch, CURLOPT_POST, 0);
	
		curl_setopt($ch,CURLOPT_URL,$link);
	
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	
		$result=curl_exec($ch);
	
		curl_close($ch);
	
		return $result;
	
	}
	
	
	public function testdomAction()
	{
		$file = 'http://s2.haivl.com/data/photos2/20140617/00a79804d8214d3d9c7f37a320573cb0/medium-186e043a08c64a94a0a7e7871a9434a0-650.jpg';
		 
		$this->grab_image($file,APP_IMG."/name.jpg");
		//$this->downloadFile($file, APP_IMG,"name.jpg");
	
		die;
	
		//  		$contents= file_get_contents($file);
		//  		die;
	
		// 		$contents= $this->GetImageFromUrl($file);
	
		// 		$imagePath = sys_get_temp_dir() . '/' . basename($file);
		// 		file_put_contents($imagePath, file_get_contents($file));
		// 		$image = Zend_Pdf_Image::imageWithPath($imagePath);
		// 		unlink($imagePath);
	
		$sourcecode = $this->GetImageFromUrl($file);
		//var_dump($sourcecode);die;
		$savefile = fopen(APP_IMG.'/'.'Page_05.jpg', 'w');
		fwrite($savefile, $sourcecode);
		fclose($savefile);
		die;
	
	
	
	
	
	
	
	
	
		$html = '<div id="content">
        <div id="mainContainer">
	
	
	
<div id="leftColumn">
	
    <div class="videoDetails">
        <div class="video">
			<iframe width="728" height="410" src="http://www.youtube.com/embed/ju8nHwAipEY?rel=0&amp;showinfo=0&amp;iv_load_policy=3&amp;modestbranding=1&amp;nologo=1&amp;vq=large&amp;autoplay=0&amp;ps=docs" frameborder="0" allowfullscreen="1">
			</iframe>
        </div>
	
        <h1>Chiến đấu trên bảng cực kì thú vị <img class="emo" src="http://s.haivl.tv/content/images/emo/static/thumbsup.png"></h1>
        <div class="stats">
            <div class="statsContent">
                <div class="numbers">
                    <span class="views">58.580</span>
                    <span class="comments">52</span>
                </div>
	
	
                <div class="fb-like fb_iframe_widget" data-href="http://haivl.tv/video/8514" data-send="false" data-layout="button_count" data-width="90" data-show-faces="false" data-share="true" fb-xfbml-state="rendered" fb-iframe-plugin-query="app_id=181604928677768&amp;href=http%3A%2F%2Fhaivl.tv%2Fvideo%2F8514&amp;layout=button_count&amp;locale=en_US&amp;sdk=joey&amp;send=false&amp;share=true&amp;show_faces=false&amp;width=90"><span style="vertical-align: bottom; width: 127px; height: 20px;"><iframe name="f1b1f5cbf4" width="90px" height="1000px" frameborder="0" allowtransparency="true" scrolling="no" title="fb:like Facebook Social Plugin" src="http://www.facebook.com/plugins/like.php?app_id=181604928677768&amp;channel=http%3A%2F%2Fstatic.ak.facebook.com%2Fconnect%2Fxd_arbiter%2FV80PAcvrynR.js%3Fversion%3D41%23cb%3Df16b514248%26domain%3Dhaivl.tv%26origin%3Dhttp%253A%252F%252Fhaivl.tv%252Ff24b784308%26relation%3Dparent.parent&amp;href=http%3A%2F%2Fhaivl.tv%2Fvideo%2F8514&amp;layout=button_count&amp;locale=en_US&amp;sdk=joey&amp;send=false&amp;share=true&amp;show_faces=false&amp;width=90" style="border: none; visibility: visible; width: 127px; height: 20px;" class=""></iframe></span></div>
	
            </div>
            <div class="clear">
            </div>
        </div>
        <div class="fp">
            <h4>
                <img src="http://s.haivl.tv/content/images/emo/static/thumbsup.png">
                Like <a href="http://www.facebook.com/haivl.tv" target="_blank" class="colorful">Haivl TV trên Facebook</a> để được cập nhật những clip hay nhất</h4>
            <div class="fb-like fb_iframe_widget" data-href="http://www.facebook.com/haivl.tv" data-send="false" data-width="500" data-show-faces="false" fb-xfbml-state="rendered" fb-iframe-plugin-query="app_id=181604928677768&amp;href=http%3A%2F%2Fwww.facebook.com%2Fhaivl.tv&amp;locale=en_US&amp;sdk=joey&amp;send=false&amp;show_faces=false&amp;width=500"><span style="vertical-align: bottom; width: 500px; height: 20px;"><iframe name="f2bfbd5b54" width="500px" height="1000px" frameborder="0" allowtransparency="true" scrolling="no" title="fb:like Facebook Social Plugin" src="http://www.facebook.com/plugins/like.php?app_id=181604928677768&amp;channel=http%3A%2F%2Fstatic.ak.facebook.com%2Fconnect%2Fxd_arbiter%2FV80PAcvrynR.js%3Fversion%3D41%23cb%3Df37ec29ce%26domain%3Dhaivl.tv%26origin%3Dhttp%253A%252F%252Fhaivl.tv%252Ff24b784308%26relation%3Dparent.parent&amp;href=http%3A%2F%2Fwww.facebook.com%2Fhaivl.tv&amp;locale=en_US&amp;sdk=joey&amp;send=false&amp;show_faces=false&amp;width=500" style="border: none; visibility: visible; width: 500px; height: 20px;" class=""></iframe></span></div>
        </div>
	
        <div class="commentContainer">
            <h3>
                Chém gió</h3>
			<div class="fb-comments fb_iframe_widget" data-href="http://haivl.tv/video/8514" data-num-posts="10" data-width="728" fb-xfbml-state="rendered"><span style="height: 1727px; width: 728px;"><iframe id="f1ca7c6e6c" name="fc3b72ec4" scrolling="no" title="Facebook Social Plugin" class="fb_ltr" src="https://www.facebook.com/plugins/comments.php?api_key=181604928677768&amp;channel_url=http%3A%2F%2Fstatic.ak.facebook.com%2Fconnect%2Fxd_arbiter%2FV80PAcvrynR.js%3Fversion%3D41%23cb%3Df1d76d325%26domain%3Dhaivl.tv%26origin%3Dhttp%253A%252F%252Fhaivl.tv%252Ff24b784308%26relation%3Dparent.parent&amp;href=http%3A%2F%2Fhaivl.tv%2Fvideo%2F8514&amp;locale=en_US&amp;numposts=10&amp;sdk=joey&amp;width=728" style="border: none; overflow: hidden; height: 1727px; width: 728px;"></iframe></span></div>
        </div>
    </div>
    <div id="footer">
	
	
    <div>
        <b class="copyright">© 2014 <a href="/">Haivl TV</a></b>
    </div>
    <div class="clear">
    </div>
</div>
</div>
<div id="rightColumn">
	
	
	
	
	
	
<div class="videoDetails">
    <div class="recommend">
	
            <div class="recommmendItem">
                <a href="/video/8445?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/pMn6XHHSwJc/mqdefault.jpg" alt="Rắn Hổ Cực Dài Cả Làng Ra Xem!!!" width="120">
                            <div class="hot">
                                Hot</div>
                        <div class="duration">1:31</div>
                    </div>
                    <div class="info">
                        <h4>
                            Rắn Hổ Cực Dài Cả Làng Ra Xem!!!</h4>
                        <div class="stats">
                            <span class="views">366.130</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/8498?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/oPlCyDI711E/mqdefault.jpg" alt="Phẫn nộ thiếu nữ khoe chân trên giường ngủ :-w" width="120">
                            <div class="hot">
                                Hot</div>
                        <div class="duration">1:18</div>
                    </div>
                    <div class="info">
                        <h4>
                            Phẫn nộ thiếu nữ khoe chân trên giường ngủ <img class="emo" src="http://s.haivl.tv/content/images/emo/static/meh.png"></h4>
                        <div class="stats">
                            <span class="views">160.218</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/8515?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/NCDSu3MPk3U/mqdefault.jpg" alt="Không thể nhịn cười =)) đúng là ngu như con cờ hó :v cười như ngựa nhai ngô :))" width="120">
                            <div class="hot">
                                Hot</div>
                        <div class="duration">0:7</div>
                    </div>
                    <div class="info">
                        <h4>
                            Không thể nhịn cười <img class="emo" src="http://s.haivl.tv/content/images/emo/static/roflmao.png"> đúng là ngu như con cờ hó <img class="emo" src="http://s.haivl.tv/content/images/emo/static/wtf.png">...</h4>
                        <div class="stats">
                            <span class="views">9.150</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/8512?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/eoj6wRrZFvY/mqdefault.jpg" alt="Có bạn nào nhớ tên ca khúc này ko :x" width="120">
                            <div class="hot">
                                Hot</div>
                        <div class="duration">6:24</div>
                    </div>
                    <div class="info">
                        <h4>
                            Có bạn nào nhớ tên ca khúc này ko <img class="emo" src="http://s.haivl.tv/content/images/emo/static/inlove.png"></h4>
                        <div class="stats">
                            <span class="views">51.444</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/8516?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/iclYZqU6hf8/mqdefault.jpg" alt="Tuổi Trẻ tài cao  em đã cười vỡ bụng sau khi xem clip này :v :v" width="120">
                            <div class="new">
                                Mới</div>
                        <div class="duration">2:06</div>
                    </div>
                    <div class="info">
                        <h4>
                            Tuổi Trẻ tài cao  em đã cười vỡ bụng sau khi xem cli...</h4>
                        <div class="stats">
                            <span class="views">16.022</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/8507?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/Fd5fCCylsak/mqdefault.jpg" alt="Chơi ngu level bá đạo =))" width="120">
                            <div class="new">
                                Mới</div>
                        <div class="duration">0:34</div>
                    </div>
                    <div class="info">
                        <h4>
                            Chơi ngu level bá đạo <img class="emo" src="http://s.haivl.tv/content/images/emo/static/roflmao.png"></h4>
                        <div class="stats">
                            <span class="views">74.264</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/7131?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/bZvBn4GKwWY/mqdefault.jpg" alt="Con bướm xuân version 2 con nhộng :))" width="120">
                        <div class="duration">3:20</div>
                    </div>
                    <div class="info">
                        <h4>
                            Con bướm xuân version 2 con nhộng <img class="emo" src="http://s.haivl.tv/content/images/emo/static/laugh.png"></h4>
                        <div class="stats">
                            <span class="views">20.621</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/134?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/vmogDKpviT8/mqdefault.jpg" alt="Đám cưới đậm chất công nghệ - Đám cưới đẳng cấp" width="120">
                        <div class="duration">5:33</div>
                    </div>
                    <div class="info">
                        <h4>
                            Đám cưới đậm chất công nghệ - Đám cưới đẳng cấp</h4>
                        <div class="stats">
                            <span class="views">31.560</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
                <div class="fixedScrollDetector">
                </div>
                <div class="fixedScroll" style="position: relative;">
            <div class="recommmendItem">
                <a href="/video/6509?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/JHEv-6KAk1o/mqdefault.jpg" alt="Cùng lên nóc nhà với DJ Blend nhé :D" width="120">
                        <div class="duration">10:03</div>
                    </div>
                    <div class="info">
                        <h4>
                            Cùng lên nóc nhà với DJ Blend nhé <img class="emo" src="http://s.haivl.tv/content/images/emo/static/biggrin.png"></h4>
                        <div class="stats">
                            <span class="views">70.424</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/1936?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/0Qqd6T_A9LY/mqdefault.jpg" alt="Mỗi lần nghe bài này em nổi da cmn gà hết :3" width="120">
                        <div class="duration">3:01</div>
                    </div>
                    <div class="info">
                        <h4>
                            Mỗi lần nghe bài này em nổi da cmn gà hết <img class="emo" src="http://s.haivl.tv/content/images/emo/static/curlylips.png"></h4>
                        <div class="stats">
                            <span class="views">30.047</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/455?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/HE7_Dl8RvSc/mqdefault.jpg" alt="Cỗ máy đặc biệt lắp ráp bằng LEGO khiến cư dân mạng thán phục" width="120">
                        <div class="duration">7:04</div>
                    </div>
                    <div class="info">
                        <h4>
                            Cỗ máy đặc biệt lắp ráp bằng LEGO khiến cư dân mạng ...</h4>
                        <div class="stats">
                            <span class="views">26.536</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/5949?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/8xfjJZFCLVI/mqdefault.jpg" alt="Bé gái nói cực kỳ đáng yêu :x" width="120">
                        <div class="duration">0:16</div>
                    </div>
                    <div class="info">
                        <h4>
                            Bé gái nói cực kỳ đáng yêu <img class="emo" src="http://s.haivl.tv/content/images/emo/static/inlove.png"></h4>
                        <div class="stats">
                            <span class="views">62.367</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/4344?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/CvLWg2v99uU/mqdefault.jpg" alt="Người ta bảo rồi ... không nên sống dựa vào người khác =))" width="120">
                        <div class="duration">0:47</div>
                    </div>
                    <div class="info">
                        <h4>
                            Người ta bảo rồi ... không nên sống dựa vào ng...</h4>
                        <div class="stats">
                            <span class="views">97.948</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/8309?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/Ojd8FNv8Bk0/mqdefault.jpg" alt="E mới bị con này cắn các bác ạ, chuẩn bị thành Người Nhện rồi :((" width="120">
                        <div class="duration">0:12</div>
                    </div>
                    <div class="info">
                        <h4>
                            E mới bị con này cắn các bác ạ, chuẩn bị thành Người...</h4>
                        <div class="stats">
                            <span class="views">82.934</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
            <div class="recommmendItem">
                <a href="/video/4356?ref=rcm">
                    <div class="thumb">
                        <img src="http://img.youtube.com/vi/H0m6_qzM6ng/mqdefault.jpg" alt="Phát hiện thánh sịp vàng mới =))" width="120">
                        <div class="duration">0:54</div>
                    </div>
                    <div class="info">
                        <h4>
                            Phát hiện thánh sịp vàng mới <img class="emo" src="http://s.haivl.tv/content/images/emo/static/roflmao.png"></h4>
                        <div class="stats">
                            <span class="views">71.867</span>
	
	
                        </div>
                    </div>
                    <div class="clear">
                    </div>
                </a>
            </div>
                </div>
    </div>
</div>
	
</div>
<div class="clear">
</div>
	
	
        </div>
    </div>';
	
		$dom = new Query($html);
		$results = $dom->execute('#content #leftColumn .video');
	
		$count = count($results); // get number of matches: 4
		foreach ($results as $result) {
			// $result is a DOMElement
		}
	
		return new JsonModel(array(
				'data' =>$count, //$zf2array,
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
	  // xu li du lieu tren view
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
	
	// Convert Oject --> array
	public function objectToArray($d) 
	{
		if (is_object($d)) {
			// Gets the properties of the given object
			// with get_object_vars function
			$d = get_object_vars($d);
		}
	
		if (is_array($d)) {
			/*
				* Return array converted to object
			* Using __FUNCTION__ (Magic constant)
			* for recursive call
			*/
			return array_map(__FUNCTION__, $d);
		}
		else {
			// Return array
			return $d;
		}
	}
	
	public function getRssgetTable() {
		if (! $this->rssgetTable) {
			$sm = $this->getServiceLocator ();
			$this->rssgetTable = $sm->get ( 'Rssget\Model\RssgetTable' );
		}
		return $this->rssgetTable;
	}
}
