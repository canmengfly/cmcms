<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);

class menu extends common {

	/**
	 * 菜单管理列表
	 */
	public function init() {
		$tree = cm_base::load_sys_class('tree');
		$tree->icon = array('&nbsp;&nbsp;&nbsp;│ ','&nbsp;&nbsp;&nbsp;├─ ','&nbsp;&nbsp;&nbsp;└─ ');
		$tree->nbsp = '&nbsp;&nbsp;&nbsp;';
		$data = D('menu')->order('listorder ASC')->select();

		$array = array();
		foreach($data as $v) {
			$v['string'] = '<a title="增加子类" href="javascript:;" onclick="cm_open(\'增加菜单\',\''.U('add',array('parentid'=>$v['id'])).'\',800,500)" style="text-decoration:none"  class="btn-mini btn-success ml-5">增加子类</a> <a title="编辑菜单" href="javascript:;" onclick="cm_open(\'编辑菜单\',\''.U('edit',array('id'=>$v['id'])).'\',800,500)" style="text-decoration:none"  class="btn-mini btn-secondary ml-5">编辑</a> <a title="删除" href="javascript:;" onclick="cm_del(\''.U('delete',array('id'=>$v['id'])).'\')" style="text-decoration:none"  class="btn-mini btn-warning ml-5">删除</a>';
			$v['display'] = $v['display'] ? '<span style="color:#0a6999">显示</span>' : '隐藏';
			$array[] = $v;
		}	
		$str  = "<tr>
					<td><input name='listorders[\$id]' type='text' value='\$listorder' class='input-text listorder'></td>
					<td>\$id</td>
					<td>\$spacer\$name</td>
					<td>\$display</td>
					<td>\$string</td>
				</tr>";
		$tree->init($array);
		$menus = $tree->get_tree(0, $str);
		include $this->admin_tpl('menu_list');
	}
	
	
	/**
	 * 删除菜单
	 */
	public function delete() {
		$id = intval($_GET['id']);
		$menu = D('menu');
		if($menu->where(array('parentid' => $id))->total() > 0){
			showmsg('删除失败，该菜单下有子菜单！');
		}else{
			$menu->delete(array('id'=>$id));
			delcache('menu_string_1');
		}	
		showmsg(L('operation_success'));
	}
	
	
	/**
	 * 添加菜单
	 */
	public function add() {		
		$menu = D('menu');
		if(isset($_POST['dosubmit'])) { 	
			$menu->insert($_POST, true);
			delcache('menu_string_1');
			return_json(array('status'=>1,'message'=>L('operation_success')));
		}else{
			$parentid = isset($_GET['parentid']) ? intval($_GET['parentid']) : 0;
			$tree = cm_base::load_sys_class('tree');
			$data = D('menu')->order('listorder ASC,id DESC')->select();
			$array = array();
			foreach($data as $v) {
				$v['selected'] = $v['id'] == $parentid ? 'selected' : '';
				$array[] = $v;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
			$tree->init($array);
			$select_menus = $tree->get_tree(0, $str);			
			include $this->admin_tpl('menu_add');
		}
		
	}
	
	
	/**
	 * 编辑菜单
	 */
	public function edit() {				
		$menu = D('menu');
		if(isset($_POST['dosubmit'])) {
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
		
			if($menu->update($_POST, array('id' => $id), true)){
				delcache('menu_string_1');
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json();
			}
			
		}else{
			$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
			$r = $menu->where(array('id' => $id))->find();
			if($r) extract($r);
			$tree = cm_base::load_sys_class('tree');
			$r = $menu->order('listorder ASC,id DESC')->select();
			$array = array();
			foreach($r as $v) {
				$v['selected'] = $v['id'] == $parentid ? 'selected' : '';
				$array[] = $v;
			}
			$str  = "<option value='\$id' \$selected>\$spacer \$name</option>";
			$tree->init($array);
			$select_menus = $tree->get_tree(0, $str);
			include $this->admin_tpl('menu_edit');
		}
		
	}


	/**
	 * 菜单排序
	 */
	function order() {
		if(isset($_POST['dosubmit'])) {
			$menu = D('menu');
			foreach($_POST['listorders'] as $id => $listorder) {
				$menu->update(array('listorder'=>$listorder),array('id'=>$id));
			}
			delcache('menu_string_1');
			showmsg(L('operation_success'), '', 1);
		} else {
			showmsg(L('operation_failure'));
		}
	}	
	
}