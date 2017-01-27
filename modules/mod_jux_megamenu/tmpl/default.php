<?php
/**
 * @version		$Id$
 * @author		JoomlaUX
 * @package		Site
 * @subpackage	mod_jux_megamenu
 * @copyright	Copyright (C) 2008 - 2013 by JoomlaUX Solutions. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL version 3
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

// Add some style, we must add it here because it depends on the 'layout' chosen by user.
$style = '#js-mainnav.' . $layout . ' ul.level1 .childcontent { margin: -20px 0 0 ' . ($params->get('mega-colwidth',200) - 30) . 'px; }';
if($params->get('css3_noJS', 0) && $params->get('responsive_toggle_button', 1)) {
	$style .= '@media screen and (max-width: 767px) {#js-mainnav.megamenu.noJS ul.megamenu li { display: none; }}';
}
JFactory::getDocument()->addStyleDeclaration($style);
?>
<div id="js-mainnav" class="clearfix <?php echo $menuStyle; ?>">
	<?php if($params->get('responsive_toggle_button', 1)) :
		$toggle_type = !$params->get('css3_noJS', 0) ? 'js' : 'css3';
	?>
	<div id="<?php echo $toggle_type; ?>-megaMenuToggle" class="megaMenuToggle">
		<?php echo JText::_('MOD_JUX_MEGAMENU_TOGGLE_MENU'); ?>
		<span class="megaMenuToggle-icon"></span>
	</div>
	<?php endif; ?>
	<?php $dropdownmenu->genMenu (0, -1); ?>
</div>

<!--<style type="text/css">
	<?php //echo '#js-mainnav.'.$layout; ?> ul.level1 .childcontent { margin: -20px 0 0 <?php //echo $params->get('mega-colwidth',200) - 30 ;?>px; }
</style>-->

<?php

if (!$params->get('css3_noJS', 0)) {
	$stickyAlignment = $params->get('sticky_alignment', 'left');
	if($stickyAlignment == 'sameasmenu') {
		$stickyAlignment = $menuAlignment;
	}
	
	if($menuOrientation == 'horizontal') {
		$direction	= $params->get('horizontal_submenu_direction', 'down');
	} else {
		$direction	= $params->get('vertical_submenu_direction', 'lefttoright');
	}

	// PHP 5.3
	// Disable slide & fade effect for IE8 or above
	/*
	preg_match('/MSIE ([0-9]\.[0-9])/',$_SERVER['HTTP_USER_AGENT'],$reg);
	if(isset($reg[1])) {
		if ($reg[1] <= 8) {
			$animation = 'none';
		}
	}
	*/

	?>
	<script type="text/javascript">
		var megamenu = new jsMegaMenuMoo ('<?php echo $params->get('special_id') ?>', {
			'bgopacity': 0,
			'animation': '<?php echo $params->get('js_menu_mega_animation', 'slide'); ?>',
			'delayHide': <?php echo $params->get('js_menu_mega_delayhide', 300); ?>,
			'menutype': '<?php echo $menuOrientation;?>',
			'direction':'<?php echo $direction;?>',
			'action':'<?php echo $params->get('js_menu_mouse_action', 'mouseenter');?>',
			'tips': false,
			'duration': <?php echo $params->get('js_menu_mega_duration', 300); ?>,
			'hover_delay': <?php echo $params->get('js_menu_mouse_hover_delay', 0); ?>,
			'sticky': <?php echo $params->get('sticky_menu', 0); ?>,
			'menu_alignment': '<?php echo $menuAlignment; ?>',
			'sticky_alignment': '<?php echo $stickyAlignment; ?>'
		});
	</script>
	<?php
}