<?php
$this->title = '推荐码邀请';
$this->params = [
    'body_style' => 'style="background: #fff;"',
];
?>

<?php $this->beginBlock('content')?>

<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">推荐码邀请</div>
</header>

<main class="main mgtop">
    <div class="top_img"><img src="/assets/images/login.jpg"></div>
    <div class="recommend">
        <div class="top">你的邀请码</div>
        <div class="center">
            <h2><?=$user_model->getCode()?></h2>
            <p><!--https://www.xxxxxxxxx.cn--></p>
        </div>
        <div class="bottom"><a href="javascript:;">复制</a></div>
    </div>
</main>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>

<?php $this->endBlock()?>
