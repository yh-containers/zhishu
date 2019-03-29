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
        //上证指数
        $sz_count = \app\models\Pan::find()->where(['type'=>0,'date'=>date('Y-m-d')])->count();
        $sz_open_time = \app\models\Pan::get_type(0,'con');
        //德国
        $gdaxi_count = \app\models\Pan::find()->where(['type'=>1,'date'=>date('Y-m-d')])->count();
        $gdaxi_open_time = \app\models\Pan::get_type(1,'con');
        //今日下注数量
        $press_count = \app\models\Vote::find()->where(['>=','create_time',strtotime(date('Y-m-d'))])->count();

        return $this->render('index',[
            'user_count' => $user_count,
            'sz_count' => $sz_count,
            'gdaxi_count' => $gdaxi_count,
            'press_count' => $press_count,
            'sz_open_time' => $sz_open_time,
            'gdaxi_open_time' => $gdaxi_open_time,
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