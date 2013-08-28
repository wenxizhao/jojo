<body id="dt_example">
<script type="text/javascript">
function validate_required(field,alerttxt)
{
	with (field)
  	{
  	if (value==null||value=="")
    	{alert(alerttxt);return false}
  	else {return true}
  	}
}
function validate_value(field,alerttxt)
{
	with(field)
	{
		if(value==null||value=="0")
		{alert(alerttxt);return false}
		else {return true}
	}
}
function validate_form(thisform)
{
	with (thisform)
  	{
  		if (validate_required(source,"Source must be filled out!")==false)
    	{source.focus();return false}
    	if(validate_value(category,"please select (sub) perfromance")==false)
    	{category.focus();return false}
    	if(validate_value(performance,"please select (sub) perfromance")==false)
    	{category.focus();return false}
    	return true;
  	}
}
$(document).ready(function(){
	/*begin-点击build_list的链接，显示build对应的测试结果*/
	$(".build_list").click(function(event){
		event.preventDefault();
		var url=$(this).attr("href");
		//begin-获取url对应的页面的内容，并改变<div>元素的内容			
		$.get(url,function(data){		
			var content=$(data).ready(function(){
				return $(this).find(".content");
				});	
			$(".buildinfo").html(content);		
			});
		//end-获取url对应的页面的内容，并改变<div>元素的内容
		});

	//<select name="category"></select>
	var x=document.getElementById('category');
	<?php foreach ($category as $key=>$val):?>
		var y=document.createElement('option');
		y.text='<?php echo $val['name']?>';
		y.value='<?php echo $val['id']?>'
		try
		{
			x.add(y,null);// standards compliant
		}
		catch(ex)
		{
			x.add(y);// IE only
		}
	<?php endforeach;?>
	//<select name="performance"></select>
	$('#category').change(function(){
		$('#performance').html("");
		
		var cid=this.value;
		var url="<?php echo base_url().'index.php/build/getp/'?>"+cid;
		$.post(url,function(data){
			var item
			var x=document.getElementById('performance');
			for(item in data){	
					//console.log(item);
					var y=document.createElement('option');
					y.text=data[item]
					y.value=item
					try
					{
						x.add(y,null);// standards compliant
					}
					catch(ex)
					{
						x.add(y);// IE only
					}
				}
			},"json");
	});
	//post form by ajax
	$("form").submit(function(e){
		//prevent default action
		e.preventDefault();
		//check valid
		if(validate_form(this))
		{
			var url=this.action;
			var version;
			var ecn=this.elements[4];
			var een=this.elements[5];
			if(ecn.checked)
			{
				version=ecn.value;
			}
			else if(een.checked){
				version=een.value;
			}
			var value={"source":this.elements[0].value,
					"priority":this.elements[1].value,
					"category":this.elements[2].value,
					"performance":this.elements[3].value,
					"version":version};
			$.post(url,value,function(data){
				$("#feedback").html(data);
				});
		}
	});
	
});
</script>
<div id="container">
    <div class="full_width big">Builds List</div>
    <div class="demo_links">
	    <div id="test_show">
		     <h1>All Versions</h1>    
		   	 <?php foreach ($builds as $build_item):?>
			    <ul>
			    	<li>
				    	<a class="build_list" href="<?php echo base_url().'index.php/build/buildinfo/'.$build_item['id'].'/'.$build_item['name'];?>" target="_blank">
						<?php echo $build_item['name'].' '.$build_item['apkdir'].' '.$build_item['date'];?>
						</a>
			    	</li>
			    </ul>
			<?php endforeach;?>
		</div>
		
		<div id="require_form">
			<h1>Require Autotest</h1>    
			<form action="<?php echo base_url().'index.php/build/testinsert/'?>" method="post" name="autotest">
			Apk source
			<input type="text" name="source" value="http://10.2.6.254/buildbot/DolphinOne_cn_2013-08-27_10-10-57_debug.apk" size="60" />&nbsp;&nbsp;
			Priority
			<select id="priority" name="priority">
				<option value='0' selected>0</option>
				<option value='1'>1</option>
			</select><br><br>
			Performance:
			<select id='category' name="category">
				<option value='0'>select perfromance</option>
			</select>
			<select id="performance" name="performance">
				<option value='0'>select sub performance</option>
			</select>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;	
			Version:
			<input type="radio" checked="checked" name="version" value="cn" />CN
			<input type="radio" name="version" value="en" />EN<br><br>		
			
			<div><input  style="border:solid thin;float:left;" type="submit" value="Submit" name="submit"/>
			&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<div  style="float:left;" id="feedback"></div>
			</div>
			
			</form>
		</div>

    </div>
 
    <div class="buildinfo" style="float:right;width:50%;"></div>
        
</div>

</body>