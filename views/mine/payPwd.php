<?php
$this->title = '支付密码';
$this->params = [
];
?>

<?php $this->beginBlock('content')?>
<header class="header red">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">支付密码</div>
</header>

<main class="main mgtop">
    <div class="pay_settings">
        <ul>
            <li><a href="<?=\yii\helpers\Url::to(['rest-pay-pwd'])?>">设置支付密码</a></li>
            <li><a href="<?=\yii\helpers\Url::to(['rest-pay-pwd-email'])?>">找回支付密码</a></li>
        </ul>
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
