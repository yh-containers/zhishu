<?php

namespace app\modules\admin\controllers;

use yii\web\Controller;

/**
 * Default controller for the `admin` module
 */
class DefaultController extends Controller
{
    public $user_id=0;
    public $user_name=0;
    protected $ignore_action = ['admin/index/login','admin/index/captcha'];
    /**
     * @var \yii\web\Request
     * */
    public $request;

    public function init()
    {
        $this->request = \Yii::$app->request;
        $session = \yii::$app->session;
        //开启session
        $session->open();
        //当前路由
        //登录信息
        $admin_user_info = $session->get('admin_user_info');


        $this->user_id = !empty($admin_user_info['user_id'])?$admin_user_info['user_id']:0;
        $this->user_name = !empty($admin_user_info['name'])?$admin_user_info['name']:'';

        if($this->user_id){
            $model = \app\models\Manage::findOne($this->user_id);
            //禁用session
            if(empty($model) || $model['status']!=1){
                $this->user_id = 0;
                \Yii::$app->session->destroy();
            }
        }

    }



    /**
     * 在程序执行之前，对访问的方法进行权限验证.
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        $current_route = \yii::$app->requestedRoute;
        if(!$this->user_id && !in_array($current_route,$this->ignore_action)){
            if($this->request->isAjax){
                //需要登录才能访问
                return $this->asJson(['code'=>0,'msg'=>'请先登录','url'=>\yii\helpers\Url::to(['index/login'])]);
            }else{
                //需要登录才能访问
                return $this->redirect(\yii\helpers\Url::to(['index/login']));
            }

        }else{
            return true;
        }
    }

    public function actionTest()
    {
        return $this->render('test');
    }
    public function actionError()
    {
        return 'error';
    }
}
