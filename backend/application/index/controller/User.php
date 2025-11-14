<?php
namespace app\index\controller;
use think\Db;

class User extends Base
{
	/**

	 * 用户个人中心首页

	 * @author lukui  2017-07-21

	 * @return [type] [description]

	 */

	public function index()

	{

		$uid = $this->uid;;

		$user = Db::name('userinfo')->where('uid',$uid)->find();
		//出金------------------------------------------

		//银行卡

		$data['banks'] = db('banks')->select();
		//地区

		$province = db('area')->where(array('pid'=>0))->select();
        //已签约信息

        // $data['mybank'] = db('bankcard')->where('uid',$uid)->find();
        // $data['mybank'] = db('bankcard')->where('uid',$uid)->find();
		$data['mybank'] = db('bankcard')->alias('bc')->field('bc.*,bs.bank_nm')
						->join('__BANKS__ bs','bs.id=bc.bankno')
						->where('uid',$uid)
						->find();

        //虚拟币钱包查询
       // $data['mybank'] = db('bankcard')->field('bit,eth,usdt,id')->where('uid',$uid)->find();
        //资金流水

        $data['order_list'] = db('price_log')->where('uid',$uid)->order('id desc')->limit(0,20)->select();

        //dump($data['order_list']);

        //充值方式

        $payment = db('payment')->where(array('isdelete'=>0,'is_use'=>1))->order('pay_order desc ')->select();

        if($payment){

        	$arr2 = $arr = $arr1 = array();

        	foreach ($payment as $key => $value) {





        		$arr1 = explode('|',trimall($value['pay_conf']));



				foreach ($arr1 as $k => $v) {

					$arr2 = explode(':',trimall($v));

					if(isset($arr2[0]) && isset($arr2[1])){

						$arr[$arr2[0]] = $arr2[1];

					}





				}

				$payment[$key]['pay_conf_arr'] = $arr;





        	}

        }



        //推广二维码

        if($user['otype'] == 101){

        	$oid = $uid;

        }else{

        	$oid = $user['oid'] ;

        }

        $data['oid_url'] = "http://".$_SERVER['SERVER_NAME'].'?fid='.$oid;
        //dump($payment);exit;


        // $data['sub_bankno'] = substr($data['mybank']['accntno'],-4,4); //截取银行卡号
		$data['sub_bankno'] = $data['mybank']['accntno']; //截取银行卡号
        //$data['sub_bankno'] = 'delete';



        //入金金额

        $reg_push = $this->conf['reg_push'];

        if($reg_push){

        	$reg_push = explode('|',$reg_push);

        }



        //公告

		$dbgg=Db::name("config")->where("name='web_qiantai'")->select();
		
		//平台规则   中英文选择
		$plarule = '';
		if(cookie('think_var')=='zh-cn'){
		     $ruleconfig=Db::name("config")->where("name='pla_rule_cn'")->select();
		     $plarule = $ruleconfig[0]['value']?$ruleconfig[0]['value']:'';
		}else if(cookie('think_var')=='zh-ja'){
		     $ruleconfig=Db::name("config")->where("name='pla_rule_ja'")->select();
		     $plarule = $ruleconfig[0]['value']?$ruleconfig[0]['value']:'';
		}else{
		     $ruleconfig=Db::name("config")->where("name='pla_rule_en'")->select();
		     $plarule = $ruleconfig[0]['value']?$ruleconfig[0]['value']:'';
		}
		
	   //今日客户盈亏
        $tody_profit = Db::name('order')->where('uid',$uid)
            ->field('SUM(ploss) as ploss')->whereTime('buytime', 'd')->select();
        $tody_profit = $tody_profit[0]['ploss'] ? $tody_profit[0]['ploss'] : 0;
        $this->assign('tody_profit', $tody_profit);

        $webgg=$dbgg[0]['value']?$dbgg[0]['value']:'';
       
        $qqkf = $this->conf['web_qqkf'];
         //增加公告
        $this->assign('plarule',$plarule);

	    $this->assign('webgg',$webgg);
        $this->assign('qqkf',$qqkf);
        $this->assign('province',$province);

		$this->assign($data);

		$this->assign('payment',$payment);

		$this->assign('reg_push',$reg_push);

		return $this->fetch();

	}


