<?php
namespace Igirlxinhcom\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;
use Igirlxinhcom\Model\Igirlxinhcom;
use Igirlxinhcom\Form\IgirlxinhcomForm;
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

class IgirlxinhcomController extends AbstractActionController {
	protected $igirlxinhcomTable;
	
	public function indexAction() {

		$select = new Select ();
		$order_by = $this->params ()->fromRoute ( 'order_by' ) ? $this->params ()->fromRoute ( 'order_by' ) : 'id';
		$order = $this->params ()->fromRoute ( 'order' ) ? $this->params ()->fromRoute ( 'order' ) : Select::ORDER_ASCENDING;
		$page = $this->params ()->fromRoute ( 'page' ) ? ( int ) $this->params ()->fromRoute ( 'page' ) : 1;
		
		$igirlxinhcoms = $this->getIgirlxinhcomTable ()->fetchAllIgirlCom( $select->order ( $order_by . ' ' . $order ) );
		
		//var_dump($igirlxinhcoms);die;
		$itemsPerPage = 3;
		
		$igirlxinhcoms->current ();
		$paginator = new Paginator ( new paginatorIterator ( $igirlxinhcoms ) );
		$paginator->setCurrentPageNumber ( $page )->setItemCountPerPage ( $itemsPerPage )->setPageRange ( 4 );
		
		return new ViewModel ( array (
				// 'igirlxinhcoms' => $this->getIgirlxinhcomTable()->fetchAll(),
				'order_by' => $order_by,
				'order' => $order,
				'page' => $page,
				'paginator' => $paginator 
		) );
	}
	
	public function hotnewgirlAction() {
	
		$select = new Select ();
		$order_by = $this->params ()->fromRoute ( 'order_by' ) ? $this->params ()->fromRoute ( 'order_by' ) : 'id';
		$order = $this->params ()->fromRoute ( 'order' ) ? $this->params ()->fromRoute ( 'order' ) : Select::ORDER_ASCENDING;
		$page = $this->params ()->fromRoute ( 'page' ) ? ( int ) $this->params ()->fromRoute ( 'page' ) : 1;
	
		$igirlxinhcoms = $this->getIgirlxinhcomTable ()->fetchAllPhototamtay( $select->order ( $order_by . ' ' . $order ) );
	
		$itemsPerPage = 3;
	
		$igirlxinhcoms->current ();
		$paginator = new Paginator ( new paginatorIterator ( $igirlxinhcoms ) );
		$paginator->setCurrentPageNumber ( $page )->setItemCountPerPage ( $itemsPerPage )->setPageRange ( 4 );
	
		return new ViewModel ( array (
				// 'igirlxinhcoms' => $this->getIgirlxinhcomTable()->fetchAll(),
				'order_by' => $order_by,
				'order' => $order,
				'page' => $page,
				'paginator' => $paginator
		) );

	
		
	}
	
	
	public function addAction() {
		$form = new IgirlxinhcomForm ();
		$form->get ( 'submit' )->setAttribute ( 'value', 'Add' );
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$igirlxinhcom = new Igirlxinhcom ();
			$form->setInputFilter ( $igirlxinhcom->getInputFilter () );
			$data = array_merge_recursive ( $this->getRequest ()->getPost ()->toArray (), $this->getRequest ()->getFiles ()->toArray () );
				
			$form->setData ( $data);
			if ($form->isValid ()) {
				
								echo 'data';
								echo '<pre>';
								print_r($data);
								echo '</pre>';
								die;
				
				$igirlxinhcom->exchangeArray ( $data);
				$this->getIgirlxinhcomTable ()->saveIgirlxinhcom ( $igirlxinhcom );
				
				// Redirect to list of igirlxinhcoms
				return $this->redirect ()->toRoute ( 'igirlxinhcom' );
			}
		}
		
