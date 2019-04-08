<?php
$this->title = '分销中心';
$this->params = [
];
?>

<?php $this->beginBlock('content')?>
<header class="header">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">我的分销</div>
</header>

<main class="main mgtop">
    <div class="distribution">
        <div class="top">
            <div class="avatar"><img src="<?=$user_model['face']?>">
                <?php if($user_model['type']>0){?>
                    <i class="icon iconfont icon-vip"></i>
                <?php }?>
            </div>
            <div class="text">
                <h2>ID：<?=$user_model['username']?>
                    <span><?=$user_model->typeName?></span>
                </h2>
                <p><?=$user_model['email']?></p>
                <p>推荐人：<?=!empty($up_user_info)?$up_user_info['username']:''?></p>
            </div>
        </div>
        <div class="column wrap">
            <ul class="clearfix">
                <li <?= $state==1?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to(['','state'=>1])?>">一级分销</a></li>
                <li <?= $state==2?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to(['','state'=>2])?>">二级分销</a></li>
                <li <?= $state==3?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to(['','state'=>3])?>">三级分销</a></li>
                <li <?= empty($state)?'class="cur"':''?> ><a href="<?=\yii\helpers\Url::to([''])?>">全部分销</a></li>
            </ul>
        </div>
        <div class="list">
            <ul id="demo">

            </ul>
        </div>
    </div>
</main>

<footer class="footer">
    <div class="income">
        <p><i class="icon iconfont icon-wodeyuanbao"></i><strong>我的总收益：<?=$user_model['com_money']?></strong></p>
    </div>
</footer>
<?php $this->endBlock()?>

<?php $this->beginBlock('script')?>
    <script>
        var url = '<?=\yii\helpers\Url::to(['','state'=>$state])?>';
        var detail = '<?=\yii\helpers\Url::to(['chat/talk'])?>';
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
                            lis.push(' <li>\n' +
                                '                    <div class="avatar"><img src="'+item.face+'">'+(item.type?'<i class="icon iconfont icon-vip"></i>':'')+'</div>\n' +
                                '                    <div class="text">\n' +
                                '                        <h2>ID：'+item.username+'<span>'+(item.type_name)+'</span></h2>\n' +
                                '                        <p><?=\Yii::$app->params['money_name']?>：'+item.money+'</p>\n' +
                                '                    </div>\n' +
                                '                    <div class="income">+'+item.form_money+'</div>\n' +
                                '                </li>');
                        });

                        //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
                        //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                        next(lis.join(''), page < res.page);
                    });
                }
            });
        });

    </script>
<?php $this->endBlock()?>