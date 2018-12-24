(function (window, $) {

    if (typeof $ === "undefined") {
        throw new Error("请先引入Jquery");
    }

    if (typeof window.store === "undefined") {
        console.warn("请在Util之前引入store.min.js");
    }

    if (typeof window.weui === "undefined") {
        console.warn("请在Util之前引入weui.min.js + weui.min.css");
    }

    var TWEEN = {
        easeOutQuart: function (pos) {
            return -(Math.pow((pos - 1), 4) - 1);
        }
    };

    var __innerUtil = {
        __getKey: function (key) {
            return 'microanswer_' + key + '_';
        }
    };

    window.CONSTANT = {
        KEY_LOGIN_TOKEN: '_key_login_token_'
    };

    window.Util = {
        API: {
            COMMON: function () {
                var url = "/api/common.php";
                return {
                    GET_MESSAGE: [url, "getMessage"]
                }
            },
            USER: function () {
                var url = "/user/index.php";
                return {
                    LOGIN: [url, "login"],
                    REGIST: [url, "regist"],
                    GET_USER_INFO: [url, "getUserInfo"]
                }
            }
        },

        /**
         * 如果val为空，  抛出msg 错误
         * @param val
         * @param msg
         */
        nullThrow: function (val, msg) {
            if (!val || val.length < 1) {
                throw new Error(msg);
            }
        },

        /**
         * 如果 f 为 false， 抛出错误
         * @param f
         * @param msg
         */
        falseThrow: function (f, msg) {
            if (!f) {
                throw new Error(msg);
            }
        },

        /**
         * 页面跳转到 url
         * @param url
         */
        pageTo: function (url) {
            window.location.href = url;
        },

        /**
         * 网页替换到
         * @param url
         */
        pageReplaceTo: function (url) {
            window.location.replace(url);
        },

        /**
         * 平滑滚动到指定位置
         * @param x 目标x坐标
         * @param y 目标Y坐标
         * @param time 平滑时间(ms)
         * @param onEnd 动画完成回调
         * @param interpolator  速度控制器
         */
        scrollTo: function (x, y, time, onEnd, interpolator) {
            if (window._sclin_) {
                // 滚动还没有完成，不执行
                return
            }
            x = x || 0;
            y = y || 0;
            var startX = window.scrollX;
            var startY = window.scrollY;

            var distanceX = x - startX;
            var distanceY = y - startY;
            if (distanceX === 0 && distanceY === 0) {
                // 没有意义的滚动
                return undefined
            }

            var targetX = startX + distanceX;
            var targetY = startY + distanceY; // 结束位置

            time = time || 500;
            var ftp = 100;
            onEnd = onEnd || function () {
                console.log('scroll end')
            }; // 结束回掉
            var ease = TWEEN[interpolator] || TWEEN.easeOutQuart; // 要使用的缓动公式
            var startTime = new Date().getTime() // 开始时间
                // 开始执行
            ;(function dd () {
                setTimeout(function () {
                    window._sclin_ = true;
                    var newTime = new Date().getTime(); // 当前帧开始时间
                    var timestamp = newTime - startTime; // 逝去的时间
                    var detal = ease(timestamp / time);
                    var detal2 = ease(timestamp / time);
                    var result1 = Math.ceil(startX + detal * distanceX);
                    var result2 = Math.ceil(startY + detal2 * distanceY);
                    // console.log(', to：' + result2)
                    window.scrollTo(result1, result2);
                    if (time <= timestamp) {
                        window.scrollTo(targetX, targetY);
                        onEnd();
                        window._sclin_ = false
                    } else {
                        setTimeout(dd, 1000 / ftp)
                    }
                }, 1000 / ftp)
            })()
        },

        /**
         * 请求网络
         */
        post: function (url, method, param, success, fail) {
            $.ajax({
                url: url,
                type: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({
                    data: param,
                    method: method
                }),
                dataType: "json",
                processData: false,
                timeout: 30 * 1000,
                error: function (XMLHTTPRequest, errStr, exception) {
                    fail && fail((errStr || '') + ', ' + XMLHTTPRequest.statusText + ' [' + XMLHTTPRequest.status + ']');
                },
                success: function (result) {
                    if (result.code !== 200) {
                        fail && fail(result.msg, result.code);
                        return;
                    }
                    success && success(result.data);
                }
            });
        },

        /**
         * 带有提示的网络请求
         */
        postUI: function (option, txt) {
            if (__innerUtil.load) {
                Util.hideLoading(__innerUtil.load);
            }
            if (option.api) {
                option.url = option.api[0];
                option.method = option.api[1];
            }
            __innerUtil.load = Util.loading(txt || '加载中...');
            Util.post(option.url, option.method, option.param, function (result) {
                option.success && option.success(result);
                Util.hideLoading(__innerUtil.load);
            }, function (errStr, code) {
                Util.hideLoading(__innerUtil.load);
                errStr = errStr || '服务器错误，请稍后。';
                option.fail && option.fail(errStr, code);
                Util.alert(errStr);
            });
        },

        /**
         * 获取一条缓存数据
         * @param key
         */
        storeGet: function (key) {
            key = __innerUtil.__getKey(key);
            return window.store.get(key);
        },

        /**
         * 保存一条缓存数据
         * @param key
         * @param value
         */
        storeSet: function (key, value) {
            key = __innerUtil.__getKey(key);
            window.store.set(key, value);
        },

        /**
         * 移除一条缓存数据
         * @param key
         */
        storeRemove: function (key) {
            key = __innerUtil.__getKey(key);
            window.store.remove(key);
        },

        /**
         * 清除所有缓存
         * @param key
         */
        storeClear: function () {
            window.store.clear();
        },

        /**
         * 弹出警告消息
         * @param msg
         */
        alert: function (msg) {
            window.weui.alert(msg);
        },

        /**
         * 弹出顶部提示
         * @param msg
         */
        topTip: function (msg, type, dur) {
            window.weui.topTips(msg, {
                duration: dur || 2000,
                className: 'toptip-' + (type || 'default'),
                callback: function () {}
            });
        },

        /**
         * 显示加载提示
         * @param txt
         */
        loading: function (txt) {
            return window.weui.loading(txt);
        },

        /**
         * 隐藏加载提示
         * @param load
         */
        hideLoading: function (load) {
            load.hide();
        },

        /**
         * 退出登录
         * @param msg
         * @param callback
         */
        confirm: function (msg, callback) {
            window.weui.confirm(msg, callback);
        },

        /**
         * 获取当前设备类型
         * 0:常规登录，1:电脑登录，2:Android设备登录，3:IOS设备登录，4:未知设备
         */
        getDeviceType: function () {
            var type = "1";

            var sUserAgent = navigator.userAgent.toLowerCase();
            var isIpad = sUserAgent.match(/ipad/i) == "ipad";
            var isIphoneOs = sUserAgent.match(/iphone os/i) == "iphone os";
            var isMidp = sUserAgent.match(/midp/i) == "midp";
            var isUc7 = sUserAgent.match(/rv:1.2.3.4/i) == "rv:1.2.3.4";
            var isUc = sUserAgent.match(/ucweb/i) == "ucweb";
            var isAndroid = sUserAgent.match(/android/i) == "android";
            var isCE = sUserAgent.match(/windows ce/i) == "windows ce";
            var isWindows = sUserAgent.match(/windows nt/i) == "window nt";
            var isWM = sUserAgent.match(/windows mobile/i) == "windows mobile";
            if (isIpad || isIphoneOs) {
                type = "3";
            } else if (isAndroid) {
                type = "2";
            } else if (isWindows) {
                type = "1";
            } else if (isMidp || isWM) {
                type = "4";
            }
            return type;
        },

        /**
         * 获取浏览器名称
         * @returns {RegExpMatchArray | null}
         */
        getBrowserName: function () {
            var OsObject = navigator.userAgent;
            // 包含「Opera」文字列 
            if (OsObject.indexOf("Opera") != -1) {
                return "Opera";
            }
            // 包含「MSIE」文字列 
            else if (OsObject.indexOf("MSIE") != -1) {
                return "Internet Explorer";
            }
            // 包含「chrome」文字列 ，不过360浏览器也照抄chrome的UA
            else if (OsObject.indexOf("Chrome") != -1) {
                return "chrome";
            }
            // 包含「UCBrowser」文字列 
            else if (OsObject.indexOf("UCBrowser") != -1) {
                return "UCBrowser";
            }
            // 包含「BIDUBrowser」文字列 
            else if (OsObject.indexOf("BIDUBrowser") != -1) {
                return "百度浏览器";
            }
            // 包含「Firefox」文字列 
            else if (OsObject.indexOf("Firefox") != -1) {
                return "Firefox";
            }
            // 包含「Netscape」文字列 
            else if (OsObject.indexOf("Netscape") != -1) {
                return "Netscape";
            }
            // 包含「Safari」文字列 
            else if (OsObject.indexOf("Safari") != -1) {
                return "Safari";
            }
            else {
                return "未知";
            }
        },

        /**
         * 判断当前浏览器是否是 像素鸟 游戏中的浏览器
         */
        isFlappyBirdWeb: function () {
            return navigator.userAgent.indexOf("MicroanswerFlappyBirdGameWeb") > -1;
        }
    }
})(window, jQuery);