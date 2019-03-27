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
                <h2>ID：<?=$user_model['id']?>
                    <span><?=$user_model->typeName?></span>
                </h2>
                <p><?=$user_model['email']?></p>
                <p>推荐人：<?=$user_model['fuid1']?></p>
            </div>
        </div>
        <div class="column wrap">
            <ul class="clearfix">
                <li <?= $state==1?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to(['','state'=>1])?>">一级分销</a></li>
                <li <?= $state==2?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to(['','state'=>2])?>">二级分销</a></li>
                <li <?= $state==3?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to(['','state'=>3])?>">三级分销</a></li>
                <li <?= empty($state)?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to([''])?>">全部分销</a></li>
            </ul>
        </div>
        <div class="list">
            <ul>
                <li>
                    <div class="avatar"><img src="/assets/images/avatar.png"><i class="icon iconfont icon-vip"></i></div>
                    <div class="text">
                        <h2>ID：759134<span>青铜会员</span></h2>
                        <p>充值元宝：5000</p>
                    </div>
                    <div class="income">+2500</div>
                </li>
                <li>
                    <div class="avatar"><img src="/assets/images/avatar.png"><i class="icon iconfont icon-vip"></i></div>
                    <div class="text">
                        <h2>ID：759134<span>青铜会员</span></h2>
                        <p>充值元宝：5000</p>
                    </div>
                    <div class="income">+2500</div>
                </li>
                <li>
                    <div class="avatar"><img src="/assets/images/avatar.png"><i class="icon iconfont icon-vip"></i></div>
                    <div class="text">
                        <h2>ID：759134<span>青铜会员</span></h2>
                        <p>充值元宝：5000</p>
                    </div>
                    <div class="income">+2500</div>
                </li>
                <li>
                    <div class="avatar"><img src="/assets/images/avatar.png"><i class="icon iconfont icon-vip"></i></div>
                    <div class="text">
                        <h2>ID：759134<span>青铜会员</span></h2>
                        <p>充值元宝：5000</p>
                    </div>
                    <div class="income">+2500</div>
                </li>
                <li>
                    <div class="avatar"><img src="/assets/images/avatar.png"><i class="icon iconfont icon-vip"></i></div>
                    <div class="text">
                        <h2>ID：759134<span>青铜会员</span></h2>
                        <p>充值元宝：5000</p>
                    </div>
                    <div class="income">+2500</div>
                </li>
                <li>
                    <div class="avatar"><img src="/assets/images/avatar.png"><i class="icon iconfont icon-vip"></i></div>
                    <div class="text">
                        <h2>ID：759134<span>青铜会员</span></h2>
                        <p>充值元宝：5000</p>
                    </div>
                    <div class="income">+2500</div>
                </li>
            </ul>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="income">
        <p><i class="icon iconfont icon-wodeyuanbao"></i><strong>我的总收益：<?=$user_model['history_money']?></strong></p>
    </div>
</footer>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>

<?php $this->endBlock()?>
