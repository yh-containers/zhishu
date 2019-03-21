<?php
namespace app\widgets;

use Yii;

class IndexTip extends \yii\bootstrap\Widget
{
    //倒计时秒
    public $second = 15;

    public function init()
    {
        parent::init();

    }

    /**
     * {@inheritdoc}
     */
    public function run()
    {
        $use_content = \app\models\Setting::getContent('use');
        $use_content = $use_content?json_decode($use_content,true):[];
        $content = !empty($use_content['intro'])?$use_content['intro']:'';
        return $this->render('indexTip',[
            'content'=>$content,
            'second'=>$this->second,
        ]);
    }
}
