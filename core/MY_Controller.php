<?php if (!defined('BASEPATH')) exit('No direct script access allowed');

class MY_Controller extends CI_Controller {
	
	public $support_email = '';
	
	function __construct() {
		
		parent::__construct();
		parse_str($_SERVER['QUERY_STRING'], $_GET);
		$this->setLang();
		$this->load->model('Common_model');
		
		$this->load->library('user_agent');
		if($this->is_mobile())
		{	
			if($this->uri->segment(1)!='mobile')redirect('/mobile/');
			$this->config->set_item('base_url', $this->config->item('base_url').'mobile/');
			$this->view->setViewPath('mobile/');
		}
		
//		if($this->tank_auth->is_logged_in())$this->view->set('userdata', $this->users->get_user_by_id($this->tank_auth->get_user_id(), true));
		
		
	}
	
	function is_mobile()
	{
		return false;
		//return $this->agent->is_mobile('iphone') || $this->agent->is_browser('Safari');
	}
	
	public function get_template($template_type, $template_identifier, $data=false)
	{
		/**
		* $template_type - SMS OR EMAIL
		* $template_name - TEMPLATE NAME
		* RETURN ARRAY: id, subject, text
		*/
		$template_array = $this->Common_model->get_template($template_type, $template_identifier);
		if(is_array($template_array) && !empty($template_array))
		{
			$template_array = $template_array[0];
			
			if(is_array($data))
				foreach($data as $key=>$value) 
					foreach($template_array as $k=>$v)$template_array[$k] = str_replace('{%'.$key.'%}',$value,$v);
			
			return $template_array;
		}
		else
		{
			return FALSE;
		}
		
	}
	
	public function send_sms($client_mobile, $sms_text)
	{
		$send_sms_array = array();
		$send_sms_array['status'] = false;
		if($this->tank_auth->is_logged_in()) { $id_trader = $this->tank_auth->get_user_id(); } else { $id_trader = 0; }
		
		// Кодируем текст смс для его отправки
		$sms_text = urlencode($sms_text);
		
		$url = CLICKATELL_BASEURL."/http/auth?user=".CLICKATELL_USER."&password=".CLICKATELL_PASS."&api_id=".CLICKATELL_API_ID."&text=$sms_text&concat=3";
	
		// do auth call
		$ret = file($url);
		
		// split our response. return string is on first line of the data returned
		$sess = explode(":",$ret[0]);
		if ($sess[0] == "OK")
		{
			$sess_id = trim($sess[1]); // remove any whitespace
			$url = CLICKATELL_BASEURL."/http/sendmsg?session_id=$sess_id&to=$client_mobile&from=".CLICKATELL_FROM."&text=$sms_text&concat=3";
	
			// do sendmsg call
			$ret = file($url);
			$send = explode(":",$ret[0]);
			
			if($send[0]=='ERR')
			{
				$send_sms_array['msg'] = trim($send[1]);
				$this->Common_model->set_sms_log($id_trader,$client_mobile, '', $send_sms_array['msg']);
			}
			else 
			{
				$send_sms_array['status'] = true;
				$this->Common_model->set_sms_log($id_trader,$client_mobile, trim($send[1]),'');
			}
	
		}
		return ($send_sms_array);	
	}

	
	function send_email($email_from, $email_to='', $subject='', $text='', $email_cc = false, $email_bcc = false )
	{
		$this->load->library('email');
		$this->email->clear();
		if(is_array($email_from) && isset($email_from['type']))
		{
			$a = $email_from;
			if(isset($a['from']) && isset($a['fromName'])){
				$this->email->from($a['from'], $a['fromName']);
				$this->email->reply_to($a['from'], $a['fromName']);
			}
			else{
				$this->email->from($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
				$this->email->reply_to($this->config->item('webmaster_email', 'tank_auth'), $this->config->item('website_name', 'tank_auth'));
			}
			
			
			$this->email->to($a['to']);
			if(isset($a['templateFrom']) && $a['templateFrom']=='db')
			{
				$mail = $this->Common_model->get_template(1,$a['type'],lang());
				$subject = stripslashes($mail[0]['template_subject']);
				$text = stripslashes($mail[0]['template_text']);
				if(isset($a['data']) && is_array($a['data']))
				{
					foreach($a['data'] as $key=>$value)
					{
						$text = str_replace('{%'.$key.'%}',$value,$text);
						$text = str_replace('{'.$key.'}',$value,$text);
						
						$subject = str_replace('{%'.$key.'%}',$value,$subject);
						$subject = str_replace('{'.$key.'}',$value,$subject);
					}
				}
				
				if(isset($a['subject']))$subject=$a['subject'];
				if($subject=='' || $subject==NULL)$subject = lang('auth_subject_'.$a['type']);
				$subject = sprintf($subject,$this->config->item('website_name', 'tank_auth'));
				
				$this->email->subject($subject);
				$this->email->message($text);
			}
			else{
				if(isset($a['subject']))$this->email->subject($a['subject']);
				else $this->email->subject(sprintf($this->lang->line('auth_subject_'.$a['type']), $this->config->item('website_name', 'tank_auth')));
				
				$this->email->message($this->load->view('email/'.$a['type'].'-html', $a['data'], TRUE));
			}
		}
		else
		{
			$this->email->from($email_from);
			$this->email->reply_to($email_from);
			$this->email->to($email_to);
			if($email_cc) { $this->email->cc($email_cc); }
			if($email_bcc) { $this->email->bcc($email_bcc); } 
			$this->email->subject($subject);
			$this->email->message($text);
		}
		//$this->email->bcc('faiq@forex-az.net');
		return $this->email->send();
	}
	
	function action_log($id_action_log_type, $action_on_id = 0)
	{
		if($this->tank_auth->is_logged_in()) { $id_user = $this->tank_auth->get_user_id(); } else { $id_user = 0; }
		
		if($this->Common_model->set_action_log($id_action_log_type, $id_user, 0, $action_on_id))
		{
			return true;
		}
	}
	
function sendPosnetRequest($amount, $ccno, $cvc, $expDate, $id_unique)
{
	// currencyCode = US
	// currencyCode = YT
	$amount = str_replace(".","",$this->strToFloat($amount));
	$params = "xmldata=".
			"<posnetRequest>".
				"<mid>6700123687</mid>".
				"<tid>67077507</tid>".
				"<sale>".
					"<amount>".$amount."</amount>".
					"<ccno>".$ccno."</ccno>".
					"<currencyCode>YT</currencyCode>".
					"<cvc>".$cvc."</cvc>".
					"<expDate>".$expDate."</expDate>".
					"<orderID>".$id_unique."</orderID>".
				"</sale>".
			"</posnetRequest>";
			
			//echo '<pre>';
			//print_r($params);
			//echo '</pre>';
	
   
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_POST,1);
   curl_setopt($ch, CURLOPT_POSTFIELDS,$params);
   curl_setopt($ch, CURLOPT_URL,'https://forex-az.com.tr/tools/ykb.php');
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST,  FALSE);
   curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/4.0 (compatible; MSIE 5.01; Windows NT 5.0)");
   curl_setopt($ch, CURLOPT_RETURNTRANSFER,1);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE); 
   curl_setopt($ch, CURLOPT_TIMEOUT, 60);

   $result=curl_exec ($ch);
   if (curl_errno($ch)) 
       echo(curl_errno($ch)." - ".curl_error($ch));
   curl_close ($ch);
   
   $xml =  @$this->parseXML($result);
  
   return $xml;
}

