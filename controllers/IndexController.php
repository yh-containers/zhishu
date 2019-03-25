<?php

namespace app\controllers;


use app\models\Pan;

class IndexController extends CommonController
{
    protected $ignore_action = 'login,registered,send-mailer,forget,handle';

    public function actionIndex()
    {
        //获取用户信息
        $user_info = \app\models\User::findOne($this->user_id);
        return $this->render('index',[
            'user_info' => $user_info,
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

            $result = $model->actionSave($php_input);
            return $this->asJson($result);

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

            //登录成功
            $session = \Yii::$app->session;
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
        //获取今天最后一场
        $model = \app\models\Pan::find()->where(['type'=>$type,'date'=>date('Y-m-d')])->orderBy('id desc')->limit(1)->one();
        $model || $model= new \app\models\Pan();
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
        //获取当前开盘价跟收盘加
        $result = [
            //待开奖id
            'id'        => $model->getAttribute('id'),
            //关闭请求
            'is_close'  => 1,
            //距离下次开奖剩余时间
            'ons'       => (int)$open_next_second,
            //开盘价
            'open_data' => $open_data,
            //收盘价
            'close_data' => $close_data,
            'data' => $data
        ];
        return $this->asJson($result);
    }


    //test入口
    public function actionTest()
    {
        //测试
//        $model = \app\models\Pan::findOne(180);
//        $model->current_price = rand(3080,3099);
//        $model->save();
        var_dump(\app\models\Pan::getLastOpenSecond(0));
    }

    //处理结果
    public function actionHandle()
    {
        //查询所有未开奖数据
        $data = \app\models\Pan::find()->where(['type'=>0, 'compare'=>0])->orderBy('id desc')->all();
        foreach($data as $key=>$vo) {

            $current_model = $vo;
            //下一条数据
            $next_model = isset($vo[$key+1])?$vo[$key+1]:null;
            var_dump($key);
            var_dump('--------------------$current_model--------------------------');
            var_dump($current_model);
            var_dump('--------------------$next_model--------------------------');
            var_dump($next_model);exit;
            //下一条数据有值
            if(!empty($next_model)) {

                //说明有数据--更新此次同步数据
                $current_model->compare=$next_model['current_price']>$next_model['current_price']?1:2;//价格比较1涨 我跌
                $current_model->up_date=$next_model['date'];
                $current_model->up_time=$next_model['time'];
                $current_model->up_price=$next_model['current_price'];//当前价格
                $current_model->save();
            }

        }
    }

    public function actionPhpinfo()
    {
        phpinfo();
    }
}
