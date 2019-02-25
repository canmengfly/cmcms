<?php
defined('IN_cmPHP') or exit('Access Denied');
		
//此处为解决Uploadify在火狐下出现http 302错误,重新设置SESSION
$session_name = session_name();
if(isset($_POST[$session_name])) session_id($_POST[$session_name]);

session_start();

cm_base::load_sys_class('upload','',0);
cm_base::load_sys_class('page','',0);

class api{
	
	private $isadmin;
	private $groupid;
	private $userid;
	private $username;
	
	function __construct() {
		
		$this->userid = isset($_SESSION['adminid']) ? $_SESSION['adminid'] : (isset($_SESSION['_userid']) ? $_SESSION['_userid'] : 0);
		$this->username = isset($_SESSION['adminname']) ? $_SESSION['adminname'] : (isset($_SESSION['_username']) ? $_SESSION['_username'] : '');
		$this->isadmin = isset($_SESSION['roleid']) ? 1 : 0;
		$this->groupid = get_cookie('_groupid') ? intval(get_cookie('groupid')) : 0;

		//判断是否登录
		if($this->userid==0){
			showmsg(L('login_website'),U('member/index/login'),1);
		}
		
		//判断是否有权限
		//if($this->isadmin==0 && !$grouplist[$this->groupid]['allowattachment']){
		//if($this->userid==0){
			//showmsg(L('no_permission_to_access'),U('admin/index/login'),1);
		//}
		
	}	
	
	
	
	public function init(){
		
	}

	
	
	/**
	 * 上传文件
	 */	
	public function upload(){ 

		//$filename = isset($_POST['filename']) ? $_POST['filename'] : 'Filedata';
		$filename = 'Filedata';
		$type = isset($_POST['type']) ? intval($_POST['type']) : 1;
		$module = isset($_POST['module']) ? htmlspecialchars($_POST['module']) : '';
		$option = array();
		if($type == 1){
			$option['allowtype'] = array('gif', 'jpg', 'png', 'jpeg');
		}else{
			$option['allowtype'] = $this->_get_upload_types();
		}
		$upload = new upload($option);
		if($upload->uploadfile($filename)){
			$fileinfo = $upload->getnewfileinfo();
			$fileinfo['module'] = $module;
			$fileinfo['originname'] = safe_replace($fileinfo['originname']);
			$this->_att_write($fileinfo);
			$arr = array(
				'status' => 1,
				'msg' => $fileinfo['fileurl'].$fileinfo['filename'],
				'title' => $fileinfo['originname'],
				'size' => $fileinfo['filesize'],
				'filetype' => $fileinfo['filetype']
			);
			return_json($arr);
		}else{
			$arr = array(
				'status' => 0,
				'msg' => $upload->geterrormsg()
			);
			return_json($arr);
		} 
	}
	
	
	/**
	 * 上传框
	 */	
	public function upload_box(){ 
		$pid = isset($_GET['pid']) ? $_GET['pid'] : 'uploadfile';
		$module = isset($_GET['module']) ? $_GET['module'] : '';
		$t = isset($_GET['t']) ? intval($_GET['t']) : 1; //上传类型，1为图片类型
		$n = isset($_GET['n']) ? 20 : 1;  //上传数量
		$s = round(get_config('upload_maxsize')/1024, 2).'MB';  //允许上传附件大小
		
		if($t == 1){
			$type = '*.jpg; *.jpeg; *.png; *.gif;';
		}else{
			$type = '*.'.join(',*.', $this->_get_upload_types());
		}
		
		//如果不是管理员，只列出自己上传的附件
		$where = array();
		if(!$this->isadmin) $where['userid'] = $this->userid;
		$attachment = D('attachment');
		$total = $attachment->where($where)->total();
		$parameter = $_GET;
		$parameter['tab'] = 1;
		$page = new page($total, 8, $parameter);
		$data = $attachment->field('isimage,originname,filename,filepath,fileext')->where($where)->order('id DESC')->limit($page->limit())->select();
		include get_config('ishtml5') ? $this->admin_tpl('upload_box_html5') : $this->admin_tpl('upload_box'); 
	}
		

