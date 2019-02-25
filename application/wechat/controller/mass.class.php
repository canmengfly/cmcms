<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('wechat_common', 'wechat', 0);
cm_base::load_sys_class('page','',0);

class mass extends wechat_common{
	
	
    /**
     *  群发列表
     */	
	public function init(){
		$types = array('image'=>'图片','voice'=>'语音','video'=>'视频','thumb'=>'缩略图','news'=>'图文','text'=>'文本');
		$wechat_mass = D('wechat_mass');
        $total = $wechat_mass->total();
		$page = new page($total, 10);
		$data = $wechat_mass->order('id DESC')->limit($page->limit())->select();
		include $this->admin_tpl('mass_list');
    }
	

	
	/**
	 * 新建群发
	 */	
	public function add(){ 
 		if(isset($_POST['dosubmit'])) {
			$message_type = $_POST['message_type'];
			$media_id = $_POST['media_id'];

			if($_POST['type'] != 0){  //全部用户或者分组群发
				
				$str = $_POST['type']==2 ? '{"filter":{"is_to_all":true},' : '{"filter":{"is_to_all":false,"tag_id":'.$_POST['groupid'].'},';

				switch($message_type) {
					//图文
					case "news":
						$jsondata = $str.'"mpnews":{"media_id":"'.$media_id.'"},"msgtype":"mpnews","send_ignore_reprint":0}';

					break;
					//文本
					case "text":
						$jsondata = $str.'"text":{"content":"'.$_POST['content'].'"},"msgtype":"text"}';

					break;
					//语音
					case "voice":
						$jsondata = $str.'"voice":{"media_id":"'.$media_id.'"},"msgtype":"voice"}';

					break;
					//图片
					case "image":
						$jsondata = $str.'"image":{"media_id":"'.$media_id.'"},"msgtype":"image"}';

					break;
			
				}
				
				$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/sendall?access_token='.$this->get_access_token();  //全部用户或者分组群发
				
			}else{	//按openid列表群发
			
				$arr = explode(',', $_POST['openid']);
				$openid = '"'.join('","', $arr).'"';
					
				switch($message_type) {
					
					//图文
					case "news":
						$jsondata = '"touser":['.$openid.'],"mpnews":{"media_id":"'.$media_id.'"},"msgtype":"mpnews","send_ignore_reprint":0}';

					break;
					//文本
					case "text":
						$jsondata = '"touser":['.$openid.'],"msgtype": "text","text":{"content":"'.$_POST['content'].'"}';

					break;
					//语音
					case "voice":
						$jsondata = '"touser":['.$openid.'],"voice":{"media_id":"'.$media_id.'"},"msgtype":"voice"';

					break;
					//图片
					case "image":
						$jsondata = '"touser":['.$openid.'],"image":{"media_id":"'.$media_id.'"},"msgtype":"image"';

					break;
			
				}				
				
				$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.$this->get_access_token();  //按openid列表群发
			}
			
			$json_arr = $this->https_request($url, $jsondata);

			if($json_arr['errcode'] == 0){

				$_POST['msg_id'] = $json_arr['msg_id'];
				$_POST['msg_data_id'] = isset($json_arr['msg_data_id']) ? $json_arr['msg_data_id'] : '';
				$_POST['media_id'] = $message_type!='text' ? $_POST['media_id'] : $_POST['content'];
				$_POST['receive'] = $_POST['type'] != 0 ? $_POST['groupid'] : $_POST['openid'];
				$_POST['status'] = 1;
				$_POST['masstime'] = SYS_TIME;
				
				D('wechat_mass')->insert($_POST);
				showmsg(L('operation_success'), U('init'), 1);
			}else{
				showmsg('操作失败！errcode：'.$json_arr['errcode'].'，errmsg：'.$json_arr['errmsg'], 'stop');
			}			
	
		}
		
		$media_id = isset($_GET['media_id']) ? $_GET['media_id'] : '';
		$message_type = isset($_GET['message_type']) ? $_GET['message_type'] : '';
		$data = D('wechat_group')->select();
		include $this->admin_tpl('mass_add');
	}

	

