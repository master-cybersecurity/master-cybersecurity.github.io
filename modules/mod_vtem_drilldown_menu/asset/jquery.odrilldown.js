/*
 * oDrilldown - jQuery drill down ipod menu
 * Copyright (c) 2012 OpenAddon.com/vtem.net
 * Requires: jQuery v1.4.3 or later
 *
 * Dual licensed under the MIT and GPL licenses:
 * 	http://www.opensource.org/licenses/mit-license.php
 * 	http://www.gnu.org/licenses/gpl.html
 *
 */

(function($){
'use strict';
	$.fn.oDrilldown = function(options) { //set default options
		var defaults = {
			width           : '100%',
			classWrapper	: 'oDrill-wrapper',
			classMenu		: 'oDrill-menu',
			classParent		: 'oDrill-parent',
			classParentLink	: 'oDrill-parent-a',
			classActive		: 'oDrill-active',
			classHeader		: 'oDrill-header',
			classCount		: 'oDrill-count',
			classIcon		: 'oDrill-icon',
			headerTag		: 'h3',
			speed       	: 500,
			saveState		: true,
			showCount		: true,
			linkType		: 'breadcrumb', //backlink, breadcrumb, link
			resetText		: 'All',
			defaultText		: 'Select Option',
			backlinkText	: 'Back',
			stick           : false,
			stickText       : 'You chose'
		};

		//call in the default otions
		var opts = $.extend(defaults, options);

		//act upon the element that is passed into the design
		return this.each(function(){
			var obj = this;
				$(obj).addClass(opts.classMenu).wrap('<div class="'+opts.classWrapper+'" />');
			var objWrapper = $(obj).parent();
			var objIndex = $(objWrapper).index('.'+opts.classWrapper);
			var idHeader = opts.classHeader+'-'+objIndex;
			var idWrapper = opts.classWrapper+'-'+objIndex;
			$(objWrapper).attr('id',idWrapper).width(opts.width);
			if(opts.stick){
				$(objWrapper).addClass('drilldown-stick').before('<span class="drilldown-stick-btn">'+opts.stickText+'</span>');
				$(objWrapper).prev('.drilldown-stick-btn').bind('click', function(e){
					if($(objWrapper).css('left') == '0px')
						$(objWrapper).css('left','-999em');
					else
						$(objWrapper).css('left',0);
				});
			}
			var $header = '<div id="'+idHeader+'" class="'+opts.classHeader+'"></div>';

			setUpDrilldown();

			if(opts.saveState == true){
				checkCookie(idWrapper, obj);
			}

			resetDrilldown(obj, objWrapper);

			$('li a',obj).click(function(e){

				var $link = this;
				var $activeLi = $(this).parent('li').stop();
				var $siblingsLi = $($activeLi).siblings();

				// Drilldown action
				if($('> ul',$activeLi).length){
					if($($link).hasClass(opts.classActive)){
						$('ul a',$activeLi).removeClass(opts.classActive);
						resetDrilldown(obj, objWrapper);
					} else {
						actionDrillDown($activeLi, objWrapper, obj);
					}
				}

				// Prevent browsing to link if has child links
				if($(this).next('ul').length > 0){
					e.preventDefault();
				}
			});

			// Set up accordion
			function setUpDrilldown(){

				var $arrow = '<span class="'+opts.classIcon+'">&raquo;</span>';
				$(obj).before($header);

				// Get width of menu container & height of list item
				var totalWidth = $(obj).outerWidth();
				var itemHeight = $('li',obj).outerHeight(true);

				// Get height of largest sub menu
				var objUl = $('ul', objWrapper);
				var maxItems = findMaxHeight(objUl);

				// Get level of largest sub menu
				var maxUl = $('ul[rel="'+maxItems+'"]', obj);
				var getIndex = findMaxIndex(maxUl);

				// Set menu container height
				if(opts.linkType == 'link'){
					var menuHeight = itemHeight * (maxItems + getIndex);
				} else {
					var menuHeight = itemHeight * maxItems;
				}
				$(obj).css({height: menuHeight, width: totalWidth});

				// Set sub menu width and offset
				$('li',obj).each(function(){
					$('ul',this).css({width: totalWidth, marginRight: '-100%', marginTop: '0'});
					if($('> ul',this).length){
						$(this).addClass(opts.classParent);
						$('> a',this).addClass(opts.classParentLink).append($arrow);
						if(opts.showCount == true){
							var parentLink = $('a:not(.'+opts.classParentLink+')',this);
							var countParent = parseInt($(parentLink).length);
							var getCount = countParent;
							$('> a',this).append(' <span class="'+opts.classCount+'">('+getCount+')</span>');
						}
					}
				});

				// Add css class
				$('ul',objWrapper).each(function(){
					$('li:last',this).addClass('last');
				});
				$('> ul > li:last',objWrapper).addClass('last');
				if(opts.linkType == 'link'){
					$(objUl).css('top',itemHeight+'px');
				}
			}
			
			$(window).resize(function(){
				$(obj).width(objWrapper.width()).find('ul').width(objWrapper.width());
			});
			
			// Breadcrumbs
			$('#'+idHeader).delegate('a', 'click',function(e){
				if($(this).hasClass('link-back')){
					var linkIndex = $('#'+idWrapper+' .'+opts.classParentLink+'.active').length;
						linkIndex = linkIndex-2;
					$('a.'+opts.classActive+':last', obj).removeClass(opts.classActive);
				} else {
					// Get link index
					var linkIndex = parseInt($(this).index('#'+idHeader+' a'));
					if(linkIndex == 0){
						$('a',obj).removeClass(opts.classActive);
					} else {
						// Select equivalent active link
						linkIndex = linkIndex-1;
						$('a.'+opts.classActive+':gt('+linkIndex+')',obj).removeClass(opts.classActive);
					}
				}
				resetDrilldown(obj, objWrapper);
				e.preventDefault();
			});
		});

		function findMaxHeight(element){
			var maxValue = undefined;
			$(element).each(function(){
				var val = parseInt($('> li',this).length);
				$(this).attr('rel',val);
				if (maxValue === undefined || maxValue < val){
					maxValue = val;
				}
			});
			return maxValue;
		}

		function findMaxIndex(element){
			var maxIndex = undefined;
			$(element).each(function(){
				var val = parseInt($(this).parents('li').length);
				if (maxIndex === undefined || maxIndex < val) {
					maxIndex = val;
				}
			});
			return maxIndex;
		}

		// Retrieve cookie value and set active items
		function checkCookie(cookieId, obj){
			var cookieVal = $.cookie(cookieId);
			if(cookieVal !== null){
				// create array from cookie string
				var activeArray = cookieVal.split(',');
				$.each(activeArray, function(index, value){
					$('li:eq('+value+') > a', obj).addClass(opts.classActive);
				});
			}
		}

		// Drill Down
		function actionDrillDown(element, wrapper, obj){
			// Declare header
			var $header = $('.'+opts.classHeader, wrapper).addClass('oDrill-'+opts.linkType);
			
			// Get new breadcrumb and header text
			var getNewBreadcrumb = $(opts.headerTag, $header).html();
			var getNewHeaderText = $('> a',element).html();

			// Add new breadcrumb
			if(opts.linkType == 'breadcrumb'){
				if(!$('ul',$header).length){
					$($header).prepend('<ul></ul>');
				}
				if(getNewBreadcrumb == opts.defaultText){
					$('ul',$header).append('<li><a href="#" class="first">'+opts.resetText+'</a></li>');
				} else {
					$('ul',$header).append('<li><a href="#">'+getNewBreadcrumb+'</a></li>');
				}
			}
			if(opts.linkType == 'backlink'){
				if(!$('a',$header).length){
					//$($header).prepend('<a href="#" class="link-back">'+getNewBreadcrumb+'</a>');
					$($header).prepend('<a href="#" class="link-back"><span class="'+opts.classIcon+'">&laquo;</span>'+opts.backlinkText+'</a>');
				} else {
					//$('.link-back',$header).html(getNewBreadcrumb);
					$('.link-back',$header).html('<span class="'+opts.classIcon+'">&laquo;</span>'+opts.backlinkText);
				}
			}
			if(opts.linkType == 'link'){
				if(!$('a',$header).length){
					$($header).prepend('<ul><li><a href="#" class="first">'+opts.resetText+'</a></li></ul>');
				}
			}
					
			// Update header text
			updateHeader($header, getNewHeaderText);

			// declare child link
			var activeLink = $('> a',element);

			// add active class to link
			$(activeLink).addClass(opts.classActive);
			$('> ul li',element).show();
			$('> ul',element).animate({"margin-right": 0}, opts.speed);

			// Find all sibling items & hide
			var $siblingsLi = $(element).siblings();
			$($siblingsLi).hide();

			// If using breadcrumbs hide this element
			if(opts.linkType != 'link'){
				$(activeLink).hide();
			}

			// Write cookie if save state is on
			if(opts.saveState == true){
				var cookieId = $(wrapper).attr('id');
					createCookie(cookieId, obj);
			}
		}

		// Drill Up
		function actionDrillUp(element, obj, wrapper){
			// Declare header
			var $header = $('.'+opts.classHeader, wrapper);

			var activeLink = $('> a',element);
			var checklength = $('.'+opts.classActive, wrapper).length;
			var activeIndex = $(activeLink).index('.'+opts.classActive, wrapper);

			// Get width of menu for animating right
			$('ul',element).css('margin-right','-100%');

			// Show all elements
			$(activeLink).addClass(opts.classActive);
			$('> ul li',element).show();
			$('a',element).show();

			// Get new header text from clicked link
			var getNewHeaderText = $('> a',element).html();
			$(opts.headerTag, $header).html(getNewHeaderText);

			if(opts.linkType == 'breadcrumb'){
				var breadcrumbIndex = activeIndex-1;
				$('a:gt('+activeIndex+')',$header).remove();
			}
		}

		function updateHeader(obj, html){
				if($(opts.headerTag, obj).length){
					$(opts.headerTag, obj).html(html);
				} else {
					$(obj).append('<'+opts.headerTag+'>'+html+'</'+opts.headerTag+'>');
				}
		}

		// Reset accordion using active links
		function resetDrilldown(obj, wrapper){
			var $header = $('.'+opts.classHeader, wrapper).removeClass('oDrill-'+opts.linkType);
			$('ul',$header).remove();
			$('a',$header).remove();
			$('li',obj).show();
			$('a',obj).show();
			if(opts.linkType == "link"){
				if($('a.'+opts.classActive+':last',obj).parent('li').length){
					var lastActive = $('a.'+opts.classActive+':last',obj).parent('li');
					$('ul',lastActive).css('margin-right','-100%');
				}else {
				$('ul',obj).css('margin-right','-100%');
				}
			} else {
				$('ul',obj).css('margin-right','-100%');
			}
			
			updateHeader($header, opts.defaultText);

			// Write cookie if save state is on
			if(opts.saveState == true){
				var cookieId = $(wrapper).attr('id');
					createCookie(cookieId, obj);
			}
			
			$('a.'+opts.classActive, obj).each(function(i){
				var $activeLi = $(this).parent('li').stop();
				actionDrillDown($activeLi, wrapper, obj);
			});
			
		}

		// Write cookie
		function createCookie(cookieId, obj){
			var activeIndex = [];
			// Create array of active items index value
			$('a.'+opts.classActive, obj).each(function(i){
				var $arrayItem = $(this).parent('li');
				var itemIndex = $('li', obj).index($arrayItem);
					activeIndex.push(itemIndex);
					
			});
			// Store in cookie
			if (activeIndex !== 'undefined' && activeIndex.length > 0) {
				$.cookie(cookieId, activeIndex);
			}else{
				$.cookie(cookieId, 'NO');
			}
		}
	};
})(jQuery);