<?php
$this->title = '首页';
$this->params = [
        'body_style' => 'style="background: #fff;"',
        'current_active'   => 'index/index',
];
?>

<?php $this->beginBlock('content')?>
<header class="header index_header">
    <div class="content">
        <div class="left"><i class="icon iconfont icon-yuanbao-copy"></i><span>元宝数：<?=$user_info['money']?></span></div>
        <div class="center"><a href="recharge.html"><i class="icon iconfont icon-chongzhi1"></i><span>充值</span></a></div>
        <div class="right"><a href="withdraw.html"><i class="icon iconfont icon-tixian1"></i><span>提现</span></a></div>
    </div>
</header>
<main class="main mgtop">
    <div class="compared">
        <div class="vs">
            <div class="plate left" id="open-pan">
                <div class="num">
                    <h2>00.00</h2>
                    <p>开盘价</p>
                </div>
                <div class="time">00:00</div>
            </div>
            <div class="center">VS</div>
            <div class="plate right" id="close-pan">
                <div class="num">
                    <h2>00.00</h2>
                    <p>收盘价</p>
                </div>
                <div class="time">00:00</div>
            </div>
        </div>
        <div class="zhishu">
            <div id="main" style="height:300px;"></div>
        </div>

        <div class="look">
            <div class="position">
                <div class="bullish">
                    <div class="num"><i class="icon iconfont icon-yuanbao-copy"></i><span>500000</span></div>
                    <div class="red cylinder high"><p></p></div>
                    <div class="name">看涨元宝</div>
                </div>
            </div>
            <div class="img"><img src="/assets/images/yuanbao.png"></div>
            <div class="position">
                <div class="bearish">
                    <div class="num"><i class="icon iconfont icon-yuanbao-copy"></i><span>400000</span></div>
                    <div class="green cylinder"><p></p></div>
                    <div class="name">看跌元宝</div>
                </div>
            </div>
        </div>
    </div>
    <div class="exponent">
        <div class="title"><i class="icon iconfont icon-xiadan"></i><p>下单<br />合约</p></div>
        <ul>
            <li class="cur">上证指数一分钟线</li>
            <li>德国指数一分钟线</li>
        </ul>
    </div>
    <div class="betting">
        <div class="rise">
            <div class="button" data-is_up="1">
                <h2>看涨</h2>
                <p>预收益率85.48%</p>
            </div>
            <div class="price">2000</div>
        </div>
        <div class="time">
            <p>下单时间</p>
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
                <p>预收益率94.75%</p>
            </div>
            <div class="price">0</div>
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
            <p>+1800</p>
            <a href="javascript:;" class="close">确认</a>
        </div>
    </div>
</div>

<?php \app\widgets\Protocol::widget()?>
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
    layui.use(['layer'],function(){
        var layer = layui.layer;

        // $("#main").css('width',$(".zhishu").width());//获取父容器的宽度具体数值直接赋值给图表以达到宽度100%的效果
        // 基于准备好的dom，初始化echarts实例
        var myChart = echarts.init(document.getElementById('main'));

        myChart.title = '上证指数';

        var rawData = [];
        // var rawData = [['2015/12/31','3570.47','3539.18','3538.35','3580.6'],['2015/12/30','3566.73','3572.88','3538.11','3573.68'],['2015/12/29','3528.4','3563.74','3515.52','3564.17']].reverse();

        var dates = rawData.map(showxAxisData);
        var tip_title = ['开盘价',''];
        var data = rawData.map(showData);
        var option = {
            backgroundColor: '#21202D',
            legend: {
                data: ['上证指数'],
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
                        htmlStr += '最高价:'+value[1]+'<br>' ;
                        htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                        //圆点后面显示的文本
                        htmlStr += '最低价:'+value[2]+'<br>' ;
                        htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                        //圆点后面显示的文本
                        htmlStr += '当前价:'+value[2]+'<br>' ;
                        htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                        //圆点后面显示的文本
                        htmlStr += '涨跌:'+(value[5]>0?'涨':'跌')+'<br>' ;

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
                splitLine:{lineStyle:{type:'dotted'}},
                axisLine: {show:false, lineStyle: { color: '#8392A5' } }
            },
            grid: {
                bottom: 80
            },

            animation: false,
            series:
                {
                    type: 'candlestick',
// 定义了每个维度的名称。这个名称会被显示到默认的 tooltip 中。
                name: '上证指数',
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
            var url = "<?=\yii\helpers\Url::to(['index/pan-data'])?>";
            url = request_date ==='' ? (url+'?date='+h_i) : (url+'?is_init=1&date='+request_date)
            $.get(url,function(result){
                //待开奖id
                wait_id = result.hasOwnProperty('id')?result.id:0;
                //是否已关闭
                var is_close = result.hasOwnProperty('is_close')?result.is_close:0;
                //以往开盘数据
                var req_data = result.hasOwnProperty('data')?result.data:[];
                //以往开盘数据
                var open_data = result.hasOwnProperty('open_data')?result.open_data:[];
                var close_data = result.hasOwnProperty('close_data')?result.close_data:[];
                //距离下次开奖剩余时间
                var ons = result.hasOwnProperty('ons')?result.ons:10;
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

                if(req_data.length>1){
                    var data_type = typeof req_data[0]
                    if( data_type !== 'string'){
                        option.xAxis.data=req_data.map(showxAxisData);
                        option.series.data=req_data.map(showData);
                    }else{
                        //x坐标
                        option.xAxis.data.shift();
                        option.xAxis.data.push(showxAxisData(req_data));
                        //数据
                        option.series.data.shift();
                        option.series.data.push(showData(req_data));

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
            second=second?second:60
            is_close=is_close?is_close:0
            if(!is_close){
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
                        obj.is_up = is_up;
                        obj._csrf="<?= Yii::$app->request->csrfToken ?>";
                        $.post("<?=\yii\helpers\Url::to(['mine/vote'])?>",obj,function(result){
                            layer.msg(result.msg)
                            layer.close(index)
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
