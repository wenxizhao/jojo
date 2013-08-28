<script type="text/javascript" charset="utf-8">
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
$(document).ready(function() {
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
			var value={"source":this.elements[0].value,
					"category":this.elements[1].value,
					"performance":this.elements[2].value,
					"version":this.elements[3].value,
					"priority":this.elements[4].value};
			$.post(url,value,function(data){
				$("#feedback").html(data);
				});
		}
	});
});
</script>
<body id="dt_example">
<div id="container">
		<div id="test_require">
			<h1>Require Autotest</h1>    
			<form action="<?php echo base_url().'index.php/build/testinsert/'?>" method="post" name="autotest">
			apk source:
			<input type="text" name="source" value="http://10.2.6.254/buildbot/DolphinOne_cn_2013-08-27_10-10-57_debug.apk" size="60" /><br>
			performance:
			<select id='category' name="category">
				<option value='0'>select perfromance</option>
			</select>
			<select id="performance" name="performance">
				<option value='0'>select sub performance</option>
			</select>&nbsp;&nbsp;
			version:
			<input type="radio" checked="checked" name="version" value="cn" />CN
			<input type="radio" name="version" value="en" />EN&nbsp;&nbsp;			
			priority
			<select id="priority" name="priority">
				<option value='0' selected>0</option>
				<option value='1'>1</option>
			</select><br>
			<div><input type="submit" value="Submit" name="submit"/></div>
			</form>
			
			<div id="feedback"></div>
		</div>
</div>
</body>