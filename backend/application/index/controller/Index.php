<?php
namespace app\index\controller;
use think\Db;
use think\Cookie;



class Index extends Base
{

	/**
	 * 首页 行情列表
	 * @author lukui  2017-02-18
	 * @return [type] [description]
	 */
    public function index()
    {
        if(!input('token')){
            $this->redirect('index/index?token='.$this->token);
        }
        //获取产品信息
        $pro = Db::name('productinfo')->alias('pi')->field('pi.pid,pi.ptitle,pi.proorder,pd.Price,pd.UpdateTime,pd.Low,pd.High')
        		->join('__PRODUCTDATA__ pd','pd.pid=pi.pid')
        		->where('pi.isdelete',0)->order('pi.proorder asc')->select();
        //dump(cookie('pid7'));
        $this->assign('pro',$pro);

        $dbgg=Db::name("config")->where("name='web_qiantai'")->select();

        $webgg = '';
		if(cookie('think_var')=='zh-cn'){
		     $ruleconfig=Db::name("config")->where("name='web_qiantai_cn'")->select();
		     $webgg = $ruleconfig[0]['value']?$ruleconfig[0]['value']:'';
		}else if(cookie('think_var')=='zh-ja'){
		     $ruleconfig=Db::name("config")->where("name='web_qiantai_ja'")->select();
		     $webgg = $ruleconfig[0]['value']?$ruleconfig[0]['value']:'';
		}else{
		     $ruleconfig=Db::name("config")->where("name='web_qiantai'")->select();
		     $webgg = $ruleconfig[0]['value']?$ruleconfig[0]['value']:'';
		}

        // $web_logo = Db::name("config")->where("name='web_logo'")->select();
        // $this->assign('web_logo',$web_logo);
        $this->assign('web_logo',$this->conf['web_logo']);
        
        $this->assign('webgg',$webgg);

		/*
		//晒单功能
		$order_pub = Db::name('productinfo')->alias('pi')->field('pi.pid,pi.ptitle,pd.otype,pd.oid,pd.buytime,pd.price')
        		->join('order_pub pd','pd.pid=pi.pid')
        		->where('pd.buytime > 0')->order('pd.buytime desc')->select();

		$id_arr = array();
		foreach($pro as $r){
			array_push($id_arr,$r["pid"]);
		}
		$pro_length = count($id_arr);

		$price_arr = array(100,100,100,100,200,200,200,500,500,500,1000,1000,5000,5000,10000,20000);
		$price_arr_length = count($price_arr);

		$type_arr = array('买涨','买跌');
		$type_arr_length = count($type_arr);

		for($i=0;$i<$pro_length;$i++){
			$rand_pid_index = rand(0,($pro_length - 1));
			$rand_price_index = rand(0,($price_arr_length - 1));
			$rand_type_index = rand(0,($type_arr_length - 1));

			$o_pub = array();
			$o_pub['buytime'] = time();
			$o_pub['pid'] = $id_arr[$rand_pid_index];
			$o_pub['price'] = $price_arr[$rand_price_index];
			$o_pub['otype'] = $type_arr[$rand_type_index];
			db('order_pub')->insert($o_pub);
		}

		foreach($order_pub as $k => $v){
			$order_pub[$k]['buytime'] = date("H:i:s",$v['buytime']);
		}
		$this->assign('order_pub',$order_pub);
		*/




        return $this->fetch();
    }
	public function center()
	{
        return $this->fetch();
    }
	public function ajax_order(){
		$pro_length = 50;
		$phone_pre_arr = array("139","138","137","136","135","134","159","158","157","150","151","152","187","188","130","131","132","156","155","133","153","189");
		$phone_pre_length = count($phone_pre_arr);
		//$price_arr = array(100,200,300,400,500,600,700,800,500,500,1000,1000,5000,5000,10000,20000);
		//$price_arr_length = count($price_arr);
		$type_arr = array('买涨','买跌');
		$type_arr_length = count($type_arr);
		$order_pub = array();
		for($i=0;$i<$pro_length;$i++){
			//$rand_pid_index = rand(0,($pro_length - 1));
			$phone_pre_index = rand(0,($phone_pre_length - 1));
			//$rand_price_index = rand(0,($price_arr_length - 1));
			//$rand_type_index = rand(0,($type_arr_length - 1));

			$o_pub = array();
			//$o_pub['buytime'] = time();
			//$o_pub['pid'] = $id_arr[$rand_pid_index];
			$o_pub['phone'] = $phone_pre_arr[$phone_pre_index] . "****" . rand(1000,9999);


			//$o_pub['price'] = $price_arr[$rand_price_index];

			$o_pub['price'] = 50 * rand(1,20);
			if(rand(1,100)>=90){
				$o_pub['price'] = 50 * rand(20,100);
			}
			/*
			else if(rand(1,100)>=80){
				$o_pub['price'] = 50 * rand(0,100);
			}
			*/

			//$o_pub['otype'] = $type_arr[$rand_type_index];
			array_push($order_pub,$o_pub);
		}


		//foreach($order_pub as $k => $v){
			//$order_pub[$k]['buytime'] = date("H:i:s",$v['buytime']);
		//}
		 echo json_encode($order_pub);
	}
	
	
	public function uploadImg(){
			$data = '';
			foreach($_FILES as $k=>$v){
				if($v["error"])
				{
				    return $v["error"];
				}
				else
				{
				    //没有出错
				    //加限制条件
				    //判断上传文件类型为png或jpg且大小不超过1024000B
				    if(($v["type"]=="image/png"||$v["type"]=="image/jpeg"||$v["type"]=="image/jpg")&& $v["size"]<1024000)
				    {
				        //防止文件名重复
				        $path = "/upload/".date('Y-m-d')."/".time().rand(0,1000).$v["name"];
				        $filename = $_SERVER['DOCUMENT_ROOT'].$path;
				        //转码，把utf-8转成gb2312,返回转换后的字符串， 或者在失败时返回 FALSE。
				        //$filename =iconv("UTF-8","gb2312",$filename);
				        //检查文件或目录是否存在
				        if(file_exists($filename))
				        {
							return '文件已存在';
				        }
				        else
				        {
				            //保存文件,   move_uploaded_file 将上传的文件移动到新位置
				            if ( !@copy($v['tmp_name'], $file_name)){
				                $dir = $_SERVER['DOCUMENT_ROOT'].'/upload/'.date('Y-m-d');
				                if(!is_dir($dir)){
				                    mkdir($dir,0777);
				                }
				                if(move_uploaded_file($v['tmp_name'],$filename)){
				                    $data = $path;
				                }else{
									return '上传失败';
				                }
				            }
				        }
				    }else{
				        $data = 0;
						return WPreturn($data,-1);
				    }
				}
			}
			return WPreturn($data,1);
	}

