<?php

namespace app\controllers;


use app\models\Pan;

class IndexController extends CommonController
{
    protected $ignore_action = 'login,registered,send-mailer,forget,handle,handle-gdaxi,handle-vote';
//
//    public function actionIndex()
//    {
//        $type = (int)$this->request->get('type',0);
//        $is_lock = (int)$this->request->get('is_lock',0);
//        if(!$is_lock){
//
//            //是否锁定主页--德国时间
//            $gdaxi_con = \app\models\Pan::get_type(1,'con');
//            foreach ($gdaxi_con as $key=>$vo){
//                $pointer_month = $vo[1]; //指定月份时间
//                $open_time = $vo[0]; //开盘时间
//                if(in_array((int)date('m'), $pointer_month)){
//                    $start_time = strtotime(key($open_time));
//                    $end_time = strtotime(end($open_time));
//                    if(time()>$start_time){
//                        $this->redirect(\yii\helpers\Url::to(['','is_lock'=>1,'type'=>1]));
//                    }
//                    break;
//                }
//            }
//
//        }
//        //获取用户信息
//        $user_info = \app\models\User::findOne($this->user_id);
//        if(!empty($user_info) && empty($user_info['is_show_protocol'])){
//            $is_show_protocol=1;
//            $user_info->is_show_protocol=$is_show_protocol;
//            $user_info->save(false);
//        }
//        return $this->render('index',[
//            'user_info' => $user_info,
//            'type' => $type,
//            'is_show_protocol' => isset($is_show_protocol)?$is_show_protocol:0,
//        ]);
//    }

    public function actionIndex()
    {
        $type = (int)$this->request->get('type',0);
        $is_lock = (int)$this->request->get('is_lock',0);
        if(!$is_lock){
            //是否锁定主页--德国时间
            $gdaxi_con = \app\models\Pan::get_type(1,'con');
            foreach ($gdaxi_con as $key=>$vo){
                $pointer_month = $vo[1]; //指定月份时间
                $open_time = $vo[0]; //开盘时间
                if(in_array((int)date('m'), $pointer_month)){

                    $start_time = strtotime(key($open_time));
                    $end_time = strtotime(end($open_time));
                    if(time()>$start_time){
                        $this->redirect(\yii\helpers\Url::to(['','is_lock'=>1,'type'=>1]));
                    }
                    break;
                }
            }

        }
        //获取用户信息
        $user_info = \app\models\User::findOne($this->user_id);
        if(!empty($user_info) && empty($user_info['is_show_protocol'])){
            $is_show_protocol=1;
            $user_info->is_show_protocol=$is_show_protocol;
            $user_info->save(false);
        }
        $is_open = (int)!(\app\models\Pan::getTypeState($type));

        //自己下注多少金额
        $press_info = \app\models\Vote::find()->where(['type'=>$type,'status'=>1,'uid'=>$this->user_id, 'wid'=>null])->one();

        return $this->render('index',[
            'user_info' => $user_info,
            'type' => $type,
            'is_show_protocol' => isset($is_show_protocol)?$is_show_protocol:0,
            //是否开放
            'is_open'  => $is_open,
            'press_info'  => $press_info,
        ]);
    }

    /*
     * 用户注册
     * */
    public function actionRegistered()
    {
        if(\Yii::$app->request->isAjax){
            $php_input = \Yii::$app->request->post();
            $model =new \app\models\User();
            $model->scenario = \app\models\User::SCENARIO_REGISTER;

            $model->attributes = $php_input;
            try{
                if(!$model->validate()){
                    $error_msg = $model->getFirstErrors();
                    return $this->asJson(['code'=>0,'msg'=>$error_msg[key($error_msg)]]);
                }else{
                    \app\models\Mail::checkVerify($php_input['email'],$php_input['verify'],0);
                }
            }catch (\Exception $e){
                return $this->asJson(['code'=>0,'msg'=>$e->getMessage()]);
            }


            $state = $model->save();
            return $this->asJson(['code'=>$state?1:0,'msg'=>$state?'操作成功':'操作失败']);

        }
        return $this->render('registered',[

        ]);
    }

