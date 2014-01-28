<?php
use yii\helpers\Html;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="menu-editor">
	<?php echo Html::beginForm('', 'post'); ?>

		<p>
			<input class="btn btn-primary" type="submit" name="save" value="<?= Yii::t('rusporting/website', 'Save') ?>"/>
			<a href="#" onclick="return treeAdd(1, this);" class="btn btn-success"><i class="fa fa-plus"></i></a>
		</p>

		<?php function pagesAdminMenu($menu, $level=1) {?>
		<ul class="tree-editor">
			<?php if (isset($menu) && $menu) foreach ($menu as $ar) {?>
				<li class="form-inline">
					<a href="#" onclick="return treeToggle(this);" class="btn btn-link"><i class="fa fa-caret-down"></i></a>

					<input type="hidden" name="level[]" value="<?php echo (int)$level;?>"/>
					<input type="hidden" name="delete[]" value="0"/>

					<input type="hidden" name="id[]" value="<?php echo intval($ar['id']);?>"/>


					<input class="form-control" style="width: 150px;" type="text" title="<?= Yii::t('rusporting/website', 'Title') ?>" placeholder="<?= Yii::t('rusporting/website', 'Title') ?>" name="title[]"
						   value="<?php echo Html::encode($ar['title']);?>"/>&nbsp;
					<input class="form-control" style="width: 250px;"  type="text" title="<?= Yii::t('rusporting/website', 'Link') ?>" placeholder="<?= Yii::t('rusporting/website', 'Link') ?>" name="link[]" value="<?php echo Html::encode($ar['link']);?>"/>
					&nbsp;

					<label title="<?= Yii::t('rusporting/website', 'Enabled') ?>" class="btn btn-default checkbox-hidden">
						<input type="hidden" name="enabled[]" data-default="1" value="<?php echo intval($ar['enabled']); ?>" />
					</label>&nbsp;

					<a href="#" title="<?= Yii::t('rusporting/website', 'Move up') ?>" onclick="return treeUp(this)" class="btn btn-primary"><i class="fa fa-arrow-up"></i></a>
					<a href="#" title="<?= Yii::t('rusporting/website', 'Mode down') ?>" onclick="return treeDown(this)" class="btn btn-primary"><i class="fa fa-arrow-down"></i></a> &nbsp;

					<a href="#" title="<?= Yii::t('rusporting/website', 'Add child') ?>" onclick="return treeAdd(<?php echo @$level+1;?>, this)" class="btn btn-success"><i class="fa fa-plus"></i></a> &nbsp;
					<a href="#" title="<?= Yii::t('rusporting/website', 'Delete') ?>" onclick="return treeDelete(this)" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>

					&nbsp;
					<span class="inline-block">
						<a href="#" class="btn btn-link"  title="<?= Yii::t('rusporting/website', 'Additional') ?>"
							  onclick="$(this).next().toggleClass('hidden'); return false;"><?= Yii::t('rusporting/website', 'Additional') ?></a>
						<span class="hidden">
							<input class="form-control" style="width: 100px;"  type="text" title="<?= Yii::t('rusporting/website', 'CSS Class') ?>" placeholder="<?= Yii::t('rusporting/website', 'CSS Class') ?>" name="css_class[]"
								   value="<?php echo Html::encode($ar['css_class']);?>"/>

							<input class="form-control" style="width: 100px;"  type="text" title="<?= Yii::t('rusporting/website', 'Element ID') ?>" placeholder="<?= Yii::t('rusporting/website', 'Element ID') ?>" name="element_id[]"
								   value="<?php echo Html::encode($ar['element_id']);?>"/>

							<input class="form-control" style="width: 200px;"  type="text"  title="<?= Yii::t('rusporting/website', 'Active regular expression') ?>"
								   placeholder="<?= Yii::t('rusporting/website', 'Active regular expression') ?>" name="preg[]" value="<?php echo Html::encode($ar['preg']);?>"/>
						</span>
					</span>

					<?php if (count($ar['children'])>0) {
						echo pagesAdminMenu($ar['children'], $level+1);
					} else {
						echo '<ul class="tree-editor"></ul>';
					}?>
				</li>

			<?php } else {?>
				<li class="form-inline">
					<a href="#" onclick="return treeToggle(this);" class="btn btn-link"><i class="fa fa-caret-down"></i></a>

					<input type="hidden" name="level[]" value="1"/>
					<input type="hidden" name="delete[]" value="0"/>

					<input type="hidden" name="id[]" value=""/>


					<input class="form-control" style="width: 150px;" type="text" title="<?= Yii::t('rusporting/website', 'Title') ?>" placeholder="<?= Yii::t('rusporting/website', 'Title') ?>" name="title[]"
						   value=""/>&nbsp;
					<input class="form-control" style="width: 250px;"  type="text" title="<?= Yii::t('rusporting/website', 'Link') ?>" placeholder="<?= Yii::t('rusporting/website', 'Link') ?>" name="link[]" value=""/>
					&nbsp;
					<a href="#" title="<?= Yii::t('rusporting/website', 'Move up') ?>" onclick="return treeUp(this)" class="btn btn-primary"><i class="fa fa-arrow-up"></i></a>
					<a href="#" title="<?= Yii::t('rusporting/website', 'Mode down') ?>" onclick="return treeDown(this)" class="btn btn-primary"><i class="fa fa-arrow-down"></i></a> &nbsp;

					<a href="#" title="<?= Yii::t('rusporting/website', 'Add child') ?>" onclick="return treeAdd(<?php echo @$level+1;?>, this)" class="btn btn-success"><i class="fa fa-plus"></i></a> &nbsp;
					<a href="#" title="<?= Yii::t('rusporting/website', 'Delete') ?>" onclick="return treeDelete(this)" class="btn btn-danger"><i class="fa fa-trash-o"></i></a>

					&nbsp;
					<span class="inline-block">
						<a href="#" class="btn btn-link"  title="<?= Yii::t('rusporting/website', 'Additional') ?>"
						   onclick="$(this).next().toggleClass('hidden'); return false;"><?= Yii::t('rusporting/website', 'Additional') ?></a>
						<span class="hidden">
							<input class="form-control" style="width: 100px;"  type="text" title="<?= Yii::t('rusporting/website', 'CSS Class') ?>" placeholder="<?= Yii::t('rusporting/website', 'CSS Class') ?>" name="css_class[]"
								   value=""/>
							<input class="form-control" style="width: 100px;"  type="text" title="<?= Yii::t('rusporting/website', 'Element ID') ?>" placeholder="<?= Yii::t('rusporting/website', 'Element ID') ?>" name="element_id[]"
								   value=""/>
							<input class="form-control" style="width: 200px;"  type="text"  title="<?= Yii::t('rusporting/website', 'Active regular expression') ?>"
								   placeholder="<?= Yii::t('rusporting/website', 'Active regular expression') ?>" name="preg[]" value=""/>
						</span>
					</span>

					<ul class="tree-editor"></ul>
				</li>
			<?php }?>
		</ul>
	<?php }?>
	<?php echo pagesAdminMenu($items, 1);?>

	<p>
		<input type="submit" name="save" value="<?= Yii::t('rusporting/website', 'Save') ?>" class="btn btn-primary"/> &nbsp;
		<a href="#" onclick="return treeAdd(1, this);" class="btn btn-success"><i class="fa fa-plus"></i></a>
	</p>

	<?php echo Html::endForm();	?>
</div>