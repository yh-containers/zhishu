<?php
namespace app\models;


class UserMoneyLogs extends BaseModel
{
    protected $ignore_update_time=true;

    public static function tableName()
    {
        return '{{%user_money_logs}}';
    }

    /**
     * 自动添加时间戳，序列化参数
     * @return array
     */
    public function behaviors()
    {
        $behaviors = [];
        if($this->use_create_time){
            $behaviors[]=[
                'class' => \yii\behaviors\TimestampBehavior::className(),
                'attributes' => [
                    \yii\db\ActiveRecord::EVENT_BEFORE_INSERT => ['create_time'],
                ],
            ];
        }
        return $behaviors;
    }


}