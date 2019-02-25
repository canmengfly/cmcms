<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);

class index extends common{
	
	/**
	 * 附件列表
	 */	
	public function init(){ 
		cm_base::load_sys_class('page','',0);
		$attachment = D('attachment');
		$total = $attachment->total();
		$page = new page($total, 10);
		$data = $attachment->order('id DESC')->limit($page->limit())->select();		

		include $this->admin_tpl('attachment_list');
	}
	

	/**
	 * 附件搜索
	 */	
	public function search_list(){ 
		cm_base::load_sys_class('page','',0);
		$attachment = D('attachment');
		$where = array();
		if(isset($_GET['dosubmit'])){
			if(isset($_GET['originname']) && $_GET['originname']){
				$where['originname'] = '%'.$_GET['originname'].'%';
			}
			if(isset($_GET['username']) && $_GET['username']){
				$where['username'] = '%'.$_GET['username'].'%';
			}
			if(isset($_GET['fileext']) && $_GET['fileext']){
				$where['fileext'] = $_GET['fileext'];
			}
			if(isset($_GET['start']) && isset($_GET['end']) && $_GET['start']) {
				$where['uploadtime>='] = strtotime($_GET['start']);
				$where['uploadtime<='] = strtotime($_GET['end']);
			}			
		}
		$total = $attachment->where($where)->total();
		$page = new page($total, 10);
		$data = $attachment->where($where)->order('id DESC')->limit($page->limit())->select();			

		include $this->admin_tpl('attachment_list');
	}

	
	/**
	 * 附件浏览
	 */	
	public function public_att_view(){		
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$info = D('attachment')->where(array('id'=>$id))->find();
		if($info){
			echo '<p style="text-align:center;">';
			echo $info['isimage'] ? '<img src="'.$info['filepath'].$info['filename'].'">' : '<img src="'.(in_array($info['fileext'], array('zip', 'rar')) ? STATIC_URL.'images/ext/rar.png' : STATIC_URL.'images/ext/blank.png').'" title="'.$info['originname'].'"><a style="font-size:14px;display:block;margin-top:20px;" href="'.$info['filepath'].$info['filename'].'">点击下载</a>';
			echo '</p>';
		}
	}
	
	
	/**
	 * 删除一个附件
	 */
	public function del_one() {
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$attachment = D('attachment');
		$info = $attachment->field('filepath,filename')->where(array('id'=>$id))->find();
		$file = cmPHP_PATH.strstr($info['filepath'].$info['filename'], C('upload_file'));
		if(is_file($file)) @unlink($file);
		if($attachment->delete(array('id' => $id))){
			showmsg(L('operation_success'));
		}else{
			showmsg(L('operation_failure'));
		}
	}

	
	/**
	 * 删除多个附件
	 */
	public function del() {
		if($_POST && is_array($_POST['id'])){
			$attachment = D('attachment');
			foreach($_POST['id'] as $val){
				$info = $attachment->field('filepath,filename')->where(array('id'=>$val))->find();
				$file = cmPHP_PATH.strstr($info['filepath'].$info['filename'], C('upload_file'));
				if(is_file($file)) @unlink($file);
				$attachment->delete(array('id' => $val));
			}
			showmsg(L('operation_success'));
		}
	}
	
}