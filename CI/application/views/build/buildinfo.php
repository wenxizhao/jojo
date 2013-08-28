<script type="text/javascript" charset="utf-8">
$(document).ready(function() {
    /* Init DataTables */
    $('.dataTable').dataTable({
    "bPaginate": false,
    "bLengthChange": false,
    "bFilter": false,
    "bSort": false,
    "bInfo": false,
    "bAutoWidth": false
        });
    /*add link to performance*/
    $('td#pname').html(function(){
        var base_url="<?php echo base_url().'index.php/build/chart/'?>";
        var url=base_url+$(this).parent().children('#pid').text();
        var some="<a id='performance' href='"+url+"' target='_blank'>"+$(this).text()+"</a>";
        return some;
        });
    /*add link to apk name*/
    $('h1#apkname').html(function(){
        var base_url="<?php echo base_url().'index.php/build/detail/'?>";
        var url=base_url+$('h1#build_id').text();
        var link="<a id='detail' href='"+url+"' target='_blank'>"+$(this).text()+"</a>";
        return link;
        });
   <!-- a href="<?#php echo base_url().'index.php/build/detail/'.$val['c_name']?>">-->
});
</script>
<body id="dt_example" style="margin: 30px 30px;">
<div class="content" style="margin: 30px 30px;">
	<h1 style="text-transform:capitalize" id="apkname"><?php echo $build_name?></h1>
	<h1 style="display:none" id="build_id"><?php echo $build_id?></h1>
	<?php if($build_info['valid']===FALSE):?>
	<div class="demo_table"></div>
		<h2 >There is no build of the day.</h2>
	</div>
	<?php else :?>
	<div class="demo_table">
		<?php foreach ($build_info as $key=>$val):?>
		<?php if(is_array($val)):?>
			<h3 style="text-transform:capitalize"><?php echo $val['c_name']?></h3>
				<table cellpadding="0" cellspacing="0" border="0" class="display dataTable" id="example" width="100%">
					<thead>
						<tr>
							<th>name</th>
							<th >dolphin</th>
							<th >uc</th>
							<th >denomination</th>
							<th style="display: none">pid</th>
						</tr>
					</thead>
					<tbody>					
					<?php foreach ($val as $key1=>$val1):?>
						<?php if(is_array($val1)):?>	
						<tr class="even gradeA">
							<?php if(count($val1)>2):?>							
							<td class="center" id="pname"><?php echo $val1['name']?></td>
							<td class="center"><?php echo $val1['dolphin']?></td>
							<td class="center"><?php echo $val1['uc']?></td>
							<td class="center"><?php echo $val1['denomination']?></td>
							<td id="pid" style="display: none"><?php echo $val1['pid']?></td>
							<?php else:?>							
							<td class="center" id="pname"><?php echo $val1['name']?></td>
							<td class="center"> </td>
							<td class="center"> </td>
							<td class="center"> </td>
							<td id="pid" style="display: none"><?php echo $val1['pid']?></td>
							<?php endif;?>
						</tr>
						<?php endif;?>
					<?php endforeach;?>					
					</tbody>
					<tfoot>
						<tr>
							<th>name</th>
							<th >dolphin</th>
							<th >uc</th>
							<th >denomination</th>
							<th style="display: none">pid</th>
						</tr>
					</tfoot>
				</table>
		<?php endif;?>
		<?php endforeach;?>
	<?php endif?>
	</div>
	

</div>
</body>
<?php 
include_once '../ChromePhp.php';
// 被嵌入到这一页中的php代码段
/*ChromePhp::log($build_info);
if($build_info['valid']===FALSE)
{
	echo "meiyou";
}
else{
	foreach ($build_info as $key=>$val)
	{
		if(is_array($val)){
			echo $val['c_name']."<br>";
			foreach ($val as $key1=>$val1)
			{
				if(is_array($val1)){
					if(count($val1)>2){
						echo $val1['pid'].' '.$val1['name'].' '.$val1['value'].' '.$val1['denomination']."<br>";	
					}
					else{
						echo $val1['pid'].' '.$val1['name']."<br>";
					}
						
				}
			}
		}	
	}
}
*/
?>


