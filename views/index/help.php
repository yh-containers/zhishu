<?php
$this->title = '帮助中心';
$this->params = [
    'body_style' => 'style="background: #fff;"',
    'current_active'   => 'index/help',
];
?>

<?php $this->beginBlock('content')?>
<header class="header red">
    <div class="header_title">帮助中心</div>
</header>

<main class="main mgtop">
    <div class="help">
        <ul>
            <?php foreach($list as $vo){?>
                <li><a href="<?=\yii\helpers\Url::to(['help-detail','id'=>$vo['id']])?>"><?=$vo['title']?></a></li>
            <?php }?>
        </ul>
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
