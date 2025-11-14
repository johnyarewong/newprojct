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

	/**
     * 微信支付
     * @return [type] [description]
     */
    public function wxpay($data)
    {
		//return $data;
    	if (!empty($data)) {
            //获取用户openid
            $tools = new JsApiPay();
            $openId = Db::name('userinfo')->where(array('uid'=>$data['uid']))->value('openid');
			
            if(!$openId){
            	return WPreturn('openId不存在',-1);
            }
            //统一下单
            $input = new WxPayUnifiedOrder();
            $input->setBody("会员余额充值");
            $input->setAttach("web_user_pay_ing");
            $input->setOutTradeNo($data['balance_sn']);
            //$input->setTotalFee($data['bpprice'] * 100);
            $input->setTotalFee(1);
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
    /**
     * 中云支付
     * @return [type] [description]
     */
    public function zypay($data)
    {
    	
    $pay_memberid = "12789";   //商户ID
	$pay_orderid = $data['balance_sn'];    //订单号
	$pay_amount = $data['bpprice'];    //交易金额
	$pay_applydate = date("Y-m-d H:i:s");  //订单时间
	$pay_bankcode = "WftZfb";   //银行编码
	$pay_notifyurl = "http://".$_SERVER['SERVER_NAME']."/index/pay/zypay_notify.html";   //服务端返回地址
	$pay_callbackurl = "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";  //页面跳转返回地址
	
	$Md5key = "nAkMf7v2xa3ssoySQkHokTlI6fVE34";   //密钥
	
	$tjurl = "http://zy.cnzypay.com/Pay_Index.html";   //提交地址,如有变动请到官网下载最新接口文档
	
	$requestarray = array(
            "pay_memberid" => $pay_memberid,
            "pay_orderid" => $pay_orderid,
            "pay_amount" => $pay_amount,
            "pay_applydate" => $pay_applydate,
            "pay_bankcode" => $pay_bankcode,
            "pay_notifyurl" => $pay_notifyurl,
            "pay_callbackurl" => $pay_callbackurl
        );
		
	    ksort($requestarray);
        reset($requestarray);
        $md5str = "";
        foreach ($requestarray as $key => $val) {
            $md5str = $md5str . $key . "=>" . $val . "&";
        }
		
        $sign = strtoupper(md5($md5str . "key=" . $Md5key)); 
		$requestarray["pay_md5sign"] = $sign;
		
		$str = '<form id="Form1" name="Form1" method="post" action="' . $tjurl . '">';
        foreach ($requestarray as $key => $val) {
            $str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }
		$str = $str . '<input type="submit" value="提交">';
        $str = $str . '</form>';
        $str = $str . '<script>';
        $str = $str . 'document.Form1.submit();';
        $str = $str . '</script>';
        
        return $str;
        
    }
	
	

	    /**
     * yOU
     * @return [type] [description]
     */
    public function yupay($data)
    {
    $pid = "3019409135";   //优云宝APPID
	$ORDER = $data['balance_sn'];    //订单号
	$lb= $data['lb'];
	$money = $data['bpprice'];    //交易金额
	$m = 1;  //订单时间
	$url = "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";   //服务端返回地址
	$bk	 = 1;  //页面跳转返回地址
	$tjurl="http://pay2.youyunnet.com/pay/";
	$requestarray = array(
            "pid" => $pid,
            "data" => $ORDER,
            "lb" => $lb,
            "money" => $money,
            "m" => $m,
            "url" => $url,
            "bk" => $bk
        );
		
	   ksort($requestarray);
		$str = '<form id="Form1" name="Form1" method="post" action="' . $tjurl . '">';
        foreach ($requestarray as $key => $val) {
            $str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '">';
        }
		$str = $str . '<input type="submit" value="提交">';
        $str = $str . '</form>';
        $str = $str . '<script>';
        $str = $str . 'document.Form1.submit();';
        $str = $str . '</script>';
        
        return $str;
        
    }
	
	
	function yupay_notify(){
		
		
      $ddh = $_POST['ddh']; //支付宝订单号
       
      $key = $_POST['key']; //KEY验证
       
      $name = $_POST['name']; //备注信息  接收网关data 参数  支付订单号
       
      $lb = $_POST['lb']; //分类 =1 支付宝 =2财付通 =3 微信
       
      $money = $_POST['money'];//金额
         
      $paytime = $_POST['paytime'];//充值时间
       
	  $key2 ='a65a835f4095a0eb0b44208ce1987342';//优云宝 APPKEY 和云端和软件上面保持一致 
	  
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
     * 博扬支付
     * @return [type] [description]
     */
    public function bypay($data,$tongdao){
    	header("Content-type: text/html; charset=utf-8");
		$pay_memberid = "10189";   //商户ID
		$pay_orderid = $data['balance_sn'];    //订单号
		$pay_amount = $data['bpprice'];    //交易金额
		//$pay_amount = "0.1";    //交易金额
		$pay_applydate = date("Y-m-d H:i:s");  //订单时间
		$pay_bankcode = "WXZF";   //银行编码
		$pay_notifyurl = "http://".$_SERVER['SERVER_NAME']."/index/pay/bypay_notify.html";   //服务端返回地址
	$pay_callbackurl = "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";  //页面跳转返回地址
		$Md5key = "6yflp9dls94mbbjsr3fz06b7h8z1a1";   //密钥
		$tjurl = "http://www.boyangpay.com/Pay_Index.html";   //网关提交地址

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
		$jsapi["pay_tongdao"] = $tongdao; //通道
		$jsapi["pay_tradetype"] = '900021'; //通道类型   900021 微信支付，900022 支付宝支付 
		$jsapi["pay_productname"] = '会员服务'; //商品名称
		if($tongdao == 'Ucwxh5'){
			$str = '<form id="Form1" name="Form1" method="post" action="' . $tjurl . '">';
			foreach ($jsapi as $key => $val) {
				$str = $str . '<input type="hidden" name="' . $key . '" value="' . $val . '">';
			}
			$str = $str . '<input type="submit" value="提交">';
			$str = $str . '</form>';
			$str = $str . '<script>';
			$str = $str . 'document.Form1.submit();';
			$str = $str . '</script>';
			
			return $str;
		}else{
			if($tongdao == 'Ucwxscan'){
				$payinfo['title']='微信支付';
				$payinfo['content']='请使用微信扫码完成支付';
			}elseif($tongdao == 'Ucaliscan'){
				$payinfo['title']='支付宝支付';
				$payinfo['content']='请使用支付宝扫码完成支付';
			}
			$payinfo['balance_sn']=$data['balance_sn'];
			$payinfo['bpprice']=$data['bpprice'];
			
			$data=http_build_query($jsapi);
			$options = array(    
				'http' => array(    
					'method' => 'POST',    
					'header' => 'Content-type:application/x-www-form-urlencoded',    
					'content' => $data,    
					'timeout' => 15 * 60 // 超时时间（单位:s）    
				)    
			);    
			$context = stream_context_create($options);    
			$result = file_get_contents($tjurl, false, $context);    
			// print_r($result);die;
			$img = json_decode($result,true);
			$payinfo['img']=$img['code_img_url'];
			$strstr = '<img style="width:100%" src="'.$payinfo['img'].'" />';
			return $strstr;
		}
		
        

    }
	public function bypay_notify()
    {
		$ReturnArray = array( // 返回字段
            "memberid" => $_REQUEST["memberid"], // 商户ID
            "orderid" =>  $_REQUEST["orderid"], // 订单号
            "amount" =>  $_REQUEST["amount"], // 交易金额
            "datetime" =>  $_REQUEST["datetime"], // 交易时间
            "returncode" => $_REQUEST["returncode"]
        );
      
        $Md5key = "6yflp9dls94mbbjsr3fz06b7h8z1a1";
   
		ksort($ReturnArray);
        reset($ReturnArray);
        $md5str = "";
        foreach ($ReturnArray as $key => $val) {
            $md5str = $md5str . $key . "=" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "key=" . $Md5key)); 
        if ($sign == $_REQUEST["sign"]) {
			
            if ($_REQUEST["returncode"] == "00") {
				$this->notify_ok_dopay($ReturnArray['orderid'],$ReturnArray['amount']);
                exit("ok");
            }
        }

        cache('');
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
	//回调-青果畅付
	public function hkb()
    {
		$data = json_decode(file_get_contents('php://input'),true);
		$appid = $data['appid'];//商户号终端APPID
		$method = $data['method'];//接口支付类型参考各接口method
		$status = $data['status'];//支付状态 0未支付/待支付 1支付成功 2退款成功
		$out_trade_no = $data['out_trade_no'];//平台订单号
		$u_out_trade_no = $data['u_out_trade_no'];//商户订单号
		$transaction_id = $data['transaction_id'];//官方订单号成功状态订单不为空
		$total_fee = $data['total_fee'];//交易金额单位:分
		$create_time = $data['create_time'];//订单创建时间
		$nonce_str = $data['nonce_str'];//随机字符串
		$sign = $data['sign'];//数据签名参考数据签名规则
		unset($data['sign']);
		$checkSign = hkbSign($data,$key);

		if($sign == $checkSign){
			$this->notify_ok_dopay($out_trade_no,$total_fee/100);
			return '验签ok';

		}else{
			return '验签错误';
		}    

    }
	//签名-青果畅付
	function hkbSign($value,$key)
	{
		$value = array_filter($value);
		ksort($value);
		$str = '';
		foreach($value as $k=>$v){
			if($k == 'sign' || $v == null || $v == ''){
				continue;
			}
			$str.=$k.'='.$v.'&';
		}
		$str = $str.'key=20NBS89NOO6M6NGTA8G3OT'.$key;  //此处填写appKey
		$str = strtoupper(md5($str));
		return $str;
	}
	function hkbgo($data,$pay_type)
	{
        $type = $pay_type;
		$value = $data['bpprice'];
		$orderid = $data['balance_sn'];
		
		
		$shidai_bank_url   = '/hkbank/';
		if($type==1){
			$pagename="ali_h5.php";
		}else if($type==2){
			$pagename="wx_jsapi.php";
		}
        
        //最终url
		$total="total=".$value*100;
		$body="&body=用户充值";
		$out_trade_no="&out_trade_no=".$orderid;
		$return_url="&return_url=http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";
        $url= $shidai_bank_url . $pagename . "?".$total .$body.$out_trade_no.$return_url;
		
	    return $url; 
	}
	
    public function zypay_notify()
    {
        
        $ReturnArray = array( // 返回字段
            "memberid" => $_REQUEST["memberid"], // 商户ID
            "orderid" =>  $_REQUEST["orderid"], // 订单号
            "amount" =>  $_REQUEST["amount"], // 交易金额
            "datetime" =>  $_REQUEST["datetime"], // 交易时间
            "returncode" => $_REQUEST["returncode"]
        );
      
        $Md5key = "nAkMf7v2xa3ssoySQkHokTlI6fVE34";
        //$sign = $this->md5sign($Md5key, $ReturnArray);
        
        ///////////////////////////////////////////////////////
        ksort($ReturnArray);
        reset($ReturnArray);
        $md5str = "";
        foreach ($ReturnArray as $key => $val) {
            $md5str = $md5str . $key . "=>" . $val . "&";
        }
        $sign = strtoupper(md5($md5str . "key=" . $Md5key)); 
        ///////////////////////////////////////////////////////
        if ($sign == $_REQUEST["sign"]) {
            if ($_REQUEST["returncode"] == "00") {
                   $this->notify_ok_dopay($ReturnArray['orderid'],$ReturnArray['amount']);
                   exit("ok");
            }
        }
        cache('');
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
        $callbackurl = "http://".$_SERVER['SERVER_NAME']."/index/pay/qdb_notify.html";
        
        /*
        * 支付完成之后平台会自动跳转回到的页面
        */
        $hrefbackurl = "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html";
        
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
    function get_nonce( $length = 18 ) { 
		// 密码字符集，可任意添加你需要的字符 
		$chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789'; 
		$password =''; 
		for ( $i = 0; $i < $length; $i++ ) 
		{ 
			$password .= $chars[ mt_rand(0, strlen($chars) - 1) ]; 
		} 
		return $password; 
	} 
	public function fubei($data,$pay_type)
    {
    /*第一步获取威信授权*/
		$biz_content = array(
			'url'=>"http://".$_SERVER['HTTP_HOST']."/index/pay/fubeiweixinapi/ordersn/".$data['bpid']."/total_fee/".$data['bpprice'],
		);
		$biz_content_str = json_encode($biz_content);
		$post_arr = array(
			'app_id'=>'20170811163540529851',
			'method'=>"openapi.payment.auth.auth",
			'format'=>'json',
			'sign_method'=>'md5',
			'nonce'=>$this->get_nonce(),
			'biz_content'=>$biz_content_str,
		);
		ksort($post_arr);
		reset($post_arr);
		$md5str = "";
		foreach ($post_arr as $key => $val) {
			$md5str = $md5str . $key . "=" . $val . "&";
		}
		$md5str = substr($md5str,0,-1);
		$sign = strtoupper(md5($md5str . 'ccf9d126bf73d49d77fad93c0ee7a370')); 
		$post_arr['sign'] = $sign;
		$post_str = json_encode($post_arr);
		
		
		$url = 'https://shq-api.51fubei.com/gateway';
		
		$res = $this->send($url,$post_str);
		$res = json_decode($res,1);
		if($res['result_message'] == '成功' && $res['result_code'] == 200){
			return $res['data']['authUrl'];
		}
    }
	function send($url, $data)
	{
		$ch      = curl_init();
		$timeout = 30;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // 跳过证书检查
		curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); // 从证书中检查SSL加密算法是否存在
		$html     = curl_exec($ch);
		$curlinfo = curl_getinfo($ch);
		curl_close($ch);
		return $html;
	}
	function fubeiweixinapi(){
		if(input('ordersn')&&input('open_id')&&input('sub_open_id')&&input('total_fee')){
			$ordersn = input('ordersn');
			$open_id = input('open_id');
			$total_fee = input('total_fee');
			$sub_open_id = input('sub_open_id');
			$biz_content = array(
				'merchant_order_sn'=>$ordersn,
				'open_id'=>$open_id,
				'sub_open_id'=>$sub_open_id,
				//'total_fee'=>$total_fee,
				'total_fee'=>$total_fee,
				'store_id'=>210839,
			);
			$biz_content_str = json_encode($biz_content);
			$post_arr = array(
				'app_id'=>'20170811163540529851',
				'method'=>"openapi.payment.order.h5pay",
				'format'=>'json',
				'sign_method'=>'md5',
				'nonce'=>$this->get_nonce(),
				'biz_content'=>$biz_content_str,
			);
			ksort($post_arr);
			reset($post_arr);
			$md5str = "";
			foreach ($post_arr as $key => $val) {
				$md5str = $md5str . $key . "=" . $val . "&";
			}
			$md5str = substr($md5str,0,-1);
			$sign = strtoupper(md5($md5str . 'ccf9d126bf73d49d77fad93c0ee7a370')); 
			$post_arr['sign'] = $sign;
			$post_str = json_encode($post_arr);
			$url = 'https://shq-api.51fubei.com/gateway';
			$res = $this->send($url,$post_str);
			$res = json_decode($res,1);
			
			if($res['result_message'] == '成功' && $res['result_code'] == 200){
				$callbackurl = "http://".$_SERVER['HTTP_HOST']."/index/user/index";
				$notifyurl = "http://".$_SERVER['HTTP_HOST']."/index/pay/fubeicallback";
				$poststr = "prepay_id=".$res['data']['prepay_id']."&callback_url=".$callbackurl."&cancel_url=".$callbackurl;
				$datastr = "https://shq-api.51fubei.com/paypage?".$poststr;
				$this->assign('datastrro',$datastr);
        		return $this->fetch();
				//header("Location:https://shq-api.51fubei.com/paypage?".$poststr);
				//var_dump($res['data']['authUrl']);exit;
			}else{
				die('参数错误！');
			}
		}else{
			die('参数错误！');
		}
	}
	function fubeicallback(){
		$postdata = $_POST;
		if($postdata['result_code'] == 200 && $postdata['result_message'] == '成功'){
			unset($postdata['sign']);
			ksort($postdata);
			$md5str = "";
			foreach ($postdata as $key => $val) {
				if(!empty($val)){
					$md5str = $md5str . $key . "=" . $val . "&";
				}
			}
			$md5str = substr($md5str,0,-1);
			$sign = strtoupper(md5($md5str . 'ccf9d126bf73d49d77fad93c0ee7a370')); 
			if($sign == $_POST['sign']){
				$payinfo = json_decode($postdata['data'],true);
				//file_put_contents("log.txt", $payinfo['merchant_order_sn']."Hello world everyone.".$_POST['sign'].PHP_EOL, FILE_APPEND);
				$this->notify_ok_dopay1($payinfo['merchant_order_sn']);
				exit('success');
			}else{
				exit('success');
			}
		}
		
	}
    public function qdb_notify()
    {
        cache('qdb_test',$_GET);
        //$_GET = cache('qdb_test');
        //获取返回的下行数据
        
        //$sysorderid     = trim($_GET['sysorderid']);
        //$completiontime     = trim($_GET['systime']);
        //进行爱扬签名认证
        $key = 'ca2282966c884c7a9b688d96b4346819';
        header('Content-Type:text/html;charset=GB2312');
        $orderid        = trim($_GET['orderid']);
        $opstate        = trim($_GET['opstate']);
        $ovalue         = trim($_GET['ovalue']);
        $sign           = trim($_GET['sign']);
        
        //订单号为必须接收的参数，若没有该参数，则返回错误
        if(empty($orderid)){
            die("opstate=-1");      //签名不正确，则按照协议返回数据
        }
        
        $sign_text  = "orderid=$orderid&opstate=$opstate&ovalue=$ovalue".$key;
        $sign_md5 = md5($sign_text);
        if($sign_md5 != $sign){
            die("opstate=-2");      //签名不正确，则按照协议返回数据
        }
        $this->notify_ok_dopay($orderid,$ovalue);
                   
    }
    
    public function alipay($data){
        $config = array (   
        //应用ID,您的APPID。
        'app_id' => "2017022705923867",
        //商户私钥，您的原始格式RSA私钥
        'merchant_private_key' => "MIIEowIBAAKCAQEA4SvhwaggPK6YcT9KFcWatlWzmPOGuinPibsSuQOKOzIdndmsobx8gxYsL40SBJZJ7gUzLW53WUPJiu1Cn2K6b1m/PsOQNl6WRQD7fD62fCO5z3Wqitx9bts/LoUbX7vb4Dxpplw7KKVikUCBwe75hOTuhAfQ7dqGzbE0xfKjO2ugRBDceCy5InBK/xfvVbNRk+1DZyexLSUJx7pm5nUCkVj81URlnQYzcW06OBjvSSecTpmAktbvruZE450vhxkfDzxp47R0qba4c8ALRrDlnrUb29EPD4TFmXWGxteZQBQWKbEJWte7tV/sGW9ed/6QeC8A9N3CalnzXpqIF4hpcQIDAQABAoIBAFOUPDnrs/uSOxdeDJvEO0cOzJkrW4jiWByhibOO8tJCKegbkg5+riDiLAiCbnuxZUOqPnLQnBBQLxEYPDB5LwaB45DiejcUKOb4FGDrzkSJ5kBxRppAeXaafvs/gQep7VVwVy7e8T6HFO0haoiXsZp4d2gelpiTEpJrAlGvXJODDzMJPoEcpeHEDUUroH1+PXCGmZL8mB5a+ZzcP14IRsxWEygTy64MADa5RQ3U7qpSKSSiCRvTp1CUIMTEzgcYDziWCpWwdDEjrmyoQy3sUpdxwFrShQ0gwxgFgfawlR31d1rJxarF1/ZOsEa3RbbDdJWS4MwgMbYi70gB4UFTDLkCgYEA9bsfRblnK44C0oWTVxemxtuP96JPpqFj+jtcUMSBDZvnXyV5TKMWiP+agefWgQ5Gz5z6yBEicXvMcC9qcYf2nNnZYeTiCJmSof8dqWg5Uah3l+GBBJ13AVcrhJv/pm1Gkm3+WubREQBEXq3l9F/cRyEMzF2XWFCdrjX7R1JufssCgYEA6pTPTrIxufboxtJJXusdSSueqxN5see4TiqKXizRMuUaEF9h0iHd1fvxHWSMo3zlLt8s4LrR3PlKGXo88RnScNvRE3KyvznLkRhwdaFvTQjSUrMe+wV+OIRJm9UnV2ysqrB3w8+GP6iZPdiRN/AW4rkoPf0SMo2IGYR1/JsLlTMCgYBJxXqW+RlDFyg7wYRBYkVcb/AhvOXCtbMJHacSTFweFM76Xoqy+kc6q9nb5Bket4WEsLENPS+k+DChAWsoWFQuNKyxWgCN6mT+I1PpVvPWUwhMXZPZKdjfWycicZ7nfOjx7vmsmpzrSLQ95GEj41+DLyXjeLmF9vXPpj8g41tuzwKBgQCK31nzDs8ddqzLt4Y0KSCHRsmCId9zkOitbcXIhuO6K6NIeg8hJWd83NAbRIF17+SF4R1iVXcUSIizmIgne8/3fErEJqznREHdPgilutJ3WneY+e2nUdMthjNFi+TkfrOhwSLFyz+AxEEkOeeOpBYIVvEZ8Y4qW1ttL9vhlbA/vQKBgGrC0rchgbdL9Ehd8lG5yDYce1N2ZAxDLLGzyxbp76OExGQMj6vBJZeGp1S6ICNLSbbVWD3Wflk1d0o1o47GgF9p+PXJyeKes2ZTOByH0R+8M92fjVXmOxNUvC0oiqVTlFLd18cH4Yd9d6DaA+msmnkJY62tyZgceJcAmTwRVHkJ",
        
        //异步通知地址
        'notify_url' => "http://".$_SERVER['SERVER_NAME']."/index/pay/alipay_notify.html",
        
        //同步跳转
        'return_url' => "http://".$_SERVER['SERVER_NAME']."/index/user/index-bak.html",
        //编码格式
        'charset' => "UTF-8",
        //签名方式
        'sign_type'=>"RSA2",
        //支付宝网关
        'gatewayUrl' => "https://openapi.alipay.com/gateway.do",
        //支付宝公钥,查看地址：https://openhome.alipay.com/platform/keyManage.htm 对应APPID下的支付宝公钥。
        'alipay_public_key' => "MIIBIjANBgkqhkiG9w0BAQEFAAOCAQ8AMIIBCgKCAQEA4SvhwaggPK6YcT9KFcWatlWzmPOGuinPibsSuQOKOzIdndmsobx8gxYsL40SBJZJ7gUzLW53WUPJiu1Cn2K6b1m/PsOQNl6WRQD7fD62fCO5z3Wqitx9bts/LoUbX7vb4Dxpplw7KKVikUCBwe75hOTuhAfQ7dqGzbE0xfKjO2ugRBDceCy5InBK/xfvVbNRk+1DZyexLSUJx7pm5nUCkVj81URlnQYzcW06OBjvSSecTpmAktbvruZE450vhxkfDzxp47R0qba4c8ALRrDlnrUb29EPD4TFmXWGxteZQBQWKbEJWte7tV/sGW9ed/6QeC8A9N3CalnzXpqIF4hpcQIDAQAB",
        
    
);
        //商户订单号，商户网站订单系统中唯一订单号，必填
        $out_trade_no = $data['balance_sn'];
        //订单名称，必填
        $subject = '用户充值';
        //付款金额，必填
        $total_amount = $data['bpprice'];
        //商品描述，可空
        $body = '';
        //超时时间
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
public function notify_ok_dopay1($order_no)
    {
        
        if(!$order_no){
            
            return false;
        }
        $balance = db('balance')->where('bpid',$order_no)->find();
        if(!$balance){
            
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
            // add money
            $_ids=db('userinfo')->where('uid',$balance['uid'])->setInc('usermoney',$balance['bpprice']);
            if($_ids){
                //资金日志
                set_price_log($balance['uid'],1,$balance['bpprice'],'充值','用户充值',$_edit['bpid'],$_edit['bpbalance']);
            }
            
            return true;
        }else{
            
            return false;
        }
    }
    
    public function notify_ok_dopay($order_no,$order_amount)
    {
        
        if(!$order_no || !$order_amount){
            
            return false;
        }
        $balance = db('balance')->where('balance_sn',$order_no)->find();
        if(!$balance){
            
            return false;
        }
        if($balance['bpprice'] != $order_amount){
            
           // return false;
        }
        if($balance['bptype'] != 3){
            
            return true;
        }
		 
        $_edit['bpid'] = $balance['bpid'];
        $_edit['bptype'] = 1;
        $_edit['isverified'] = 1;
        $_edit['cltime'] = time();
        $_edit['bpbalance'] = $balance['bpbalance'] + $order_amount;
        
        $is_edit = db('balance')->update($_edit);
       
        if($is_edit){
            // add money
            $_ids=db('userinfo')->where('uid',$balance['uid'])->setInc('usermoney',$order_amount);
            if($_ids){
                //资金日志
                set_price_log($balance['uid'],1,$order_amount,'充值','用户充值',$_edit['bpid'],$_edit['bpbalance']);
            }
            
            return true;
        }else{
            
            return false;
        }
    }
    public function test_not()
    {
        
        dump(cache('qdb_test'));
    }
}
?>