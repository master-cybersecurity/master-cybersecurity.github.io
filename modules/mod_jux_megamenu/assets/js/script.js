/**
 * @version		$Id$
 * @author		JoomlaUX!
 * @package		Joomla.Site
 * @subpackage	mod_jux_megamenu
 * @copyright	Copyright (C) 2008 - 2013 by JoomlaUX. All rights reserved.
 * @license		http://www.gnu.org/licenses/gpl.html GNU/GPL version 3
 */

function getChildren(elm) {
    var n = elm.firstChild;
    var r = [];
    for (; n; n = n.nextSibling) {
        if (n.nodeType === 1 && n !== elm) {
            r.push(n);
        }
    }
    return r;
}
function bindEvent(elm, type, handler) {
    if (elm.addEventListener) {
        elm.addEventListener(type, handler, false);
    } else {
        elm.attachEvent('on' + type, handler);
    }
}

var isMobile = {
    Android: function() {
        return navigator.userAgent.match(/Android/i);
    },
    BlackBerry: function() {
        return navigator.userAgent.match(/BlackBerry/i);
    },
    iOS: function() {
        return navigator.userAgent.match(/iPhone|iPad|iPod/i);
    },
    Opera: function() {
        return navigator.userAgent.match(/Opera Mini/i);
    },
    Windows: function() {
        return navigator.userAgent.match(/IEMobile/i);
    },
    any: function() {
        return (isMobile.Android() || isMobile.BlackBerry() || isMobile.iOS() || isMobile.Opera() || isMobile.Windows());
    }
};

