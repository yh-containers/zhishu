<?php
namespace app\modules\admin\controllers;

class TransactionController extends DefaultController
{
    public function actionIndex()
    {
        $type = \Yii::$app->request->get('type',0);

        $date = date('Y-m-d');
        $query = \app\models\Pan::find()->where(['date'=>$date,'type'=>$type]);
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount' => $count]);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->all();
        foreach ($list as &$vo) {
            $info = $vo->getVote()->asArray()->select(['y_count'=>'count(*)','up_money_total'=>'sum(if(is_up=1,money,0))','down_money_total'=>'sum(if(is_up=2,money,0))'])->one();
            $vo['y_count']  = $info['y_count'];
            $vo['up_money_total']  = $info['up_money_total'];
            $vo['down_money_total']  = $info['down_money_total'];
        }

        return $this->render('index', [
            'list' => $list,
            'type' => $type,
            'pagination' => $pagination
        ]);
    }
}