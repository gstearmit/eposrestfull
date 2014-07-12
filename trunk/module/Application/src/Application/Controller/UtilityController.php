<?php

namespace Application\Controller;

use Zend\Mvc\Controller\AbstractActionController;
use Zend\View\Model\ViewModel;

use Application\Model\Acl;
//use Zend\Permissions\Acl\Acl;
use Zend\Permissions\Acl\Role\GenericRole as Role;
use Zend\Permissions\Acl\Resource\GenericResource as Resource;

class UtilityController extends AbstractActionController
{
	
	
	
	
	
	public function sendMail($mailfrom, $fromname=null,$emailTo,$subject,$body,$altBody=null,$ccList=null, $bccList=null,$replyTo=null,$fileAttach=null)
	{
		//include("PHPMailer/class.smtp.php"); // optional, gets called from within class.phpmailer.php if not already loaded
		$mail             = new PHPMailer();
		$mail->IsSMTP(); // telling the class to use SMTP
		$mail->Host       = "mail.yourdomain.com"; // SMTP server
		$mail->SMTPDebug  = SMTP_DEBUG_MOD;        // enables SMTP debug information (for testing)
		// 1 = errors and messages
		// 2 = messages only
		$mail->SMTPAuth   = true;                  // enable SMTP authentication
		$mail->SMTPSecure = "ssl";                 // sets the prefix to the servier
		$mail->Host       = SMTP_SERVER;      		// sets GMAIL as the SMTP server
		$mail->Port       = 465;                   // set the SMTP port for the GMAIL server
		$mail->Username   = GMAIL_USERNAME;  	   // GMAIL username
		$mail->Password   = GMAIL_PASSWORD;        // GMAIL password
	
		if(!$fromname) $fromname = ADMIN_NAME;
		$mail->SetFrom($mailfrom,$fromname);
		if(is_array($replyTo) && sizeof($replyTo) >0) $mail->AddReplyTo($replyTo['mail'],$replyTo['name']);
		$mail->Subject    = $subject;
		if($altBody) $mail->AltBody = $altBody;
		$mail->MsgHTML($body);
		$environment = SERVER_ENVIRONMENT;
		if($environment != 'localhost'){
			$mail->AddAddress($emailTo);
			$ccList = explode(',',$ccList);
			$bccList = explode(',',$bccList);
			foreach((array)$ccList as $cc){
				if($cc){
					$mail->AddCC($cc);
				}
			}
			foreach((array)$bccList as $bcc){
				if($bcc){
					$mail->AddBCC($bcc);
				}
			}
		}else{
			$mail->AddAddress(EMAIL_TEST);
		}
	
		$return = array();
		$mailInfo = $mailfrom.$fromname.$emailTo.$subject.$body;
		//if($_SESSION['mailInfo'] != $mailInfo  && (int)@$_SESSION['timeSend'] + WAITING_TIME_FOR_SEND_NEXT_EMAIL < time()){
		//$_SESSION['mailInfo'] = $mailInfo;
		//$_SESSION['timeSend'] = time();
		if(!$mail->Send()) {
			//echo "Mailer Error: " . $mail->ErrorInfo;
			$return['errorCode'] = 1;
			$return['errorMessage'] = $mail->ErrorInfo;
			//} else {
			//$return['errorCode'] = 0;
			//$return['errorMessage'] = 'Mail has been sent';
			//}
		}else{
			$return['errorCode'] = 0;
			$return['errorMessage'] = 'Mail has been sent';
		}
		return $return;
	}
	
