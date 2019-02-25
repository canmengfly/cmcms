<?php defined('IN_cmPHP') or exit('No permission resources.'); ?><!--mini登陆条-->
<div id="head_login">
<div class="w1000">
<div id="mini">
<?php if(intval(get_cookie('_userid'))==0) { ?>
<a href="<?php echo U('member/index/register');?>" target="_blank">注册</a> <a href="<?php echo U('member/index/login');?>"  target="_blank">登录</a>
<?php } else { ?>
你好：<?php echo safe_replace(get_cookie('_username'));?>，<a href="<?php echo U('member/index/init');?>">会员中心</a> <a href="<?php echo U('member/index/logout');?>">退出</a>
<?php } ?>
</div>
欢迎光临本站！
</div>
</div>
<!--网站容器-->
<div id="container">
<div id="header">
	<div id="logo">
	 <a href="<?php echo $site['site_url'];?>"><img src="<?php echo STATIC_URL;?>images/logo.png" title="<?php echo $site['site_name'];?>" alt="<?php echo $site['site_name'];?>"></a>
	</div>
	<div id="search">
	<form method="get" action="<?php echo SITE_URL;?>index.php" target="_blank">
		<div id="searchtxt" class="searchtxt">
			<div class="searchmenu">	
				<div class="searchselected" id="searchselected">文章</div>	
				<div class="searchtab" id="searchtab">
					<ul>
						<li data="1">文章</li>
						<li data="2">产品</li>
						<li data="3">下载</li>
					</ul>
				</div>
			</div>
			<input type="hidden" name="m" value="search" />
			<input type="hidden" name="c" value="index" />
			<input type="hidden" name="a" value="init" />
			<input type="hidden" name="modelid" value="1" id="modelid" />
			<input name="q" type="text" placeholder="输入关键字"/>
		</div>
		<div class="searchbtn">
			<button id="searchbtn" type="submit">搜索</button>
		</div>
	</form>
    </div>
	<div id="hea_bot"><a href="<?php echo U('member/member_content/init');?>" target="_blank">免费发布信息</a></div>
</div>
<!--导航条-->
<div class="menu">
  <ul class="nav">
	 <li><a <?php if(!isset($catid)) { ?> class="current" <?php } ?> href="<?php echo $site['site_url'];?>">首页</a></li>
	<?php $tag = cm_base::load_sys_class('cm_tag');if(method_exists($tag, 'nav')) {$data = $tag->nav(array('field'=>'catid,catname,arrchildid,pclink,type','where'=>"parentid=0",'limit'=>'20',));}?>
	<?php if(is_array($data)) foreach($data as $v) { ?>
	    <li>
			<a<?php if(isset($catid) && $v['catid']==$catid) { ?> class="current" <?php } ?> href="<?php echo $v['pclink'];?>" <?php if($v['type']==2) { ?> target="_blank" <?php } ?>><?php echo $v['catname'];?></a>
			<!-- 这里是二级栏目的循环，不需要的可以删除，代码开始 -->
			<?php if($v['arrchildid']!=$v['catid']) { ?> 
			<?php $r = get_childcat($v['catid']);?>
			<ul class="sub_nav">
				<?php if(is_array($r)) foreach($r as $v) { ?>
				<li><a href="<?php echo $v['pclink'];?>"><?php echo $v['catname'];?></a></li>
				<?php } ?>	
			</ul>
			<?php } ?>
			<!-- 这里是二级栏目的循环，不需要的可以删除，代码结束 -->
		</li>		
	<?php } ?>	
  </ul>
</div>
<div class="clearfix"></div>	
<script>
	$(function(){
		$(".nav>li").hover(function(){
			$(this).children('ul').stop(true,true).show(200);
		},function(){
			$(this).children('ul').stop(true,true).hide(200);
		})
	})
</script>