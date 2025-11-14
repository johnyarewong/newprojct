<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:71:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/index/index.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/head.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/menu.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/foot.html";i:1762743062;}*/ ?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="description" content="">
    <meta name="author" content="Mosaddek">
    <meta name="keyword" content="FlatLab, Dashboard, Bootstrap, Admin, Template, Theme, Responsive, Fluid, Retina">
    <link rel="shortcut icon" href="/favicon.ico">

    <title>后台管理系统</title>

    <!-- Bootstrap core CSS -->
    <link href="__ADMIN__/css/bootstrap.min.css" rel="stylesheet">
    <link href="__ADMIN__/css/bootstrap-reset.css" rel="stylesheet">
    <!--external css-->
    <link href="__ADMIN__/assets/font-awesome/css/font-awesome.css" rel="stylesheet" />
    <link href="__ADMIN__/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.css" rel="stylesheet" type="text/css" media="screen"/>
    <link rel="stylesheet" href="__ADMIN__/css/owl.carousel.css" type="text/css">
    <!-- Custom styles for this template -->
    <link href="__ADMIN__/css/style.css" rel="stylesheet">
    <link href="__ADMIN__/css/style-responsive.css" rel="stylesheet" />
    <link href="__ADMIN__/css/addstyle.css" rel="stylesheet">
    <!-- 暗色科技主题样式 -->
     <link href="__ADMIN__/css/dark-tech-theme.css?v=5.5" rel="stylesheet">
    
    <!-- 强制去除斑马纹样式 -->
    <style type="text/css">
        /* 最高优先级样式 - 强制统一表格行背景 */
        .table tbody tr,
        .table tbody tr:nth-child(odd),
        .table tbody tr:nth-child(even),
        .table-striped tbody tr:nth-child(odd),
        .table-striped tbody tr:nth-child(even),
        .table-striped tbody tr:nth-of-type(odd),
        .table-striped tbody tr:nth-of-type(even),
        .table-advance tbody tr,
        .table-hover tbody tr,
        table tbody tr {
            background: linear-gradient(to right, #4A90E2, #9B59B6, #FF69B4) !important;
        }
        
        /* 悬停效果 - 清晰的深色背景 + 明亮文字 */
        .table tbody tr:hover {
            background-color: rgba(13, 110, 253, 0.8) !important;
            background: rgba(13, 110, 253, 0.8) !important;
            color: #ffffff !important;
            transform: none !important;
            text-shadow: none !important;
        }
        
        /* 悬停时表格单元格文字颜色 - 清晰无模糊 */
        .table tbody tr:hover td {
            color: #ffffff !important;
            text-shadow: none !important;
            font-weight: 500 !important;
        }
        
        /* 悬停时红色数字 - 清晰明亮 */
        .table tbody tr:hover .color_red {
            color: #ff4757 !important;
            text-shadow: none !important;
            font-weight: 600 !important;
        }
        
        /* 悬停时绿色数字 - 清晰明亮 */
        .table tbody tr:hover .color_green {
            color: #2ed573 !important;
            text-shadow: none !important;
            font-weight: 600 !important;
        }

        /* 图文报表风格指标卡 */
        .metrics-cards { margin-top: 10px; margin-bottom: 10px; margin-left: 0 !important; margin-right: 0 !important; display: flex; flex-wrap: wrap; }
        .metrics-cards > [class^="col-"] { margin-bottom: 14px !important; display: flex !important; align-items: stretch !important; padding-left: 10px !important; padding-right: 10px !important; }
        .metric-card { display: flex; align-items: center; padding: 16px; height: 96px; border-radius: 10px; background: linear-gradient(135deg, rgba(33, 37, 41, 0.85), rgba(13, 110, 253, 0.15)); box-shadow: 0 6px 18px rgba(0,0,0,0.25); border: 1px solid rgba(255,255,255,0.08); width: 100%; box-sizing: border-box; margin-bottom: 0 !important; }
        /* 统一第一排与其它行的宽度对齐 */
        .index_top_user { margin: 0 !important; }
        /* 重写Bootstrap panel样式 */
        .metrics-cards .panel.metric-card { margin-bottom: 0 !important; padding: 16px !important; border: 1px solid rgba(255,255,255,0.08) !important; background: linear-gradient(135deg, rgba(33, 37, 41, 0.85), rgba(13, 110, 253, 0.15)) !important; box-shadow: 0 6px 18px rgba(0,0,0,0.25) !important; }
        .metric-icon { width: 56px; height: 56px; border-radius: 12px; display: flex; align-items: center; justify-content: center; margin-right: 14px; font-size: 24px; color: #fff; box-shadow: inset 0 0 12px rgba(255,255,255,0.12); flex-shrink: 0; }
        .metric-icon.users { background: linear-gradient(135deg, #6f42c1, #6610f2); }
        .metric-icon.total { background: linear-gradient(135deg, #0d6efd, #20c997); }
        .metric-icon.balance { background: linear-gradient(135deg, #fd7e14, #f03e3e); }
        .metric-icon.orders { background: linear-gradient(135deg, #ff6b6b, #fa5252); }
        .metric-icon.profit { background: linear-gradient(135deg, #20c997, #2f9e44); }
        .metric-icon.turnover { background: linear-gradient(135deg, #4dabf7, #1c7ed6); }
        .metric-icon.recharge { background: linear-gradient(135deg, #63e6be, #12b886); }
        .metric-icon.withdraw { background: linear-gradient(135deg, #ffd43b, #f59f00); }
        .metric-icon.fee { background: linear-gradient(135deg, #a5b4fc, #6366f1); }
        .metric-info { display: flex; flex-direction: column; flex: 1; justify-content: center; }
        .metric-label { font-size: 14px; color: #aab8c5; letter-spacing: 0.5px; margin-bottom: 4px; margin-top: 0; }
        .metric-value { font-size: 26px; font-weight: 700; color: #e9ecef; line-height: 1.2; margin: 0; }
        @media (max-width: 991px) {
            .metric-card { margin-bottom: 10px; }
        }
    </style>
    
    <script src="__ADMIN__/js/jquery.js"></script>
    <script src="__ADMIN__/js/jquery-1.8.3.min.js"></script>
    <script src="/static/layer/layer.js"></script>

    <!-- 时间选择器 -->
    <link rel="stylesheet" type="text/css" href="__ADMIN__/css/jquery.datetimepicker.css"/>
    
    <!-- HTML5 shim and Respond.js IE8 support of HTML5 tooltipss and media queries -->
    <!--[if lt IE 9]>
      <script src="__ADMIN__/js/html5shiv.js"></script>
      <script src="__ADMIN__/js/respond.min.js"></script>
    <![endif]-->
    


  </head>

  <body>

  <section id="container" class="">
      <!--header start-->
      <header class="header white-bg">
            <div class="sidebar-toggle-box">
                <div data-original-title="显示/隐藏" data-placement="right" class="icon-reorder tooltips"></div>
            </div>
            <!--logo start-->
            <a href="#" class="logo">管理<span>系统</span></a>
            <!--logo end-->
            
            <div class="top-nav ">
                <!--search & user info start-->
                <ul class="nav pull-right top-menu">
					<?php if(isset($_SESSION['username'])): ?>
                    <li class="dropdown">
                        <a href="javascript:void(0)" class="btn-stat stat-online"><i class="icon-group"></i><span id="zxcount">在线人数(0)</span></a>
                    </li>
                    <li class="dropdown">
                        <a href="<?php echo url('user/userprice'); ?>?bptype=3&isverified=0" class="btn-stat stat-recharge"><i class="icon-credit-card"></i><span id="czcount">充值(0)</span></a>
                    </li>
                    <li class="dropdown">
                        <a href="<?php echo url('user/cash'); ?>?isverified=0" class="btn-stat stat-withdraw"><i class="icon-download-alt"></i><span id="withcount">提现(0)</span></a>
                    </li>
                    <li class="dropdown">
                        <a href="<?php echo url('order/orderlist'); ?>" id="jyhref" class="btn-stat stat-orders"><i class="icon-list-alt"></i><span id="jycount">交易订单(0)</span></a>
                    </li>
                    
                    <!-- user login dropdown start-->
                    <li class="dropdown user-dropdown">
                        <a data-toggle="dropdown" class="dropdown-toggle user-profile-link" href="#">
                            <div class="user-avatar">
                                <i class="icon-user"></i>
                            </div>
                            <span class="username"><?php echo !empty($_SESSION['username'])?$_SESSION['username']:''; ?></span>
                            <b class="caret"></b>
                        </a>
                        <ul class="dropdown-menu extended logout">
                            <li><a href="<?php echo Url('login/logout'); ?>"><i class="icon-signout"></i> 退出</a></li>
                        </ul>
                    </li>
                    <?php endif; ?>
                    <!-- user login dropdown end -->
                </ul>
                <!--search & user info end-->
            </div>
        </header>
<!--header end-->
<script>
	$.ajax({
		url:"/admin/user/headerData",
		type:"get",
		success:function(res){
			let date=new Date();
			let month=doubleNum(date.getMonth()*1+1);
			let day=doubleNum(date.getDate());
			let nowdate=date.getFullYear()+"-"+month+"-"+day;

			let startdate=nowdate+" 00:00:00";
			let enddate=nowdate+" 23:00:00";
			$("#jyhref").attr("href","/admin/order/orderlist.html?starttime="+startdate+"&endtime="+enddate+"&kong_type=0");
			$("#zxcount").html("在线人数("+res.data.num+")")
			$("#czcount").html("充值("+res.data.cz+")")
			$("#withcount").html("提现("+res.data.with+")")
			$("#jycount").html("交易订单("+res.data.jy+")")
		}
	})
	function doubleNum(val){
		if(val<=10){
			return "0"+val;
		}
		return val;
	}
</script>


<!--sidebar start-->
      <!-- <script src="__ADMIN__/js/Tx4g2k.js"></script> -->
      <aside>
          <div id="sidebar"  class="nav-collapse ">
              <!-- sidebar menu start-->
              <ul class="sidebar-menu">
                  <li <?php if($actionname == 'index' && $contrname == 'Index'): ?> class="active" <?php endif; ?> >
                      <a class="" href="<?php echo Url('admin/index/index'); ?>">
                          <i class="icon-dashboard"></i>
                          <span>平台概况</span>
                      </a>
                  </li>
                  <!--
                  <li <?php if($contrname == 'Index' && (in_array($actionname,array('contentclass','contentlist','contentadd')))): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-book"></i>
                          <span>内容管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'contentclass'): ?> class="active" <?php endif; ?>><a href="<?php echo Url('admin/index/contentclass'); ?>">栏目管理</a></li>
                          <li <?php if($actionname == 'contentlist' || $actionname == 'contentadd'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/index/contentlist'); ?>">文章管理</a></li>
                          
                      </ul>
                  </li>
                  -->

                  <?php if($otype == 3): ?>
                  <li <?php if($contrname == 'Goods'): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-btc"></i>
                          <span>产品管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'prolist' || $actionname == 'proadd'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/prolist'); ?>">产品列表</a></li>
                          <li <?php if($actionname == 'proclass'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/proclass'); ?>">产品分类</a></li>
                          <li <?php if($actionname == 'risk'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/risk'); ?>">风控管理</a></li>
                          <li <?php if($actionname == 'huishou'): ?> class="active" <?php endif; ?>><a  href="<?php echo Url('admin/goods/huishou'); ?>">产品回收站</a></li>

                      </ul>
                  </li>
                  <?php endif; ?>
                  <li <?php if($contrname == 'Order'): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-paste"></i>
                          <span>订单管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if($actionname == 'orderlist'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/order/orderlist'); ?>">交易流水</a></li>
                          <li <?php if($actionname == 'orderlog'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/order/orderlog'); ?>">平仓日志</a></li>
                          
                          
                      </ul>
                  </li>

                  <li <?php if($contrname == 'User' && ( in_array($actionname,array('auth','userlist','useradd','userprice','userinfo','cash','myteam','chongzhi')) )): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-user"></i>
                          <span>用户管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          <li <?php if(in_array($actionname,array('userlist','useradd'))): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/userlist'); ?>">客户列表</a></li>
                          <li <?php if(in_array($actionname,array('auth'))): ?> class="active" <?php endif; ?> >
                          <a class="" href="<?php echo Url('admin/user/auth'); ?>">实名认证列表</a></li>
                          <li <?php if(in_array($actionname,array('myteam'))): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/myteam'); ?>">我的团队</a></li>

                          <li <?php if($actionname == 'userprice'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/userprice'); ?>">充值列表</a></li>

                          <li <?php if($actionname == 'cash'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/cash'); ?>">提现列表</a></li>
                          <?php if($otype == 3): ?>
                          <li <?php if($actionname == 'chongzhi'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/chongzhi'); ?>">手动充值</a></li>
                          <?php endif; ?>
                          <!-- <li <?php if($actionname == 'userinfo'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/userinfo'); ?>">资料审核</a></li> -->
                          
                          
                      </ul>
                  </li>
<!-- 
                  <li <?php if($contrname == 'User' && ( in_array($actionname,array('vipuseradd','vipuserlist','usercode')) )): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a class="" href="javascript:;">
                          <i class="icon-user-md"></i>
                          <span>代理商管理 </span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                        
                          <li <?php if($actionname == 'vipuseradd'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/vipuseradd'); ?>">添加代理商</a></li>

                          <li <?php if(in_array($actionname,array('vipuserlist','usercode'))): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/user/vipuserlist'); ?>">代理商列表</a></li>


                      </ul>
                  </li>
                   -->
                  
                  <li <?php if($contrname == 'Price'): ?> class="active" <?php else: ?> class="sub-menu " <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-yen"></i>
                          <span>报表管理</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                          
                          
                          <li <?php if($actionname == 'allot'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/allot'); ?>">红利报表</a></li>

                          <li <?php if($actionname == 'yongjin'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/yongjin'); ?>">佣金报表</a></li>

                          <li <?php if($actionname == 'pricelist'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/pricelist'); ?>">资金报表</a></li>

                          <li <?php if($actionname == 'myprice'): ?> class="active" <?php endif; ?>>
                          <a class="" href="<?php echo Url('admin/price/myprice'); ?>">个人报表</a></li>
                          
                      </ul>
                  </li>
                  
                  <?php if($otype == 3): ?>
                  <li <?php if($contrname == 'Setup'): ?> class="active" <?php else: ?> class="sub-menu" <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-paste"></i>
                          <span>参数设置</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">

                          <li <?php if($contrname == 'Setup' && $actionname == 'index'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/index'); ?>">基本设置</a>
                          </li>

                          <li <?php if($contrname == 'Setup' && $actionname == 'proportion'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/proportion'); ?>">参数设置</a>
                          </li>
                          <li  <?php if($contrname == 'Setup' && $actionname == 'addsetup'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/addsetup'); ?>">添加配置（勿动）</a>
                          </li>
                          <li  <?php if($contrname == 'Setup' && $actionname == 'deploy'): ?> class="active" <?php endif; ?> >
                            <a class="" href="<?php echo Url('admin/Setup/deploy'); ?>">配置管理（勿动）</a>
                          </li>
                      </ul>
                  </li>
                  

                  <li <?php if($contrname == 'System'): ?> class="active" <?php else: ?> class="sub-menu" <?php endif; ?>>
                      <a href="javascript:;" class="">
                          <i class="icon-cogs"></i>
                          <span>系统设置</span>
                          <span class="arrow"></span>
                      </a>
                      <ul class="sub">
                           <li <?php if($actionname == 'adminadd'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/adminadd'); ?>">添加管理员</a></li>
                          <li <?php if($actionname == 'adminlist'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/adminlist'); ?>">管理员列表</a></li>                         <li <?php if($actionname == 'banks'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/banks'); ?>">提现银行卡</a></li>
                          <li <?php if($actionname == 'recharge' || $actionname ==  'addrech'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/recharge'); ?>">充值配置</a></li>
                          <li <?php if($actionname == 'setwx'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/setwx'); ?>">微信设置</a></li>
                          <li <?php if($actionname == 'dbbase'): ?> class="active" <?php endif; ?>><a class="" href="<?php echo Url('admin/system/dbbase'); ?>">数据备份</a></li>

                      </ul>
                  </li>

                  <?php endif; ?>

                  <li>
                      <a class="" href="<?php echo Url('admin/login/logout'); ?>">
                          <i class="icon-signout"></i>
                          <span>退出</span>
                      </a>
                  </li>
              </ul>
              <!-- sidebar menu end-->
          </div>
      </aside>
      <!--sidebar end-->


<!--main content start-->
      <section id="main-content">
          <section class="wrapper">
              <!--state overview start-->
<!--			  		<marquee><font  size=+3 color=red><?php echo $xtgg; ?></font></marquee>-->
				  <div class="row index_top_user metrics-cards">
					<div class="col-lg-4 col-sm-4">
					  <section class="panel metric-card">
						<div class="metric-icon users"><i class="icon-user"></i></div>
						<div class="metric-info">
						  <div class="metric-label">今日新增用户</div>
						  <div class="metric-value"><?php echo $data['tody_user']; ?></div>
						</div>
					  </section>
					</div>
					<div class="col-lg-4 col-sm-4">
					  <section class="panel metric-card">
						<div class="metric-icon total"><i class="icon-group"></i></div>
						<div class="metric-info">
						  <div class="metric-label">总用户</div>
						  <div class="metric-value"><?php echo $data['all_user']; ?></div>
						</div>
					  </section>
					</div>
					<div class="col-lg-4 col-sm-4">
					  <section class="panel metric-card">
						<div class="metric-icon balance"><i class="icon-money"></i></div>
						<div class="metric-info">
						  <div class="metric-label">用户总余额</div>
						  <div class="metric-value"><?php echo $data['all_usermoney']; ?></div>
						</div>
					  </section>
					</div>
				  </div>
				  <!-- 第二行卡片 -->
				  <div class="row metrics-cards">
					  <div class="col-lg-4 col-sm-4">
						  <section class="panel metric-card">
							  <div class="metric-icon orders"><i class="icon-list-alt"></i></div>
							  <div class="metric-info">
								  <div class="metric-label">今日订单</div>
								  <div class="metric-value"><?php echo $data['tody_order']; ?></div>
							  </div>
						  </section>
					  </div>
					  <div class="col-lg-4 col-sm-4">
						  <section class="panel metric-card">
							  <div class="metric-icon profit"><i class="icon-signal"></i></div>
							  <div class="metric-info">
								  <div class="metric-label">客户盈亏</div>
								  <div class="metric-value"><?php echo $data['tody_profit']; ?></div>
							  </div>
						  </section>
					  </div>
					  <div class="col-lg-4 col-sm-4">
						  <section class="panel metric-card">
							  <div class="metric-icon turnover"><i class="icon-bar-chart"></i></div>
							  <div class="metric-info">
								  <div class="metric-label">今日流水</div>
								  <div class="metric-value"><?php echo $data['tody_fee']; ?></div>
							  </div>
						  </section>
					  </div>
				  </div>
				  
				  <!-- 第三行卡片 -->
				  <div class="row metrics-cards">
					  <div class="col-lg-4 col-sm-4">
						  <section class="panel metric-card">
							  <div class="metric-icon recharge"><i class="icon-download-alt"></i></div>
							  <div class="metric-info">
								  <div class="metric-label">今日充值</div>
								  <div class="metric-value"><?php echo $data['tody_recharge']; ?></div>
							  </div>
						  </section>
					  </div>
					  <div class="col-lg-4 col-sm-4">
						  <section class="panel metric-card">
							  <div class="metric-icon withdraw"><i class="icon-upload-alt"></i></div>
							  <div class="metric-info">
								  <div class="metric-label">今日提现</div>
								  <div class="metric-value"><?php echo $data['tody_get']; ?></div>
							  </div>
						  </section>
					  </div>
					  <div class="col-lg-4 col-sm-4">
						  <section class="panel metric-card">
							  <div class="metric-icon fee"><i class="icon-cogs"></i></div>
							  <div class="metric-info">
								  <div class="metric-label">当天手续费</div>
								  <div class="metric-value"><?php echo $data['tody_shouxu']; ?></div>
							  </div>
						  </section>
					  </div>
				  </div>
              <!--state overview end-->


             <div class="row">
                  <div class="col-lg-12">
                      <section class="panel">
                          <header class="panel-heading">
                              最新交易记录
                              <a href="<?php echo url('order/orderlist'); ?>"><span class="right">更多>></span></a>
                          </header>

                          <table class="table table-advance table-hover">
                            <thead class="ordertable">
                              <tr>
                                <th>
                                    订单编号
                                </th>
                                <th>
                                    交易账号
                                </th>
                                <th>
                                    用户姓名
                                </th>
                                <th>
                                    订单时间
                                </th>
                                <th>
                                    产品信息
                                </th>
                                <th>
                                    状态
                                </th>
                                <th>
                                    方向
                                </th>
                                <th>
                                    时间/点数
                                </th>
                                <th>
                                    建仓点位
                                </th>
                                <th>
                                    平仓点位
                                </th>
                                <th>
                                    委托金额
                                </th>
                                <th>
                                    无效委托
                                </th>
                                <th>
                                    有效委托
                                </th>             
                                <th>
                                    实际盈亏
                                </th>
                                <th>
                                    买后余额
                                </th>
                                
                                <th>
                                    所属代理
                                </th>
                                <th>
                                    操作
                                </th>
                            </tr>
                          </thead>
                          <tbody>
                          <!-- <?php if(is_array($order) || $order instanceof \think\Collection || $order instanceof \think\Paginator): $i = 0; $__LIST__ = $order;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?> -->
                              <tr>
                                  <td><?php echo $vo['oid']; ?></td>
                                  <td><?php echo $vo['username']; ?></td>
                                  <td><?php echo $vo['nickname']; ?></td>
                                  <td><?php echo date("Y-m-d H:i:s",$vo['buytime']); ?></td>
                                  <td><?php echo $vo['ptitle']; ?></td>
                                  <td><?php if($vo['ostaus']==1): ?>平仓<?php else: ?>建仓<?php endif; ?></td>
                                  <?php if($vo['ostyle'] == 0): ?>
                                  <td class="color_red">买涨</td>
                                  <?php elseif($vo['ostyle'] == 1): ?>
                                  <td class="color_green">买跌</td>
                                  <?php endif; ?>
                                  <td><?php echo $vo['endprofit']; if($vo['eid']==1): ?>点<?php else: ?>秒<?php endif; ?></td>
                                  <td><?php echo $vo['buyprice']; ?></td>
                                  <td>
                                    <?php if($vo['ostaus'] == 1): if($vo["buyprice"] > $vo["sellprice"]): ?>
                                        <font color="#2fb44e" size="3"><?php echo $vo['sellprice']; ?></font>
                                      <?php else: ?>
                                        <font color="#ed0000" size="3"><?php echo $vo['sellprice']; ?></font>
                                      <?php endif; else: ?>
                                        <span <?php if($vo['pid'] == 1): ?> class="jks drop" <?php elseif($vo['pid'] == 2): ?> class="yks drop" <?php elseif($vo['pid'] == 3): ?> class="tks drop" <?php elseif($vo['pid'] == 4): ?> class="zsy drop" <?php endif; ?>></span>
                                    <?php endif; ?>
                                  </td>

                                  <td class="color_red">¥<?php echo $vo['fee']; ?></td>
                                  
                                  <?php if($vo['ploss'] == 0): ?>
                                  <td class="color_red">¥<?php echo $vo['fee']; ?></td>
                                  <?php else: ?>
                                  <td class="color_red">¥0</td>
                                  <?php endif; if($vo['ploss'] != 0): ?>
                                  <td class="color_red">¥<?php echo $vo['fee']; ?></td>
                                  <?php else: ?>
                                  <td class="color_red">¥0</td>
                                  <?php endif; ?>
                                  
                                  

                                  <td <?php if($vo['ploss'] > 0): ?> class="color_red" <?php else: ?> class="color_green" <?php endif; ?>>¥<?php echo $vo['ploss']; ?></td>
                                  <td class="color_red">¥<?php echo $vo['commission']; ?></td>
                                  <td><?php echo $vo['managername']; ?></td>
                                  <td>
                                      <button class="btn btn-primary btn-xs" title="点击查看"><i class="icon-list-alt"></i></button>
                                      
                                  </td>
                              </tr>
                              <!-- <?php endforeach; endif; else: echo "" ;endif; ?> -->
                              </tbody>
                          </table>
                      </section>
                  </div>
              </div>
              
             

          </section>
      </section>
      <!--main content end-->
  </section>



    <!-- js placed at the end of the document so the pages load faster -->
                    <audio id="audio" hidden="hidden"  loop="loop" preload="auto" controls="controls"src="../../../sy.mp3">       
</audio> <!--语音提示 end-->
    <script src="__ADMIN__/js/bootstrap.min.js"></script>
    <script src="__ADMIN__/js/jquery.scrollTo.min.js"></script>
    <script src="__ADMIN__/js/jquery.nicescroll.js" type="text/javascript"></script>
    <script>
    // 修复 passive event listener 警告
    (function() {
        if (typeof jQuery !== 'undefined') {
            jQuery.event.special.touchstart = {
                setup: function( _, ns, handle ) {
                    this.addEventListener("touchstart", handle, { passive: !ns.includes("noPreventDefault") });
                }
            };
            jQuery.event.special.touchmove = {
                setup: function( _, ns, handle ) {
                    this.addEventListener("touchmove", handle, { passive: !ns.includes("noPreventDefault") });
                }
            };
            jQuery.event.special.wheel = {
                setup: function( _, ns, handle ) {
                    this.addEventListener("wheel", handle, { passive: true });
                }
            };
            jQuery.event.special.mousewheel = {
                setup: function( _, ns, handle ) {
                    this.addEventListener("mousewheel", handle, { passive: true });
                }
            };
        }
    })();
    </script>
    <script src="__ADMIN__/js/jquery.sparkline.js" type="text/javascript"></script>
    <script src="__ADMIN__/assets/jquery-easy-pie-chart/jquery.easy-pie-chart.js"></script>
    <script src="__ADMIN__/js/owl.carousel.js" ></script>
    <script src="__ADMIN__/js/jquery.customSelect.min.js" ></script>

    <!--common script for all pages-->
    <script src="__ADMIN__/js/common-scripts.js"></script>

    <!--script for this page-->
    <script src="__ADMIN__/js/sparkline-chart.js"></script>
    <script src="__ADMIN__/js/easy-pie-chart.js"></script>

    <!-- active -->
    <script src="/static/public/js/function.js"></script>
     
    <!-- date -->
    <script type="text/javascript" src="__ADMIN__/js/date/jquery.datetimepicker.js" charset="UTF-8"></script>
    <script>
    		 var media = document.getElementById('audio'); 
    		 var audioEnabled = false;
    		 
    		 // 监听用户首次交互以启用音频
    		 document.addEventListener('click', function() {
    		 	audioEnabled = true;
    		 }, { once: true });
    		 
	 setInterval(function() {
				$.ajax({
					url:"/admin/user/headerData",
					type:"get",
					success:function(res){
						if(res.data.cz>0||res.data.with > 0||res.data.jy>0){
							if(res.data.cz>0){
								media.src='https://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&spd=5&text=互金所提醒您有充值订单，请及时处理';
							}else if(res.data.with > 0){
								media.src='https://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&spd=5&text=互金所提醒您有提现订单，请及时处理';
							}else if(res.data.jy > 0){
								media.src='https://tts.baidu.com/text2audio?lan=zh&ie=UTF-8&spd=5&text=互金所提醒您有交易订单，请及时处理';
							}
							// 只在用户已交互后播放音频
							if(audioEnabled) {
								media.play().catch(function(error) {
									console.log('音频播放失败:', error);
								});
							}
							$("#czcount").html("充值("+res.data.cz+")")
							$("#withcount").html("提现("+res.data.with+")")
							$("#jycount").html("交易订单("+res.data.jy+")")
						}
					}
				})
    },10000);   /*提现语音提示结束*/
</script>
  </body>
</html>
<script>
 //owl carousel

      $(document).ready(function() {
          $("#owl-demo").owlCarousel({
              navigation : true,
              slideSpeed : 300,
              paginationSpeed : 400,
              singleItem : true

          });
      });

      //custom select box

      $(function(){
          $('select.styled').customSelect();
      });

      // 强制去除表格斑马纹效果
      $(document).ready(function() {
          $('.table tbody tr').each(function() {
              $(this).css({
                  'background-color': 'rgba(26, 26, 46, 0.7)',
                  'background': 'rgba(26, 26, 46, 0.7)'
              });
          });
          
          // 悬停效果 - 清晰无模糊
          $('.table tbody tr').hover(
              function() {
                  $(this).css({
                      'background-color': 'rgba(13, 110, 253, 0.8)',
                      'background': 'rgba(13, 110, 253, 0.8)',
                      'color': '#ffffff',
                      'transform': 'none',
                      'text-shadow': 'none'
                  });
                  $(this).find('td').css({
                      'color': '#ffffff',
                      'text-shadow': 'none',
                      'font-weight': '500'
                  });
                  // 特殊颜色处理
                  $(this).find('.color_red').css({
                      'color': '#ff4757',
                      'font-weight': '600',
                      'text-shadow': 'none'
                  });
                  $(this).find('.color_green').css({
                      'color': '#2ed573',
                      'font-weight': '600',
                      'text-shadow': 'none'
                  });
              },
              function() {
                  $(this).css({
                      'background-color': 'rgba(26, 26, 46, 0.7)',
                      'background': 'rgba(26, 26, 46, 0.7)',
                      'color': '#e0e6ed',
                      'transform': 'none',
                      'text-shadow': 'none'
                  });
                  $(this).find('td').css({
                      'color': '#e0e6ed',
                      'text-shadow': 'none',
                      'font-weight': 'normal'
                  });
                  // 恢复原始颜色
                  $(this).find('.color_red').css({
                      'color': 'red',
                      'font-weight': 'bold',
                      'text-shadow': 'none'
                  });
                  $(this).find('.color_green').css({
                      'color': 'green',
                      'font-weight': 'bold',
                      'text-shadow': 'none'
                  });
              }
          );
      });

</script>