	/**

	 * 现金充值

	 * @author lukui  2017-07-21

	 * @return [type] [description]

	 */

	public function recharge()

	{

		if(input('post.')){

			$data = input('post.');

			if(isset($data['wxpay']) && $data['wxpay']){

				//微信充值：

			}

		}else{

			$uid = $this->uid;;

			$user = Db::name('userinfo')->field('usermoney')->where('uid',$uid)->find();

			$this->assign($user);

			return $this->fetch();

		}



	}





	/**

	 * 用户提现

	 * @author lukui  2017-07-21

	 * @return [type] [description]

	 */

	public function cash()

	{

		$uid = $this->uid;

		if(input('post.')){

			$data = input('post.');



			if($data){

				if(!$data['price']){

					return WPreturn(lang('qsrtxje'),-1);

				}
                if(!$data['paypwd']){

					return WPreturn(lang('qsrtxmm'),-1);

				}
				//验证申请金额

				$user = $this->user;

				if($user['ustatus'] != 0){

					return WPreturn(lang('wfcj'),-1);

				}

				$conf = $this->conf;





				if($conf['is_cash'] != 1){

					return WPreturn(lang('wfcj'),-1);

				}

				if($conf['cash_min'] > $data['price']){

					return WPreturn(lang('dbzdtx').$conf['cash_min'],-1);

				}

				if($conf['cash_max'] < $data['price']){

					return WPreturn(lang('dbzgtx').$conf['cash_max'],-1);

				}



				$_map['uid'] = $uid;

				$_map['bptype'] = 0;

				$cash_num = db('balance')->where($_map)->whereTime('bptime', 'd')->count();



				if($cash_num + 1 > $conf['day_cash']){

					return WPreturn(lang('mrzdtxcs').$conf['day_cash'],-1);

				}

				$cash_day_max = db('balance')->where($_map)->whereTime('bptime', 'd')->sum('bpprice');

				if($conf['cash_day_max'] < $cash_day_max + $data['price']){

					return WPreturn(lang('drtxjesx').$conf['cash_day_max'],-1);

				}



                

	 

				$statrdate=Db::name("config")->where("name='role_ks'")->select();

                $txstatrdate=$statrdate[0]['value']?$statrdate[0]['value']:9;

				

				

				$enddate=Db::name("config")->where("name='role_js'")->select();

                $txenddate=$enddate[0]['value']?$enddate[0]['value']:17;

				



				/*if(date('H') < intval($txstatrdate) || date('H') >  intval($txenddate)){

					return WPreturn('出金时间为'.$txstatrdate.'-'.$txenddate.'点',-1);

				}*/

				

				//代理商的话判断金额是否够

				if($this->user['otype'] == 101){

					if( ($this->user['usermoney'] - $data['price']) < $this->user['minprice'] ){

						return WPreturn(lang('bzjbndy').$this->user['minprice'],-1);

					}

				}



				if($this->user['otype'] == 0){

					if (($this->user['usermoney'] - $data['price']) < 0) {

						return WPreturn(lang('zdntx').$this->user['usermoney'],-1);

					}

				}



				if( ($this->user['usermoney'] - $data['price']) < 0){

					return WPreturn(lang('zdntx').$this->user['usermoney'],-1);

				}

				//签约信息

				$mybank = db('bankcard')->where('uid',$uid)->find();
                if($user['paypwd'] != $data['paypwd']){
                    return WPreturn(lang('zjmmcw'),-1);
                }

				//提现申请

				$newdata['bpprice'] = $data['price'];

				$newdata['bptime'] = time();

				$newdata['bptype'] = 0;

				$newdata['remarks'] = '会员提现';

				$newdata['uid'] = $uid;

				$newdata['isverified'] = 0;

				$newdata['bpbalance'] = 0;

				$newdata['bankid'] = $mybank['id'];

				$newdata['btime'] = time();

				$newdata['reg_par'] = $conf['reg_par'];







				$bpid = Db::name('balance')->insertGetId($newdata);

				if($bpid){

					//插入申请成功后,扣除金额

					$editmoney = Db::name('userinfo')->where('uid',$uid)->setDec('usermoney',$data['price']);

					if($editmoney){

						//插入此刻的余额。

						$usermoney = Db::name('userinfo')->where('uid',$uid)->value('usermoney');

						Db::name('balance')->where('bpid',$bpid)->update(array('bpbalance'=>$usermoney));



						//资金日志

       					set_price_log($uid,2,$data['price'],'提现','提现申请',$bpid,$usermoney);



						return WPreturn(lang('txcg'),1);

					}else{

						//扣除金额失败，删除提现记录

						Db::name('balance')->where('bpid',$bpid)->delete();

						return WPreturn(lang('txsb'),-1);

					}



				}else{

					return WPreturn(lang('txsb'),-1);

				}







			}else{

				return WPreturn('Erro！',-1);

			}

		}else{



			$user = Db::name('userinfo')->field('usermoney')->where('uid',$uid)->find();

			$this->assign($user);

			return $this->fetch();

		}

	}





