<?php
 if (!defined('BASEPATH')) exit('No direct script access allowed');
 
 /**
  * View Class
  *
  * @author Alexander Makarov
  * @copyright 2008
  * @version 0.9
  * @uses PHP5
  * @link http://rmcreative.ru/
  */
 class View {
     private $layoutVars = array();
 
     private $vars = array();
     private $layout = 'layout';
	 private $view_path = '';
     private $title = 'Traderroom Trendoks.com';
 
     function setLayout($template){
         $this->layout = $template;
		 return $this;
     }
 
     function setTitle($title){
         $this->title = $title;
		 return $this;
     }
	 
	 function setViewPath($view_path){
         $this->view_path = $view_path;
		 return $this;
     }
 
     function set($varName, $value=""){
     	if(is_array($varName))$this->vars = array_merge($this->vars,$varName);
		 else $this->vars[$varName] = $value;
		 return $this;
     }
	 function get($varName){
     	if(isset($this->vars[$varName]))return $this->vars[$varName];
		else if(isset($this->layoutVars[$varName]))return $this->layoutVars[$varName];
		return false;
     }
 
     function setGlobal($varName, $value){
        if(is_array($varName))$this->layoutVars = array_merge($this->layoutVars,$varName);
		 else $this->layoutVars[$varName] = $value;
		 return $this;
     }
 
     /**
      * Fetch template and return it.
      *
      * @param String $template
      */
     function fetch($template,$data=false){
         /* @var CI CI_Base */
         $CI = &get_instance();
 		
		 if(is_array($data))$data = array_merge($this->vars,$data);
		 else $data = $this->vars;
		 
         $content = $CI->load->view($this->view_path.$template, $data, true);
 
         $this->layoutVars['content'] = $content;
         $this->layoutVars['title'] = $this->title;
 
         return $CI->load->view($this->view_path.$this->layout, $this->layoutVars, true);
     }

     function fetchPartial($template,$data=false){
         /* @var CI CI_Base */
         $CI = &get_instance();
 		
		 if(is_array($data))$data = array_merge($this->vars,$data);
		 else $data = $this->vars;
		
         return $CI->load->view($this->view_path.$template, $data, true);
     }

     function renderPartial($template){
         header('Content-type: text/html; charset=UTF-8');
		 echo $this->fetchPartial($template);
     }
	 
	 function ajaxRender($data){
         global $compressor;
		 header('Content-type: text/html; charset=UTF-8');
		 if(is_array($data))$data = json_encode($data);
		 echo $data;
		 if(isset($compressor))$compressor->finish();
		 exit();
     }
 
     /**
      * Renders template to $content.
      *
      * @param String $template
      */
     function render($template){
               
			   header('Content-type: text/html; charset=UTF-8');
			   echo $this->fetch($template);
     }
 }