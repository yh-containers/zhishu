<?php
//设置时间区
ini_set('date.timezone','Asia/Shanghai');

class WebsocketTest {
    //消息类型
    const BIND_USER='bind_user';
    const INIT_DATA='init_data'; //初始化数据
    const USER_OFFONLINE='off_user';//下线
    const IS_OPEN_STATE = 'is_open_state';//开盘状态  1开盘 0闭盘
    const VOTE_DATA = 'vote_data';//投票数据消息类型
    const CHECK_DJS = 'check_djs';//查看倒计时


    const URL = 'http://43.225.157.28/';
    const ZHISHU='zhishu';
    const GDAXI='gdaxi';
    const HANDLE_AWARD='handle_award';
    const E_CYCLE_SECOND=1000; //执行周期
    const E_CYCLE_TIMES=10; //执行周期
    const E_ZHISHU_DATA_NUM = 15; //指数数量
    const E_GDAXI_DATA_NUM = 15; //指数数量

    const INSERT_TABLE = 'zs_pan'; //操作表
    const VOTE_TABLE = 'zs_vote'; //操作--下注表


    //无需执行的日期 六、日
    private $no_need_week = [0,6];


    public $server;
    public $pdo;

    public $sh_table;   //上证指数
    public $table;      //德国指数

    public $temp_second_table; //德国临时秒表

    public function __construct() {

        //临时表--秒
        $this->temp_second_table = new Swoole\Table(1024);
        $this->temp_second_table->column('time', swoole_table::TYPE_STRING,20);
        $this->temp_second_table->column('current_price', swoole_table::TYPE_FLOAT);       //1,2,4,8
        $this->temp_second_table->column('up_price', swoole_table::TYPE_FLOAT);
        $this->temp_second_table->column('top_price', swoole_table::TYPE_FLOAT);
        $this->temp_second_table->column('down_price', swoole_table::TYPE_FLOAT);
        $this->temp_second_table->column('que_key', swoole_table::TYPE_STRING,255);
        $this->temp_second_table->column('last_time', swoole_table::TYPE_STRING,20);
        $this->temp_second_table->column('first_time', swoole_table::TYPE_STRING,20);
        $this->temp_second_table->column('is_flash', swoole_table::TYPE_INT);
        $this->temp_second_table->column('is_wait', swoole_table::TYPE_INT);
        $this->temp_second_table->column('is_init', swoole_table::TYPE_INT);
        $this->temp_second_table->create();

        //上证指数
        $this->sh_table = new Swoole\Table(1024);
        $this->sh_table->column('time', swoole_table::TYPE_STRING,20);
        $this->sh_table->column('current_price', swoole_table::TYPE_FLOAT);       //1,2,4,8
        $this->sh_table->column('up_price', swoole_table::TYPE_FLOAT);
        $this->sh_table->column('top_price', swoole_table::TYPE_FLOAT);
        $this->sh_table->column('down_price', swoole_table::TYPE_FLOAT);
        $this->sh_table->column('is_wait', swoole_table::TYPE_INT);
        $this->sh_table->column('time', swoole_table::TYPE_STRING,20);
        $this->sh_table->column('up_time', swoole_table::TYPE_STRING,20);
        $this->sh_table->create();

        //初始化数据
        $this->initData($this->sh_table,self::ZHISHU);

        //德国指数表
        $this->table = new Swoole\Table(1024);
        $this->table->column('time', swoole_table::TYPE_STRING,20);
        $this->table->column('current_price', swoole_table::TYPE_FLOAT);       //1,2,4,8
        $this->table->column('up_price', swoole_table::TYPE_FLOAT);
        $this->table->column('top_price', swoole_table::TYPE_FLOAT);
        $this->table->column('down_price', swoole_table::TYPE_FLOAT);
        $this->table->column('is_wait', swoole_table::TYPE_INT);
        $this->table->column('time', swoole_table::TYPE_STRING,20);
        $this->table->column('up_time', swoole_table::TYPE_STRING,20);
        $this->table->create();
        //初始化数据
        $this->initData($this->table,self::GDAXI);





        $this->server = new Swoole\WebSocket\Server("0.0.0.0", 9502);
        $this->server->set([
            'worker_num' => 8,
            'task_worker_num' =>16,
            'daemonize' => 1,       //加入此参数后，执行php server.php将转入后台作为守护进程运行
            'dispatch_mode' => 5,   //1平均分配，2按FD取模固定分配，3抢占式分配，默认为取模(dispatch=2)
            'heartbeat_idle_time' => 300,   //与heartbeat_check_interval配合使用。表示连接最大允许空闲的时间
            'heartbeat_check_interval' => 60, //心跳检测
        ]);
        $this->server->on('WorkerStart', array($this, 'onWorkerStart'));
        $this->server->on('Connect', array($this, 'onConnect'));
        $this->server->on('Receive', array($this, 'onReceive'));
        $this->server->on('Close', array($this, 'onClose'));
        // bind callback
        $this->server->on('Task', array($this, 'onTask'));
        $this->server->on('Finish', array($this, 'onFinish'));

        $this->server->on('open', array($this,'onOpen') );
        $this->server->on('message', array($this,'onMessage'));

        $this->server->on('request',array($this,'onRequest'));
        $this->server->start();
    }

