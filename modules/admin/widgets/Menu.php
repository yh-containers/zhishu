<?php
namespace app\modules\admin\widgets;

use yii\base\Widget;

class Menu extends Widget
{
    public $current_active;

    public function init()
    {
        parent::init();

    }

    public function run()
    {
        $menu = [
            [
                'name'  => '用户管理',
                'href'  => '',
                'column'=> 'user',
                'child' => [
                    ['name' => '用户列表','href'=>'user/index','child'=>[]]
                ]
            ],
            [
                'name'  => '交易管理',
                'href'  => '',
                'column'=> 'transaction',
                'child' => [
                    ['name' => '交易记录','href'=>'transaction/index','child'=>[]]
                ]
            ],
            [
                'name'  => '系统设置',
                'href'  => '',
                'column'=> 'system',
                'child' => [
                    ['name' => '常用设置','href'=>'system/setting','child'=>[]],
                    ['name' => '用户投诉','href'=>'system/complaint','child'=>[]],
                    ['name' => '用户协议','href'=>'system/protocol','child'=>[]],
                    ['name' => '用户注册协议','href'=>'system/protocol-reg','child'=>[]],
                    ['name' => '帮助中心','href'=>'system/help-center','child'=>[]],
                    ['name' => '管理员列表','href'=>'system/manage','child'=>[]],
                    ['name' => '数据库备份','href'=>'backup/index','child'=>[]],
                ]
            ],
        ];
        //当前选中页面
        $current_active = $this->current_active;
        return $this->render('menu',compact('menu','current_active'));
    }
}