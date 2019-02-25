<?php defined('IN_cmPHP') or exit('No permission resources.'); ?><div class="clearfix"></div>		 
		<!--网站底部-->
		<div id="footer">
		  <div id="foot_left">
		   <a href="<?php echo $site['site_url'];?>"><img src="<?php echo STATIC_URL;?>images/logo.png" title="<?php echo $site['site_name'];?>"></a>
		  </div>
		  <div id="foot_right">
			<!-- 
				为了支持cmCMS的发展,请您保留cmCMS内容管理系统的链接信息,谢谢!
			-->			  
			  <p><?php echo $site['site_filing'];?> <?php echo $site['site_code'];?> <a href="<?php echo U('guestbook/index/init');?>" target="_blank">留言板</a>|<a href="<?php echo $site['site_url'];?>index.php?m=mobile">手机版</a></p>
			  <p><?php echo $site['site_copyright'];?></p>
			  <p>Powered by <a href="http://cms.canmeng.com" target="_blank">cmCMS</a> © 2014-2019</p>
		  </div>
		</div>
    </div>
  </body>
</html>