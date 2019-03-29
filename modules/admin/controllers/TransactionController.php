<?php
namespace app\modules\admin\controllers;

class TransactionController extends DefaultController
{
    public function actionIndex()
    {
        $date = $this->request->get('date');
        $date = $date?$date:date('Y-m-d');
        $type = $this->request->get('type',0);

        $query = \app\models\Pan::find()->where(['date'=>$date,'type'=>$type]);
        $count = $query->count();
        $pagination = new \yii\data\Pagination(['totalCount' => $count]);
        $list = $query->offset($pagination->offset)->limit($pagination->limit)->orderBy('id desc')->all();
        $ids = array_column($list,'id');
//        var_dump($ids);exit;
        $vote = \app\models\Vote::find()
            ->asArray()
            ->select(['wid','y_count'=>'count(*)','up_money_total'=>'sum(if(is_up=1,money,0))','down_money_total'=>'sum(if(is_up=2,money,0))'])
            ->where(['in','wid',$ids])
            ->groupBy('wid')
            ->one();

        !empty($vote) && $vote = array_column($vote,null,'wid');
        foreach ($list as &$vo) {
            $vo['y_count']  = isset($vote[$vo['id']])?$vote[$vo['id']]['y_count']:0;
            $vo['up_money_total']  = isset($vote[$vo['id']])?$vote[$vo['id']]['up_money_total']:0;
            $vo['down_money_total']  = isset($vote[$vo['id']])?$vote[$vo['id']]['down_money_total']:0;
        }

        return $this->render('index', [
            'date' => $date,
            'list' => $list,
            'type' => $type,
            'pagination' => $pagination
        ]);
    }

    //详情
    public function actionDetail()
    {
        $id = $this->request->get('id',0);
        $model = \app\models\Pan::find()->with(['linkVote.linkUser'])->where(['id'=>$id])->one();
        return $this->render('detail',[
            'model' => $model,
        ]);
    }

    //原额返回
    public function actionBack()
    {
        $id = $this->request->get('id',0);
        $model = \app\models\Vote::findOne($id);
        if(empty($model)){$this->asJson(['code'=>0,'msg'=>'操作对象异常']);}
        try{
            $model && $model->back();
            $this->asJson(['code'=>1,'msg'=>'操作成功']);
        }catch (\Exception $e){
            $this->asJson(['code'=>0,'msg'=>'异常:'.$e->getMessage()]);
        }
    }

    //原额返回
    public function actionOpen()
    {
        $id = $this->request->get('id',0);//投票id
        $model = \app\models\Vote::findOne($id);
        if(empty($model)){$this->asJson(['code'=>0,'msg'=>'操作对象异常']);}
        try{
            $model && $model->open();
            $this->asJson(['code'=>1,'msg'=>'操作成功']);
        }catch (\Exception $e){
            $this->asJson(['code'=>0,'msg'=>'异常:'.$e->getMessage()]);
        }
    }
}