    public function ajaxindexpro()
    {
    	//获取产品信息
        $pro = Db::name('productinfo')->alias('pi')->field('pi.pid,pi.ptitle,pi.proorder,pd.Price,pd.UpdateTime,pd.Low,pd.High')
        		->join('__PRODUCTDATA__ pd','pd.pid=pi.pid')
        		->where('pi.isdelete',0)->order('pi.pid desc')->select();
        $newpro = array();
        foreach ($pro as $k => $v) {
        	$newpro[$v['pid']] = $pro[$k];
        	$newpro[$v['pid']]['UpdateTime'] = date('H:i:s',$v['UpdateTime']);
            // if(!isset($_COOKIE['pid'.$v['pid']])){
            //     cookie('pid'.$v['pid'],$v['Price']);
            //     continue;
            // }
        	if($v['Price'] < cookie('pid'.$v['pid']) ){  //跌了
        		$newpro[$v['pid']]['isup'] = 0;
        	}elseif($v['Price'] > cookie('pid'.$v['pid']) ){  //涨了
        		$newpro[$v['pid']]['isup'] = 1;
        	}else{  //没跌没涨
        		$newpro[$v['pid']]['isup'] = 2;
        	}

        	cookie('pid'.$v['pid'],$v['Price']);

        }

        return base64_encode(json_encode($newpro));
    }

