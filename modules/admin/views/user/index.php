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
            <button id="user-del" class="btn  margin btn-danger">删除</button>
            <a href="<?=\yii\helpers\Url::to(['user-export'])?>" class="btn  margin  btn-warning">导出excel</a>
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
                    <th><input type="checkbox"  id="full-checkbox"/></th>
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
                        <td><input type="checkbox" name="id[]" value="<?=$vo['id']?>"/></td>
                        <td><?=$vo['username']?></td>
                        <td><?=$vo->typeName?></td>
                        <td><?=$vo['email']?> </td>
                        <td><?=$vo['money']?> </td>
                        <td><?=$vo->statusName?></td>
                        <td><?=$vo->updateTime?></td>
                        <td>
                            <a href="<?=\yii\helpers\Url::to(['user-add','id'=>$vo['id']])?>">编辑</a>
                            <a href="<?=\yii\helpers\Url::to(['user-detail','id'=>$vo['id']])?>">查看</a>
                            <a href="<?=\yii\helpers\Url::to(['user-charge','id'=>$vo['id']])?>">账单明细</a>
                            <a  href="javascript:;" onclick="$.common.del('<?= \yii\helpers\Url::to(['user-del','id'=>$vo['id']])?>','删除')" class="ml-5">  删除</a>
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

<?php $this->beginBlock('script')?>
<script>
    var _csrf = '<?= Yii::$app->request->csrfToken ?>';
    $(function(){
        $("#full-checkbox").change(function(){
            var bool = $(this).prop('checked')
            $("table tbody input[type='checkbox']").attr('checked',bool)
        })
        $("#user-del").click(function(){
            var ids = []
            $('.table tbody tr input:checked').each(function(){
                ids.push($(this).val())
            })
            if(ids.length===0){
                layer.msg('请选择要删除的会员');
                return false;
            }
            layer.confirm('是否删除选中的会员',function(){
                $.post('<?= \yii\helpers\Url::to(['user-del'])?>',{id:ids,_csrf:_csrf},function(result){
                    layer.msg(result.msg)
                    if(result.code===1){
                        setTimeout(function(){location.reload()},1000)
                    }
                })
            })

        })
    })
</script>

<?php $this->endBlock()?>