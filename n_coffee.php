<?php
include "./layui_base/head.php";
?>

<!-- 页面标题 -->
<title>Microanswer</title>
<style>
    .payImg {
        width: 300px;
        border: 1px solid gray;
        border-radius: 3px;
        margin: 20px;
        box-shadow: 0 0 3px gray;
    }
</style>
</head>
<body>

<!-- 顶部导航 -->
<div class="clear-both shadow" style="background-color: #eeeeee">
    <div class="max-width850">
        <span class="logo">Microanswer.cn</span>
        <ul class="nav inline-block">
            <li><a href="n_home.php">主要页面</a></li>
            <li><a href="n_app.php">相关作品</a></li>
            <li><a href="n_devp.php">Follow</a></li>
            <li>请喝咖啡</li>
            <!--            <li class="text-under-line">登录</li>-->
            <!--            <li class="text-under-line">注册</li>-->
        </ul>
        <span class="disable no-warp"><a class="btn" target="_blank" href="http://beian.miit.gov.cn">蜀ICP备17035828号</a></span>
        <?php include "./layui_base/cnzz.php"; ?>
    </div>
</div>
<?php include './layui_base/mod_zan.php' ?>

<!-- 内容区域 -->
<div>

    <h1 class="margin5px">你可以通过下面的方式请 Microanswer 喝一杯咖啡：</h1>

    <img class="payImg" src="img/aliPayQr.png"/>
    <img class="payImg" src="img/wxPayQr.png"/>

</div>


</body>
</html>