<?php
$this->title = '转账';
$this->params = [
    'body_style' => 'style="background: #fff;"',
];
?>

<?php $this->beginBlock('content')?>

<header class="header red">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">转账</div>
</header>

<main class="main mgtop">
    <div class="transfer wrap">
        <div class="top">
            <div class="avatar"><img src="<?=$charge_user_info['face']?>"><?=$charge_user_info['type']?'<i class="icon iconfont icon-vip"></i>':''?></div>
            <div class="text">
                <h2>ID：<?=$charge_user_info['username']?><span><?=!empty($charge_user_info)?$charge_user_info->getTypeName():''?></span></h2>
                <p>（<?=$charge_user_info ? ($charge_user_info->getOnline()?'在线':'离线'):'离线'?>）</p>
            </div>
        </div>
        <form action="" id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input name="to_uid" type="hidden" value="<?=$charge_user_info['id']?>"/>
            <div class="box">
                <label>转账<?=\Yii::$app->params['money_name']?></label>
                <input type="number" name="money" placeholder="请输入<?=\Yii::$app->params['money_name']?>数量">
            </div>
            <div class="btn"><input type="button" id="submit" name="" value="确认转账"></div>
            <div class="link" >
                <a href="<?=\yii\helpers\Url::to(['rest-pay-pwd'])?>"  class="fr" style="padding: 20px">设置支付密码</a>
            </div>
        </form>
    </div>
</main>
<div class="pwd_pop">
    <header class="header red">
        <a href="javascript:;" class="header_left close"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
        <div class="header_title">输入支付密码</div>
    </header>

    <main class="main mgtop">
        <div class="container-fluid">
            <div class="tishi">请输入密码</div>
            <div class="keyboard-show-text"></div>
            <div class="keyboard-box">
            </div>
        </div>
    </main>
</div>

<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script type="text/javascript" src="/assets/js/keyboard.js"></script>
<link href="/assets/css/bootstrap.min.css" rel="stylesheet">
<link href="/assets/css/animate.css" rel="stylesheet">
<script>
    $(function(){

        $(".keyboard-box").KeyBoard({
            random: true, // 随机键盘
            type: "password", // 密码 password or 金额 money
            show: $(".keyboard-show-text"), // 展示区域
            safe: false, // 加密显示
            handlePay:sure_submit
        });

        /*密码弹窗*/
        $(".transfer .btn").click(function(){
            $(".pwd_pop").show();
        });

        $(".pwd_pop .close").click(function(){
            $(".pwd_pop").hide();
        });
        function sure_submit(pwd){
            layer.confirm('是否进行转账操作?',function(){
                var index = layer.load(0, {time: 3000})
                var res_data = $("#form").serialize()+'&pay_pwd='+pwd;
                $.post($("#form").attr('action'),res_data,function(result){
                    layer.msg(result.msg)
                    layer.close(index)
                    if(result.code==1){
                        setTimeout(function(){window.history.back()},1000)
                    }
                })
            })
        }

    })
</script>
<?php $this->endBlock()?>
