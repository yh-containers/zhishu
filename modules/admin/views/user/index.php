<?php
    $this->title = '用户管理';
    $this->params = [
            'current_active' => ['user','user/index'],
            'crumb'          => ['用户管理','用户列表'],
    ];
?>
<?php $this->beginBlock('content')?>


    <div class="box">
        <div class="box-header">
            <a href="<?=\yii\helpers\Url::to(['user-add'])?>" class="btn bg-olive margin">新增</a>
            <div class="box-tools margin">
                <form>
                <div class="input-group input-group-sm" style="width: 250px; margin-right: 50px">
                    <input type="text" name="keyword" value="<?=$keyword?>" class="form-control pull-right" placeholder="用户名/邮箱">

                    <div class="input-group-btn">
                        <button type="submit" class="btn btn-default"><i class="fa fa-search"></i></button>
                    </div>
                </div>
                </form>
            </div>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered">
                <thead>
                <tr>
                    <th>#</th>
                    <th>用户名（ID）</th>
                    <th><a href="<?=\yii\helpers\Url::to(['','o_type'=>$o_type=='desc'?'asc':'desc'])?>">用户类型</a></th>
                    <th>邮箱</th>
                    <th><a href="<?=\yii\helpers\Url::to(['','o_money'=>$o_money=='desc'?'asc':'desc'])?>"><?=\Yii::$app->params['money_name']?></a></th>
                    <th>状态</th>
                    <th><a href="<?=\yii\helpers\Url::to(['','o_update_time'=>$o_update_time=='desc'?'asc':'desc'])?>">更新时间</a></th>
                    <th>操作</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($list as $key=>$vo) {?>
                    <tr>
                        <td><?=$key+1?></td>
                        <td><?=$vo['username']?></td>
                        <td><?=$vo->typeName?></td>
                        <td><?=$vo['email']?> </td>
                        <td><?=$vo['money']?> </td>
                        <td><?=$vo->statusName?></td>
                        <td><?=$vo->updateTime?></td>
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