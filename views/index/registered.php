<?php
$this->title = '注册';
?>
<?php $this->beginBlock('content');?>

<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">注册</div>
</header>

<main class="main mgtop">
    <div class="top_img"><img src="/assets/images/login.jpg"></div>
    <div class="registered wrap">
        <form action="" id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="box">
                <i class="icon iconfont icon-yonghutouxiang"></i>
                <input type="text" name="username" placeholder="用户名">
            </div>
            <div class="box">
                <i class="icon iconfont icon-youxiang"></i>
                <input type="text" name="email" id="email" placeholder="邮箱">
            </div>
            <div class="box">
                <i class="icon iconfont icon-yanzhengma"></i>
                <input type="text" name="verify" class="code" placeholder="验证码">
                <button type="button" id="get-verify"  onclick="$.common.sendVerify(this,$('#email'))"  data-type="0" class="code">获取验证码</button>
            </div>
            <div class="box">
                <i class="icon iconfont icon-mima"></i>
                <input type="password" name="password" placeholder="请设置密码">
            </div>
            <div class="box">
                <i class="icon iconfont icon-mima"></i>
                <input type="password" name="re_password" placeholder="请重新输入密码">
            </div>
            <div class="box">
                <i class="icon iconfont icon-tuijianma"></i>
                <input type="text" name="code" value="<?=\Yii::$app->session->get('req_code');?>" placeholder="推荐码（必填）">
            </div>
            <div class="agree"><input type="checkbox" name="" id="agree" class="on_checkbox"><label for="agree">我已阅读并同意</label><a href="javascript:;">《用户协议》</a></div>
            <div class="btn"><input type="button" name="" id="submit" value="立即注册"></div>
        </form>
    </div>
</main>

<?= \app\widgets\ProtocolReg::widget()?>

<?php $this->endBlock(); ?>

<?php $this->beginBlock('script')?>
<script>
    $(function(){


        $("#submit").click(function(){
            $.post($("#form").attr('action'),$("#form").serialize(),function(result){
                layer.msg(result.msg);
                if(result.code==1){
                    setTimeout(function(){window.location.href='<?=\yii\helpers\Url::to(['login'])?>'},1000)
                }
            })
        })
    })

</script>
<?php $this->endBlock()?>