	/**

	 * 提现记录

	 * @author lukui  2017-07-24

	 * @return [type] [description]

	 */

	public function income()

	{



		$where['uid'] = $this->uid;;

		$where['bptype'] = 0;



		$list = Db::name('balance')->where($where)->order('bpid desc')->paginate(20);



		$this->assign('list',$list);

		return $this->fetch();

	}





	/**

	 * 充值记录

	 * @author lukui  2017-07-24

	 * @return [type] [description]

	 */

	public function rechargelist()

	{



		return $this->fetch();

	}



  











	/**

	 * 用户资金明细

	 * @author lukui  2017-07-21

	 * @return [type] [description]

	 */

	public function orders()

	{

		$uid = $this->uid;;

		$where['uid'] = $uid;

		$where['ostaus'] = 1;

		if(input('param.month')){

			$month = input('param.month');

		}else{

			$month = date("m");

		}

		if(input('param.years')){

			$years = input('param.years');

		}else{

			$years = date("Y");

		}



		//当月时间戳

		$BeginDate = date('Y-m-d',strtotime($years.'-'.$month.'-01'));

		$EndDate = date('Y-m-d', strtotime("$BeginDate +1 month -1 day"));

		$BeginDate = strtotime($BeginDate);

		$EndDate = strtotime($EndDate);





		$where['buytime'] = array('between', [$BeginDate, $EndDate]);

		//订单

		$order = Db::name('order')->where($where)->order('oid desc')->paginate(10);



		if(input('get.page')){  //ajax请求的



			return $order;

		}else{

			//总盈亏

			$data['allincome'] = Db::name('order')->where($where)->sum('ploss');

			//总手数

			$data['count'] = Db::name('order')->where($where)->count();

			$data['date'] = $years.'-'.$month;



			if($month == 12){

				$next['month'] = 1;

				$next['years'] = $years + 1;

			}else{

				$next['month'] = $month + 1;

				$next['years'] = $years;

			}



			if($month == 1){

				$over['month'] = 12;

				$over['years'] = $years - 1;

			}else{

				$over['month'] = $month - 1;

				$over['years'] = $years;

			}







			$this->assign('next',$next);

			$this->assign('over',$over);

			$this->assign($data);

			$this->assign('order',$order);

			return $this->fetch();

		}



	}







	/**

	 * 用户积分

	 * @author lukui  2017-07-21

	 * @return [type] [description]

	 */

	public function integral()

	{

		$uid = $this->uid;;

		$point = Db::name('userinfo')->where('uid',$uid)->value('userpoint');

		//进入是否签到

		$isregister = Db::name('integral')->where(array('uid'=>$uid,'type'=>1))->whereTime('time', 'd')->select();



		$this->assign('isregister',$isregister);

		$this->assign('point',$point);

		return $this->fetch();

	}



	/**

	 * 签到处理

	 * @author lukui  2017-07-21

	 * @return [type] [description]

	 */

	public function dointegral()

	{

		$uid = $this->uid;;

		//是否签到

		$isregister = Db::name('integral')->where(array('uid'=>$uid,'type'=>1))->whereTime('time', 'd')->select();

		if(empty($isregister) ){ //签到

			//积分流水表 并增加积分

        	$i_data['type'] = 1;

        	$i_data['amount'] = 50;

        	$i_data['time'] = time();

        	$i_data['uid'] = $uid;

        	$add = Db::name('integral')->insert($i_data);

        	//会员增加积分

        	Db::name('userinfo')->where('uid',$uid)->setInc('userpoint',$i_data['amount']);

        	if($add){

        		return WPreturn('签到成功',1);

        	}else{

        		return WPreturn('签到失败，请重试',-1);

        	}

		}else{

			return WPreturn('您今天已签到',-1);

		}

	}





