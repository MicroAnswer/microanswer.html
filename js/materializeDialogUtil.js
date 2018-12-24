(function (window, M, $) {
    // 在页面上输出弹出框布局

    var _body = $("body");

    // -----------------------------------------------------------------------------------------------------------------
    // confirm + alert 的处理
    var _confirmDom = $("<div id='modal_confirmHtml' class='modal'>" +
        "<div class='modal-content'>" +
        "<h4 id='modal_confirmHtml_title'>提示</h4><div id='modal_confirmHtml_text'></div></div>" +
        "<div class='modal-footer'>" +
        "<a href='javascript:;' id='modal_confirmHtml_cancel' class='modal-close waves-effect waves-red btn-flat'>取消</a>" +
        "<a href='javascript:;' id='modal_confirmHtml_ok' class='modal-close waves-effect waves-green btn-flat'>确定</a>" +
        "</div></div>");
    var _onOpenStart, _onOpenEnd, _onCloseStart, _onCloseEnd;

    _confirmDom.ready(function () {
        // 部署confirm方法
        M.confirm = function (msg, OkTxt, CancelTxt, callBackOk, callBackCancel) {
            _confirmDom.find("#modal_confirmHtml_title").text("提示");
            _confirmDom.find("#modal_confirmHtml_text").html("").append(msg);
            _confirmDom.find("#modal_confirmHtml_ok").text(OkTxt || '确定').on("click", callBackOk);
            _confirmDom.find("#modal_confirmHtml_cancel").text(CancelTxt || '取消').css("display", "inline-block").on("click", callBackCancel);
            var ins = M.Modal.getInstance(_confirmDom[0]);

            ins.open();
        };
        // 部署alert方法
        M.alert = function (msg, callback) {
            var option = {
                title: '提示',
                msg: msg || '',
                btnTxt: '确定',
                callBack: callback || function () {},
                onOpenEnd: undefined
            };
            if (typeof msg === 'object') {
                $.extend(option, msg);
            }

            _onOpenEnd = option.onOpenEnd;

            _onCloseEnd = option.onCloseEnd;


            _confirmDom.find("#modal_confirmHtml_title").text(option.title || "提示");
            _confirmDom.find("#modal_confirmHtml_text").html("").append(option.msg || '');
            _confirmDom.find("#modal_confirmHtml_ok").text(option.btnTxt || '确定').on('click', option.callBack);
            _confirmDom.find("#modal_confirmHtml_cancel").css("display", "none").on('click', undefined);
            var ins = M.Modal.getInstance(_confirmDom[0]);

            ins.open();
        };

        M.close = function () {
            M.Modal.getInstance(_confirmDom[0]).close();
        };

        M.Modal.init(_confirmDom[0], {
            onOpenStart: function () {
                _onOpenStart && _onOpenStart();
            },
            onOpenEnd: function () {
                _onOpenEnd && _onOpenEnd();
            },
            onCloseStart: function () {
                _onCloseStart && _onCloseStart();
            },
            onCloseEnd: function () {
                _onCloseEnd && _onCloseEnd();
            }
        });
    });
    _body.append(_confirmDom);
    // -----------------------------------------------------------------------------------------------------------------

})(window, window.Materialize || window.M, window.jQuery || window.$);