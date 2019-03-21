<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?= $this->title?></title>
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="/modules/admin/assets/bower_components/bootstrap/dist/css/bootstrap.min.css">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="/modules/admin/assets/bower_components/font-awesome/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="/modules/admin/assets/bower_components/Ionicons/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="/modules/admin/assets/dist/css/AdminLTE.min.css">
    <!-- AdminLTE Skins. Choose a skin from the css/skins
         folder instead of downloading all of them to reduce the load. -->
    <link rel="stylesheet" href="/modules/admin/assets/dist/css/skins/_all-skins.min.css">
    <link rel="stylesheet" href="/assets/layui-v2.4.5/css/layui.css">


</head>
<body class="hold-transition skin-blue sidebar-mini">
<!-- Site wrapper -->
<div class="wrapper">

    <header class="main-header">
        <!-- Logo -->
        <a href="<?= \yii\helpers\Url::to(['index/index'])?>" class="logo">
            <!-- mini logo for sidebar mini 50x50 pixels -->
            <span class="logo-mini"><?= \Yii::$app->params['app_name_min']?></span>
            <!-- logo for regular state and mobile devices -->
            <span class="logo-lg"><b><?= \Yii::$app->params['app_name']?></b></span>
        </a>
        <!-- Header Navbar: style can be found in header.less -->
        <nav class="navbar navbar-static-top">
            <!-- Sidebar toggle button-->
            <a href="#" class="sidebar-toggle" data-toggle="push-menu" role="button">
                <span class="sr-only">Toggle navigation</span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </a>

            <div class="navbar-custom-menu">
                <ul class="nav navbar-nav">
                    <li class="dropdown user user-menu">
                        <a href="javascript:;">
                            <span class="hidden-xs"><?=\Yii::$app->controller->user_name?></span>
                        </a>
                    </li>
                    <li class="dropdown user user-menu">

                        <a href="<?=\yii\helpers\Url::to(['index/logout'])?>">
                            <span class="hidden-xs">退出登录</span>
                        </a>
                    </li>
                </ul>
            </div>
        </nav>
    </header>
    <?= \app\modules\admin\widgets\Menu::widget(['current_active'=>isset($this->params['current_active'])?$this->params['current_active']:[]])?>

    <!-- Content Wrapper. Contains page content -->
    <div class="content-wrapper">
        <!-- Content Header (Page header) -->
        <section class="content-header">

            <ol class="breadcrumb" style="float: none;position: unset;right:unset; font-size: 14px">
                <?php
                    if(isset($this->params['crumb']))
                    foreach($this->params['crumb'] as $key=>$vo) {
                ?>
                    <?= '<li><a href="javascript:;">'.(empty($key)?'<i class="fa fa-dashboard"></i>':'').$vo.'</a></li>'?>
                <?php }?>
            </ol>
        </section>

        <!-- Main content -->
            <!-- Default box -->
        <section class="content">
            <?php if (isset($this->blocks['content'])): ?>
                <?= $this->blocks['content'] ?>

            <?php endif; ?>
            <!-- /.box -->

        </section>
        <!-- /.content -->
    </div>
<!-- ./wrapper -->
    <!-- /.content-wrapper -->

    <footer class="main-footer">

    </footer>

    <!-- /.control-sidebar -->
    <!-- Add the sidebar's background. This div must be placed
         immediately after the control sidebar -->
    <div class="control-sidebar-bg"></div>
</div>

</body>
</html>
<!-- jQuery 3 -->
<script src="/modules/admin/assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/modules/admin/assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<!-- SlimScroll -->
<script src="/modules/admin/assets/bower_components/jquery-slimscroll/jquery.slimscroll.min.js"></script>
<!-- FastClick -->
<script src="/modules/admin/assets/bower_components/fastclick/lib/fastclick.js"></script>
<!-- AdminLTE App -->
<script src="/modules/admin/assets/dist/js/adminlte.min.js"></script>
<script src="/assets/layui-v2.4.5/layui.js"></script>
<script src="/modules/admin/assets/handle.js"></script>
<script>
    $(document).ready(function () {
        $('.sidebar-menu').tree()
    })
</script>

<?php if (isset($this->blocks['script'])): ?>
    <?= $this->blocks['script'] ?>

<?php endif; ?>
