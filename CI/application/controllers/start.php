<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once '../ChromePhp.php';

class Start extends CI_Controller {
//    var $base;
//    var $css;
    
	function Start()
	{
	    parent::__construct();
	  $this->load->helper(array('form', 'url'));
	  
	  $this->load->library('form_validation');
//	    ChromePhp::log('here1');
//	    echo 'heree';
//	    $this->config->item('name_of_config_variable');
//	    $this->base = $this->config->item('base_url');
//	    $this->css = $this->config->item('css');

	}
	function hello()
	{

	    $this->load->view('testview');
	    //$this->load->view('welcome_message', $data);


	}
	function onday()
	{
		$value=$this->input->post();
	    ChromePhp::log($value);
	}

}
?>