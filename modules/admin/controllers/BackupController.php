<?php
namespace app\modules\admin\controllers;

use app\models\DbModel;
use yii\helpers\Url;

class BackupController extends DefaultController
{
    public function actionIndex(){
        $dbModel = new DbModel();
        $dataProvider =$dbModel->getSqlFiles();

        return $this->render('/system/backup', [
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionCreate(){
        $dbModel = new DbModel();
        $dbModel->backUp();
        return $this->redirect([Url::to('backup/index')]);
    }

    public function actionDown($file)
    {
        $res = \YII::$app->response;
        return $res->sendFile(\Yii::$app->basePath.'/dbdata/' .$file);
    }


    public function actionUpdate($file){
        $dbModel = new DbModel();
        $dbModel->recoverSqlFile($file);
        return $this->redirect([Url::to('backup/index')]);
    }

    public function actionDel($file){
        $dbModel = new DbModel();
        $dbModel->deleteSqlFile($file);
        return $this->asJson(['code'=>1,'msg'=>'删除成功']);
    }

}