<?php

/* @var yii\web\View $this */
/* @var array[] $items */
/* @var maddoger\website\common\models\Menu $menu */
?>
<div id="menu-items-editor">
    <?php
    echo '<ul>';
    if ($items) {
        foreach ($items as $item) {
            echo $this->render('_item', ['item' => $item]);
            //var_dump($item);
        }
    }
    echo '</ul>';
    ?>
</div>