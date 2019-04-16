
var is_init_data = false;
var is_bind_user = false;
var websocket_is_close;
console.log('浏览器支持websocket');
// 打开一个 web socket
var ws = new WebSocket("ws://43.225.157.28:9502");

ws.onopen = function()
{
    // Web Socket 已连接上，使用 send() 方法发送数据
    console.log('open')
};

ws.onmessage = function (evt)
{
    var received_msg = evt.data;
    var msg_info = received_msg.split(',')
    var type = msg_info.hasOwnProperty(0)?msg_info.hasOwnProperty(0):0;

    if(received_msg[0]==='{' || received_msg[0]==='['){
        //说明是对象-强转对象
        received_msg = eval('(' + received_msg + ')');
    }

    if(type==1 && is_bind_user===false){
        //绑定用户消息
        ws.send('1,'+global_user_id+',');
        is_bind_user = true;
    }
    //处于玩法界面
    if(is_test===1){
        //初始化数据
        if(!is_init_data && init_type!=='' ){
            is_init_data= true;
            ws.send('3,'+init_type);
        }
        //绑定的数据++--初始化
        if(received_msg.hasOwnProperty('type') && received_msg.type=='init_data'){
            // console.log('--------------');
            handlePageData(received_msg.type,received_msg.data,received_msg)
        }

        //停盘
        if(received_msg.hasOwnProperty('type') && received_msg.type==='is_open_state' && received_msg.state){
            is_open=0
        }

        if(is_open){
            //绑定的数据
            if(received_msg.hasOwnProperty('type') ){
                // console.log('--------------');
                handlePageData(received_msg.type,received_msg.data,received_msg)
            }

            //倒计时功能
            if(received_msg.hasOwnProperty('djs') ){
                // console.log('$$$$$$$$$$')
                // console.log(received_msg.djs)
                vote_info(received_msg.djs)

            }
            //数据展示
            if(received_msg.type==='vote_data' && received_msg.payload.hasOwnProperty(0)){

                if( init_type == received_msg.payload[0]){
                    //押涨
                    received_msg.payload.hasOwnProperty(1) && $("#up-money").text(received_msg.payload[1])
                    //押跌
                    received_msg.payload.hasOwnProperty(3) && $("#down-money").text(received_msg.payload[3])

                    //涨百分比
                    received_msg.payload.hasOwnProperty(2) && $("#up_per").text(received_msg.payload[2])
                    //跌百分比
                    received_msg.payload.hasOwnProperty(4) && $("#down_per").text(received_msg.payload[4])
                    //先删除高度
                    $(".look .high").removeClass('high')
                    //判断幅度
                    if(received_msg.payload[1]!==received_msg.payload[3]){

                        parseFloat(received_msg.payload[1])>parseFloat(received_msg.payload[3]) ?$(".look .red").addClass('high'):$(".look .green").addClass('high')
                    }
                }


            }

        }else{
            $("#vote-second").text('已停盘')
        }

        //等待结果状态
        if(received_msg.hasOwnProperty('is_wait')){
            //等待状态
            $("#wait-name").text(received_msg.is_wait?'等待结果':'下单时间')
        }



    }


    // console.log(received_msg)
};

ws.onclose = function()
{
    //关闭心跳
    clearTimeout(websocket_is_close);
    // 关闭 websocket
    alert('网络异常，请点击右上角刷新')
    console.log('连接已关闭')
};
//每隔30秒发送一次心跳包
websocket_is_close = setInterval(function(){
    ws.send('2,0')
},30000)

//初始化数据
if(is_test===1){
    var type=init_type; //当前股票类型
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
                    htmlStr += '开盘价:'+value[1]+'<br>' ;
                    htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                    //圆点后面显示的文本
                    htmlStr += '收盘价:'+value[2]+'<br>' ;
                    // htmlStr += '<span style="margin-right:5px;display:inline-block;width:10px;height:10px;border-radius:5px;background-color:#fff;"></span>';
                    //圆点后面显示的文本
                    // htmlStr += '涨跌:'+(value[5]>0?(value[5]==1?'涨':(value[5]==2?'跌':'平')):'待开奖')+'<br>' ;

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
            right:60
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
                },
                markLine: {
                    symbol: ['none'],
                    data: [
                        {
                            name: 'Y 轴值为 100 的水平线',
                            yAxis:  0,
                            lineStyle:{
                                color: '#fff500'
                            }
                        },
                    ]
                }
            }

    };
