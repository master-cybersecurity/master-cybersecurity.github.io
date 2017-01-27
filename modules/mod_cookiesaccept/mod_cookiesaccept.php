<?php
/*------------------------------------------------------------------------
# mod_cookiesaccept
# ------------------------------------------------------------------------
# author    Le Cinquième Crayon
# copyright Copyright (C) 2013 Le Cinquième Crayon. All Rights Reserved.
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: http://www.cinquiemecrayon.eu
-------------------------------------------------------------------------*/

defined( '_JEXEC' ) or die( 'Restricted access' ); 

$document = JFactory::getDocument();
$document->addStyleSheet('modules/mod_cookiesaccept/screen.css');

if($params->get('jquery'))
    $document->addScript("http://code.jquery.com/jquery-latest.min.js");
$color = $params->get('color');
$position = $params->get('position');
$enable_info = $params->get('enable_info');
$article_info = $params->get('article_info');
$more_info = FALSE;
$accept = FALSE;
$lang =& JFactory::getLanguage();
$other_lang = $params->get('lang_code');
$current_lang = $lang->getTag();
//$current_lang = 'no-NO';
$ca_banner = $params->get('ca_banner');
$ca_banner_h2 = $params->get('ca_banner_h2');
$ca_banner_p = $params->get('ca_banner_p');
$ca_banner_p_span = $params->get('ca_banner_p_span'); 
$ca_banner_div_accept = $params->get('ca_banner_div_accept');
$ca_banner_div_accept_hover = $params->get('ca_banner_div_accept_hover');
if($ca_banner_div_accept_hover)
    echo "<style>#ca_banner div.accept:hover {".$ca_banner_div_accept_hover."}</style>";
$ca_info = $params->get('ca_info');
$ca_info_close = $params->get('ca_info_close');
$ca_info_plus = $params->get('ca_info_plus');


if (!isset($_COOKIE['cookieaccept'])) :

   switch ($current_lang) {
        case 'fr-FR':
            $info1 = $params->get('info1_fr-FR');
            $info2 = $params->get('info2_fr-FR');
            $text = $params->get('text_fr-FR');
			$link = $params->get('article_info_link_fr-FR');
            break;    
        case 'pl-PL':
            $info1 = $params->get('info1_pl-PL');
            $info2 = $params->get('info2_pl-PL');
            $text = $params->get('text_pl-PL');
			$link = $params->get('article_info_link_pl-PL');
            break;
		case 'it-IT':
            $info1 = $params->get('info1_it-IT');
            $info2 = $params->get('info2_it-IT');
            $text = $params->get('text_it-IT');
			$link = $params->get('article_info_link_it-IT');
            break;
		case 'es-ES':
            $info1 = $params->get('info1_es-ES');
            $info2 = $params->get('info2_es-ES');
            $text = $params->get('text_es-ES');
			$link = $params->get('article_info_link_es-ES');
            break;
        case $other_lang:
            $info1 = $params->get('info1_xx-XX');
            $info2 = $params->get('info2_xx-XX');
            $text = $params->get('text_xx-XX');
            $more_info = $params->get('more_info');
            $accept =  $params->get('accept');
			$link = $params->get('article_info_link_xx-XX');
            break;
        default:
            $info1 = $params->get('info1_en-GB');
            $info2 = $params->get('info2_en-GB');
            $text = $params->get('text_en-GB');
			$link = $params->get('article_info_link_en-GB');
            break;
    }

?>
<!--googleoff: all-->
<div id="ca_banner" 
    style="<?php echo $position; ?>:0px;
    <?php if($color=="black" && $ca_banner=="") echo "background:url('".JURI::base()."modules/mod_cookiesaccept/img/przez_b.png');border-color:#000;color:#fff;" ?>
    <?php echo $ca_banner; ?>
    ">
    <h2 style="
	<?php if($color=="black" && $ca_banner_h2=="") echo "color:#fff;" ?>
	<?php echo $ca_banner_h2; ?>"><?php echo $info1; ?></h2> 
    <p style="
		<?php if($color=="black" && $ca_banner_p=="") echo "color:#fff;" ?>
		<?php echo $ca_banner_p; ?>"><?php echo $info2; ?>
        <?php if($enable_info) : ?>
			<?php if($article_info) : ?>
				<span class="infoplus" style="<?php echo $ca_banner_p_span; ?>"><a href="<?php echo $link; ?>"><?php if(!$more_info) { echo JText::_('MOD_COOKIESACCEPT_PLUS_INFO'); } else { echo $more_info; }; ?></a></span>
			<?php else: ?>
				<span class="infoplus info_modal" style="<?php echo $ca_banner_p_span; ?>"><?php if(!$more_info) { echo JText::_('MOD_COOKIESACCEPT_PLUS_INFO'); } else { echo $more_info; }; ?></span>
			<?php endif; ?>
		<?php endif; ?>
        </p>
    <div class="accept" style="<?php echo $ca_banner_div_accept; ?>"><?php if(!$accept) { echo JText::_('MOD_COOKIESACCEPT_ACCEPT'); } else { echo $accept; }; ?></div>
</div>
<?php if($enable_info && !$article_info) : ?>
<div id="ca_info" style="<?php echo $ca_info; ?>">
    <div class="ca_info_close" style="<?php echo $ca_info_close; ?>"></div>
        <div id="ca_info_plus" style="<?php echo $ca_info_plus; ?>">
            <?php echo $text; ?>
        </div>
</div>
<?php endif; ?>

<?php if(isset($_POST['set_cookie'])):
	if($_POST['set_cookie']==1)
		setcookie("cookieaccept", "yes", time()+3600*24*365, "/");
?>

<?php endif; ?>

<script type="text/javascript">
    jQuery(document).ready(function () { 
	
	function setCookie(c_name,value,exdays)
	{
		var exdate=new Date();
		exdate.setDate(exdate.getDate() + exdays);
		var c_value=escape(value) + ((exdays==null) ? "" : "; expires="+exdate.toUTCString()) + "; path=/";
		document.cookie=c_name + "=" + c_value;
	}
	
	function readCookie(name) {
		var nameEQ = name + "=";
		var ca = document.cookie.split(';');
		for(var i=0;i < ca.length;i++) {
			var c = ca[i];
			while (c.charAt(0)==' ') c = c.substring(1,c.length);
			if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
			}
		return null;
	}
    
	var $ca_banner = jQuery('#ca_banner');
    var $ca_infoplus = jQuery('.infoplus.info_modal');
    var $ca_info = jQuery('#ca_info');
    var $ca_info_close = jQuery('.ca_info_close');
    var $ca_infoaccept = jQuery('.accept');
    
	var cookieaccept = readCookie('cookieaccept');
	if(!(cookieaccept == "yes")){
	
		$ca_banner.delay(1000).slideDown('fast'); 
        $ca_infoplus.click(function(){
            $ca_info.fadeIn("fast");
        });
        $ca_info_close.click(function(){
            $ca_info.fadeOut("slow");
        });
        $ca_infoaccept.click(function(){
			setCookie("cookieaccept","yes",365);
            jQuery.post('<?php echo JURI::current(); ?>', 'set_cookie=1', function(){});
            $ca_banner.slideUp('slow');
            $ca_info.fadeOut("slow");
        });
       } 
    });
</script>
<!--googleon: all-->
<?php endif ?>