<?php
/**
* @Copyright Copyright (C) 2010 VTEM . All rights reserved.
* @license GNU/GPL http://www.gnu.org/copyleft/gpl.html
* @link     	http://www.vtem.net
**/
// no direct access
defined('_JEXEC') or die('Restricted access');
?>
<ul id="<?php echo $module_id;?>" class="vt-fancy-menu vt-fancy<?php echo $params->get('moduleclass_sfx'); ?>">
<?php
		if($headertext != '') echo '<h2 class="metro-main-header">'.$headertext.'</h2>';
        foreach ($items as $i => &$item) {
            if (isset($item->content) AND $item->content)
                echo '<li class="'.$item->megaGroup.' '.$item->classe.' level'.$item->level.' mega-group">'.$item->content;
            if ($item->ftitle != "") {
                echo '<li class="'.$item->classe.' level'.$item->level.'">';
                switch ($item->type) :
                    default:
                        echo '<a href="'.$item->flink.'">'.$item->ftitle.'</a>';
                        break;
                    case 'separator':
                        echo '<span class="separator ">'.$item->ftitle.'</span>';
                        break;
                    case 'url':
                    case 'component':
                        switch ($item->browserNav) :
                            default:
                            case 0:
                                echo '<a href="'.$item->flink.'">'.$item->ftitle.'</a>';
                                break;
                            case 1:
                                echo '<a href="'.$item->flink.'" target="_blank" >'.$item->ftitle.'</a>';// _blank
                                break;
                            case 2:
                                echo '<a href="' . $item->flink . '&tmpl=component" onclick="window.open(this.href,\'targetWindow\',\'$attribs\');return false;">'.$item->ftitle.'</a>';
                                break;
                        endswitch;
                        break;
                endswitch;
            }
            if ($item->deeper)
                echo "<ul class='vt_menu_sub'>";
            elseif ($item->shallower) // The next item is shallower.
                echo "</li>".str_repeat("</ul></li>", $item->level_diff);
            elseif ($item->is_end) // the item is the last.
                echo str_repeat("</li></ul>", $item->level_diff)."</li>";
            else 
				echo "</li>";
        }
?>
</ul>
<script type="text/javascript" src="<?php echo JURI::base();?>modules/mod_vtem_fancymenu/styles/jquery.mCustomScrollbar.min.js"></script>
<script type="text/javascript" src="<?php echo JURI::base();?>modules/mod_vtem_fancymenu/styles/jquery.metromenu.js"></script>
<script type="text/javascript">
	var vtemmenu = jQuery.noConflict();
	jQuery(document).ready(function(){
		jQuery('#<?php echo $module_id;?>').metroMenu({
			width: '<?php echo $modwidth;?>',
			position: '<?php echo $modposition;?>',
			mouseEvent: '<?php echo $mouseEvent;?>', // 'click', 'hover'
			speed: <?php echo $duration;?>,
			effect: '<?php echo $transition;?>', // 'fade', 'blind', 'slide', 'fold', 'bounce'
			colsWidth: '<?php echo $sub_width;?>',
			theme: 'dark-thick',
			easing: 'swing',
			stick: true,
			fixedMode: true
		});
	});
</script>