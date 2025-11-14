<?php if (!defined('THINK_PATH')) exit(); /*a:4:{s:73:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/goods/prolist.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/head.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/menu.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/foot.html";i:1762743062;}*/ ?>
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



<style>
/* 现代化产品管理页面样式 */
#main-content {
    background: linear-gradient(135deg, #f5f7fa 0%, #e8ecf1 100%);
    min-height: 100vh;
    padding: 20px;
}

.modern-header {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    border-radius: 16px;
    padding: 30px;
    margin-bottom: 30px;
    box-shadow: 0 10px 30px rgba(102, 126, 234, 0.3);
    color: white;
}

.modern-header h2 {
    margin: 0;
    font-size: 28px;
    font-weight: 700;
    color: white;
}

.modern-header p {
    margin: 8px 0 0 0;
    opacity: 0.95;
    font-size: 14px;
}

.stats-row {
    display: flex;
    gap: 20px;
    margin-bottom: 25px;
}

.stat-card {
    flex: 1;
    background: white;
    border-radius: 12px;
    padding: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    transition: all 0.3s ease;
}

.stat-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 20px rgba(0,0,0,0.12);
}

.stat-card .stat-icon {
    width: 50px;
    height: 50px;
    border-radius: 10px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 24px;
    margin-bottom: 12px;
}

.stat-card.total .stat-icon {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.stat-card.active .stat-icon {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
}

.stat-card.inactive .stat-icon {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
}

.stat-card .stat-value {
    font-size: 32px;
    font-weight: 700;
    color: #2d3748;
    margin: 0;
}

.stat-card .stat-label {
    font-size: 13px;
    color: #718096;
    margin-top: 5px;
}

.toolbar {
    background: white;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 20px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.search-box {
    display: flex;
    gap: 10px;
    align-items: center;
}

.search-input {
    border: 2px solid #e2e8f0;
    border-radius: 10px;
    padding: 10px 15px;
    width: 300px;
    font-size: 14px;
    transition: all 0.3s ease;
}

.search-input:focus {
    outline: none;
    border-color: #667eea;
    box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
}

.btn-modern {
    padding: 10px 24px;
    border-radius: 10px;
    font-weight: 600;
    font-size: 14px;
    border: none;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 8px;
}

.btn-modern.btn-add {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(102, 126, 234, 0.3);
}

.btn-modern.btn-add:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(102, 126, 234, 0.4);
}

.btn-modern.btn-sort {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    box-shadow: 0 4px 15px rgba(245, 87, 108, 0.3);
}

.btn-modern.btn-sort:hover {
    transform: translateY(-2px);
    box-shadow: 0 6px 20px rgba(245, 87, 108, 0.4);
}

.product-table-container {
    background: white;
    border-radius: 12px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.08);
    overflow: hidden;
}

.modern-table {
    width: 100%;
    border-collapse: separate;
    border-spacing: 0;
}

.modern-table thead {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
}

.modern-table thead th {
    padding: 18px 15px;
    text-align: left;
    font-weight: 600;
    font-size: 13px;
    color: white;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    border: none;
}

.modern-table tbody tr {
    transition: all 0.3s ease;
    border-bottom: 1px solid #e2e8f0;
}

.modern-table tbody tr:hover {
    background: linear-gradient(90deg, #f7fafc 0%, #edf2f7 100%);
    transform: scale(1.01);
}

.modern-table tbody td {
    padding: 16px 15px;
    color: #4a5568;
    font-size: 14px;
    vertical-align: middle;
}

.sort-input {
    width: 60px;
    padding: 8px 10px;
    border: 2px solid #f8f9fa;
    border-radius: 8px;
    text-align: center;
    font-weight: 600;
    transition: all 0.3s ease;
    background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
    color: #495057;
    box-shadow: 0 2px 8px rgba(255, 255, 255, 0.5), inset 0 1px 3px rgba(0, 0, 0, 0.05);
}

.sort-input:focus {
    outline: none;
    border-color: #e9ecef;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(248, 249, 250, 0.6), 0 4px 12px rgba(255, 255, 255, 0.8);
}

.product-id {
    background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
    color: white;
    padding: 6px 12px;
    border-radius: 8px;
    font-weight: 700;
    font-size: 13px;
    display: inline-block;
}

.product-name {
    font-weight: 600;
    color: #2d3748;
    font-size: 15px;
}

.status-badge {
    padding: 6px 14px;
    border-radius: 20px;
    font-weight: 600;
    font-size: 12px;
    display: inline-block;
    text-align: center;
    min-width: 70px;
}

.status-badge.open {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(56, 239, 125, 0.3);
}

.status-badge.closed {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 87, 108, 0.3);
}

