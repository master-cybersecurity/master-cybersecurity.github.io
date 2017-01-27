<?php
/**
* @package   Warp Theme Framework
* @author    YOOtheme http://www.yootheme.com
* @copyright Copyright (C) YOOtheme GmbH
* @license   http://www.gnu.org/licenses/gpl.html GNU/GPL
*/

// no direct access
defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_COMPONENT.'/helpers');

?>
	
<?php if ($this->params->get('show_page_heading')) : ?>
<h1 class="tm-title"><?php echo $this->escape($this->params->get('page_heading')); ?></h1>
<?php endif; ?>

<?php if ($this->params->get('show_base_description') && ($this->params->get('categories_description') || $this->parent->description)) : ?>
<div class="uk-clearfix uk-margin">
	<?php
		if ($this->params->get('categories_description')) {
			echo JHtml::_('content.prepare', $this->params->get('categories_description'), '', 'com_contact.categories');
		} elseif ($this->parent->description) {
			echo JHtml::_('content.prepare', $this->parent->description, '', 'com_contact.categories');
		}
	?>
</div>
<?php endif; ?>
	
<?php echo $this->loadTemplate('items'); ?>