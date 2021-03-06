<?php
$this->title = '首页';
$this->params = [
        'body_style' => 'style="background: #fff;"',
        'current_active'   => 'index/index',
//        'init_type'     => isset($type) ? $type : 0,
];
?>
<?php $this->beginBlock('style')?>
<style>
    .layui-layer .layui-layer-content{text-align: center;}
    .layui-layer .layui-layer-btn .layui-layer-btn0{float: right;background: #c50000;color: #fff;border: none;margin: 10px;}
    .layui-layer .layui-layer-btn .layui-layer-btn1{float: left;background: #00c500;color: #fff;border: none;margin: 10px;}
</style>
<?php $this->endBlock()?>
<?php $this->beginBlock('content')?>
<header class="header index_header">
    <div class="content">
        <div class="left"><i class="icon iconfont icon-yuanbao-copy"></i><span id="user-money"><?=$user_info['money']?></span></div>
        <div class="right">
            <a href="<?=\yii\helpers\Url::to(['mine/money-logs'])?>"><i class="icon iconfont icon-jiaoyijilu-"></i><span>账单</span></a>
            <a href="<?=\yii\helpers\Url::to(['mine/recharge'])?>"><i class="icon iconfont icon-chongzhi1"></i><span>充值</span></a>
            <a href="<?=\yii\helpers\Url::to(['mine/withdraw'])?>"><i class="icon iconfont icon-tixian1"></i><span>提现</span></a>
        </div>
    </div>
</header>
<main class="main">
    <div class="compared">
        <div class="vs">
            <div class="plate left" id="open-pan">
                <div class="num">
                    <h2>00.00</h2>
                    <p>收盘价</p>
                </div>
                <div class="time">10:02</div>
            </div>
            <div class="center"><h2>VS</h2><p><?=$type?'德国指数一分钟线':'上证指数一分钟线'?><!--<span id="then-open-time">10：58</p>--></div>
            <div class="plate right" id="close-pan">
                <div class="num">
                    <h2>00.00</h2>
                    <p>收盘价</p>
                </div>
                <div class="time">10:04</div>
            </div>
        </div>
        <div class="zhishu">
            <div id="main" style="height:calc(100vh - 446px);width:100%;"></div>
        </div>

        <div class="look">
            <div class="position">
                <div class="bullish">
                    <div class="num"><i class="icon iconfont icon-yuanbao-copy"></i><span id="up-money">500000</span></div>
                    <div class="red cylinder"><p></p></div>
                    <div class="name">看涨元宝</div>
                </div>
            </div>
            <div class="img"><img src="/assets/images/yuanbao.png"></div>
            <div class="position">
                <div class="bearish">
                    <div class="num"><i class="icon iconfont icon-yuanbao-copy"></i><span id="down-money">400000</span></div>
                    <div class="green cylinder"><p></p></div>
                    <div class="name">看跌元宝</div>
                </div>
            </div>
        </div>
    </div>
    <div class="exponent">
        <div class="title"><i class="icon iconfont icon-xiadan"></i><p>下单<br />合约</p></div>
        <ul>
            <li <?=$type?'':'class="cur"'?> onclick="window.location.href='<?=\yii\helpers\Url::to(['','is_lock'=>1])?>'">上证指数一分钟线</li>
            <li <?=$type==1?'class="cur"':''?> onclick="window.location.href='<?=\yii\helpers\Url::to(['','is_lock'=>1,'type'=>1])?>'">德国指数一分钟线</li>
        </ul>
    </div>
    <div class="betting">
        <div class="rise">
            <div class="button" data-is_up="1">
                <h2>看涨</h2>
                <p>预收益率<span id="up_per">85.48</span>%</p>
            </div>
            <div class="price" id="press-up-money">0</div>
        </div>
        <div class="time">
            <p id="wait-name">下单时间</p>
            <table cellpadding="0" cellspacing="0" class="data-table">
                <tr>
                    <td class="colspan-a">
                        <div class="data-show-box" id="dateShow">
                            <span class="date-s-span s" id="vote-second">00</span>
                        </div>
                    </td>
                </tr>
            </table>
            <p>倒计时</p>
        </div>
        <div class="fall">
            <div class="button" data-is_up="2">
                <h2>看跌</h2>
                <p>预收益率<span id="down_per">85.48</span>%</p>
            </div>
            <div class="price"  id="press-down-money">0</div>
        </div>
    </div>
</main>



<div class="bet_pop">
    <div class="bet_list">
        <ul class="clearfix">
            <li data-money="100"><i class="icon iconfont icon-yuanbao-copy"></i>100</li>
            <li data-money="300"><i class="icon iconfont icon-yuanbao-copy"></i>300</li>
            <li data-money="600"><i class="icon iconfont icon-yuanbao-copy"></i>600</li>
            <li data-money="1000"><i class="icon iconfont icon-yuanbao-copy"></i>1000</li>
            <li data-money="2000"><i class="icon iconfont icon-yuanbao-copy"></i>2000</li>
            <li data-money="3000"><i class="icon iconfont icon-yuanbao-copy"></i>3000</li>
            <li data-money="4000"><i class="icon iconfont icon-yuanbao-copy"></i>4000</li>
            <li data-money="5000"><i class="icon iconfont icon-yuanbao-copy"></i>5000</li>
            <li data-money="6000"><i class="icon iconfont icon-yuanbao-copy"></i>6000</li>
        </ul>
        <div class="queding"><a href="javascript:;">确定</a></div>
    </div>
</div>

<div class="income_pop">
    <div class="my_income">
        <div class="title">我的收益</div>
        <div class="content">
            <i><img src="/assets/images/ingots.png"></i>
            <p id="up_get_money">+1800</p>
            <a href="javascript:;" class="close">确认</a>
        </div>
    </div>
</div>

<?= \app\widgets\Protocol::widget(['is_show'=>$is_show_protocol])?>
<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script type="text/javascript" src="/assets/js/leftTime.min.js"></script>
<script type="text/javascript" src="/assets/js/echarts.min.js"></script>
<script type="text/javascript">

    //当前请求时间
    var request_date='';
    //待开奖id
    var wait_id=0;
    var is_up=0;
    var is_close=0;
    var type=<?=$type?>; //当前股票类型
    layui.use(['layer'],function(){
        var layer = layui.layer;

        // $("#main").css('width',$(".zhishu").width());//获取父容器的宽度具体数值直接赋值给图表以达到宽度100%的效果
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));

        var legend_title = type>0?'德国指数':'上证指数';
        var rawData = [];
        // var rawData = [['2015/12/31','3570.47','3539.18','3538.35','3580.6'],['2015/12/30','3566.73','3572.88','3538.11','3573.68'],['2015/12/29','3528.4','3563.74','3515.52','3564.17']].reverse();

        var dates = rawData.map(showxAxisData);
        var tip_title = ['开盘价',''];
        var data = rawData.map(showData);
        var option = {
            backgroundColor: '#21202D',
            legend: {
                data: [legend_title],
                inactiveColor: '#777',
                textStyle: {
                    color: '#fff'
                }
            },
            tooltip: {
                trigger: 'axis',
                axisPointer: {
                    animation: false,
                    type: 'cross',
                    lineStyle: {
                        color: '#376df4',
                        width: 2,
                        opacity: 1
                    }
                },
                formatter: function (params, ticket, callback) {
                    var htmlStr = '';
                    for(var i=0;i<params.length;i++){
                        var param = params[i];
                        var xName = param.name;//x轴的名称
                        var value = param.value
                        if(i===0){
                            htmlStr += xName + '<br/>';//x轴的名称
                        }
                        htmlStr +='<div>';
                        //为了保证和原来的效果一样，这里自己实现了一个点的效果
                        htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                        //圆点后面显示的文本
                        htmlStr += '最高价:'+value[3]+'<br>' ;
                        htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                        //圆点后面显示的文本
                        htmlStr += '最低价:'+value[4]+'<br>' ;
                        htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                        //圆点后面显示的文本
                        htmlStr += '当前价:'+value[1]+'<br>' ;
                        htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                        //圆点后面显示的文本
                        htmlStr += '涨跌:'+(value[5]>0?(value[5]==1?'涨':(value[5]==2?'跌':'平')):'待开奖')+'<br>' ;

                        htmlStr += '</div>';
                    }
                    return htmlStr;
                }

            },
            xAxis: {
                type: 'category',
                data: dates,
                axisLine: { lineStyle: { color: '#8392A5' } }
            },
            yAxis: {
                scale: true,
                position: 'right',
                splitLine:{lineStyle:{color: '#474747'}},
                axisLine: {show:false, lineStyle: { color: '#8392A5' } }
            },
            grid: {
                bottom: 30,
                top:10,
                right:60,
            },

            animation: false,
            series:
                {
                type: 'candlestick',
                // 定义了每个维度的名称。这个名称会被显示到默认的 tooltip 中。
                //name: legend_title,
                data: data,
                itemStyle: {
                    normal: {
                        color: '#FD1050',
                        color0: '#0CF49B',
                        borderColor: '#FD1050',
                        borderColor0: '#0CF49B'
                    }
                }

            }

        };
        // console.log(data)
        // 使用刚指定的配置项和数据显示图表。
        // myChart.setOption(option);

        //获取数据
        ajaxShowData()//直接展示数据
        // setInterval(ajaxShowData,10000)
        //获取展示数据--重新渲染组件
        function ajaxShowData(){
            var date = new Date();
            var is_new_obj=true;
            var index = layer.load(0, {time: 3000});
            var h_i = date.getHours()+':'+date.getMinutes()+':'+date.getSeconds()
            var url = "<?=\yii\helpers\Url::to(['index/pan-data','type'=>$type])?>";
            url = request_date ==='' ? (url+'&date='+h_i) : (url+'&is_init=1'+'&id='+wait_id+'&date='+request_date)
            $.get(url,function(result){
                //待开奖id
                wait_id = result.hasOwnProperty('id')?result.id:0;
                //是否已关闭
                is_close = result.hasOwnProperty('is_close')?result.is_close:0;
                //以往开盘数据
                var req_data = result.hasOwnProperty('data')?result.data:[];
                //上一次开盘数据
                var up_data = result.hasOwnProperty('up_data')?result.up_data:[];
                //以往开盘数据
                var open_data = result.hasOwnProperty('open_data')?result.open_data:[];
                var close_data = result.hasOwnProperty('close_data')?result.close_data:[];
                var is_wait = result.hasOwnProperty('is_wait')?result.is_wait:0;
                //距离下次开奖剩余时间
                var ons = result.hasOwnProperty('ons')?result.ons:60;
                //其它数据
                var o_data = result.hasOwnProperty('o_data')?result.o_data:[]
                console.log(open_data.hasOwnProperty(0))
                if(open_data.hasOwnProperty(0)){
                    console.log(open_data[0])
                }
                //开盘金额
                open_data.hasOwnProperty(1) && $("#open-pan h2").text(open_data[1])
                //开盘时间
                open_data.hasOwnProperty(0) && $("#open-pan .time").text(open_data[0])
                //收盘金额
                close_data.hasOwnProperty(1) && $("#close-pan h2").text(close_data[1])
                //收盘时间
                close_data.hasOwnProperty(0) && $("#close-pan .time").text(close_data[0])
                //收盘时间
                // close_data.hasOwnProperty(0) && $("#then-open-time").text(close_data[0])
                //押涨
                o_data.hasOwnProperty(0) && $("#up-money").text(o_data[0])
                //押跌
                o_data.hasOwnProperty(1) && $("#down-money").text(o_data[1])
                //押涨-用户
                o_data.hasOwnProperty(2) && $("#press-up-money").text(o_data[2])
                //押跌-用户
                o_data.hasOwnProperty(3) && $("#press-down-money").text(o_data[3])
                //涨百分比
                o_data.hasOwnProperty(4) && $("#up_per").text(o_data[4])
                //跌百分比
                o_data.hasOwnProperty(5) && $("#down_per").text(o_data[5])
                //等待状态
                $("#wait-name").text(is_wait?'等待结果':'下单时间')
                //判断幅度
                if(o_data.hasOwnProperty(0) && o_data.hasOwnProperty(1)){
                    //先删除高度
                    $(".look .high").removeClass('high')
                    o_data[0]!==o_data[1]?(parseFloat(o_data[0])>parseFloat(o_data[1]) ?$(".look .red").addClass('high'):$(".look .green").addClass('high')):''
                }
                //是否中奖
                if(up_data.hasOwnProperty(1)){
                    if(parseFloat(up_data[1])!=0){
                        //中奖效果
                        $(".income_pop #up_get_money").text((parseFloat(up_data[1])>0?'+':'')+up_data[1]);
                        $(".income_pop").show();
                    }
                }

                //用户余额
                result.hasOwnProperty('user_money') && $("#user-money").text(result.user_money);
                if(req_data.length>1){
                    var data_type = typeof req_data[0]
                    // console.log(request_date)
                    if( !request_date){
                        option.xAxis.data=req_data.map(showxAxisData);
                        option.series.data=req_data.map(showData);
                    }else{
                        var current_show_length = option.series.data.length;
                        //更改开奖属性
                        // console.log(option.series.data[(current_show_length-1)])
                        // option.series.data[(current_show_length-1)][4] = up_data.hasOwnProperty(0)?up_data[0]:0
                        // console.log(option.series.data[(current_show_length-1)])
                        //x坐标
                        if(current_show_length>=15) {
                            option.xAxis.data.shift();
                            option.xAxis.data.pop();
                        }else{
                            option.xAxis.data.pop();
                        }
                        req_data.map(function(item){
                            option.xAxis.data.push(showxAxisData(item));
                        })

                        //数据
                        if(current_show_length>=15) {
                            option.series.data.shift();
                            option.series.data.pop();
                        }else{
                            option.series.data.pop();
                        }
                        // console.log(option.series.data)
                        req_data.map(function(item){
                            option.series.data.push(showData(item));
                        })
                        // console.log(option.series.data)
                        // current_show_length>=15 && option.series.data.shift();
                        // option.series.data.push(showData(req_data));
                    }
                    is_new_obj && myChart.setOption(option);

                }

                layer.close(index); //此时你只需要把获得的index，轻轻地赋予layer.close即可
                //开启倒计时功能
                vote_info(ons,is_close)
            })
        }

        //加载投票倒计时
        var vote_info = function(second,is_close){
            second=second?second:3
            if(is_close){
                $("#vote-second").text('已停盘')
                return false;
            }

            var second_int = setInterval(function(){
                second--
                $("#vote-second").text(second)
                //小于零重新请求数据
                if(second<=0){
                    //清空倒计时
                    clearInterval(second_int)
                    //加载数据
                    ajaxShowData()
                }
            },1000)
        }



        //渲染数据格式
        function showxAxisData(item){
            request_date= item[0];//一直保存最新的请求时间
            return request_date;
        }
        function showData(item){
            return [+item[1], +item[2], +item[3], +item[4], +item[5]];
        }

        //刷新页面数据
        var flush_page_data = setInterval(function(){
            if(is_close){
                clearInterval(flush_page_data)
            }
            getPressMoney()
        },3000)

        //押注数量
        function getPressMoney() {
             $.get("<?= \yii\helpers\Url::to(['press-money'])?>",{id:wait_id,type:type},function(result){
                 result.hasOwnProperty(0) && $("#press-up-money").text(result[0])
                 result.hasOwnProperty(1) && $("#press-down-money").text(result[1])
                 //押涨
                 result.hasOwnProperty(2) && $("#up-money").text(result[2])
                 //押跌
                 result.hasOwnProperty(3) && $("#down-money").text(result[3])
                 //用户余额
                 result.hasOwnProperty(4) && $("#user-money").text(result[4]);
                //涨百分比
                 result.hasOwnProperty(5) && $("#up_per").text(result[5])
                 //跌百分比
                 result.hasOwnProperty(6) && $("#down_per").text(result[6])

                 //判断幅度
                 if(result.hasOwnProperty(2) && result.hasOwnProperty(3)){
                     //先删除高度
                     $(".look .high").removeClass('high')

                     result[2]!==result[3]?(parseFloat(result[2])>parseFloat(result[3]) ?$(".look .red").addClass('high'):$(".look .green").addClass('high')):''
                 }
             })
        }

        $(function(){
            /*点击投注弹窗出现*/
            $(".betting .button").click(function(){
                is_up = $(this).data('is_up')
                $(".bet_pop").show();
            });

            /*提交投注并关闭弹窗*/
            $(".bet_pop .queding").click(function(){
                var money = $(this).prev().find('.cur').data('money')
                if(money>0){
                    layer.confirm('是否确定下注',function(){
                        var index = layer.load()
                        var obj = {};
                        obj.money=money;
                        obj.id = wait_id;
                        obj.type = type;
                        obj.is_up = is_up;
                        obj._csrf="<?= Yii::$app->request->csrfToken ?>";
                        $.post("<?=\yii\helpers\Url::to(['mine/vote'])?>",obj,function(result){
                            layer.msg(result.msg)
                            layer.close(index)
                            //获取下压数量
                            getPressMoney()

                        })


                    })
                }


                $(".bet_pop").hide();
            });
            /*当前投注数量*/
            $(".bet_pop ul li").click(function(){
                $(this).toggleClass("cur").siblings().removeClass("cur");
            });
            /*提交投注并关闭弹窗*/
            $(".income_pop .close").click(function(){
                $(".income_pop").hide();
            });

        });
    })


</script>
<?php $this->endBlock()?>
