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