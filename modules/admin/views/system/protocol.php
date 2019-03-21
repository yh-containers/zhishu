<?php
$this->title = '系统设置';
//用于显示左侧栏目选中状态
$this->params = [
    'current_active' => ['system','system/protocol'],
    'crumb'          => ['系统设置','用户协议'],
];
?>
<?php $this->beginBlock('content'); ?>

<div class="row">
    <div class="col-sm-12">
        <div class="box box-info">
            <form class="form-horizontal" action="<?= \yii\helpers\Url::to(['setting-save'])?>">
                <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
                <input name="type" type="hidden" value="protocol">
                <div class="box-header with-border">
                    <h3 class="box-title">用户协议</h3>
                    <div class="box-tools pull-right">
                        <button type="button" class="btn btn-info btn-sm bg-yellow save-btn"  onclick="$.common.formSubmit($(this).parents('form'),1)">保存</button>
                    </div>
                </div>
                <div class="box-body">
                    <!-- 加载编辑器的容器 -->
                    <script id="container" name="content" type="text/plain"><?=$content?></script>
                </div>
            </form>
        </div>
    </div>


</div>

<?php $this->endBlock(); ?>

<?php $this->beginBlock('script'); ?>
<!-- 配置文件 -->
<script type="text/javascript" src="/modules/admin/assets/ueditor1_4_3_3/ueditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="/modules/admin/assets/ueditor1_4_3_3/ueditor.all.js"></script>
<script>
    var ue = UE.getEditor('container',{
        toolbars: [
            ['fullscreen', 'source', 'undo', 'redo','inserttable', 'deletetable', 'insertparagraphbeforetable', 'insertrow', 'deleterow', 'insertcol', 'deletecol', 'mergecells', 'mergeright', 'mergedown', 'splittocells', 'splittorows', 'splittocols', 'charts','|','simpleupload'],
            ['lineheight','|','customstyle', 'paragraph', 'fontfamily', 'fontsize', '|','directionalityltr', 'directionalityrtl', 'indent', '|','justifyleft', 'justifycenter', 'justifyright', 'justifyjustify'],
            ['bold', 'italic', 'underline', 'fontborder', 'strikethrough', 'superscript', 'subscript', 'removeformat', 'formatmatch', 'autotypeset', 'blockquote', 'pasteplain', '|', 'forecolor', 'backcolor', 'insertorderedlist', 'insertunorderedlist', 'selectall', 'cleardoc']
        ]
    });

</script>
<?php $this->endBlock(); ?>

