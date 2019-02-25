<?php 
/**
 * 支付宝跳转同步通知
 * author：残梦
 * site: http://cms.canmeng.com
 */
 
cm_base::load_common('plugin/alipay/service/AlipayTradeService.php');
cm_base::load_model('payment', 'pay', 0);

class returnpay{
	
    /**
     * 同步通知校验
     * @param array  $params 回调参数
     */
    public static function check($params){
        $config = payment::alipay_config();
        $alipaySevice = new AlipayTradeService($config);
		unset($params['m'], $params['a'], $params['c']);
        $signResult = $alipaySevice->check($params);

        if($signResult) {
            return true;
        } else {
            return false;
        }
    }
}