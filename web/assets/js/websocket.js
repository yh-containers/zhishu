if ("WebSocket" in window)
{
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
        if(type==1){
            //绑定用户消息
            ws.send('1,'+global_user_id+',');
        }
        console.log(received_msg)
    };

    ws.onclose = function()
    {
        //关闭心跳
        clearTimeout(websocket_is_close);
        // 关闭 websocket
        console.log('连接已关闭')
    };
    //每隔30秒发送一次心跳包
    websocket_is_close = setInterval(function(){
        ws.send('2,0')
    },30000)

}

else
{
   console.log('浏览器不支持websocket');
}