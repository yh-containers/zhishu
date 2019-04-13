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
        <div class="top">我的邀请码</div>
        <div class="center" id="text">
            <h2><?=$user_model['username']?></h2>
            <p><?= \Yii::$app->request->hostInfo?></p>
        </div>
        <div class="bottom" onclick="copyText()"><a href="javascript:;">复制</a></div>
    </div>
</main>
<textarea id="input" style="opacity: 0;"></textarea>
<script type="text/javascript">
    function copyText() {
      var text = document.getElementById("text").innerText;
      var input = document.getElementById("input");
      input.value = "<?= \yii\helpers\Url::to(['index/index', 'req_code' => $user_model['username']], true)?>"; // 修改文本框的内容
      input.select(); // 选中文本
      document.execCommand("copy"); // 执行浏览器复制命令
      alert("复制成功");
    }
</script>

<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>

<?php $this->endBlock()?>


