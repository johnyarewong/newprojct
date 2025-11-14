<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Cookie;

use wxpay\database\WxPayUnifiedOrder;
use wxpay\JsApiPay;
use wxpay\NativePay;
use wxpay\PayNotifyCallBack;
use think\Log;
use wxpay\WxPayApi;
use wxpay\WxPayConfig;

use alipay\wappay\buildermodel\AlipayTradeWapPayContentBuilder;
use alipay\wappay\service\AlipayTradeService;

use pinganpay\Webapp;

use pufapay\ConfigUtil;
use pufapay\HttpUtils;
use pufapay\SignUtil;
use pufapay\TDESUtil;



class Pay extends Controller
{

    public function __construct()
    {
        parent::__construct();
        $this->parter1 = 1729;
        $this->key1 = '3177204082c74e4db0f24ae2d5290617';
        $this->parter2 = 2865;
        $this->key2 = '57a599aafd1342f8be3b31417883186f';
    }

	
	
	public function yyb()
    {
      $ddh = $_POST['ddh']; //支付宝订单号
       
      $key = $_POST['key']; //KEY验证
       
      $name = $_POST['name']; //备注信息  接收网关data 参数  支付订单号
       
      $lb = $_POST['lb']; //分类 =1 支付宝 =2财付通 =3 微信
       
      $money = $_POST['money'];//金额
         
      $paytime = $_POST['paytime'];//充值时间
       
	  $key2 ='be87e41e2b319996dc78c4243d672e15';//优云宝 APPKEY 和云端和软件上面保持一致 
	  
      if($key==$key2){//验证KEY是否正确
	      //KEY正确执行
	  
		  //判断支付来源
		  if($lb==1) $leibie='支付宝';//可根据网站自定义数据
		  if($lb==2) $leibie='财付通QQ钱包';//可根据网站自定义数据
		  if($lb==3) $leibie='微信支付';//可根据网站自定义数据
		  
		   /*
		  此处执行你的程序逻辑 回执成功后
		  1、可以做成 判断支付宝订单号是否存在来完成充值
		  2、还可以做成 判断网站订单号(name)来完成充值
		  3、请做好订单号充值判断
		  */
		
		   $this->notify_ok_dopay($name,$money);
		  
          //执行完毕回执输出ok 字符
          echo "ok";
    
       }else{
		   //密匙错误
		   echo 'key error'; 
	   }      

    }
    /**

     * @return [type] [description]
     */
    public function wxpay($data)
    {


        if (!empty($data)) {
     
            $tools = new JsApiPay();
            $openId = Db::name('userinfo')->where(array('uid'=>$data['uid']))->value('openid');

            if(!$openId){
                return WPreturn('openId不存在',-1);
            }
          
            $input = new WxPayUnifiedOrder();
            $input->setBody("会员余额充值");
            $input->setAttach("web_user_pay_ing");

            $input->setOutTradeNo($data['balance_sn']);
            $input->setTotalFee($data['bpprice'] * 100);
            $input->setTimeStart(date("YmdHis"));
            $input->setTimeExpire(date("YmdHis", time() + 600));
            $input->setGoodsTag("goods");
            $input->setNotifyUrl("http://".$_SERVER['SERVER_NAME']."/index/wechat/notifyurl/bpid/".$data['bpid']);
            $input->setTradeType("JSAPI");
            $input->setOpenid($openId);

            $order = WxPayApi::unifiedOrder($input);

            $jsApiParameters = $tools->getJsApiParameters($order);

            /*
            $this->assign('order',$order);
            $this->assign('jsApiParameters',$jsApiParameters);
            return $this->fetch('jsapi');
            */
           return $jsApiParameters;
        }

    }

   

	function cut($start,$end,$file){
		$content=explode($start,$file);
		$content=explode($end,$content[1]);
		return  $content[0];
	}