    //用户登录
    public function actionLogin()
    {
        $request = \Yii::$app->request;
        if($request->isAjax){
            $account = $request->getBodyParam('account');
            $password = $request->getBodyParam('password');
            if(empty($account)) throw new \yii\base\UserException('帐号不能为空');
            if(empty($password)) throw new  \yii\base\UserException('密码不能为空');

            $user_info = \app\models\User::find()->andWhere(['or',['like','username',$account],['like','email',$account]])->limit(1)->one();
            if(empty($user_info))  throw new  \yii\base\UserException('帐号或密码异常');
            //验证密码
            if(\app\models\User::generatePwd($password,$user_info['salt'])!=$user_info['password'])  throw new  \yii\base\UserException('帐号或密码异常');
            //状态判断
            if($user_info['status']!=1)  throw new  \yii\base\UserException('帐号已被禁用');

            //登录成功
            $session = \Yii::$app->session;
            $session->setTimeout(86400);
            $session->set('user_info',[
                'user_id'   =>  $user_info['id'],
            ]);
            return $this->asJson(['code'=>1,'msg'=>'登录成功','url'=>\yii\helpers\Url::to(['index/index'])]);
        }
        return $this->render('login',[

        ]);
    }

    //退出
    public function actionLogout()
    {
        // 销毁session中所有已注册的数据
        $session = \Yii::$app->session;
        $session->destroy();
        $this->redirect(\yii\helpers\Url::to(['index/login']));
    }

    //忘记密码
    public function actionForget()
    {
        if(\Yii::$app->request->isAjax){
            $php_input = \Yii::$app->request->post();
            $model =new \app\models\User();
            $model->scenario = \app\models\User::SCENARIO_FORGET;
            $model->attributes = $php_input;
            if(!$model->validate()){
                $error_msg = $model->getFirstErrors();
                return  $this->asJson(['code'=>0,'msg'=>$error_msg[key($error_msg)]]);
            }
            $user_model = \app\models\User::find()->where(['email'=>$php_input['email']])->one();
            $user_model->password = $php_input['password'];
            $state = $user_model->save();
            return $this->asJson(['code'=>(int)$state,'msg'=>$state?'找回成功':'找回异常']);

        }
        return $this->render('forget',[

        ]);
    }

    //发送邮箱
    public function actionSendMailer()
    {
        $email = \Yii::$app->request->get('email');
        $type = \Yii::$app->request->get('type',0);
        try{
            $state = \app\models\Mail::sendMail($email,$type);
            return $this->asJson(['code'=>(int)$state,'msg'=>$state?'发送成功':'发送异常']);
        }catch (\Exception $e){
            return $this->asJson(['code'=>0,'msg'=>'发送异常:'.$e->getMessage()]);
        }
    }

    //帮助中心
    public function actionHelp()
    {
        $list = \app\models\SysHelp::find()->select('id,title')->where(['status'=>1])->orderBy('sort asc')->all();
        return $this->render('help',[
            'list' => $list,
        ]);
    }
    //帮助中心--详情
    public function actionHelpDetail()
    {
        $id = \Yii::$app->request->get('id');
        $model = \app\models\SysHelp::findOne($id);
        $model && $model->updateCounters(['views' => 1]);
        return $this->render('helpDetail',[
            'model' => $model,
        ]);
    }

