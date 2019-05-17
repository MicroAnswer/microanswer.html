<?php
include "../layui_base/head.php";
include "../layui_base/head-layui.php";
?>

    <!-- 页面标题 -->
    <title>测试界面</title>
</head>
<body>
<div id="context"></div>

<?php include "../layui_base/body-layui.php";?>
<script>
    layui.config({base: '/layui_mod/'}).use('test'); //加载入口
</script>
</body>
</html>
