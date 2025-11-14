
setInterval("ajaxpro()",1000);
function ajaxpro(){
    var geturl = "/index/index/ajaxindexpro";
    var type = '';
    $.get(geturl,function(data){
        if (data) {
            data = jQuery.parseJSON(Base64.decode(data));
            // console.log(data)
            var html1 = "";
            var html2 = "";
            var html3 = "";
            $.each(data,function(k,v){
                if (v.isup == 1)
                {
                    html1 += '<div data-v-12171d70="" style="color: #FF0000;" class="Li" onclick="goToPage('+v.pid+')">\n' +
                        '                        <div data-v-12171d70="" class="left">\n' +
                        '                            <div data-v-12171d70="">\n' +
                        '                                <p data-v-12171d70="" class="p1">'+v.ptitle+'</p>\n' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                        <div data-v-12171d70="" class="center">\n' +
                        '                            <p data-v-12171d70="" class="p1" style="color:rgb(255, 0, 0);text-align:center;">'+v.Price+'</p>\n' +
                        '                        </div>\n' +
                        '                         <div data-v-12171d70="" class="center">\n' +
                        '                            <p data-v-12171d70="" class="p1" style="color:rgb(255, 0, 0);text-align:center;">'+v.High+'</p>\n' +
                        '                        </div>\n' +
                        '                    </div>';

                    html2 += '<div data-v-12171d70="" class="Li" onclick="goToPage('+v.pid+')">\n' +
                        '                        <div data-v-12171d70="" class="left">\n' +
                        '                            <div data-v-12171d70="">\n' +
                        '                                <p data-v-12171d70="" class="p1">'+v.ptitle+'</p>\n' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                        <div data-v-12171d70="" class="center">\n' +
                        '                            <p data-v-12171d70="" class="p1" style="color: rgb(28, 173, 144);">'+v.Price+'</p>\n' +
                        '                        </div>\n' +
                        '                        <div data-v-12171d70="" class="right">\n' +
                        '                            <p data-v-12171d70="" class="zf2" style="font-size: 12px;">'+(Math.random()*(8000 - 100) + 100).toFixed(3)+'万</p>\n' +
                        '                        </div>\n' +
                        '                    </div>';

                    html3 += '<div data-v-be3a9f74="" class="li" onclick="goToPage('+v.pid+')">\n' +
                        '                    <p data-v-be3a9f74="" class="p1">'+v.ptitle+'</p>\n' +
                        '                    <!---->\n' +
                        '                    <p data-v-be3a9f74="" class="p2" style="color: rgb(255, 0, 0);">'+v.Price+'</p>\n' +
                        '                    <p data-v-be3a9f74="" class="p3" style="color: rgb(255, 0, 0);">'+v.High+'</p>\n' +
                        '                </div>';
                }
                else
                {
                    html1 += '<div data-v-12171d70="" class="Li" onclick="goToPage('+v.pid+')">\n' +
                        '                        <div data-v-12171d70="" class="left">\n' +
                        '                            <div data-v-12171d70="">\n' +
                        '                                <p data-v-12171d70="" class="p1">'+v.ptitle+'</p>\n' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                        <div data-v-12171d70="" class="center">\n' +
                        '                            <p data-v-12171d70="" class="p1" style="color:rgb(28, 173, 144);text-align:center;">'+v.Price+'</p>\n' +
                        '                        </div>\n' +
                        '                         <div data-v-12171d70="" class="center">\n' +
                        '                            <p data-v-12171d70="" class="p1" style="color:rgb(28, 173, 144);text-align:center;">'+v.High+'</p>\n' +
                        '                        </div>\n' +
                        '                    </div>';

                    html2 += '<div data-v-12171d70="" class="Li" onclick="goToPage('+v.pid+')">\n' +
                        '                        <div data-v-12171d70="" class="left">\n' +
                        '                            <div data-v-12171d70="">\n' +
                        '                                <p data-v-12171d70="" class="p1">'+v.ptitle+'</p>\n' +
                        '                            </div>\n' +
                        '                        </div>\n' +
                        '                        <div data-v-12171d70="" class="center">\n' +
                        '                            <p data-v-12171d70="" class="p1" style="color: rgb(28, 173, 144);">'+v.Price+'</p>\n' +
                        '                        </div>\n' +
                        '                        <div data-v-12171d70="" class="right">\n' +
                        '                            <p data-v-12171d70="" class="zf2" style="font-size: 12px;">'+(Math.random()*(8000 - 100) + 100).toFixed(3)+'万</p>\n' +
                        '                        </div>\n' +
                        '                    </div>';

                    html3 += '<div data-v-be3a9f74="" class="li" onclick="goToPage('+v.pid+')">\n' +
                        '                    <p data-v-be3a9f74="" class="p1">'+v.ptitle+'</p>\n' +
                        '                    <!---->\n' +
                        '                    <p data-v-be3a9f74="" class="p2" style="color: rgb(28, 173, 144);">'+v.Price+'</p>\n' +
                        '                    <p data-v-be3a9f74="" class="p3" style="color: rgb(28, 173, 144);">'+v.High+'</p>\n' +
                        '                </div>';

                }
                // $('#pid'+v.pid+' .prtitle').html(v.ptitle);
                // $('#pid'+v.pid+' .now-value').html(v.Price);
                // $('#pid'+v.pid+' .rise-low').html(v.Low);
                // $('#pid'+v.pid+' .rise-high').html(v.High);
                //
                // if(v.isup == 1){
                //
                //     $('#pid'+v.pid+' .now-value').addClass('rise-value');
                //     $('#pid'+v.pid+' .now-value').removeClass('fall-value');
                //
                //     $('#pid'+v.pid+' .rise-low').addClass('rise');
                //     $('#pid'+v.pid+' .rise-low').removeClass('fall');
                //
                //     $('#pid'+v.pid+' .rise-high').addClass('rise');
                //     $('#pid'+v.pid+' .rise-high').removeClass('fall');
                //
                // }else if(v.isup == 0){
                //     $('#pid'+v.pid+' .now-value').removeClass('rise-value');
                //     $('#pid'+v.pid+' .now-value').addClass('fall-value');
                //
                //     $('#pid'+v.pid+' .rise-low').removeClass('rise');
                //     $('#pid'+v.pid+' .rise-low').addClass('fall');
                //
                //     $('#pid'+v.pid+' .rise-high').removeClass('rise');
                //     $('#pid'+v.pid+' .rise-high').addClass('fall');
                // }


            });
            $("#show-list-div").html(html1);
            $("#show-list-div-for-chengjiaoe").html(html2);
            $("#show-heng-div").html(html3);
        }

    });
}