    //操盘数据
    public function actionPanData()
    {
        $type = $this->request->get('type',0);
        $is_init = $this->request->get('is_init',0);
        $id = $this->request->get('id',0); //等待开奖

        //获取今天最后一场
        $model = \app\models\Pan::find()->where(['type'=>$type,'date'=>date('Y-m-d')])->orderBy('id desc')->limit(1)->one();
        $model || $model= new \app\models\Pan();
//        $id = $id?$id:$model->getAttribute('id');
        $id = $model->getAttribute('id');

        //上一盘开奖数据
        $up_compare = 0;//上一次开盘奖励情况
        $award_money = "0";
        if($is_init){
            $model_up = \app\models\Pan::find()->where(['<','id',$id])->orderBy('id desc')->one();
            $up_compare = !empty($model_up['compare'])?$model_up['compare']:0;
            //是否中奖--非初始化数据
            $award_info = \app\models\Vote::find()->where(['uid'=>$this->user_id,'wid'=>$model_up['id']])->one();
            $award_money = $award_info['is_win']==1?$award_info['get_money']:($award_info['award_state']==4?-$award_info['per_money']:-$award_info['money']);
        }

        $data = $model->getPanData($type,$is_init);

        //最近一次开奖时间
//        $time = $model->getAttribute('time');
        $time = date('H:i:s');
        $current_minute_second = strtotime($time);
        //距离下一分钟时间
        $next_minute_second = strtotime('+1 minute',strtotime(date('H:i')));
        //延迟3秒
        $open_next_second = $next_minute_second-$current_minute_second+3;//\app\models\Pan::getLastOpenSecond($type);
        //获取之前开盘数据
        list($open_data,$close_data) =\app\models\Pan::getCachePanData($type);
        //查询这一期结果
        $up_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>1,'wid'=>$id])->sum('money');
        $per_up_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>1,'wid'=>$id])->sum('per_money');
        $down_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>2,'wid'=>$id])->sum('money');
        $per_down_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>2,'wid'=>$id])->sum('per_money');
        //涨--用户
        $user_press_up_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>1,'wid'=>$id,'uid'=>$this->user_id])->sum('money');
        //跌--用户
        $user_press_down_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>2,'wid'=>$id,'uid'=>$this->user_id])->sum('money');
        $model_user = \app\models\User::findOne($this->user_id);

        //获取涨跌数据
        $per_money = $per_up_money+$per_down_money;
        //看涨方数据
        $up_per = $up_money?sprintf('%.2f',($down_money-$per_money)/$up_money*100):0.00;
        $down_per = $down_money?sprintf('%.2f',($up_money-$per_money)/$down_money*100):0.00;


        //获取当前开盘价跟收盘加
        $result = [
            //待开奖id
            'id'        => $model->getAttribute('id'),
            //关闭请求
            'is_close'  => \app\models\Pan::getTypeState($type),
            //距离下次开奖剩余时间
            'ons'       => (int)$open_next_second,
            //开盘价
            'open_data' => $open_data,
            //收盘价
            'close_data' => $close_data,
            //上一次开盘结果
            'up_data' => [$up_compare,$award_money],
            //上一次开盘结果
            'is_wait' => \app\models\Pan::handleWait(),

            'data' => $data,
            'user_money' => $model_user['money']?$model_user['money']:0.00,
            'o_data' => [
                $up_money?$up_money:0,
                $down_money?$down_money:0,
                $user_press_up_money?$user_press_up_money:0,
                $user_press_down_money?$user_press_down_money:0,
                $up_per,
                $down_per
            ],
        ];
        return $this->asJson($result);
    }

    //获取下压数据
    public function actionPressMoney()
    {
        $id = $this->request->get('id',0); //多少期
        $type = $this->request->get('type',0);
        $model_user = \app\models\User::findOne($this->user_id);
        $where=['uid'=>$this->user_id];
        //涨
        $up_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>1,'wid'=>$id])->andWhere($where)->sum('money');
        //跌
        $down_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>2,'wid'=>$id])->andWhere($where)->sum('money');
        //查询这一期结果
        $up_money_total = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>1,'wid'=>$id])->sum('money');
        $per_up_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>1,'wid'=>$id])->sum('per_money');
        $down_money_total = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>2,'wid'=>$id])->sum('money');
        $per_down_money = \app\models\Vote::find()->where(['type'=>$type,'is_up'=>2,'wid'=>$id])->sum('per_money');


        //获取涨跌数据
        $per_money = $per_up_money+$per_down_money;
        //看涨方数据
        $up_per = $up_money_total?sprintf('%.2f',($down_money_total-$per_money)/$up_money_total*100):0.00;
        $down_per = $down_money_total?sprintf('%.2f',($up_money_total-$per_money)/$down_money_total*100):0.00;


        return $this->asJson([
            $up_money?$up_money:0,
            $down_money?$down_money:0,
            $up_money_total?$up_money_total:0,
            $down_money_total?$down_money_total:0,
            $model_user['money']?$model_user['money']:0.00,
            $up_per,
            $down_per
        ]);
    }

    //获取其它信息
    public function actionOtherInfo()
    {
        $type = $this->request->get('type',0);
        $is_init = $this->request->get('is_init',0); //first ajax
        $model_user = \app\models\User::findOne($this->user_id);
        $model_pan = \app\models\Pan::find()->orderBy('id desc')->limit(1)->one();
        //下注数量
        $press_info = \app\models\Vote::find()->where(['type'=>$type,'status'=>1,'uid'=>$this->user_id, 'wid'=>null])->one();
        $is_up = empty($press_info)?0:$press_info['is_up'];
        $press_money = empty($press_info)?0:$press_info['money'];


        //获取之前开盘数据
        $open_data =\app\models\Pan::getCachePanData($type);
        $award_money = 0;
        //一定得开奖 -- 开奖状态
        if(!empty($model_pan) && empty($model_pan['is_wait'])){

            $i=5; //延迟 4秒
            while ($i>0){
                $i--;
                if($model_pan['compare']>0){
                    break;
                }else{
                    //等会再查
                    sleep(1);
                    $model_pan = \app\models\Pan::findOne($model_pan['id']);
                }
            }
            if($model_pan['compare']>0){
                //是否中奖--非初始化数据
                $award_info = \app\models\Vote::find()->where(['uid'=>$this->user_id,'status'=>2,'is_show'=>0,'wid'=>$model_pan['id']])->one();
                if ($award_info['award_state']==4){
                    $award_money = -$award_info['per_money'];
                }elseif($award_info['is_win']==1){
                    $award_money = $award_info['get_money'];
                }else{
                    $award_money = -$award_info['money'];
                }

                if($award_info){
                    $award_info->is_show=1;
                    $award_info->save();

                }
            }

        }


        return $this->asJson([
            $model_user['money']?$model_user['money']:0,
            [$is_up,$press_money],
            $open_data,
            [$award_money,$model_pan['id'],$model_pan['compare']],
//            $award_info?json_encode($award_info->getAttributes()):[],
        ]);
    }

    //检测是否可以开奖
    public function checkAwardStatus()
    {
        $id = $this->request->get('id',0); //多少期
        $model = \app\models\Pan::findOne($id);
        return $model['compare']>0?1:0;
    }

    //test入口
    public function actionTest()
    {

        $info = \app\models\Vote::find()->asArray()->where(['wid'=>991])->all();
        foreach($info as $vo){
            $award_info = \app\models\Vote::find()->where(['id'=>$vo['id']])->one();
            if ($award_info['award_state']==4){
                $award_money = -$award_info['per_money'];
            }elseif($award_info['is_win']==1){
                $award_money = $award_info['get_money'];
            }else{
                $award_money = -$award_info['money'];
            }
            var_dump('------------'.PHP_EOL);
            var_dump($award_info['uid']);
            var_dump($award_info['money']);
            var_dump($award_money);
        }

//        var_dump($award_info);
//        var_dump($award_money);
//        $data = [["21:10:00", 11940.18, 11939.77, 11942.07, 11939.77, 0],["21:09:00", 11939.77, 11941.35, 11941.35, 11939.77, 0]
// ,       ["21:14:00", 11939.69, 11942.31, 11942.31, 11939.69, 0]
//,["21:13:00", 11942.31, 11941.77, 11942.31, 11941.77, 0]
//,["21:11:00", 11941.19, 11940.18, 11941.22, 11939.78, 0]
//        ];
//        var_dump($data);
//        $sort_times = array_column($data,0);
//        array_multisort($sort_times,SORT_STRING,SORT_ASC,$data);
//        var_dump($data);
//
//        $arr = [1,2,3,4,5,6];
//        var_dump($arr);
//        array_shift($arr);
//        array_pop($arr);
//        $arr[]=7;
//        var_dump($arr);
//        var_dump(date_default_timezone_get());
//        var_dump(date('Y-m-d H:i:s'));
//        var_dump(\app\models\Pan::handleWait());
//        $id = $this->request->get('id',10);
//        $model = \app\models\User::findOne($id);
//        $model->vote_times=$model->vote_times+1;
//        $model->save();
        exit;
//        $week = date("w");
//        var_dump($week);
//        $date_time='00:30:00';
//        if(($date_time>'16:00:00' && $date_time<'23:59:59') || ($date_time>'00:00:00' && $date_time<'00:30:00')){
//            echo '123';
//        }
//        echo 321321;exit;
//        //测试
////        $model = \app\models\Pan::findOne(180);
////        $model->current_price = rand(3080,3099);
////        $model->save();
////        var_dump(\app\models\Pan::getLastOpenSecond(0));
//        //获取数据
//        $content = file_get_contents("http://pdfm.eastmoney.com/EM_UBG_PDTI_Fast/api/js?id=GDAXI_UI&TYPE=r&rtntype=5");
//        $content = substr($content,1,-1);
//        $content = json_decode($content,true);
//        var_dump($content);exit;

    }
    //处理投票数据
    public function actionHandleVote()
    {
        $type = $this->request->get('type',0);
        $time = $this->request->get('time',time());
        //查询所有未开奖数据--上证指数
        $info = \app\models\Pan::find()->where(['type'=>$type])->orderBy('id desc')->limit(1)->one();
        $info->setOpenData();
        if($info['is_wait']==0 && $info->id) {
//            $up_info = Pan::find()->where(['<','id',$info->id])->orderBy('id desc')->one();
            \app\models\Vote::updateAll([
                'wid'=>$info['id'],
                'record_wid_date'=>date('Y-m-d H:i:s',$time)
            ],['wid'=>null,'type'=>$type]);
        }


        return 1;
    }

    private function _handleVoteData($type=0)
    {
        //查询所有未开奖数据--上证指数
        $data = \app\models\Pan::find()->where(['type'=>$type, 'compare'=>0, 'is_wait'=>0])->all();
//        var_dump($data);
        foreach($data as $key=>$vo) {
            $current_model = $vo;
            //下一条数据
//            $compare_model = \app\models\Pan::find()->where(['type'=>$type])->andWhere(['<','id',$vo['id']])->orderBy('id desc')->limit(2)->all();
            $compare_model = \app\models\Pan::find()->where(['type'=>$type])->andWhere(['<','id',$vo['id']])->orderBy('id desc')->one();

            //闭盘数据
//            $close_model = !empty($compare_model[0])?$compare_model[0]:null;
//            //开盘数据
//            $open_model = !empty($compare_model[1])?$compare_model[1]:null;
            //--
            $close_model = $vo;
            $open_model = $compare_model;
//            var_dump($close_model->getAttributes());
//            var_dump($open_model->getAttributes());exit;
            if(empty($open_model) || empty($close_model)){
                return;
            }

            $current_model->compare=$open_model['current_price']>$close_model['current_price']?2:($open_model['current_price']<$close_model['current_price']?1:3);//价格比较1涨 2跌 3平
            $current_model->save();

        }
    }


    //处理结果--上证指数
    public function actionHandle()
    {
        $this->_handleVoteData();
        return;
        //查询所有未开奖数据--上证指数
        $data = \app\models\Pan::find()->where(['type'=>0, 'compare'=>0, 'is_wait'=>0])->all();
        foreach($data as $key=>$vo) {
            $current_model = $vo;
            //下一条数据
            $next_model = \app\models\Pan::find()->where(['type'=>0])->andWhere(['<','id',$vo['id']])->limit(2)->one();
//            $next_model = isset($data[$key+1])?$data[$key+1]:null;
//
//            //获取上一条数据
//            if(!isset($up_model)){
//                $up_model = \app\models\Pan::find()->where(['<', 'id',$vo['id']])->andWhere(['type'=>0])->orderBy('id desc')->limit(1)->one();
//            }else{
//                $up_model = $data[$key-1];
//            }

//            var_dump($up_model->getAttributes());

            //下一条数据有值
            if(!empty($next_model)) {

                //说明有数据--更新此次同步数据
//                $current_model->up_date=$next_model['date'];
//                $current_model->up_time=$next_model['time'];
//                $current_model->up_price=$next_model['current_price'];//当前价格
//
////                $current_model->top_price=empty($up_model['top_price'])?$current_model['current_price']:($current_model['current_price']>$up_model['top_price']?$current_model['current_price']:$up_model['top_price']);//最高价
////                $current_model->down_price=empty($up_model['down_price'])?$current_model['current_price']:($current_model['current_price']<$up_model['down_price']?$current_model['current_price']:$up_model['down_price']);//最低价
//                $current_model->top_price = $current_model['current_price']>$next_model['current_price']?$current_model['current_price']:$next_model['current_price'];
//                $current_model->down_price = $current_model['current_price']>$next_model['current_price']?$next_model['current_price']:$current_model['current_price'];

                $current_model->compare=$current_model['up_price']>$next_model['up_price']?2:($current_model['up_price']<$next_model['up_price']?1:3);//价格比较1涨 2跌 3平
//                var_dump($current_model->getAttributes());exit;
                $current_model->save();
//                var_dump($state);
//                var_dump($current_model->getAttributes());
            }
        }
    }
    //处理结果--德国指数
    public function actionHandleGdaxi()
    {

        $this->_handleVoteData(1);
        return;
        //查询所有未开奖数据--上证指数
        $data = \app\models\Pan::find()->where(['type'=>1, 'compare'=>0, 'is_wait'=>1])->orderBy('id asc')->all();

        foreach($data as $key=>$vo) {

            $current_model = $vo;
                //下一条数据
                $next_model = \app\models\Pan::find()->where(['type'=>1])->andWhere(['<','id',$vo['id']])->limit(1)->one();
//            //下一条数据
//            $next_model = isset($data[$key+1])?$data[$key+1]:null;
//
//            //获取上一条数据
//            if(!isset($up_model)){
//                $up_model = \app\models\Pan::find()->where(['<', 'id',$vo['id']])->andWhere(['type'=>1])->orderBy('id desc')->limit(1)->one();
//            }else{
//                $up_model = $data[$key-1];
//            }

            //下一条数据有值
            if(!empty($next_model)) {

//                //说明有数据--更新此次同步数据
//                $current_model->up_date=$next_model['date'];
//                $current_model->up_time=$next_model['time'];
//                $current_model->up_price=$next_model['current_price'];//当前价格
//
////                $current_model->top_price=empty($up_model['top_price'])?$current_model['current_price']:($current_model['current_price']>$up_model['top_price']?$current_model['current_price']:$up_model['top_price']);//最高价
////                $current_model->down_price=empty($up_model['down_price'])?$current_model['current_price']:($current_model['current_price']<$up_model['down_price']?$current_model['current_price']:$up_model['down_price']);//最低价
//                $current_model->top_price = $current_model['current_price']>$next_model['current_price']?$current_model['current_price']:$next_model['current_price'];
//                $current_model->down_price = $current_model['current_price']>$next_model['current_price']?$next_model['current_price']:$current_model['current_price'];

                $current_model->compare=$current_model['up_price']>$next_model['up_price']?2:($current_model['up_price']<$next_model['up_price']?1:3);//价格比较1涨 2跌 3平
                $current_model->save();
//                var_dump($state);
//                var_dump($current_model->getAttributes());
            }

        }
    }

    public function actionPhpinfo()
    {
        phpinfo();
    }

    /*
     * 获取当前时间
     * */
    public function actionGetTime()
    {
        var_dump(date('Y-m-d H:i:s'));
    }
}