	/**

	 * 积分列表

	 * @author lukui  2017-07-21

	 * @return [type] [description]

	 */

	public function integralInfos()

	{

		$uid = $this->uid;;



		$integral = Db::name('integral')->where('uid',$uid)->order('id desc')->paginate(20);



		if(input('get.page')){

			return $integral;

		}else{

			$this->assign('integral',$integral);

			return $this->fetch();

		}

	}





	/**

	 * 用户积分明细

	 * @author lukui  2017-07-24

	 * @return [type] [description]

	 */

	public function integraldetail()

	{

		$uid = $this->uid;;

		$id = input('param.id');

		$integral = Db::name('integral')->where('id',$id)->find();

		if($integral['oid']){  //微交易的  查询下 微交易的订单。

			$order = Db::name('order')->where('oid',$integral['oid'])->find();

			$integral['orderno'] = $order['orderno'];

			$integral['ostaus'] = $order['ostaus'];

			$integral['ptitle'] = $order['ptitle'];

			$integral['fee'] = $order['fee'];

			$integral['buytime'] = $order['buytime'];



		}

		$this->assign($integral);

		return $this->fetch();

	}





	/**

	 * 修改登录密码

	 * @author lukui  2017-07-24

	 * @return [type] [description]

	 */

	public function editpwd()

	{



		$uid = $this->uid;;

		//查找用户是信息

        $user = Db::name('userinfo')->where('uid',$uid)->field('upwd,utime')->find();



        //添加密码

        if(input('post.')){

            $data = input('post.');

            if(!isset($data['oldpwd']) || empty($data['oldpwd'])){

                return WPreturn('请输入原始密码！',-1);

            }

            //验证密码

            if($user['upwd'] != md5($data['oldpwd'].$user['utime'])){

            	return WPreturn('原始密码错误，请重试！',-1);

            }

            if(!isset($data['newpwd']) || empty($data['newpwd'])){

                return WPreturn('请输入新登录密码！',-1);

            }

            if(!isset($data['newpwd2']) || empty($data['newpwd2'])){

                return WPreturn('请确认新登录密码！',-1);

            }

            if($data['newpwd'] != $data['newpwd2']){

                return WPreturn('两次输入密码不同！',-1);

            }

            if($data['oldpwd'] == $data['newpwd']){

            	return WPreturn('请不要修改为原始密码！',-1);

            }



            $adddata['upwd'] = trim($data['newpwd']);

            $adddata['upwd'] = md5($adddata['upwd'].$user['utime']);

            $adddata['uid'] = $uid;



            $newids = Db::name('userinfo')->update($adddata);

            if ($newids) {

                return WPreturn(lan('czcg'),1);

            }else{

                return WPreturn(lan('czsb'),-1);

            }



        }





        return $this->fetch();



	}





	/**

	 * 实名认证

	 * @author lukui  2017-07-24

	 * @return [type] [description]

	 */

	public function autonym()

	{



		return $this->fetch();

	}







	/**

     * 获取城市

     * @author lukui  2017-04-24

     * @return [type] [description]

     */

    public function getarea()

    {



        $id = input('id');

        if(!$id){

            return false;

        }



        $list = db('area')->where('pid',$id)->select();

        $data = '<option value="">请选择</option>';

        foreach ($list as $k => $v) {

            $data .= '<option value="'.$v['id'].'">'.$v['name'].'</option>';

        }

        echo $data;



    }



    /**

     * 签约

     * @author lukui  2017-07-03

     * @return [type] [description]

     */

    public function dobanks()
    {
    	$post = input('post.');
    	foreach ($post as $k => $v) {
    		if(empty($v)){
    			return WPreturn($v.'请正确填写信息！'.$k,-1);
    		}
    		$post[$k] = trim($v);
    	}

    	if(isset($post['id']) && !empty($post['id'])){
    		unset($post['user_id']);
    		$ids = db('bankcard')->update($post);
    	}else{
    		unset($post['id']);
    		unset($post['user_id']);
    		$post['uid'] = $this->uid;
    		$ids = db('bankcard')->insert($post);
    	}

    	if ($ids) {
            return WPreturn(lang('czcg'),1);
        }else{
            return WPreturn(lang('czsb'),-1);
        }
    }







