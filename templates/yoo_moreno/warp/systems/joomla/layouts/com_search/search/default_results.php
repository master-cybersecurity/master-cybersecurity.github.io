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

<div class="uk-margin">

	<?php foreach ($this->results as $result) : ?>
	<article class="uk-article">
		
		<?php if ( $result->href ) : ?>
		<h1 class="uk-article-title"><a href="<?php echo JRoute::_($result->href); ?>" <?php if ($result->browsernav == 1 ) echo 'target="_blank"'; ?>><?php  echo $this->escape($result->title); ?></a></h1>
		<?php else : ?>
		<h1 class="uk-h3"><?php echo $this->escape($result->title); ?></h1>
		<?php endif; ?>

		<?php if ($result->section && $this->params->get('show_date')) : ?>
		<p class="uk-article-meta">
			<?php if ($this->params->get('show_date')) echo JText::sprintf('JGLOBAL_CREATED_DATE_ON', $result->created).'. '; ?>
			<?php if ($result->section) echo JText::_('TPL_WARP_POSTED_IN').' '.$this->escape($result->section); ?>
		</p>
		<?php endif; ?>
		
		<?php echo $result->text; ?>

	</article>
	<?php endforeach; ?>

</div>

<?php echo $this->pagination->getPagesLinks();