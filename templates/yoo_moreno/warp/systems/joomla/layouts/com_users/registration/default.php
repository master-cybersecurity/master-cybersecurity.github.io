<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::_('behavior.mootools');
JHtml::_('behavior.keepalive');
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
?>
	
<?php if ($this->params->get('show_page_heading')) : ?>
<h1 class="tm-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>

<form class="uk-form uk-form-horizontal" action="<?php echo JRoute::_('index.php?option=com_users&task=registration.register'); ?>" method="post" enctype="multipart/form-data">
	<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
		<?php $fields = $this->form->getFieldset($fieldset->name); ?>
		<?php if (count($fields)): ?>
			<fieldset>
				<?php foreach ($fields as $field): ?>
					<?php if ($field->hidden): ?>
						<?php echo $field->input; ?>
					<?php else: ?>
						<div class="uk-form-row">
							<span class="uk-form-label"><?php echo $field->label; ?></span>
							<div class="uk-form-controls">
								<?php echo ($field->type!='Spacer') ? $field->input : "&#160;"; ?>
								<?php if (!$field->required && $field->type != 'Spacer'): ?>
									<span class="uk-text-muted"><?php echo JText::_('COM_USERS_OPTIONAL'); ?></span>
								<?php endif; ?>
							</div>
						</div>
				<?php endif; ?>
				<?php endforeach; ?>
			</fieldset>
		<?php endif; ?>
	<?php endforeach; ?>

	<button class="uk-button uk-button-primary" type="submit"><?php echo JText::_('JREGISTER'); ?></button>
	
	<input type="hidden" name="option" value="com_users" />
	<input type="hidden" name="task" value="registration.register" />
	<?php echo JHtml::_('form.token');?>
	
</form>