.category-tag {
    background: #edf2f7;
    color: #4a5568;
    padding: 6px 12px;
    border-radius: 8px;
    font-size: 13px;
    font-weight: 500;
}

.value-box {
    background: #f7fafc;
    padding: 6px 10px;
    border-radius: 6px;
    font-weight: 600;
    color: #2d3748;
    display: inline-block;
}

.action-buttons {
    display: flex;
    gap: 6px;
}

.btn-action {
    padding: 8px 12px;
    border-radius: 8px;
    border: none;
    font-size: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    display: inline-flex;
    align-items: center;
    gap: 4px;
}

.btn-action.btn-open {
    background: linear-gradient(135deg, #11998e 0%, #38ef7d 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(56, 239, 125, 0.3);
}

.btn-action.btn-open:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(56, 239, 125, 0.4);
}

.btn-action.btn-close {
    background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(245, 87, 108, 0.3);
}

.btn-action.btn-close:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(245, 87, 108, 0.4);
}

.btn-action.btn-edit {
    background: linear-gradient(135deg, #ffd89b 0%, #19547b 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(25, 84, 123, 0.3);
}

.btn-action.btn-edit:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(25, 84, 123, 0.4);
}

.btn-action.btn-delete {
    background: linear-gradient(135deg, #ff6b6b 0%, #ee5a6f 100%);
    color: white;
    box-shadow: 0 2px 8px rgba(255, 107, 107, 0.3);
}

.btn-action.btn-delete:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 12px rgba(255, 107, 107, 0.4);
}

/* 响应式设计 */
@media (max-width: 768px) {
    .stats-row {
        flex-direction: column;
    }
    
    .toolbar {
        flex-direction: column;
        gap: 15px;
    }
    
    .search-input {
        width: 100%;
    }
}

/* 加载动画 */
@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(20px);
    }
    to {
        opacity: 1;
        transform: translateY(0);
    }
}

.modern-table tbody tr {
    animation: fadeIn 0.5s ease;
}

/* 图标样式 */
.icon {
    font-size: 16px;
}
</style>

