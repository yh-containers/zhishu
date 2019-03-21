<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?=\Yii::$app->params['app_name']?></title>
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
    <!-- iCheck -->
    <link rel="stylesheet" href="/modules/admin/assets/bower_components/iCheck/square/blue.css">

</head>
<body class="hold-transition login-page">
<div class="login-box">
    <div class="login-logo">
        <a href="javascript:;"><b><?=\Yii::$app->params['app_name']?>后台管理</b></a>
    </div>
    <!-- /.login-logo -->
    <div class="login-box-body">

        <form action="" method="post" id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="form-group has-feedback">
                <input type="text" name="account" class="form-control" placeholder="帐号">
                <span class="glyphicon glyphicon-envelope form-control-feedback"></span>
            </div>
            <div class="form-group has-feedback">
                <input type="password" name="password" class="form-control" placeholder="****   ">
            </div>
            <div class="row">
                <div class="form-group has-feedback">
                    <div class="col-xs-6">
                        <input type="text" name="verify" class="form-control" placeholder="验证码">
                    </div>
                    <div class="col-xs-5">
                        <img src="<?= \yii\helpers\Url::to(['captcha'])?>" id="captcha-image" onclick="changeImage(this)" class="captcha"/>
                    </div>
                </div>
            </div>

            <div class="row" style="margin-top: 10px;">


                <!-- /.col -->
                <div class="col-xs-4">
                    <button type="button" id="submit" class="btn btn-primary btn-block btn-flat">登录</button>
                </div>
                <!-- /.col -->
            </div>
        </form>



    </div>
    <!-- /.login-box-body -->
</div>
<!-- /.login-box -->

<!-- jQuery 3 -->
<script src="/modules/admin/assets/bower_components/jquery/dist/jquery.min.js"></script>
<!-- Bootstrap 3.3.7 -->
<script src="/modules/admin/assets/bower_components/bootstrap/dist/js/bootstrap.min.js"></script>
<script>
    $(function () {
        $("#submit").click(function () {
            $.post($("#form").attr('action'),$("#form").serialize(),function(result){
                if(result.hasOwnProperty('url')){
                    window.location.href = result.url
                }else{
                    changeImage()
                    alert(result.msg)
                }
            })

        })
    });

    function changeImage() {
        var url = "<?= \yii\helpers\Url::to(['captcha'])?>";
        console.log(url.indexOf('?'));
        if(url.indexOf('?')>-1){
            url = url+'&m='+Math.random()
        }else{
            url = url+'?m='+Math.random()
        }
        $("#captcha-image").attr('src',url);
    }
</script>
</body>
</html>