	/**
	 * 图像裁剪
	 */	
	public function img_cropper(){
		$cmcms_path = SITE_PATH == '/' ? cmPHP_PATH : str_replace(SITE_PATH, '', str_replace('\\', '/', cmPHP_PATH));
		if(isset($_POST['filepath'])){
			$x = $_POST['x'];
			$y = $_POST['y'];
			$w = $_POST['w'];
			$h = $_POST['h'];
			$image = cm_base::load_sys_class('image');
			$filepath = SITE_PATH.C('upload_file').'/'.date('Ym/d/');
			$filename = date("ymdhis").rand(100,999);
			$filetype = fileext($_POST['filepath']);
			$fileinfo = $image->crop($cmcms_path.$_POST['filepath'], $cmcms_path.$filepath.$filename.'.'.$filetype, $w, $h, $x, $y);
			if($fileinfo){
				$fileinfo['module'] = 'admin';
				$fileinfo['originname'] = basename($_POST['filepath']);
				$fileinfo['filepath'] = $filepath;
				$fileinfo['filename'] = $filename.'.'.$filetype;
				$fileinfo['filesize'] = $fileinfo['size'];
				$fileinfo['filetype'] = $filetype;
				$this->_att_write($fileinfo);
				return_json(array('status' => 1, 'filepath' => $fileinfo['filepath'].$fileinfo['filename']));
			}else{
				return_json(array('status' => 0));
			}
		}

		$filepath = isset($_GET['f']) ? base64_decode($_GET['f']) : showmsg(L('lose_parameters'), 'stop');
		if(strpos($filepath, 'ttp:') || !is_file($cmcms_path.$filepath)) showmsg('请选择本地已存在的图像！', 'stop');
		$spec = isset($_GET['spec']) ? intval($_GET['spec']) : 1; 
		$cid = isset($_GET['cid']) ? $_GET['cid'] : 'thumb';
		switch ($spec){
			case 1:
			  $spec = '3 / 2';
			  break;  
			case 2:
			  $spec = '4 / 3';
			  break;
			case 3:
			  $spec = '1 / 1';
			  break;
			default:
			  $spec = '3 / 2';
		}
		include $this->admin_tpl('cropper_box'); 
	}

	
	/**
	 * 上传附件写入数据库
	 */	
	private function _att_write($fileinfo){
		$arr = array();
		$arr['originname'] = strlen($fileinfo['originname'])<50 ? $fileinfo['originname'] : $fileinfo['filename'];
		$arr['filename'] = $fileinfo['filename'];
		$arr['filepath'] = $fileinfo['filepath'];
		$arr['filesize'] = $fileinfo['filesize'];
		$arr['fileext'] = $fileinfo['filetype'];
		$arr['module'] = $fileinfo['module'];
		$arr['isimage'] = in_array($fileinfo['filetype'], array('gif', 'jpg', 'png', 'jpeg')) ? 1 : 0;
		$arr['downloads'] = 0;
		$arr['userid'] = $this->userid;
		$arr['username'] = $this->username;
		$arr['uploadtime'] = SYS_TIME;
		$arr['uploadip'] = getip();
		D('attachment')->insert($arr);
	}

	
	
	/**
	 * 获取上传类型
	 */	
	private function _get_upload_types(){
		$arr = explode('|', get_config('upload_types'));
		$allow = array('gif', 'jpg', 'png', 'jpeg','zip', 'rar', 'doc', 'docx', 'xls', 'xlsx', 'ppt', 'pptx', 'pdf','mp4', 'avi', 'wmv', 'rmvb', 'flv','mp3', 'wma', 'wav', 'amr', 'ogg');
		foreach($arr as $key => $val){
			if(!in_array($val, $allow)) unset($arr[$key]);
		}
		
		return $arr;
	}
	

	/**
	 * 加载模板
	 */	
	public static function admin_tpl($file = 'Undefined', $m = '') {
		$m = empty($m) ? ROUTE_M : $m;
		if(empty($m)) return false;
		return APP_PATH.$m.DIRECTORY_SEPARATOR.'view'.DIRECTORY_SEPARATOR.$file.'.html';
	}

}