<!--main content start-->
<section id="main-content">
    <section class="wrapper">
        
        <!-- 页面标题 -->
        <div class="modern-header">
            <h2>🎯 产品管理中心</h2>
            <p>管理您的所有产品，包括开市/休市状态、分类、风控参数等</p>
        </div>

        <!-- 统计卡片 -->
        <div class="stats-row">
            <div class="stat-card total">
                <div class="stat-icon">📦</div>
                <div class="stat-value" id="totalCount">0</div>
                <div class="stat-label">产品总数</div>
            </div>
            <div class="stat-card active">
                <div class="stat-icon">✅</div>
                <div class="stat-value" id="openCount">0</div>
                <div class="stat-label">开市中</div>
            </div>
            <div class="stat-card inactive">
                <div class="stat-icon">🔴</div>
                <div class="stat-value" id="closedCount">0</div>
                <div class="stat-label">休市中</div>
            </div>
        </div>

        <!-- 工具栏 -->
        <div class="toolbar">
            <div class="search-box">
                <input type="text" class="search-input" placeholder="🔍 搜索产品名称、编号..." id="searchInput">
            </div>
            <div style="display: flex; gap: 10px;">
                <button type="button" class="btn-modern btn-sort" onclick="document.querySelector('#sortForm').submit()">
                    ⚡ 保存排序
                </button>
                <a href="<?php echo url('goods/proadd'); ?>">
                    <button type="button" class="btn-modern btn-add">
                        ➕ 添加产品
                    </button>
                </a>
            </div>
        </div>

        <!-- 产品列表 -->
        <div class="product-table-container">
            <form action="proorder" method="post" id="sortForm">
                <table class="modern-table">
                    <thead>
                        <tr>
                            <th style="width: 80px;">排序</th>
                            <th style="width: 80px;">编号</th>
                            <th>产品名称</th>
                            <th style="width: 120px;">市场状态</th>
                            <th style="width: 120px;">所属分类</th>
                            <th style="width: 100px;">随机值</th>
                            <th style="width: 110px;">风控最小值</th>
                            <th style="width: 110px;">风控最大值</th>
                            <th style="width: 280px;">操作</th>
                        </tr>
                    </thead>
                    <tbody id="productTableBody">
                        <!-- <?php if(is_array($proinfo) || $proinfo instanceof \think\Collection || $proinfo instanceof \think\Paginator): $i = 0; $__LIST__ = $proinfo;if( count($__LIST__)==0 ) : echo "" ;else: foreach($__LIST__ as $key=>$vo): $mod = ($i % 2 );++$i;?> -->
                        <tr data-search="<?php echo $vo['ptitle']; ?> <?php echo $vo['pid']; ?>">
                            <td>
                                <input class="sort-input" type="text" name="proorder[<?php echo $vo['pid']; ?>]" value="<?php echo $vo['proorder']; ?>">
                            </td>
                            <td>
                                <span class="product-id">#<?php echo $vo['pid']; ?></span>
                            </td>
                            <td>
                                <span class="product-name"><?php echo $vo['ptitle']; ?></span>
                            </td>
                            <td>
                                <?php if($vo['isopen'] == 1): ?>
                                    <span class="status-badge open">🟢 开市</span>
                                <?php else: ?>
                                    <span class="status-badge closed">🔴 休市</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span class="category-tag"><?php echo $vo['pcname']; ?></span>
                            </td>
                            <td>
                                <span class="value-box"><?php echo $vo['rands']; ?></span>
                            </td>
                            <td>
                                <span class="value-box"><?php echo $vo['point_low']; ?></span>
                            </td>
                            <td>
                                <span class="value-box"><?php echo $vo['point_top']; ?></span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <?php if($vo['isopen'] == 1): ?>
                                        <button type="button" class="btn-action btn-close" onclick="isopen(0,<?php echo $vo['pid']; ?>)">
                                            休市
                                        </button>
                                    <?php else: ?>
                                        <button type="button" class="btn-action btn-open" onclick="isopen(1,<?php echo $vo['pid']; ?>)">
                                            开市
                                        </button>
                                    <?php endif; ?>
                                    <a href="<?php echo url('goods/proadd',array('pid'=>$vo['pid'])); ?>">
                                        <button type="button" class="btn-action btn-edit" title="编辑产品">
                                            ✏️ 编辑
                                        </button>
                                    </a>
                                    <button type="button" class="btn-action btn-delete" onclick="deleteinfo('<?php echo $vo['pid']; ?>')" title="删除产品">
                                        🗑️ 删除
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <!-- <?php endforeach; endif; else: echo "" ;endif; ?> -->
                    </tbody>
                </table>
            </form>
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
// 统计数据
function updateStats() {
    const rows = document.querySelectorAll('#productTableBody tr');
    let totalCount = 0;
    let openCount = 0;
    let closedCount = 0;
    
    rows.forEach(row => {
        if (row.style.display !== 'none') {
            totalCount++;
            const statusBadge = row.querySelector('.status-badge');
            if (statusBadge && statusBadge.classList.contains('open')) {
                openCount++;
            } else {
                closedCount++;
            }
        }
    });
    
    document.getElementById('totalCount').textContent = totalCount;
    document.getElementById('openCount').textContent = openCount;
    document.getElementById('closedCount').textContent = closedCount;
}

// 初始化统计
updateStats();

// 搜索功能
document.getElementById('searchInput').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#productTableBody tr');
    
    rows.forEach(row => {
        const searchData = row.getAttribute('data-search').toLowerCase();
        if (searchData.includes(searchTerm)) {
            row.style.display = '';
        } else {
            row.style.display = 'none';
        }
    });
    
    updateStats();
});

/**
 * 开、休市控制器
 */
function isopen(data, pid) {
    var formurl = "<?php echo Url('goods/proisopen'); ?>";
    var data = "isopen=" + data + "&pid=" + pid;
    var locurl = "<?php echo Url('admin/goods/prolist'); ?>";
    
    WPpost(formurl, data, locurl);
    return false;
}

/**
 * 删除产品
 */
function deleteinfo(id) {
    layer.open({
        content: '⚠️ 确定要删除这个产品吗？此操作不可恢复！',
        btn: ['确定删除', '取消'],
        yes: function(index) {
            var url = "<?php echo url('goods/delpro'); ?>" + "?id=" + id;
            var locurl = "<?php echo Url('admin/goods/prolist'); ?>";
            WPget(url, locurl);
            layer.close(index);
        }
    });
}

// 添加平滑滚动
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        e.preventDefault();
        document.querySelector(this.getAttribute('href')).scrollIntoView({
            behavior: 'smooth'
        });
    });
});
</script>