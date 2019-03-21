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
        return $this->render('footer',[
            'current_active' => $this->current_active
        ]);
    }
}