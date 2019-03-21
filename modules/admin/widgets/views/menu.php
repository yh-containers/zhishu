<!-- =============================================== -->

<!-- Left side column. contains the sidebar -->
<aside class="main-sidebar">
    <!-- sidebar: style can be found in sidebar.less -->
    <section class="sidebar">

        <!-- sidebar menu: : style can be found in sidebar.less -->
        <ul class="sidebar-menu" data-widget="tree">
            <?php foreach($menu as $vo){?>
            <li class="treeview <?= in_array($vo['column'],$current_active)?' active menu-open':''?>">
                <a href="#">
                    <i class="fa fa-dashboard"></i> <span><?=$vo['name']?></span>
                    <span class="pull-right-container">
                          <i class="fa fa-angle-left pull-right"></i>
                        </span>
                </a>
                <ul class="treeview-menu">
                    <?php foreach($vo['child'] as $item){?>
                    <li class="<?= in_array($item['href'],$current_active)?' active':''?>"><a href="<?= $item['href']?\yii\helpers\Url::to([$item['href']]):''?>"><i class="fa fa-circle-o"></i> <?=$item['name']?></a></li>
                    <?php } ?>
                </ul>
            </li>
            <?php } ?>

        </ul>
    </section>
    <!-- /.sidebar -->
</aside>

<!-- =============================================== -->