    public function getchart()
    {

        /*$data['hangqing'] = '商品行情';
        $data['jiaoyijilu'] = '交易记录';
        $data['shangpinmingcheng'] = '商品名称';
        $data['xianjia'] = '现价';
        $data['zuidi'] = '最低';
        $data['zuigao'] = '最高';
        $data['xianjia'] = '现价';
        $data['xianjia'] = '现价';*/
        $data['hangqing'] = lang('sphq');
        $data['jiaoyijilu'] = lang('jyjl');
        $data['shangpinmingcheng'] = lang('spmc');
        $data['xianjia'] = lang('xj');
        $data['zuidi'] = lang('zd');
        $data['zuigao'] = lang('zg');
        $res = base64_encode(json_encode($data));
        return $res;
    }





public function pay()
{

	//$arr=Db::name("userinfo")->find();
	//
//require("mysql_class.php");
//$connObj=new mySQL_Class();
$uid=$_REQUEST['uid'];
$userinfo=Db::name("userinfo")->where("`uid`='$uid'")->find();

if($userinfo['uid']<1)
{
  exit("用户不存在，充值失败");
}
$username=$userinfo['username'];
$usermoney=$userinfo['usermoney'];



$biaoti='英汇国际'.$username.'_'.$uid.'_zfb在线充值';



$type=$_REQUEST['type'];












$bptype=3;
$bptime=time();
//$bpprice='0.01';
$bpprice=$_REQUEST['price'];
//$bpprice=0.01;
//$_REQUEST['price']
$remarks='英汇国际'.$username.'_'.$uid.'_wx在线充值';
$bpbalance=$usermoney+$bpprice;//在线充值后的余额
$btime=time();
$reg_par=0;
$balance_sn=date("YmdHis");//订单号
$pay_type='weixin';


$sqlid="INSERT INTO  `wp_balance` (
`bpid` ,
`bptype` ,
`bptime` ,
`bpprice` ,
`remarks` ,
`uid` ,
`isverified` ,
`cltime` ,
`bankid` ,
`bpbalance` ,
`btime` ,
`reg_par` ,
`balance_sn` ,
`pay_type`
)
VALUES (
NULL ,  '$bptype', '".time()."' , '$bpprice' ,  '$remarks',  '$uid',  '1', NULL , NULL ,  '$bpbalance',  '".time()."',  '$reg_par',  '$balance_sn', '$pay_type'
)";


$datah['bptype']=$bptype;
$datah['bptime']=time();
$datah['bpprice']=$bpprice;
$datah['remarks']=$remarks;
$datah['uid']=$uid;
$datah['isverified']='1';
$datah['cltime']='';
$datah['bankid']='';
$datah['bpbalance']=$bpbalance;
$datah['btime']=time();
$datah['reg_par']=$reg_par;
$datah['balance_sn']=$balance_sn;
$datah['pay_type']=$pay_type;


$idnum=Db::name("balance")->query($sqlid);
if($idnum<1)
{
echo "订单无法失败，充值失败";
}
if($type=='1')
{
   $payMethod='22';
   $partner='201810231616338982';
   $key = '4B35883F9A5EE3BD2DDD2EA68E2C28FA'; //key
   $tz='tz.php';
}else{
   $payMethod='11';
   $partner='2018090523';
   $key = '458EA54C02268E214E6C3CAAE16F2453'; //key
   $tz='tz1.php';
}

$data = [
    "orderAmount"=>$bpprice, //金额
    "orderId"=>$balance_sn,//订单号
    "partner"=>$partner, //商户号
    'payMethod'=>$payMethod,
    "payType"=>"syt",
    "signType"=>"MD5",
    "version"=>"1.0",
];
$key = $key; //key
ksort($data);
$postString = http_build_query($data);
$signMyself = strtoupper(md5($postString.$key));
$data["sign"] = $signMyself;
$data['productName'] = '9677';
$data['productId'] = '9677';
$data['productDesc'] = '9677';
$data['notifyUrl'] = 'http://www.weiende.com/'.$tz;
$postString = http_build_query($data);
$url = "http://qr.sytpay.cn/api/v1/create.php?".$postString;
//echo $url;

//$url=file_get_contents($url);
//echo $url;
//exit;
header("Location: " .$url);

}































