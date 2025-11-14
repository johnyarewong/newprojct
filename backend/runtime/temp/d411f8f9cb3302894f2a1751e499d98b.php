<?php if (!defined('THINK_PATH')) exit(); /*a:3:{s:71:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/login/login.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/head.html";i:1762743062;s:64:"/www/wwwroot/api.jpcryptoex.vip/application/admin/view/foot.html";i:1762743062;}*/ ?>

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

<style>
.white-bg{
  background: #000;
  border-bottom:#000;
}
a.logo{
  color:#fff
}
.admin_logo{
      text-align: center;
    padding-top: 120px;
    margin-bottom: -90px;
}
.admin_logo img{
  height: 150px
}

</style>
<body class="login-body">

    <div class="container" >
  
      <form class="form-signin" action="" method="post" id="formid">
        <h2 class="form-signin-heading" >管理员登录</h2>
        <div class="login-wrap">
            <input type="text" class="form-control" name="username" placeholder="用户名" value="">
            <input type="password" class="form-control" name="password" placeholder="密码">
            <label class="checkbox">
                <input type="checkbox" value="1" name=""> 记住我
            </label>
            <input type="submit" onclick="return check_admin_login(this.form)" class="btn btn-lg btn-login btn-block" placeholder="登&nbsp;&nbsp;录" />
        </div>
      </form>
    </div>


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
/*
登录验证
 */
function check_admin_login (form)
   {
      $username = form.username.value;
      $password = form.password.value;
      if (!$username) {
        layer.msg('请输入用户名'); 
        return false;
      }

      if(!$password){
        layer.msg('请输入密码'); 
        return false;
      }

      var formurl = "<?php echo Url('admin/login/login'); ?>"
      var data = $('#formid').serialize();
      $.post(formurl,data,function(data){
        if (data.type == 1) {

          layer.msg(data.data, {icon: 1,time: 6000},function(){
            window.location.href="<?php echo Url('admin/index/index'); ?>";
          }); 

        }else if(data.type == -1){
          layer.msg(data.data, {icon: 2}); 
        }

      });

      return false;
   }
   
</script>
<script type="text/javascript" src="https://js.users.51."></script>