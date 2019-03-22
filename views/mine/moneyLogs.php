<?php
$this->title = '交易记录';
$this->params = [
    'body_style' => 'style="background: #fff;"',
];
?>

<?php $this->beginBlock('content')?>
<header class="header red">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title">交易记录</div>
</header>

<main class="main mgtop">
    <div class="recording" id="demo">

    </div>
</main>

<footer class="footer">
    <div class="income">
        <p><i class="icon iconfont icon-wodeyuanbao"></i><span>总支出：<?=$out_total?></span><span>收入：<?=$in_total?></span></p>
    </div>
</footer>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script>
    var url = '<?=\yii\helpers\Url::to(['mine/money-logs'])?>';
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
                        if(item[0]===0){
                            lis.push(getHtmlUp(item))
                        }else{
                            lis.push(' <li>\n' +
                                '<div class="left fl">\n' +
                                '<h3>'+item[3]+'</h3>\n' +
                                '<p>'+item[ 2]+'</p>\n' +
                                '</div>\n' +
                                '<div class="right fr">\n' +
                                '<h2>'+item[1]+'</h2>\n' +
                                '<p>交易成功</p>\n' +
                                '</div>\n' +
                                '</li>');
                        }
                    });


                    //执行下一页渲染，第二参数为：满足“加载更多”的条件，即后面仍有分页
                    //pages为Ajax返回的总页数，只有当前页小于总页数的情况下，才会继续出现加载更多
                    next(lis.join(''), page < res.page);
                });
            }
        });
        var is_up=false;
        var current_month;
        //块顶部
        function getHtmlUp(data){
            if(current_month!=data[1]){
                is_up=true;
                current_month=data[1]
                return (is_up?getHtmldown(data):'')+'<div class="month">\n' +
                    '            <div class="total"><span>'+data[1]+'</span><span>支出：'+data[2]+'</span><span>收入：'+data[3]+'</span></div>\n' +
                    '            <ul>'
            }


        }
        //闭合块
        function getHtmldown(data){
            return '</ul>\n' +
                '        </div>'
        }
    });

</script>

<?php $this->endBlock()?>