    public function ajax_price_list()

    {

    	$uid = $this->uid;



    	$list = db('price_log')->where('uid',$uid)->order('id desc')->paginate(20);

    	return $list;



    }



   	public function addbalance()
   	{
   		$post = input('post.');
	
   		if(!$post){
   			$this->error(lang('cscw'));
   		}
	
   		if(!$post['paytype'] || !$post['rechargemoney']){
   			return WPreturn(lang('cscw'),-1);
   		}

   		// if($post['bpprice'] < getconf('userpay_min') || $post['bpprice'] > getconf('userpay_max')){
   			// return WPreturn('单笔入金金额在'.getconf('userpay_min').'-'.getconf('userpay_max').'之间',-1);
   		// }

//   		if(!strpos($post['rechargemoney'],'.')){
//   			return WPreturn('请输入.01-.09结尾小数 例如104.03',-1);
//   		}

   		#$post['bpprice'] =  1;
		

   		$uid = $this->uid;
   		$user = $this->user;
   		$nowtime = time();

   		//插入充值数据
        $data['bptype'] = 3;
        $data['bptime'] = $nowtime;
        $data['bpprice'] = $post['rechargemoney'];
        $data['remarks'] = '前台提交充值订单';
        $data['uid'] = $uid;
        $data['isverified'] = 0;
        $data['btime'] = $nowtime;
        $data['reg_par'] = 0;
        $data['balance_sn'] = $uid.$nowtime.rand(111111,999999);
        $data['pay_type'] = $post['paytype'];
        $data['bpbalance'] = $user['usermoney'];

        $ids = db('balance')->insertGetId($data);
        if(!$ids){
            return WPreturn(lang('wlyc'),-1);
        }else{
            return WPreturn(lang('tjcg'),1);
        }
//   		$data['bpid'] = $ids;
		
//   		$Pay = controller('Pay');
		
//   		//支付类型
//   		if($post['pay_type'] == 'qbt_pay_wxpay'){
//   			$res = $Pay->qianbaotong($data,1007);
//   			return $res;
//   		}
//   		if($post['pay_type'] == 'qtb_alipay'){
//   			$res = $Pay->qianbaotong($data,1006);
   			
//   			return $res;
//   		}
		
		
// 		if($post['pay_type'] == 'yupay'){
//   			$res = $Pay->yupay($data);
//   			return $res;
//   		}
   		
//   		if($post['pay_type'] == 'wxpay'){
   			
//   			$res = $Pay->wxpay($data);
//   			return $res;
//   		}
//   		if($post['pay_type'] == 'zypay'){
//   			$res = $Pay->zypay($data);
//   			return $res;
//   		}
//   		if($post['pay_type'] == 'bywxscan'){
//   			$res = $Pay->bypay($data,'Ucwxscan');
//   			return $res;
//   		}
// 		if($post['pay_type'] == 'byaliscan'){
//   			$res = $Pay->bypay($data,'Ucaliscan');
//   			return $res;
//   		}
// 		if($post['pay_type'] == 'bywxpay'){
//   			$res = $Pay->bypay($data,'Ucwxh5');
//   			return $res;
//   		}
//   		if($post['pay_type'] == 'alipay'){
//   			$res = $Pay->alipay($data);
   			
//   			return $res;
//   		}
//   		if($post['pay_type'] == 'qtb_yinlian'){
//   			$res = $Pay->qianbaotong($data,1005);
   			
//   			return $res;
//   		}
		
// 		//二维码-通用
		
//   		if($post['pay_type'] == 'zfb_zz'){
//   			$res = "/index/user/zfb";
   			
//   			return $res;
//   		}
//   		if($post['pay_type'] == 'wx_zz'){
//   			$res = "/index/user/wx";
   			
//   			return $res;
//   		}
// 		//青果畅付
		
//   		if($post['pay_type'] == 'qgcf_zfb'){
//   			$res = $Pay->hkbgo($data,1);
   			
//   			return $res;
//   		}
//   		if($post['pay_type'] == 'qgcf_wx'){
//   			$res = $Pay->hkbgo($data,2);
   			
//   			return $res;
//   		}
   	}



