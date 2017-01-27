<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;
?>
	
<?php if ($this->params->get('show_page_heading')) : ?>
<h1 class="tm-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>

<?php if (($this->params->get('logoutdescription_show') == 1 && str_replace(' ', '', $this->params->get('logout_description')) != '')|| $this->params->get('logout_image') != '') : ?>
<div class="clearfix">
	<?php if ($this->params->get('logout_image')) : ?>
		<img src="<?php $this->escape($this->params->get('logout_image')); ?>" alt="<?php echo JText::_('COM_USER_LOGOUT_IMAGE_ALT')?>" class="size-auto" />
	<?php endif; ?>
	<?php if ($this->params->get('logoutdescription_show')) echo $this->params->get('logout_description'); ?>
</div>
<?php endif; ?>

<form class="uk-form uk-panel uk-panel-box" action="<?php echo JRoute::_('index.php?option=com_users&task=user.logout'); ?>" method="post">
	<button class="uk-button uk-button-primary" type="submit" class="button"><?php echo JText::_('JLOGOUT'); ?></button>
	<input type="hidden" name="return" value="<?php echo base64_encode($this->params->get('logout_redirect_url', $this->form->getValue('return'))); ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>