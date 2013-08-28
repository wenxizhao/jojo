<?php
include '../ChromePhp.php';
class Pages extends CI_Controller{
public function __construct()
	{
echo 'here1';
		ChromePhp::log('here1');
		parent::__construct();
		$this->load->model('news_model');
		echo 'here2';
		ChromePhp::log('here2');
	}
	public function view($page='home')
	{
		ChromePhp::log('here');
		if(!file_exists('application/views/pages/'.$page.'.php'))
		{
			show_404();
		}
		$date['title']=ucfirst($page);
		$this->load->view('templates/header',$date);
		$this->load->view('pages/'.$page,$date);
		$this->load->view('templates/footer',$date);
	}
}
