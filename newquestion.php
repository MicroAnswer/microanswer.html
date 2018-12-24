<?php
/**
 * Created by IntelliJ IDEA.
 * User: Micro
 * Date: 2018-10-17
 * Time: 23:26
 */
?>

<html style="height: 100%;margin: 0;padding: 0">
<head>
    <meta charset="UTF-8">
    <!-- 优先使用 IE 最新版本和 Chrome -->
    <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1"/>
    <!-- 页面描述 -->
    <meta name="description" content="欢迎访问范雪蛟(Microanswer)的网页。你可以留言并参看他的最新作品。"/>
    <!-- 页面关键词 -->
    <meta name="keywords"
          content="Java,Microanswer,answer,范雪蛟,范雪娇,小范阿娇,阿蛟,Micro,JavaScript,开发,Vue,Php,主机,域名,大神,高手,QQ飞车"/>
    <!-- 网页作者 -->
    <meta name="author" content="Microanswer, 范雪蛟, microanswer@gmail.com"/>
    <!-- 搜索引擎抓取 -->
    <meta name="robots" content="index,follow"/>
    <!-- 针对手持设备优化，主要是针对一些老的不识别viewport的浏览器，比如黑莓 -->
    <meta name="HandheldFriendly" content="true">
    <!--Let browser know website is optimized for mobile-->
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>提问</title>
    <link rel="stylesheet" type="text/css" href="./css/newquestion.css"/>
</head>
<body style="background-color: #fcfcfc;height: 100%;margin: 0;padding: 0">

<div class="main">

    <div class="page-title">提出新的疑问</div>

    <!-- 问题的标题输入 -->
    <div>
        <label class="desc" for="title">您的疑问是：</label><br/>
        <input class="title-input" id="title">
    </div>

    <!-- 问题详细描述的输入 -->
    <div style="margin-top: 10px;">
        <label class="desc" for="content">再详细的说明一下吧：</label><br/>
        <textarea class="content-input" id="content" rows="10"></textarea>
    </div>

    <!-- 标签输入 -->
    <div style="margin-top: 10px;">
        <label class="desc" for="keys">标签：</label><br/>
        <input id="keys" class="keys-input">
    </div>

    <!-- 设置相关 -->
    <div style="margin-top: 10px">
        <label class="desc">隐私设置：</label><br/>
        <label><input type="radio" name="permission">完全公开</label>
        <label><input type="radio" name="permission">仅我可见</label>
    </div>

    <div style="margin-top: 10px">
        <a href="javascript:;" id="btn-submuit">提交</a>
    </div>

</div>

<div class="coverDevinf">提问板块开发中...<br/>感谢您的使用</div>

</body>
</html>