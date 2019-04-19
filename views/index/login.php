<?php
$this->title = '登录';
?>
<?php $this->beginBlock('content');?>
<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">登录</div>
</header>

<main class="main mgtop">
    <div class="top_img"><img src="/assets/images/login.jpg"></div>
    <div class="registered wrap">
        <form action="" id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <div class="box">
                <i class="icon iconfont icon-yonghutouxiang"></i>
                <input type="text" name="account" placeholder="邮箱/用户名">
            </div>
            <div class="box">
                <i class="icon iconfont icon-mima"></i>
                <input type="password" name="password" placeholder="请输入密码">
            </div>
            <duv class="btn"><input type="button" name="" id="submit" value="登录"></duv>
            <div class="link">
                <a href="<?= \yii\helpers\Url::to(['forget'])?>" class="fl">忘记密码？</a>
                <a href="<?= \yii\helpers\Url::to(['registered'])?>" class="fr">立即注册</a>
            </div>
        </form>
    </div>
</main>

<div class="maintain" style="display: <?= $sys_switch==1?'':'block'?>">
    <div class="content">
        <p><img src="/assets/images/maintain.png"></p>
        <p>系统在升级维护中，请稍后再试</p>
    </div>
</div>


<?php $this->endBlock(); ?>

<?php $this->beginBlock('script')?>
<script>

    $(function(){


        $("#submit").click(function(){
            $.post($("#form").attr('action'),$("#form").serialize(),function(result){
                if(result.hasOwnProperty('url')){
                    window.location.href=result.url
                }else{
                    layer.msg(result.msg)
                }
            })
        })
    })

</script>
<?php $this->endBlock()?>