    public function onWorkerStart( $serv , $worker_id) {
        echo "onWorkerStart {$worker_id} \n";
        if( $serv->taskworker ) {
            $this->getPdo();
        }else{
            //下一分钟开始执行
            $cur_second = strtotime(date('H:i:s'));
            $next_second = strtotime('+1 minute',strtotime(date('H:i')));
            $time = ($next_second-$cur_second)*1000;

            echo 'tick---#'.$worker_id.'date_time :'.date('Y-m-d H:i:s').PHP_EOL;
            if($worker_id==0) {
                //启动定时任务
//                $serv->after($time, function () use ($serv, $worker_id) {
//                    echo 'tick---#after worker_id' . $worker_id . ' date_time :' . date('Y-m-d H:i:s') . PHP_EOL;
                    $serv->tick(self::E_CYCLE_SECOND, function () use ($serv, $worker_id) {
                        echo 'tick---#start worker_id:' . $worker_id.'plan:'.self::ZHISHU . ' date_time :' . date('Y-m-d H:i:s') . PHP_EOL;
                        //获取指数数据
                        $serv->task(self::handleEncodeMsg(self::ZHISHU));
                    });

//                });
            }
            if($worker_id==1) {
                //启动定时任务
//                $serv->after($time, function () use ($serv, $worker_id) {
//                    echo 'tick---#after worker_id' . $worker_id . ' date_time :' . date('Y-m-d H:i:s') . PHP_EOL;
                    $serv->tick(self::E_CYCLE_SECOND, function () use ($serv, $worker_id) {
                        echo 'tick---#start worker_id:' . $worker_id.'plan:'.self::GDAXI . ' date_time :' . date('Y-m-d H:i:s') . PHP_EOL;
                        //获取指数数据
                        $serv->task(self::handleEncodeMsg(self::GDAXI));
                    });

//                });
            }
        }
    }

    public function onConnect( $serv, $fd, $from_id ) {
//        $fdinfo =$serv->getClientInfo($fd); //查看绑定的用户id
        echo "Client {$fd} connect:\n";
//        if(empty($fdinfo['uid'])) {
//            $serv->push($fd, '1,0,');//需要绑定用户
//        }
        echo "Client {$fd} connect\n";
    }
    public function onReceive( swoole_server $serv, $fd, $from_id, $data ) {
        echo "Get Message From Client {$fd}:{$data}\n";
    }
    public function onOpen(swoole_websocket_server $serv, $request) {
        $fdinfo =$serv->getClientInfo($request->fd); //查看绑定的用户id
        echo "Client {$request->fd} connect:\n";
        if(empty($fdinfo['uid'])) {
            $serv->push($request->fd, '1,0,');//需要绑定用户
        }
        echo "server: handshake success with fd{$request->fd}\n";
    }
    public function onMessage(Swoole\WebSocket\Server $serv, $frame) {
        $data = explode(',',$frame->data);
        //消息类型 1-绑定userid 2心跳包 3初始化数据 4倒计时+
        $msg_type = isset($data[0])?$data[0]:0;
        //消息载体
        $msg_payload = isset($data[1])?$data[1]:0;
        //消息内容
        $msg_content= isset($data[2])?$data[2]:'';
        echo '$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$----'.$msg_type.'-----$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$$'.PHP_EOL;
        if($msg_type==1 && $msg_payload>0){
            $serv->bind($frame->fd,$msg_payload);
            //发起在线响应
            $serv->task(self::handleEncodeMsg(self::BIND_USER, ['fd'=>$frame->fd,'uid'=>$msg_payload]));
        }elseif($msg_type==3){
            //初始化数据
            $serv->task(self::handleEncodeMsg(self::INIT_DATA, ['fd'=>$frame->fd,'type'=>$msg_payload]));
        }elseif($msg_type==4){
            //初始化数据
            $serv->task(self::handleEncodeMsg(self::CHECK_DJS, ['fd'=>$frame->fd,'type'=>$msg_payload]));
        }
        echo "receive from {$frame->fd}:{$frame->data},opcode:{$frame->opcode},fin:{$frame->finish}\n";
//        $serv->push($frame->fd, "this is server");
    }

