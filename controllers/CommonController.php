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
     * 用户模型
     * @var \app\models\User
     * */
    protected $user_model;

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
                \Yii::$app->response->format = \yii\web\Response::FORMAT_JSON;
                \Yii::$app->response->data = array(
                    'code' => 0,
                    'msg' => '请先登录',
                    'url' => \yii\helpers\Url::to(['index/login'])
                );
                return false;
//                return $this->asJson(['code'=>0,'msg'=>'请先登录','url'=>\yii\helpers\Url::to(['index/login'])]);
            }else{
                //需要登录才能访问
                $this->redirect(\yii\helpers\Url::to(['/index/login']));
                return false;
            }
        }

        return parent::beforeAction($action);

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
            $this->user_model = \app\models\User::findOne($this->user_id);

            //禁用session
            if(empty($this->user_model) || $this->user_model['status']!=1){
                $this->user_id = 0;
                \Yii::$app->session->destroy();
            }
        }

        //邀请码
        $req_code = $this->request->get('req_code','');
        if($req_code){
            \Yii::$app->session->set('req_code',$req_code);
        }

    }




}
