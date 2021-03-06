<?php
namespace app\models;


class UserMoneyLogs extends BaseModel
{
    const TYPE_DEFAULT = 0; //余额来源默认 0其它
    const TYPE_CHARGE_OUT = 1; //余额来源 充值-发送给某用户
    const TYPE_CHARGE_IN = 2; //余额来源 充值-发送方给接收用户
    const TYPE_CHOOSE = 3; //余额来源 下注
    const TYPE_CHOOSE_WIN = 4; //余额来源 下注
    const TYPE_COMMISSION = 5; //余额来源 佣金
    const TYPE_BACK = 6; //余额来源 返还
    const TYPE_WITHDRAW_UP = 7; //上架
    const TYPE_WITHDRAW_DOWN = 8; //下架

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
                // if you're using datetime instead of UNIX timestamp:
                 'value' => date('Y-m-d H:i:s'),
            ];
        }
        return $behaviors;
    }


    public static function getType()
    {
        return [
            ['type'=>self::TYPE_DEFAULT,'name'=>'其它'],
            ['type'=>self::TYPE_CHARGE_OUT,'name'=>'充值-给予'],
            ['type'=>self::TYPE_CHARGE_IN,'name'=>'来源-获得'],
            ['type'=>self::TYPE_CHOOSE,'name'=>'下注'],
            ['type'=>self::TYPE_CHOOSE_WIN,'name'=>'下注-获胜'],
            ['type'=>self::TYPE_COMMISSION,'name'=>'佣金'],
            ['type'=>self::TYPE_BACK,'name'=>'返还'],
            ['type'=>self::TYPE_WITHDRAW_UP,'name'=>'上架'],
            ['type'=>self::TYPE_WITHDRAW_DOWN,'name'=>'下架'],
        ];
    }

}