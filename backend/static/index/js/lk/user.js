var listionhajax = '';
var is_ajax_list = '';
var page = 2;
var countdown=60;
function update_user() {
	
	var bankno = $('.bankno').val();
	var province = $('.province').val();
	var city = $('.city').val();
	var address = $('.address').val();
	var accntnm = $('.accntnm').val();
	var accntno = $('.accntno').val();
	var scard = $('.scard').val();
//	var phone = $('.phone').val();
//	var zjpwd = $('.zjpwd').val();
	var id = $('.mid').val();
	if(!bankno){layer.msg('请选择银行');return false;}
//	if(!province){layer.msg('请选择省份');return false;}
//	if(!city){layer.msg('请选择城市');return false;}
	if(!address){layer.msg('请输入支行地址');return false;}
	if(!accntnm){layer.msg('请输入开户名称');return false;}
	if(!accntno){layer.msg('请输入卡号');return false;}
//	if(!zjpwd){layer.msg('请输入提现密码');return false;}
//	if(!scard){layer.msg('请输入身份证号码');return false;}
//	if(!phone){layer.msg('请输入手机号');return false;}

	if(!accntnm.match("^[a-zA-Z0-9_\u4e00-\u9fa5]+$")){
    	layer.msg("请正确输入开户姓名");
    return false; 
  }
  
  if(!address.match("^[a-zA-Z0-9_\u4e00-\u9fa5]+$")){
    	layer.msg("请正确输入支行地址");
    return false; 
  }
  
  if(!accntno.match("^[a-zA-Z0-9_\u4e00-\u9fa5]+$")){
    	layer.msg("请正确输入卡号");
    return false; 
  }
  
 
  
  
	var postdata = 'bankno='+bankno+"&provinceid="+province+"&cityno="+city+"&address="+address+"&accntnm="+accntnm+"&accntno="+accntno;

	if(id){
	    
		postdata += "&id="+id
	}
	var posturl = "/index/user/dobanks";
	$.post(posturl,postdata,function(resdata){
		layer.msg(resdata.data);
		if(resdata.type == 1){
			setTimeout('gourl()',1000);
		}
	})
	
}
function update_qpayment() {
    var bankname = $('.bankname').val();
    var accntnm = $('.accntnm2').val();
    var accntno = $('.accntno2').val();
    var scard = $('.scard2').val();
    var phone = $('.phone2').val();
    var id = $('.id2').val();
    if(!accntnm){layer.msg('请输入开户名称');return false;}
    if(!accntno){layer.msg('请输入卡号');return false;}
  //  if(!scard){layer.msg('请输入身份证号码');return false;}
 //   if(!phone){layer.msg('请输入手机号');return false;}
 
 	if(!accntnm.match("^[a-zA-Z0-9_\u4e00-\u9fa5]+$")){
    	layer.msg("请正确输入开户姓名");
    return false; 
  }
  
  if(!address.match("^[a-zA-Z0-9_\u4e00-\u9fa5]+$")){
    	layer.msg("请正确输入支行地址");
    return false; 
  }
  
  if(!accntno.match("^[a-zA-Z0-9_\u4e00-\u9fa5]+$")){
    	layer.msg("请正确输入卡号");
    return false; 
  }
  

 
    var postdata = 'bankname='+bankname+"&accntnm="+accntnm+"&accntno="+accntno+"&scard="+scard+"&phone="+phone;
    if(id){
        postdata += "&id="+id
    }
    var posturl = "/index/user/quickbanks";
    $.post(posturl,postdata,function(resdata){
        layer.msg(resdata.data);
        if(resdata.type == 1){
            setTimeout('gourl()',1000);
        }
    })
}
function remove_qpayment() {
	var id = $('.id2').val();
    if(!id){layer.msg('系统出错了');return false;}
    postdata = "id="+id;
    var posturl = "/index/user/remove_quickbanks";
    $.post(posturl,postdata,function(resdata){
        layer.msg(resdata.data);
        if(resdata.type == 1){
            setTimeout('gourl()',1000);
        }
    })
}
function gourl() {
	
	history.go(0);
}


