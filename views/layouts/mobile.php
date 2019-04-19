<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1" />
    <meta name="apple-mobile-web-app-capable" content="yes" />
    <meta name="apple-mobile-web-app-status-bar-style" content="black" />
    <meta name="format-detection" content="telephone = no" />
    <title><?=$this->title?></title>
    <meta name="keywords" content="" />
    <meta name="description" content="" />
    <link rel="stylesheet" href="/assets/layui-v2.4.5/css/layui.css">
    <link rel="stylesheet" type="text/css" href="/assets/css/style.css" />
    <link rel="stylesheet" type="text/css" href="/assets/font/iconfont.css">
    <script type="text/javascript" src="/assets/js/jquery-1.11.0.min.js"></script>
    <?php if(isset($this->blocks['style'])){?>
        <?=$this->blocks['style']?>
    <?php }?>
    <script>
        //当前登录者用户id
        var global_user_id = <?=\Yii::$app->controller->user_id?>;
        var init_type = "<?=isset($this->params['init_type'])?$this->params['init_type']:false?>";
        var is_test = <?=isset($this->params['is_test'])?$this->params['is_test']:0?>;
        var is_open = <?=isset($this->params['is_open'])?$this->params['is_open']:0?>;
        var _csrf = "<?= Yii::$app->request->csrfToken ?>";
    </script>
</head>
<body <?= isset($this->params['body_style'])?$this->params['body_style']:'' ?> oncontextmenu=self.event.returnValue=false>

<?php if(isset($this->blocks['content'])){?>
    <?=$this->blocks['content']?>
<?php }?>

<?= empty($this->params['current_active'])?'':\app\widgets\Footer::widget([
        'current_active'    =>  $this->params['current_active'],
        'hide_ajax_chat'    =>  isset($this->params['hide_ajax_chat'])?$this->params['hide_ajax_chat']:0,
])?>
</body>
</html>
<script src="/assets/js/handle.js"></script>

<script src="/assets/layui-v2.4.5/layui.js"></script>
<script>
    var layer;
    layui.use(['layer'],function(){
        layer = layui.layer;
    })
</script>
<?php if(isset($this->blocks['script'])){?>
    <?=$this->blocks['script']?>
<?php }?>

<?php if(\Yii::$app->controller->user_id){?>
    <!--链接websocket-->
    <script src="/assets/js/websocket.js"></script>
<?php }?>
