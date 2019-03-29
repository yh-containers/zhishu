<?php
namespace app\widgets;


class Footer extends \yii\bootstrap\Widget
{
    //倒计时秒
    public $current_active;

    public function init()
    {
        parent::init();

    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $chat_num = \app\models\UserChat::find()->where(['is_read'=>0,'rec_uid'=>\Yii::$app->controller->user_id])->count();
        return $this->render('footer',[
            'current_active' => $this->current_active,
            'chat_num'       => $chat_num,
        ]);
    }
}