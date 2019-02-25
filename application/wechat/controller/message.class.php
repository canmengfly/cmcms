<?php
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('wechat_common', 'wechat', 0);
cm_base::load_sys_class('page','',0);

class message extends wechat_common{
	
	
    /**
     *  消息列表
     */	
	public function init(){
		
		$wechat_message = D('wechat_message');
        $total = $wechat_message->where(array('issystem' => 0))->total();
		$page = new page($total, 10);
		$data = $wechat_message->where(array('issystem' => 0))->order('id DESC')->limit($page->limit())->select();
		include $this->admin_tpl('message_list');
    }


	
	/**
     * 客服接口 给指定用户发送信息
     * 注意：微信规则只允许给在48小时内给公众平台发送过消息的用户发送信息
     * @param  string $openid  用户的openid
     * @param  array  $content 发送的数据，目前仅支持 text 类型
     */
    public function send_message(){
		if(isset($_POST['dosubmit'])){
			$openid = $_POST['openid'];
			$content = $_POST['content'];
			
			$url = 'https://api.weixin.qq.com/cgi-bin/message/custom/send?access_token='.$this->get_access_token();
			$json_str = '{
				"touser":"'.$openid.'",
				"msgtype":"text",
				"text":
				{"content":"'.$content.'"}
			}';
			
			$json_arr = $this->https_request($url, $json_str);

			if($json_arr['errcode'] == 0){
				$arr['openid'] = $openid;
				$arr['content'] = $content;
				$arr['inputtime'] = SYS_TIME;
				$arr['msgtype'] = 'text';
				$arr['isread'] = 1;
				$arr['issystem'] = 1;
				
				D('wechat_message')->insert($arr);
				showmsg('操作成功！', U('init'), 1);
			}else{
				showmsg('操作失败！'.$json_arr['errmsg'], 'stop');
			}
		}else{
			$openid = isset($_GET['openid']) ? $_GET['openid'] : 0;
			
			$wechat_message = D('wechat_message');
			
			$data = D('wechat_user')->field('openid, nickname, headimgurl')->where(array('openid' => $openid))->find();
			$wechat_message->update(array('isread'=>1), array('openid' => $openid));
			$message = $wechat_message->field('issystem, inputtime, content')->where(array('openid' => $openid))->order('id ASC')->select();
			include $this->admin_tpl('send_message');	
		}
    }
	

	/**
	 * 标识已读
	 */	
	public function read(){ 
		D('wechat_message')->update(array('isread'=>1), array('1'=>1));
		showmsg(L('operation_success'),'',1);
	}

	
	/**
	 * 删除消息
	 */	
	public function del(){ 
		if($_POST && is_array($_POST['ids'])){
			$wechat_message = D('wechat_message');
			foreach($_POST['ids'] as $val){
				$wechat_message->delete(array('id'=>$val));
			}
			showmsg(L('operation_success'),'',1);
		}
	}
	
	
	
    /**
     *  获取用户信息
     */	
	public function get_userinfo($openid){
		global $wechat_user;
		$wechat_user = isset($wechat_user) ? $wechat_user : D('wechat_user');
        $data = $wechat_user->field('nickname, headimgurl, remark')->where(array('openid' => $openid))->find();
		return '<img src="'.$data['headimgurl'].'" height="25" title="'.$data['remark'].'"> '.$data['nickname'];
    }

}