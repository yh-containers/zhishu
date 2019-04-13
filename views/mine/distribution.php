<?php
$this->title = '分销中心';
$this->params = [
];
?>

<?php $this->beginBlock('content')?>
<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">我的分销</div>
</header>

<main class="main mgtop">
    <div class="distribution">
        <div class="top">
            <div class="avatar"><img src="<?=$user_model['face']?>">
                <?php if($user_model['type']>0){?>
                    <i class="icon iconfont icon-vip"></i>
                <?php }?>
            </div>
            <div class="text">
                <h2>ID：<?=$user_model['username']?>
                    <span><?=$user_model->typeName?></span>
                </h2>
                <p><?=$user_model['email']?></p>
                <p>推荐人：<?=!empty($up_user_info)?$up_user_info['username']:''?></p>
            </div>
        </div>
        <div class="level">
            <ul>
                <li><a href="<?=\yii\helpers\Url::to(['dis-list'])?>"><i class="icon iconfont icon-quanbu"></i>全部分销<span><?=$total_count?>人</span></a></li>
                <li><a href="<?=\yii\helpers\Url::to(['dis-list','state'=>1])?>"><i class="icon iconfont icon-yijituijian"></i>一级分销<span><?=$one_count?>人</span></a></li>
                <li><a href="<?=\yii\helpers\Url::to(['dis-list','state'=>2])?>"><i class="icon iconfont icon-erji"></i>二级分销<span><?=$two_count?>人</span></a></li>
                <li><a href="<?=\yii\helpers\Url::to(['dis-list','state'=>3])?>"><i class="icon iconfont icon-sanji"></i>三级分销<span><?=$three_count?>人</span></a></li>
            </ul>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="income">
        <p><i class="icon iconfont icon-wodeyuanbao"></i><strong>我的分销总收益：<?=$user_model['com_money']?></strong></p>
    </div>
</footer>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>

<?php $this->endBlock()?>