// console.log(data)
// 使用刚指定的配置项和数据显示图表。
//     myChart.setOption(option);
    var is_temp_step  = 1;
    function handlePageData(type,data,obj) {
        // console.log(type)
        // console.log(data)
        var is_wait = obj.hasOwnProperty('is_wait')?obj.is_wait:false
        data=data?data:[]
        if(type==='init_data'){
            //初始化数据
            option.xAxis.data=data.map(showxAxisData);
            option.series.data=data.map(showData);

        }else if(type==='table'){
            if(!is_temp_step){
                //删除临时数据
                option.xAxis.data.pop();
                option.series.data.pop();
            }

            //调整数据
            var current_show_length = option.series.data.length;
            //更改开奖属性
            // console.log(option.series.data[(current_show_length-1)])
            // option.series.data[(current_show_length-1)][4] = up_data.hasOwnProperty(0)?up_data[0]:0
            // console.log(option.series.data[(current_show_length-1)])
            //x坐标
            if(current_show_length>=15) {
                option.xAxis.data.shift();
            }
            data.map(function(item){
                option.xAxis.data.push(showxAxisData(item));
            })

            //数据
            if(current_show_length>=15) {
                option.series.data.shift();
            }
            // console.log(option.series.data)
            data.map(function(item){
                option.series.data.push(showData(item));
            })

            //重写数据
            is_temp_step=1;

        }else if(type==='temp'){
            //调整数据
            if(is_temp_step){
                is_temp_step=0;

            }else{
                option.xAxis.data.pop();
                option.series.data.pop();
            }
            //
            data.map(function(item){
                var show_temp_data = showData(item);
                option.xAxis.data.push(showxAxisData(item));
                option.series.data.push(show_temp_data);
                option.series.markLine.data[0].yAxis=show_temp_data[1]

            })
        }

        //更新页面数据
        if(type==='init_data'||type==='table'){
            updateOtherInfo(is_wait)
        }


        // console.log(option.series.data);
        myChart.setOption(option);
    }
    //加载投票倒计时
    var second_int=null;
    function vote_info(second,is_close){
        second=second?second:3

        if(second_int) {clearInterval(second_int)}


        if(is_close){
            $("#vote-second").text('已停盘')
            return false;
        }

        second_int = setInterval(function(){
            second--
            $("#vote-second").text(second)
            //小于零重新请求数据
            if(second<=0){
                //清空倒计时
                clearInterval(second_int)

                //监听倒计时问题
                setTimeout(function(){
                    ws.send('4,0');
                },1000);

            }
        },1000)
    }
//渲染数据格式
    function showxAxisData(item){
        request_date= item[0].substr(0,5);//一直保存最新的请求时间
        return request_date;
    }
    function showData(item){
        return [+item[1], +item[2], +item[3], +item[4], +item[5]];
    }

    //是否第一次加载
    var is_init=1
    //押注数量
    function updateOtherInfo(is_wait) {
        $.get("/index/other-info",{type:type,is_init:is_init},function(result){
            var user_money=result.hasOwnProperty(0)?parseFloat(result[0]):0.00;
            var press_info=result.hasOwnProperty(1)?result[1]:[];
            var open_data=result.hasOwnProperty(2)?result[2]:[];
            var award_money=result.hasOwnProperty(3)?parseFloat(result[3][0]):0;
            console.log(result)
            // if(is_wait===1){
            //     //等待开奖
            //     $("#open-pan h2").text(0)
            //     $("#open-pan .time").text('--')
            //
            //     $("#close-pan h2").text(0)
            //     $("#close-pan .time").text('--')
            //
            // }else{
                //先删除高度
                $(".look .high").removeClass('high')

                //开盘金额
                if(open_data.hasOwnProperty(0)){
                    $("#open-pan h2").text(open_data[0][1])
                    $("#open-pan .time").text(open_data[0][0])
                }
                //收盘价
                if(open_data.hasOwnProperty(1)){
                    $("#close-pan h2").text(open_data[1][1])
                    $("#close-pan .time").text(open_data[1][0])
                }
                //下压金额
                if(press_info.hasOwnProperty(0) && press_info[0]>0){
                    press_info[0]===1 ? $("#press-up-money").text(press_info[1]):$("#press-down-money").text(press_info[1])
                }else{
                    //清空下注数量
                    $("#press-up-money").text(0)
                    $("#press-down-money").text(0)
                }
            //是否中奖
            if(award_money!=0){
                //中奖效果
                $(".income_pop #up_get_money").text((award_money>0?'+':'')+award_money);
                $(".income_pop").show();
            }

            // }
            //用户余额
            user_money>0 && $("#user-money").text(user_money);
        })

        is_init = 0;
    }


    $(function(){
        var is_up=1;
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
                    obj.type = type;
                    obj.is_up = is_up;
                    obj._csrf=_csrf;
                    $.post("/mine/vote",obj,function(result){
                        layer.msg(result.msg)
                        layer.close(index)
                        //获取下压数量
                        updateOtherInfo(false)

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
}