public function tz()
{
file_put_contents('bbb.txt','bbbb');
	$keySign = '4B35883F9A5EE3BD2DDD2EA68E2C28FA';//密钥
	$returnText = file_get_contents('php://input');
	 $post='||';
	$post = $returnText;




	$posts = explode('|',$post);
	$sign = $posts[0];


	if(count($posts)<2)
	{

	   $posts[1]='';

	}
	$paramsJson = $posts[1];





	$paramsJsonBase64 = base64_encode($paramsJson);

	$paramsJsonBase64Md5 = md5($paramsJsonBase64);
	$signMyself = strtoupper(md5($keySign.$paramsJsonBase64Md5));
	if($sign != $signMyself){
		echo 'sign Error';
		$params = json_decode($paramsJson,true);
			file_put_contents('zzz_errors11.txt',$params);

	}
	else{
		//获取传递的值
		$params = json_decode($paramsJson,true);
		//print_r($params[data]);
		//$ccc=var_dump();
		//exit();
		$orderId=$params[data]['orderId'];
		$trade_no=$params[data]['trade_no'];
		$outTradeNo=$params[data]['outTradeNo'];
		$orderAmount=$params[data]['orderAmount'];
		$dateTime=$params[data]['dateTime'];

	    file_put_contents('zzzorderId.txt',$orderId);
	    file_put_contents('zzztrade_no.txt',$trade_no);
	    file_put_contents('zzzoutTradeNo.txt',$outTradeNo);
	    file_put_contents('zzzorderAmount.txt',$orderAmount);
	    file_put_contents('zzzdateTime.txt',$dateTime);
	   // file_put_contents('wx_params11=.txt',$arr['message']);
	    //file_put_contents('wx_params11==.txt',$paramsJson['orderId']);

		// 这里会有一些逻辑的,具体有商户定义


$balance_sn=$orderId;
$orderarr=Db::name("balance")->where("`balance_sn`='$balance_sn'")->find();
if($orderarr['bptype']=='1')
{
	exit();
}
if($orderarr['uid']<0)
{
	$arr=file_put_contents("zzzzfbbalance.txt",Db::name("balance")->getLastSql());
	exit();
}
$userinfo=Db::name("userinfo")->where("`uid`='$orderarr[uid]'")->find();
if($userinfo['uid']<0)
{
	$arr=file_put_contents("zzzuserinfo.txt",Db::name("userinfo")->getLastSql());
	exit();
}
$total_fee=$orderAmount;
Db::name("userinfo")->query("UPDATE `wp_userinfo` SET `usermoney` = usermoney+$total_fee WHERE `uid` ='$userinfo[uid]'");
$arr=file_put_contents("zzzzfbUPDATE_userinfo.txt",Db::name("userinfo")->getLastSql());
Db::name("balance")->query("UPDATE `wp_balance` SET `bptype` = '1' WHERE `balance_sn` ='$balance_sn'");
$arr=file_put_contents("zzzzfbUPDATE_balance.txt",Db::name("balance")->getLastSql());



















		//成功一定要输出success这七个英文字母.
		echo 'success';
	}

	   }



    public function enter()
    {

        return $this->fetch();
    }

    public function newinfo()
    {

        return $this->fetch();
    }

    public function detail()
    {
        $id = input('param.id');

        return $this->fetch('/news/'.$id);
    }


    public function getgogao(){
        $dbgg=Db::name("config")->where("name='web_qiantai'")->select();

        $webgg=$dbgg[0]['value']?$dbgg[0]['value']:'';

        return $webgg;

    }

    public function hangqing(){
        return $this->fetch();

    }





public function tz1()
{

	$keySign = '458EA54C02268E214E6C3CAAE16F2453';//密钥
	$returnText = file_get_contents('php://input');
	$post = $returnText;






	$posts = explode('|',$post);
	$sign = $posts[0];
	$paramsJson = $posts[1];





	$paramsJsonBase64 = base64_encode($paramsJson);

	$paramsJsonBase64Md5 = md5($paramsJsonBase64);
	$signMyself = strtoupper(md5($keySign.$paramsJsonBase64Md5));
	if($sign != $signMyself){
		echo 'sign Error';
		$params = json_decode($paramsJson,true);
			file_put_contents('wx_errors11.txt',$params);

	}
	else{
		//获取传递的值
		$params = json_decode($paramsJson,true);
		//print_r($params[data]);
		//$ccc=var_dump();
		//exit();
		$orderId=$params[data]['orderId'];
		$trade_no=$params[data]['trade_no'];
		$outTradeNo=$params[data]['outTradeNo'];
		$orderAmount=$params[data]['orderAmount'];
		$dateTime=$params[data]['dateTime'];

	    file_put_contents('orderId.txt',$orderId);
	    file_put_contents('trade_no.txt',$trade_no);
	    file_put_contents('outTradeNo.txt',$outTradeNo);
	    file_put_contents('orderAmount.txt',$orderAmount);
	    file_put_contents('dateTime.txt',$dateTime);
	   // file_put_contents('wx_params11=.txt',$arr['message']);
	    //file_put_contents('wx_params11==.txt',$paramsJson['orderId']);

		// 这里会有一些逻辑的,具体有商户定义


$balance_sn=$orderId;
$orderarr=M("balance")->where("`balance_sn`='$balance_sn'")->find();
if($orderarr['bptype']=='1')
{
	exit();
}
if($orderarr['uid']<0)
{
	$arr=file_put_contents("zfbbalance.txt",M("balance")->getLastSql());
	exit();
}
$userinfo=M("userinfo")->where("`uid`='$orderarr[uid]'")->find();
if($userinfo['uid']<0)
{
	$arr=file_put_contents("userinfo.txt",M("userinfo")->getLastSql());
	exit();
}
$total_fee=$orderAmount;
M("userinfo")->query("UPDATE `wp_userinfo` SET `usermoney` = usermoney+$total_fee WHERE `uid` ='$userinfo[uid]'");
$arr=file_put_contents("zfbUPDATE_userinfo.txt",M("userinfo")->getLastSql());
M("balance")->query("UPDATE `wp_balance` SET `bptype` = '1' WHERE `balance_sn` ='$balance_sn'");
$arr=file_put_contents("zfbUPDATE_balance.txt",M("balance")->getLastSql());



















		//成功一定要输出success这七个英文字母.
		echo 'success';
	}





}






























}
