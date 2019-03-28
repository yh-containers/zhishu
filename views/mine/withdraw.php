<?php
$this->title = '提现';
$this->params = [
    'body_style' => 'style="background: #fff;"',
];
?>

<?php $this->beginBlock('content')?>

<header class="header red">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">提现</div>
    <div class="header_right"><a href="<?=\yii\helpers\Url::to(['withdraw-up'])?>" class="shangjia">上架</a></div>
</header>

<main class="main mgtop">
    <div class="withdraw">
        <ul id="demo">

        </ul>
    </div>
</main>

<div class="obtained_pop">
    <div class="btn">
        <a href="javascript:;" class="obtained" data-id="0">下架</a>
        <a href="javascript:;" class="close">返回</a>
    </div>
</div>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script>
    var _csrf= "<?=\Yii::$app->request->csrfToken?>";
    var url = '<?=\yii\helpers\Url::to(['mine/withdraw-list','uid'=>$user_model->id])?>';
    layui.use('flow', function(){
        var $ = layui.jquery; //不用额外加载jQuery，flow模块本身是有依赖jQuery的，直接用即可。
        var flow = layui.flow;
        flow.load({
            elem: '#demo' //指定列表容器
            ,done: function(page, next){ //到达临界点（默认滚动触发），触发下一页
                var lis = [];
                //以jQuery的Ajax请求为例，请求下一页数据（注意：page是从2开始返回）
                $.get(url+(url.indexOf('?')>-1?'&':'?')+'page='+page, function(res){
                    var data = res.hasOwnProperty('data')?res.data:[];

                    //假设你的列表返回在data集合中
                    layui.each(data, function(index, item){

                        lis.push('<li data-id="'+item.id+'">\n' +
                            '<div class="avatar"><img src="'+item['face']+'">'+(item.type?'<i class="icon iconfont icon-vip"></i>':'')+'</div>\n' +
                            '<div class="text">\n' +
                            '<h2>ID：'+item.uid+'<span>'+(item.type_name)+'</span></h2>\n' +
                            '<div class="info">\n' +
                            '<div class="num"><p>出售<?=\Yii::$app->params["money_name"]?>数量</p><span>'+item.money+'个</span></div>\n' +
                            '<div><p>单价</p><span>'+item.price+'元</span></div>\n' +
                            '<div><p>总价</p><span>'+item.price*item.money+'元</span></div>\n' +
                            '<div><p>收款方式</p><span><img src="/assets/images/pay0'+item.label+'.png"></span></div>\n' +
                            '</div>\n' +
                            '</div>\n' +
                            '</li>');

                    });


                    //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
                    //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                    next(lis.join(''), page < res.page);
                });
            }
        });

    });
    var choose_obj ;
    $(".withdraw ").on('click','ul li',function(){
        choose_obj = $(this)
        $(".obtained_pop").show();
        $(".obtained_pop .obtained").data('id',$(this).data('id'))
    });
    $(".obtained_pop .close").click(function(){
        $(this).parents(".obtained_pop").hide();
    });
    $(".obtained_pop .obtained").click(function(){
        var id= $(this).data('id')
        var $this = $(this)
        $.post("<?=\yii\helpers\Url::to(['withdraw-del'])?>",{id:id,_csrf:_csrf},function(result){
            layer.msg(result.msg)
            choose_obj.remove()
        }).then(function(){
            $this.parents(".obtained_pop").hide();
        })
    });
</script>


<?php $this->endBlock()?>
