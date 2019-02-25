<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_common('lib/sql'.EXT, 'admin');

class sitemodel extends common {

	/**
	 * 模型列表
	 */
	public function init() {
		$data = D('model')->where(array('type' => 0))->order('modelid ASC')->select();
		include $this->admin_tpl('model_list');
	}
	
	
	/**
	 * 删除模型
	 */
	public function delete() {
		$modelid = intval($_GET['modelid']);
		if(in_array($modelid, array(1, 2, 3))) showmsg('不能删除系统模型！');		
		$model = D('model');		
		$r = $model->field('tablename')->where(array('modelid'=>$modelid))->find();
		if($r) sql::sql_delete($r['tablename']); 
		$model->delete(array('modelid'=>$modelid)); //删除model信息
		D('model_field')->delete(array('modelid'=>$modelid)); //删除字段
		delcache('modelinfo');
	
		showmsg(L('operation_success'));
	}
	
	/**
	 * 添加模型
	 */
	public function add() {		
		
		if(isset($_POST['dosubmit'])) { 
			if(!$_POST['name']) return_json(array('status'=>0,'message'=>'模型名称不能为空！'));
			$tablename = isset($_POST['tablename']) ? strip_tags($_POST['tablename']) : '';
			if(!$tablename) return_json(array('status'=>0,'message'=>'表名称不能为空！'));			
			$model = D('model');
			if($model->table_exists(C('db_prefix').$tablename)) return_json(array('status'=>0,'message'=>'表名已存在！'));	
			$_POST['issystem'] = $_POST['type'] = 0;
			$_POST['inputtime'] = SYS_TIME;
			$model->insert($_POST, true);
			sql::sql_create($tablename);
			delcache('modelinfo');
			return_json(array('status'=>1,'message'=>L('operation_success')));
		}else{			
			include $this->admin_tpl('model_add');
		}
		
	}
	
	/**
	 * 编辑模型
	 */
	public function edit() {				
		$model = D('model');
		if(isset($_POST['dosubmit'])) {
			$modelid = isset($_POST['modelid']) ? intval($_POST['modelid']) : 0;
			if($model->update(array('name'=>$_POST["name"],'description'=>$_POST["description"],'disabled'=>$_POST["disabled"]), array('modelid' => $modelid))){
				delcache('modelinfo');
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json();
			}
			
		}else{
			$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
			$data = $model->where(array('modelid' => $modelid))->find();
			include $this->admin_tpl('model_edit');
		}
		
	}	


	/*
	 * 模型导出
	 */
	public function export() {
		$modelid = isset($_GET['modelid']) ? intval($_GET['modelid']) : 0;
		$data = D('model')->where(array('modelid' => $modelid))->find();
		if(!$data) showmsg('参数错误！', 'stop');
		$arr['model_data'] = $data;
		$field_data = D('model_field')->where(array('modelid' => $modelid))->select();
		$arr['model_field_data'] = $field_data;
		$res = array2string($arr);
		header('Content-Disposition: attachment; filename="cm_'.$data['tablename'].'.model"');
		echo $res;exit;
	}


	/*
	 * 模型导入
	 */
	public function import() {
		if(isset($_POST['dosubmit'])) {
			$data_import = $_FILES['data_import']['tmp_name'];
			if(empty($data_import)) return_json(array('status'=>0,'message'=>'请上传文件！'));
			if(fileext($_FILES['data_import']['name']) != 'model') return_json(array('status'=>0,'message'=>'上传文件类型错误！'));
			$data_import = @file_get_contents($data_import);
			if(empty($data_import)) return_json(array('status'=>0,'message'=>'上传文件数据为空！'));
			$model_import_data = string2array($data_import);
			if(!is_array($model_import_data))  return_json(array('status'=>0,'message'=>'解析文件数据错误！'));
			$model_data = $model_import_data['model_data'];

			$modelid = D('model')->field('modelid')->where(array('tablename' => $model_data['tablename']))->one();
			$model_arr = array();
			$model_arr['name'] = htmlspecialchars($model_data['name']);
			$model_arr['description'] = htmlspecialchars($model_data['description']);
			$model_arr['setting'] = $model_data['setting'];
			$model_arr['inputtime'] = intval($model_data['inputtime']);
			$model_arr['disabled'] = intval($model_data['disabled']);
			$model_arr['type'] = intval($model_data['type']);
			$model_arr['sort'] = intval($model_data['sort']);
			$model_arr['issystem'] = intval($model_data['issystem']);	

			//更新模型
			if($modelid){
				D('model')->update($model_arr, array('modelid' => $modelid));
			}else{
				$model_arr['tablename'] = htmlspecialchars($model_data['tablename']);
				sql::sql_create($model_data['tablename']);
				$modelid = D('model')->insert($model_arr);
			}
			
			//更新模型字段
			$model_field = D('model_field');
			$model_field_data = $model_import_data['model_field_data'];
			foreach($model_field_data as $val){
				$fieldid = $model_field->field('fieldid')->where(array('modelid' => $modelid, 'field' => $val['field']))->one();
				$arr = array();
				$arr['modelid'] = $modelid;
				$arr['name'] = htmlspecialchars($val['name']);
				$arr['tips'] = htmlspecialchars($val['tips']);
				$arr['minlength'] = intval($val['minlength']);
				$arr['maxlength'] = intval($val['maxlength']);
				$arr['errortips'] = htmlspecialchars($val['errortips']);
				$arr['fieldtype'] = htmlspecialchars($val['fieldtype']);
				$arr['defaultvalue'] = htmlspecialchars($val['defaultvalue']);
				$arr['setting'] = $val['setting'];
				$arr['isrequired'] = intval($val['isrequired']);
				$arr['issystem'] = intval($val['issystem']);
				$arr['isunique'] = intval($val['isunique']);
				$arr['isadd'] = intval($val['isadd']);
				$arr['listorder'] = intval($val['listorder']);
				$arr['disabled'] = intval($val['disabled']);
				$arr['type'] = intval($val['type']);
				$arr['status'] = intval($val['status']);
				if($fieldid){
					$model_field->update($arr, array('fieldid' => $fieldid));
				}else{
					$arr['field'] = htmlspecialchars($val['field']);
					$model_field->insert($arr);
					$this->_add_field($arr, $model_data['tablename']);
				}
			}			
			
			delcache('modelinfo');
			delcache($modelid.'_model');
			return_json(array('status'=>1,'message'=>'导入成功！'));
		}else{
			$title = '导入模型';
			$url = U('import');
			include $this->admin_tpl('data_import');	
		}
	}
	

	/*
	 * 添加模型字段
	 */	
	private function _add_field($data, $table){
		if($data['fieldtype'] == 'textarea' || $data['fieldtype'] == 'images'){
		   sql::sql_add_field_mediumtext($table, $data['field']);  
		}else if($data['fieldtype'] == 'editor' || $data['fieldtype'] == 'editor_mini'){
		   sql::sql_add_field_text($table, $data['field']);  
		}else if($data['fieldtype'] == 'number'){
		   sql::sql_add_field_int($table, $data['field'], intval($data['defaultvalue'])); 
		   $data['fieldtype'] = 'input';
		}else{
		   sql::sql_add_field($table, $data['field'], $data['defaultvalue'], $data['maxlength']);  
		}		
	}
	
}