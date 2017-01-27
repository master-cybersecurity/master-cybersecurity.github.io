<?php
/**
 * @version		$Id: categories.php 1034 2011-10-04 17:00:00Z joomlaworks $
 * @package		K2
 * @author		JoomlaWorks http://www.joomlaworks.gr
 * @copyright	Copyright (c) 2006 - 2011 JoomlaWorks Ltd. All rights reserved.
 * @license		GNU/GPL license: http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript" src="<?php echo JURI::root();?>modules/mod_vtem_drilldown_menu/asset/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo JURI::root();?>modules/mod_vtem_drilldown_menu/asset/jquery.odrilldown.js"></script>
<script type="text/javascript">
var vtemdrilldown = jQuery.noConflict();
vtemdrilldown(document).ready(function(){
	vtemdrilldown('#<?php echo $slideID;?> > ul').oDrilldown({
		width           : '<?php echo $width;?>',
		speed       	: <?php echo $showSpeed;?>,
		saveState		: <?php echo $saveState;?>,
		showCount		: <?php echo $showCount;?>,
		linkType		: '<?php echo $linkType;?>',
		resetText		: '<?php echo $topLinkText;?>',
		defaultText		: '<?php echo $defaultText;?>',
		backlinkText	: '<?php echo $backLinkText;?>',
		stick           : <?php echo $showStick;?>,
		stickText       : '<?php echo $stickLabel;?>'
	});
});
</script>
<div class="vtemdrildown_wrapper clearfix vt_drilldown_menu<?php echo $params->get('moduleclass_sfx').' modstyle'.$modstyle.' vtem-drill-style'.$vtemstyle.' wrap-'.$slideID; ?>">
	<?php echo '<div id="'.$slideID.'">'.$output.'</div>'; ?>
</div>
