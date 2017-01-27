<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.tooltip');
?>

<?php if (isset($this->error)) : ?>
	<?php echo $this->error; ?>
<?php endif; ?>

<form class="uk-form uk-form-horizontal uk-panel uk-panel-box" action="<?php echo JRoute::_('index.php'); ?>" method="post" id="contact-form">
	<fieldset>
		<legend><?php echo JText::_('COM_CONTACT_FORM_LABEL'); ?></legend>
		
		<div class="uk-form-row">
			<span class="uk-form-label"><?php echo $this->form->getLabel('contact_name'); ?></span>
			<div class="uk-form-controls"><?php echo $this->form->getInput('contact_name'); ?></div>
		</div>
		
		<div class="uk-form-row">
			<span class="uk-form-label"><?php echo $this->form->getLabel('contact_email'); ?></span>
			<div class="uk-form-controls"><?php echo $this->form->getInput('contact_email'); ?></div>
		</div>
		
		<div class="uk-form-row">
			<span class="uk-form-label"><?php echo $this->form->getLabel('contact_subject'); ?></span>
			<div class="uk-form-controls"><?php echo $this->form->getInput('contact_subject'); ?></div>
		</div>
		
		<div class="uk-form-row">
			<span class="uk-form-label"><?php echo $this->form->getLabel('contact_message'); ?></span>
			<div class="uk-form-controls"><?php echo $this->form->getInput('contact_message'); ?></div>
		</div>

		<?php if ($this->params->get('show_email_copy')): ?>
		<div class="uk-form-row">
			<?php echo $this->form->getLabel('contact_email_copy'); ?>
			<?php echo $this->form->getInput('contact_email_copy'); ?>
		</div>
		<?php endif ?>

		<?php //Dynamically load any additional fields from plugins. ?>
		<?php foreach ($this->form->getFieldsets() as $fieldset): ?>
		<?php if ($fieldset->name != 'contact'):?>
			<?php $fields = $this->form->getFieldset($fieldset->name);?>
			<?php foreach($fields as $field): ?>
				<?php if ($field->hidden): ?>
					<?php echo $field->input;?>
				<?php else : ?>
					<div>
						<?php echo $field->label; ?>
						<?php if (!$field->required && $field->type != "Spacer"): ?>
							<p><?php echo JText::_('COM_CONTACT_OPTIONAL');?></p>
						<?php endif; ?>
						<?php echo $field->input;?>
					</div>
				<?php endif;?>
			<?php endforeach;?>
		<?php endif ?>
		<?php endforeach;?>

	</fieldset>

	<button class="uk-button uk-button-primary" type="submit"><?php echo JText::_('COM_CONTACT_CONTACT_SEND'); ?></button>
	
	<input type="hidden" name="option" value="com_contact" />
	<input type="hidden" name="task" value="contact.submit" />
	<input type="hidden" name="return" value="<?php echo $this->return_page;?>" />
	<input type="hidden" name="id" value="<?php echo $this->contact->slug; ?>" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>

