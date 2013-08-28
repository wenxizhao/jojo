<?php
include_once '../ChromePhp.php';
class Build extends CI_Controller{
	public function __construct()
	{
		parent::__construct();
		$this->load->helper(array('form', 'url'));	  
	  	$this->load->library('form_validation');
		$this->load->model('build_model');
	}
	public function index()
	{
		$data['builds']=$this->build_model->get_buids();
		$data['title']='main page';
		$data['category']=$this->build_model->get_clist();
		$this->load->view('templates/header',$data);
		$this->load->view('build/index',$data);
		$this->load->view('templates/footer');
	}
	public function buildinfo($bid = NULL,$build_name = NULL)
	{
		if($bid==NULL || $build_name==NULL)
		{
			$build=$this->build_model->default_build();
			$bid=$build['id'];
			$build_name=$build['name'];
		}		
		$data['build_info']=$this->build_model->get_buildinfo($bid);
		$data['build_name']=$build_name;
		$data['build_id']=$bid;
		$data['title']=$build_name;
		$this->load->view('templates/header',$data);
		$this->load->view('build/buildinfo',$data);
		$this->load->view('templates/footer');
	}
	public function chart($pid=NULL)
	{
		$data['title']='chart';
		$data['pname']=$this->build_model->get_pname($pid);
		$data['value']=$this->build_model->get_performance($pid);
		$this->load->view('templates/header',$data);
		$this->load->view('build/chart',$data);
		$this->load->view('templates/footer');
	}
	public function detail($bid=NULL)
	{
		$data['title']='detail info';
		$data['bname']=$this->build_model->get_bname($bid);
		$data['value']=$this->build_model->get_totallyloaded($bid);
		$this->load->view('templates/header',$data);
		$this->load->view('build/detail',$data);
		$this->load->view('templates/footer');		
	}
	public function getp($c_id=NULL)
	{
		if($c_id==NUll)
		{
			$c_id=1;
		}
		$performance=$this->build_model->get_plist($c_id);
		echo json_encode($performance);
	}
	
	public function testinsert()
	{
		$source=trim($_POST["source"]);
		$category=$_POST["category"];
		$performance=$_POST["performance"];
		$version=$_POST["version"];
		$priority=$_POST["priority"];
		if($version=='cn'||$version=='en')
		{
			$feedback=$this->build_model->test_storage($source,$category,$performance,$version,$priority);
			if($feedback){
				echo 'ok!';
			}
			else{
				echo 'error';
			}
		}
		else{
			echo 'version error';
		}		
	}

}
?>