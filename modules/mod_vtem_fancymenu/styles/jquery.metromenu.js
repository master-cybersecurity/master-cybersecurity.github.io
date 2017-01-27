/**
* hoverIntent r5 // 2007.03.27 // jQuery 1.1.2+
* <http://cherne.net/brian/resources/jquery.hoverIntent.html>
* 
* @param  f  onMouseOver function || An object with configuration options
* @param  g  onMouseOut function  || Nothing (use configuration options object)
* @author    Brian Cherne <brian@cherne.net>
*/
(function($){"use strict";$.fn.hoverIntent=function(f,g){var cfg={sensitivity:7,interval:100,timeout:0};cfg=$.extend(cfg,g?{over:f,out:g}:f);var cX,cY,pX,pY;var track=function(ev){cX=ev.pageX;cY=ev.pageY;};var compare=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);if((Math.abs(pX-cX)+Math.abs(pY-cY))<cfg.sensitivity){$(ob).unbind("mousemove",track);ob.hoverIntent_s=1;return cfg.over.apply(ob,[ev]);}else{pX=cX;pY=cY;ob.hoverIntent_t=setTimeout(function(){compare(ev,ob);},cfg.interval);}};var delay=function(ev,ob){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);ob.hoverIntent_s=0;return cfg.out.apply(ob,[ev]);};var handleHover=function(e){var p=(e.type=="mouseover"?e.fromElement:e.toElement)||e.relatedTarget;while(p&&p!=this){try{p=p.parentNode;}catch(e){p=this;}}if(p==this){return false;}var ev=jQuery.extend({},e);var ob=this;if(ob.hoverIntent_t){ob.hoverIntent_t=clearTimeout(ob.hoverIntent_t);}if(e.type=="mouseover"){pX=ev.pageX;pY=ev.pageY;$(ob).bind("mousemove",track);if(ob.hoverIntent_s!=1){ob.hoverIntent_t=setTimeout(function(){compare(ev,ob);},cfg.interval);}}else{$(ob).unbind("mousemove",track);if(ob.hoverIntent_s==1){ob.hoverIntent_t=setTimeout(function(){delay(ev,ob);},cfg.timeout);}}};return this.mouseover(handleHover).mouseout(handleHover);};})(jQuery);
/*
 * metroMenu - jQuery metro menu
 * Copyright (c) 2011 OpenAddon.com/vtem.net
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 */
;(function($){
	"use strict";
	$.fn.metroMenu = function(options){ //define the defaults for the plugin and how to call it	
		var defaults = { //set default options  
		    width: '180px',
			position: 'left', //left, right
			mouseEvent: 'click', // 'click', 'hover'
			speed: 400,
			effect: 'slide', // 'fade', 'slide'
			colsWidth: '220px',
			theme: 'dark-thick',
			easing: 'swing',
			stick: true,
			fixedMode: true,
			onLoad : function(){},
            beforeOpen : function(){},
			beforeClose: function(){}
		};
		var options = $.extend(defaults, options); //call in the default otions	
		return this.each(function(){ //The element that is passed into the design  
			var obj = $(this),
				opts = options,  
				classParent = 'metro-menu',
				classSubContainer = 'sub-container',
				classSubGroup = 'metro-group',
				classSubMenu = 'sub-menu',
				classHover = 'metro-hover',
				videoTag = 'iframe, video, audio',
				metroFixed = '';
			if(opts.fixedMode){
				metroFixed ='metro-menu-fixed';
				$('body').append(obj);
			}
			obj.width(opts.width).addClass('main-'+classParent+' pos'+opts.position).wrap('<div class="wrap-pos'+opts.position+' '+(opts.theme)+' '+metroFixed+' '+classParent+'-wrapper clearfix" />').children().show();
			if(opts.stick){
				obj.before('<span class="menu-stick">&equiv;</span>').addClass('metro-menu-stick');
				$(document).click(function(e){
					if(obj.css('display') == 'none' && $(e.target).is('.menu-stick')){
						obj.show().parent().removeClass('oMenuStickClose').addClass('oMenuStickOpen');					
					}else{
						if(!$(e.target).is('li, a', obj))
							obj.hide().parent().removeClass('oMenuStickOpen').addClass('oMenuStickClose');
					}
				});
			}
			metroSetup();
			function menuOpen(self){
				if(opts.mouseEvent == 'hover') var self = $(this);
				var subNav = $('> .'+classSubContainer, self);
				self.addClass(classHover);
				subNav.addClass('metroOpen').removeClass('metroClose').find(videoTag).show();
				switch(opts.effect){
					default:
					case 'fade':
						subNav.width(opts.colsWidth).css(opts.position,obj.width()).children().css('margin-'+opts.position,0).animate({'opacity': 1}, opts.speed, opts.easing);
						break;
					case 'slide':
						subNav.width(opts.colsWidth).css(opts.position,obj.width()).children().animate(opts.position == 'left' ? {'margin-left':0} : {'margin-right':0}, opts.speed, opts.easing);
						break;
				}
				opts.beforeOpen.call(this); // beforeOpen callback;
			}
			
			function menuClose(self){
				if(opts.mouseEvent == 'hover') var self = $(this);
				var subNav = $('> .'+classSubContainer, self);
				var videosrc = $(videoTag, subNav).attr('src');
				subNav.addClass('metroClose').removeClass('metroOpen').find(videoTag).removeAttr('src').hide().attr('src',videosrc);
				switch(opts.effect){
					default:
					case 'fade':
						subNav.width(opts.colsWidth).css(opts.position,-parseInt(opts.colsWidth)).children().css('margin-'+opts.position, 0).animate({'opacity': 0}, opts.speed /2);
						break;
					case 'slide':
						subNav.width(0).css(opts.position,obj.width()).children().animate(opts.position == 'left' ? {'margin-left':'-'+opts.colsWidth} : {'margin-right':'-'+opts.colsWidth}, opts.speed/2);
						break;
				}
				self.removeClass(classHover);
				opts.beforeClose.call(this); // beforeClose callback;
			}

			function metroSetup(){
				var arrow = '<span class="metro-menu-icon">&nbsp;</span>';
				var subWrap = '<div class="'+classSubContainer+'"><div class="'+classSubContainer+'-wrap"><div class="'+classSubContainer+'-inner"></div></div></div>';
				$('> li', obj).each(function(){ //Set Width of sub
					var $mainSub = $('> ul,> .'+classSubMenu, this);
					var $primaryLink = $('> a', this);
					if($mainSub.length){
						$primaryLink.addClass(classParent).append(arrow);
						$mainSub.addClass(classSubMenu+' clearfix').wrap(subWrap);
						$primaryLink.next('.'+classSubContainer).children().width(opts.colsWidth).prepend('<h2 class="metro-header">'+$primaryLink.text()+'</h2>');				
					}
				});
		
				if(opts.mouseEvent == 'hover'){
					$('li', obj).hoverIntent({
						sensitivity: 2,
						interval: 20,
						over: menuOpen,
						timeout: 100,
						out: menuClose
					}); 
				}else if(opts.mouseEvent == 'click'){
					$(document).mouseup(function(e){
						if((!$(e.target, obj).parent().hasClass(classHover) && $(e.target, obj).is('li > a')) || $(e.target).is('.overlay'+metroFixed) || $(e.target).is('a.'+classParent+' > img, a.'+classParent+' > span')){
							menuClose($('li.'+classHover, obj));
							$('li.'+classHover, obj).removeClass(classHover);
							$('body').find('.overlay'+metroFixed).remove();
						}
					});
					$('a.'+classParent, obj).click(function(e){
						e.preventDefault();
						if(($(this).next('.'+classSubContainer).width() == parseFloat(opts.colsWidth)) && (parseInt($(this).next('.'+classSubContainer).css(opts.position)) == parseFloat(obj.width()))){
							$(this).parent().removeClass(classHover);
							menuClose($(this).parent());
							$('body').find('.overlay'+metroFixed).remove();
						}else{
							$(this).parent().addClass(classHover);
							menuOpen($(this).parent());
							$('body').append('<div class="overlay'+metroFixed+'" />');
						}
					});
				}
				$('.'+classSubContainer+'-inner', obj).mCustomScrollbar({
					theme: opts.theme,
					scrollButtons:{	enable:true	}
				});
				opts.onLoad.call(this); // onLoad callback;
			}
		});
	};
})(jQuery);