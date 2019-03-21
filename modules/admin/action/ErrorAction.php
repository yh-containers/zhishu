<?php
namespace app\modules\admin\actions;

class ErrorAction extends \yii\web\ErrorAction
{
    protected function renderHtmlResponse()
    {
        echo 123;exit;
        return $this->controller->render($this->view ?: $this->id, $this->getViewRenderParams());
    }
}