	/**
	 * 查询群发状态
	 */	
	public function select_status(){
		if(isset($_POST['dosubmit'])){
			$msg_id = $_POST['msg_id'];
			$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/get?access_token='.$this->get_access_token();  
			$data = '{"msg_id": "'.$msg_id.'"}';
			
			$json_arr = $this->https_request($url, $data);
			if(!isset($json_arr['errcode'])){

				showmsg('msg_id：'.$json_arr['msg_id'].'，status：'.$json_arr['msg_status'], 'stop');
			}else{
				showmsg('查询失败！errcode：'.$json_arr['errcode'].'，errmsg：'.$json_arr['errmsg'], 'stop');
			}			
		}else{
			include $this->admin_tpl('select_status');
		}
	}
	
	
	/**
	 * 删除群发
	 */	
	public function del(){
		$id = intval($_GET['id']);
		$msg_id = intval($_GET['msg_id']);
		$url = 'https://api.weixin.qq.com/cgi-bin/message/mass/delete?access_token='.$this->get_access_token();  
		$data = '{"msg_id":'.$msg_id.'}';
		
		$json_arr = $this->https_request($url, $data);
		if($json_arr['errcode'] == 0){
			D('wechat_mass')->delete(array('id' => $id));
			showmsg('删除成功！', U('init'));
		}else{
			showmsg('删除失败！errcode：'.$json_arr['errcode'].'，errmsg：'.$json_arr['errmsg'], 'stop');
		}
	}
	

	
	/**
	 * 选择素材
	 */	
	public function select_material(){
		$types = array('image'=>'图片','voice'=>'语音','video'=>'视频','thumb'=>'缩略图','news'=>'图文');
		$wechat_media = D('wechat_media');
		$where = '1=1';
		if(isset($_GET['dosubmit'])){	
			$media_type = isset($_GET["media_type"]) ? intval($_GET["media_type"]) : 99;
			$type = isset($_GET["type"]) ? safe_replace($_GET["type"]) : '';
			
			if($media_type != 99) {
				$where .= ' AND media_type = '.$media_type;
			}
			
			if($type) {
				$where .= ' AND type = "'.$type.'"';
			}				
		}
		$total = $wechat_media->where($where)->total();
		$page = new page($total, 7);
		$data = $wechat_media->field('type, originname, filename, filepath, media_id, created_at, media_type, url')->where($where)->order('id DESC')->limit($page->limit())->select();
		include $this->admin_tpl('select_material');
	}
	
	
	/**
	 * 选择用户
	 */	
	public function select_user(){
		$groupid = isset($_GET["groupid"]) ? intval($_GET["groupid"]) : 99;
		$wechat_user = D('wechat_user');
		$wechat_group = D('wechat_group')->select();
		$where = 'subscribe = 1';
		if(isset($_GET['dosubmit'])){	
			$searinfo = isset($_GET["searinfo"]) ? safe_replace($_GET["searinfo"]) : '';
			$type = isset($_GET["type"]) ? $_GET["type"] : 1;
			
			if($searinfo != ''){
				if($type == '1')
					$where .= ' AND wechatid = \''.$searinfo.'\'';
				elseif($type == '2')
					$where .= ' AND scan = \''.$searinfo.'\'';
				else
					$where .= ' AND nickname LIKE \'%'.$searinfo.'%\'';
			}
			
			if($groupid != 99) {
				$where .= ' AND groupid = '.$groupid;
			}			
		}
		$total = $wechat_user->where($where)->total();
		$page = new page($total, 7);
		$data = $wechat_user->where($where)->order('wechatid DESC')->limit($page->limit())->select();
		include $this->admin_tpl('select_user');
	}
	
	
    /**
     *  获取分组名称
     */	
	public function get_groupname($wechat_group, $groupid){
		$arr = array();
        foreach($wechat_group as $val){
			$arr[$val['id']] = $val['name'];
		}
		
		return $arr[$groupid];
    }

}