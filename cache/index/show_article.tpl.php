<?php defined('IN_cmPHP') or exit('No permission resources.'); ?><!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html>
  <head>
      <meta http-equiv="Content-Type" content="text/html; charset=utf-8">
	  <title><?php echo $seo_title;?></title>
	  <meta name="keywords" content="<?php echo $keywords;?>" />
	  <meta name="description" content="<?php echo $description;?>" />
	  <link href="<?php echo STATIC_URL;?>css/default_common.css" rel="stylesheet" type="text/css" />
	  <link href="<?php echo STATIC_URL;?>css/default_style.css" rel="stylesheet" type="text/css" />
	  <script type="text/javascript" src="<?php echo STATIC_URL;?>js/jquery-1.8.2.min.js"></script>
	  <script type="text/javascript" src="<?php echo STATIC_URL;?>js/js.js"></script>
	  <script type="text/javascript" src="<?php echo STATIC_URL;?>js/jquery.qqFace.js"></script>
  </head>
  <body>
	<?php include template("index","header"); ?> 
		<div class="main_left">
            <div class="ind_bt col46"><?php echo get_location($catid);?></div>
			
            <div class="content">
			  <h1><?php echo $title;?></h1>
			  <div class="info">
			   <div class="msg">来源：<?php echo $copyfrom;?>  &nbsp;&nbsp; 更新时间：<?php echo date('Y-m-d H:i:s',$updatetime);?>   &nbsp;&nbsp; 编辑：<?php echo $nickname;?>  &nbsp;&nbsp;  浏览：<?php echo $click;?></div>
			  </div>
			  <?php echo $content;?>
			</div>
			
			<div class="content_tag"><span class="tag_title"><img src="<?php echo STATIC_URL;?>images/tag.png" title="Tag标签" alt="Tag标签">标签：</span>
			<?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'centent_tag')) {$data = $tag->centent_tag(array('modelid'=>$modelid,'id'=>$id,'limit'=>'10',));}?>
			<?php if(empty($data)) { ?>无标签<?php } ?>  <!-- 这句话是判断有没有内容标签的，可以选择删除 -->
			<?php if(is_array($data)) foreach($data as $v) { ?>
				<a href="<?php echo U('search/index/tag',array('id'=>$v['id']));?>" target="_blank"><?php echo $v['tag'];?></a>
			<?php } ?>
			</div>
			
			<div class="operate">
			<span id="favorite"><a href="javascript:;" onclick="add_favorite('<?php echo $title;?>');">收藏</a></span>  | <a href="javascript:;" onClick="window.print();" class="print">打印</a>
			</div>
			
	        <div class="pn">		
			  <p>上一篇：<?php echo $pre;?></p>
			  <p>下一篇：<?php echo $next;?></p>
			</div>
			
			<div class="clearfix"></div>			
			<!-- 评论区 开始 -->
			<h3 class="ind_bt">评论区</h3>
			<div class="comment">
				<div class="com_form">
				<form action="<?php echo U('comment/index/init');?>" method="post"  onsubmit="return check_comm(this)">
				<input type="hidden" name="modelid" value="<?php echo $modelid;?>">
				<input type="hidden" name="catid" value="<?php echo $catid;?>">
				<input type="hidden" name="id" value="<?php echo $id;?>">
				<input type="hidden" name="title" value="<?php echo $title;?>">
				<input type="hidden" name="url" value="<?php echo $url;?>">
				<textarea class="textarea" id="content" name="content" placeholder="我来说两句~"></textarea>
				<p><input type="submit" class="sub_btn" name="dosubmit" value="提交"><span class="emotion">表情</span></p>
				</form>
				</div>
			</div>
			<div class="comment_list">
				<?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'comment_list')) {$data = $tag->comment_list(array('modelid'=>$modelid,'catid'=>$catid,'id'=>$id,'limit'=>'20',));}?>
				<div class="comment_list_top">共<?php echo count($data);?>条评论</div>
				<div class="comment_list_body">
				<ul>
					<?php if(is_array($data)) foreach($data as $v) { ?>
					<li>
						<a class="user_pic" href="<?php echo U('member/myhome/init', array('userid'=>$v['userid']));?>" target="blank"><img src="<?php if(!empty($v['userpic'])) { ?><?php echo $v['userpic'];?><?php } else { ?><?php echo STATIC_URL;?>images/default.gif<?php } ?>" height="35" width="35"></a>
						<div class="comm_right">
							<a class="user_name" href="<?php echo U('member/myhome/init', array('userid'=>$v['userid']));?>" target="blank"><?php echo $v['username'];?></a>
							<p><?php echo nl2br($v['content']);?></p>
							<p><span class="comm_time"><?php echo date('Y-m-d H:i:s',$v['inputtime']);?></span> <a href="javascript:toreply('<?php echo $v['id'];?>');" class="comm_a">回复</a></p>
							<div id="rep_<?php echo $v['id'];?>" class="none">
							<form action="<?php echo U('comment/index/init');?>" method="post"  onsubmit="return check_rep(this)">
							<input type="hidden" name="modelid" value="<?php echo $modelid;?>">
							<input type="hidden" name="catid" value="<?php echo $catid;?>">
							<input type="hidden" name="id" value="<?php echo $id;?>">
							<input type="hidden" name="title" value="<?php echo $title;?>">
							<input type="hidden" name="url" value="<?php echo $url;?>">
							<input type="hidden" name="reply" value="<?php echo $v['id'];?>">
							<input type="hidden" name="reply_to" value="<?php echo $v['username'];?>">
							<textarea name="content" class="textarea textarea2" placeholder="我来说两句~"></textarea>
							<input type="submit" class="sub_btn static" name="dosubmit" value="提交">
							</form>
							</div>
						</div>
					</li>
					<?php } ?>
					<?php if(empty($data)) { ?><li>这篇文章还没有收到评论，赶紧来抢沙发吧~</li><?php } ?>
				</ul>
				</div>
			</div> 			
			<!-- 评论区 结束 -->
			
			<h3 class="ind_bt mt_20">相关内容</h3>
			<div class="tuijian">
			<ul>	
			<?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'relation')) {$data = $tag->relation(array('field'=>'title,url,thumb','modelid'=>$modelid,'id'=>$id,'limit'=>'4',));}?>
			<?php if(is_array($data)) foreach($data as $k=>$v) { ?>	
			<li<?php if($k==3) { ?> class="m_r0"<?php } ?>><a href="<?php echo $v['url'];?>"><img src="<?php if(!empty($v['thumb'])) { ?><?php echo $v['thumb'];?><?php } else { ?><?php echo STATIC_URL;?>images/nopic.jpg<?php } ?>" alt="<?php echo $v['title'];?>"><em><?php echo str_cut($v['title'], 36);?></em></a></li>
			<?php } ?>
			</ul>               
			</div>			
		</div>
		<div class="main_right">		 
			<h2 class="ind_bt">点击排行</h2>
			<?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'hits')) {$data = $tag->hits(array('field'=>'title,url,thumb','modelid'=>$modelid,'limit'=>'5',));}?>
			<?php if(is_array($data)) foreach($data as $v) { ?>	
			<div class="img_text"><a href="<?php echo $v['url'];?>"><img src="<?php if(!empty($v['thumb'])) { ?><?php echo $v['thumb'];?><?php } else { ?><?php echo STATIC_URL;?>images/nopic.jpg<?php } ?>" alt="<?php echo $v['title'];?>"></a><a href="<?php echo $v['url'];?>"><?php echo str_cut($v['title'], 81);?></a></div>
			<?php } ?>
			
			<h2 class="ind_bt mt_20 pl_10">随机新闻</h2>
			<?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'lists')) {$data = $tag->lists(array('field'=>'title,url,thumb','modelid'=>$modelid,'order'=>'RAND()','limit'=>'5',));}?>
			<?php if(is_array($data)) foreach($data as $v) { ?>	
			<div class="img_text"><a href="<?php echo $v['url'];?>"><img src="<?php if(!empty($v['thumb'])) { ?><?php echo $v['thumb'];?><?php } else { ?><?php echo STATIC_URL;?>images/nopic.jpg<?php } ?>" alt="<?php echo $v['title'];?>"></a><a href="<?php echo $v['url'];?>"><?php echo str_cut($v['title'], 81);?></a></div>
			<?php } ?>
			
			<h2 class="ind_bt mt_20 pl_10">评论排行榜</h2>
			<div class="l_list">
			<ul>
			<?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'comment_ranking')) {$data = $tag->comment_ranking(array('modelid'=>'1','limit'=>'10',));}?>
			<?php if(is_array($data)) foreach($data as $v) { ?>	
			<li><a href="<?php echo $v['url'];?>" title="<?php echo $v['title'];?>"><?php echo str_cut($v['title'], 63);?></a></li>
			<?php } ?>
			</ul>
			</div>					  
		</div>	
<script type="text/javascript">
function add_favorite(title) {
	$.ajax({
		type: 'POST',
		url: '<?php echo U("api/index/favorite");?>', 
		data: 'title='+title+'&url='+location.href,
		dataType: "json", 
		success: function (msg) {
			if(msg.status == 1){
				$("#favorite").html('收藏成功');
			}else{
				alert('请先登录！');
			}
		}
	});
}

$(function(){
	$('.emotion').qqFace({
		path:'<?php echo STATIC_URL;?>images/face/'	
	});
});
</script>		 
<?php include template("index","footer"); ?> 