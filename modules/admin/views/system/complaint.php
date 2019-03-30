<?php
    $this->title = '投诉';
    $this->params = [
            'current_active' => ['system','system/complaint'],
            'crumb'          => ['系统管理','投诉'],
    ];
?>
<?php $this->beginBlock('content')?>


    <div class="box">
        <div class="box-header with-border">
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered" id="layer-photos-demo">
                <thead>
                <tr>
                    <th>#</th>
                    <th>投诉者</th>
                    <th>被投诉者</th>
                    <th>内容</th>
                    <th>图片</th>
                    <th>创建日期</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($list as $key=>$vo) {?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><a href="<?=\yii\helpers\Url::to(['user/user-detail','id'=>$vo['uid']])?>"><?=$vo['linkUser']['username']?></a></td>
                        <td><a href="<?=\yii\helpers\Url::to(['user/user-detail','id'=>$vo['c_uid']])?>"><?=$vo['linkCoverUser']['username']?></a></td>
                        <td><?=$vo['content']?> </td>
                        <td>
                        <?php
                            $img = $vo['img']?explode(',',$vo['img']):[];
                            foreach ($img as $st){
                        ?>
                            <img src="<?=$st?>" width="40px" height="40px"/>
                        <?php }?>
                        </td>
                        <td><?=$vo['createTime']?></td>

                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <?= \yii\widgets\LinkPager::widget(['pagination'=>$pagination])?>
        </div>

    </div>


<?php $this->endBlock()?>
<?php $this->beginBlock('script');?>
<script>
    layui.use(['layer'],function(){
        layui.layer.photos({
            photos: '#layer-photos-demo'
            ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
        });
    })


</script>
<?php $this->endBlock();?>
