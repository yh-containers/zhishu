<?php
$this->title = '投诉';
$this->params = [
    'body_style' => 'style="background: #fff;"',
];
?>

<?php $this->beginBlock('content')?>

<header class="header red">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">投诉</div>
</header>

<main class="main mgtop">
    <div class="complaint">
        <form  id="form">
            <input name="_csrf" type="hidden" id="_csrf" value="<?= Yii::$app->request->csrfToken ?>">
            <input name="c_uid" type="hidden" value="<?=$complaint_user_info['id']?>"/>
            <div class="top">
                <div class="avatar"><img src="<?=$complaint_user_info['face']?>"><?=$complaint_user_info['type']?'<i class="icon iconfont icon-vip"></i>':''?></div>
                <div class="text">
                    <p><?=!empty($charge_user_info)?$charge_user_info->getTypeName():''?></p>
                    <h3>ID：<?=$complaint_user_info['id']?></h3>
                </div>
            </div>
            <div class="textarea"><textarea placeholder="投诉原因：" name="content"></textarea></div>
            <div class="z_photo upimg-div clearfix">
                <section class="z_file fl">
                    <img src="/assets/images/file.png" class="add-img" id="test1">
                </section>
            </div>
            <div class="btn"><input type="button" name="" id="submit" value="提交"></div>
        </form>
    </div>
</main>



<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script>
    var _csrf='<?=\Yii::$app->request->csrfToken?>'

    var img_html = function(path){
        return '<section class="up-section fl">' +
            '<span class="up-span"></span>' +
            '<img class="close-upimg" src="/assets/images/a7.png">' +
            '<img class="up-img" src="'+path+'">' +
            '<input name="img[]" value="'+path+'" type="hidden">' +
            '</section>'
    }

    layui.use(['upload','layer'], function() {
        var upload = layui.upload;
        var layer = layui.layer;


        //执行实例
        var uploadInst = upload.render({
            elem: '#test1' //绑定元素
            ,url: '<?=\yii\helpers\Url::to(['upload/upload'])?>' //上传接口
            ,data:{_csrf:_csrf,type:'complaint'}
            ,acceptMime:'image/*'
            ,before: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                layer.load(); //上传loading
            }
            ,done: function(res){
                layer.closeAll('loading'); //关闭loading
                layer.msg(res.msg)
                //上传完毕回调
                if(res.code===1){
                    var html = img_html(res.path)

                    $(".z_photo").append(html)
                }
            }
            ,error: function(){
                layer.closeAll('loading'); //关闭loading
                //请求异常回调
                layer.msg('上传异常')
            }
        });

        $("#form").on('click','.close-upimg',function(){
            var $this = $(this)
            layer.confirm('是否删除该图片',function(){
                $this.parent().remove()
                layer.closeAll()
            })
        })
        $("#submit").click(function(){
            $.post($("#form").attr('action'),$("#form").serialize(),function(result){
                layer.msg(result.msg)

            })
        })
    })
</script>
<?php $this->endBlock()?>