function apply_tx(){
    
}


	/**
	 * 监听输入提现金额
	 * @author lukui  2017-07-05
	 * @param  {[type]} ) {		var       price [description]
	 * @return {[type]}   [description]
	 */
	$('.cash input').bind('input propertychange', function() {
		var price = $('.cash-price').val();
		var reg_par = $('.reg_par').attr('attrdata');
		var true_price = (price*(100-reg_par)/100).toFixed(2);
		$('.true_price').html(true_price);
		$('.true_price').show();
	});
/**
 * 资金流水
 * @author lukui  2017-07-05
 * @param  {[type]} ){	var isshow        [description]
 * @return {[type]}         [description]
 */
$(document).on("click",'.price_list li',function(){
	var isshow = $(this).attr('isshow');
	if(isshow == 0){
		$('.today_list_footer').hide();
		$('.price_list li').attr('isshow',0);
		$('.clickshow').addClass('ion-ios-arrow-up');
		$('.clickshow').removeClass('ion-ios-arrow-down');
		$(this).find('.clickshow').removeClass('ion-ios-arrow-up');
		$(this).find('.clickshow').addClass('ion-ios-arrow-down');
		$(this).find('.today_list_footer').show();
		$(this).attr('isshow',1);
	}else{
		$(this).find('.clickshow').addClass('ion-ios-arrow-up');
		$(this).find('.clickshow').removeClass('ion-ios-arrow-down');
		$(this).find('.today_list_footer').hide();
		$(this).attr('isshow',0);
	}
	
});
listionhajax = setInterval("listionh()",1000);
/**
 * 监听高度
 * @author lukui  2017-07-05
 * @return {[type]} [description]
 */
function listionh() {
    if($(".price_list li:last").attr('ng-repeat')){
        var ScrollTop = $(".price_list li:last").offset().top; 
        if(ScrollTop <1000 ){
        	ajax_price_list();
        }
    }
    
}
/**
 * ajax加载资金流水
 * @author lukui  2017-07-05
 * @return {[type]} [description]
 */
function ajax_price_list() {
	
	var url = "/index/user/ajax_price_list?page="+page;
    var html = '';
    if(is_ajax_list == 1){
        return ;
    }
    is_ajax_list = 1;
    $.get(url,function(resdata){
        
        console.log(resdata);
        
        var res_list = resdata.data;
        if(res_list.length == 0){
            clearInterval(listionhajax);
            is_ajax_list = 1;
            return;
        }
        $.each(res_list,function(k,v){
        	if(v.type == 2){
        		var other_money = v.account*-1;
        	}else{
        		var other_money = v.account;
        	}
        	html += '<li ng-repeat="c in moneyList" class="" isshow="0">\
                	<div class="money_list_header">\
                		<section class="other_money_bg">\
                		</section><section>\
                			<p class="ng-binding other_money">'+v.title+'</p>\
                			<p>\
                				<i class="iconfont icon--1 "></i>\
                				<i class="iconfont icon-30 ng-hide"></i>\
                				<span class="ng-binding">'+v.nowmoney+'</span></p>\
                			<p>\
                				<i class="iconfont icon--2 pay_blue"></i>\
                				<span class="ng-binding">'+getLocalTime(v.time)+'</span>\
                			</p>\
                		</section><section class="ng-binding other_money">\
                			'+other_money+'                		</section><section class="icon clickshow ion-ios-arrow-up">\
                		</section>\
                	</div>\
                	<article class="today_list_footer" style="display: none;">\
                		<p class="ng-binding">详情：'+v.content+'</p>\
                	</article>\
                </li>';
        
        
    	})
        $('.price_list').append(html);
        page++;
        is_ajax_list = 0;
    })
}
/**
 * 发送验证码
 * @return {[type]} [description]
 */
