<?php
/*------------------------------------------------------------------------
# mod_drilldown_menu - VTEM DrillDown Module
# ------------------------------------------------------------------------
# author Nguyen Van Tuyen
# copyright Copyright (C) 2010 VTEM.NET. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.vtem.net
# Technical Support: Forum - http://vtem.net/en/forum.html
-------------------------------------------------------------------------*/
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<script type="text/javascript" src="<?php echo JURI::root();?>modules/mod_vtem_drilldown_menu/asset/jquery.cookie.js"></script>
<script type="text/javascript" src="<?php echo JURI::root();?>modules/mod_vtem_drilldown_menu/asset/jquery.odrilldown.js"></script>
<script type="text/javascript">
var vtemdrilldown = jQuery.noConflict();
vtemdrilldown(document).ready(function(){
	vtemdrilldown('#<?php echo $slideID;?>').oDrilldown({
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
    <ul id="<?php echo $slideID;?>" class="vt_drilldownmenu" <?php $tag = ''; if ($params->get('tag_id')!=NULL) echo 'id="'.$params->get('tag_id').'"';?>>
    <?php
        foreach ($list as $i => &$item) :
            $class = '';
            if ($item->id == $active_id) $class .= 'current ';      
            if (in_array($item->id, $path)) $class .= 'active ';       
            if ($item->deeper) $class .= 'vtemparent'.$item->level.' ';       
            if (!empty($class)) $class = ' class="'.trim($class) .'"';
      
            echo '<li id="item-'.$item->id.'" '.$class.'>';
        
            // Render the menu item.
            switch ($item->type) :
                case 'separator':
                case 'url':
                case 'component':
                    require JModuleHelper::getLayoutPath('mod_vtem_drilldown_menu', 'default_'.$item->type);
                    break;
        
                default:
                    require JModuleHelper::getLayoutPath('mod_vtem_drilldown_menu', 'default_url');
                    break;
            endswitch;
        
            // The next item is deeper.
            if ($item->deeper)
                echo '<ul class="vtemlevel'.$item->level.'">';
            // The next item is shallower.
            else if ($item->shallower) 
                echo '</li>'.str_repeat('</ul></li>', $item->level_diff);
            // The next item is on the same level.
            else 
                echo '</li>';
        endforeach;
    ?>
    </ul>
</div>