    public function qianbaotong($data,$pay_type)
    {
        
        
        /*
        * 商户id，由平台分配
        */
        $parter = 2833;
        
        /*
        * 准备使用网银支付的银行
        */
        $type = $pay_type;
        
        /*
        * 支付金额
        */
        $value = $data['bpprice'];
        
        /*
        * 请求发起方自己的订单号，该订单号将作为平台的返回数据
        */
        $orderid = $data['balance_sn'];
        
        /*
        * 在下行过程中返回结果的地址，需要以http://开头。
        */
        $callbackurl = "http://".$_SERVER['SERVER_NAME']."/index/pay/qdb_notify.html";;
        
        /*
        * 支付完成之后平台会自动跳转回到的页面
        */
        $hrefbackurl = "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";;
        
        /*
        * 商户密钥
        */
        $key = 'ca2282966c884c7a9b688d96b4346819';
        if($type==1006)$types= 1 ;
		if($type==1007)$types= 3 ;
        $shidai_bank_url   = '/codepay/codepay.php';
        $url = "type=". $types ."&money=". $value. "&data=". $orderid ."&callbackurl=". $callbackurl;
        //签名
        $sign   = md5($url. $key);
        
        //最终url
        $url    = $shidai_bank_url . "?" . $url . "&sign=" .$sign. "&hrefbackurl=". $hrefbackurl;
        // echo  $url ;
	   return $url; // Header("Location: ".$url);
        
    }




