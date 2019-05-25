<?php
include "./layui_base/head.php";
?>

<!-- 页面标题 -->
<title>Follow Microanswer</title>
<style>
    .logos {
        height: 70px;
        border: 1px solid gray;
        margin: 20px 20px;
    }
    a{
        cursor: pointer;
        -webkit-transition-duration: 150ms;
        -moz-transition-duration: 150ms;
        -ms-transition-duration: 150ms;
        -o-transition-duration: 150ms;
        transition-duration: 150ms;
    }
    a:hover {
        -webkit-transform: translateY(-1px);
        -moz-transform: translateY(-1px);
        -ms-transform: translateY(-1px);
        -o-transform: translateY(-1px);
        transform: translateY(-1px);
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
            <li>Follow</li>
            <li><a href="n_coffee.php">请喝咖啡</a></li>
            <li class="text-under-line">登录</li>
            <li class="text-under-line">注册</li>
        </ul>
        <span class="disable no-warp"><a class="btn" target="_blank" href="http://beian.miit.gov.cn">蜀ICP备17035828号</a></span>
        <?php include "./layui_base/cnzz.php"; ?>
    </div>
</div>

<!-- 内容区域 -->
<div class="max-width850">

    <h1 class="margin5px">Microanswer 在下面的站点活动:</h1>

    <a target="_blank" href="https://me.csdn.net/MicroAnswer"><img class="logos" src="img/ic_csdn.svg" alt="csdn" title="csdn"/></a>
    <a target="_blank" href="https://gitee.com/Microanswer"><img class="logos" src="img/ic_gitee.svg" alt="码云" title="码云"/></a>
    <a target="_blank" href="https://github.com/Microanswer"><img class="logos" src="img/ic_github.svg" alt="github" title="github"/></a>
    <a target="_blank" href="https://coding.net/u/MicroAnswer"><img class="logos" src="img/ic_coding.png" alt="Coding" title="Coding"/></a>

</div>


</body>
</html>