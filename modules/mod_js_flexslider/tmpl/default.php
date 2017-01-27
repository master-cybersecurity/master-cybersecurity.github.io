<?php

/**
* @copyright	Copyright (C) 2012 JoomSpirit. All rights reserved.
* Slideshow based on the JQuery script Flexslider
* @license		GNU General Public License version 2 or later
*/

defined('_JEXEC') or die;

?>

<script src="modules/mod_js_flexslider/assets/js/jquery.flexslider-min.js" type="text/javascript"></script>

<div id="flexslider-<?php echo $module_id; ?>" class="flexslider <?php if ( $slide_theme == 'false' ) : ?> <?php echo $theme_shadow ; ?> theme-border-<?php echo $theme_border ; ?> theme-border-radius-<?php echo $theme_border_radius ; ?> <?php else : ?>flex-slide-theme-<?php echo $direction; ?><?php endif ; ?> <?php echo $directionNav ; ?> <?php if ($controlNav == 'true') : ?>position-nav-<?php echo $positionNav ; ?><?php endif ; ?> bg-caption-<?php echo $bg_caption ; ?>-<?php echo $transparency_caption ; ?> position-caption-<?php echo $position_caption ; ?>-<?php echo $text_align ; ?> color-nav-<?php echo $colorNav ; ?> color-nav-active-<?php echo $colorNavActive ; ?>" style="background-color : <?php echo $bg_color ; ?>;" >
 
  <ul class="slides" <?php if ( $enable_minheight == 'yes' ) : ?>style="min-height:<?php echo $min_height ; ?>px;"<?php endif ; ?>>
  	<?php
  		foreach($listofimages as $item){
  			echo $item; 
  		}
  	?> 
  </ul>
  
  <?php if ( $slide_theme == 'true' ) : ?>
  <span class="slide-theme">
  	<span class="slide-theme-side slide-top-left"></span>
  	<span class="slide-theme-side slide-top-right"></span>
  	<span class="slide-theme-side slide-bottom-left"></span>
  	<span class="slide-theme-side slide-bottom-right"></span>
  </span>
  <?php endif; ?>
  
</div>

<script type="text/javascript">
  jQuery(window).load(function() {
    jQuery('#flexslider-<?php echo $module_id; ?>').flexslider({
        animation: "<?php echo $transition; ?>",
        easing:"<?php echo $easing; ?>",
 		direction: "<?php echo $direction; ?>",        //String: Select the sliding direction, "horizontal" or "vertical"
		slideshowSpeed: <?php echo $pauseTime; ?>, 			// How long each slide will show
		animationSpeed: <?php echo $animSpeed; ?>, 			// Slide transition speed
    	directionNav: <?php if ($directionNav == 'false') { echo "false" ;} else { echo "true" ;} ?>,             
    	controlNav: <?php echo $controlNav ; ?>,    
    	pauseOnHover: <?php echo $pauseOnHover; ?>,
    	initDelay: <?php echo $initDelay; ?>,
    	randomize: <?php echo $randomize; ?>,
    	smoothHeight: false,
    	touch: false,
    	keyboardNav: true
    	
    });
  });
</script>