var JRMenu = window.JRMenu || {};
var jsMegaMenuMoo = new Class({
    initialize: function(menu, options) {
        this.options = Object.append({
            animation: 'slide',
            slide: true, //enable slide
            duration: 300, //slide speed. lower for slower, bigger for faster
            fading: false, //Enable fading
            bgopacity: 0.9, //set the transparent background. 0 to disable, 0<bgopacity<1: the opacity of the background
            delayHide: 500,
            menutype: 'horizontal',
            direction: 'down',
            action: 'mouseenter', //mouseenter or click
            hidestyle: 'normal', // the hiding style, normal, fast or fastwhenshow.
            hover_delay: 50, // the delay time that the mouse must hover over before the transition started.
            sticky: 1, // Enable menu stick with the window event after scroll pass the menu.
            menu_alignment: 'left',
            sticky_alignment: 'center'
        }, options || {});

        //ignore delayHide if no animation
        if (this.options.animation == 'none')
            this.options.delayHide = 10;

        this.menu = $(menu);

        JRMenu.inst = this;
        this.menu.addClass('mm-enable');
        this.childopen = new Array();
        this.imgloaded = false;
        this.loaded = false;

        this.addnavbtn();

        if (this.options.sticky && this.options.menutype == 'horizontal') {
            this.stickyMenu();
        }

        //check is touch mobile  
        this.isTouch = 'ontouchstart' in window;
        if (this.isTouch) {
            this.options.action = 'click'
            this.touchMenu(menu);
        }

        window.addEvent('load', this.start.bind(this));
        this.start();


    },
    touchMenu: function(id) {
        var all = getChildren(document.body),
                self = this;  //document.getElements(':not(#'+ menu +')');
        for (var i = 0; i < all.length; i++) {
            bindEvent(all[i], 'click', function() {
                self.itemHideOthers(null)
            });
        }
        this.menu.addEvent('click', function(e) {
            //stop default event
            e.preventDefault();
            e.stopPropagation();
        });
    },
    addnavbtn: function() {
        if (!(Browser.ie && Browser.version < 9)) {
            var mainNav = this.menu.getElement('ul.megamenu.level0');

            if (mainNav) {
                var megaMenu = this.menu;

                var toggleBtn = document.id('js-megaMenuToggle');
                // new Element ('div', {id:'js-megaMenuToggle', 'class': 'megaMenuToggle', html: 'Menu', styles: { display: 'none'}}).inject (megaMenu, 'before');
                // new Element('span', {'class': 'megaMenuToggle-icon'}).inject(toggleBtn);

                if (toggleBtn) {
                    toggleBtn.addEvent('click', function(e) {
                        e.stop();

                        if (megaMenu.getStyle('display') == 'block') {
                            megaMenu.removeClass('active').setStyle('display', 'none');
                            toggleBtn.removeClass('active');
                        } else {
                            megaMenu.addClass('active').setStyle('display', 'block');
                            toggleBtn.addClass('active');
                        }
                    });

                    $(document).addEvent('click', function() {
                        if (!megaMenu.hasClass('mm-enable')) {
                            megaMenu.removeClass('active').setStyle('display', 'none');
                        }

                        toggleBtn.removeClass('active');
                    });
                } else {
                    $(document).addEvent('click', function() {
                        if (!megaMenu.hasClass('mm-enable')) {
                            megaMenu.removeClass('active').setStyle('display', 'none');
                        }
                    });
                }
            }

            JRMenu.mmenuid = null;
            window.addEvent('resize', function() {
                clearTimeout(JRMenu.mmenuid);
                JRMenu.mmenuid = setTimeout(function() {
                    JRMenu.inst.start();
                }, 100);
            });
        }
    },
    stickyMenu: function() {
        if (this.options.menutype == 'horizontal' && this.menu.getHeight() > 0) {
            var megaMenuParent = this.menu.getParent('div.#js-mainnav');
            var menuPosition = megaMenuParent.getPosition().y;
            var menuAlignment = this.options.menu_alignment;
            var stickyAlignment = this.options.sticky_alignment;
            window.addEvent('scroll', function(e) {
                var scrollPosition = window.getScrollTop();
                if (!megaMenuParent.hasClass('megamenu-sticky') && menuPosition < scrollPosition) {
                    megaMenuParent.addClass('megamenu-sticky');
                    megaMenuParent.removeClass(menuAlignment);
                    megaMenuParent.addClass(stickyAlignment);
                } else if (megaMenuParent.hasClass('megamenu-sticky') && menuPosition >= scrollPosition) {
                    megaMenuParent.removeClass('megamenu-sticky');
                    megaMenuParent.removeClass(stickyAlignment);
                    megaMenuParent.addClass(menuAlignment);
                }
            });
        }
    },
    detect: function() {
        var toggleBtn = $('js-megaMenuToggle'),
                rs = true;
        if (toggleBtn) {
            rs = toggleBtn.getComputedStyle('display') == 'none';

            if (rs != this.menu.hasClass('mm-enable')) {

                this.menu[rs ? 'addClass' : 'removeClass']('mm-enable');

                this.menu.setStyle('display', rs ? 'block' : 'none');
            }
        }

        return rs;
    },
    start: function() {
        //do nothing if loaded

        if (!this.detect() || !this.imgloaded || this.loaded)
            ;

        if (this.loaded)
            return;

        this.menu = $(this.menu);
        //preload images
        var images = this.menu.getElements('img');
        if (images && images.length && !this.imageloaded) {
            var imgs = [];
            images.each(function(image) {
                imgs.push(image.src)
            });
        }

        //mark as called
        this.loaded = true;

        //get wrapper
        p = this.menu;
        while (p = p.getParent()) {
            if (p.hasClass('main') || p.hasClass('wrap')) {
                this.wrapper = p;
                break;
            }
        }
        this.items = this.menu.getElements('li.mega');
        //this.items.setStyle ('position', 'relative');
        this.items.each(function(li) {
            //link item
            if ((a = li.getElement('a.mega')) && this.isChild(a, li))
                li.a = a;
            else
                li.a = null;
            //parent
            li._parent = this.getParent(li);
            //child content
            if ((childcontent = li.getElement('.childcontent')) && this.isChild(childcontent, li)) {
                li.childcontent = childcontent;
                li.childcontent_inner = li.childcontent.getElement('.childcontent-inner-wrap');
                var coor = li.childcontent_inner.getCoordinates();
                li._w = li.getElement('.childcontent-inner').offsetWidth;
                li._h = li.getElement('.childcontent-inner').offsetHeight;

                li.level0 = li.getParent().hasClass('level0');
                //luanND change the childcontent to equal width with childcontent_inner
                //li.childcontent.setStyles ({'width':li._w+10, 'height':li._h});
                li.childcontent.setStyles({'width': li._w, 'height': li._h});
                li.childcontent_inner.setStyles({'width': li._w});
                //fix for overflow
                li.childcontent_inner1 = li.childcontent.getElement('.childcontent-inner');
                li.childcontent_inner1.ol = false;
                if (li.childcontent_inner1.getStyle('overflow') == 'auto' || li.childcontent_inner1.getStyle('overflow') == 'scroll') {
                    li.childcontent_inner1.ol = true;
                    //fix for ie6/7
                    if (window.ie6 || window.ie7) {
                        li.childcontent_inner1.setStyle('position', 'relative');
                    }

                    if (window.ie6) {
                        li.childcontent_inner1.setStyle('height', li.childcontent_inner1.getStyle('max-height') || 400);
                    }
                }

                //show direction
                if (this.options.direction == 'up') {
                    if (li.level0) {
                        li.childcontent.setStyle('top', -li.childcontent.offsetHeight); //ajust top position
                    } else {
                        li.childcontent.setStyle('bottom', 0);
                    }
                }
            }
            else
                li.childcontent = null;

            if (li.childcontent && this.options.bgopacity) {
                //Make transparent background
                var bg = new Element('div', {'class': 'childcontent-bg'});
                bg.injectTop(li.childcontent_inner);
                bg.setStyles({'width': '100%', 'height': li._h, 'opacity': this.options.bgopacity,
                    'position': 'absolute', 'top': 0, 'left': 0, 'z-index': 1
                });
                if (li.childcontent.getStyle('background'))
                    bg.setStyle('background', li.childcontent.getStyle('background'));
                if (li.childcontent.getStyle('background-image'))
                    bg.setStyle('background-image', li.childcontent.getStyle('background-image'));
                if (li.childcontent.getStyle('background-repeat'))
                    bg.setStyle('background-repeat', li.childcontent.getStyle('background-repeat'));
                if (li.childcontent.getStyle('background-color'))
                    bg.setStyle('background-color', li.childcontent.getStyle('background-color'));
                li.childcontent.setStyle('background', 'none');
                li.childcontent_inner.setStyles({'position': 'relative', 'z-index': 2});
            }

            if (li.childcontent && (this.options.animation != 'none')) {
                //li.childcontent.setStyles ({'width': li._w});

                li.childcontent.setStyles({'left': 'auto'});
                if (li.childcontent.hasClass('right'))
                    li.childcontent.setStyle('right', 0);
                if (this.options.animation == 'slide') {
                    li.childcontent.setStyles({'left': 'auto', 'overflow': 'hidden'});
                    if (li.level0) {
                        if (this.options.menutype == 'horizontal') {
                            if (this.options.direction == 'up') {
                                li.childcontent_inner.setStyle('bottom', -li._h - 20);
                            } else {
                                li.childcontent_inner.setStyle('margin-top', -li._h - 20);
                            }
                        }
                        else {

                            if (this.options.direction == 'righttoleft') {

                                li.childcontent_inner.setStyle('margin-left', li._w - 20);
                            } else {
                                li.childcontent_inner.setStyle('margin-left', -li._w - 20);
                            }
                        }

                    } else {
                        if (this.options.menutype == 'vertical' && this.options.direction == 'righttoleft') {
                            li.childcontent_inner.setStyle('margin-left', li._w - 20);
                        }
                        else {
                            li.childcontent_inner.setStyle('margin-left', -li._w - 20);
                        }
                    }
                }
                if (this.options.animation == 'fade') {
                    li.childcontent_inner.setStyle('opacity', 0);
                }
                //Init Fx.Styles for childcontent
                //li.fx = new Fx.Styles(li.childcontent_inner, {duration: this.options.duration, transition: Fx.Transitions.linear, onComplete: this.itemAnimDone.bind(this, li)});
                //li.fx = new Fx.Tween (li.childcontent_inner, {duration: this.options.duration, transition: Fx.Transitions.linear, onComplete: this.itemAnimDone.bind(this, li)});
                // Dohq: Fix for use both fade & slide
                li.fx = new Fx.Morph(li.childcontent_inner, {duration: this.options.duration, transition: Fx.Transitions.linear, onComplete: this.itemAnimDone.bind(this, li)});
                //effect
                //li.eff_on = {p:[],to:[]};
                //li.eff_off = {p:[],to:[]};
                li.eff_on = {};
                li.eff_off = {};
                if (this.options.animation == 'slide') {
                    if (li.level0) {
                        if (this.options.menutype == 'horizontal') {
                            if (this.options.direction == 'up') {
                                li.eff_on ['bottom'] = 0;
                                li.eff_off ['bottom'] = -li._h;
                            } else {
                                li.eff_on ['margin-top'] = 0;
                                li.eff_off ['margin-top'] = -li._h;
                            }
                        }
                        else {
                            if (this.options.direction == 'righttoleft') {
                                li.eff_on['margin-left'] = 0;
                                li.eff_off['margin-left'] = li._w;
                            }
                            else {
                                li.eff_on['margin-left'] = 0;
                                li.eff_off['margin-left'] = -li._w;
                            }
                        }
                    } else {
                        if (this.options.menutype == 'vertical' && this.options.direction == 'righttoleft') {
                            li.eff_on['margin-left'] = 0;
                            li.eff_off['margin-left'] = li._w;
                        }
                        else {
                            li.eff_on['margin-left'] = 0;
                            li.eff_off['margin-left'] = -li._w;
                        }
                    }
                }
                if (this.options.animation == 'fade') {
                    li.eff_on ['opacity'] = 1;
                    li.eff_off['opacity'] = 0;
                }
            }

            if (this.options.action == 'click' /*  && li.childcontent*/ ) {
                li.addEvent('click', function(e) {

                    var event = new DOMEvent(e);
                    //stop default event
                    event.preventDefault();
                    event.stopPropagation();
                    
//                    if (li.hasClass('group'))
//                        return;
                    
                    if (li.childcontent) {
                        if (li.status == 'open') {
                            if (this.cursorIn(li, event)) {
                                this.itemHide(li);
                            } else {
                                this.itemHideOthers(li);
                            }
                            //custom by mrdang
                            if (li.a && li.a.href){
                                location.href = li.a.href;
                           }
                        } else {
                            this.itemShow(li);
                        }
                    } else {
                        
                        var g = li.getElement('div.group-title');
                        if(g){
                            li.a = g.getElement('a.mega');
                        }
                        if (li.a && li.a.href){
                            location.href = li.a.href;
                       }
                       if (li.hasClass('group')){
                            return;
                        }
                    }
                    

                }.bind(this));
            }
            if (li.timer == undefined)
                li.timer = null;
			
			if (isMobile.any()) {
                 if (li.childcontent) {
                    mobile_button = new Element('span', {'class': 'arrow-icon', html: '', styles: {display: 'none'}}).inject(li.childcontent, 'before');
                    mobile_button.addEvent('click', function(e) {
                     var event = new DOMEvent (e);
                     //if (li.hasClass ('group')) return;
                     
                     if (li.childcontent) {
                         
                         if (li.status == 'open') {
                             if (this.cursorIn (li, event)) {
                                 this.itemHide (li);
                             } else {
                                 this.itemHideOthers(li);
                             }
                         } else {
                             this.itemShow (li);
                         }
                     } else {
                         
                         if (li.a) location.href = li.a.href;
                     }
                     event.stopPropagation();
                 }.bind (this));
                }
            }
			
            if (this.options.action == 'mouseover' || this.options.action == 'mouseenter') {
                var config = {
                    over: function(e) {
                        if (li.hasClass('group'))
                            return;

                        //fn.delay(li.timer);
                        //this.itemShow.delay(this.options.hover_delay, this, li);
                        this.itemShow(li);

                        if (!e.stopped) {
                            // Comment this line because of error in IE 8. Version 3.0.9
//							e.stopPropagation();
                            e.stopped = true; //make sure the stop function is call only once
                        }
                    }.bind(this),
                    interval: this.options.hover_delay,
                    out: function(e) {
                        if (li.hasClass('group'))
                            return;
                        //fn.delay(li.timer);
                        if (li.childcontent)
                            li.timer = this.itemHide.delay(this.options.delayHide, this, [li, e]);
                        else
                            this.itemHide(li, e);
                        if (!e.stopped) {
                            // Comment this line because of error in IE 8. Version 3.0.9
//							e.stopPropagation();
                            e.stopped = true; //make sure the stop function is call only once
                        }
                    }.bind(this)
                };

                li.hoverIntent(config);

                //if has childcontent, don't goto link before open childcontent - fix for touch screen
                if (li.a && li.childcontent) {
                    this.disableclick(li);
                    li.a.addEvent('click', function(e) {
                        if (!li.clickable) {
                            new DOMEvent(e).stop();
                        }
                    }.bind(this));
                }

                if (li.childcontent) {
                    mobile_button = new Element('span', {'class': 'arrow-icon', html: '', styles: {display: 'none'}}).inject(li.childcontent, 'before');
//					mobile_button.addEvent('click', function(e) {
//						var event = new DOMEvent (e);
//						//if (li.hasClass ('group')) return;
//						
//						if (li.childcontent) {
//							
//							if (li.status == 'open') {
//								if (this.cursorIn (li, event)) {
//									this.itemHide (li);
//								} else {
//									this.itemHideOthers(li);
//								}
//							} else {
//								this.itemShow (li);
//							}
//						} else {
//							
//							if (li.a) location.href = li.a.href;
//						}
//						event.stopPropagation();
//					}.bind (this));
                }

                //stop if click on menu item - prevent raise event to container => hide all open submenu
                li.addEvent('click', function(e) {
                    new DOMEvent(e).stopPropagation()
                });
            }

            //when click on a link - close all open childcontent
            if (li.a && !li.childcontent) {
                li.a.addEvent('click', function(e) {
                    this.itemHideOthers(null);
                    //Remove current class
                    this.menu.getElements('.active').removeClass('active');
                    //Add current class
                    var p = li;
                    while (p) {
                        p.addClass('active');
                        if (p.a) {
                            p.a.addClass('active');
                        }
                        p = p._parent;
                    }
                    new DOMEvent(e).stopPropagation();//prevent to raise event up
                }.bind(this));
            }

            if (li.childcontent)
                this.positionSubmenu(li);
        }, this);

        //click on windows will close all submenus
//        var container = $('wrapper');
//        if (!container) container = document.body;
//        if (container.addEvent) {
//            container.addEvent('click',function (e) {
//                this.itemHideOthers(null);
//            }.bind (this));
//        } else {
//            container.attachEvent('click',function (e) {
//                this.itemHideOthers(null);
//            }.bind (this));
//        }

        if (this.options.animation != 'none') {
            //hide all content child
            this.menu.getElements('.childcontent').setStyle('display', 'none');
        }

    },
    getParent: function(li) {
        var p = li;
        while ((p = p.getParent())) {
            if (this.items.contains(p) && !p.hasClass('group'))
                return p;
            if (!p || p == this.menu)
                return null;
        }
    },
    cursorIn: function(el, event) {
        if (!el || !event)
            return false;
        var pos = Object.merge(el.getPosition(), {'w': el.offsetWidth, 'h': el.offsetHeight});
        ;
        var cursor = {'x': event.page.x, 'y': event.page.y};

        if (cursor.x > pos.x && cursor.x < pos.x + el.offsetWidth
                && cursor.y > pos.y && cursor.y < pos.y + el.offsetHeight)
            return true;
        return false;
    },
    isChild: function(child, parent) {
        return !!parent.getChildren().contains(child);
    },
    itemOver: function(li) {
        if (li.hasClass('haschild'))
            li.removeClass('haschild').addClass('haschild-over');
        li.addClass('over');
        if (li.a) {
            li.a.addClass('over');
        }
    },
    itemOut: function(li) {
        if (li.hasClass('haschild-over'))
            li.removeClass('haschild-over').addClass('haschild');
        li.removeClass('over');
        if (li.a) {
            li.a.removeClass('over');
        }
    },
    itemShow: function(li) {
        clearTimeout(li.timer);
        if (li.status == 'open')
            return; //don't need do anything
        //Setup the class
        this.itemOver(li);
        //push to show queue
        li.status = 'open';
        this.childopen.push(li);
        //hide other
        this.itemHideOthers(li);
        if (li.childcontent) {
            //reposition the submenu
            this.positionSubmenu(li);
        }


        if (li.fx == null || li.childcontent == null)
            return;

        li.childcontent.setStyle('display', 'block');

        li.childcontent.setStyles({'overflow': 'hidden'});
        if (li.childcontent_inner1.ol) {
            li.childcontent_inner1.setStyles({'overflow': 'hidden'});
        }
        li.fx.cancel();

        li.fx.start(li.eff_on);
        //li.fx.start (li.eff_on.p, li.eff_on.to);
        //if (li._parent) this.itemShow (li._parent);
        this.enableclick.delay(this.options.duration + 500, this, li);
    },
    itemHide: function(li, e) {
        if (e && e.page) { //if event
            if (this.cursorIn(li, e) || this.cursorIn(li.childcontent, e)) {
                return;
            } //cursor in li
            var p = li._parent;
            if (p && !this.cursorIn(p, e) && !this.cursorIn(p.childcontent, e)) {
                p.fireEvent('mouseleave', e); //fire mouseleave event
            }
        }
        clearTimeout(li.timer);
        this.itemOut(li);
        li.status = 'close';
        this.childopen.erase(li);

        if (li.fx == null || li.childcontent == null)
            return;

        if (li.childcontent.getStyle('opacity') == 0)
            return;
        li.childcontent.setStyles({'overflow': 'hidden'});
        if (li.childcontent_inner1.ol)
            li.childcontent_inner1.setStyles({'overflow': 'hidden'});
        li.fx.cancel();
        switch (this.options.hidestyle) {
            case 'fast':
                li.fx.options.duration = 100;
                li.fx.start(Object.merge(li.eff_off, {'opacity': 0}));
                //li.fx.start ($merge(li.eff_off,{'opacity':0}));
                //li.fx.start(li.eff_off.p, li.eff_off.to);
                break;
            case 'fastwhenshow': //when other show
                if (!e) { //force hide, not because of event => hide fast
                    li.fx.start(Object.merge(li.eff_off, {'opacity': 0}));
                    //li.fx.options.duration = 300;
                    //li.fx.start ($merge(li.eff_off,{'opacity':0}));
                    //li.fx.start(li.eff_off.p, li.eff_off.to);
                } else {    //hide as normal
                    li.fx.start(li.eff_off);
                    //li.fx.start (li.eff_off);
                    //li.fx.start(li.eff_off.p, li.eff_off.to);
                }
                break;
            case 'normal':
            default:
                li.fx.start(li.eff_off);
                //li.fx.start(li.eff_off.p, li.eff_off.to);
                break;
        }
        //li.fx.start (li.eff_off);
    },
    itemAnimDone: function(li) {
        //hide done
        if (li.status == 'close') {
            //reset duration and enable opacity if not fading
            if (this.options.hidestyle.test(/fast/)) {
                li.fx.options.duration = this.options.duration;
                if (!this.options.animation != 'fade')
                    li.childcontent_inner.setStyle('opacity', 1);
            }
            //hide
            li.childcontent.setStyles({'display': 'none'});
            this.disableclick.delay(this.options.duration + 100, this, li);
        }

        //show done
        if (li.status == 'open') {
            li.childcontent.setStyles({'overflow': ''});
            if (li.childcontent_inner1.ol)
                li.childcontent_inner1.setStyles({'overflow-y': 'auto'});
            li.childcontent_inner.setStyle('opacity', 1);
            li.childcontent.setStyles({'display': 'block'});
        }
    },
    itemHideOthers: function(el) {
        var fakeevent = null
        if (el && !el.childcontent)
            fakeevent = {};
        var curopen = this.childopen;
        curopen.each(function(li) {
            if (li && typeof (li.status) != 'undefined' && (!el || (li != el && !(el !== li && li.contains(el))))) {
                this.itemHide(li, fakeevent);
            }
        }, this);
    },
    enableclick: function(li) {
        if (li.a && li.childcontent)
            li.clickable = true;
    },
    disableclick: function(li) {
        if (li.a && li.childcontent)
            li.clickable = false;
    },
    positionSubmenu: function(li) {
        if (li.level0) {
            if (!window.isRTL) {
                //check position
                var lcor = li.getCoordinates();
                var ccor = li.childcontent.getCoordinates();
                if (!ccor.width)
                {
                    li.childcontent.setStyle('display', 'block');
                    ccor = li.childcontent.getCoordinates();
                    li.childcontent.setStyle('display', 'none');
                }

                var ml = 0;
                var l = lcor.left;
                var r = l + ccor.width;
                if (this.wrapper) {
                    var wcor = this.wrapper.getCoordinates();
                    l = l - wcor.left;
                    r = wcor.right - r + 10;
                } else {
                    r = window.getWidth() - r + 10;
                }
                if (l < 0 || l + r < 0) {
                    ml = -l;
                } else if (r < 0) {
                    ml = r;
                }
                if (ml != 0)
                    li.childcontent.setStyle('margin-left', ml);
            } else {
                //check position
                var lcor = li.getCoordinates();
                var ccor = li.childcontent.getCoordinates();
                if (!ccor.width)
                {
                    li.childcontent.setStyle('display', 'block');
                    ccor = li.childcontent.getCoordinates();
                    li.childcontent.setStyle('display', 'none');
                }
                var mr = 0;
                var r = lcor.right;
                var l = r - ccor.width;
                if (this.wrapper) {
                    var wcor = this.wrapper.getCoordinates();
                    l = l - wcor.left;
                    r = wcor.right - r + 10;
                } else {
                    r = window.getWidth() - r + 10;
                }
                if (r < 0 || l + r < 0) {
                    mr = -r;
                } else if (l < 0) {
                    mr = l;
                }
                if (mr != 0)
                    li.childcontent.setStyle('margin-right', mr);
            }
        } else {
            // Process submenu with level > 1
            if (window.isRTL) {
                // Window is RTL
                // If direction = left  & childcontent is out of viewport, change direction to right & view content to right
                // If direction = right & childcontent is out of viewport, change direction to left
                var direction = 'left';
                if (li.view_direction != undefined) {
                    direction = li.view_direction;
                }
                var lcor = li.getCoordinates();
                var ccor = li.childcontent.getCoordinates();
                if (!ccor.width)
                {
                    li.childcontent.setStyle('display', 'block');
                    ccor = li.childcontent.getCoordinates();
                    li.childcontent.setStyle('display', 'none');
                }
                if (direction == 'right') {
                    // Check out of viewport
                    var r = lcor.right + ccor.width;
                    if (this.wrapper) {
                        var wcor = this.wrapper.getCoordinates();
                        r = wcor.right - r + 10;
                    } else {
                        r = window.getWidth() - r + 10;
                    }
                    if (r < 0) {
                        // Change position for submenu
                        li.childcontent.setStyle('margin-right', lcor.width);
                        // Change direction of submenu
                        var els = li.childcontent.getElements('li');
                        for (i = 0; i < els.length; i++) {
                            els[i].view_direction = 'left';
                        }
                    } else {
                        // Not out of viewport, however it is being right, so it need to be viewed in right
                        li.childcontent.setStyle('margin-right', -ccor.width + 20);
                    }
                } else {
                    // Check out of viewport in left direction
                    var l = lcor.left - ccor.width;
                    if (this.wrapper) {
                        var wcor = this.wrapper.getCoordinates();
                        l = l - wcor.left + 10;
                    } else {
                        l = l + 10;
                    }
                    if (l < 0) {
                        // Out of viewport, change position
                        li.childcontent.setStyle('margin-right', -ccor.width + 20);
                        // Change direction
                        var els = li.childcontent.getElements('li');
                        for (i = 0; i < els.length; i++) {
                            els[i].view_direction = 'right';
                        }
                    } else {
                        // Not viewport, however the position is not correct after initialize, so it need to change postion
                        li.childcontent.setStyle('margin-right', lcor.width);
                        // Level 3 is still have direction = right, so need to change submenu direction to left
                        var els = li.childcontent.getElements('li');
                        for (i = 0; i < els.length; i++) {
                            els[i].view_direction = 'left';
                        }
                    }
                }
            } else {
                // window isn't RTL
                // If direction == right and childcontent is out of view-port, change direction to left and view childcontent in left
                // If direction == left  and childcontent is out of view-port, change direction to right
                var direction = 'right';
                if (li.view_direction != undefined) {
                    direction = li.view_direction;
                }
                var lcor = li.getCoordinates();
                var ccor = li.childcontent.getCoordinates();
                if (!ccor.width)
                {
                    li.childcontent.setStyle('display', 'block');
                    ccor = li.childcontent.getCoordinates();
                    li.childcontent.setStyle('display', 'none');
                }
                if (direction == 'right') {
                    // Check out of viewport
                    var r = lcor.right + ccor.width;
                    if (this.wrapper) {
                        var wcor = this.wrapper.getCoordinates();
                        r = wcor.right - r + 10;
                    } else {
                        r = window.getWidth() - r + 10;
                    }
                    if (r < 0) {
                        // Change position for submenu
                        li.childcontent.setStyle('margin-left', -ccor.width + 20);
                        // Change direction of submenu
                        var els = li.childcontent.getElements('li');
                        for (i = 0; i < els.length; i++) {
                            els[i].view_direction = 'left';
                        }
                    }
                } else {
                    // Check out of viewport in left direction
                    var l = lcor.left - ccor.width;
                    if (this.wrapper) {
                        var wcor = this.wrapper.getCoordinates();
                        l = l - wcor.left + 10;
                    } else {
                        l = l + 10;
                    }
                    if (l < 0) {
                        // Out of viewport, so change direction
                        var els = li.childcontent.getElements('li');
                        for (i = 0; i < els.length; i++) {
                            els[i].view_direction = 'right';
                        }
                    } else {
                        // Not out of viewport, however it is being left, so it need to be viewed in left
                        li.childcontent.setStyle('margin-left', -lcor.width - 20);
                    }
                }
            }
        }
    }
});