    public function onRequest( $request, $response ) {
        // 接收http请求从get获取message参数的值，给用户推送
        // $this->server->connections 遍历所有websocket连接用户的fd，给所有用户推送
        foreach ($this->server->connections as $fd) {
            $this->server->push($fd, $request->get['message']);
        }
        echo "Get onRequest From Client \n";
    }

    public function onTask(swoole_server $serv, $task_id, $src_worker_id, $msg_content) {
        echo 'onTask---#start $task_id:'.$task_id.' worker_id: '.$src_worker_id.' content : '.$msg_content.' date_time :'.date('Y-m-d H:i:s').PHP_EOL;
        //无需指定的日期
        $week = date("w");//当前星期几

        $msg_data = json_decode($msg_content,true);
        $msg_type = isset($msg_data['type'])?$msg_data['type']:'';
        $payload = isset($msg_data['payload'])?$msg_data['payload']:'';
        //获取数据库动作
        $this->getPdo();
        //获取当前时间
        $current_date = date('H:i:s');

        //倒计时距离结束时间
        //下一分钟开始执行
        $cur_second = strtotime($current_date);
        $next_second = strtotime('+1 minute',strtotime(date('H:i')));
        $dj_second = ($next_second-$cur_second);
        $dj_second = $dj_second?$dj_second:60;
        //当前分钟
        $minute = (int)date('i');
        //单数等待 双数下注
        if($minute%2){
            $is_wait=1;
        }else{
            $is_wait=0;
        }


        switch ($msg_type){
            case self::BIND_USER :
                $fd = isset($payload['fd'])?$payload['fd']:0;
                $uid = isset($payload['uid'])?$payload['uid']:0;
                $this->bindUser($fd,$uid);
                break;

            case self::USER_OFFONLINE :
                $fd = isset($payload['fd'])?$payload['fd']:0;
                $uid = isset($payload['uid'])?$payload['uid']:0;
                $this->bindUser($fd,$uid,0);
                break;

            case self::GDAXI :
                //非交易日
                if(in_array($week,$this->no_need_week)){
                    echo 'this week is '.$week.PHP_EOL;
                    return;
                }

                //百分比数值
                $press_data = $this->getVoteCount(self::GDAXI);
                array_unshift($press_data,1); //增加类型
//                $current_time = time();
                $current_month = (int)date('m');
//                $condition = [
//                    [['15:00:00'=>'23:31:00'],[4,5,6,7,8,9,10,11]],
//                    [['16:00:00'=>'00:31:00'],[12,1,2,3]],
//                ];
//                foreach ($condition as $key=>$vo){
//                    $pointer_month = $vo[2]; //指定月份时间
//                    $pointer_month = $vo[1]; //指定月份时间
//                    $open_time = $vo[0]; //开盘时间
//                    if(in_array($current_month, $pointer_month)){
//                        $start_time = strtotime(key($open_time));
//                        $end_time = strtotime(end($open_time));
//                        break;
//                    }
//                }
                echo '--wait_time:'.(isset($wait_time)?$wait_time:'--').PHP_EOL;
                //执行获取计划
                if(
                    (in_array($current_month,[4,5,6,7,8,9,10,11]) && $current_date>'15:00:00' && $current_date<'23:31:00')
                    ||
                    (in_array($current_month,[12,1,2,3]) && ($current_date>'16:00:00' || $current_date<'00:31:00'))
                ){
                    $content = file_get_contents("http://pdfm.eastmoney.com/EM_UBG_PDTI_Fast/api/js?id=GDAXI_UI&TYPE=r&rtntype=5");
                    $content = substr($content,1,-1);
                    $data = json_decode($content,true);

                    list($temp_data,$table_data) = $this->handleGDAXIData($data['info'], $data['data'], $content);
                    if (!empty($table_data)) {
                        echo 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx';
                        $send_data['type'] = 'table';
                        $send_data['is_wait'] = $table_data['is_wait'];
                        $send_data['djs'] = $dj_second;
                        $send_data['data'] = [self::handelDataMode($table_data)];
                    } else {
                        $send_data['type'] = 'temp';
                        $send_data['data'] = [self::handelDataMode($temp_data)];
                    }

                }else{
                    //清除缓存数据
//                    $this->temp_second_table->exist(self::GDAXI) && $this->temp_second_table->del(self::GDAXI);

                    $send_data['type'] = self::IS_OPEN_STATE;
                    $send_data['mode'] = self::GDAXI;
                    $send_data['data'] = 0;
                }
                break;

            case self::ZHISHU :
                //非交易日
                if(in_array($week,$this->no_need_week)){
                    echo 'this week is '.$week.PHP_EOL;
                    return;
                }
                //百分比数值
                $press_data = $this->getVoteCount(self::ZHISHU); //增加类型
                array_unshift($press_data,0);
                if(($current_date>'09:30:00' && $current_date<'11:31:00') || ($current_date>'13:00:00' && $current_date<'15:01:00')){

                    //上证指数
                    //指数流程数据获取
                    $content = file_get_contents('http://hq.sinajs.cn/list=sh000001');
                    $data = $content?explode(',',$content):'';

                    list($temp_data,$table_data) = $this->handleZSData($data,$content);

                    if (!empty($table_data)) {
                        echo 'xxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxxx'.PHP_EOL;
                        $send_data['type'] = 'table';
                        $send_data['is_wait'] = $table_data['is_wait'];
                        $send_data['djs'] = $dj_second;
                        $send_data['data'] = [self::handelDataMode($table_data)];
                    } else {
                        $send_data['type'] = 'temp';
                        $send_data['data'] = [self::handelDataMode($temp_data)];
                    }
                }else{
                    //清除缓存数据
//                    $this->temp_second_table->exist(self::ZHISHU) && $this->temp_second_table->del(self::ZHISHU);

                    $send_data['type'] = self::IS_OPEN_STATE;
                    $send_data['mode'] = self::ZHISHU;
                    $send_data['data'] = 0;
                }
                break;

            case self::INIT_DATA :
                $p_fd = $payload['fd'];
                $type = $payload['type'];
                echo '$$$$$$$$$$$$$$$$$$$$$$$$$'.$payload.'$$$$$$$$$$$$$$$$$$$$$$$$$'.self::INIT_DATA.PHP_EOL;
                //上证指数
                $obj = $this->sh_table;
                $temp_key = self::ZHISHU;
                if($type==1){
                    //德国指数
                    $obj = $this->table;
                    $temp_key = self::GDAXI;
                }
                $send_data = ['type'=>'init_data','data'=>[],'is_wait' => $is_wait,'djs'=>$dj_second];

                //xx
                $info = $this->temp_second_table->get($temp_key);
                //记录队伍链
                $que_key = empty($info['que_key'])?[]:explode(',',$info['que_key']);
                foreach($que_key as $vo){
                    $row = $obj->get($vo);
                    $send_data['data'][] = self::handelDataMode($row);
                }
                break;
            case self::CHECK_DJS:
                //倒计时
                $p_fd = $payload['fd'];
                $this->server->push($p_fd, json_encode(['djs'=>$dj_second]));
                break;
            default:
                break;
        }


        $finish_data['type'] = $msg_type;
        //open pan
        isset($send_data['is_wait']) && $finish_data['is_wait']=$send_data['is_wait'];
        //广播数据
        if(isset($send_data) && is_array($send_data)){
            if(isset($p_fd)){
                //剩余多少秒--倒计时
//                $send_data['type']==self::INIT_DATA && $this->server->push($p_fd, json_encode(['djs'=>$dj_second]));
                //发送投票数据
                isset($press_data) && $this->server->push($p_fd, self::handleEncodeMsg(self::VOTE_DATA,$press_data));

                $this->server->push($p_fd, json_encode($send_data));

            }else{

                foreach ($this->server->connections as $fd) {
                    //发送投票数据
                    isset($press_data) && $this->server->push($fd, self::handleEncodeMsg(self::VOTE_DATA,$press_data));

                    //剩余多少秒--倒计时
//                    echo 'send_data_type:'.$send_data['type'];
//                    $send_data['type']=='table' && $this->server->push($fd, json_encode(['djs'=>$dj_second]));

                    $this->server->push($fd, json_encode($send_data));

                }
            }

        }


//        echo "Tasker进程接收到数据";
//        echo "#{$serv->worker_id}\tonTask: [PID={$serv->worker_pid}]: task_id=$task_id, data_len=".strlen($content).".".PHP_EOL;
        $serv->taskworker && $serv->finish(json_encode($finish_data));
    }