private function parseXML($response_string)
{
	$pos=strpos($response_string, 'encoding');
	if($pos===false)$response_string="<?xml version=\"1.0\" encoding=\"utf-8\"?>\n".$response_string;
	$xml = new SimpleXMLElement($response_string);
	if (is_a($xml, 'SimpleXMLElement'))return $xml;
	else return FALSE;
}

private function strToFloat($value)
{
	$len=strlen($value);
	$pos=strpos($value, '.');
	
	if($pos===false)
	{
		$value=$value.'.00';
	}
	else
	{
		$dizi = explode('.',$value);
		$k=strlen($dizi[1]);
		if($k==1)$value = $dizi[0].'.'.$dizi[1].'0';
		else if($k > 2)
		{
			$dizi[1]=substr($dizi[1],0,2);
			$value = $dizi[0].'.'.$dizi[1];
		}else $value = $dizi[0].'.'.$dizi[1];
	}
	return $value;
}
private function setLang()
{
	if($this->input->get('lang'))$lang = $this->input->get('lang',true);
	else if($this->input->cookie('language'))$lang = $this->input->cookie('language',true);
	else $lang = 'en';
	
	if(!in_array($lang,array('en','tr','fr','ru')))$lang = 'en';
	
	
	$this->input->set_cookie(array('name'   => 'language',
		'value'  => $lang,
		'expire' => '86500',
	   // 'domain' => '.trendoks.com',
		'path'   => '/',
		'prefix' => '',
		'secure' => TRUE)
	);
	
	$this->config->set_item('language', $lang);
	$this->lang->load('interface', 'en');
	$this->lang->is_loaded = array();
	$this->lang->load('interface', $lang);
	$this->lang->load('tank_auth', $lang);
	$this->title($this->lang->line('site_title'));
}
	
public function title($value=false,$return=false)
{
	if($value)array_push($this->title_array,$value);
	
	if($return || !$value)return implode($this->lang->line('title_separator'),$this->title_array);
}	
private $title_array = array();
}
/* End of file My_Controller.php */
/* Location: ./application/libraries/My_Controller.php */