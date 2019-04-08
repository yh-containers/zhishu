<?php

namespace app\controllers;


use yii\helpers\Url;
use yii\web\Controller;

class CommonController extends Controller
{
    public $layout = 'mobile';
    /**
     * @var \yii\web\Request
     * */
    public $request;

    public $user_id = 0;
    public $is_need_login = true;
    protected $ignore_action = '';

    /**
     * 在程序执行之前，对访问的方法进行权限验证.
     * @param \yii\base\Action $action
     * @return bool
     * @throws ForbiddenHttpException
     */
    public function beforeAction($action)
    {
        if(!$this->user_id && strpos($this->ignore_action,$action->id)===false){
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

    /*
     * 初始方式
     * */
    public function init()
    {
        $this->request = \Yii::$app->request;

        $user_info = \Yii::$app->session->get('user_info');
        if(!empty($user_info)){
            $this->user_id = $user_info['user_id'];
        }

    }




}