    public function onFinish(swoole_server $serv, $task_id, $msg_data) {
        $msg_data = json_decode($msg_data,true);
        $result ='';

        if($msg_data['type']!=self::INIT_DATA && isset($msg_data['is_wait'])){
            $type=$msg_data['type']===self::GDAXI?1:0;
            $req_url = self::URL.'index/handle-vote?type='.$type.'&time='.time();

            $result=file_get_contents($req_url);

            //任务完成后执行开奖
            if($msg_data['type']===self::ZHISHU){ //上证指数
                $result=file_get_contents(self::URL.'index/handle');

            }elseif($msg_data['type']=self::GDAXI){//德国指数
                $result=file_get_contents(self::URL.'index/handle-gdaxi');
            }

        }


        echo "Task#$task_id finished, data_len=".strlen($result).PHP_EOL;
    }

    public function onClose( $serv, $fd, $from_id ) {
        $fdinfo =$serv->getClientInfo($fd); //查看绑定的用户id
        $uid=0;//绑定用户id
        if(!empty($fdinfo['uid'])) {
            $uid=$fdinfo['uid'];
        }
        //发起在线响应--已绑定用户就下线
        $uid && $serv->task(self::handleEncodeMsg(self::USER_OFFONLINE,['fd'=>$fd,'uid'=>$uid]));
        echo "Client {$fd} user_id: {$uid} close connection\n";
    }