   	/**

   	 * 提现列表

   	 * @author lukui  2017-09-04

   	 * @return [type] [description]

   	 */

   	public function cashlist()

   	{

   		$map['uid'] = $this->uid;
   		//$map['bptype'] = 0;
   		$map['bptype'] = ['<',2];
   		$list = db('balance')->where($map)->order('bpid desc')->select();
   		$this->assign('list',$list);
   		return $this->fetch();

   	}





   	/**

   	 * 充值列表

   	 * @author lukui  2017-09-04

   	 * @return [type] [description]

   	 */

   	public function reglist()

   	{

   		

   		$map['uid'] = $this->uid;

   		$map['bptype'] = 1;



   		$list = db('balance')->where($map)->order('bpid desc')->select();



   		$this->assign('list',$list);



   		return $this->fetch();

   	}



   	/**

   	 * 二维码

   	 * @author lukui  2017-09-04

   	 * @return [type] [description]

   	 */

   	public function ercode()

   	{

   		



   		$user = $this->user;



   		//推广二维码

        if($user['otype'] == 101){

        	$oid = $this->uid;

        }else{

        	$oid = $user['oid'] ;

        }

        $oid_url = "http://".$_SERVER['SERVER_NAME'].'?fid='.$oid;

   		$this->assign('oid_url',$oid_url);

   		return $this->fetch();

   	}



   	public function mcpay()

   	{

   		

  

   		$id = input('id');

   		if(!$id){

   			$this->error('参数错误！');

   		}



   		$balance = db('balance')->where('bpid',$id)->find();

   		if(!$balance){

   			$this->error('参数错误！');

   		}

		

	 $pay_type=	trim($balance['pay_type']);

	 if($pay_type=='qd_wxpay'){

		$typ="5";

	}else if($pay_type=='qd_wxpay2'){

		$typ="5";

	}else if($pay_type=='qd_alipay2'){

		$typ="2";

	}else if($pay_type=='qd_qqpay2'){

		$typ="4";	

	}else{

		$typ="5";	

	}

		

		

		

   		$appid="2018040259";//扫码应用APPID

		$username=$balance['balance_sn'];///调用网站前台登录的用户名;

		$back_url='http://'.$_SERVER['HTTP_HOST'].'/index/pay/mcb_notify';//成功返回页面

		//$back_url=urlencode($back_url);



         $this->assign('typ',$typ);

		$this->assign('balance',$balance);

		$this->assign('appid',$appid);

		$this->assign('back_url',$back_url);

		$this->assign('username',$username);

		return $this->fetch();

   	}

	public function zxwxzf(){

		$user = $this->user;

		$money = $_GET['money'];

		//$money = 1;

		$merchant_id = '8032';  //商家Id

		$merchant_key = '1539f98fe5e444a0b20aaf826b88d4f6'; //商家密钥

		$bankType = '1007';   //商家密钥

		$amount = $money;    //提交金额

		$order_id = (string) date("YmdHis");   //订单Id号

		$bank_callback_url = "http://m.bfdee.cn/index/pay/cardpay"; //下行url地址 回调

		$bank_hrefbackurl = "http://m.bfdee.cn/index/pay/cardpay"; //下行url地址  跳转

		$date['bptype'] = 3;

		$date['bptime'] = time();

		$date['bpprice'] = $amount;

		$date['uid'] = $user['uid'];

		$date['btime'] = time();

		$date['balance_sn'] = $order_id;

		$date['pay_type'] = 'qtbwxpay';

		$date['remarks'] = '会员充值';

		db('balance')->insertGetId($date);

		$url = "parter=". $merchant_id ."&type=". $bankType ."&value=". $amount . "&orderid=". $order_id ."&callbackurl=". $bank_callback_url;

		//签名

		$sign	= md5($url. $merchant_key);

		

		//最终url

		$url	= 'http://gateway.qpabc.com/bank/' . "?" . $url . "&sign=" .$sign. "&hrefbackurl=". $bank_hrefbackurl;				

		

		//页面跳转

		header("location:" .$url);

	}

