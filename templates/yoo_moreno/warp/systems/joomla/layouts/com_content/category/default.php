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

<?php if ($this->params->get('page_subheading')) : ?>
<h2><?php echo $this->escape($this->params->get('page_subheading')); ?></h2>
<?php endif; ?>

<?php if ($this->params->get('show_category_title', 1)) : ?>
<h1 class="uk-h3"><?php echo $this->category->title;?></h1>
<?php endif; ?>

<?php if ($this->params->get('show_description', 1) || $this->params->def('show_description_image', 1)) :?>
<div class="uk-clearfix uk-margin">
	<?php if ($this->params->get('show_description_image') && $this->category->getParams()->get('image')) : ?>
		<img src="<?php echo $this->category->getParams()->get('image'); ?>" alt="<?php echo $this->category->getParams()->get('image'); ?>" class="size-auto align-right" />
	<?php endif; ?>
	<?php if ($this->params->get('show_description') && $this->category->description) echo JHtml::_('content.prepare', $this->category->description, '', 'com_content.category'); ?>
</div>
<?php endif; ?>

<?php echo $this->loadTemplate('articles'); ?>