    //消息处理
    public static function handleEncodeMsg($type,$payload=[]) {
        $content = json_encode(['type'=>$type,'payload'=>$payload]);
        return  $content?$content:'';
    }

    //消息解密
    public static function handleDecodeMsg($content) {
        $content = json_decode($content,true);
        return $content?$content:[];
    }

    //处理数据格式
    public static function handelDataMode($data=[]) {
        $need_field = ['time'=>'','up_price'=>'','current_price'=>'','top_price'=>'','down_price'=>'','compare'=>0];
        foreach($need_field as $key=>$vo){
            $value=isset($data[$key])?$data[$key]:$vo;
            if($key!='time' && $key!='compare'){
                //强制两位小数
                $value = sprintf('%.2f',$value);
            }
            $need_field[$key] = $value;
        }
        return array_values($need_field);
    }


    //绑定用户在线状态
    public function bindUser($fd,$uid,$online=1)
    {
        if($online){
            $sql = 'update zs_user set swoole_fd="'.$fd.'",online_time="'.date('Y-m-d H:i:s').'" where id='.$uid;
        }else{
            $sql = 'update zs_user set swoole_fd=0,offline_time="'.date('Y-m-d H:i:s').'" where id='.$uid;
        }

        try {
            echo $sql.PHP_EOL;
            $this->pdo->exec($sql);
        }catch (\Exception $e){
            echo $e->getMessage().PHP_EOL;
//            file_put_contents('error-mysql.log','sql-------异常:'.$e->getMessage().PHP_EOL);
            return false;
        }
    }


