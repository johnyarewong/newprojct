<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use wlpays\wlpays;

class User extends Base
{
	/**
	 * 用户列表
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function authStatus(){
		$post = input('post.');
		if(!$post){
			$this->error('非法操作！');
		}

		if(!$post['uid'] || !in_array($post['card_status'],array(2,3))){
			return WPreturn('参数错误',-1);
		}

		$ids = db('userinfo')->update($post);
		if($ids){
			return WPreturn('操作成功！',1);
		}else{
			return WPreturn('操作失败！',-1);
		}
	}
	public function headerData(){
		$where['bptype']=3;
		$czCount=db('balance')->where($where)->count();//未处理充值
		$where['bptype']=0;
		$where['isverified']=0;
		$withCount=db('balance')->where($where)->count();//未处理提现
		$whereOrder['kong_type']=0;
		$whereOrder['ostaus']=0;
		$today = strtotime(date('Y-m-d', time()));  //当前时间
		$start_today = $today;
		$end_today = $today + 86400; //,获取明天，一天是86400(时间戳)
		$whereOrder['buytime'] = array( //数据表字段create_time进行条件判断
			array('egt', $start_today),
			array('lt', $end_today),
		);
		$jyCount=db('order')->where($whereOrder)->count();//未处理提现
// 		$list=db('userinfo')->field('last_time')->select();
        $list=[];
		$num=0;
		foreach($list as $k=>$v){
			if(time()-$v['last_time']<300){
				$num++;
			}
		}
		$data['num']=$num;
		$data['cz']=$czCount;
		$data['with']=$withCount;
		$data['jy']=$jyCount;
		return WPreturn($data,1);
	}
	//上传图片
	public function authEditPost(){
		$uid = input('uid');
		$data=[];
		$imgList=$this->uploadImg();
		if(is_array($imgList)){
			foreach($imgList as $k=>$v){
				if($v['name']=="cardpic"){
					$data['cardpic']=$v['img'];
				}
				if($v['name']=="cardpic1"){
					$data['cardpic1']=$v['img'];
				}
			}
		}else{
			return WPreturn($imgList,-1);
		}
		$data['card_status']=3;
		$res=db('userinfo')->where('uid',$uid)->update($data);
		if($res){
			return WPreturn('修改成功',1);
		}else{
			return WPreturn('修改失败请重试',1);
		}
	}
	/**
	 * 实名认证修改信息
	 * @return [type] [description]
	 */
	public function authEdit()
	{
		$data = input('post.');
		if(!$data['id']){
			return WPreturn('参数错误',-1);
		}
		/*echo('<PRE>');
        var_dump($data);die;*/
		$shi = db('user_shi')->where('id',$data['id'])->find();
		if($shi){
		    if($shi['status'] == 0){
		        $data['stime'] = time();
		        $res = Db::name('user_shi')->update($data);
    		    if($res){
    			    return WPreturn('修改成功',1);
        		}else{
        			return WPreturn('修改失败请重试',1);
        		}
		    }
		    return WPreturn('修改成功',1);
		}
		
			return WPreturn('网络异常请刷新',-1);
			
		/*
		$uid = input('uid');
		$userinfo = db('userinfo')->where('id',$uid)->find();
		$this->assign('uid',$uid);
		$this->assign('userinfo',$userinfo);
		return $this->fetch();*/
	}
	
	public function auth()
	{
	    $pagenum = cache('page');
		$getdata = $where = array();
		$data = input('param.');
		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			$where['u.username'] = array('like','%'.$data['username'].'%');
			$getdata['username'] = $data['username'];
		}
		
		$userinfo = Db::name('user_shi')
            ->alias('us')
            ->join('userinfo u','us.uid = u.uid')
		    ->where($where)
		    ->order('us.id desc')
		    ->field('us.*, u.username, u.nickname')
		    ->paginate($pagenum,false,['query'=> $getdata]);

