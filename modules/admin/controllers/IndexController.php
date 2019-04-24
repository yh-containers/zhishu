<?php
namespace app\modules\admin\controllers;

class IndexController extends DefaultController
{

    public function actions()
    {
        return [
            //默认验证码刷新页面不会自动刷新
            'captcha' => [
                'class' => 'app\modules\admin\action\CaptchaAction',
                'testLimit' => 1,
                'maxLength' => 4,
                'minLength' => 4,
                'padding' => 1,
                'height' => 50,
                'width' => 140,
                'offset' => 1,
            ],
        ];
    }

    public function actionIndex()
    {
        //用户总数
        $user_count = \app\models\User::find()->count();
        //总元宝数量
        $sum_money = \app\models\User::find()->sum('money');
        //上证指数
        $sz_count = \app\models\Pan::find()->where(['type'=>0,'date'=>date('Y-m-d')])->count();
        $sz_open_time = \app\models\Pan::get_type(0,'con');
        //德国
        $gdaxi_count = \app\models\Pan::find()->where(['type'=>1,'date'=>date('Y-m-d')])->count();
        $gdaxi_open_time = \app\models\Pan::get_type(1,'con');
        //今日下注数量
        $press_count = \app\models\Vote::find()->where(['>=','create_time',strtotime(date('Y-m-d'))])->count();

        //查看投票数据--只看近30天的数据
        $_30_time = strtotime('-30 days',strtotime(date('Y-m-d')));

        $vote_data = \app\models\Vote::find()
            ->asArray()
            ->select([
                'vote_times'=>'count(*)',
                'sum_money'=>'sum(money)',
                'current_date'=>'left(open_time,10)',
                'type'
            ])->
            where(['>','create_time',$_30_time])
            ->groupBy(['type', 'left(open_time,10)'])
            ->all();
//        var_dump($vote_data);exit;

        //德国指数
        $gdaxi_data = $zhishu_data = [];
        foreach ($vote_data as $vo){
            if($vo['type']){
                $gdaxi_data[] = $vo;
            }else{
                $zhishu_data[] = $vo;
            }
        }
        //按日期显示数据
        $gdaxi_data = array_column($gdaxi_data,null,'current_date');
        $zhishu_data = array_column($zhishu_data,null,'current_date');

        $charge_legend = ['德国指数交易金额','德国指数交易次数','上证指数交易金额','上证指数交易次数','交易总量'];
        $charge_date = [];
        $charge_data = [
            ['name'=>'德国指数交易金额','type'=>'line','itemStyle'=>['color'=>'#990099'],'data'=>[]],
            ['name'=>'德国指数交易次数','type'=>'line','itemStyle'=>['color'=>'#ff0000'],'data'=>[]],
            ['name'=>'上证指数交易金额','type'=>'line','itemStyle'=>['color'=>'#0000ff'],'data'=>[]],
            ['name'=>'上证指数交易次数','type'=>'line','itemStyle'=>['color'=>'#00FF06'],'data'=>[]],
            ['name'=>'交易总量','type'=>'line','itemStyle'=>['color'=>'#FFC000'],'data'=>[]],
        ];
        for($i=0;$i<=30;$i++){
            $date = date('Y-m-d',strtotime($i.' days',$_30_time));
            $charge_date[] = $date;
            //德国
            $gdaxi_money = isset($gdaxi_data[$date])?$gdaxi_data[$date]['sum_money']:0;
            $charge_data[0]['data'][] = (float)$gdaxi_money;
            $charge_data[1]['data'][] = intval(isset($gdaxi_data[$date])?$gdaxi_data[$date]['vote_times']:0);
            //指数
            $zhishu_money = isset($zhishu_data[$date])?$zhishu_data[$date]['sum_money']:0;
            $charge_data[2]['data'][] = (float)$zhishu_money;
            $charge_data[3]['data'][] = intval(isset($zhishu_data[$date])?$zhishu_data[$date]['vote_times']:0);
            $charge_data[4]['data'][] = (float)($gdaxi_money+$zhishu_money);
        }
        return $this->render('index',[
            'user_count' => $user_count,
            'sum_money' => $sum_money,
            'sz_count' => $sz_count,
            'gdaxi_count' => $gdaxi_count,
            'press_count' => $press_count,
            'sz_open_time' => $sz_open_time,
            'gdaxi_open_time' => $gdaxi_open_time,

            'charge_legend' => $charge_legend,
            'charge_date' => $charge_date,
            'charge_data' => $charge_data,
        ]);
    }

    //用户登录
    public function actionLogin()
    {
        $request = \Yii::$app->request;
        if($request->isAjax){
            $account = $request->post('account');
            $password = $request->post('password');
            $verify = $request->post('verify');

            if(empty($account)) return $this->asJson(['code'=>0,'msg'=>'请输入帐号']);
            if(empty($password)) return $this->asJson(['code'=>0,'msg'=>'请输入密码']);
            if(empty($verify)) return $this->asJson(['code'=>0,'msg'=>'请输入密码']);

            $captcha = new \yii\captcha\CaptchaValidator();
            $captcha->captchaAction = 'admin/index/captcha';
            if(!$captcha->validate($verify))  return $this->asJson(['code'=>0,'msg'=>'验证码错误']);

            $manage = \app\models\Manage::find()->where(['account'=>$account])->one();
            if(empty($manage)) return $this->asJson(['code'=>0,'msg'=>'用户不存在']);
            $generate_pwd = \app\models\Manage::generatePwd($password,$manage->salt);
            if($generate_pwd!=$manage->password) return  $this->asJson(['code'=>0,'msg'=>'用户名或密码不正确']);
            if($manage->status!=1) return  $this->asJson(['code'=>0,'msg'=>'帐号已被禁用']);

            $session = \yii::$app->session;
            // 开启session
            $session->open();
            $session->setTimeout(86400);
            $session['admin_user_info'] =[
                'user_id' => $manage->id,
                'name' => $manage->name,
            ];

            return $this->asJson(['code'=>1,'msg'=>'登录成功','url'=>\yii\helpers\Url::to(['index/index'])]);
        }

        return $this->renderPartial('login',[

        ]);
    }



    /*
     * 退出
     * */
    public function actionLogout()
    {
        $session = \yii::$app->session;
        $session->destroy();
        $this->redirect(\yii\helpers\Url::to(['index/login']));
    }

    public function actionTest()
    {
        $result=\app\models\User::modMoney(10,-10,'abc');
        var_dump($result);
    }

    public function actionBackup()
    {
        /** @var \demi\backup\Component $backup */
        $backup = \Yii::$app->backup;

        $file = $backup->create();
        var_dump($file);
//        $console->stdout('Backup file created: ' . $file . PHP_EOL, \yii\helpers\Console::FG_GREEN);
    }

}