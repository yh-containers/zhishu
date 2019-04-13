<?php
namespace app\models;

class Vote extends BaseModel
{
    public static function tableName()
    {
        return '{{%vote}}';
    }


    /**
     * 获取压盘类型
     * */
    public static function getPushType($type=null)
    {
        $push_type = ['平','涨','跌'];
        if(is_null($type)){
            return $push_type;
        }else{
            if(isset($push_type[$type])){
                return $push_type[$type];
            }
            return false;
        }
    }

    //下注方案
    public static function getVoteUp($type=null)
    {
        $data = ['未知','涨','跌'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }
    //开奖状态
    public static function getAwardState($type=null)
    {
        $data = ['未开奖','涨','跌','','返还'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }
    //开奖状态
    public static function getStatus($type=null)
    {
        $data = ['未知','创建','已开奖'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }
    //获胜状态
    public static function getIsWin($type=null)
    {
        $data = ['未知','赢','输'];
        if(is_null($data)){
            return $data;
        }else{
            return isset($data[$type])?$data[$type]:'';
        }
    }
    //返还原额
    public function back()
    {
        if($this->status!=1){ throw new \Exception('未处于待开奖状态无法进行此操作');}
        try{
            //直接返回
            $transaction = self::getDb()->beginTransaction();
            User::modMoney($this->uid,$this->money,'原额返还.',['money_change_type'=>UserMoneyLogs::TYPE_BACK]);
            $this->award_state = 4;
            $this->status = 2;//已开奖
            $this->open_time = date('Y-m-d H:i:s');//已开奖
            $this->save();
            $transaction->commit();
        }catch (\Exception $e){
            $transaction->rollBack();
            throw new \Exception($e->getMessage());

        }
    }
    //返还原额
    public function open()
    {

        if($this->status!=1){ throw new \Exception('未处于待开奖状态无法进行此操作');}
        //大盘信息
        $model_pan = Pan::findOne($this->wid);
        if(empty($model_pan)) throw new \Exception('大盘信息异常');
        if(empty($model_pan['compare'])){
            //--当前-天开盘的下一期
//            $model_pan_next = Pan::find()->where(['>','id',$this->wid])->andWhere(['date'=>date('Y-m-d',$this->create_time)])->limit(1)->one();
//            if(empty($model_pan_next)) throw new \Exception('该期已是最后一期,无法进行开奖');
            //执行开奖动作
//            $model_pan->up_date=$model_pan_next['date'];
//            $model_pan->up_time=$model_pan_next['time'];
//            $model_pan->up_price=$model_pan_next['current_price'];//当前价格
            $model_pan->compare=$model_pan['current_price']>$model_pan['up_price']?2:($model_pan['current_price']<$model_pan['up_price']?1:3);//价格比较1涨 2跌 3平
            $model_pan->save();
        }else{
            //直接进行开奖动作
            $model_pan->handleVote(null);
        }

    }

    /*
     * 获取交易费率
     * */
    public static function getPer()
    {
        $content = Setting::getContent('normal');
        $content = $content?json_decode($content,true):[];
        $per = isset($content['per'])?$content['per']:0;
        return $per;
    }

    /*
     * 下注用户信息
     * */
    public function getlinkUser()
    {
        return $this->hasOne(User::className(),['id'=>'uid']);
    }


}