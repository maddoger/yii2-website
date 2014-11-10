<?php

/* @var yii\web\View $this */
/* @var array[] $items */
/* @var maddoger\website\common\models\Menu $menu */

$this->registerJs('$("#menu-items-editor > ol").nestedSortable({
    forcePlaceholderSize: true,
    handle: "div.panel-heading",
    helper:	"clone",
    items: "li",
    opacity: .6,
    placeholder: "placeholder",
    revert: 250,
    tabSize: 25,
    tolerance: "pointer",
    toleranceElement: "> div",
    isTree: true,
    expandOnHover: 700,
    startCollapsed: false,
    update: function(event, ui){
        var item = ui.item;
        var id = item.prop("id");
        var field = $("#"+id+"-parent_id");
        var parent = item.parent().parent().eq(0);
        field.val(parent.is("li") ? parent.data("id") : '.$menu->id.');
    }
});');

?>
<div id="menu-items-editor">
    <?php
    echo '<ol>';
    if ($items) {
        foreach ($items as $item) {
            echo $this->render('_item', ['item' => $item]);
        }
    }
    echo '</ol>';
    ?>
</div>