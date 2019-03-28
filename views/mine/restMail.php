<?php
$this->title = '重置邮箱';
$this->params = [
];
?>

<?php $this->beginBlock('content')?>

<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">重置邮箱</div>
</header>

<main class="main mgtop">
    <div class="top_img"><img src="/assets/images/login.jpg"></div>
    <div class="registered wrap">
        <form action="" id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="box">
                <i class="icon iconfont icon-youxiang"></i>
                <input type="text" name="email" id="email" placeholder="请输入新邮箱">
            </div>
            <div class="box">
                <i class="icon iconfont icon-yanzhengma"></i>
                <input type="text" name="verify" class="code" placeholder="验证码">
                <button type="button" id="get-verify"  onclick="$.common.sendVerify(this,$('#email'))"  data-type="2" class="code">获取验证码</button>
            </div>
            <duv class="btn"><input type="button" id="submit" name="" value="保存"></duv>
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
                    setTimeout(function(){window.location.href='<?=\yii\helpers\Url::to(['mine/index'])?>'},1000)
                }
            })
        })
    })

</script>
<?php $this->endBlock()?>
