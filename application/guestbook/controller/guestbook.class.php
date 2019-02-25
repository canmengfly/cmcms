<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_sys_class('page','',0);

class guestbook extends common {

	/**
	 * 留言板列表
	 */
	public function init() {
		$guestbook = D('guestbook');
		$total = $guestbook->where(array('replyid'=>'0'))->total();
		$page = new page($total, 10);
		$data = $guestbook->where(array('replyid'=>'0'))->order('id DESC')->limit($page->limit())->select();		
		include $this->admin_tpl('guestbook_list');
	}


	/**
	 * 查看及回复留言
	 */
	public function read() {
		$guestbook = D('guestbook');
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
 		if(isset($_POST['dosubmit'])) {
			$_POST['name'] = $_SESSION['adminname'];
			$_POST['booktime'] = SYS_TIME;
			$_POST['ip'] = getip();
			$guestbook->insert($_POST, true);
			showmsg("回复成功！", '', 1);
		}else{
			$guestbook->update(array('isread'=>'1'),array('id'=>$id));
			$data = $guestbook->where(array('id'=>$id))->find();
			$reply = $guestbook->field('booktime,bookmsg')->where(array('replyid'=>$id))->select(); //管理员回复
			include $this->admin_tpl('guestbook_read');
		}
	}
	
	/**
	 * 留言显示/隐藏
	 */
 	public function toggle() {
		$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		$ischeck = isset($_POST['ischeck']) ? intval($_POST['ischeck']) : 0;
	    D('guestbook')->update(array('ischeck'=>$ischeck), array('id'=>$id));
	}

	
	/**
	 * 删除多个留言
	 */
	public function del() {
		if($_POST && is_array($_POST['id'])){
			$guestbook = D('guestbook');
			foreach($_POST['id'] as $val){
				$guestbook->delete(array('id'=>$val));
				$guestbook->delete(array('replyid'=>$val));  //删除回复
			}
			showmsg(L('operation_success'));
		}
	}
	
}