<?php
$this->title = '系统设置';
//用于显示左侧栏目选中状态
$this->params = [
    'current_active' => ['system','system/setting'],
    'crumb'          => ['系统设置','常规设置'],
];
?>
<?php $this->beginBlock('content'); ?>

<div class="row">
    <div class="col-sm-6">
        <div class="box box-info">
            <form class="form-horizontal" action="<?= \yii\helpers\Url::to(['setting-save'])?>">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <input name="type" type="hidden" value="normal">
                <div class="box-header with-border">
                    <h3 class="box-title">常规设置</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-info btn-sm bg-yellow save-btn"  onclick="$.common.formSubmit($(this).parents('form'),1)">保存</button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">压宝手续费</label>
                        <div class="col-sm-10">
                            <input type="number" class="form-control" name="content[per]" value="<?= isset($normal_content['per'])?$normal_content['per']:''?>" placeholder="压宝手续费">
                            <span class="help-block">手续费0-1之间的两位小数(0或空则不限制)</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">系统开关</label>
                        <div class="col-sm-10">
                            <input type="radio"  name="content[switch]" value="1" <?= isset($normal_content['switch'])?($normal_content['switch']==1?'checked':''):'checked'?>>开
                            <input type="radio"  name="content[switch]" value="2" <?= isset($normal_content['switch']) && $normal_content['switch']==2?'checked':''?>>关
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>
    <div class="col-sm-6" style="display: none;">
        <div class="box box-info">
            <form class="form-horizontal" action="<?= \yii\helpers\Url::to(['setting-save'])?>">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <input name="type" type="hidden" value="use">
                <div class="box-header with-border">
                    <h3 class="box-title">常用语设置</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-info btn-sm bg-yellow save-btn"  onclick="$.common.formSubmit($(this).parents('form'),1)">保存</button>
                    </div>
                </div>
                <div class="box-body">
                    <div class="form-group">
                        <label for="inputEmail3" class="col-sm-2 control-label">使用说明</label>
                        <div class="col-sm-10">
                            <textarea name="content[intro]" class="textarea layui-textarea"><?= isset($use_content['intro'])?$use_content['intro']:''?></textarea>
                        </div>
                    </div>
                </div>
            </form>
        </div>

    </div>


</div>

<?php $this->endBlock(); ?>
<?php $this->beginBlock('script'); ?>
<script>
    <!-- 实例化编辑器 -->
    layui.use(['upload'], function(){
        var upload = layui.upload;

        $.common.uploadFile(upload,'.upload')

    });
    $(function(){

    })
</script>
<?php $this->endBlock(); ?>