    public function qdb_notify()
    {
        cache('qdb_test',$_GET);
        //$_GET = cache('qdb_test');
    
        
        //$sysorderid     = trim($_GET['sysorderid']);
        //$completiontime     = trim($_GET['systime']);

     
		$conf = getconf('');
        /*
     
        */
        $key = $conf['qtb_key'];
        header('Content-Type:text/html;charset=GB2312');
        $orderid        = trim($_GET['orderid']);
        $opstate        = trim($_GET['opstate']);
        $ovalue         = trim($_GET['ovalue']);
        $sign           = trim($_GET['sign']);
        
   
        if(empty($orderid)){
            die("opstate=-1");      
        }
        
        $sign_text  = "orderid=$orderid&opstate=$opstate&ovalue=$ovalue".$key;
        $sign_md5 = md5($sign_text);
        if($sign_md5 != $sign){
            die("opstate=-2");      
        }
        $this->notify_ok_dopay($orderid,$ovalue);
        die("opstate=0");       

    }

    
    public function alipay($data){

        $config = array (   

        'app_id' => "2017022705923867",


        'merchant_private_key' => "MIIEowIBAAKCAQEA4SvhwaggPK6YcT9KFcWatlWzmPOGuinPibsSuQOKOzIdndmsobx8gxYsL40SBJZJ7gUzLW53WUPJiu1Cn2K6b1m/PsOQNl6WRQD7fD62fCO5z3Wqitx9bts/LoUbX7vb4Dxpplw7KKVikUCBwe75hOTuhAfQ7dqGzbE0xfKjO2ugRBDceCy5InBK/xfvVbNRk+1DZyexLSUJx7pm5nUCkVj81URlnQYzcW06OBjvSSecTpmAktbvruZE450vhxkfDzxp47R0qba4c8ALRrDlnrUb29EPD4TFmXWGxteZQBQWKbEJWte7tV/sGW9ed/6QeC8A9N3CalnzXpqIF4hpcQIDAQABAoIBAFOUPDnrs/uSOxdeDJvEO0cOzJkrW4jiWByhibOO8tJCKegbkg5+riDiLAiCbnuxZUOqPnLQnBBQLxEYPDB5LwaB45DiejcUKOb4FGDrzkSJ5kBxRppAeXaafvs/gQep7VVwVy7e8T6HFO0haoiXsZp4d2gelpiTEpJrAlGvXJODDzMJPoEcpeHEDUUroH1+PXCGmZL8mB5a+ZzcP14IRsxWEygTy64MADa5RQ3U7qpSKSSiCRvTp1CUIMTEzgcYDziWCpWwdDEjrmyoQy3sUpdxwFrShQ0gwxgFgfawlR31d1rJxarF1/ZOsEa3RbbDdJWS4MwgMbYi70gB4UFTDLkCgYEA9bsfRblnK44C0oWTVxemxtuP96JPpqFj+jtcUMSBDZvnXyV5TKMWiP+agefWgQ5Gz5z6yBEicXvMcC9qcYf2nNnZYeTiCJmSof8dqWg5Uah3l+GBBJ13AVcrhJv/pm1Gkm3+WubREQBEXq3l9F/cRyEMzF2XWFCdrjX7R1JufssCgYEA6pTPTrIxufboxtJJXusdSSueqxN5see4TiqKXizRMuUaEF9h0iHd1fvxHWSMo3zlLt8s4LrR3PlKGXo88RnScNvRE3KyvznLkRhwdaFvTQjSUrMe+wV+OIRJm9UnV2ysqrB3w8+GP6iZPdiRN/AW4rkoPf0SMo2IGYR1/JsLlTMCgYBJxXqW+RlDFyg7wYRBYkVcb/AhvOXCtbMJHacSTFweFM76Xoqy+kc6q9nb5Bket4WEsLENPS+k+DChAWsoWFQuNKyxWgCN6mT+I1PpVvPWUwhMXZPZKdjfWycicZ7nfOjx7vmsmpzrSLQ95GEj41+DLyXjeLmF9vXPpj8g41tuzwKBgQCK31nzDs8ddqzLt4Y0KSCHRsmCId9zkOitbcXIhuO6K6NIeg8hJWd83NAbRIF17+SF4R1iVXcUSIizmIgne8/3fErEJqznREHdPgilutJ3WneY+e2nUdMthjNFi+TkfrOhwSLFyz+AxEEkOeeOpBYIVvEZ8Y4qW1ttL9vhlbA/vQKBgGrC0rchgbdL9Ehd8lG5yDYce1N2ZAxDLLGzyxbp76OExGQMj6vBJZeGp1S6ICNLSbbVWD3Wflk1d0o1o47GgF9p+PXJyeKes2ZTOByH0R+8M92fjVXmOxNUvC0oiqVTlFLd18cH4Yd9d6DaA+msmnkJY62tyZgceJcAmTwRVHkJ",
        
   
        'notify_url' => "http://".$_SERVER['SERVER_NAME']."/index/pay/alipay_notify.html",
        
 
        'return_url' => "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html",

    
        'charset' => "UTF-8",


        'sign_type'=>"RSA2",


        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",


        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4SvhwaggPK6YcT9KFcWatlWzmPOGuinPibsSuQOKOzIdndmsobx8gxYsL40SBJZJ7gUzLW53WUPJiu1Cn2K6b1m/PsOQNl6WRQD7fD62fCO5z3Wqitx9bts/LoUbX7vb4Dxpplw7KKVikUCBwe75hOTuhAfQ7dqGzbE0xfKjO2ugRBDceCy5InBK/xfvVbNRk+1DZyexLSUJx7pm5nUCkVj81URlnQYzcW06OBjvSSecTpmAktbvruZE450vhxkfDzxp47R0qba4c8ALRrDlnrUb29EPD4TFmXWGxteZQBQWKbEJWte7tV/sGW9ed/6QeC8A9N3CalnzXpqIF4hpcQIDAQAB",
        
    
);


     
        $out_trade_no = $data['balance_sn'];

  
        $subject = '用户充值';

 
        $total_amount = $data['bpprice'];


        $body = '';


        $timeout_express="1m";

        $payRequestBuilder = new AlipayTradeWapPayContentBuilder();
        $payRequestBuilder->setBody($body);
        $payRequestBuilder->setSubject($subject);
        $payRequestBuilder->setOutTradeNo($out_trade_no);
        $payRequestBuilder->setTotalAmount($total_amount);
        $payRequestBuilder->setTimeExpress($timeout_express);
        
        $payResponse = new AlipayTradeService($config);

        $result=$payResponse->wapPay($payRequestBuilder,$config['return_url'],$config['notify_url']);

        return $result;

    }