		/*foreach($userinfo as $k=>$v){
			$v['lastlog']=date("Y-m-d H:i",$v['lastlog']);
			$userinfo[$k]=$v;
		}*/
		$this->assign('userinfo',$userinfo);
		$this->assign('getdata',$getdata);
		return $this->fetch();
		/*$pagenum = cache('page');
		$getdata = $where = array();
		$data = input('param.');
		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			$where['username|truename|uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			$getdata['username'] = $data['username'];
		}
		if(isset($data['today']) && $data['today'] == 1){
			$getdata['starttime'] = strtotime(date("Y").'-'.date("m").'-'.date("d").' 00:00:00');
			$getdata['endtime'] = strtotime(date("Y").'-'.date("m").'-'.date("d").' 24:00:00');
			$where['utime'] = array('between time',array($getdata['starttime'],$getdata['endtime']));

		}
		$oid = input('oid');
		if($oid){
			$where['oid'] = $oid;
			$getdata['oid'] = $oid;
		}

		if(isset($data['uid']) && !empty($data['uid'])){
			$where['uid'] =$data['uid'];
			$getdata['uid'] =$data['uid'];
		}

		//权限检测
		if($this->otype != 3){

		   $uids = myuids($this->uid);
	        if(!empty($uids)){
	            $where['uid'] = array('IN',$uids);
	        }else{
	        	$where['uid'] = $this->uid;
	        }
	    }

	    if(isset($data['otype']) && $data['otype'] != '' && in_array($data['otype'],array(0,101))){
	    	$where['otype'] = $data['otype'];
	    	$getdata['otype'] = $data['otype'];
	    }else{
	    	$where['otype'] = array('IN',array(0,101));
	    }
	    //dump($where);
		//exit;
		$userinfo = Db::name('userinfo')->where($where)->order('uid desc')->paginate($pagenum,false,['query'=> $getdata]);

		foreach($userinfo as $k=>$v){
			$v['lastlog']=date("Y-m-d H:i",$v['lastlog']);
			$userinfo[$k]=$v;
		}
		$this->assign('userinfo',$userinfo);
		$this->assign('getdata',$getdata);
		return $this->fetch();*/
	}
	
	/**
	 * 用户列表
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function userlist()
	{
		$pagenum = cache('page');
		$getdata = $where = array();
		$data = input('param.');
		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			$where['username|uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			$getdata['username'] = $data['username'];
		}

		if(isset($data['today']) && $data['today'] == 1){
			$getdata['starttime'] = strtotime(date("Y").'-'.date("m").'-'.date("d").' 00:00:00');
			$getdata['endtime'] = strtotime(date("Y").'-'.date("m").'-'.date("d").' 24:00:00');
    		$where['utime'] = array('between time',array($getdata['starttime'],$getdata['endtime']));

		}
		$oid = input('oid');
		if($oid){
			$where['oid'] = $oid;
			$getdata['oid'] = $oid;
		}

		if(isset($data['uid']) && !empty($data['uid'])){
			$where['uid'] =$data['uid'];
			$getdata['uid'] =$data['uid'];
		}

		//权限检测
		if($this->otype != 3){

		   $uids = myuids($this->uid);
            if(!empty($uids)){
                $where['uid'] = array('IN',$uids);
            }else{
            	$where['uid'] = $this->uid;
            }
        }

        if(isset($data['otype']) && $data['otype'] != '' && in_array($data['otype'],array(0,101))){
        	$where['otype'] = $data['otype'];
        	$getdata['otype'] = $data['otype'];
        }else{
        	$where['otype'] = array('IN',array(0,101));
        }
        //dump($where);
		//exit;
		$userinfo = Db::name('userinfo')->where($where)->order('uid desc')->paginate($pagenum,false,['query'=> $getdata]);

		foreach($userinfo as $k=>$v){
			$v['lastlog']=date("Y-m-d H:i",$v['lastlog']);
			$userinfo[$k]=$v;
		}
		$this->assign('userinfo',$userinfo);
		$this->assign('getdata',$getdata);
		return $this->fetch();
	}

	/**
	 * 添加用户
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function useradd()
	{
		if(input('post.')){
			$data = input('post.');
			$data['utime'] = time();
			$data['upwd'] = md5($data['upwd'].$data['utime']);
			$data['oid'] = $_SESSION['userid'];
			$data['managername'] = db('userinfo')->where('uid',$data['oid'])->value('username');
			$data['username'] = $data['utime'];

			$issetutl = db('userinfo')->where('utel',$data['utel'])->find();
			if($issetutl){
				return WPreturn('该手机号已存在!',-1);
			}

			//去除空字符串，无用字符串
			$data = array_filter($data);
			unset($data['upwd2']);
			//插入数据
			$ids = Db::name('userinfo')->insertGetId($data);

			$newdata['uid'] = $ids;
			$newdata['username'] = 10000000+$ids;

			$newids = Db::name('userinfo')->update($newdata);

			if ($newids) {
				return WPreturn('添加用户成功!',1);
			}else{
				return WPreturn('添加用户失败,请重试!',-1);
			}
		}else{
		    $userlist= Db::name('userinfo')->where('ustatus',0)->select();
			$this->assign('userlist',$userlist);
			$this->assign('isedit',0);
			$userinfo['oid']=0;
			$this->assign('userinfo',$userinfo);
			return $this->fetch();
		}

	}

	/**
	 * 编辑用户
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function useredit()
	{
		if(input('post.')){
			$data = input('post.');
			if(!isset($data['uid']) || empty($data['uid'])){
				return WPreturn('参数错误,缺少用户id!',-1);
			}


			//修改密码
			if(isset($data['upwd']) && !empty($data['upwd'])){
				//验证用户密码
				$utime = Db::name('userinfo')->where('uid',$data['uid'])->value('utime');

				if(!isset($data['upwd']) || empty($data['upwd'])){
					return WPreturn('如需修改密码请输入新密码!',-1);
				}
				if(isset($data['upwd']) && isset($data['upwd2']) && $data['upwd'] != $data['upwd2']){
					return WPreturn('两次输入密码不同!',-1);
				}
				unset($data['upwd2']);
				//unset($data['ordpwd']);
				$data['upwd'] = md5($data['upwd'].$utime);

			}
			//去除空字符串和多余字符串
			$data = array_filter($data);
			if(!isset($data['ustatus'])){
				$data['ustatus'] = 0;
			}

			//判断是否修改了金额，如修改金额需插入balance记录
			if(!isset($data['usermoney'])){
				$data['usermoney'] = 0;
			}
			if(!isset($data['ordusermoney'])){
				$data['ordusermoney'] = 0;
			}

			if($data['usermoney'] != $data['ordusermoney']){
				$b_data['bptype'] = 2;
				$b_data['bptime'] = $b_data['cltime'] = time();
				$b_data['bpprice'] = $data['usermoney'] - $data['ordusermoney'] ;
				$b_data['remarks'] = '后台管理员id'.$_SESSION['userid'].'编辑客户信息改动金额';
				$b_data['uid'] = $data['uid'];
				$b_data['isverified'] = 1;
				$b_data['bpbalance'] = $data['usermoney'];
				$addbal = Db::name('balance')->insertGetId($b_data);
				if(!$addbal){
					return WPreturn('增加金额失败，请重试!',-1);
				}

			}
			unset($data['ordusermoney']);

			$editid = Db::name('userinfo')->update($data);

			if ($editid) {
				return WPreturn('修改用户成功!',1);
			}else{
				return WPreturn('修改用户失败,请重试!',-1);
			}
		}else{
			$uid = input('param.uid');
			$where['uid'] = $uid;
			$userinfo = Db::name('userinfo')->where($where)->find();
			unset($userinfo['otype']);
			//获取用户所属信息
			$oidinfo = GetUserOidInfo($uid,'username,oid');
			$userlist= Db::name('userinfo')->where('ustatus',0)->select();
			$this->assign($userinfo);
			$this->assign('userinfo',$userinfo);
			$this->assign('userlist',$userlist);
			$this->assign('isedit',1);
			$this->assign($oidinfo);
			return $this->fetch('useradd');
		}

	}

	/**
	 * 充值和提现
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function userprice()
	{
		$pagenum = cache('page');
		$getdata = $where = array();
		$data = input('');
		$where['bptype'] = array('IN',array(1,3));
		//类型
		if(isset($data['bptype']) && $data['bptype'] != ''){
			$where['bptype']=$data['bptype'];
			$getdata['bptype'] = $data['bptype'];
		}

		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			if($data['stype'] == 1){
				$where['username|u.uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			}
			if($data['stype'] == 2){
				$puid = db('userinfo')->where(array('username'=>$data['username']))->whereOr('utel',$data['username'])->value('uid');
				if(!$puid) $puid = 0;
				$where['u.oid'] = $puid;
			}


			$getdata['username'] = $data['username'];
			$getdata['stype'] = $data['stype'];
		}

		//时间搜索
		if(isset($data['starttime']) && !empty($data['starttime'])){
			if(!isset($data['endtime']) || empty($data['endtime'])){
				$data['endtime'] = date('Y-m-d H:i:s',time());
			}
			$where['bptime'] = array('between time',array($data['starttime'],$data['endtime']));
			$getdata['starttime'] = $data['starttime'];
			$getdata['endtime'] = $data['endtime'];
		}

		//权限检测
		if($this->otype != 3){

		   $uids = myuids($this->uid);
            if(!empty($uids)){
                $where['u.uid'] = array('IN',$uids);
            }
        }

		$balance = Db::name('balance')->alias('b')->field('b.*,u.username,u.nickname,u.oid')
					->join('__USERINFO__ u','u.uid=b.uid')
					->where($where)->order('bpid desc')->paginate($pagenum,false,['query'=> $getdata]);
		$all_bpprice = Db::name('balance')->alias('b')->field('b.*,u.username,u.nickname,u.oid')
					->join('__USERINFO__ u','u.uid=b.uid')
					->where($where)->sum('bpprice');
		//dump($balance);
		$this->assign('balance',$balance);
		$this->assign('getdata',$getdata);
		$this->assign('all_bpprice',$all_bpprice);
		return $this->fetch();
	}

	/**/
	public function userdfpay(){
		$id = input('param.id');
		$bpid = input('param.bpid');
		if(!$id || !$bpid){
			$this->error('参数错误！');
		}

		$bank = db('bankcard')->field('*')->where('id',$id)->find();
		$order = db('balance')->field('*')->where('bpid',$bpid)->find();
		if($order){
			if($order['isverified']=='1' || $order['pay_type']=='1'){
				echo json_encode(array('state'=>false,'msg'=>'订单已支付过'));
				exit;
			}
			if($order['pay_type']=='' || empty($order['pay_type']) || $order['pay_type']=='0' || $order['pay_type']=='3'){
				$orderid = "T".time().rand(10000,9999999);
				$wl = new wlpays();
				$amount = round($order['bpprice']*(100-$order['reg_par'])/100,2);
				//$amount = 1;
				$data = array(
					'out_trade_no'=>$orderid,
					'amount'=>$amount,
					'account_no'=>$bank['accntno'],
					'account_name'=>$bank['accntnm'],
					'id'=>$bank['scard'],
					'bankcode'=>$bank['bankcode'],
					'notify_url'=>'http://'.$_SERVER['SERVER_NAME'].'/index/pay/wldf_notify/bpid/'.$bpid.'.html',
					'attach'=>$bpid,
				);
				$res = $wl->dfpay($data);
				$res = json_decode($res,true);
				$udata['bpid'] = $bpid;
				if($res['resp_code']=='0000'){
					if($res['trans_status']=='P'){
						$udata['remarks'] = '处理中。。';
						$udata['pay_type'] = '2';
						$result = true;
					}else if($res['trans_status']=='S'){
						$udata['remarks'] = '提现成功';
						$udata['cltime'] = time();
						$udata['pay_type'] = '1';
						$udata['isverified'] = '1';
						$result = true;
					}else{
						$udata['remarks'] = '处理失败';
						$udata['pay_type'] = '3';
						$result = false;
					}
				}else{
					$udata['remarks'] = $res['resp_msg'];
					$udata['pay_type'] = '3';
					$result = false;
				}
				$udata['balance_sn'] = $orderid;
				Db::name('balance')->update($udata);
				echo json_encode(array('state'=>$result,'msg'=>$udata['remarks']),JSON_UNESCAPED_UNICODE);
			}else{
				echo json_encode(array('state'=>false,'msg'=>'状态不能改变'),JSON_UNESCAPED_UNICODE);
			}
		}else{
			echo json_encode(array('state'=>false,'msg'=>'订单不存在'),JSON_UNESCAPED_UNICODE);
		}
	}

	/**
	 * 提现
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function cash()
	{
		$pagenum = cache('page');
		$getdata = $where = array();
		$data = input('');
		$where['bptype'] = 0;
		//类型
		if(isset($data['isverified']) && $data['isverified'] != ''){
			$where['isverified']=$data['isverified'];
			$getdata['isverified'] = $data['isverified'];
		}

		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			if($data['stype'] == 1){
				$where['username|u.uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			}
			if($data['stype'] == 2){
				$puid = db('userinfo')->where(array('username'=>$data['username']))->whereOr('utel',$data['username'])->value('uid');
				if(!$puid) $puid = 0;
				$where['u.oid'] = $puid;
			}


			$getdata['username'] = $data['username'];
			$getdata['stype'] = $data['stype'];
		}

		//时间搜索
		if(isset($data['starttime']) && !empty($data['starttime'])){
			if(!isset($data['endtime']) || empty($data['endtime'])){
				$data['endtime'] = date('Y-m-d H:i:s',time());
			}
			$where['bptime'] = array('between time',array($data['starttime'],$data['endtime']));
			$getdata['starttime'] = $data['starttime'];
			$getdata['endtime'] = $data['endtime'];
		}

		//权限检测
		if($this->otype != 3){

		   $uids = myuids($this->uid);
            if(!empty($uids)){
                $where['u.uid'] = array('IN',$uids);
            }
        }
			//,c.address,c.accntno
		$balance = Db::name('balance')->alias('b')->field('b.*,u.username,u.nickname,u.oid,u.managername,c.accntnm,c.accntno,c.bankno,bs.bank_nm')
					->join('__USERINFO__ u','u.uid=b.uid')
					->join('__BANKCARD__ c','c.uid=b.uid')
					->join('__BANKS__ bs','bs.id=c.bankno')
					->where($where)->order('bpid desc')->paginate($pagenum,false,['query'=> $getdata]);
		$all_cash = Db::name('balance')->alias('b')->field('b.*,u.username,u.nickname,u.oid')
					->join('__USERINFO__ u','u.uid=b.uid')
					->where($where)->sum('bpprice');
		//dump($balance);
		$this->assign('otype',$this->otype);
		$this->assign('balance',$balance);
		$this->assign('getdata',$getdata);
		$this->assign('all_cash',$all_cash);
		return $this->fetch();
	}
	public function seeNum(){
		$where['bptype'] = 3;
		$where['isverified'] = 0;
			$balance = Db::name('balance')->where($where)->select();
			if(!$balance){

							$data['code'] = '000';
							$data['msghoutai'] = '';
							$data['url'] = '';
			}else{
			 		$data['code'] = '000';
					$data['msghoutai'] = '';
					$data['url'] = '/sy.mp3' ;
			}

			echo json_encode($data,JSON_UNESCAPED_UNICODE);

	}
	public function jujueprices(){
			$data = input('post.');
			$bpid= $data['bpid'];
			$uid = (int)$data['uid'];
			$userinfo = Db::name('userinfo')->where('uid',$data['uid'])->find();
			$b_data['bptype'] =1;
			$b_data['bptime'] = $b_data['cltime'] = time();


			$b_data['remarks'] = '审核拒绝';

			$b_data['isverified'] = 2;
			$b_data['bpbalance'] = $userinfo['usermoney'];

			$addbal = Db::name('balance')->where('bpid',$data['bpid'])->update($b_data);

			if(!$addbal){
				return WPreturn('系统错误，请核对订单!',-1);
			}else{
				return WPreturn('操作成功',1);
			}

	}
	public function addprices(){

			$data = input('post.');
			$bpid= $data['bpid'];
			$uid = (int)$data['uid'];


				$balance = Db::name('balance')->field('bpid,bpprice,isverified,bptime,reg_par')->where('bpid',$data['bpid'])->find();

					$userinfo = Db::name('userinfo')->where('uid',$data['uid'])->find();

				$ids = db('userinfo')->where('uid',$uid)->setInc('usermoney',$balance['bpprice']);

				if(!$ids) return WPreturn('增加金额失败，请重试!',-1);

				$b_data['bptype'] = 1;
				$b_data['bptime'] = $b_data['cltime'] = time();


				$b_data['remarks'] = '充值成功';

				$b_data['isverified'] = 1;
				$b_data['bpbalance'] = $userinfo['usermoney']+$balance['bpprice'];

				$addbal = Db::name('balance')->where('bpid',$data['bpid'])->update($b_data);

				if(!$addbal){
					return WPreturn('系统错误，请核对订单!',-1);
				}else{
					return WPreturn('操作成功',1);
				}

	}
	/**
	 * 提现处理
	 * @author lukui  2017-02-16
	 * @param  string $value [description]
	 * @return [type]        [description]
	 */
	public function dorecharge()
	{
		if(input('post.')){
			$data = input('post.');


			//获取提现订单信息和个人信息
			$balance = Db::name('balance')->field('bpid,bpprice,isverified,bptime,reg_par')->where('bpid',$data['bpid'])->find();
			$userinfo = Db::name('userinfo')->field('uid,username')->where('uid',$data['uid'])->find();
			if(empty($userinfo) || empty($balance)){
				return WPreturn('提现失败，缺少参数!',-1);
			}
			if($balance['isverified'] != 0){
				//return WPreturn('此订单已操作',-1);
			}



			//提现功能实现：

			$_data['bpid'] = $data['bpid'];
			$_data['isverified'] = (int)$data['type'];
			$_data['cltime'] = time();
			$_data['remarks'] = trim($data['cash_content']);

			//提现代付
			if($_data['isverified'] == 1){		//同意
				/*
				$bank = db('bankcard')->alias('bc')->field('bc.*,bs.bank_nm')
						->join('__BANKS__ bs','bs.id=bc.bankno')
						->where('uid',$userinfo['uid'])
						->find();
				$api = controller('Api');

				$resdafu = $api->daifu($balance,$userinfo,$bank);

				//return $resdafu;
				if($resdafu['type'] == -1){
					return $resdafu;
					exit;
				}else{
					$_data['isverified'] == 4;	//代付中……
				}
				*/

			}





			$ids = Db::name('balance')->update($_data);
			if($ids){

				if($_data['isverified'] == 2){  //拒绝
					$_ids=db('userinfo')->where('uid',$data['uid'])->setInc('usermoney',$balance['bpprice']);
					if($_ids){
						$user_money = db('userinfo')->where('uid',$data['uid'])->value('usermoney');
						//资金日志
                		set_price_log($data['uid'],1,$balance['bpprice'],'提现','拒绝申请：'.$data['cash_content'],$data['bpid'],$user_money);
					}
				}elseif($_data['isverified'] == 1){		//同意

				}else{
					return WPreturn('操作失败2！',-1);
				}
				return WPreturn('操作成功！',1);

			}else{
				return WPreturn('操作失败1！',-1);
			}
			//验证是否提现成功，成功后修改订单状态



		}else{
			$this->redirect('user/userprice');
		}

	}

	/**
	 * 客户资料审核
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function userinfo()
	{
		if(input('post.')){
			$data = input('post.');
			if(!$data['cid']){
				return WPreturn('审核失败,参数错误!',-1);
			}
			$editid = Db::name('cardinfo')->update($data);

			if ($editid) {
				return WPreturn('审核处理成功!',1);
			}else{
				return WPreturn('审核处理失败,请重试!',-1);
			}
		}else{
			$pagenum = cache('page');
			$getdata = $where = array();
			$data=input('get.');
			$is_check = input('param.is_check');
			//类型
			if(isset($data['is_check']) && $data['is_check'] != ''){
				$is_check = $data['is_check'];
			}
			if(isset($is_check) && $is_check != ''){
				$where['is_check']=$is_check;
				$getdata['is_check'] = $is_check;
			}

			//用户名称、id、手机、昵称
			if(isset($data['username']) && !empty($data['username'])){
				$where['username|u.uid|utel|nickname'] = array('like','%'.$data['username'].'%');
				$getdata['username'] = $data['username'];
			}

			//时间搜索
			if(isset($data['starttime']) && !empty($data['starttime'])){
				if(!isset($data['endtime']) || empty($data['endtime'])){
					$data['endtime'] = date('Y-m-d H:i:s',time());
				}
				$where['ctime'] = array('between time',array($data['starttime'],$data['endtime']));
				$getdata['starttime'] = $data['starttime'];
				$getdata['endtime'] = $data['endtime'];
			}


			$cardinfo = Db::name('cardinfo')->alias('c')->field('c.*,u.username,u.nickname,u.oid,u.portrait,u.utel')
						->join('__USERINFO__ u','u.uid=c.uid')
						->where($where)->order('cid desc')->paginate($pagenum,false,['query'=> $getdata]);

			$this->assign('cardinfo',$cardinfo);
			$this->assign('getdata',$getdata);
			return $this->fetch();
		}

	}


	/**
	 * 会员列表
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function vipuserlist()
	{
		$pagenum = cache('page');
		$data = input('param.');
		$getdata = array();
		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			$where['username|uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			$getdata['username'] = $data['username'];
		}

		$oid = input('oid');
		if($oid){
			$where['oid'] = $oid;
			$getdata['oid'] = $oid;
		}

		//权限检测
		if($this->otype != 3){
		   $oids = myoids($this->uid);
		   $oids[] = $this->uid;
            if(!empty($oids)){
                $where['uid'] = array('IN',$oids);
            }
        }

		$where['otype'] = 101;
		//dump($where);
		$userinfo = Db::name('userinfo')->where($where)->order('uid desc')->paginate($pagenum,false,['query'=> $getdata]);

		$this->assign('userinfo',$userinfo);
		$this->assign('getdata',$getdata);
		return $this->fetch();
	}

	/**
	 * 添加会员
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function vipuseradd()
	{

		if(input('post.')){
			$data = input('post.');
			$data['utime'] = time();
			$data['zhip'] = '127.0.0';
	  	$data['zcym']	= $_SERVER['HTTP_HOST'];
			$data['upwd'] = md5($data['upwd'].$data['utime']);

			$_this_user = db('userinfo')->where('uid',$this->uid)->find();


			//判断用户是否存在
			$data['username'] = trim($data['username']);
			$c_uid = Db::name('userinfo')->where('username',$data['username'])->value('uid');
			if($c_uid){
				return WPreturn('此用户已存在，请更改用户名!',-1);
			}

			$issetutl = db('userinfo')->where('utel',$data['utel'])->find();
			if($issetutl){
				return WPreturn('该手机号已存在!',-1);
			}
			//佣金比例(手续费)
			if($this->otype == 3){
				if($data['rebate'] > 100){
					return WPreturn('红利比例不得大于100!',-1);
				}
			}else{
				if($_this_user['rebate'] <= $data['rebate']){
					return WPreturn('红利比例不得大于'.$_this_user['rebate'].'!',-1);
				}
			}

			//红利比例(下单)
			if($this->otype == 3){
				if($data['feerebate'] > 100){
					return WPreturn('佣金比例不得大于100!',-1);
				}
			}else{
				if($_this_user['feerebate'] <= $data['feerebate']){
					return WPreturn('佣金比例不得大于'.$_this_user['feerebate'].'!',-1);
				}
			}



			//去除空数组
			$data = array_filter($data);
			unset($data['upwd2']);
			$data['oid'] = $_SESSION['userid'];
			$data['managername'] = db('userinfo')->where('uid',$data['oid'])->value('username');

			$data['otype'] = 101;


			$ids = Db::name('userinfo')->insertGetId($data);
			if ($ids) {
				return WPreturn('添加会员成功!',1);
			}else{
				return WPreturn('添加会员失败,请重试!',-1);
			}
		}else{
			//所有经理
			$jingli = Db::name('userinfo')->field('uid,username')->where('otype',2)->order('uid desc')->select();
			$this->assign('isedit',0);
			$this->assign('jingli',$jingli);
			return $this->fetch();
		}
	}

	/**
	 * 编辑会员
	 * @author lukui  2017-02-16
	 * @return [type] [description]
	 */
	public function vipuseredit()
	{
		if(input('post.')){
			$data = input('post.');
			if(!isset($data['uid']) || empty($data['uid'])){
				return WPreturn('参数错误,缺少用户id!',-1);
			}

			$foid = db('userinfo')->where('uid',$data['uid'])->value('oid');

			$_this_user = db('userinfo')->where('uid',$foid)->find();
			//佣金比例(手续费)
			if($this->otype == 3){
				if($data['rebate'] > 100){
					return WPreturn('红利比例不得大于100!',-1);
				}
			}else{
				if($_this_user['rebate'] < $data['rebate']){
					return WPreturn('红利比例不得大于'.$_this_user['rebate'].'!',-1);
				}
			}

			//红利比例(下单)
			if($this->otype == 3){
				if($data['feerebate'] > 100){
					return WPreturn('佣金比例不得大于100!',-1);
				}
			}else{
				if($_this_user['feerebate'] < $data['feerebate']){
					return WPreturn('佣金比例不得大于'.$_this_user['feerebate'].'!',-1);
				}
			}



			//修改密码
			if(isset($data['upwd']) && !empty($data['upwd'])){
				//验证用户密码
				$c_user = Db::name('userinfo')->where('uid',$data['uid'])->find();
				$utime = $c_user['utime'];
				/*
				if(md5($data['ordpwd'].$utime) != $c_user['upwd']){
					return WPreturn('旧密码不正确!',-1);
				}
				*/

				if(!isset($data['upwd']) || empty($data['upwd'])){
					return WPreturn('如需修改密码请输入新密码!',-1);
				}
				if(isset($data['upwd']) && isset($data['upwd2']) && $data['upwd'] != $data['upwd2']){
					return WPreturn('两次输入密码不同!',-1);
				}
				unset($data['upwd2']);
				//unset($data['ordpwd']);
				$data['upwd'] = md5($data['upwd'].$utime);

			}

			if(empty($data["upwd"])){
				unset($data["upwd"]);

			}
			//unset($data["ordpwd"]);
			unset($data["upwd2"]);

			if($this->otype == 3){

				if(empty($data["usermoney"])){
					$data["usermoney"] = 0;
				}

				$_data_user = db('userinfo')->where('uid',$data['uid'])->find();
				if($data['usermoney'] != $_data_user['usermoney']){
					$b_data['bptype'] = 2;
					$b_data['bptime'] = $b_data['cltime'] = time();
					$b_data['bpprice'] = $data['usermoney'] - $_data_user['usermoney'] ;
					$b_data['remarks'] = '后台管理员id'.$_SESSION['userid'].'编辑客户信息改动金额';
					$b_data['uid'] = $data['uid'];
					$b_data['isverified'] = 1;
					$b_data['bpbalance'] = $data['usermoney'];
					$addbal = Db::name('balance')->insertGetId($b_data);
					if(!$addbal){
						return WPreturn('增加金额失败，请重试!',-1);
					}

				}
			}


			$data['ustatus']--;


			$editid = Db::name('userinfo')->update($data);

			if ($editid) {
				return WPreturn('修改用户成功!',1);
			}else{
				return WPreturn('修改用户失败,请重试!',-1);
			}
		}else{
			$uid = input('param.uid');
			if (!isset($uid) || empty($uid)) {
				$this->redirect('user/vipuserlist');
			}
			//获取用户信息
			$where['uid'] = $uid;
			$userinfo = Db::name('userinfo')->where($where)->find();

			//获取所有经理信息
			$jingli = Db::name('userinfo')->field('uid,username')->where('otype',2)->order('uid desc')->select();


			unset($userinfo['otype']);
			$this->assign($userinfo);
			$this->assign('isedit',1);
			$this->assign('jingli',$jingli);
			return $this->fetch('vipuseradd');
		}
	}


	/**
	 * 会员的邀请码
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function usercode()
	{
		if (input('post.')) {
			$data = input('post.');
			$data['usercode'] = trim($data['usercode']);
			//邀请码是否存在
			$codeid = Db::name('usercode')->where('usercode',$data['usercode'])->value('id');
			if($codeid){
				return WPreturn('此邀请码已存在',-1);
			}
			$ids = Db::name('usercode')->insertGetId($data);
			if ($ids) {
				return WPreturn('添加邀请码成功!',1);
			}else{
				return WPreturn('添加邀请码失败,请重试!',-1);
			}
			dump($data);

		}else{
			$uid = input('param.uid');
			if(!isset($uid) || empty($uid)){
				$this->redirect('user/vipuserlist');
			}

			//所有渠道
			$manner = Db::name('userinfo')->field('uid,username')->where('otype',3)->order('uid desc')->select();

			//所有邀请码
			$usercode = Db::name('usercode')->alias('uc')->field('uc.*,ui.username')
						->join('__USERINFO__ ui','ui.uid=uc.mannerid')
						->where('uc.uid',$uid)->order('id desc')->select();

			$this->assign('uid',$uid);
			$this->assign('manner',$manner);
			$this->assign('usercode',$usercode);
			return $this->fetch();
		}
	}



	/**
	 * 会员资金管理
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function vipuserbalance()
	{
		$pagenum = cache('page');
		$getdata = $userinfo = array();
		$data = input('get.');

		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			$where['username|uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			$getdata['username'] = $data['username'];
		}

		//时间搜索
		if(isset($data['starttime']) && !empty($data['starttime'])){
			if(!isset($data['endtime']) || empty($data['endtime'])){
				$data['endtime'] = date('Y-m-d H:i:s',time());
			}
			$u_where['bptime'] = array('between time',array($data['starttime'],$data['endtime']));
			$getdata['starttime'] = $data['starttime'];
			$getdata['endtime'] = $data['endtime'];
		}

		//会员类型 otype
		if(isset($data['otype']) && !empty($data['otype'])){
			$where['otype'] = $data['otype'];
			$getdata['otype'] = $data['otype'];
		}else{
			$where['otype'] = array('IN',array(2,3,4));
		}

		//必须是已经审核了的
		$u_where['isverified'] = 1;

		$user = Db::name('userinfo')->field('uid,username,oid,otype')->where($where)->order('uid desc')->paginate($pagenum,false,['query'=> $getdata]);

		//分页与数据分开执行
		$page = $user->render();
		$userinfo = $user->items();

		//获取会员下面客户的资金情况
		foreach ($userinfo as $key => $value) {
			$u_uid = array();
			//获取会员的客户id
			if($value['otype'] == 2){  //经理
				$u_uid = JingliUser($value['uid']);
			}elseif($value['otype'] == 3){  //渠道
				$u_uid = QudaoUser($value['uid']);
			}elseif($value['otype'] == 4){  //员工
				$u_uid = YuangongUser($value['uid']);
			}
			if(empty($u_uid)){
				$u_uid = array(0);
			}
			$u_where['uid'] = array('IN',$u_uid);
			//总充值
			$u_where['bptype'] = 1;
			$userinfo[$key]['recharge'] = Db::name('balance')->where($u_where)->sum('bpprice');
			//总提现
			$u_where['bptype'] = 0;
			$userinfo[$key]['getprice'] = Db::name('balance')->where($u_where)->sum('bpprice');
			//总净入
			$userinfo[$key]['income'] = $userinfo[$key]['recharge'] - $userinfo[$key]['getprice'];


		}

		//dump($userinfo);
		$this->assign('userinfo',$userinfo);
		$this->assign('page', $page);
		$this->assign('getdata',$getdata);
		return $this->fetch();
	}


	/**
	 * 客户资金管理
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function userbalance()
	{
		$pagenum = cache('page');

		//所有归属
		$vipuser['jingli'] = Db::name('userinfo')->field('uid,username')->where('otype',2)->select();
		$vipuser['qudao'] = Db::name('userinfo')->field('uid,username')->where('otype',3)->select();
		$vipuser['yuangong'] = Db::name('userinfo')->field('uid,username')->where('otype',4)->select();
		//搜索条件
		$where = $getdata = array();
		$data = input('get.');
		//用户名称、id、手机、昵称
		if(isset($data['username']) && !empty($data['username'])){
			$where['username|u.uid|utel|nickname'] = array('like','%'.$data['username'].'%');
			$getdata['username'] = $data['username'];
		}

		//时间搜索
		if(isset($data['starttime']) && !empty($data['starttime'])){
			if(!isset($data['endtime']) || empty($data['endtime'])){
				$data['endtime'] = date('Y-m-d H:i:s',time());
			}
			$where['bptime'] = array('between time',array($data['starttime'],$data['endtime']));
			$getdata['starttime'] = $data['starttime'];
			$getdata['endtime'] = $data['endtime'];
		}

		//会员类型 ouid
		if(isset($data['ouid']) && !empty($data['ouid'])){
			//该会员下所有的邀请码
			$uids = UserCodeForUser($data['ouid']);
			if(empty($uids)){
				$uids = array(0);
			}
			$where['b.uid'] = array('IN',$uids);
		}

		//必须是已经审核了的
		$where['isverified'] = 1;


		$where['bptype'] = array('between','0,2');
		//客户资金变动
		$balance = Db::name('balance')->alias('b')->field('b.*,u.username,u.nickname,u.oid')
					->join('__USERINFO__ u','u.uid=b.uid')
					->where($where)->order('bpid desc')->paginate($pagenum,false,['query'=> $getdata]);

		$this->assign('vipuser',$vipuser);
		$this->assign('balance',$balance);
		return $this->fetch();
	}















	/**
	 * 添加管理员
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function adminadd()
	{

		return $this->fetch();
	}

	/**
	 * 管理员列表
	 * @author lukui  2017-02-17
	 * @return [type] [description]
	 */
	public function adminlist()
	{

		return $this->fetch();
	}






	/**
	 * 禁用、启用用户
	 * @return [type] [description]
	 */
	public function doustatus()
	{

		$post = input('post.');
		if(!$post){
			$this->error('非法操作！');
		}

		if(!$post['uid'] || !in_array($post['ustatus'],array(0,1))){
			return WPreturn('参数错误',-1);
		}

		$ids = db('userinfo')->update($post);
		if($ids){
			return WPreturn('操作成功！',1);
		}else{
			return WPreturn('操作失败！',-1);
		}


	}

	/**
	 * 禁用、启用用户交易
	 */
		public function jystatus()
	{

		$post = input('post.');
		if(!$post){
			$this->error('非法操作！');
		}

		if(!$post['uid'] || !in_array($post['ustatus'],array(0,4))){
			return WPreturn('参数错误',-1);
		}

		$ids = db('userinfo')->update($post);
		if($ids){
			return WPreturn('操作成功！',1);
		}else{
			return WPreturn('操作失败！',-1);
		}


	}


	/**
	 * 成为代理商
	 * @return [type] [description]
	 */
	public function dootype()
	{

		$post = input('post.');
		if(!$post){
			$this->error('非法操作！');
		}

		if(!$post['uid'] || $post['otype'] != 101){
			return WPreturn('参数错误',-1);
		}

		$ids = db('userinfo')->update($post);
		if($ids){
			return WPreturn('操作成功！',1);
		}else{
			return WPreturn('操作失败！',-1);
		}


	}


	/**
	 * 签约管理
	 * @return [type] [description]
	 */
	public function userbank()
	{


		$uid = input('param.uid');
		if(!$uid){
			$this->error('参数错误！');
		}
		//gb
		$bank = db('bankcard')->alias('bc')->field('bc.*,bs.bank_nm')
				->join('__BANKS__ bs','bs.id=bc.bankno')
				->where('uid',$uid)
				->find();

		$banks = db('banks')->select();
	$this->assign('uid',$uid);
		$this->assign('banks',$banks);
		$this->assign('bank',$bank);
		return $this->fetch();
	}

	public function usercar(){

		if(input('post.')){
				$data = input('post.');
					$userinfo = db('bankcard');


				$user = $userinfo->where('uid',$data['uid'])->find();
				if($user){

						$editid = Db::name('bankcard')->where('uid',$data['uid'])->update($data);
				}else{
						$editid = Db::name('bankcard')->insert($data);
				}


			if($editid){
			$this->success("修改成功");
		}else{
			$this->error("修改失败");
		}

		}

	}
	/**
	 * 我的团队
	 * @return [type] [description]
	 */
	public function myteam()
	{

		$uid = $this->uid;
		$userinfo = db('userinfo');
		//$myteam = $userinfo->field('uid,oid,username,utel,nickname,usermoney')->where(array('oid'=>$uid,'otype'=>101))->select();
		$myteam = mytime_oids($uid);
		$user = $userinfo->where('uid',$uid)->find();
		$user['mysons'] = $myteam;
		$this->assign('mysons',$user);
		return $this->fetch();

	}






	/**
	 * 某个代理商的业绩
	 * @return [type] [description]
	 */
	public function yeji()
	{
		$userinfo = db('userinfo');
		$price_log = db('price_log');
		$uid = input('uid');
		if(!$uid){
			$this->error('参数错误！');
		}

		$_user = $userinfo->where('uid',$uid)->find();
		if(!$_user){
			$this->error('暂无用户！');
		}



		//搜索条件
		$data = input('param.');

		if(isset($data['starttime']) && !empty($data['starttime'])){
			if(!isset($data['endtime']) || empty($data['endtime'])){
				$data['endtime'] = date('Y-m-d H:i:s',time());
			}
			$getdata['starttime'] = $data['starttime'];
			$getdata['endtime'] = $data['endtime'];
		}else{
			$getdata['starttime'] = date('Y-m-d',time()).' 00:00:00';
			$getdata['endtime'] = date('Y-m-d',time()).' 23:59:59';
		}

		$map['time'] = array('between time',array($getdata['starttime'],$getdata['endtime']));
		$map['uid'] = $uid;
		/*
		//红利收益
		$map['title'] = '对冲';
		$hl_account = $price_log->where($map)->sum('account');
		if(!$hl_account) $hl_account = 0;
		//佣金收益
		$map['title'] = '客户手续费';
		$yj_account = $price_log->where($map)->sum('account');
		if(!$yj_account) $yj_account = 0;
		dump($yj_account);
		*/

		$_map['buytime'] = array('between time',array($getdata['starttime'],$getdata['endtime']));
		$uids = myuids($uid);
		$_map['uid']  = array('IN',$uids);
		$all_sxfee = db('order')->where($_map)->sum('sx_fee');
		if(!$all_sxfee) $all_sxfee = 0;
		$all_ploss = db('order')->where($_map)->sum('ploss');
		if(!$all_ploss) $all_ploss = 0;

		$this->assign('_user',$_user);
		$this->assign('getdata',$getdata);
		$this->assign('all_sxfee',$all_sxfee);
		$this->assign('all_ploss',$all_ploss);
		/*
		$this->assign('hl_account',$hl_account);
		$this->assign('yj_account',$yj_account);
		*/
		return $this->fetch();
	}


	/**删除用户
	*/
	public function deleteuser()
	{

		$uid = input('post.uid');
		if(!$uid){
			return WPreturn('参数错误！',-1);
		}

		$ids = db('userinfo')->where('uid',$uid)->delete();

		db('balance')->where('uid',$uid)->delete();
		db('order')->where('uid',$uid)->delete();
		db('order_log')->where('uid',$uid)->delete();
		db('price_log')->where('uid',$uid)->delete();

		if($uid){
			return WPreturn('删除成功',1);
		}else{
			return WPreturn('删除失败',-1);
		}
	}

	public function chongzhi()
	{


		return $this->fetch();
	}


	public function addprice()
	{

		$post = input('post.');
	//	var_dump($psot);exit;

		$stalen = strlen($post['utel']);

		if($stalen == 11){

				$post['utel'] = trim($post['utel']);
				$post['bpprice'] = trim($post['bpprice']);

				if(!$post || !$post['bpprice']){
					return WPreturn('请正常填写参数',-1);
				}
				$user = db('userinfo')->where('utel',$post['utel'])->find();

				if(!$user) return WPreturn('此用户不存在，请正确填写用户手机号',-1);

				$ids = db('userinfo')->where('utel',$post['utel'])->setInc('usermoney',$post['bpprice']);

				if(!$ids) return WPreturn('增加金额失败，请重试!',-1);

				$b_data['bptype'] = 2;
				$b_data['bptime'] = $b_data['cltime'] = time();
				$b_data['bpprice'] = $post['bpprice'] ;
				$b_data['remarks'] = '后台管理员id'.$_SESSION['userid'].'编辑客户信息改动金额';
				$b_data['uid'] = $user['uid'];
				$b_data['isverified'] = 1;
				$b_data['bpbalance'] = $user['usermoney']+$post['bpprice'];
				$addbal = Db::name('balance')->insertGetId($b_data);
				if(!$addbal){
					return WPreturn('系统错误，请核对订单!',-1);
				}else{
					return WPreturn('操作成功',1);
				}
		}else{
				$post['utel'] = trim($post['utel']);
				$post['bpprice'] = trim($post['bpprice']);

				if(!$post || !$post['bpprice']){
					return WPreturn('请正常填写参数',-1);
				}
				$user = db('userinfo')->where('uid',$post['utel'])->find();

				if(!$user) return WPreturn('此用户不存在，请正确填写用户手机号',-1);

				$ids = db('userinfo')->where('uid',$post['utel'])->setInc('usermoney',$post['bpprice']);

				if(!$ids) return WPreturn('增加金额失败，请重试!',-1);

				$b_data['bptype'] = 2;
				$b_data['bptime'] = $b_data['cltime'] = time();
				$b_data['bpprice'] = $post['bpprice'] ;
				$b_data['remarks'] = '后台管理员id'.$_SESSION['userid'].'编辑客户信息改动金额';
				$b_data['uid'] = $user['uid'];
				$b_data['isverified'] = 1;
				$b_data['bpbalance'] = $user['usermoney']+$post['bpprice'];
				$addbal = Db::name('balance')->insertGetId($b_data);
				if(!$addbal){
					return WPreturn('系统错误，请核对订单!',-1);
				}else{
					return WPreturn('操作成功',1);
				}
		}

	}
	public function uploadImg(){
			$data=[];
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
				                    $data[]=['img'=>$path,'name'=>$k];
				                }else{
									return '上传失败';
				                }
				            }
				        }
				    }else{
						return '文件类型不对';
				    }
				}
			}
			return $data;
	}










}
