<?php
    $this->title = '系统设置';
    $this->params = [
            'current_active' => ['system','backup/index'],
            'crumb'          => ['系统设置','数据库备份'],
    ];
?>
<?php $this->beginBlock('content')?>



    <div class="box">
        <div class="box-header with-border">
            <a href="<?=\yii\helpers\Url::to(['create'])?>" class="btn bg-olive margin">备份</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>文件名</th>
                    <th>日期</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($dataProvider as $key=>$vo) {?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$vo['name']?></td>
                        <td><?=$vo['create_time']?></td>
                        <td>
                            <a href="<?=\yii\helpers\Url::to(['down','file'=>$vo['name']])?>">下载</a>
                            <a  href="javascript:;" onclick="$.common.del('<?= \yii\helpers\Url::to(['del','file'=>$vo['name']])?>','删除')" class="ml-5">  删除</a>
                        </td>
                    </tr>
                <?php }?>
                </tbody>
            </table>
        </div>

    </div>


<?php $this->endBlock()?>