    //处理指数数据入库操作
    public  function handleZSData($data,$content)
    {
        echo 'handleZSDATA'.PHP_EOL;

        try {
            list($temp_data,$table_data) = $this->handleTableData($data[31],$data[3],self::ZHISHU,$this->sh_table);



            if(!empty($table_data)){
                $insert_data = [
                    'type' => "'".'0'."'",
                    'today_price' => "'".$data[1]."'",
                    'yesterday_price' =>"'". $data[2]."'",
                    'current_price' => "'".$table_data['current_price']."'",
                    'up_price' => "'".$table_data['up_price']."'",
                    'top_price' => "'".$table_data['top_price']."'",
                    'down_price' => "'".$table_data['down_price']."'",
                    'tran_num' => "'".$data[8]."'",
                    'tran_money' => "'".$data[9]."'",
                    'date' => "'".$data[30]."'",
                    'content' => "'".iconv('GB2312'."'", 'UTF-8', substr($content,0,-2))."'",
                    'time' => "'".$table_data['time']."'",
                    'up_date' => "'".date('Y-m-d')."'",
                    'up_time' => "'".$table_data['up_time']."'",
                    'create_time' => "'".date('Y-m-d H:i:s')."'",
                    'is_wait' => "'".$table_data['is_wait']."'",
                ];
                $sql = 'Insert into '.self::INSERT_TABLE.' ('.implode(',',array_keys($insert_data)).' ) value ('.implode(',',$insert_data).') ';



                    echo $sql.PHP_EOL;
                    $this->pdo->exec($sql);

            }
        }catch (\Exception $e){
            echo $e->getMessage().PHP_EOL;
//            file_put_contents('error-mysql.log','sql-------异常:'.$e->getMessage().PHP_EOL);
            return false;
        }



        return [$temp_data,$table_data];
    }

    //处理德国
    public function handleGDAXIData($info,$current_data,$content)
    {
        echo '---------------------------德国指数--------------------------------'.PHP_EOL;
        //最后一个元素
        $last_arr = array_pop($current_data);
        $last_unit_arr = explode(',',$last_arr);
        //时间
        $time = isset($last_unit_arr[0])?$last_unit_arr[0]:'';
        $time_arr = explode(' ',$time);
        $date = isset($time_arr[0])?$time_arr[0]:'';
        $date_time = isset($time_arr[1])?$time_arr[1].':00':'';
        //金额
        $money = isset($last_unit_arr[1])?$last_unit_arr[1]:'';
        $today_price = isset($info['o'])?$info['o']:'';//开盘价
        $top_price = isset($info['h'])?$info['h']:'';//最高价
        $down_price = isset($info['l'])?$info['l']:'';//最低价

        try {

            list($temp_data,$table_data) = $this->handleTableData($date_time,$money,self::GDAXI,$this->table);

            if(!empty($table_data)){
                $insert_data = [
                    'type' => "'".'1'."'",
                    'today_price' => "'".$today_price."'",       //开盘价
                    'yesterday_price' =>"'"."0"."'",
                    'current_price' => "'".$table_data['current_price']."'",       //当前价
                    'up_price' => "'".$table_data['up_price']."'",
                    'top_price' => "'".($table_data['top_price'])."'",
                    'down_price' => "'".($table_data['down_price'])."'",
                    'tran_num' => "'"."0"."'",
                    'tran_money' => "'"."0"."'",
                    'date' => "'".$date."'",
                    'content' => "'".json_encode($info)."'",
                    'time' => "'".$table_data['time']."'",
                    'up_date' => "'".date('Y-m-d')."'",
                    'up_time' => "'".$table_data['up_time']."'",
                    'create_time' => "'".date('Y-m-d H:i:s')."'",
                    'is_wait' => "'".$table_data['is_wait']."'",
                ];
                echo 'data_info :'.json_encode($insert_data).PHP_EOL;



                $sql = 'Insert into '.self::INSERT_TABLE.' ('.implode(',',array_keys($insert_data)).' ) value ('.implode(',',$insert_data).') ';


                echo $sql.PHP_EOL;
                $this->pdo->exec($sql);

            }
        }catch (\Exception $e){
            echo $e->getMessage().PHP_EOL;
            //            file_put_contents('error-mysql.log','sql-------异常:'.$e->getMessage().PHP_EOL);
            return false;
        }
        return [$temp_data,$table_data];
    }

