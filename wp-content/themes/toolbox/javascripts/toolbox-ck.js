/*-----------------------------------------------------------------------------------

 	Custom JS - All custom front-end jQuery

-----------------------------------------------------------------------------------*//*-----------------------------------------------------------------------------------*//*	Let's dance
/*-----------------------------------------------------------------------------------*/jQuery(document).ready(function(){function a(){$height=jQuery("#overlay").outerHeight();jQuery("#overlay").css({marginTop:-$height});jQuery("#overlay-trigger").toggle(function(){jQuery("#overlay").stop().animate({marginTop:0},500,"easeInOutExpo");return!1},function(){jQuery("#overlay").stop().animate({marginTop:-$height},500,"easeInOutExpo");return!1})}function b(a,c,d,e){if(typeof dt!="undefined"){jQuery("#load-more.disabled").click(function(){return!1});if(!a)var a=parseInt(dt.startPage)+1;if(!c)var c=parseInt(dt.maxPages);if(!d)var d=dt.nextLink;if(!e)var e=parseInt(jQuery(".count").text());a<=c?jQuery(".post-navigation").remove():jQuery("#load-more").addClass("disabled");jQuery("#load-more").not(".disabled").click(function(){jQuery(this).unbind("click",b());if(a<=c){jQuery("#detail-holder").fadeOut(200,function(){jQuery("#loader").fadeIn(200)});jQuery("#masonry-new").load(d+" .item.normal",function(){var f=jQuery("#masonry-new .item.normal");jQuery("#masonry").isotope("insert",f,function(){dt_hovers();dt_lightbox();a++;d=d.replace(/\/page\/[0-9]?/,"/page/"+a);a<=c?jQuery("#loader").fadeOut(200,function(){e-=parseInt(jQuery("#loader").attr("data-perpage"));jQuery(".count").text(e);jQuery("#detail-holder").fadeIn(200);jQuery("#load-more").bind("click",b(a,c,d,e))}):jQuery("#loader").fadeOut(200,function(){jQuery("#load-more").addClass("disabled");jQuery(".count").text("0");jQuery("#detail-holder").fadeIn(200);jQuery("#load-more").bind("click",b(a,c,d,e))})})})}return!1})}}$(".share-link").click(function(){var a=$(this).parent();if(!a.hasClass("active")){$(this).parent().toggleClass("active");$(".overlay").toggleClass("hidden")}return!1});$(".share-widget").hover(function(){$(this).addClass("active")},function(){$(this).removeClass("active")});$(".overlay").click(function(){$(this).addClass("hidden");$(".share-widget").removeClass("active")});jQuery(".toggle .trigger").bind("click",function(){var a=jQuery(this).parent(".toggle").find(".pane");jQuery(a).slideToggle();jQuery(this).toggleClass("open");return!1});jQuery('<div class="clear">&nbsp;</div>').insertAfter(".column-last");jQuery("#primary-menu ul").superfish({delay:0,animation:{opacity:"show",height:"show"},speed:"fast",autoArrows:!1,dropShadows:!1});jQuery("#primary-menu ul ul").each(function(a){jQuery(this).hover(function(){jQuery(this).parent().find("a").slice(0,1).addClass("active")},function(){jQuery(this).parent().find("a").slice(0,1).removeClass("active")});var b=jQuery(this).parent().outerWidth();if(b<150){var c=150-b;jQuery(this).css({width:"150px",marginLeft:-c/2})}else jQuery(this).css("width","100%")});a();jQuery(window).resize(function(){a()});if(jQuery().isotope){$container=jQuery("#masonry");$container.imagesLoaded(function(){$container.isotope({itemSelector:".item",masonry:{columnWidth:320},getSortData:{order:function(a){return parseInt(a.attr("data-order"))}},sortBy:"order"},function(){b()})});jQuery("#filter li").click(function(){jQuery("#filter li").removeClass("active");jQuery(this).addClass("active");var a=jQuery(this).find("a").attr("data-filter");$container.isotope({filter:a});return!1})}jQuery(window).bind("scroll",function(){jQuery("#backtotop").toggle(jQuery(this).scrollTop()>200)});jQuery.fn.exists=function(){return jQuery(this).length};jQuery(".dt-contactform").submit(function(){var a=jQuery(this);a.prev(".alert").slideUp(400,function(){a.prev(".alert").hide();jQuery.post(ajaxurl,{name:a.find(".dt-name").val(),email:a.find(".dt-email").val(),subject:a.find(".dt-subject").val(),comments:a.find(".dt-comments").val(),verify:a.find(".dt-verify").val(),action:"dt_contact_form"},function(b){a.prev(".alert").html(b);a.prev(".alert").slideDown("slow");a.find("img.loader").fadeOut("slow",function(){jQuery(this).remove()});b.match("success")!=null&&a.slideUp("slow")})});return!1});(function(a){function c(a){return typeof a=="object"?a:{top:a,left:a}}var b=a.scrollTo=function(b,c,e){a(window).scrollTo(b,c,e)};b.defaults={axis:"xy",duration:parseFloat(a.fn.jquery)>=1.3?0:1};b.window=function(b){return a(window)._scrollable()};a.fn._scrollable=function(){return this.map(function(){var b=this,c=!b.nodeName||a.inArray(b.nodeName.toLowerCase(),["iframe","#document","html","body"])!=-1;if(!c)return b;var e=(b.contentWindow||b).document||b.ownerDocument||b;return a.browser.safari||e.compatMode=="BackCompat"?e.body:e.documentElement})};a.fn.scrollTo=function(e,f,g){if(typeof f=="object"){g=f;f=0}typeof g=="function"&&(g={onAfter:g});e=="max"&&(e=9e9);g=a.extend({},b.defaults,g);f=f||g.speed||g.duration;g.queue=g.queue&&g.axis.length>1;g.queue&&(f/=2);g.offset=c(g.offset);g.over=c(g.over);return this._scrollable().each(function(){function r(a){i.animate(o,f,g.easing,a&&function(){a.call(this,e,g)})}var h=this,i=a(h),l=e,m,o={},q=i.is("html,body");switch(typeof l){case"number":case"string":if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(l)){l=c(l);break}l=a(l,this);case"object":if(l.is||l.style)m=(l=a(l)).offset()}a.each(g.axis.split(""),function(a,c){var d=c=="x"?"Left":"Top",e=d.toLowerCase(),f="scroll"+d,j=h[f],n=b.max(h,c);if(m){o[f]=m[e]+(q?0:j-i.offset()[e]);if(g.margin){o[f]-=parseInt(l.css("margin"+d))||0;o[f]-=parseInt(l.css("border"+d+"Width"))||0}o[f]+=g.offset[e]||0;g.over[e]&&(o[f]+=l[c=="x"?"width":"height"]()*g.over[e])}else{var p=l[e];o[f]=p.slice&&p.slice(-1)=="%"?parseFloat(p)/100*n:p}/^\d+$/.test(o[f])&&(o[f]=o[f]<=0?0:Math.min(o[f],n));if(!a&&g.queue){j!=o[f]&&r(g.onAfterFirst);delete o[f]}});r(g.onAfter)}).end()};b.max=function(b,c){var e=c=="x"?"Width":"Height",f="scroll"+e;if(!a(b).is("html,body"))return b[f]-a(b)[e.toLowerCase()]();var g="client"+e,h=b.ownerDocument.documentElement,i=b.ownerDocument.body;return Math.max(h[f],i[f])-Math.min(h[g],i[g])}})(jQuery)});