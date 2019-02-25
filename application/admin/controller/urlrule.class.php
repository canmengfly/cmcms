<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_sys_class('page','',0);

class urlrule extends common {

	/**
	 * URL规则列表
	 */
	public function init() {
		$urlrule = D('urlrule');
		$total = $urlrule->total();
		$page = new page($total, 10);
		$data = $urlrule->order('urlruleid DESC')->limit($page->limit())->select();	
		include $this->admin_tpl('urlrule_list');
	}
	
	
	/**
	 * 删除URL规则
	 */
	public function del() {
		if($_POST && is_array($_POST['id'])){
			if(D('urlrule')->delete($_POST['id'], true)){
				delcache('urlrule');	
				delcache('mapping');	
				showmsg(L('operation_success'));
			}else{
				showmsg(L('operation_failure'));
			}
		}
	}
	
	
	/**
	 * 添加URL规则
	 */
	public function add() {		
		$urlrule = D('urlrule');
		if(isset($_POST['dosubmit'])) { 
			if(!preg_match('/^([a-zA-Z0-9]|[\/\(\)\\\+\-~!@_]){0,30}$/', $_POST['urlrule'])) return_json(array('status'=>0,'message'=>'URL规则不符合规范！'));
			$r = $urlrule->field('urlrule')->where(array('urlrule' => $_POST['urlrule']))->find();
			if($r) return_json(array('status'=>0,'message'=>'URL规则已存在！'));
			
			$urlrule->insert($_POST, true);
			delcache('urlrule');
			delcache('mapping');
			return_json(array('status'=>1,'message'=>L('operation_success')));
		}else{
			$category = get_category();
			include $this->admin_tpl('urlrule_add');
		}
		
	}
	
	
	/**
	 * 编辑URL规则
	 */
	public function edit() {				
		$urlrule = D('urlrule');
		if(isset($_POST['dosubmit'])) {
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			if(!preg_match('/^([a-zA-Z0-9]|[\/\(\)\\\+\-~!@_]){0,30}$/', $_POST['urlrule'])) return_json(array('status'=>0,'message'=>'URL规则不符合规范！'));
			if($urlrule->update($_POST, array('urlruleid' => $id), true)){
				delcache('urlrule');
				delcache('mapping');
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json();
			}
			
		}else{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$data = $urlrule->where(array('urlruleid' => $id))->find();
			include $this->admin_tpl('urlrule_edit');
		}
		
	}
	
}