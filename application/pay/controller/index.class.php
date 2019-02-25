<?php
/**
 * 支付操作类
 * @author           残梦  
 * @license          http://cms.canmeng.com
 * @lastmodify       2018-07-03
 */

class index{
	
	
	/**
	 * 支付宝支付异步回调
	 */	
	public function pay_callback(){
		if(is_post()){
			$out_trade_no = $_POST['out_trade_no'];
			$order = D('order')->field('id,order_sn,status,userid,username,paytype,money,quantity,`desc`')->where(array('order_sn' => $out_trade_no))->find();
			if(!$order) exit('fail');
			$order_params = array(
				'out_trade_no' => $order['order_sn'],
				'total_amount' => $order['money'],
				'status'       => $order['status'],
				'id'           => $order['id']
			);
			
			// 验签和参数校检
			$result = M('payment')->alipay_notify($_POST, $order_params);
			if($result) {
				$result = M('payment')->alipay_check_status($_POST['trade_status']);
				if($result){
					M('payment')->update_order($order, $_POST['trade_no']);
				}
				echo "success"; //不要修改或删除
			} else {
				echo "fail"; //不要修改或删除
			}
			exit;
		}
	}	
	
	
	/**
	 * 支付宝支付同步跳转通知
	 */	
	public function pay_return(){
		cm_base::load_common('plugin/alipay/returnpay.php');
		$result = returnpay::check($_GET);
		if($result){
			showmsg('支付成功，正在返回会员中心！', U('member/index/init'), 2);
		}else{
			showmsg('支付校验失败！', 'stop');
		}
	}



	/**
	 * 微信异步通知
	 */	
	public function wx_notify(){
		cm_base::load_common('plugin/wxpay/notify.php');
		if(class_exists('notify')){
			$notify = new notify();
			$notify->Handle();
		}
	}

}