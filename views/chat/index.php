<?php
$this->title = '聊天大厅';
$this->params = [
        'body_style' => 'style="background: #fff;"',
        'current_active'   => 'chat/index',
];
?>

<?php $this->beginBlock('content')?>
<header class="header red">
    <div class="header_title">聊天大厅</div>
</header>

<main class="main mgtop">
    <div class="chatroom">
        <div class="chatroom_nav">
            <ul class="clearfix">
                <?php /*?>
                <li <?=$type?'':'class="cur"'?>><a href="<?=\yii\helpers\Url::to(['chat/index'])?>"><span>全部会员</span></a></li>
                <?php */?>
                <li <?=$type==1?'class="cur"':''?>><a href="<?=\yii\helpers\Url::to(['chat/index','type'=>1])?>"><span>我的好友</span></a></li>
                <li <?=$type==2?'class="cur"':''?>><a href="<?=\yii\helpers\Url::to(['chat/index','type'=>2])?>"><span>陌生人</span></a></li>
                <li <?=$type==3?'class="cur"':''?>><a href="<?=\yii\helpers\Url::to(['chat/index','type'=>3])?>"><span>黑名单</span></a></li>
            </ul>
        </div>
        <div class="withdraw">
            <ul id="demo">

            </ul>
        </div>
    </div>
</main>


<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script>
    var url = '<?=\yii\helpers\Url::to(['mine/show-list','type'=>$type])?>';
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
                        lis.push('<li>\n' +
                            '<a href="'+detail+(detail.indexOf('?')>-1?'&':'?')+'id='+item.id+'">\n' +
                            '<div class="avatar">' +
                            (item.chat_count>0?'<span class="red_dot">'+item.chat_count+'</span>':'')+
                            '<img src="'+item.face+'">'+(item.type?'<i class="icon iconfont icon-vip"></i>':'')+'</div>\n' +
                            '<div class="text">\n' +
                            '<h2>ID：'+item.username+'<span>'+(item.type_name)+'</span></h2>\n' +
                            '<div class="status"><span>（'+(item.online?'在线':'离线')+'）</span>充值元宝：'+item.money+'</div>\n' +
                            '</div>\n' +
                            '</a>\n' +
                            '</li>');
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
