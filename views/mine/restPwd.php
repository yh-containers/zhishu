<?php
$this->title = '修改密码';
$this->params = [
];
?>

<?php $this->beginBlock('content')?>

<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title"><?=$user_model['pay_pwd']?'修改支付密码':'设置支付密码'?></div>
</header>

<main class="main mgtop">
    <div class="top_img"><img src="/assets/images/login.jpg"></div>
    <div class="registered wrap">
        <form action="" id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="box">
                <i class="icon iconfont icon-pay-key"></i>
                <input type="password" name="old_pwd" placeholder="请输入旧密码">
            </div>
            <div class="box">
                <i class="icon iconfont icon-pay-key"></i>
                <input type="password" name="password" placeholder="请设置密码">
            </div>
            <div class="box">
                <i class="icon iconfont icon-pay-key"></i>
                <input type="password" name="re_password" placeholder="请再次输入密码">
            </div>
            <div class="btn"><input type="button" id="submit" name="" value="保存"></div>
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
            })
        })
    })

</script>
<?php $this->endBlock()?>
