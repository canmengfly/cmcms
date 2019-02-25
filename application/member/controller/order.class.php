<?php
/**
 * 管理员后台订单管理
 * @author           残梦  
 * @license          http://cms.canmeng.com
 * @lastmodify       2018-06-29
 */
 
defined('IN_cmPHP') or exit('Access Denied'); 
cm_base::load_controller('common', 'admin', 0);
cm_base::load_sys_class('page','',0);

class order extends common{
	
	public $paytype = array('1' => '支付宝', '2' => '微信');  //支付类型
	public $order_status = array('0' => '未付款', '1' => '已付款');  //订单状态
	
	/**
	 * 订单列表
	 */	
	public function init(){ 
		$order = D('order');
		$total = $order->total();
		$page = new page($total, 10);
		$data = $order->order('id DESC')->limit($page->limit())->select();			
		include $this->admin_tpl('order_list');
	}
	
	
	
	/**
	 * 订单记录搜索
	 */	
	public function order_search(){ 
		$order = D('order');
		$where = '1=1';
		if(isset($_GET['dosubmit'])){

			$searinfo = isset($_GET["searinfo"]) ? safe_replace($_GET["searinfo"]) : '';
			$status = isset($_GET["status"]) ? intval($_GET["status"]) : 99;
			$type = isset($_GET["type"]) ? $_GET["type"] : 1;
			
			if($status != 99) $where .= ' AND status = '.$status;
				
			if($searinfo != ''){
				if($type == '1')
					$where .= ' AND username LIKE \'%'.$searinfo.'%\'';
				else
					$where .= ' AND order_sn = \''.$searinfo.'\'';
			}
			
			if(isset($_GET['start']) && isset($_GET['end']) && $_GET['start']) {
				$where .= " AND `addtime` >= '".strtotime($_GET["start"])."' AND `addtime` <= '".strtotime($_GET["end"])."' ";
			}			
		}
		$total = $order->where($where)->total();
		$page = new page($total, 10);
		$data = $order->where($where)->order('id DESC')->limit($page->limit())->select();					
		include $this->admin_tpl('order_list');
	}
	
	
	/**
	 * 订单改价
	 */		
	public function change_price(){
		if(isset($_POST['dosubmit'])){
			$id = isset($_POST['id']) ? intval($_POST['id']) : 0;
			$money = isset($_POST['money']) ? floatval($_POST['money']) : 0;
			if($money < 0.01) return_json(array('status'=>0,'message'=>'支付金额不能小于0.01元'));
				
			if(D('order')->update(array('money' => $money), array('id' => $id))){
				return_json(array('status'=>1,'message'=>L('operation_success')));
			}else{
				return_json();
			}			
		}		
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$data = D('order')->where(array('id'=>$id))->find();
		include $this->admin_tpl('change_price');
	}
	
	
	/**
	 * 订单详情
	 */		
	public function order_details(){		
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		$data = D('order')->where(array('id'=>$id))->find();
		include $this->admin_tpl('order_details');
	}
	
	
	/**
	 * 删除
	 */	
	public function del(){ 
		$id = isset($_GET['id']) ? intval($_GET['id']) : 0;
		D('order')->delete(array('id'=>$id));
		showmsg(L('operation_success'),'',1);
	}
	

}