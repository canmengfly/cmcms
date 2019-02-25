<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_sys_class('page','',0);

class banner extends common {

	/**
	 * banner列表
	 */
	public function init() {
		$banner = D('banner');
		$total = $banner->total();
		$page = new page($total, 5);
		$data = $banner->order('listorder ASC,id DESC')->limit($page->limit())->select();		
		include $this->admin_tpl('banner_list');
	}	

	
	/**
	 * 添加banner分类
	 */
	public function cat_add() {

		if(isset($_POST['dosubmit'])) {
			$typeid = D('banner_type')->insert($_POST, true);
			if($typeid){
                $html = "<option value='{$typeid}' selected>{$_POST['name']}</option>";
				return_json(array('status'=>1,'message'=>L('operation_success'),'html'=>$html));
			}else{
				return_json(array('status'=>0,'message'=>L('operation_failure')));
			}
		}else{
			include $this->admin_tpl('cat_add');
		}
	}

	
	/**
	 * banner分类管理
	 */
	public function cat_manage() {

		if($_POST && is_array($_POST['id'])) {
			if(D('banner_type')->delete($_POST['id'], true)){
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json(array('status'=>0,'message'=>L('operation_failure')));
			}
		}else{
			$data = D('banner_type')->select();
			include $this->admin_tpl('cat_manage');
		}
	}


	/**
	 * 添加banner
	 */
	public function add() {

		if(isset($_POST['dosubmit'])) {
			$_POST['inputtime'] = SYS_TIME;
			if(D('banner')->insert($_POST, true)){
				showmsg(L('operation_success'), U('init'), 1);
			}else{
				showmsg(L('operation_failure'));
			}
		}else{
			$types = D('banner_type')->select();
			include $this->admin_tpl('banner_add');
		}
	}


	
	/**
	 * 编辑banner
	 */
	public function edit() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		if(isset($_POST['dosubmit'])) {
			if(D('banner')->update($_POST, array('id'=>$id))){
				showmsg(L('operation_success'), U('init'), 1);
			}else{
				showmsg(L('operation_failure'));
			}
		}else{
			
			$types = D('banner_type')->select();
			$data = D('banner')->where(array('id'=>$id))->find();
			include $this->admin_tpl('banner_edit');			
		}
	}

	
	/**
	 * 删除banner
	 */
	public function del() {
		if($_POST && is_array($_POST['id'])){
			if(D('banner')->delete($_POST['id'], true)){
				showmsg(L('operation_success'));
			}else{
				showmsg(L('operation_failure'));
			}
		}
	}
	
	
	/**
	 * 获取banner分类
	 */
	public function get_banner_type($typeid) {
		if(!$typeid) return '无分类';
		$r = D('banner_type')->where(array('tid'=>$typeid))->find();
		return $r ? $r['name'] : '';
	}
	
}