	public function zxzfbzf(){

		$user = $this->user;

		$money = $_GET['money'];

		$merchant_id = '8032';  //商家Id

		$merchant_key = '1539f98fe5e444a0b20aaf826b88d4f6'; //商家密钥

		$bankType = '1006';   //商家密钥

		$amount = $money;    //提交金额

		$order_id = (string) date("YmdHis");   //订单Id号

		$bank_callback_url = "http://m.bfdee.cn/index/pay/cardpay"; //下行url地址 回调

		$bank_hrefbackurl = "http://m.bfdee.cn"; //下行url地址  跳转

		$date['bptype'] = 3;

		$date['bptime'] = time();

		$date['bpprice'] = $amount;

		$date['uid'] = $user['uid'];

		$date['btime'] = time();

		$date['balance_sn'] = $order_id;

		$date['pay_type'] = 'qtbzfbpay';

		$date['remarks'] = '支付宝充值';

		db('balance')->insertGetId($date);

		$url = "parter=". $merchant_id ."&type=". $bankType ."&value=". $amount . "&orderid=". $order_id ."&callbackurl=". $bank_callback_url;

		//签名

		$sign	= md5($url. $merchant_key);

		

		//最终url

		$url	= 'http://gateway.qpabc.com/bank/' . "?" . $url . "&sign=" .$sign. "&hrefbackurl=". $bank_hrefbackurl;				

		

		//页面跳转

		header("location:" .$url);

	}

	public function zxylzf(){

		$user = $this->user;

		$money = $_GET['price'];

		$merchant_id = '8032';  //商家Id

		$merchant_key = '1539f98fe5e444a0b20aaf826b88d4f6'; //商家密钥

		$bankType = $_GET['banktype'];   

		$amount = $money;    //提交金额

		$order_id = (string) date("YmdHis");   //订单Id号

		$bank_callback_url = "http://m.bfdee.cn/index/pay/cardpay"; //下行url地址 回调

		$bank_hrefbackurl = "http://m.bfdee.cn"; //下行url地址  跳转

		$date['bptype'] = 3;

		$date['bptime'] = time();

		$date['bpprice'] = $amount;

		$date['uid'] = $user['uid'];

		$date['btime'] = time();

		$date['balance_sn'] = $order_id;

		$date['pay_type'] = 'qtbyl';

		$date['remarks'] = '会员充值';

		db('balance')->insertGetId($date);

		$url = "parter=". $merchant_id ."&type=". $bankType ."&value=". $amount . "&orderid=". $order_id ."&callbackurl=". $bank_callback_url;

		//签名

		$sign	= md5($url. $merchant_key);

		

		//最终url

		$url	= 'http://gateway.qpabc.com/bank/' . "?" . $url . "&sign=" .$sign. "&hrefbackurl=". $bank_hrefbackurl;				

		

		//页面跳转

		header("location:" .$url);

	}

	public function zxqqsmzf(){

		$user = $this->user;

		$money = $_GET['money'];

		$merchant_id = '8032';  //商家Id

		$merchant_key = '1539f98fe5e444a0b20aaf826b88d4f6'; //商家密钥

		$bankType = '1008';   //商家密钥

		$amount = $money;    //提交金额

		$order_id = (string) date("YmdHis");   //订单Id号

		$bank_callback_url = "http://m.bfdee.cn/index/pay/cardpay"; //下行url地址 回调

		$bank_hrefbackurl = "http://m.bfdee.cn"; //下行url地址  跳转

		$date['bptype'] = 3;

		$date['bptime'] = time();

		$date['bpprice'] = $amount;

		$date['uid'] = $user['uid'];

		$date['btime'] = time();

		$date['balance_sn'] = $order_id;

		$date['pay_type'] = 'qtbzfbpay';

		$date['remarks'] = '支付宝充值';

		db('balance')->insertGetId($date);

		$url = "parter=". $merchant_id ."&type=". $bankType ."&value=". $amount . "&orderid=". $order_id ."&callbackurl=". $bank_callback_url;

		//签名

		$sign	= md5($url. $merchant_key);

		

		//最终url

		$url	= 'http://gateway.qpabc.com/bank/' . "?" . $url . "&sign=" .$sign. "&hrefbackurl=". $bank_hrefbackurl;				

		

		//页面跳转

		header("location:" .$url);

	}

	public function zfb()
	{

		return $this->fetch();
	}
	
	public function wx()
	{

		return $this->fetch();
	}

