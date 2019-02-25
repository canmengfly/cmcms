<?php
/**
 * 后台管理中心 - 自定义表单管理
 * @author           残梦  
 * @license          http://cms.canmeng.com
 * @lastmodify       2018-01-13
 */
 
 
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_common('lib/sql'.EXT, ROUTE_M);

class diyform extends common{
	
	
	/**
	 * 自定义表单列表
	 */	
	public function init(){	
		cm_base::load_sys_class('page','',0);
		$model = D('model');
		$total = $model->where(array('type'=>1))->total();
		$page = new page($total, 10);
		$data = $model->where(array('type'=>1))->order('modelid DESC')->limit($page->limit())->select();
		include $this->admin_tpl('diyform_list');
	}
	
	
	
	/**
	 * 添加自定义表单
	 */
 	public function add() {
 		if(isset($_POST['dosubmit'])) {
			if(!$_POST['name']) return_json(array('status'=>0,'message'=>'表单名称不能为空！'));
			$tablename = isset($_POST['tablename']) ? strip_tags($_POST['tablename']) : '';
			if(!$tablename) return_json(array('status'=>0,'message'=>'表名称不能为空！'));			
			$model = D('model');
			if($model->table_exists(C('db_prefix').$tablename)) return_json(array('status'=>0,'message'=>'表名已存在！'));	
			$_POST['setting'] = json_encode(array('show_template'=>$_POST['show_template'], 
			'check_code'=> intval($_POST['check_code']), 'sendmail'=> intval($_POST['sendmail']), 'allowvisitor'=>intval($_POST['allowvisitor'])));
			$_POST['type'] = 1;
			$_POST['inputtime'] = SYS_TIME;
			$model->insert($_POST);
			sql::sql_create($tablename);
			return_json(array('status'=>1,'message'=>L('operation_success')));
		}else{
			$show_temp = $this->select_template('show_temp', 'show_');
			$data = array('show_template'=>'show_diyform');
			include $this->admin_tpl('diyform_add');
		}

	}
	
	
	/**
	 * 编辑自定义表单
	 */
 	public function edit() {
		$model = D('model');
		if(isset($_POST['dosubmit'])) {
			$modelid = isset($_POST['modelid']) ? intval($_POST['modelid']) : 0;
			
			$data = array('name'=>$_POST["name"],
				'description'=>$_POST["description"],
				'disabled'=>$_POST["disabled"],
				'setting'=>json_encode(array('show_template'=>$_POST['show_template'], 'check_code'=> intval($_POST['check_code']), 'sendmail'=> intval($_POST['sendmail']), 'allowvisitor'=>intval($_POST['allowvisitor'])))
			);
			if($model->update($data, array('modelid' => $modelid))){
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json();
			}
			
		}else{
			$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
			$data = $model->where(array('modelid' => $modelid))->find();
			$setting = json_decode($data['setting'], true);
			$data = array_merge($data, $setting);
			$show_temp = $this->select_template('show_temp', 'show_');
			include $this->admin_tpl('diyform_edit');
		}

	}

	
	/**
	 * 删除自定义表单
	 */
	public function del() {
		$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
		
		$model = D('model');
		$r = $model->field('tablename')->where(array('modelid'=>$modelid))->find();
		if($r) sql::sql_delete($r['tablename']);
		
		$model->delete(array('modelid'=>$modelid)); 			//删除model信息
		D('model_field')->delete(array('modelid'=>$modelid)); 	//删除字段
	
		showmsg(L('operation_success'), U('init'), 1);
	}
	
	
	/**
	 * 模板选择
	 * 
	 * @param $style  风格
	 * @param $pre 模板前缀
	 */
	private function select_template($style, $pre = '') {
			$files = glob(APP_PATH.'index'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.C('site_theme').DIRECTORY_SEPARATOR.$pre.'*.html');
			$files = @array_map('basename', $files);
			$templates = array();
			if(is_array($files)) {
				foreach($files as $file) {
					$key = substr($file, 0, -5);
					$templates[$key] = $file;
				}
			}
			
			$tem_style = APP_PATH.'index'.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.C('site_theme').DIRECTORY_SEPARATOR.'config.php';
			if(is_file($tem_style)){
				$templets = require($tem_style);			
				return array_merge($templates, $templets[$style]);
			}else{
				return $templates;
			}
			
	}

}