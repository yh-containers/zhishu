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
            <button id="user-del" class="btn  margin btn-danger">删除</button>
        </div>
        <!-- /.box-header -->
        <div class="box-body">
            <table class="table table-bordered" id="layer-photos-demo">

                <thead>
                <tr>
                    <th width="80"><input type="checkbox"  id="full-checkbox"/></th>
                    <th width="120">投诉者</th>
                    <th width="120">被投诉者</th>
                    <th>内容</th>
                    <th width="200">图片</th>
                    <th width="240">创建日期</th>
                    <th width="80">操作</th>
                </tr>
                </thead>
                <tbody>

                <?php foreach ($list as $key=>$vo) {?>
                    <tr>
                        <td><input type="checkbox" name="id[]" value="<?=$vo['id']?>"/></td>
                        <td><a href="<?=\yii\helpers\Url::to(['user/user-detail','id'=>$vo['uid']])?>"><?=$vo['linkUser']['username']?></a></td>
                        <td><a href="<?=\yii\helpers\Url::to(['user/user-detail','id'=>$vo['c_uid']])?>"><?=$vo['linkCoverUser']['username']?></a></td>
                        <td title="<?=$vo['content']?>"><?=mb_strlen($vo['content'],'utf-8')>30?mb_substr($vo['content'],0,30,'utf-8').'......':$vo['content']?> </td>
                        <td>
                        <?php
                            $img = $vo['img']?explode(',',$vo['img']):[];
                            foreach ($img as $st){
                        ?>
                            <img src="<?=$st?>" width="40px" height="40px"/>
                        <?php }?>
                        </td>
                        <td><?=$vo['createTime']?></td>
                        <td>
                            <a  href="javascript:;" onclick="$.common.del('<?= \yii\helpers\Url::to(['complaint-del','id'=>$vo['id']])?>','删除')" class="ml-5">  删除</a>
                            <a  href="javascript:;"  class="ml-5 look-content">查看</a>
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
<?php $this->beginBlock('script');?>
<script>
    var _csrf = '<?= Yii::$app->request->csrfToken ?>';

    $(function(){

    layui.use(['layer'],function(){
            var layer = layui.layer;
            layui.layer.photos({
                photos: '#layer-photos-demo'
                ,anim: 5 //0-6的选择，指定弹出图片动画类型，默认随机（请注意，3.0之前的版本用shift参数）
            });

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
                    layer.msg('请选择要删除的数据');
                    return false;
                }
                layer.confirm('是否删除选中的数据',function(){
                    $.post('<?= \yii\helpers\Url::to(['complaint-del'])?>',{id:ids,_csrf:_csrf},function(result){
                        layer.msg(result.msg)
                        if(result.code===1){
                            setTimeout(function(){location.reload()},1000)
                        }
                    })
                })

            })

            $(".look-content").click(function(){
                layer.open({
                    type:0,
                    title:'投诉内容',
                    content:$(this).parents('tr').find('td:eq(3)').attr('title')
                })
            })

        })
    })


</script>
<?php $this->endBlock();?>
