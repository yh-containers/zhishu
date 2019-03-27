<?php
$this->title = '聊天大厅';
$this->params = [
        'body_style' => 'style="background: #eee;"',
];
?>

<?php $this->beginBlock('style')?>
<style>
    #chat-box .message .info p img{display: inline-block}
</style>
<?php $this->endBlock()?>

<?php $this->beginBlock('content')?>
<header class="header red">
    <a href="javascript:window.history.back()" class="header_left"><i class="icon iconfont icon-jiantou"></i><span>返回</span></a>
    <div class="header_title"><p>ID<?=!empty($chart_obj_info)?$chart_obj_info['id']:''?><br /><span>（<?=!empty($chart_obj_info)?($chart_obj_info['online']?'在线':'离线'):'离线'?>）</span></p></div>
    <div class="header_right"><a href="<?=\yii\helpers\Url::to(['mine/complaint','uid'=>$f_uid])?>" class="shangjia">投诉</a></div>
</header>

<main class="main mgtop">
    <div class="chatbox" id="chat-box">

    </div>
</main>

<footer class="chat_footer">
    <div class="bottom">
        <div class="enter_box">
            <div id="div" class="box" contenteditable="true"></div>
            <input type="button" id="submit" name="" class="send" value="发送">
        </div>
        <ul class="clearfix">
            <li class="move">
                <a href="javascript:;"><i class="icon iconfont icon-add"></i></a>
                <dl>
                    <dd onclick="friend(1)">移至我的好友</dd>
                    <dd onclick="friend(2)">移至陌生人</dd>
                    <dd onclick="friend(3)">移至黑名单</dd>
                </dl>
            </li>
            <li>
                <a href="javascript:;"  id="test1"><i class="icon iconfont icon-font29"></i></a>
            </li>
            <li id="meditor" class="mcont"><a href="javascript:;" class="mbar"><i class="icon iconfont icon-weixiao"></i></a></li>
            <li><a href="<?=\yii\helpers\Url::to(['mine/money-logs'])?>"><i class="icon iconfont icon-jiaoyijilu-"></i></a></li>
            <li><a href="<?=\yii\helpers\Url::to(['mine/transfer','uid'=>$f_uid])?>"><i class="icon iconfont icon-huhuan"></i></a></li>
        </ul>
    </div>
</footer>

<div class="prompt_pop">
    <div class="content">
        <p>请先设置支付密码</p>
        <a href="javascript:;">确认</a>
    </div>
</div>

<?php $this->endBlock()?>


<?php $this->beginBlock('script')?>
<script type="text/javascript" src="/assets/js/expression.js"></script>
<script type="text/javascript">
    var _csrf='<?=\Yii::$app->request->csrfToken?>'

    //聊天用户信息
    var user_info = <?=json_encode($user_info?$user_info:[])?>
    //聊天对象
    var chat_obj_uid = "<?=$f_uid?>";
    var chat_obj_info = user_info.hasOwnProperty(chat_obj_uid)?user_info[chat_obj_uid]:{}
    //当前操作者用户
    var chat_uid = "<?=\Yii::$app->controller->user_id?>";
    var chat_info = user_info.hasOwnProperty(chat_uid)?user_info[chat_uid]:{}

    layui.use(['upload','layer'], function(){
        var upload = layui.upload;
        var layer = layui.layer
        var record_id;  //当前记录的最后一条id




        //执行实例
        var uploadInst = upload.render({
            elem: '#test1' //绑定元素
            ,url: '<?=\yii\helpers\Url::to(['chat/say'])?>' //上传接口
            ,data:{_csrf:_csrf,type:1,rec_uid:chat_obj_uid}
            ,acceptMime:'image/*'
            ,before: function(obj){ //obj参数包含的信息，跟 choose回调完全一致，可参见上文。
                layer.load(); //上传loading
            }
            ,done: function(res){
                layer.closeAll('loading'); //关闭loading
                //上传完毕回调
                loadData()
            }
            ,error: function(){
                layer.closeAll('loading'); //关闭loading
                //请求异常回调
            }
        });




        $("#submit").click(function(){
            var content = $(this).prev().html()
            //过滤标签
            content=filterHTMLTag(content)
            //发送消息
            sendMsg(content,0)
        })


        /*移动好友*/
        $(".chat_footer .bottom .move").click(function(){
            $(this).children("dl").toggle();
        });

        /*提示弹窗*/
        $(".chat_footer .bottom .zz").click(function(){
            $(".prompt_pop").show();
        });
        $(".prompt_pop .content a").click(function(){
            $(".prompt_pop").hide();
        });



        function sendMsg(content,type) {
            var index=layer.load(0, {time: 3000});
            $.post('<?=\yii\helpers\Url::to(['chat/say'])?>',{_csrf:_csrf,content:content,type:type,rec_uid:chat_obj_uid},function(result){
                layer.close(index)
                console.log(result)
                $("#div").html('')
                loadData()
            })
        }

        //加载数据
        loadData()
        function loadData() {
            var index=layer.load(0, {time: 3000});
            //当前请求时间戳
            var time = (''+(new Date).getTime()).substr(0,10)
            $.post('<?=\yii\helpers\Url::to(['chat/record'])?>',{_csrf:_csrf,record_id:record_id,time:time,rec_uid:chat_obj_uid},function(result){
                if(result.length>0){
                    var html = '';
                    result.map(function(item,index){
                        //更新记录id
                        record_id = item[0]
                        var user_info = item[2]===chat_uid?chat_info:chat_obj_info;
                        html +='<div class="'+(item[2]===chat_uid?'right':'left')+'">\n' +
                            '            <div class="avatar"><img src="'+(user_info.hasOwnProperty('face')?user_info.face:'')+'">'+((user_info.hasOwnProperty('type')&&user_info.type>0)?'<i class="icon iconfont icon-vip"></i>':'')+'</div>\n' +
                            '            <div class="text">\n' +
                            '                <h2><i>ID：'+(user_info.hasOwnProperty('id')?user_info.id:'')+'</i><span>'+(user_info.type_name)+'</span></h2>\n' +
                            '                <div class="message">\n' +
                            '                    <div class="info">'+(item[3]==='1'?'<img src="'+item[4]+'">':'<p>'+showHtmlTag(item[4])+'</p>')+'</div>\n' +
                            '                </div>\n' +
                            '            </div>\n' +
                            '        </div>'
                    })
                    $("#chat-box").append(html)
                }
                layer.close(index)

            })
        }

        setInterval(loadData,20000)
    });


    function friend(opt_index){
        var tip = ['','是否添加好友?','是否移至陌生人?','是否移至黑名单?'];
        var url = ['','<?=\yii\helpers\Url::to(['mine/add-friend'])?>','<?=\yii\helpers\Url::to(['mine/know-friend'])?>','<?=\yii\helpers\Url::to(['mine/black-friend'])?>'];
        if(tip.hasOwnProperty(opt_index)){
            var msg = tip[opt_index];
            var obj = {};
            obj._csrf=_csrf
            obj.f_uid=chat_obj_uid;
            layer.confirm(msg,function(){
                var index = layer.load();
                $.post(url[opt_index],obj,function(result){
                    layer.msg(result.msg)
                    layer.close(index)
                })
            })
        }
    }

</script>
<?php $this->endBlock()?>
