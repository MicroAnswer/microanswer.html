<?php
require_once __DIR__ . './user/Util.php';

use user\SQL;
use user\Util;

$util = new Util();
$sql = new SQL($util->db(null));
// 查询总赞数
$result = $sql->select('count(id) as zanCount')->from('likeme')->d0();
$zanCount = 0;
if ($result instanceof mysqli_result) {
    $obj = $result->fetch_array(MYSQLI_ASSOC);
    $zanCount = $obj['zanCount'] ?: 0;
}
?>
<!DOCTYPE html>
<html style="height: 100%;margin: 0;padding: 0">
<head>
    <meta charset="UTF-8">
    <!-- 优先使用 IE 最新版本和 Chrome -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <!-- 页面描述 -->
    <meta name="description" content="欢迎访问范Microanswer的网页。你可以留言并参看他的最新作品。"/>
    <!-- 页面关键词 -->
    <meta name="keywords" content="Java,Microanswer,answer,范雪蛟,小范,阿蛟,Micro,JavaScript,开发,Vue,Php,主机,域名"/>
    <!-- 网页作者 -->
    <meta name="author" content="Microanswer, 范雪蛟, microanswer@outlook.com"/>
    <!-- 搜索引擎抓取 -->
    <meta name="robots" content="index,follow"/>
    <!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
    <meta name="HandheldFriendly" content="true">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>Microanswer</title>
    <script src="./js/store.min.js?t=102"></script>
    <script src="./js/jquery.min.js?t=102"></script>
    <script src="./js/layer/layer.js?t=102"></script>
    <link href="./css/welcome.css?t=102" rel="stylesheet" type="text/css">
</head>
<body style="background-color: #fcfcfc;height: 100%;margin: 0;padding: 0">
<table style="height: 100%;width: 100%;margin: 0;padding: 0">
    <tbody><!-- 使用表格来初始居中在任意浏览器都不会有太大差别 -->
    <tr style="height: 33%"><?php for($i=0;$i<3;$i++){?><td style="width: 33%"></td><?php }?></tr>
    <tr>
        <td style="width: 33%"></td>
        <td style="width: 33%">
            <div class="card-box">
                <!-- 名片 -->
                <div class="card">
                    <!-- 反面 -->
                    <div class="back">
                        <table style="width: 100%;color: white;font-size: 3mm;" cellspacing="0" cellpadding="0">
                            <tbody>
                            <tr>
                                <td style="padding: 3.5mm 6mm;line-height: 4.5mm">
                                    <div style="text-align: center;margin-top: 2cm">空空如也，求一个好设计</div>
                                </td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                    <!-- 正面 -->
                    <div class="front">
                        <div class="nickname">Microanswer</div>
                        <div class="signature">我是稀的。</div>
                        <div style="margin-top: 2.5cm;">
                            <table cellpadding="0" cellspacing="0" style="width: 100%">
                                <tbody>
                                <tr>
                                    <td style="width: 14mm">
                                        <div style="margin-left: 6mm">
                                            <img src="/img/head.jpg" class="headimg"/>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="name">范雪蛟</div>
                                        <div class="email">
                                            <table cellpadding="0" cellspacing="0" style="width: 100%"><tbody><tr>
                                                    <td>microanswer@outlook.com</td>
                                                    <td align="right"><a class="zan dis" id="zanBtn" href="javascript:;" title="赞赏对方">赞<span
                                                                    id="zhanCount">(<?= $zanCount ?>)</span></a></td>
                                                </tr></tbody></table>
                                        </div>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
            <!-- 按钮区域 -->
            <div style="text-align: center;margin-top: 20px;">
                <a class="btn" href="app.html?t=105" title="作品">作品</a>
                <i class="dot"></i>
                <a class="btn" href="javascript:;" id="btnRedPacket" title="领取红包">领红包</a>
                <i class="dot"></i>
                <a class="btn" id="tog" href="javascript:;" title="按住查看背面">背面</a>
                <!--<i class="dot"></i>-->
                <!--<a class="btn" href="newquestion.php?t=103" title="我有问题要问">提问</a>-->
            </div>

        </td>
        <td style="width: 33%"></td>
    </tr>
    <tr style="height: 33%"><?php for($i=0;$i<3;$i++){?><td style="width: 33%"></td><?php }?></tr>
    </tbody>
</table>
<div class="beian-foot">
    <a class="btn" target="_blank" href="http://www.miitbeian.gov.cn"  style="font-size: 12px;color: gray">蜀ICP备17035828号</a>
    <a class="btn" href="javascript:;" id="btnAliPay"  style="font-size: 12px;color: gray">支付宝付款</a>
    <a class="btn" href="javascript:;" id="btnWxPay"  style="font-size: 12px;color: gray">微信付款</a>
    <script type="text/javascript">var cnzz_protocol = (("https:" == document.location.protocol) ? "https://" : "http://");document.write(unescape("%3Cspan style='vertical-align:middle' id='cnzz_stat_icon_1263962182'%3E%3C/span%3E%3Cscript src='" + cnzz_protocol + "s22.cnzz.com/z_stat.php%3Fid%3D1263962182%26show%3Dpic' type='text/javascript'%3E%3C/script%3E"));</script>
</div>
</body>
<script src="./welcome.js?t=102"></script>
</html>