	function readFiles($dir)
	{
		$fileList = array();
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					$file = $dir."/".$object;
					if(!in_array($file,$fileList)) $fileList[] = $file;
				}
			}
			reset($objects);
		}
		return $fileList;
	}
	
	function deleteFolder($dir) 
	{
		if (is_dir($dir)) {
			$objects = scandir($dir);
			foreach ($objects as $object) {
				if ($object != "." && $object != "..") {
					if (filetype($dir."/".$object) == "dir") deleteFolder($dir."/".$object); else unlink($dir."/".$object);
				}
			}
			reset($objects);
			rmdir($dir);
		}
	}
	
	function deleteFile($filePath)
	{
		@unlink($filePath);
	}
	
	function createFolder($path,$folderName,$mode='0777')
	{
		$folderPath = $path.$folderName;
		mkdir($folderPath);
		chmod($folderPath, $mode);
	}
	
	
	function mb_ucfirst($str) 
	{
		$fc = mb_strtoupper(mb_substr($str, 0, 1));
		return $fc.mb_substr($str, 1);
	}
	
	function unzipFile($path,$file)
	{
		$filePath = $path.$file;
		$zip = new ZipArchive;
		$res = $zip->open($filePath);
		if ($res === TRUE) {
			$zip->extractTo($path);
			$zip->close();
			return true;
		} else {
			return false;
	      }
	
	}
	
	
	Static function formatDateTime($dateTime){
		if($dateTime && $dateTime != '0000-00-00 00:00:00' && $dateTime != '1970-01-01 00:00:00'){
			list($date,$time) = explode(" ", $dateTime);
			list($year,$month,$day) = explode("-", $date);
			return $day."/".$month."/".$year;
		}
	}
	
	function formatDate($date,$showNow=true)
	{
		$date = trim($date);
		if (!$date && $showNow) {
			$date = 'now';
		}
		if($date){
			return date('d/m/Y', strtotime($date));
		}else{
			return '';
		}
	}
	
	function parseDate($strDate)
	{
		$strDate = trim($strDate);
		if($strDate){
			return date('d/m/Y', $strDate);
		}else{
			return '';
		}
	}
	
	function normalDate($dateTime){
		if($dateTime != '0000-00-00 00:00:00'){
			return date('d.m.Y', strtotime($dateTime));
		}else{
			return null;
		}
	}
	
	function reportDate($dateTime){
		return date('d.m', strtotime($dateTime));
	}
	
