$(function () {
  // 文字上下轮播效果
  var showMarquee = function ($el, cls, num) {
    $el.addClass(cls);
    setTimeout(function () {
      var li = $el.find('li');
      if (num == '1') {
        $el.append(li[0]);
      }
      $el.removeClass(cls);
    }, 500)
  }
  var timer1 = setInterval(function () {
    showMarquee($('.marquee-animate .marquee'), 'show-marquee', '1')
  }, 2000);

  // banar 轮播
  var bannarSwiper = new Swiper('.bannar-swiper-container', {
    loop : true,
    autoplay: 3000,
    pagination : '.bannar-pagination',
    paginationClickable :true,
    effect : 'coverflow',
    grabCursor: true,
    preventLinksPropagation : true,
    slidesPerView: 1,
    spaceBetween: 10,
    centeredSlides: true,
    coverflow: {
        rotate: 20,
        stretch: 40,
        depth: 80,
        modifier: 1,
        slideShadows : false
    },
   // autoplay: true,
   // slidesPerView: 1,
   // spaceBetween: 10,
   // pagination: {
    //  el: '.swiper-pagination',
    //  clickable: true,
   // },
  });

  

  $(".rate-list .list-wrap").eq(0).show();
  // 排行榜
  function tab() {
    $(".rate-hd li").off("click").on("click", function () {
      var index = $(this).index();
      $(this).addClass("cur").siblings().removeClass("cur");
      $(".rate-list .list-wrap").eq(index).show().siblings().hide();
    });
  }
  tab();
  
  $(".header-right").delegate(".language","click",function(){
	  $('.header-right ul').slideToggle();
  });
  
})