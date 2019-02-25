<?php
/**
 * 管理员后台消息操作类
 * @author           残梦  
 * @license          http://cms.canmeng.com
 * @lastmodify       2017-01-15
 */
 
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_sys_class('page','',0);

class member_message extends common{
	
	function __construct() {
		parent::__construct();
	}

	
	/**
	 * 消息列表[单发消息]
	 */	
	public function init(){ 
		$message = D('message');
		$total = $message->total();
		$page = new page($total, 10);
		$data = $message->order('messageid DESC')->limit($page->limit())->select();			
		include $this->admin_tpl('message_list');
	}
	

	
	/**
	 * 消息搜索[单发消息]
	 */	
	public function search(){ 
		$message = D('message');
		$where = '1=1';
		if(isset($_GET['dosubmit'])){	
			$type = isset($_GET["type"]) ? $_GET["type"] : '0';
			$username = isset($_GET["username"]) ? safe_replace($_GET["username"]) : '';
			
			if($username != '' && $type != '0'){
				if($type == '1')
					$where .= ' AND send_to LIKE \'%'.$username.'%\'';
				else
					$where .= ' AND send_from LIKE \'%'.$username.'%\'';
			}
			
			if(isset($_GET['start']) && isset($_GET['end']) && $_GET['start']) {
				$where .= " AND `message_time` >= '".strtotime($_GET["start"])."' AND `message_time` <= '".strtotime($_GET["end"])."' ";
			}			
		}
		$total = $message->where($where)->total();
		$page = new page($total, 10);
		$data = $message->where($where)->order('messageid DESC')->limit($page->limit())->select();		
		include $this->admin_tpl('message_list');
	}	
	
	
	
	/**
	 * 删除消息[单发消息]
	 */
	public function del() {
		if($_POST && is_array($_POST['ids'])){
			$message = D('message'); 
			foreach($_POST['ids'] as $id){
				$message->delete(array('messageid'=>$id));
			}
		}
		showmsg(L('operation_success'),'',1);
	}
	
	
	
	/**
	 * 发送消息[单发消息]
	 */
	public function add() {
		if(isset($_POST['dosubmit'])){
			
			if(!is_username($_POST['send_to'])) return_json(array('status'=>0,'message'=>'收件人格式不正确！'));
			if(!D('member')->where(array('username' => $_POST['send_to']))->find()) return_json(array('status'=>0,'message'=>'收件人不存在！'));

			$data['send_to'] = $_POST['send_to'];
			$data['subject'] = $_POST['subject'];
			$data['content'] = $_POST['content'];
			$data['message_time'] = SYS_TIME;
			$data['issystem'] = '1';
			$data['send_from'] = '系统';
			if(D('message')->insert($data, true)){
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json(array('status'=>0,'message'=>L('operation_failure')));
			}
			
		}
		include $this->admin_tpl('message_add');
	}
	
	
	/**
	 * 消息列表[群发消息]
	 */	
	public function messages_list(){ 
		$message_group = D('message_group');
		$total = $message_group->total();
		$page = new page($total, 10);
		$data = $message_group->order('id DESC')->limit($page->limit())->select();			
		include $this->admin_tpl('message_group_list');
	}
	
	
	
	/**
	 * 新建群发[群发消息]
	 */	
	public function add_messages(){ 
		if(isset($_POST['dosubmit'])){
			$data['groupid'] = intval($_POST['groupid']);
			$data['subject'] = $_POST['subject'];
			$data['content'] = $_POST['content'];
			$data['inputtime'] = SYS_TIME;
			if(D('message_group')->insert($data, true)){
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json(array('status'=>0,'message'=>L('operation_failure')));
			}
			
		}
		$member_group = get_groupinfo();
		include $this->admin_tpl('add_messages');
	}
	
	
	
	/**
	 * 删除消息[群发消息]
	 */
	public function del_messages() {
		if($_POST && is_array($_POST['ids'])){
			$message_group = D('message_group'); 
			foreach($_POST['ids'] as $id){
				$message_group->delete(array('id'=>$id));
			}
		}
		showmsg(L('operation_success'),'',1);
	}

}