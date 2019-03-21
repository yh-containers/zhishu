<?php
$this->title = '忘记密码';
$this->params = [
    'body_style' => 'style="background: #fff;"',
    'current_active'   => 'index/help',
];
?>

<?php $this->beginBlock('content')?>
<header class="header red">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">详情</div>
</header>

<main class="main mgtop">
    <div class="help_det">
        <div class="title"><?=$model['title']?></div>
        <div class="content">
            <?=$model['content']?>
        </div>
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