    //初始数据
    public function initData($table,$mode)
    {
        $type = $mode==self::GDAXI?1:0;
        //获取链接状态
        $pdo = $this->sqlConnect();//验证pdo是否可以使用-无法使用
//        $sql = 'SELECT * FROM '.self::INSERT_TABLE.' where date="'.date('Y-m-d').'" and type='.$type.'  ORDER BY id desc limit 15';
        $sql = 'SELECT * FROM '.self::INSERT_TABLE.' where  type='.$type.'  ORDER BY id desc limit 15';
        echo $sql.PHP_EOL;
        $init_data = [];
        foreach ($pdo->query($sql) as $row) {
            $init_data[] = $row;
        }
        $init_data = array_reverse($init_data);
        $que_key=[];
        foreach ($init_data as $row) {
            $key = $row['id'];
            if(!empty($key)){
                $que_key[]=$key;
                $data = [
                    'time'              =>  $row['time'],
                    'current_price'     =>  $row['current_price'],
                    'up_price'          =>  $row['up_price'],
                    'top_price'         =>  $row['top_price'],
                    'down_price'        =>  $row['down_price'],
                    'is_wait'           =>  $row['is_wait'],

                ];
                $table->set($key,$data);
                $data['que_key'] = implode(',',$que_key);
                $data['last_time'] = strtotime($row['create_time']);
                $data['first_time'] = strtotime($row['up_date'].' '.$row['up_time']);
                $data['is_flash'] = 0;//是否刷新数据
                $data['is_init']  = 1;//是否刷新数据
                $this->temp_second_table->set($mode,$data);
            }
        }

    }



    //记录数据
    public function handleTableData($time,$current_money,$mode,$table)
    {
        $current_money = $current_money? sprintf('%.2f',$current_money):0.00;
        //当前分钟
        $minute = date('i');

        //表数据-新入
        $temp_data = $table_data=[];
        //刷新数据
        $is_flash = 0;
        //临时条目索引
        $temp_time_index = (string)time();

        //获取该模式下临时数据
        $info = $this->temp_second_table->get($mode);

        //记录队伍链
        $que_key = empty($info['que_key'])?[]:explode(',',$info['que_key']);
        
        //清空隔天数据//超过120秒 重置临时数据
//        if(!empty($info['last_time']) && date('Y-m-d',$info['last_time'])!=date('Y-m-d')){
//            $info=null;
//        }else
        if(!empty($info['last_time']) && (int)$temp_time_index-(int)$info['last_time']>120){
            $info=null;
        }


        if(empty($info)){
            echo '*********************************'.PHP_EOL;
            echo '初始化数据:'.$info['is_flash'].PHP_EOL;
            echo '*********************************'.PHP_EOL;
            //直接赋数据
            $current_price = $up_price = $top_price = $down_price = (float)$current_money;

            //单数等待 双数下注
            if(((int)$minute)%2){
                $is_wait=1;
            }else{
                $is_wait=0;
            }

            $temp_data['first_time'] = $temp_time_index;

        }elseif (!empty($info['is_flash'])){

            $current_money==0 && $current_money = $info['current_price'];
            echo '*********************************'.PHP_EOL;
            echo '刷新数据:'.$info['is_flash'].PHP_EOL;
            echo '*********************************'.PHP_EOL;

            //上一次记录时间
            $up_record_time = $info['last_time'];
            //直接赋数据
            $current_price = $up_price = $top_price = $down_price = (float)$current_money;

            $temp_data['first_time'] = $temp_time_index;
        }else{
            //上一次记录时间
            $up_record_time = $info['last_time'];
            $current_price = $current_money?(float)$current_money:$info['up_price'];
            $up_price = $info['up_price']?$info['up_price']:$current_money;
            $top_price = $current_price>$info['top_price']?$current_price:$info['top_price'];
            $down_price = $current_money<$info['down_price']?$current_price:$info['down_price'];

        }

        //临时数据

        $temp_data = array_merge($temp_data,[
            'time'              =>  $time,
            'current_price'     =>  $current_price,
            'up_price'          =>  $up_price?$up_price:$current_price,
            'top_price'         =>  $top_price?$top_price:$current_price,
            'down_price'        =>  $down_price?$down_price:$current_price,

        ]);
//        echo '*********************************';
//        echo (date('i',$up_record_time)).'--------------'.$minute.PHP_EOL.'********'.$is_wait;
//        echo date('i',$up_record_time)!=$minute;
//        echo '*********************************';
        //小于不等于当前记录时间-或者没有设置上一条记录时间
        if(!isset($up_record_time) || date('i',$up_record_time)!=$minute){
            if($info['is_init']==1){
                //初始化数据
                //单数等待 双数下注
                if(((int)$minute)%2){
                    $is_wait=1;
                }else{
                    $is_wait=0;
                }
            }
            //记录是下单还是等待
            $temp_data['is_wait'] = isset($is_wait)?$is_wait:(int)!$info['is_wait'];
            $que_key[] = $temp_time_index;
            $table_data = $temp_data;
            $table_data['time'] = date('H:i:s',$info['first_time']);
            $table_data['up_time'] = date('H:i:s',$temp_time_index);
            //记录表数据
            $table->set($temp_time_index,$table_data);
            //满载 删除头元素
            if($table->count()>15){
                $first_key = array_shift($que_key);
                echo '*********************************';
                var_dump($first_key);
                var_dump($table->get($first_key));
                var_dump($is_flash);
                echo '*********************************';
                $table->del($first_key);
            }
            $is_flash = 1; //需要刷新数据

            //非初始化数据
            $temp_data['is_init']   = 0;
        }else{

        }
        //队伍链
        $temp_data['que_key']   = implode(',',$que_key);
        $temp_data['last_time'] = $temp_time_index;
        $temp_data['is_flash']  = $is_flash;
        $this->temp_second_table->set($mode,$temp_data);
        return [$temp_data,$table_data];
    }