// 		if (!$logFile) {
// 			$logFile = $_SERVER['SERVER_NAME'].date(' Y-m-d');
// 		}
// 		return error_log(date('[Y-m-d H:i:s] ').$message."\n", 3, JPATH_ROOT.'/logs/'.$logFile);
// 	}
	
	function logImport($message)
	{
		return Utility::log($message, self::LOG_IMPORT_FILE);
	}
	
	function readLogImport()
	{
		return file_get_contents(JPATH_ROOT.'/logs/'.self::LOG_IMPORT_FILE);
	}
	
	function rotateLogImport()
	{
		$logFile = JPATH_ROOT.'/logs/'.self::LOG_IMPORT_FILE;
		return rename($logFile, $logFile.'-'.date('Y-m-d H:i:s'));
	}
	
	function invalidateEmails($emails)
	{
		list($email_user) = explode('@', $_SERVER['SERVER_ADMIN']);
		$suffix = '...xpc_com...'.$email_user;
		$emails = (array)$emails;
		foreach ($emails as &$email) {
			$email .= $suffix;
		}
		return $emails;
	}
	
	function getAge($birthday)
	{
		list($bdate, $bmonth, $byear) = explode('.', $birthday);
		list($date , $month , $year ) = explode('.', date('d.m.Y'));
		if($byear < date('Y') && $byear >0){
			$age = $year - $byear;
			if ($bmonth.$bdate > $month.$date) {
				$age--;
			}
			return $age;
		}else{
			return "";
		}
	}
	
	
	public static function getEmails($contacts) {
		$emails = array();
		if (count($contacts)) {
			foreach($contacts as $contact) {
				$emails[] = $contact->contactEmail;
			}
		}
		return $emails;
	}
	
	public function cutstring($str, $maxlength = 30, $strip_tag = true){
		if ($strip_tag) {
			$str = strip_tags($str);
		}
		if (strlen($str) > $maxlength){
			return $this->substr($str, 0 , $maxlength).'...';
		}else{
			return $str;
		}
	}
	
	/*
	 * cut a string if too long, add a tooltip after cutting
	*
	* @param: string, max lenght
	* @return string
	* 18-4-09
	* */
	public function tooltipString($str, $maxlength = 30, $strip_tag = true){
		if ($strip_tag) {
			$str = strip_tags($str);
		}
		if (strlen($str) > $maxlength){
			return "<span title='$str'>".$this->subString($str,$maxlength).'</span>';
		}else{
		return $str;
		}
	}
	
	function subString($str, $len, $charset='UTF-8'){
			$str = html_entity_decode($str, ENT_QUOTES, $charset);
			if(mb_strlen($str, $charset)> $len){
			$arr = explode(' ', $str);
			$str = mb_substr($str, 0, $len, $charset);
			$arrRes = explode(' ', $str);
			$last = $arr[count($arrRes)-1];
			unset($arr);
			if(strcasecmp($arrRes[count($arrRes)-1], $last)){
			unset($arrRes[count($arrRes)-1]);
			}
			return implode(' ', $arrRes)."...";
	}
					return $str;
	}
	
	function getDays($defaultVal=null,$blankVal = null,$blankText=null,$comboboxName = null){
		if(!$comboboxName) $comboboxName = 'day';
		$comboxDays .= '<select id="'.$comboboxName.'" name="'.$comboboxName.'">';
		if($blankVal){
			$comboxDays .= '<option value="'.$blankVal.'">'.$blankText.'</option>';
		}
		for($i=1;$i<=31;$i++){
			if($defaultVal == $i){
				$selected = 'selected';
			}else{
				$selected = null;
			}
			if($i < 10) $i = '0'.$i;
	
			$comboxDays .= '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
		}
		$comboxDays .= '</select>';
		return $comboxDays;
	}
	
	function getMonths($defaultVal=null,$blankVal = null,$blankText=null,$comboboxName = null){
		if(!$comboboxName) $comboboxName = 'month';
		$comboxMonths .= '<select id="'.$comboboxName.'" name="'.$comboboxName.'">';
		if($blankVal){
			$comboxMonths .= '<option value="'.$blankVal.'">'.$blankText.'</option>';
		}
		for($i=1;$i<=12;$i++){
			if($defaultVal == $i){
				$selected = 'selected';
			}else{
				$selected = null;
			}
			if($i < 10) $i = '0'.$i;
			$comboxMonths .= '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
		}
		$comboxMonths .= '</select>';
		return $comboxMonths;
	}
	
	function getYears($defaultVal=null,$blankVal = null,$blankText=null,$comboboxName = null,$from=2010,$to=2015){
		if(!$comboboxName) $comboboxName = 'years';
		$comboxYears .= '<select id="'.$comboboxName.'" name="'.$comboboxName.'">';
		if($blankVal){
			$comboxYears .= '<option value="'.$blankVal.'">'.$blankText.'</option>';
		}
		for($i=$from;$i<=$to;$i++){
			if($defaultVal == $i){
				$selected = 'selected';
			}else{
				$selected = null;
			}
			$comboxYears .= '<option '.$selected.' value="'.$i.'">'.$i.'</option>';
		}
		$comboxYears .= '</select>';
		return $comboxYears;
	}
	
	function getDateValid($day,$month,$year,$format='dd/mm/yyyy'){
		$day = (int)$day;
		$month = (int)$month;
		$year = $year;
		if($day && $month && $year){
			if(checkdate($month,$day,$year)){
				switch($format){
					case 'dd/mm/yyyy':
						return $day.'-'.$month.'-'.$year;
						break;
					default:
						return $day.'-'.$month.'-'.$year;
						break;
				}
			}else{
				return false;
			}
		}else{
			return false;
		}
	
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
}
