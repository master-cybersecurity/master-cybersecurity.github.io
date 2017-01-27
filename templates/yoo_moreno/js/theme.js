/* Copyright (C) YOOtheme GmbH, YOOtheme Proprietary Use License (http://www.yootheme.com/license) */

jQuery(function($) {

	var config = $('html').data('config') || {};

	// Social buttons
	$('article[data-permalink]').socialButtons(config);


   (function(){

       var options    = {itemSelector: '.uk-panel', isResizeBound: false, columnWidth:'.tm-masonary-column-indicator'},
           containers = $('.tm-mosaic').each(function() {

                var container = $(this).data("columnIndicator", $('<div class="tm-masonary-column-indicator"></div>').appendTo(this));

                recalcSize(container);
                container.masonry(options);
           });

       $(window).on("resize", $.UIkit.Utils.debounce(function(){
           updateContainers();
       }, 100)).on("message", $.UIkit.Utils.debounce(function(e) {
               if (e.originalEvent.data == "customizer-update")  updateContainers();
       }, 150));

       function updateContainers() {
           containers.each(function(){
               var container = $(this);
               recalcSize(container);
               container.masonry("layout");
           });
       }

       function recalcSize(container){

           var columnwidth  = container.data("columnIndicator").width(),
               layoutwidth  = container.width(),
               columns      = Math.round(layoutwidth / columnwidth),
               base         = Math.floor(layoutwidth / columns);

           if(columns==3) {
             base -=1;
           }

           if (columns==1) {
               container.data("basewidth", false).find(options.itemSelector).css({ width: "100%", height:"auto"});
               return;
           }

           if (base && container.data("basewidth") != base) {

               container.data("basewidth", base);

               container.find(options.itemSelector).each(function(){
                   var element = $(this),
                       match   = element.attr("class").match(/size(\d+)/),
                       dim     = match ? String(match[1]).split(""):[1,1];

                   element.css({"width": (dim[0]<columns ? dim[0]:1) * base, "height": (dim[0]<columns ? dim[1]:1) * base});
               });
           }
       }

   })();

});