    /**
     * izpay
     * @author lukui  2017-08-16
     * @return [type] [description]
     */
    public function izpay_wx($data)
    {
        
        header("Access-Control-Allow-Origin: *");

        $url = 'http://www.izpay.cn:9002/thirdsync_server/third_pay_server';
        
        $para['out_trade_no'] = $data['balance_sn'];
        $para['mer_id'] = 'pay177';
        $para['goods_name'] = 'userpay';
        $para['total_fee'] = $data['bpprice']*100;
        $para['callback_url'] =  "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";
        $para['notify_url'] = "http://".$_SERVER['SERVER_NAME']."/index/pay/izpay_wx_notify.html";
       
        $para['attach'] =  '709';
        $para['nonce_str'] = mt_rand(time(),time()+rand());
        $para['pay_type'] = '003';
        $key = "c71elu2cq25b5m8ks99fxhqteljugo6m";
        
        
        
        
        $sign_str = 'mer_id='.$para['mer_id'].'&nonce_str='.$para['nonce_str'].'&out_trade_no='.$para['out_trade_no'].'&total_fee='.$para['total_fee'].'&key='.$key;
        
        //echo $sign_str;
        
        $para['sign'] = md5($sign_str); 
        
        
        $str = "";
        foreach($para as $key=>$val){
        $str .= $key.'='.$val.'&';
        }
        $newstr = substr($str,0,strlen($str)-1); 
        
        $pay_url = $url.'?'.$newstr;
        
        
        $temp_data = file_get_contents($pay_url);
        $result = json_decode($temp_data,true);
        
        
        return $temp_data;
    }

    public function izpay_alipay($data)
    {
        
        header("Access-Control-Allow-Origin: *");

        $url = 'http://www.izpay.cn:9002/thirdsync_server/third_pay_server';
        
        $para['out_trade_no'] = $data['balance_sn'];
        $para['mer_id'] = 'pay177';
        $para['goods_name'] = 'userpay';
        $para['total_fee'] = $data['bpprice']*100;
        $para['callback_url'] =  "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";
        $para['notify_url'] = "http://".$_SERVER['SERVER_NAME']."/index/pay/izpay_wx_notify.html";
       
        $para['attach'] =  '709';
        $para['nonce_str'] = mt_rand(time(),time()+rand());
        $para['pay_type'] = '006';
        $key = "c71elu2cq25b5m8ks99fxhqteljugo6m";
        
        
        
        
        $sign_str = 'mer_id='.$para['mer_id'].'&nonce_str='.$para['nonce_str'].'&out_trade_no='.$para['out_trade_no'].'&total_fee='.$para['total_fee'].'&key='.$key;
        
        //echo $sign_str;
        
        $para['sign'] = md5($sign_str); 
        
        
        $str = "";
        foreach($para as $key=>$val){
        $str .= $key.'='.$val.'&';
        }
        $newstr = substr($str,0,strlen($str)-1); 
        
        $pay_url = $url.'?'.$newstr;
        
        
        $temp_data = file_get_contents($pay_url);
        $result = json_decode($temp_data,true);
        
        
        return $temp_data;
    }
    
    
    public function izpay_wx_notify(){
        $data = input('');
        if(!isset($data['out_trade_no'])){
            return false;
        }
        $this->notify_ok_dopay($data['out_trade_no'],$data['total_fee']/100);
        return true;
        
    }





    

