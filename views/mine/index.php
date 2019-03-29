<?php
$this->title = '个人中心';
$this->params = [
        'body_style' => 'style="background: #fff;"',
        'current_active'   => 'mine/index',
];
?>

<?php $this->beginBlock('content')?>
<main class="main">
    <div class="member">
        <div class="top wrap">
            <div class="drop_out"><a href="<?=\yii\helpers\Url::to(['index/logout'])?>">退出登录</a></div>
            <div class="avatar"><img id="preview" src="<?=$user_model['face']?>"/>
                <?php if($user_model['type']>0){?>
                    <i class="icon iconfont icon-vip"></i>
                <?php }?>
                <input id="file" type="file" name="file" accept=""/>
            </div>
            <div class="text">
                <h2>ID：<?=$user_model['id']?>
                    <span><?=$user_model->typeName?></span>
                </h2>
                <p><?=$user_model['email']?></p>
                <p>推荐人：<?=$user_model['fuid1']?></p>
            </div>
            <div class="balance">
                <div class="left">
                    <i><img src="/assets/images/icon02.png"></i>
                    <p>余额：<span><?=$user_model['money']?></span></p>
                </div>
                <div class="right">
                    <a href="<?=\yii\helpers\Url::to(['mine/recharge'])?>" class="red"><i class="icon iconfont icon-chongzhi"></i>充值</a>
                    <a href="<?=\yii\helpers\Url::to(['mine/withdraw'])?>" class="green"><i class="icon iconfont icon-tixian"></i>提现</a>
                </div>
            </div>
        </div>
        <ul>
            <li><a href="<?=\yii\helpers\Url::to(['distribution'])?>"><i class="icon iconfont icon-wodefenxiao"></i><span>我的分销</span></a></li>
            <li><a href="<?=\yii\helpers\Url::to(['req-code'])?>"><i class="icon iconfont icon-xingxing"></i><span>推荐码邀请</span></a></li>
            <li><a href="<?=\yii\helpers\Url::to(['rest-mail'])?>"><i class="icon iconfont icon-youxiang"></i><span>重置邮箱</span></a></li>
            <li><a href="<?=\yii\helpers\Url::to(['rest-pwd'])?>"><i class="icon iconfont icon-mima"></i><span>修改登录密码</span></a></li>
            <li><a href="<?=\yii\helpers\Url::to(['rest-pay-pwd'])?>"><i class="icon iconfont icon-pay-key"></i><span>设置支付密码</span></a></li>
        </ul>
    </div>
</main>

<div id="clipArea">
    <div class="clipwrap">
        <button id="clipBtn">完成</button>
        <button id="clipClose">取消</button>
    </div>
</div>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<!-- 头像裁剪 -->
<script src="/assets/js/hammer.min.js"></script>
<script src="/assets/js/lrz.all.bundle.js"></script>
<script src="/assets/js/iscroll-zoom-min.js"></script>
<script src="/assets/js/PhotoClip.js"></script>
<script>
    var _csrf="<?=\Yii::$app->request->csrfToken?>"
    document.addEventListener('touchmove', function (e) {
        e.preventDefault();
    }, false);
    var clipArea = new PhotoClip("#clipArea", {
        size: [280, 280],
        outputSize: [640, 640],
        file: "#file",
        view: "#view",
        ok: "#clipBtn",
        loadComplete: function () {
            $("#clipArea").css("display", "block");
        },
        done: function (dataURL,other) {
            $("#preview").attr("src", dataURL);
            $(".image").val(dataURL);
            $("#clipArea").css("display", "none");
            $.post("<?=\yii\helpers\Url::to(['mine/mod-info'])?>",{face:dataURL,_csrf:_csrf},function(result){
                console.log(result.msg)
            })
        }
    });
    $("#clipClose").click(function () {
        $("#clipArea").css("display", "none");
    })
    $("#changeImage").click(function () {
        $("#file").click();
    });
</script>
<?php $this->endBlock()?>