    public function quanyi()
    {
        $uid = $this->uid;;

        $user = Db::name('userinfo')->where('uid',$uid)->find();
        $this->assign('user',$user);
        return $this->fetch();
    }

    public function kf()
    {
        $uid = $this->uid;;

        $user = Db::name('userinfo')->where('uid',$uid)->find();
        //header('Location:http://baidu.com&uid='. $user['uid'] . '&u='. $user['uid'] .'['. $user['nickname'] .']');
        header('Location:'.$this->conf['web_qqkf']);

    }
    //查看下线
    public function useragent(){
        
        
        $map['oid'] = $this->uid;


   		$list = db('userinfo')->where($map)->order('uid desc')->select();
   		
   	 /* $reTotal = db('banlance')->where($map)->order('uid desc')->select();
   	    
   	    $reTotal3 =  Db::name('userinfo')->alias('u')->field('u.*,u.username,u.nickname,u.oid')
					->join('__BANLANCE__ b','u.uid=b.uid')
					->where($where)->sum('bpprice');
*/


   		$this->assign('list',$list);



   		return $this->fetch();
        
    }
    
    //-------------------uniapp新增接口-------------------------------
    //用户信息获取
    public function user_info(){
        $uid = $this->uid;
		$user = Db::name('userinfo')->where('uid',$uid)->find();
		return WPreturn($user,1);
    }
    
    //利息宝数据
    public function lixibao_shou(){
        $uid = $this->uid;;
        $user = Db::name('userinfo')->where('uid',$uid)->find();
        $shouyi = Db::name('lixibao')->where('uid',$uid)->sum("shouyi");
        $start_time = strtotime(date("Y-m-d",strtotime("-1 day")).' 00:00:00');
        $end_time = strtotime(date("Y-m-d",strtotime("-1 day")).' 23:59:00');
        $where['uid'] = $uid;
        $where['time'] = array('between', [$start_time, $end_time]);
        $before_shouyi = Db::name('lixibao')->where($where)->sum("shouyi");
        $shouyi_list = Db::name('lixibao')->where('uid',$uid)->select();
		return WPreturn(['shouyi'=>$shouyi?$shouyi:0, 'before_shouyi'=>$before_shouyi?$before_shouyi:0, 'shouyi_list'=>$shouyi_list],1);
    }
    
    //银行卡
    public function user_bank_info(){
        $uid = $this->uid;
        $bankcard = Db::name('bankcard')->alias('bc')->field('bc.*,bs.bank_nm')
			->join('__BANKS__ bs','bs.id=bc.bankno')
			->where('uid',$uid)
			->find();
		return WPreturn($bankcard,1);
    }
    
    //资金流水
    public function zijinliushui(){
        $map['uid'] = $this->uid;
   		$map['bptype'] = ['<',2];
   		$list = Db::name('balance')->where($map)->order('bpid desc')->select();
		return WPreturn($list,1);
    }
    
    //实名认证
    public function shi(){
        $uid = $this->uid;
        $shi = DB::name('user_shi')->where('uid', $uid)->order('id desc')->find();
        
        $data = input('post.');
        if($shi){
            if($shi['status'] == 0){
                return WPreturn(lang('dengdaishenhe'),-1);
            }
            
            if($shi['status'] == 1){
			    return WPreturn(lang('yitijiao'),-1);
            }
            
            if($shi['status'] == 2){
                DB::name('user_shi')->where('id', $shi['id'])->update([
                    'uid' => $uid,
                    'name' => $data['name'],
                    'code' => $data['code'],
                    'img1' => $data['img1'],
                    'img2' => $data['img2'],
                    'times' => time(),
                    'status' => 0,
                ]);
            }
        }else{
            $shi = DB::name('user_shi')->insert([
                'uid' => $uid,
                'name' => $data['name'],
                'code' => $data['code'],
                'img1' => $data['img1'],
                'img2' => $data['img2'],
                'times' => time(),
            ]);
        }
        
        
        
        return WPreturn(lang('tijiaochenggong'),1);
        
    }
    
    //实名认证
    public function shi_info(){
        $uid = $this->uid;
        $shi = DB::name('user_shi')->where('uid', $uid)->order('id desc')->find();
        return WPreturn(['list'=>$shi],1);
    }
}

