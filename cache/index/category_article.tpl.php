<?php defined('IN_cmPHP') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	  <title><?php echo $seo_title;?></title>
	  <link href="<?php echo STATIC_URL;?>css/default_common.css" rel="stylesheet" type="text/css" />
	  <link href="<?php echo STATIC_URL;?>css/default_category.css" rel="stylesheet" type="text/css" />
	  <script type="text/javascript" src="<?php echo STATIC_URL;?>js/jquery-1.8.2.min.js"></script>
	  <script type="text/javascript" src="<?php echo STATIC_URL;?>js/js.js"></script>
	  <script type="text/javascript" src="<?php echo STATIC_URL;?>js/koala.min.1.5.js"></script> <!-- 焦点图js -->
	  <meta name="keywords" content="<?php echo $keywords;?>" />
	  <meta name="description" content="<?php echo $description;?>" />
  </head>
  <body>
	     <?php include template("index","header"); ?> 
		 <div class="main_left">
			 <?php $data = get_childcat($catid);?>
			 <?php if(is_array($data)) foreach($data as $k=>$v) { ?>
			 <div class="category_list<?php if($k%2!=0) { ?> mr_0<?php } ?>">
				<h3 class="cat_title"><a href="<?php echo $v['pclink'];?>"  class="gengduo">更多>></a><?php echo $v['catname'];?></h3>
				<ul>
				  <?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'lists')) {$data = $tag->lists(array('field'=>'title,inputtime,url','catid'=>$v['catid'],'limit'=>'10',));}?>
				  <?php if(is_array($data)) foreach($data as $v) { ?>	
				   <li><span><?php echo date('m-d',$v['inputtime']);?></span><a href="<?php echo $v['url'];?>" title="<?php echo $v['title'];?>"><?php echo str_cut($v['title'], 69);?></a></li>
				  <?php } ?>				 
				</ul>
			 </div>	
			<?php } ?>				 
		  </div>
		 <div class="main_right">
		  <!-- 焦点图 开始 -->
			<div id="jiaodian" class="focus">  
				<div id="fpic" class="fpic">
				  <?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'lists')) {$data = $tag->lists(array('field'=>'title,thumb,url','catid'=>$catid,'thumb'=>'1','limit'=>'3',));}?>
				  <?php $total = count($data);?>
				  <?php if(is_array($data)) foreach($data as $v) { ?>
				    <div class="fcon" style="display: none;">
						<a target="_blank" href="<?php echo $v['url'];?>"><img src="<?php echo $v['thumb'];?>" style="opacity: 1;" title="<?php echo $v['title'];?>"></a>
						<span class="shadow"><a target="_blank" href="<?php echo $v['url'];?>" title="<?php echo $v['title'];?>"><?php echo str_cut($v['title'], 36);?></a></span>
					</div>
				  <?php } ?>					  
				</div>
				<div class="fbg">  
				<div class="d1fbt" id="d1fbt"> 
					<?php for($i=1; $i<=$total; $i++) { ?>
					   <a href="javascript:void(0)" hidefocus="true" target="_self"><i><?php echo $i;?></i></a>
					<?php } ?>
				</div>  
				</div>     
			</div>  
			<script type="text/javascript">
				Qfast.add('widgets', { path: "<?php echo STATIC_URL;?>js/terminator2.2.min.js", type: "js", requires: ['fx'] });  
				Qfast(false, 'widgets', function () {
					K.tabs({
						id: 'jiaodian',     //焦点图包裹id  
						conId: 'fpic',      //大图域包裹id  
						tabId: 'd1fbt',  
						tabTn: 'a',
						conCn: '.fcon',    //大图域配置class       
						auto: 1,           //自动播放 1或0
						effect: 'fade',    //效果配置
						eType: 'click',    //鼠标事件
						pageBt: true,      //是否有按钮切换页码 					
						interval: 3000     //停顿时间  
					}) 
				})  
			</script>
			<!-- 焦点图 结束 -->
			
			 <h2 class="ind_bt mt_20 pl_10">频道点击排行</h2>
			 <div class="l_list">
			 <ul>
			  <?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'hits')) {$data = $tag->hits(array('field'=>'title,url,status','catid'=>$catid,'limit'=>'10',));}?>
			  <?php if(is_array($data)) foreach($data as $v) { ?>	
			   <li><a href="<?php echo $v['url'];?>" title="<?php echo $v['title'];?>"><?php echo str_cut($v['title'], 63);?></a></li>
			  <?php } ?>
			  </ul>
			 </div>					 
		 </div>		  	
 <?php include template("index","footer"); ?> 