<footer class="footer">
    <div class="footer_nav clearfix">
        <a href="<?=\yii\helpers\Url::to(['index/index'])?>" <?=$current_active=='index/index'?'class="cur"':''?> ><i class="icon iconfont icon-jiaoyi"></i><span>交易</span></a>
        <a href="<?=\yii\helpers\Url::to(['index/help'])?>"  <?=$current_active=='index/help'?'class="cur"':''?> ><i class="icon iconfont icon-bangzhu"></i><span>帮助</span></a>
        <a href="<?=\yii\helpers\Url::to(['chat/index'])?>"  <?=$current_active=='chat/index'?'class="cur"':''?> ><i class="icon iconfont icon-xiaoxi"></i><span>聊天<span class="red_dot" style="<?= $chat_num?'':'display:none'?>" ><?=$chat_num?></span></span></a>
        <a href="<?=\yii\helpers\Url::to(['mine/index'])?>"  <?=$current_active=='mine/index'?'class="cur"':''?> ><i class="icon iconfont icon-zhanghu"></i><span>账户</span></a>
    </div>
</footer>
<?php if(empty($hide_ajax_chat)) {?>
<script>
var footer_msg_interval = setInterval(function(){
    $.get("<?= \yii\helpers\Url::to(['chat/nread'])?>",function(result){
        result.hasOwnProperty(0) && result[0]>0 && $(".footer .red_dot").text(result[0])
    })
},10000)
</script>
<?php }?>