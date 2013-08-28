<?php
include_once '../ChromePhp.php';
class Build_model extends CI_Model{
  public function __construct()
  {
    $this->load->database();
//    $query = $this->db->get('t_build');
//  	ChromePhp::log($query->result_array());
  }
  public function get_buids()
  {	
  	$sql="select * from t_build where company='dolphin' order by date DESC";
  	$query = $this->db->query($sql);
    if($query->num_rows()>0)
    {
  		return $query->result_array();
    }
    return NULL;
  }
  
  public function get_buildinfo($buildid)
  {
  	/*query uc bid_uc*/
  	$sql="select date from t_build where id='$buildid'";
  	$query=$this->db->query($sql);
  	$row=$query->row();
  	$time=$row->date;
  	$sql="select id from t_build where date>='$time' and company='uc'";
  	$query=$this->db->query($sql);
  	$row=$query->row();
  	$bid_uc=$row->id;
  	
  	/*query dolphin every performance value*/
  	$query_category=$this->db->get('t_category');
  	foreach ($query_category->result_array() as $category)
  	{
  		$c_id=$category['id'];
  		$data[$c_id]['c_name']=$category['name'];
  		$sql="select id,name from t_performance where categoryID='$c_id'";
  		$query_p=$this->db->query($sql);
  		foreach ($query_p->result_array() as $performance)
  		{
  			$pid=$performance['id'];
  			$data[$c_id][$pid]['pid']=$performance['id'];
  			$data[$c_id][$pid]['name']=$performance['name'];
  			$sql="select value,denomination from t_results where pid='$pid' and bid='$buildid'";
  			$sql_uc="select value,denomination from t_results where pid='$pid' and bid='$bid_uc'";
  			$result=$this->db->query($sql);
  			$result_uc=$this->db->query($sql_uc);
	  		if ($result->num_rows()>0)
	  		{
	  			foreach ($result->result_array() as $row)
	  			{
	  				$data[$c_id][$pid]['dolphin']=$row['value'];
	  				$data[$c_id][$pid]['denomination']=$row['denomination'];
	  			}
	  		}
  			if ($result_uc->num_rows()>0)
	  		{
	  			foreach ($result_uc->result_array() as $row_uc)
	  			{
	  				$data[$c_id][$pid]['uc']=$row_uc['value'];
	  				$data[$c_id][$pid]['denomination']=$row_uc['denomination'];
	  			}
	  		}
  		}
  	}
  	$data['valid']=TRUE;
	return $data;
  }
  
  public function default_build(){
	$sql="select * from t_build where company='dolphin' order by date DESC limit 1";
	$query=$this->db->query($sql);
	$row=$query->row();
	$date['id']=$row->id;
	$date['name']=$row->name;
	return $date;
  }
  
  public function get_performance($pid)
  {
  	/*查找build id
  	 * select b1.id,b1.date,b1.apkdir,b1.name,b1.company from(select name,max(date)as maxdate from t_build group by name)as b2 inner join t_build as b1 on b1.name=b2.name and b1.date=b2.maxdate where company='dolphin';
  	 * */
  	$sql_dolphin="select r.value,b.date from t_results as r inner join t_build as b on r.bid=b.id where company='dolphin' and pid='$pid' order by date ASC; ";
  	$sql_uc="select r.value,b.date from t_results as r inner join t_build as b on r.bid=b.id where company='uc' and pid='$pid' order by date ASC; ";
	$query_dolphin=$this->db->query($sql_dolphin);
	$data=array();
	if($query_dolphin->num_rows()>0)
	{
		foreach ($query_dolphin->result_array() as $row_dolhpin)
		{
			$time=$row_dolhpin['date'];
			$arr=array('dolphin'=>$row_dolhpin['value'],'uc'=>0);
			$data[$time]=$arr;
		}
	}
	$query_uc=$this->db->query($sql_uc);
  	if($query_uc->num_rows()>0)
	{
		foreach ($query_uc->result_array() as $row_uc)
		{
			if(array_key_exists($row_uc['date'], $data))
			{
				$time=$row_uc['date'];
				$data[$time]['uc']=$row_uc['value'];
			}
			else
			{
				$arr=array('dolphin'=>0,'uc'=>$row_uc['value']);
				$time=$row_uc['date'];
				$data[$time]=$arr;
			}		
		}
	}

	ChromePhp::log($data);
	return $data;  	
  }
  
