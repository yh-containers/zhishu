<?php
    $this->title = '用户管理';
    $this->params = [
            'current_active' => ['user','user/index'],
            'crumb'          => ['用户管理','用户列表'],
    ];
?>
<?php $this->beginBlock('content')?>


    <div class="box">
        <div class="box-header with-border">
            <a href="<?=\yii\helpers\Url::to(['user-add'])?>" class="btn bg-olive margin">新增</a>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>用户名</th>
                    <th>用户类型</th>
                    <th>邮箱</th>
                    <th>邀请码</th>
                    <th><?=\Yii::$app->params['money_name']?></th>
                    <th>状态</th>
                    <th>更新时间</th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key=>$vo) {?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$vo['username']?></td>
                        <td><?=$vo->typeName.($vo['level']?'('.$vo->levelName.')':'')?></td>
                        <td><?=$vo['email']?> </td>
                        <td><?=$vo->getCode()?> </td>
                        <td><?=$vo['money']?> </td>
                        <td><?=$vo->statusName?></td>
                        <td><?=$vo->createTime?></td>
                        <td>
                            <a href="<?=\yii\helpers\Url::to(['user-add','id'=>$vo['id']])?>">编辑</a>
                            <a href="<?=\yii\helpers\Url::to(['user-detail','id'=>$vo['id']])?>">查看</a>
                           <!-- <a  href="javascript:;" onclick="$.common.del('<?/*= \yii\helpers\Url::to(['user-del','id'=>$vo['id']])*/?>','删除')" class="ml-5">  删除</a>-->
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