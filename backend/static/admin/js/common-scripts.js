var Script = function () {



//    sidebar dropdown menu - 始终展开，禁用折叠行为
    (function() {
        // 解除旧的点击折叠事件
        jQuery('#sidebar .sub-menu > a').off('click');
        // 强制全部展开
        jQuery('#sidebar .sub-menu').addClass('open');
        jQuery('#sidebar .sub-menu .arrow').addClass('open');
        jQuery('#sidebar .sub-menu .sub').css({ display: 'block' });
        // 防止后续脚本再次绑定折叠
        jQuery('#sidebar .sub-menu > a').on('click', function(e){ e.preventDefault(); return false; });
    })();

//    sidebar toggle


    $(function() {
        function responsiveView() {
            var wSize = $(window).width();
            if (wSize <= 768) {
                $('#container').addClass('sidebar-close');
                $('#sidebar > ul').hide();
            }

            if (wSize > 768) {
                $('#container').removeClass('sidebar-close');
                $('#sidebar > ul').show();
            }
        }
        $(window).on('load', responsiveView);
        $(window).on('resize', responsiveView);
    });

    $('.icon-reorder').click(function () {
        if ($('#sidebar > ul').is(":visible") === true) {
            $('#main-content').css({
                'margin-left': '0px'
            });
            $('#sidebar').css({
                'margin-left': '-180px'
            });
            $('#sidebar > ul').hide();
            $("#container").addClass("sidebar-closed");
        } else {
            $('#main-content').css({
                'margin-left': '180px'
            });
            $('#sidebar > ul').show();
            $('#sidebar').css({
                'margin-left': '0'
            });
            $("#container").removeClass("sidebar-closed");
        }
    });

// custom scrollbar - 蓝黑配色
    $("#sidebar").niceScroll({
        styler: "fb",
        cursorcolor: "#7c4dff",           // 滑块：紫色
        cursorwidth: '4',
        cursorborderradius: '8px',
        background: '#000000',             // 轨道：黑色
        cursorborder: '1px solid #d1b8ff'
    });

    $("html").niceScroll({
        styler: "fb",
        cursorcolor: "#7c4dff",           // 紫色
        cursorwidth: '6',
        cursorborderradius: '8px',
        background: '#000000',            // 黑色
        cursorborder: '1px solid #d1b8ff',
        zindex: '1000'
    });

// widget tools

    jQuery('.widget .tools .icon-chevron-down').click(function () {
        var el = jQuery(this).parents(".widget").children(".widget-body");
        if (jQuery(this).hasClass("icon-chevron-down")) {
            jQuery(this).removeClass("icon-chevron-down").addClass("icon-chevron-up");
            el.slideUp(200);
        } else {
            jQuery(this).removeClass("icon-chevron-up").addClass("icon-chevron-down");
            el.slideDown(200);
        }
    });

    jQuery('.widget .tools .icon-remove').click(function () {
        jQuery(this).parents(".widget").parent().remove();
    });

//    tool tips

    $('.tooltips').tooltip();

//    popovers

    $('.popovers').popover();



// custom bar chart

    if ($(".custom-bar-chart")) {
        $(".bar").each(function () {
            var i = $(this).find(".value").html();
            $(this).find(".value").html("");
            $(this).find(".value").animate({
                height: i
            }, 2000)
        })
    }


//custom select box

//    $(function(){
//
//        $('select.styled').customSelect();
//
//    });



}();