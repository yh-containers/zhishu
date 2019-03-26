<?php
$this->title = '数据管理';
$this->params = [
    'current_active' => ['transaction','transaction/index'],
    'crumb'          => ['数据管理','数据列表'],
];
?>
<?php $this->beginBlock('content')?>


    <div class="box">
        <div class="box-header with-border">
            <div class="btn-group">
                <?php foreach (\app\models\Pan::get_type() as $key=>$vo) {?>
                    <a href="<?=\yii\helpers\Url::to(['','type'=>$key])?>" type="button" class="btn <?=$type==$key?'bg-olive':''?> btn-default"><?=$vo['name']?></a>
                <?php }?>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>成交时间</th>
                    <th>交易数量</th>
                    <th>金额（涨）</th>
                    <th>金额（跌）</th>
                    <th>状态</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key=>$vo) {?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$vo['date'].' '.$vo['time']?></td>
                        <td><?=$vo['y_count']?></td>
                        <td><?=$vo['up_money_total']?></td>
                        <td><?=$vo['down_money_total']?></td>
                        <td><?=\app\models\Pan::getCompareInfo($vo['compare'])?></td>
                        <td>

                        </td>
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