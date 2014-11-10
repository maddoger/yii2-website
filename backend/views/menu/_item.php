<?php

/* @var yii\web\View $this */
/* @var array|maddoger\website\common\models\Menu $item  */
?>
<li id="menu-item-<?= $item['id'] ?>">
    <?= $item['title'] ?>
    <ul>
        <?php
        if (isset($item['children']) && !empty($item['children'])) {
            foreach ($item['children'] as $child) {
                echo $this->render('_item', ['item' => $child]);
            }
        }
        ?>
    </ul>
</li>