    //获取数据总数投票
    public function getVoteCount($mode)
    {
        $type = self::GDAXI==$mode?1:0;
        $up = $down=$up_per=$down_per = 0;
        $sql = 'SELECT sum(if(is_up=1,money,0)) as up_sum, sum(per_money) as per_money_total, sum(if(is_up=2,money,0)) as down_sum  FROM '.self::VOTE_TABLE.' where wid is null  and status=1 and type='.$type;

        //看涨方数据
//        $up_per = $up_money_total?sprintf('%.2f',($down_money_total-$per_money)/$up_money_total*100):0.00;
//        $down_per = $down_money_total?sprintf('%.2f',($up_money_total-$per_money)/$down_money_total*100):0.00;
//        echo
        foreach ($this->pdo->query($sql) as $row) {
//            var_dump($row);
            $up = $row['up_sum']?$row['up_sum']:0.00;
            $down = $row['down_sum']?$row['down_sum']:0.00;
            $up_per= $up>0?sprintf('%.2f',($down-$row['per_money_total'])/$up*100):0.00;
            $down_per= $down>0?sprintf('%.2f',($up-$row['per_money_total'])/$down*100):0.00;
        }

        return [$up,$up_per,$down,$down_per];
    }



    //获取pdo
    public function getPdo()
    {
        HELL:
        try{
            if(empty($this->pdo)){
                echo '第一次链接sql'.PHP_EOL;
                $this->pdo = $this->sqlConnect();
            }else{
                //验证pdo是否可以使用-无法使用
                $sql = 'SELECT id FROM '.self::INSERT_TABLE.' limit 1';
                $this->pdo->query($sql);
            }
        }catch (\Exception $e){
            $this->pdo=null;
            echo 'mysql 异常捕获.'.$e->getMessage().PHP_EOL;
//            file_put_contents('error-mysql.log','sql-------异常222:'.$e->getMessage().PHP_EOL);
            goto HELL;//进行重连
        }

    }


    //链接mysql-pdo
    private function sqlConnect()
    {
        echo 'sqlConnect.'.PHP_EOL;
        $pdo = new PDO(
            "mysql:host=43.225.157.28;port=3306;dbname=zhishu",
            "root",
            "zhishu123",
            array(
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES 'UTF8';",
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_PERSISTENT => true
            )
        );
        return $pdo;
    }
}
new WebsocketTest();