		return array (
				'form' => $form 
		);
	}
	public function editAction() {
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'igirlxinhcom', array (
					'action' => 'add' 
			) );
		}
		$igirlxinhcom = $this->getIgirlxinhcomTable ()->getIgirlxinhcom ( $id );
		
		$form = new IgirlxinhcomForm ();
		$form->bind ( $igirlxinhcom );
		$form->get ( 'submit' )->setAttribute ( 'value', 'Edit' );
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$form->setData ( $request->getPost () );
			if ($form->isValid ()) {
				$this->getIgirlxinhcomTable ()->saveIgirlxinhcom ( $igirlxinhcom );
				
				// Redirect to list of igirlxinhcoms
				return $this->redirect ()->toRoute ( 'igirlxinhcom' );
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
			return $this->redirect ()->toRoute ( 'igirlxinhcom' );
		}
		
		$request = $this->getRequest ();
		if ($request->isPost ()) {
			$del = $request->getPost ()->get ( 'del', 'No' );
			if ($del == 'Yes') {
				$id = ( int ) $request->getPost ()->get ( 'id' );
				$this->getIgirlxinhcomTable ()->deleteIgirlxinhcom ( $id );
			}
			
			// Redirect to list of igirlxinhcoms
			return $this->redirect ()->toRoute ( 'igirlxinhcom' );
		}
		
		return array (
				'id' => $id,
				'igirlxinhcom' => $this->getIgirlxinhcomTable ()->getIgirlxinhcom ( $id ) 
		);
	}
	
	public function readdetailAction()
	{
		$id = ( int ) $this->params ( 'id' );
		if (! $id) {
			return $this->redirect ()->toRoute ( 'igirlxinhcom' );
		}
	
		$read = $this->getIgirlxinhcomTable()->getIgirlxinhid($id);
		//$this->getManastoryTable ()->getReadManastory( $id ) ;
		return array (
				'id' => $id,
				'readdetail' => $read,
		);
	}
	
	// http://www.wattpad.com/rss?mode=2&language=19
	public function wattpadAction() {
		try {
			// lay theo catalogue truyen : http://www.wattpad.com/rss?mode=1&language=19&category=5
			// lay theo rss languege viet nam
			$wattpad = feed\Reader::import ( 'http://www.wattpad.com/rss?mode=2&language=19' );
		} catch ( feed\Exception\RuntimeException $e ) {
			echo "error : " . $e->getMessage ();
			exit ();
		}
		
		$wattpad_array = array (
				'title' => $wattpad->getTitle (),
				'description' => $wattpad->getDescription (),
				'link' => $wattpad->getLink (),
				'items' => array () 
		);
		
		foreach ( $wattpad as $item ) {
			
			// Zend/Feed/Reader/Entry/rss.php
			$wattpad_array ['items'] [] = array (
					'title' => $item->getTitle (),
					'link' => $item->getLink (),
					'image' => '/default.png',
					'description' => $item->getDescription () 
			);
		}
		
		return new ViewModel ( array (
				'data' => $wattpad_array 
		) );
	}
	public function runtimerssAction() {
		
		// PRINT "Elapsed time was $time seconds.";
		// echo '<script>window.open("http://127.0.0.1:1913/wattpad/haivltv", "_blank", "width=400,height=500")</script>';
		$time = time ();
		$starttime = date ( "Y-m-d", mktime ( 0, 0, 0, date ( "n", $time ), date ( "j", $time ), date ( "Y", $time ) ) );
		$endtime = date ( "Y-m-d", mktime ( 0, 0, 0, date ( "n", $time ), date ( "j", $time ) + 1, date ( "Y", $time ) ) );
		var_dump ( $starttime );
		var_dump ( $endtime );
		
		// $totaltime = ($endtime - $starttime);
		// echo "This page was created in ".$totaltime." seconds";
		
		$startime = "2014-06-18 12:05";
		$starttime = strtotime ( $starttime );
		$oneday = 60 * 60 * 0.1;
		if ($starttime < (time () - $oneday)) {
			echo 'more than one day since start';
			echo '<script>window.open("http://127.0.0.1:1913/wattpad/haivltv", "_blank", "width=400,height=500")</script>';
		} else {
			echo 'started within the last day';
		}
	}
	public function igirlxinhcomAction() {
		$domain = "http://hamtruyen.com";
		$url_haivltv = "http://hamtruyen.com/cuu-dinh-ky-1385.html";
		$client = new HttpClient ();
		$client->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
		$client->setUri ( $url_haivltv );
		$result = $client->send ();
		$body = $result->getBody (); // content of the web
		                                              // echo $body;die;
		$dom = new Query ( $body );
		$title = $dom->execute ( '.wrapper_sanpham .ten_truyen' );
		foreach ( $title as $key => $r ) {
			$aelement = $r->getElementsByTagName ( "a" )->item ( 0 );
			if ($aelement->hasAttributes ()) {
				$nameapp = 'igirlxinhcom';
				$title = $aelement->textContent;
				$link = $url_haivltv;
			}
		}
		
		// .wrapper_sanpham .content_noidung_tomtat
		$content_detail_lop = $dom->execute ( '.wrapper_sanpham .content_noidung_tomtat' );
		foreach ( $content_detail_lop as $keyhai => $valuehaivl ) {
			$description = $this->innerHTML ( $valuehaivl );
		}
		// get Thumbnail img
		$img = $dom->execute ( '.wrapper_sanpham .image_anh img' );
		foreach ( $img as $keydo => $mainelemen ) {
			if ($mainelemen->hasAttributes ()) {
				$image_thumbnail = $mainelemen->getAttributeNode ( 'src' )->nodeValue;
			}
		}
		
		// get Chap of story .wrapper_sanpham .last_chap_update
		$get_chap = $dom->execute ( '.wrapper_sanpham .last_chap_update' );
		// var_dump($get_chap->count());die;
		foreach ( $get_chap as $keyhai => $valuehaivl ) {
			$conten_text = $valuehaivl->getElementsByTagName ( "a" )->item ( 0 );
			if ($aelement->hasAttributes ()) {
				$text = $conten_text->textContent;
				$pieces = explode ( " ", $text ); // get number chapter
				foreach ( $pieces as $element ) {
					if (is_numeric ( $element )) {
						$numberChap = $element;
						// var_dump($aelemen23t);
					}
				}
			}
		}
		
		$igirlxinhcom = array (
				'nameapp' => $nameapp,
				'title' => $title,
				'link' => $link,
				'description' => $description,
				'numberchap' => $text,
				'image_thumbnail' => $image_thumbnail,
				'category' => '',
				'items' => array () 
		);
		
		// .doctruyen_last_first : link doc truyen
		$link_story_get = $dom->execute ( '.wrapper_sanpham .doctruyen_last_first' );
		// var_dump($link_story_get->count());die;
		foreach ( $link_story_get as $key => $aelement ) {
			$conten_text = $aelement->getElementsByTagName ( "a" )->item ( 0 );
			if ($aelement->hasAttributes ()) {
				$link_story_start = $domain . $conten_text->getAttributeNode ( 'href' )->nodeValue;
				
				// eplox get string loop
				$array_explo = explode ( "/", $conten_text->getAttributeNode ( 'href' )->nodeValue ); // $conten_text->getAttributeNode('href')->nodeValue) = '/doc-truyen/cuu-dinh-ky-chapter-1.html'
				$link_story = $array_explo [1]; // doc-truyen
				$url_start = explode ( ".", $array_explo [2] ); // cuu-dinh-ky-chapter-1.html
				$url_next = $url_start [0];
				$pieces2 = explode ( '-', $url_next );
				foreach ( $pieces2 as $e ) {
					if (is_numeric ( $e )) {
						$url_number = $e;
					}
				}
				// $url_loop = preg_replace('/1/', '', 'cuu-dinh-ky-chapter-1') ;
				// $url_loop = str_replace('1', '', 'cuu-dinh-ky-chapter-1') ;
				$reple = '/' . $url_number . '/'; // "'/.1.'/";
				$url_loop = preg_replace ( $reple, '', $url_next ); // 'cuu-dinh-ky-chapter-'
				$link_loop_real = $domain . '/' . $link_story . '/' . $url_loop;
			}
		}
		
		// // lay duoc thanh cong
		
		// $client2 = new HttpClient();
		// $client2->setAdapter('Zend\Http\Client\Adapter\Curl');
		
		// $response2 = $this->getResponse();
		// $response2->getHeaders()->addHeaderLine('content-type', 'text/html; charset=utf-8'); //set content-type
		
		// $client2->setUri('http://hamtruyen.com/doc-truyen/cuu-dinh-ky-chapter-1.html');
		// $result2 = $client2->send();
		// $body2 = $result2->getBody(); //content of the web
		// $dom2 = new Query($body2);
		// $img_array = $dom2->execute('#content .content_chap img');
		
		// $array_img = array();
		// //$item_array[] = $img_array->current()->getAttribute('src');
		// // $folder = DIR_UPLOAD_NEW ;
		// // var_dump($folder);die;
		
		// foreach($img_array as $key)
		// {
		// $array_img[] = $key->getAttribute('src');
		
		// $sour_url = $key->getAttribute('src'); //$sour_url = 'http://file.diendanbaclieu.net/hinh/2013/10/hinh-anh-dep-ve-tinh-yeu-6.jpg';
		// // save img
		// //$dir_file = dirname(__FILE__);
		// $dir_file = DIR_UPLOAD_NEW; //$dir_file.'/uploadnew';
		// $folder = DIR_UPLOAD_NEW ;
		// $sour = pathinfo($sour_url);
		// // Thư mục chứa ảnh
		// if(file_exists($folder.'/'.$sour['basename']))
		// {
		// $dest = $folder.'/'.time().'_'.$sour['basename'];
		// }else
		// {
		// $dest = $folder.'/'.$sour['basename'];
		// }
		
		// $this->save_img($sour_url,$dest);
		// }
		
		// echo '<pre>';
		// print_r($array_img);
		// echo '</pre>';
		// die;
		
		// GetConTentFull Deatil
		
		for($j = 1; $j < $numberChap; $j ++) {
			$chap_look = "Chapter " . $j;
			$get_img_link = $link_loop_real . $j . '.html';
			$neo_chap = "Chap_" . $j;
			// var_dump($urdsdsl);
			
			$content_detail_full = array ();
			
			// $content_detail_full = $this->getContent_chap($get_img_link);
			$content_detail_full = $this->getContent_chap_img ( $get_img_link );
			$igirlxinhcom ['items'] [] = array (
					'Chap' => $chap_look,
					'link_chap' => $get_img_link,
					'detail' => $content_detail_full 
			);
			
			// break; // run one step
		}
		
		// echo '<pre>';
		// print_r($igirlxinhcom);
		// echo '</pre>';
		// die;
		
		// // save
		// foreach ($haivltv as $keysave =>$valuehaivlSave)
		// {
		// $arry_tmp = array();
		// $arry_tmp = (array)$valuehaivlSave;
		// $rssget = New Rssget();
		// $rssget->exchangeArray($arry_tmp);
		// $this->getRssgetTable()->saveRssget($rssget);
		// }
		
		return new JsonModel ( array (
				'data' => $igirlxinhcom 
		) );
	}
	public function igirlxinhcomcopyimgAction() {
		$domain = "http://hamtruyen.com";
		$url_haivltv = "http://hamtruyen.com/cuu-dinh-ky-1385.html";
		$client = new HttpClient ();
		$client->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
		$client->setUri ( $url_haivltv );
		$result = $client->send ();
		$body = $result->getBody (); // content of the web
		                                              // echo $body;die;
		$dom = new Query ( $body );
		$title = $dom->execute ( '.wrapper_sanpham .ten_truyen' );
		foreach ( $title as $key => $r ) {
			$aelement = $r->getElementsByTagName ( "a" )->item ( 0 );
			if ($aelement->hasAttributes ()) {
				$nameapp = 'igirlxinhcom';
				$title = $aelement->textContent;
				$link = $url_haivltv;
			}
		}
		
		// .wrapper_sanpham .content_noidung_tomtat
		$content_detail_lop = $dom->execute ( '.wrapper_sanpham .content_noidung_tomtat' );
		foreach ( $content_detail_lop as $keyhai => $valuehaivl ) {
			$description = $this->innerHTML ( $valuehaivl );
		}
		// get Thumbnail img
		$img = $dom->execute ( '.wrapper_sanpham .image_anh img' );
		foreach ( $img as $keydo => $mainelemen ) {
			if ($mainelemen->hasAttributes ()) {
				$image_thumbnail = $mainelemen->getAttributeNode ( 'src' )->nodeValue;
			}
		}
		
		// get Chap of story .wrapper_sanpham .last_chap_update
		$get_chap = $dom->execute ( '.wrapper_sanpham .last_chap_update' );
		// var_dump($get_chap->count());die;
		foreach ( $get_chap as $keyhai => $valuehaivl ) {
			$conten_text = $valuehaivl->getElementsByTagName ( "a" )->item ( 0 );
			if ($aelement->hasAttributes ()) {
				$text = $conten_text->textContent;
				$pieces = explode ( " ", $text ); // get number chapter
				foreach ( $pieces as $element ) {
					if (is_numeric ( $element )) {
						$numberChap = $element;
						// var_dump($aelemen23t);
					}
				}
			}
		}
		
		$igirlxinhcom = array (
				'nameapp' => $nameapp,
				'title' => $title,
				'link' => $link,
				'description' => $description,
				'numberchap' => $text,
				'image_thumbnail' => $image_thumbnail,
				'category' => '',
				'items' => array () 
		);
		
		// .doctruyen_last_first : link doc truyen
		$link_story_get = $dom->execute ( '.wrapper_sanpham .doctruyen_last_first' );
		foreach ( $link_story_get as $key => $aelement ) {
			$conten_text = $aelement->getElementsByTagName ( "a" )->item ( 0 );
			if ($aelement->hasAttributes ()) {
				$link_story_start = $domain . $conten_text->getAttributeNode ( 'href' )->nodeValue;
				
				// eplox get string loop
				$array_explo = explode ( "/", $conten_text->getAttributeNode ( 'href' )->nodeValue ); // $conten_text->getAttributeNode('href')->nodeValue) = '/doc-truyen/cuu-dinh-ky-chapter-1.html'
				$link_story = $array_explo [1]; // doc-truyen
				$url_start = explode ( ".", $array_explo [2] ); // cuu-dinh-ky-chapter-1.html
				$url_next = $url_start [0];
				$pieces2 = explode ( '-', $url_next );
				foreach ( $pieces2 as $e ) {
					if (is_numeric ( $e )) {
						$url_number = $e;
					}
				}
				// $url_loop = preg_replace('/1/', '', 'cuu-dinh-ky-chapter-1') ;
				// $url_loop = str_replace('1', '', 'cuu-dinh-ky-chapter-1') ;
				$reple = '/' . $url_number . '/'; // "'/.1.'/";
				$url_loop = preg_replace ( $reple, '', $url_next ); // 'cuu-dinh-ky-chapter-'
				$link_loop_real = $domain . '/' . $link_story . '/' . $url_loop;
			}
		}
		
		// GetConTentFull Deatil
		
		for($j = 1; $j < $numberChap; $j ++) {
			$chap_look = "Chapter " . $j;
			$neo_chap = $url_loop . $j . '_';
			$get_img_link = $link_loop_real . $j . '.html';
			
			$content_detail_full = array ();
			// $content_detail_full = $this->getContent_chap($get_img_link); // lay qua the a de lap anh
			$content_detail_full = $this->getContent_chap_img ( $get_img_link ); // lay theo img de co anh
			$img_thumbnail = $this->getContentchapimgThumnail( $get_img_link ); // lay theo img de co anh
			                                                                  // $content_detail_full = $this->getContentChapImg_NameArray($get_img_link,$neo_chap); // lay tem anh tra ve theo mang
			$igirlxinhcom ['items'] [] = array (
					'Chap' => $chap_look,
					'neochap' => $neo_chap,
					'link_chap' => $get_img_link,
					'img_thumbnail'=>$img_thumbnail,
					'detail' => $content_detail_full 
			);
			
			// break; // run one step
		}
		
		// echo '<pre>';
		// print_r($igirlxinhcom);
		// echo '</pre>';
		
		// Lưu tin đã lấy vào file cache
		$path = APPLICATION_PATH23 . '/cache/temp_data.cache.php';
		$content = '<?php $igirlxinhcom = ' . var_export ( $igirlxinhcom, true ) . ';?>';
		$handler = fopen ( $path, 'w+' );
		fwrite ( $handler, $content );
		fclose ( $handler );
		
		// echo 'Lay nhieu chuong </br>';
		// echo '<pre>';
		// print_r($igirlxinhcom['items']);
		// echo '</pre>';
		
		// die;
		
		// die;
		
		echo "-----------------------------------------------------";
		$data_array_cahe = array ();
		$data_array_cahe = $this->get_hamtruyen (); // get con tent form file cahe
		
		echo 'Lay du lieu tu file cache </br>';
		echo '<pre>';
		print_r ( $data_array_cahe );
		echo '</pre>';
		
		die ();
		
		// copy anh
		$arrayimgConvert = array ();
		// save img
		$dir_file = DIR_UPLOAD_NEW; // $dir_file.'/uploadnew';
		$folder = DIR_UPLOAD_NEW;
		// $keyham['neochap'] : 'cuu-dinh-ky-chapter-11_';
		
		foreach ( $igirlxinhcom ['items'] as $keyham ) {
			
			if (! empty ( $keyham ['detail'] )) {
				foreach ( $keyham ['detail'] as $keyhamvalue => $getvalue_link ) {
					$sour = pathinfo ( $getvalue_link );
					// Thư mục chứa ảnh
					if (file_exists ( $folder . '/' . $keyham ['neochap'] . $sour ['basename'] )) {
						$dest = $folder . '/' . time () . '_' . $keyham ['neochap'] . $sour ['basename'];
						$dest_img = time () . '_' . $keyham ['neochap'] . $sour ['basename'];
					} else {
						$dest = $folder . '/' . $keyham ['neochap'] . $sour ['basename'];
						$dest_img = $keyham ['neochap'] . $sour ['basename'];
					}
					
					$tmp_convert = array ();
					$tmp_convert = $dest_img;
					$arrayimgConvert [] = $tmp_convert;
					
					// save img in dir uploadnews
					$this->save_img ( $getvalue_link, $dest );
					
					// break;
				}
			} elseif (empty ( $keyham ['detail'] ) and is_array ( $keyham ['detail'] )) {
				break; // ngat de chuyen next
			}
		}
		
		echo '<pre>';
		print_r ( $arrayimgConvert );
		echo '</pre>';
		die ();
		
		// // save
		// foreach ($haivltv as $keysave =>$valuehaivlSave)
		// {
		// $arry_tmp = array();
		// $arry_tmp = (array)$valuehaivlSave;
		// $rssget = New Rssget();
		// $rssget->exchangeArray($arry_tmp);
		// $this->getRssgetTable()->saveRssget($rssget);
		// }
		
		return new JsonModel ( array (
				'data' => $igirlxinhcom 
		) );
	}
	
	
	public function igirlxinhcomgetAction() {
		$domain = "http://www.igirlxinh.com";
		$url_haivltv = "http://www.igirlxinh.com/";
		$client = new HttpClient ();
		$client->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
		$client->setUri ( $url_haivltv );
		$result = $client->send ();
		$body = $result->getBody (); // content of the web
		                                              // echo $body;die;
		$dom = new Query ( $body );
		$title = $dom->execute ( '#container #content .post-thumb' );
		//var_dump($title->count());die;
		$nameapp = 'igirlxinhcom';
		$igirlxinhcom = array (
				'nameapp' => $nameapp,
				'title' => $nameapp,
				'link' => $domain,
				'items' => array ()
		);
		
		
		$igirlxinhcom ['items'][] = array ();
		$arrayLoop_link = array();
		$i = 0;
		$titles = array();$links = array();
		foreach ( $title as $key => $r ) {
			$tmptitle = array();$tmplink = array();
			$aelement = $r->getElementsByTagName ( "a" )->item (0);
			if ($aelement->hasAttributes ()) {
				//$igirlxinhcom ['items'][$i]->id =  $i;
				$igirlxinhcom ['items'][$i]->link  = '';
				$igirlxinhcom ['items'][$i]->content_detail =  $aelement->getAttribute ( 'title' );
				$igirlxinhcom ['items'][$i]->image_thumbnail  = $aelement->getAttributeNode('href')->nodeValue;
				$i++;
			}
		}
		
// 		// get detail inenr contend
		$get_chap = $dom->execute ( '#container #content .post-thumb' );
		$i= 0;
		foreach ( $get_chap as $keyim =>$valuediv ) {
			$tmp_inner = $this->innerHTML($valuediv);
			$contentget = $this->ConTentDomProcess($tmp_inner); // new dom --> get array loop a : href 
			$igirlxinhcom ['items'][$i]->content_detail_full = $contentget;
			$i++;
		}
		
		
// 		echo "--------------------------------------</br>";
// 		echo '<pre>';
// 		print_r ($igirlxinhcom);
// 		echo '</pre>';
		//die ();

		// Lưu tin đã lấy vào file cache
		$path = APPLICATION_PATH23 . '/cache/igirlxinhcomget.cache.php';
		$content = '<?php $igirlxinhcom = ' . var_export ( $igirlxinhcom, true ) . ';?>';
		$handler = fopen ( $path, 'w+' );
		fwrite ( $handler, $content );
		fclose ( $handler );
	
		
 		//die ();
		
		
		
		
		
// 		// copy anh
// 		$arrayimgConvert = array ();
// 		// save img
// 		$dir_file = DIR_UPLOAD_NEW; // $dir_file.'/uploadnew';
// 		$folder = DIR_UPLOAD_NEW;
// 		// $keyham['neochap'] : 'cuu-dinh-ky-chapter-11_';
		
// 		foreach ( $igirlxinhcom ['items'] as $keyham ) {
			
// 			if (! empty ( $keyham ['detail'] )) {
// 				foreach ( $keyham ['detail'] as $keyhamvalue => $getvalue_link ) {
// 					$sour = pathinfo ( $getvalue_link );
// 					// Thư mục chứa ảnh
// 					if (file_exists ( $folder . '/' . $keyham ['neochap'] . $sour ['basename'] )) {
// 						$dest = $folder . '/' . time () . '_' . $keyham ['neochap'] . $sour ['basename'];
// 						$dest_img = time () . '_' . $keyham ['neochap'] . $sour ['basename'];
// 					} else {
// 						$dest = $folder . '/' . $keyham ['neochap'] . $sour ['basename'];
// 						$dest_img = $keyham ['neochap'] . $sour ['basename'];
// 					}
					
// 					$tmp_convert = array ();
// 					$tmp_convert = $dest_img;
// 					$arrayimgConvert [] = $tmp_convert;
					
// 					// save img in dir uploadnews
// 					$this->save_img ( $getvalue_link, $dest );
					
// 					// break;
// 				}
// 			} elseif (empty ( $keyham ['detail'] ) and is_array ( $keyham ['detail'] )) {
// 				break; // ngat de chuyen next
// 			}
// 		}
		
// 		echo '<pre>';
// 		print_r ( $arrayimgConvert );
// 		echo '</pre>';
// 		die ();
		
		// save
		foreach ($igirlxinhcom as $keysave )
		{
			$nameppp = $igirlxinhcom['nameapp'];
			$titlep  = $igirlxinhcom['title'];
			$linkp =   $igirlxinhcom['link'];
			
			foreach ($igirlxinhcom['items'] as $lkey => $lvalue)
			{
				$atrrytmp = array();
				$atrrytmp = (array)$lvalue;
				$igirl = New Igirlxinhcom();
				$igirl->exchangeArrayigril($atrrytmp,$nameppp,$titlep,$linkp);
				$id_girl = $this->getigirlxinhcomTable()->saveIgirlxinhcom($igirl);
				
				// save 1-n field content_detail_full in table content_detail_full
				$arrject = (array)$lvalue->content_detail_full; // Oject

// 				var_dump($id_girl);
// 				echo "--------------------------</br>";
//                 echo "<pre>";
// 				print_r($arrject);
// 				echo "</pre>";
				
				if (is_array($arrject) and !empty($arrject))
				{
				
					$returnResult = $this->getigirlxinhcomTable()->saveContent_detail_full($arrject,$id_girl);
// 									echo "-----------returnResult---------------</br>";
// 					                echo "<pre>";
// 									print_r($returnResult);
// 									echo "</pre>";
				   // break; 
					if($returnResult === 1)
					{
							
					}else {
						die('Oop! Error .Not Save detail . Please try again');
				
					}
				
				}
			}
			
		}
		

		
		
		return new JsonModel ( array (
				'data' => $igirlxinhcom 
		) );
	}
	
	
	public function igirlxinhcomrestAction()
	{
		$name ="igirlxinhcomget.cache";
		$data_array_cahe = array ();
		
		// not reque file cache in controller 
		
		$data_array_cahe = $this->get_temcaches1234($name); // get con tent form file cahe
		return new JsonModel ( array (
				'data' => $data_array_cahe
		) );
	}
	
	
	public function phototamtayvngetAction() 
	{
		$domain = "http://photo.tamtay.vn";
		$url_haivltv = "http://photo.tamtay.vn";
		$client = new HttpClient ();
		$client->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
		$client->setUri ( $url_haivltv );
		$result = $client->send ();
		$body = $result->getBody (); // content of the web
		// echo $body;die;
		$dom = new Query ( $body );
		// get img 
		$linkarr = $dom->execute ( '.body .photoPage .site-wrap .item .img' );
		//var_dump($imgarr->count());die;
		if($linkarr->count() > 0 ) { $continue  = true;}else {$continue = false;}
		if ($continue === false) { print "Not get Data "; exit(); }
		$nameapp = 'phototamtayvn';
		$phototamtayvn = array (
				'nameapp' => $nameapp,
				'title' => $nameapp,
				'link' => $domain,
				'items' => array ()
		);
	
	
		$phototamtayvn ['items'][] = array ();
		$arrayLoop_link = array();
		$i = 0;
		$titles = array();$links = array();
		foreach ( $linkarr as $key => $r ) {
			$tmptitle = array();$tmplink = array();
			$aelement = $r->getElementsByTagName ( "a" )->item (0);
			if ($aelement->hasAttributes ()) {
				$linktmpkk = $domain.$aelement->getAttributeNode('href')->nodeValue;
				$phototamtayvn ['items'][$i]->link  = $linktmpkk;
				$array_tmp_content_full =  $this->getContentImgFull($linktmpkk); // get array all img
				//$array_tmp_content_full =  $this->getContentinnerdiv($linktmpkk); // chuan roi
				$phototamtayvn ['items'][$i]->content_detail_full = $array_tmp_content_full;
				$i++;
			}
		}
		
		// get img decription
		$imgarray = $dom->execute ( '.body .photoPage .site-wrap .item .img img' );
		$i = 0;
		foreach ( $imgarray as $key => $r ) {
			if ($r->hasAttributes())
			{
				$phototamtayvn ['items'][$i]->image_thumbnail =  $r->getAttributeNode( 'src' )->nodeValue;
			    $i++;
			}
		}	

		// get img decription
		$titlearr = $dom->execute ( '.body .photoPage .site-wrap .item .infoAlbum .titleAlbum h3' );
		$i = 0;
		foreach ( $titlearr as $key => $r ) {
			$aelement = $r->getElementsByTagName ( "a" )->item (0);
			if ($aelement->hasAttributes ()) {
				$phototamtayvn ['items'][$i]->content_detail = $aelement->textContent;;
			    $i++;
			}
				
		}
	
		
	
		// 		echo "--------------------------------------</br>";
		// 		echo '<pre>';
		// 		print_r ($phototamtayvn);
		// 		echo '</pre>';
		//die ();
	
		// Lưu tin đã lấy vào file cache
		$path = APPLICATION_PATH23 . '/cache/phototamtayvnget.cache.php';
		$content = '<?php $phototamtayvn = ' . var_export ( $phototamtayvn, true ) . ';?>';
		$handler = fopen ( $path, 'w+' );
		fwrite ( $handler, $content );
		fclose ( $handler );
	
	
		//die ();
	
	
	
	
	
		// 		// copy anh
		// 		$arrayimgConvert = array ();
		// 		// save img
		// 		$dir_file = DIR_UPLOAD_NEW; // $dir_file.'/uploadnew';
		// 		$folder = DIR_UPLOAD_NEW;
		// 		// $keyham['neochap'] : 'cuu-dinh-ky-chapter-11_';
	
		// 		foreach ( $phototamtayvn ['items'] as $keyham ) {
			
		// 			if (! empty ( $keyham ['detail'] )) {
		// 				foreach ( $keyham ['detail'] as $keyhamvalue => $getvalue_link ) {
		// 					$sour = pathinfo ( $getvalue_link );
		// 					// Thư mục chứa ảnh
		// 					if (file_exists ( $folder . '/' . $keyham ['neochap'] . $sour ['basename'] )) {
		// 						$dest = $folder . '/' . time () . '_' . $keyham ['neochap'] . $sour ['basename'];
		// 						$dest_img = time () . '_' . $keyham ['neochap'] . $sour ['basename'];
		// 					} else {
		// 						$dest = $folder . '/' . $keyham ['neochap'] . $sour ['basename'];
		// 						$dest_img = $keyham ['neochap'] . $sour ['basename'];
		// 					}
			
		// 					$tmp_convert = array ();
		// 					$tmp_convert = $dest_img;
		// 					$arrayimgConvert [] = $tmp_convert;
			
		// 					// save img in dir uploadnews
		// 					$this->save_img ( $getvalue_link, $dest );
			
		// 					// break;
		// 				}
		// 			} elseif (empty ( $keyham ['detail'] ) and is_array ( $keyham ['detail'] )) {
		// 				break; // ngat de chuyen next
		// 			}
		// 		}
	
		// 		echo '<pre>';
		// 		print_r ( $arrayimgConvert );
		// 		echo '</pre>';
		// 		die ();
	
		//echo "---------------------------------------------------</br>";
		// save
		foreach ($phototamtayvn as $keysave )
		{
			$nameppp = $phototamtayvn['nameapp'];
			$titlep  = $phototamtayvn['title'];
			$linkp =   $phototamtayvn['link'];
				
			foreach ($phototamtayvn['items'] as $lkey => $lvalue)
			{
				$atrrytmp = array();
				$atrrytmp = (array)$lvalue;
				
				$igirl = New Igirlxinhcom();
				$igirl->exchangeArrayigril($atrrytmp,$nameppp,$titlep,$linkp);
				$id_tamtay = $this->getigirlxinhcomTable()->saveIgirlxinhcom($igirl);

				// save 1-n field content_detail_full in table content_detail_full
				   $arrject = (array)$lvalue->content_detail_full; // Oject
				   if (is_array($arrject) and !empty($arrject))
				   {
				   	
				   	$returnResult = $this->getigirlxinhcomTable()->saveContent_detail_full($arrject,$id_tamtay);
				   	
				   	if($returnResult === 1)
				   	{
				   		
				   	}else {
				   		die('Oop! Error .Not Save detail . Please try again');
				   	
				   	}
				   
				   }
				
			}
				
		}
	
		return new JsonModel ( array (
				'data' => $phototamtayvn
		) );
	}
	
	//Query Dom
	public function ConTentDomProcess($dom)
	{
		$DomAr = new Query($dom);
		$ArrDom = $DomAr->execute('a');
		//return $ArrDom->count();
		$array_img_get = array();
		foreach ( $ArrDom as $key => $r ) {
// 			$aelement = $r->getElementsByTagName ( "a" )->item (0);
// 			if ($aelement->hasAttributes ()) {
				$array_img_get[]  = $r->getAttributeNode('href')->nodeValue;
	//		}
		}
		
		return $array_img_get;
	}
	
	// get item file cahe
	public function get_hamtruyen() {
		$data1 = array ();
		$dir = APPLICATION_PATH23 . '/cache/temp_data.cache.php';
		
		if (file_exists ( $dir )) {
			require $dir;
			if (isset ( $igirlxinhcom ) and $igirlxinhcom) {
				foreach ( $igirlxinhcom as $newk ) {
					$data = array (
							'nameapp' => $igirlxinhcom ['nameapp'],
							'title' => $igirlxinhcom ['title'],
							'link' => $igirlxinhcom ['link'],
							'description' => $igirlxinhcom ['description'],
							'numberchap' => $igirlxinhcom ['numberchap'],
							'image_thumbnail' => $igirlxinhcom ['image_thumbnail'],
							'category' => $igirlxinhcom ['category'],
							'items' => array () 
					);
				}
				foreach ( $igirlxinhcom ['items'] as $keyloop ) {
					$data2 = array ();
					foreach ( $keyloop ['detail'] as $key => $value ) {
						$tmpl = array ();
						$tmpl = $value;
						$data2 [] = $tmpl;
					}
					$data ['items'] [] = array (
							'id'=>$keyloop ['id'],
							'Chap' => $keyloop ['Chap'],
							'neochap' => $keyloop ['neochap'],
							'link_chap' => $keyloop ['link_chap'],
							'img_thumbnail'=>$keyloop ['img_thumbnail'],
							'detail' => $data2 
					);
				}
			}
		}
		
		return $data;
	}
	
	//
	// get item file cahe
	public function get_temcaches1234($name) {
		$data1 = array ();
		$dir = APPLICATION_PATH23 . '/cache/'.$name.'.php';
		
		if (file_exists ( $dir )) {
			
			include $dir;
			return $igirlxinhcom;die;
			if (isset ( $igirlxinhcom ) and $igirlxinhcom) 
			{
				
				
				foreach ( $igirlxinhcom as $newk ) 
				{
					$data = array (
							'nameapp' => $igirlxinhcom ['nameapp'],
							'title' => $igirlxinhcom ['title'],
							'items' => array ()
					);
				}
				
					
				$i=0;
				$data['items'][] = array ();
				foreach ( $igirlxinhcom ['items'] as $keyloop ) 
				{
					
					//$data['items'][$i]->id =  $keyloop ['id'];
					$data['items'][$i]->content_detail =  $keyloop ['title'];
					$data['items'][$i]->image_thumbnail  =  $keyloop ['image_thumbnail'];
					$data['items'][$i]->content_detail_full  =  $keyloop ['detail'];
					$i++;	
				
				}
			}
		}
	
		return $data;
	}
	
	
	// input : $img_array = $dom2->execute('#content .content_chap .separator');
	// Out put : Array img all
	public function getContent_chap($url = null) {
		// get Content
		if ($url === null) {
			return $array_img = null;
		} else {
			$client2 = new HttpClient ();
			$client2->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
			
			$response2 = $this->getResponse ();
			$response2->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
			
			$client2->setUri ( $url );
			$result2 = $client2->send ();
			$body2 = $result2->getBody (); // content of the web
			                                                // return $body2; die;
			                                                // echo $body2;
			$dom2 = new Query ( $body2 );
			// get img detail story
			// $img_array = $dom2->execute('#content .content_chap img');
			$img_array = $dom2->execute ( '#content .content_chap .separator' );
			// $arra = (array)$video;
			$a = $img_array->count ();
			// return $a;exit();
			$array_img = array ();
			foreach ( $img_array as $key => $r ) {
				$aelement = $r->getElementsByTagName ( "a" )->item ( 0 );
				if ($aelement->hasAttributes ()) {
					$tmp_link_img = array ();
					$tmp_link_img = $aelement->getAttributeNode ( 'href' )->nodeValue;
					$array_img [] = $tmp_link_img;
				}
			}
			
			return $array_img;
		}
	}
	// getContentChapImg_NameArray
	public function getContentChapImg_NameArray($url = null, $neo_chap) {
		$array_img = array (); // $array_img : link anh goc
		$array_img_convert = array (); // $array_img_convert : cuu-dinh-ky-chapter-1_08.jpg
		                              // get Content
		if ($url === null) {
			return $array_img_convert = null;
		} else {
			$client2 = new HttpClient ();
			$client2->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
			
			$response2 = $this->getResponse ();
			$response2->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
			
			$client2->setUri ( $url );
			$result2 = $client2->send ();
			$body2 = $result2->getBody (); // content of the web
			$dom2 = new Query ( $body2 );
			$img_array = $dom2->execute ( '#content .content_chap img' );
			foreach ( $img_array as $key ) {
				$tmp_img = array ();
				$tmp_convert = array ();
				
				$tmp_img = $key->getAttribute ( 'src' );
				$array_img [] = $tmp_img;
				
				$sour_url = $key->getAttribute ( 'src' ); // $sour_url = 'http://file.diendanbaclieu.net/hinh/2013/10/hinh-anh-dep-ve-tinh-yeu-6.jpg';
				                                       // save img
				$dir_file = DIR_UPLOAD_NEW; // $dir_file.'/uploadnew';
				$folder = DIR_UPLOAD_NEW;
				$sour = pathinfo ( $sour_url );
				// Thư mục chứa ảnh
				if (file_exists ( $folder . '/' . $neo_chap . $sour ['basename'] )) {
					$dest = $folder . '/' . time () . '_' . $neo_chap . $sour ['basename'];
					$dest_img = time () . '_' . $neo_chap . $sour ['basename'];
				} else {
					$dest = $folder . '/' . $neo_chap . $sour ['basename'];
					$dest_img = $neo_chap . $sour ['basename'];
				}
				
				$tmp_convert = $dest_img;
				$array_img_convert [] = $tmp_convert;
			}
			
			return $array_img_convert;
		}
	}
	
	// Return : array link goc truyen
	public function getContent_chap_img($url = null) 
	{
		// get Content
		if ($url === null) {
			return $array_img = null;
		} else {
			$client2 = new HttpClient ();
			$client2->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
			
			$response2 = $this->getResponse ();
			$response2->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
			
			$client2->setUri ( $url );
			$result2 = $client2->send ();
			$body2 = $result2->getBody (); // content of the web
			                                                // return $body2; die;
			                                                // echo $body2;
			$dom2 = new Query ( $body2 );
			// get img detail story
			$img_array = $dom2->execute ( '#content .content_chap img' );
			$array_img = array ();
			// $array_img[] = $img_array->current()->getAttribute('src');;
			foreach ( $img_array as $key ) {
				$array_img [] = $key->getAttribute ( 'src' );
			}
			
			return $array_img;
		}
	}
	
	//GetContentImgFull
	public function getContentImgFull($url = null)
	{
		
		// get Content
		if ($url === null) {
			return $array_img = null;
		} else {
			$client2 = new HttpClient ();
			$client2->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
				
			$response2 = $this->getResponse ();
			$response2->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
				
			$client2->setUri ( $url );
			$result2 = $client2->send ();
			$body2 = $result2->getBody (); // content of the web
			// return $body2; die;
			// echo $body2;
			$dom2 = new Query ( $body2 );
			// get img detail story
			$img_array23 = $dom2->execute ( '.photoPage .site-wrap3 .containerLeft .imgDetailArticle .t_center img.image_tag' );
			//$img_array23 = $dom2->execute ( '.photoPage .site-wrap3 .containerLeft .imgDetailArticle .t_center img' );
			$array_img = array ();
			foreach ( $img_array23 as $key245 ) {
				$array_img [] = $key245->getAttribute('data-original');
			}
				

			
			return $array_img;
		}
	}
	
	// get inner div
	public function getContentinnerdiv($url = null)
	{
	
		// get Content
		if ($url === null) {
			return $array_img = null;
		} else {
			$client2 = new HttpClient ();
			$client2->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
	
			$response2 = $this->getResponse ();
			$response2->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
	
			$client2->setUri ( $url );
			$result2 = $client2->send ();
			$body2 = $result2->getBody (); // content of the web
			// return $body2; die;
			// echo $body2;
			$dom2 = new Query ( $body2 );
			// get img detail story
			$img_array = $dom2->execute ( '.photoPage .site-wrap3 .containerLeft .imgDetailArticle' );
			//return $img_array->count();
			$array_img = array ();
			// $array_img[] = $img_array->current()->getAttribute('src');;
			foreach ( $img_array as $key ) {
				$array_img = $this->innerHTML($key);
			}
	
			return $array_img;
		}
	}
	
	// Return : array link goc truyen
	public function getContentchapimgThumnail($url = null) {
		// get Content
		if ($url === null) {
			return $array_img = null;
		} else {
			$client2 = new HttpClient ();
			$client2->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
				
			$response2 = $this->getResponse ();
			$response2->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
				
			$client2->setUri ( $url );
			$result2 = $client2->send ();
			$body2 = $result2->getBody (); // content of the web
			// return $body2; die;
			// echo $body2;
			$dom2 = new Query ( $body2 );
			// get img detail story
			$img_array = $dom2->execute ( '#content .content_chap img' );
			$array_img = array ();
			// $array_img[] = $img_array->current()->getAttribute('src');;
			foreach ( $img_array as $key ) {
				$array_img = $key->getAttribute ( 'src' ); // lay img cuoi cung
				//break; // lay cai img dau tien
			}
				
			return $array_img;
		}
	}
	
	// Return : Array img convert when get img link destination
	public function getContentChapImgCopy($url = null, $neo_chap) {
		$array_img = array (); // $array_img : link anh goc
		$array_img_convert = array (); // $array_img_convert : cuu-dinh-ky-chapter-1_08.jpg
		                              // get Content
		if ($url === null) {
			return $array_img_convert = null;
		} else {
			$client2 = new HttpClient ();
			$client2->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
			
			$response2 = $this->getResponse ();
			$response2->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
			
			$client2->setUri ( $url );
			$result2 = $client2->send ();
			$body2 = $result2->getBody (); // content of the web
			
			$dom2 = new Query ( $body2 );
			
			$img_array = $dom2->execute ( '#content .content_chap img' );
			foreach ( $img_array as $key ) {
				$tmp_img = array ();
				$tmp_convert = array ();
				
				$tmp_img = $key->getAttribute ( 'src' );
				$array_img [] = $tmp_img;
				
				$sour_url = $key->getAttribute ( 'src' ); // $sour_url = 'http://file.diendanbaclieu.net/hinh/2013/10/hinh-anh-dep-ve-tinh-yeu-6.jpg';
				                                       // save img
				$dir_file = DIR_UPLOAD_NEW; // $dir_file.'/uploadnew';
				$folder = DIR_UPLOAD_NEW;
				$sour = pathinfo ( $sour_url );
				// Thư mục chứa ảnh
				if (file_exists ( $folder . '/' . $neo_chap . $sour ['basename'] )) {
					$dest = $folder . '/' . time () . '_' . $neo_chap . $sour ['basename'];
					$dest_img = time () . '_' . $neo_chap . $sour ['basename'];
				} else {
					$dest = $folder . '/' . $neo_chap . $sour ['basename'];
					$dest_img = $neo_chap . $sour ['basename'];
				}
				
				$tmp_convert = $dest_img;
				$array_img_convert [] = $tmp_convert;
				
				$this->save_img ( $sour_url, $dest );
			}
			
			return $array_img_convert;
		}
	}
	public function save_img($sour1, $dest1) {
		if (! file_put_contents ( $dest1, file_get_contents ( $sour1 ) )) {
			$dest = '';
		}
	}
	public function truyentranhtuancomAction() {
		$domain = "http://truyentranhtuan.com";
		$url_haivltv = "http://truyentranhtuan.com/cuu-dinh-ky/";
		$client = new HttpClient ();
		$client->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
		$response = $this->getResponse ();
		$response->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
		$client->setUri ( $url_haivltv );
		$result = $client->send ();
		$body = $result->getBody (); // content of the web
		                                              // echo $body;die;
		$dom = new Query ( $body );
		// $title = $dom->execute('.wrapper_sanpham .ten_truyen');
		// foreach($title as $key=>$r)
		// {
		// $aelement = $r->getElementsByTagName("a")->item(0);
		// if ($aelement->hasAttributes())
		// {
		// $nameapp = 'igirlxinhcom';
		// $title = $aelement->textContent;
		// $link = $url_haivltv;
		// }
		// }
		
		// //.wrapper_sanpham .content_noidung_tomtat
		// $content_detail_lop = $dom->execute('.wrapper_sanpham .content_noidung_tomtat');
		// foreach ($content_detail_lop as $keyhai => $valuehaivl)
		// {
		// $description = $this->innerHTML($valuehaivl);
		
		// }
		// get Thumbnail img
		$img = $dom->execute ( '#main-content #infor-box .manga-cover img' );
		// var_dump($img->count()); die;
		foreach ( $img as $keydo => $mainelemen ) {
			if ($mainelemen->hasAttributes ()) {
				$image_thumbnail = $mainelemen->getAttributeNode ( 'src' )->nodeValue;
			}
		}
		
		var_dump ( $image_thumbnail );
		
		// get Chap of story .wrapper_sanpham .last_chap_update
		$get_chap = $dom->execute ( '#main-content #manga-chapter .chapter-name' );
		// var_dump($get_chap->count());die;
		
		$number_loop_ = $get_chap->count ();
		$i = 0;
		$array = array ();
		foreach ( $get_chap as $key => $r ) {
			
			$aelement = $r->getElementsByTagName ( "a" )->item ( 0 );
			if ($aelement->hasAttributes ()) {
				$nameChapter [$i]->namechap = $aelement->textContent;
				$link_chap [$i]->link_chap = $aelement->getAttributeNode ( 'href' )->nodeValue;
				
				// $tmp = array();
				$tmp [$i]->namechap = $aelement->textContent;
				$tmp [$i]->link_chap = $aelement->getAttributeNode ( 'href' )->nodeValue;
				// $array[] = $tmp;
				$array = $tmp;
			}
			$i ++;
		}
		
		// // swap
		// $array = array();
		// foreach ($results as $result)
		// {
		// $tmp = array();
		// $tmp= $result;
		// $array[] = $tmp;
		// }
		
		$igirlxinhcom = array (
				// 'nameapp' =>$nameapp,
				// 'title' => $title,
				// 'link' =>$link,
				// 'description'=>$description,
				// 'numberchap'=>$number_loop_,
				// 'image_thumbnail' =>$image_thumbnail,
				// 'category'=>'',
				// 'items' => array (),
				'namechatpter' => array (),
				'link_chapter' => array () 
		);
		
		$igirlxinhcom ['namechatpter'] [] = $nameChapter;
		$igirlxinhcom ['link_chapter'] [] = $link_chap;
		
		echo 'namechatpter';
		echo '<pre>';
		print_r ( $array );
		echo '</pre>';
		// die;
		
		// get det tail
		
		// // GetConTentFull Deatil
		// // $i=0;
		// for($j=1;$j<$numberChap ;$j++)
		// {
		// $chap_look = "Chapter ".$j;
		// $get_img_link = $link_loop_real.$j.'.html';
		// // var_dump($get_img_link);
		// // die;
		// $content_detail_full = array();
		
		// $content_detail_full = $this->getContent_chap_img($get_img_link);
		// $igirlxinhcom['items'][] = array(
		// 'Chap'=>$chap_look,
		// 'detail'=>$content_detail_full,
		// );
		// // $i++;
		// break;
		// }
		
		// var_dump($array_explo);
		// echo '</br>';
		// var_dump($url_array);
		// echo 'loop</br>';
		// echo '<pre>';
		// print_r($pieces2);
		// echo '</pre>';
		// echo '</pr>';
		// var_dump($url_loop);
		
		// //die;
		// // $igirlxinhcom['items'][] = array(
		// // 'link_story'=>$link_story_start,
		// // );
		
		// // // save
		// // foreach ($haivltv as $keysave =>$valuehaivlSave)
		// // {
		// // $arry_tmp = array();
		// // $arry_tmp = (array)$valuehaivlSave;
		// // $rssget = New Rssget();
		// // $rssget->exchangeArray($arry_tmp);
		// // $this->getRssgetTable()->saveRssget($rssget);
		// // }
		
		echo 'igirlxinhcom';
		echo '<pre>';
		print_r ( $igirlxinhcom );
		echo '</pre>';
		var_dump ( $numberChap );
		
		die ();
		
		return new JsonModel ( array (
				'data' => $igirlxinhcom 
		) );
	}
	public function detailAction()
	{
	//public function truyentranhtuancomAction(){() 

		$clienttest = new HttpClient ();
		$clienttest->setAdapter ( 'Zend\Http\Client\Adapter\Curl' );
		
		$responsetest = $this->getResponse ();
		$responsetest->getHeaders ()->addHeaderLine ( 'content-type', 'text/html; charset=utf-8' ); // set content-type
		
		$clienttest->setUri ( 'http://truyentranhtuan.com/cuu-dinh-ky-chuong-5/' );
		$resulttest = $clienttest->send ();
		$bodytest = $resulttest->getBody (); // content of the web
		                                                      
		// var_dump(is_array($bodytest));
		                                                      // var_dump(is_string($bodytest));
		                                                      
		// $domhtml = new \simple_html_dom();
		                                                      // $html = $domhtml->loadFile($bodytest);
		                                                      // var_dump($html);
		                                                      
		// $string = '<script type="text/javascript">';
		                                                      // print strpos($bodytest, $string) . "\n";
		                                                      
		// die;
		                                                      
		// // echo $bodytest; die();
		                                                      // //echo $body2;
		                                                      // $domtest = new Query($bodytest); // return xml
		                                                      
		// //echo "<pre>";
		                                                      // echo ($domtest);
		                                                      // //echo '</pre>';
		                                                      // die;
		                                                      
		// //print_r($domtest);//die;
		                                                      // $img_array = $domtest->execute('.wrapper #viewer');
		                                                      
		// var_dump($img_array->count());
		                                                      // //var_dump($img_array->getXpathQuery());
		                                                      // // echo "<pre>";
		                                                      // // print_r($img_array);
		                                                      // // echo '</pre>';
		                                                      // die;
		
		$slides_page_path = '{"cards":["http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-014.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-006.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-005.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-013.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-016.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-018.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-008.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-022.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-011.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-023.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-015.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-012.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-021.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-010.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-004.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-002.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-007.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-019.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-017.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-001.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-024.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-020.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-003.jpg","http://truyentranhtuan.com/manga/cuu-dinh-ky-nhom-hamtruyen-com/5/cuu-dinh-ky-chap-5-trang-009.jpg"]}';
		$arr = json_decode ( $slides_page_path, TRUE );
		
		// $json_raw = '{"cards":
		// [{"id":100001,"name":"batman","image":11111,"size":75,"region_id":1,"locked":false,"status":"active","created_at":"2013-08-15T11:37:07Z"},
		// {"id":100002,"name":"superman","image":111111,"size":75,"region_id":1,"locked":false,"status":"active","created_at":"2013-08-15T12:30:09Z"},
		// {"id":100003,"name":"catwoman","image":1111111,"size":75,"region_id":1,"locked":false,"status":"active","created_at":"2013-08-15T12:39:42Z"},
		// {"id":100004,"name":"bane","image":1111111,"size":75,"region_id":1,"locked":false,"status":"active","created_at":"2013-09-08T12:56:04Z"}
		// ]}';
		
		// $arr = json_decode($json_raw, TRUE);
		
		// // ### getting the name of first man ###
		
		// $man1 = $arr['cards']['0']['name'];
		// if(isset($man1) && $man1 == 'batman') {
		// echo 'BATMAN RULEZ';
		// }
		
		// echo "\n\n\n"; // moar space
		// // ### in a loop ###
		
		for($i = 0; $i < count ( $arr ['cards'] ); $i ++) {
			echo $arr ['cards'] [$i] . "</br>";
		}
		
		die ();
		
		return new JsonModel ( array (
				'data' => $igirlxinhcom 
		) );
	}
	public function innerHTML($contentdiv) {
		$r = '';
		$elements = $contentdiv->childNodes;
		foreach ( $elements as $element ) {
			if ($element->nodeType == XML_TEXT_NODE) {
				$text = $element->nodeValue;
				// IIRC the next line was for working around a
				// WordPress bug
				// $text = str_replace( '<', '&lt;', $text );
				$r .= $text;
			}			// FIXME we should return comments as well
			elseif ($element->nodeType == XML_COMMENT_NODE) {
				$r .= '';
			} else {
				$r .= '<';
				$r .= $element->nodeName;
				if ($element->hasAttributes ()) {
					$attributes = $element->attributes;
					foreach ( $attributes as $attribute )
						$r .= " {$attribute->nodeName}='{$attribute->nodeValue}'";
				}
				$r .= '>';
				$r .= $this->innerHTML ( $element );
				$r .= "</{$element->nodeName}>";
			}
		}
		return $r;
	}
	public function get_data($url) {
		// return data: not html
		$ch = curl_init ();
		$timeout = 5;
		curl_setopt ( $ch, CURLOPT_URL, $url );
		// curl_setopt($ch, CURLOPT_POST, TRUE); // Use POST method
		// curl_setopt($ch, CURLOPT_POSTFIELDS, "var1=1&var2=2&var3=3"); // Define POST data values
		curl_setopt ( $ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 8.0; Windows NT 6.0)" );
		curl_setopt ( $ch, CURLOPT_RETURNTRANSFER, 1 );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYHOST, false );
		curl_setopt ( $ch, CURLOPT_SSL_VERIFYPEER, false );
		curl_setopt ( $ch, CURLOPT_MAXREDIRS, 10 );
		curl_setopt ( $ch, CURLOPT_FOLLOWLOCATION, 1 );
		curl_setopt ( $ch, CURLOPT_CONNECTTIMEOUT, $timeout );
		$data = curl_exec ( $ch );
		curl_close ( $ch );
		return $data;
	}
	
	/**
	 * Fetch the page source and cache it, ensuring it's saved as UTF-8
	 *
	 * @param string $url        	
	 * @return string
	 */
	public function fetch($url) {
		$content = '';
		$md5 = md5 ( $url );
		$path = __DIR__ . '/cache/' . $md5;
		if (! file_exists ( $path )) {
			$content = file_get_contents ( $url );
			$content = mb_convert_encoding ( $content, 'UTF-8', mb_detect_encoding($content, 'UTF-8, ISO-8859-1', true));
			file_put_contents($path, $content);
		} else {
			$content = file_get_contents($path);
		}
		return $content;
	}
	
	
	
	public function getIgirlxinhcomTable() {
		if (! $this->igirlxinhcomTable) {
			$sm = $this->getServiceLocator ();
			$this->igirlxinhcomTable = $sm->get ( 'Igirlxinhcom\Model\IgirlxinhcomTable' );
		}
		return $this->igirlxinhcomTable;
	}
}
