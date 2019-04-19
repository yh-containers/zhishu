<?php
    $this->title = '用户管理';
    $this->params = [
            'current_active' => ['user','user/index'],
            'crumb'          => ['用户管理','交易明细'],
    ];
?>
<?php $this->beginBlock('content')?>


    <div class="box">

        <div class="box-header with-border">

            <h3>账单明细</h3>
        </div>
        <div class="col-sm-12 btn-group" style="display: inline">
            <a href="<?=\yii\helpers\Url::to(['','id'=>$id])?>" type="button" class="btn <?=is_null($type)?'bg-olive':''?> btn-default">全部</a>
            <?php foreach (\app\models\UserMoneyLogs::getType() as $key=>$vo) {?>
                <a href="<?=\yii\helpers\Url::to(['','id'=>$id,'type'=>$vo['type']])?>" type="button" class="btn <?=($type==$key && !is_null($type))?'bg-olive':''?> btn-default"><?=$vo['name']?></a>
            <?php }?>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>#</th>
                        <th>账单类型</th>
                        <th>说明</th>
                        <th>变动额度</th>
                        <th>交易时间</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($list as $key=>$vo) {?>
                        <tr>
                            <td><?=$key+1?></td>
                            <td><?=isset($logs_type[$vo['type']])?$logs_type[$vo['type']]['name']:''?></td>
                            <td><?=$vo['intro']?></td>
                            <td><?=$vo['money']?></td>
                            <td><?=$vo['create_time']?></td>
                        </tr>
                    <?php }?>
                    </tbody>
                </table>
            </div>
            <!-- /.box-body -->
            <div class="box-footer clearfix">
                <?= \yii\widgets\LinkPager::widget(['pagination'=>$pagination])?>
            </div>

        <!-- /.box-body -->

    </div>


<?php $this->endBlock()?>