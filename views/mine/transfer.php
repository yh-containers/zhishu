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
                <h2>ID：<?=$charge_user_info['id']?><span><?=!empty($charge_user_info)?$charge_user_info->getTypeName():''?></span></h2>
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
        </form>
    </div>
</main>

<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script>
    $(function(){
        $("#submit").click(function(){
            layer.confirm('是否进行转账操作?',function(){
                var index = layer.load(0, {time: 3000})
                $.post($("#form").attr('action'),$("#form").serialize(),function(result){
                    layer.msg(result.msg)
                    layer.close(index)
                })
            })
        })
    })
</script>
<?php $this->endBlock()?>
