/**
 * 把所有的操作放在 function 中， 不要直接放在 window 作用域下。
 * 这样可以防止控制台拿到 me 对象控制界面，减少部分懂程序的人通过
 * 控制台进行非法操作。
 */
$(function () {

    /**
     * 此页面的所有操作都汇聚在 me 对象中
     */
    var me = {

        /**
         * 保存所有要用到的界面上的控件的引用。
         */
        doms: {
            // ftBtnb: $('#ftvideo'),
            zanBtn: $('#zanBtn'),
            redPacketBtn: $('#btnRedPacket'),
            btnAliPay: $('#btnAliPay'), // 付款按钮
            btnWxPay: $('#btnWxPay'), // 付款按钮
            zhanCount: $('#zhanCount'),
            tog: $('#tog'),
            card: $('.card'),
            back: $('div.back')
        },

        /**
         * 初始化方法。
         */
        init: function () {

            // 添加监听
            me.doms.tog.bind('mousedown', me.methods.onTogOn);
            me.doms.tog.bind('touchstart', me.methods.onTogOn);
            me.doms.tog.bind('touchend', me.methods.onTogOut);
            me.doms.tog.bind('touchcancel', me.methods.onTogOut);
            me.doms.tog.bind('mouseup', me.methods.onTogOut);
            me.doms.tog.bind('mouseleave', me.methods.onTogOut);
            // me.doms.ftBtnb.on('click', me.methods.onFtBtnClick);
            me.doms.redPacketBtn.bind('click', me.methods.onRedPacketBtnClick);
            me.doms.btnAliPay.bind('click', me.methods.onBtnAliPayClick);
            me.doms.btnWxPay.bind('click', me.methods.onBtnWxPayClick);

            // 初始化赞按钮
            me.methods.initZhanBtn();

            // 提示名片是可以翻转的
            me.methods.tipBack();
        },

        /**
         * 数据
         */
        datas: {
            canLike: false, // 标记 是否可以点击赞 按钮。 用户点击了赞按钮2小时内不能再次点击。
        },

        /**
         * 所有的方法
         */
        methods: {

            // 付款按钮都调用这个方法
            onPayClick: function (payQrImgSrc) {
                layer.open(
                    {
                        type: 1,
                        content: '<div style="text-align: center;">' +
                            '    <img style="width: 300px;box-shadow: 1mm 1mm 5mm 1mm rgba(0, 0, 0, 0.2);display: block;" src="' + payQrImgSrc + '">' +
                            '</div>',
                        skin: 'pay-dialog',
                        area: ['300px', '450px'],
                        btn: null,
                        title: null,
                        shadeClose: true
                    });
            },

            // 点击支付宝付款按钮
            onBtnAliPayClick: function () {
                me.methods.onPayClick('img/aliPayQr.png');
            },

            // 点击微信付款按钮
            onBtnWxPayClick: function () {
                me.methods.onPayClick('img/wxPayQr.png');
            },

            // 点击领取红包按钮
            onRedPacketBtnClick: function () {
                layer.alert(
                    '<div style="text-align: center">' +
                    '   <h2>支付宝扫码领红包</h2>' +
                    '   <img src="img/redQr.jpg" style="width: 200px;height: 200px;"/>' +
                    '   <p style="color: gray;">' +
                    '       <a href="javascript:;" id="actRule" style="text-decoration: none;font-size: 15px;">' +
                    '           活动规则<br/>' +
                    '           <span style="font-style: 11px;color: gray;">(内容来自支付宝，请以支付宝内规则为准)</span>' +
                    '       </a>' +
                    '   </p>' +
                    '</div>',
                    {
                        btn: null,
                        title: null,
                        shadeClose: true,
                        success: function (layero, index) {
                            $('#actRule').click(function (event) {
                                layer.open({
                                    type: 2,
                                    title: '活动规则',
                                    content: 'html/aliActRule.html',
                                    area: ['400px', '500px'],
                                    shadeClose: true
                                });
                            });
                        }
                    }
                )
            },
            // 点击访谈按钮
            onFtBtnClick: function () {
                layer.alert("访谈节目视频剪辑中...<br/>感谢您的访问</br>请稍后再试。")
            },

            // 显示背面
            onTogOn: function () {
                me.doms.card.addClass('showback');

                setTimeout(function () {
                    if (me.doms.card.hasClass('showback')) {
                        // 显示背面的时候，就应该让背面显示出来，但是如果是 ie ，不支持 translateZ css。 所以只好用代码改变 z-index 让后面显示出来
                        me.doms.back.css('z-index', '2');
                    }
                }, 200)

                return false;
            },

            // 显示前面
            onTogOut: function () {
                me.doms.card.removeClass('showback');
                setTimeout(function () {
                    if (!me.doms.card.hasClass('showback')) {
                        // 显示背面的时候，就应该让背面显示出来，但是如果是 ie ，不支持 translateZ css。 所以只好用代码改变 z-index 让后面显示出来
                        me.doms.back.css('z-index', 'auto');
                    }
                }, 200)
                return false;
            },

            // 点击赞
            onZhanClick: function () {
                if (!me.datas.canLike) {
                    return;
                }
                me.methods._postZan(true, function (res, errStr) {
                    if (res) {
                        me.datas.canLike = false;
                        me.doms.zhanCount.text('(' + res.data.zanCount + ')');
                        me.doms.zanBtn.addClass('dis');
                        me.doms.zanBtn.attr('title', '赞成功了');
                        store.set('zanMeTime', new Date().getTime());
                    } else {
                        console.log('错误：' + errStr);
                    }
                })
            },

            // 提交 赞 请求到接口
            _postZan: function (zhan, back) {
                me.methods.ajax('zhanMicroanswer', {zhan: zhan ? 'yes' : 'no'}, back);
            },

            /**
             * 执行 异步 ajax 请求
             * @param method 要请求的方法
             * @param param 参数
             * @param back 完成回调。 back(res, errStr);
             */
            ajax: function (method, param, back) {
                $.ajax({
                    url: './api/common.php',
                    dataType: 'json',
                    type: 'post',
                    contentType: 'application/json',
                    data: JSON.stringify({method: method, data: param}),
                    success: function (res) {
                        if (res.code == 200) {
                            back && back(res, undefined);
                        } else {
                            back && back(undefined, "[" + res.code + "] " + res.msg);
                        }
                    },
                    error: function (XMLHTTPrequest, errStr, exception) {
                        back && back(undefined, "[" + errStr + "] " + exception);
                    }
                })
            },

            /**
             * 初始化赞按钮的状态
             */
            initZhanBtn: function () {
                // 上一次赞赏是 2 小时以内，不让用户再次赞赏
                var zanMeTime = (store.get('zanMeTime') || 0) + (2 * 3600 * 1000);
                var now = new Date().getTime();
                me.datas.canLike = now > zanMeTime;
                if (!me.datas.canLike) {
                    me.doms.zanBtn.addClass('dis');
                    me.doms.zanBtn.attr("title", "你在不久前就已经赞过了");
                } else {
                    me.doms.zanBtn.removeClass('dis');
                    me.doms.zanBtn.attr("title", "赞一下");
                    me.doms.zanBtn.click(me.methods.onZhanClick);
                }
            },

            // 没有提示过，提示名片可以翻转的。
            tipBack: function () {
                var tiped = store.get('do2tiped') || false;
                if (!tiped) {
                    setTimeout(function () {
                        me.methods.onTogOn();
                        setTimeout(function () {
                            me.methods.onTogOut();
                            store.set('do2tiped', true);
                        }, 1500);
                    }, 500);
                }
            }
        }
    };

    me.init();
});