function get_svg() {
	
	
	var phone = $('.username').val();
	if(!(/^1[3456789]\d{9}$/.test(phone))){
        layer.msg("请正确输入手机号！");
        return false;
    }
	
	var url = "/index/login/sendmsm/phone/"+phone;
	$.get(url,function(resdata){
		console.log(resdata);
		layer.msg(resdata.data);
		if(resdata.type == 1){
			$(".code_btn").attr('onclick',"return false;");
			listion_sendmsm();
		}
	})
	return false;
}
function listion_sendmsm(){
	 var time= 61;
    setTime=setInterval(function(){
        if(time<=1){
            clearInterval(setTime);
            $(".code_btn").text("再发一次");
            $(".code_btn").attr('onclick',"return get_svg();");
            return;
        }
        time--;
        $(".code_btn").text(time+"s");
    },1000);
}
/*$(".bpprice").blur(function(){
	var bpprice  = $('.bpprice').val();
	if(!bpprice || isNaN(bpprice)){
		bpprice = 200;
	}
	var num1=Math.random();
    var newNum1 =num1.toFixed(1);
	var num=Math.random();
	var newNum =num.toFixed(2);
	if(newNum1*10 > 5){
		var newbpprice = (bpprice*100 + newNum*100)/100;
	}else{
		var newbpprice = (bpprice*100 - newNum*100)/100;
	}
	$('.bpprice').val(newbpprice);
	
});*/
/**
 * 充值
 * @return {[type]} [description]
 */