	public function qiandai($data){
		$pay_memberid = "10109";   
		$pay_orderid = $data['balance_sn'];    
		$pay_amount = $data['bpprice'];    
		$pay_applydate = date("Y-m-d H:i:s");  
		//$pay_bankcode = "WXZF";   
		if($data['pay_type']=='qd_wxpay'){
			$pay_bankcode = '901'; 
		}elseif($data['pay_type']=='qd_wxpay2'){
			$pay_bankcode = '902';
		}elseif($data['pay_type']=='qd_qqpay2'){
			$pay_bankcode = '908';
		}elseif($data['pay_type']=='qd_qqpay'){
			$pay_bankcode = '905';
		}
		$pay_notifyurl = "http://".$_SERVER['HTTP_HOST']."/index/pay/notify_qiandai";   
		$pay_callbackurl = "http://".$_SERVER['HTTP_HOST']."/index/user/index";  
		$Md5key = "4efxoocpszaobqb2oxg2vvtuuhmt3nxm";   
		$tjurl = "http://www.mdkpay.com/Pay_Index.html";  

		$jsapi = array(
			"pay_memberid" => $pay_memberid,
			"pay_orderid" => $pay_orderid,
			"pay_amount" => $pay_amount,
			"pay_applydate" => $pay_applydate,
			"pay_bankcode" => $pay_bankcode,
			"pay_notifyurl" => $pay_notifyurl,
			"pay_callbackurl" => $pay_callbackurl,
		);

		ksort($jsapi);
		$md5str = "";
		foreach ($jsapi as $key => $val) {
			$md5str = $md5str . $key . "=" . $val . "&";
		}
		//echo($md5str . "key=" . $Md5key."<br>");
		$sign = strtoupper(md5($md5str . "key=" . $Md5key));
		$jsapi["pay_md5sign"] = $sign;
		$jsapi["pay_tongdao"] = 'Wlzhifu'; 
		if($data['pay_type']=='qd_wxpay'){
			$jsapi["pay_tradetype"] = '900021'; 
		}elseif($data['pay_type']=='qd_alipay'){
			$jsapi["pay_tradetype"] = '900022';
		}
		$jsapi["pay_productname"] = '充值'; 
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
		if (strpos($user_agent, 'MicroMessenger') === false||$data['pay_type']=='qd_wxpay2'||$data['pay_type']=='qd_qqpay'||$data['pay_type']=='qd_qqpay2') {
		
			$form = '<form class="form-inline" method="post" action="'.$tjurl.'">';     
			foreach ($jsapi as $key => $val) {
				$form.='<input type="hidden" name="' . $key . '" value="' . $val . '">';
			}   
			$form.='</form>';
		} else {

			$form = '<form id="payform" class="form-inline" method="post" action="'.$tjurl.'">';     
			foreach ($jsapi as $key => $val) {
				$form.='<input type="hidden" name="' . $key . '" value="' . $val . '">';
			}   
			$form.='</form>';
			$html = time().rand(1000,9999);
			file_put_contents('./public/qdpay/'.$html.'.txt',$form);
			//header('location:/index/user/browseropen?html='.$form);
			 $form1 = '<form class="form-inline" method="post" action=\'/browseropen.php?html='.$html.'\'>';
			//$form1.='<input type="hidden" name="html" value="' . $form . '">';
			$form1.='</form>';
			$form = $form1; 
		}
		return $form;
	}
	

	public function notify_qiandai(){
		$ReturnArray = array( 
            "memberid" => $_REQUEST["memberid"], 
            "orderid" =>  $_REQUEST["orderid"], 
            "amount" =>  $_REQUEST["amount"], 
            "datetime" =>  $_REQUEST["datetime"],
            "returncode" => $_REQUEST["returncode"]
        );
		$this->notify_ok_dopay($ReturnArray['orderid'],$ReturnArray['amount']);
		exit("ok");
		
	}




    public function notify_ok_dopay($order_no,$order_amount)
    {
        
        if(!$order_no || !$order_amount){
            
            return false;
        }

        $balance = db('balance')->where('balance_sn',$order_no)->where('isverified',0)->find();
        if(!$balance){
            
            return false;
        }

        if($balance['bpprice'] != $order_amount){
            
            return false;
        }

        if($balance['bptype'] != 3){
            
            return true;
        }
        $_edit['bpid'] = $balance['bpid'];
        $_edit['bptype'] = 1;
        $_edit['isverified'] = 1;
        $_edit['cltime'] = time();
        $_edit['bpbalance'] = $balance['bpbalance']+$balance['bpprice'];
        
        $is_edit = db('balance')->update($_edit);
        
        if($is_edit){
     
            $_ids=db('userinfo')->where('uid',$balance['uid'])->setInc('usermoney',$balance['bpprice']);
            if($_ids){
          
                set_price_log($balance['uid'],1,$balance['bpprice'],'充值','用户充值',$_edit['bpid'],$_edit['bpbalance']);
            }
            
            return true;
        }else{
            
            return false;
        }

    }


    public function test_not()
    {
        
        dump(cache('ysyrefund'));
    }
    public function test_not_clear()
    {
        
        cache('ysyrefund',null);
    }


}


?>