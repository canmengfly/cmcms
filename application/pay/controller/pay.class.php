<?php
/**
 * 支付模块
 * @author           残梦  
 * @license          http://cms.canmeng.com
 * @lastmodify       2018-06-28
 */
 
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_sys_class('page','',0);

class pay extends common {
	
	
	/**
	 * 列表
	 */
	public function init() {
		$pay_mode = D('pay_mode');
		$total = $pay_mode->total();
		$page = new page($total, 10);
		$data = $pay_mode->order('id ASC')->limit($page->limit())->select();		
		include $this->admin_tpl('pay_list');
	}	

	
	/**
	 * 编辑
	 */
	public function edit() {
		
		if(isset($_POST['dosubmit'])) {
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$config = array();
			$data['enabled'] = intval($_POST['enabled']);
			$data['config'] = array2string($_POST['config']);
			if(D('pay_mode')->update($data, array('id'=>$id))){
				delcache('',true);
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json();
			}
		}else{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$data = D('pay_mode')->where(array('id'=>$id))->find();
			$config = string2array($data['config']);
			include $this->admin_tpl($data['template']);			
		}
	}


}