function submit_deposit() {
	
	if(pay_type == ''){
		layer.msg('请选择支付类型');
		return false;
	}
	
    var userpay_min = $('#userpay_min').html();
	var userpay_max = $('#userpay_max').html();
	
	
	var bpprice  = $('.bpprice').val();
	var bankname = $('.wpbankname').val();
	var checkCode = $('.checkCode').val();
	var quickbank_order = $('#quickbank_order').val();
	if(!bpprice || isNaN(bpprice)){
		layer.msg('请输入充值金额');
		return false;
	}
	
	if(bpprice*1 < userpay_min*1 || bpprice*1 > userpay_max*1){
		layer.msg('请输入充值金额为'+userpay_min+'~'+userpay_max+'之间');
		return false;
	}
	if(pay_type == 'jiupaikuaijie')
	{
		if(checkCode == '' || quickbank_order == '')
		{
            layer.msg('快捷支付请先获取验证码');
            return false;
		}
	}
	var posturl = "/index/user/addbalance";
	var postdata = "pay_type="+pay_type+"&bpprice="+bpprice+"&bankname="+bankname+'&checkCode='+checkCode+'&order_sn='+quickbank_order;
	$.post(posturl,postdata,function(res){
		if(res.type == -1){
			layer.msg(res.data);
		}else{
			if(pay_type == 'wxpay'){
				wxpay_info = JSON.parse(res);
				callpay(wxpay_info);
			}
			if(pay_type == 'zypay'){
				$('#zypay_post').html(res);
				
			}
			if(pay_type == 'quickpay'){
				location.href = res;
				
			}
			if(pay_type == 'jiupaikuaijie'){
				//$('.modal-deposit').find('.scroll').html(res);
                layer.msg(res.rspMessage);
			}
			if(pay_type == 'jiupaiyinlian')
			{
                $('#zypay_post').html(res);
			}
			if(pay_type == 'yy_wxpay' || pay_type == 'yy_alipay' || pay_type == 'yy_qqpay' ){
				$('#zypay_post').html(res);
			}
			if(pay_type == 'qbt_pay_wxpay'){
				location.href = res;
			}
			if(pay_type == 'alipay'){
				$('#zypay_post').html(res);
			}
			if(pay_type == 'qtb_alipay'){
				location.href = res;
			}
			if(pay_type == 'qtb_yinlian'){
				location.href = res;
			}
			if(pay_type == 'upay'){
				$('#zypay_post').html(res);
			}			
			if(pay_type == 'pa_Weixin'||pay_type == 'pa_Alipay'||pay_type == 'pa_Qpay'){
				if(isWeiXin() && pay_type == 'pa_Weixin') {
					location.href = res;
				}else{
					$('.pay_code_area').css('display','block');
					$('.pay_code_img').html(res);
				}
				
			}
			
			if(pay_type == 'haobaipay'){
				location.href = res;
			}
			if(pay_type == '992' || pay_type == '1006' || pay_type == '1004' || pay_type == '3003' || pay_type == '3004' || pay_type == '3005' || pay_type == '3007' || pay_type == '1005'){
				location.href = res;
			}
			
		}
		
	})
}
function isWeiXin() {
	var ua = window.navigator.userAgent.toLowerCase();
	if (ua.match(/MicroMessenger/i) == 'micromessenger') {
		return true;
	} else {
		return false;
	}
}
function get_code(obj) {
    if(pay_type == ''){
        layer.msg('请选择支付类型');
        return false;
    }
    var bpprice  = $('.bpprice').val();
    var bankname = $('.bankname').val();
    if(!bpprice || isNaN(bpprice)){
        layer.msg('请输入充值金额');
        return false;
    }
    var posturl = "/index/user/quickpayinit";
    var postdata = "pay_type="+pay_type+"&bpprice="+bpprice+"&bankname="+bankname;
    $.post(posturl,postdata,function(res){
        if(res.type == -1){
            layer.msg(res.data);
        }else{
            layer.msg(res.rspMessage);
            if(res.rspCode=='IPS00000'){
                $('#quickbank_order').val(res.orderId);
            }
		}
	});
    settime(obj);
}
function settime(obj) {
    if (countdown == 0) {
        obj.removeAttribute("disabled");
        obj.value = "获取验证码";
        countdown = 60;
        return;
    } else {
        obj.setAttribute("disabled", true);
        obj.value = "重新发送(" + countdown + ")";
        countdown--;
    }
    setTimeout(function() {
            settime(obj) }
        ,1000)
}
function check_payid(id) {
	pay_type = id;
	if(pay_type == 'jiupaiyinlian')
	{
		$('#bankname').show();
	}else{
		$('#bankname').hide();
	}
    if(pay_type == 'jiupaikuaijie')
    {
        $('#quickbank').show();
    }else{
        $('#quickbank').hide();
    }
}
//调用微信JS api 支付
function jsApiCall(obj)
{
	
    WeixinJSBridge.invoke(
        'getBrandWCPayRequest',
        obj,
        function(res){
            WeixinJSBridge.log(res.err_msg);
            //alert(res.err_code+'|'+res.err_desc+'|'+res.err_msg);
            if(res.err_msg.indexOf('ok')>0){
            	layer.msg('充值成功！');
                window.location.href=returnrul;
            }
        }
    );
}
function callpay(obj)
{
    if (typeof WeixinJSBridge == "undefined"){
        if( document.addEventListener ){
            document.addEventListener('WeixinJSBridgeReady', jsApiCall, false);
        }else if (document.attachEvent){
            document.attachEvent('WeixinJSBridgeReady', jsApiCall); 
            document.attachEvent('onWeixinJSBridgeReady', jsApiCall);
        }
    }else{
        jsApiCall(obj);
    }
}
function sQrcode(qdata,classname){
	console.log(qdata);
	$("."+classname).empty().qrcode({		// 调用qQcode生成二维码
			render : "canvas",    			// 设置渲染方式，有table和canvas，使用canvas方式渲染性能相对来说比较好
			text : qdata,    				// 扫描了二维码后的内容显示,在这里也可以直接填一个网址或支付链接
			width : "165",              	// 二维码的宽度
			height : "165",             	// 二维码的高度
			background : "#ffffff",     	// 二维码的后景色
			foreground : "#000000",     	// 二维码的前景色
			src: ""    						// 二维码中间的图片
		});	
		
}	
/**
 * 扫码支付区域
 * @return {[type]} [description]
 */
function pay_code_area(type) {
	if(type == 0){
		$('.pay_code_area').hide();
	}else if(type == 1){
		$('.pay_code_area').show();
		can_balance(1);
	}
}
function can_balance(type) {
	if(type == 0){
		$('.reg_btn').attr('onclick',' ');
		$('.reg_btn').html('请稍后');
	}else if(type == 1){
		$('.reg_btn').attr('onclick','submit_deposit()');
		$('.reg_btn').html('确认充值');
	}
}