<?php
$this->title = '找回密码';
$this->params = [
];
?>

<?php $this->beginBlock('content')?>
<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">找回密码</div>
</header>

<main class="main mgtop">
    <div class="top_img"><img src="/assets/images/login.jpg"></div>
    <div class="registered wrap">
        <form action="" id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="box">
                <i class="icon iconfont icon-youxiang"></i>
                <input type="text" name="email" id="email" placeholder="邮箱">
            </div>
            <div class="box">
                <i class="icon iconfont icon-yanzhengma"></i>
                <input type="text" name="verify" class="code" placeholder="验证码">
                <button type="button" id="get-verify"  onclick="$.common.sendVerify(this,$('#email'))"  data-type="1" class="code">获取验证码</button>
            </div>
            <div class="box">
                <i class="icon iconfont icon-mima"></i>
                <input type="password" name="password" placeholder="请设置密码">
            </div>
            <div class="box">
                <i class="icon iconfont icon-mima"></i>
                <input type="password" name="re_password" placeholder="请重新输入密码">
            </div>
            <duv class="btn"><input type="button" id="submit" name="" value="确认"></duv>
        </form>
    </div>
</main>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script>
    $(function(){

        $("#submit").click(function(){
            $.post($("#form").attr('action'),$("#form").serialize(),function(result){
                layer.msg(result.msg);
                if(result.code==1){
                    window.location.href="<?=\yii\helpers\Url::to(['index/login'])?>"
                }
            })
        })
    })

</script>
<?php $this->endBlock()?>
