<?php
// 赞赏模块。
 ?>

<div id="zanID" style="max-height: 0;background-color: #ff6f74;overflow: hidden;-webkit-transition-duration: 300ms;-moz-transition-duration: 300ms;-ms-transition-duration: 300ms;-o-transition-duration: 300ms;transition-duration: 300ms;">
    <div class="margin5px">给 Microanswer 一个赞吧！
        <button id="zanBTn" style="background-color: #57e085;margin-left: 20px">　赞　</button>
        <button id="cancelZan" style="margin-left: 30px">不用了</button>
    </div>
</div>
<script>
    (function () {
        let zan = {
            init () {

                zan.doms.cancelZan.bind('click', zan.methods.onCancelZanClick);
                zan.doms.zanBTn.bind('click', zan.methods.onZanClick);

                setTimeout(function () {
                    zan.methods.initZan();
                }, 10000);
            },
            doms: {
                zan: $('#zanID'),
                cancelZan: $('#cancelZan'),
                zanBTn: $('#zanBTn')
            },
            methods: {
                show: function () {
                    zan.doms.zan[0].style.maxHeight = zan.doms.zan[0].scrollHeight + "px";
                },
                hide: function () {
                    zan.doms.zan[0].style.maxHeight = "0";
                },
                onCancelZanClick: function () {
                    zan.methods.hide();
                    setTimeout(function () {
                        zan.doms.zan[0].remove();
                    }, 400);
                },
                onZanClick: function () {
                    zan.doms.zan.css('background-color', "#57e085");
                    zan.doms.zan.html("<div class='margin5px'>Microanswer 非常感谢来自你的鼓励。</div>");
                    setTimeout(function () {
                        zan.methods.hide();
                    }, 3000);
                    $.ajax({
                        url: './api/common.php',
                        dataType: 'json',
                        type: 'post',
                        contentType: 'application/json',
                        data: JSON.stringify({
                            method: 'zhanMicroanswer',
                            data: {
                                zhan: 'yes'
                            }
                        }),
                        success: function (res) {
                            if (res) {
                                store.set('zanMeTime', new Date().getTime());
                            }
                        },
                        error: function (XMLHTTPrequest, errStr, exception) {}
                    })
                },
                initZan: function () {
                    // 上一次赞赏是 2 小时以内，不让用户再次赞赏
                    var zanMeTime = (store.get('zanMeTime') || 0) + (2 * 3600 * 1000);
                    var now = new Date().getTime();
                    var canLike = now > zanMeTime;
                    if (!canLike) {
                        // 不显示赞。
                        console.log("2小时内赞过，不再显示。")
                    } else {
                        zan.methods.show();
                    }
                }
            }
        };
        zan.init();
    })();
</script>