  public function get_totallyloaded($bid)
  {
  	//get name,datetime of dolphin and uc
  	$sql="select date_format(date,'%Y-%m-%d') as formatdate,date,name from t_build where id='$bid'";
  	$query=$this->db->query($sql);
  	$row=$query->row();
  	$dolphin_name=$row->name;
  	$time=$row->formatdate;
  	$dolphin_time=$row->date;
//  	ChromePhp::log($dolphin_name);
//  	ChromePhp::log($time);
//  	ChromePhp::log($dolphin_time);
  	$sql="select max(date) as date from t_build where date(date)='$time' and company='uc'";
  	$query=$this->db->query($sql);
  	$row=$query->row();
  	$uc_time=$row->date;
  	$sql="select name from t_build where date='$uc_time' and company='uc'";
  	$query=$this->db->query($sql);
  	$row=$query->row();
  	$uc_name=$row->name;
  	  	ChromePhp::log($uc_name);
//  	ChromePhp::log($uc_time);
  	//query t_totallyloaded:dolphin
  	$sql="select url,seconds from t_totallyloaded where name='$dolphin_name' and date='$dolphin_time'";
//  	ChromePhp::log($sql);
  	$query=$this->db->query($sql);
  	$date=array();
 // 	ChromePhp::log($query->result_array());
  	foreach ($query->result_array() as $row)
	{
		$value=array('dolphin'=>$row['seconds'],'uc'=>0);
		$date[$row['url']]=$value;
	}


	//query t_totallyloaded:uc
	$sql="select url,seconds from t_totallyloaded where name='$uc_name' and date='$uc_time'";
	$query=$this->db->query($sql);
	foreach ($query->result_array() as $row)
	{
		if(array_key_exists($row['url'], $date))
		{
//			ChromePhp::log('yes');
			$url=$row['url'];
			$date[$url]['uc']=$row['seconds'];
		}
		else
		{
			ChromePhp::log('no');
			$value=array('dolphin'=>0,'uc'=>$row['seconds']);
			$url=$row['url'];
			$date[$url]=$value;
			//$temp=array($row['url']=>$value);
			//array_push($date, $temp);
		}		
	}
//				ChromePhp::log($date);
	return $date;	
	
  }
  
  public function get_clist()
  {
  	$sql="select id,name from t_category";
  	$query=$this->db->query($sql);
  	$data=array();
  	foreach ($query->result_array() as $row)
  	{
  		$temp=array('id'=>$row['id'],'name'=>$row['name']);
  		array_push($data,$temp);
  	}
  	ChromePhp::log($data);
  	return $data;
  }
  public function get_plist($c_id)
  {
  	ChromePhp::log($c_id);
  	$sql="select id,name from t_performance where categoryID='$c_id'";
  	$query=$this->db->query($sql);
  	$data=array();
  	foreach ($query->result_array() as $row)
  	{
  		$pid=$row['id'];
  		$data[$pid]=$row['name'];
  	}
  	ChromePhp::log($data);
  	return $data;
  }
  public function get_pname($pid)
  {
  	$sql="select name from t_performance where id='$pid'";
  	$query=$this->db->query($sql);
  	$row=$query->row();
  	$pname=$row->name;
  	return $pname;
  }
  public function get_bname($bid)
  {
  	$sql="select name from t_build where id='$bid'";
  	$query=$this->db->query($sql);
  	$row=$query->row();
  	$bname=$row->name;
  	return $bname;
  }
  public function test_storage($source,$category,$performance,$version,$priority)
  {
  	$sql="insert into t_apk(src,pid,version,priority,status) value('$source','$performance','$version','$priority','0')";
  	$query=$this->db->query($sql);
  	return $query;
  }
  
}
?>