<?php
    $this->title = '帮助中心';
    $this->params = [
            'current_active' => ['system','system/help-center'],
            'crumb'          => ['系统管理','帮助中心'],
    ];
?>
<?php $this->beginBlock('content')?>


    <div class="box">
        <div class="box-header with-border">
            <a href="<?=\yii\helpers\Url::to(['help-center-add'])?>" class="btn bg-olive margin">新增</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>标题</th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($list as $key=>$vo) {?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$vo['title']?></td>
                        <td><?=$vo->statusName?> </td>
                        <td><?=$vo['update_time']?date('Y-m-d H:i:s',$vo['update_time']):''?></td>
                        <td>
                            <a href="<?=\yii\helpers\Url::to(['help-center-add','id'=>$vo['id']])?>">编辑</a>
                            <a  href="javascript:;" onclick="$.common.del('<?= \yii\helpers\Url::to(['help-center-del','id'=>$vo['id']])?>','删除')" class="ml-5">  删除</a>
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