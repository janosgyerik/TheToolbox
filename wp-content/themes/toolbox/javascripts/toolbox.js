/*-----------------------------------------------------------------------------------

 	Custom JS - All custom front-end jQuery

-----------------------------------------------------------------------------------*/

//TinySort
(function(b){var o=!1,d=null,u=parseFloat,j=String.fromCharCode,q=Math.min,l=/(-?\d+\.?\d*)$/g,g,a=[],h,m,t=9472,f={},c;if(!Array.indexOf){Array.prototype.indexOf=function(w){for(var v=0,s=this.length;v<s;v++){if(this[v]==w){return v}}return -1}}for(var p=32,k=j(p),r=255;p<r;p++,k=j(p).toLowerCase()){if(a.indexOf(k)!==-1){a.push(k)}}a.sort();b.tinysort={id:"TinySort",version:"1.3.27",copyright:"Copyright (c) 2008-2012 Ron Valstar",uri:"http://tinysort.sjeiti.com/",licenced:{MIT:"http://www.opensource.org/licenses/mit-license.php",GPL:"http://www.gnu.org/licenses/gpl.html"},defaults:{order:"asc",attr:d,data:d,useVal:o,place:"start",returns:o,cases:o,forceStrings:o,sortFunction:d,charOrder:g}};b.fn.extend({tinysort:function(V,L){if(V&&typeof(V)!="string"){L=V;V=d}var T=b.extend({},b.tinysort.defaults,L),v,Q=this,z=b(this).length,ae={},W=!(!V||V==""),H=!(T.attr===d||T.attr==""),ah=T.data!==d,J=W&&V[0]==":",C=J?Q.filter(V):Q,F=T.sortFunction,s=T.order=="asc"?1:-1,P=[];if(T.charOrder!=g){g=T.charOrder;if(!T.charOrder){m=false;t=9472;f={};c=h=d}else{h=a.slice(0);m=false;for(var S=[],B=function(i,ai){S.push(ai);f[T.cases?i:i.toLowerCase()]=ai},N="",X="z",aa=g.length,ac,Z,ad=0;ad<aa;ad++){var x=g[ad],ab=x.charCodeAt(),I=ab>96&&ab<123;if(!I){if(x=="["){var D=S.length,M=D?S[D-1]:X,w=g.substr(ad+1).match(/[^\]]*/)[0],R=w.match(/{[^}]*}/g);if(R){for(ac=0,Z=R.length;ac<Z;ac++){var O=R[ac];ad+=O.length;w=w.replace(O,"");B(O.replace(/[{}]/g,""),M);m=true}}for(ac=0,Z=w.length;ac<Z;ac++){B(M,w[ac])}ad+=w.length+1}else{if(x=="{"){var G=g.substr(ad+1).match(/[^}]*/)[0];B(G,j(t++));ad+=G.length+1;m=true}else{S.push(x)}}}if(S.length&&(I||ad===aa-1)){var E=S.join("");N+=E;b.each(E,function(i,ai){h.splice(h.indexOf(ai),1)});var A=S.slice(0);A.splice(0,0,h.indexOf(X)+1,0);Array.prototype.splice.apply(h,A);S.length=0}if(ad+1===aa){c=new RegExp("["+N+"]","gi")}else{if(I){X=x}}}}}if(!F){F=T.order=="rand"?function(){return Math.random()<0.5?1:-1}:function(av,at){var au=o,am=!T.cases?n(av.s):av.s,ak=!T.cases?n(at.s):at.s;if(!T.forceStrings){var aj=am&&am.match(l),aw=ak&&ak.match(l);if(aj&&aw){var ar=am.substr(0,am.length-aj[0].length),aq=ak.substr(0,ak.length-aw[0].length);if(ar==aq){au=!o;am=u(aj[0]);ak=u(aw[0])}}}var ai=s*(am<ak?-1:(am>ak?1:0));if(!au&&T.charOrder){if(m){for(var ax in f){var al=f[ax];am=am.replace(ax,al);ak=ak.replace(ax,al)}}if(am.match(c)!==d||ak.match(c)!==d){for(var ap=0,ao=q(am.length,ak.length);ap<ao;ap++){var an=h.indexOf(am[ap]),i=h.indexOf(ak[ap]);if(ai=s*(an<i?-1:(an>i?1:0))){break}}}}return ai}}Q.each(function(ak,al){var am=b(al),ai=W?(J?C.filter(al):am.find(V)):am,an=ah?""+ai.data(T.data):(H?ai.attr(T.attr):(T.useVal?ai.val():ai.text())),aj=am.parent();if(!ae[aj]){ae[aj]={s:[],n:[]}}if(ai.length>0){ae[aj].s.push({s:an,e:am,n:ak})}else{ae[aj].n.push({e:am,n:ak})}});for(v in ae){ae[v].s.sort(F)}for(v in ae){var ag=ae[v],K=[],Y=z,af=[0,0],ad;switch(T.place){case"first":b.each(ag.s,function(ai,aj){Y=q(Y,aj.n)});break;case"org":b.each(ag.s,function(ai,aj){K.push(aj.n)});break;case"end":Y=ag.n.length;break;default:Y=0}for(ad=0;ad<z;ad++){var y=e(K,ad)?!o:ad>=Y&&ad<Y+ag.s.length,U=(y?ag.s:ag.n)[af[y?0:1]].e;U.parent().append(U);if(y||!T.returns){P.push(U.get(0))}af[y?0:1]++}}Q.length=0;Array.prototype.push.apply(Q,P);return Q}});function n(i){return i&&i.toLowerCase?i.toLowerCase():i}function e(v,x){for(var w=0,s=v.length;w<s;w++){if(v[w]==x){return !o}}return o}b.fn.TinySort=b.fn.Tinysort=b.fn.tsort=b.fn.tinysort})(jQuery);
/*-----------------------------------------------------------------------------------*/
/*	Let's dance
/*-----------------------------------------------------------------------------------*/


jQuery(document).ready(function() {

/*-----------------------------------------------------------------------------------*/
/*	Toolbox
/*-----------------------------------------------------------------------------------*/


	$(".share-link").click(function(){
		var shareWidget=$(this).parent();
		if(!shareWidget.hasClass("active")){
			$(this).parent().toggleClass("active");
			$(".overlay").toggleClass("hidden");
		}
		return false;
	});
	$(".share-widget").hover(function(){
		$(this).addClass("active");
	},function(){
		$(this).removeClass("active");
	});
	$(".overlay").click(function(){
		$(this).addClass("hidden");
		$(".share-widget").removeClass("active");
	});

/*-----------------------------------------------------------------------------------*/
/*	Portfolio Filtering
/*-----------------------------------------------------------------------------------*/
	
			
	if(jQuery().isotope) {
		
		$container = jQuery('#masonryXXX');
		
		$container.imagesLoaded( function() {
			
			$container.isotope({
				animationEngine: 'none',
	  	    	itemSelector : '.item',
	  	    	layoutMode: 'fitRows',
	  	    	containerStyleObject: {
	  	    		position: 'static', 
	  	    		overflow: 'hidden' 
	  	    	},
	  			getSortData: {
	
					// order: function($elem) {
					// 	return parseInt($elem.attr('data-order'));
					// }, 
		  			name: function($elem) {
		  				return $elem.find(".post-title a").text();
		  			}, 
		  			votes: function($elem) {
		  				return parseInt($elem.find(".votes").text());
		  			}
	
	  			},
	  			sortBy: 'order'
	  	    }, function() {
		    	dt_getposts();
		   	});


		});


  	    // filter items when filter link is clicked
		jQuery('#filter-by li').click(function(){

			jQuery('#filter-by li').removeClass('active');
			jQuery(this).addClass('active');

			var selector = jQuery(this).find('a').attr('data-filter');

			// $container.isotope({ filter: selector });
			var items=jQuery("#masonry .item");
			if(selector=="all"){
				items.removeClass("hidden");
			}else{
				items.each(function(){
					var item=jQuery(this);
					item.removeClass("hidden");
					if(!item.hasClass(selector)){
						item.addClass("hidden");
					}
				});
			}
	        return false;

		});

		$('#sort-by li').click(function(){
			jQuery('#sort-by li').removeClass('active');
			jQuery(this).addClass('active');
			// get href attribute, minus the '#'
			var sortName = $(this).find("a").attr('href').slice(1);
			var items=$('#masonry .item');
			switch(sortName){
				case "order":
					items.tsort({data:"order"});
					break;
				case "name":
					items.tsort('.post-title a');
					break;
				case "votes":
					items.tsort('.votes', {order: 'desc'});
					break;
			}
			// var sortOrder=true;
			// if(sortName=="votes"){
			// 	var sortOrder=false;
			// }
			// $container.isotope({ sortBy : sortName, sortAscending : sortOrder });

			return false;
		});
  	}


/*-----------------------------------------------------------------------------------*/
/*	Extras
/*-----------------------------------------------------------------------------------*/

	// jQuery(".tabber ul.tabs").tabs(".tabber div.panes > div", {
	// 	effect: 'fade'
	// });

	// jQuery(".accordion").tabs(".accordion div.pane", {
	// 	tabs: '.trigger', effect: 'slide', initialIndex: null
	// });

	jQuery('.toggle .trigger').bind('click', function() {
		var maketoggle = jQuery(this).parent('.toggle').find('.pane');
		jQuery(maketoggle).slideToggle();
		jQuery(this).toggleClass('open');
		return false;
	});

	jQuery('<div class="clear">&nbsp;</div>').insertAfter('.column-last');


/*-----------------------------------------------------------------------------------*/
/*	Superfish Settings - http://users.tpg.com.au/j_birch/plugins/superfish/
/*-----------------------------------------------------------------------------------*/

	jQuery('#primary-menu ul').superfish({
		delay: 0,
		animation: {opacity:'show', height:'show'},
		speed: 'fast',
		autoArrows: false,
		dropShadows: false
	});

	jQuery("#primary-menu ul ul").each(
		function (i) { // Preserves the mouse-over on top-level menu elements when hovering over children
		    jQuery(this).hover(

			    function() {

			    	jQuery(this).parent().find("a").slice(0, 1).addClass("active");

			    }, function () {

			    	jQuery(this).parent().find("a").slice(0, 1).removeClass("active");

			    }
		    );

		    var parent = jQuery(this).parent().outerWidth();

		    if(parent < 150) {
				var diff = 150 - parent;
				jQuery(this).css({
					width: '150px',
					marginLeft: -diff / 2
				});
			}
			else {
				jQuery(this).css('width', '100%');
			}

		}
	);


/*-----------------------------------------------------------------------------------*/
/*	Header Overlay
/*-----------------------------------------------------------------------------------*/

	function dt_set_responsive() {

		$height = jQuery('#overlay').outerHeight();

		jQuery('#overlay').css({
				marginTop: -$height
		});

		jQuery('#overlay-trigger').toggle( function() {

			jQuery('#overlay').stop().animate({
				marginTop: 0
			}, 500, 'easeInOutExpo');

			return false;

		}, function() {

			jQuery('#overlay').stop().animate({
				marginTop: -$height
			}, 500, 'easeInOutExpo');

			return false;

		});

	}

	dt_set_responsive();



/*-----------------------------------------------------------------------------------*/
/*	Funky Responsive Stuff
/*-----------------------------------------------------------------------------------*/

	jQuery(window).resize(function() {

		dt_set_responsive();

	});




/*-----------------------------------------------------------------------------------*/
/*	Load More Button
/*-----------------------------------------------------------------------------------*/

	//var dt = false;

	function dt_getposts(pageNum, max, nextLink, count) {

	 	if(typeof dt != 'undefined') {

		 	jQuery('#load-more.disabled').click(function() { return false; });

			if(!pageNum) {
				// The number of the next page to load (/page/x/).
				var pageNum = parseInt(dt.startPage) + 1;
			}

			if(!max) {
				// The maximum number of pages the current query can return.
				var max = parseInt(dt.maxPages);
			}

			if(!nextLink) {
				// The link of the next page of posts.
				var nextLink = dt.nextLink;
			}

			if(!count) {
				var count = parseInt(jQuery('.count').text());
			}

			/**
			 * Replace the traditional navigation with our own,
			 * but only if there is at least one page of new posts to load.
			 */
			if(pageNum <= max) {

				// Remove the traditional navigation.
				jQuery('.post-navigation').remove();

			} else {

				jQuery('#load-more').addClass('disabled');

			}


			/**
			 * Load new posts when the link is clicked.
			 */
			jQuery('#load-more').not('.disabled').click(function() {

				jQuery(this).unbind('click', dt_getposts());

				// Are there more posts to load?
				if(pageNum <= max) {

					// Show that we're working.
					//jQuery(this).text('Loading posts...');

					jQuery('#detail-holder').fadeOut(200, function(){
						jQuery('#loader').fadeIn(200);
					});

					jQuery('#masonry-new').load(nextLink + ' .item.normal',
						function() {

							var $newEls = jQuery( '#masonry-new .item.normal' );
						    jQuery('#masonry').isotope( 'insert', $newEls, function() {

						  		dt_hovers();
						  		dt_lightbox();

								// Update page number and nextLink.
								pageNum++;
								nextLink = nextLink.replace(/\/page\/[0-9]?/, '/page/'+ pageNum);

								// Update the button message.
								if(pageNum <= max) {

									jQuery('#loader').fadeOut(200, function () {

										count = count - parseInt(jQuery('#loader').attr('data-perpage'));
										jQuery('.count').text(count);
										jQuery('#detail-holder').fadeIn(200);
										jQuery('#load-more').bind('click', dt_getposts(pageNum, max, nextLink, count));
									});

								} else {

									jQuery('#loader').fadeOut(200, function () {

										jQuery('#load-more').addClass('disabled');
										jQuery('.count').text('0');
										jQuery('#detail-holder').fadeIn(200);
										jQuery('#load-more').bind('click', dt_getposts(pageNum, max, nextLink, count));

									});

								}

						  	});
						}
					);
				}

				return false;

			});

		}

	}


/*-----------------------------------------------------------------------------------*/
/*	Show #backtotop link after scrollTop length
/*-----------------------------------------------------------------------------------*/

	jQuery(window).bind('scroll', function(){
		jQuery('#backtotop').toggle(jQuery(this).scrollTop() > 200);
	});


/*-----------------------------------------------------------------------------------*/
/*	Contact Form
/*-----------------------------------------------------------------------------------*/

	jQuery.fn.exists = function () { // Check if element exists
	    return jQuery(this).length;
	}
	jQuery('.dt-contactform').submit(function () {
	    var cf = jQuery(this);
	    cf.prev('.alert').slideUp(400, function () {
	        cf.prev('.alert').hide();
	        jQuery.post(ajaxurl, {
	            name: cf.find('.dt-name').val(),
	            email: cf.find('.dt-email').val(),
	            subject: cf.find('.dt-subject').val(),
	            comments: cf.find('.dt-comments').val(),
	            verify: cf.find('.dt-verify').val(),
	            action: 'dt_contact_form'
	        }, function (data) {
	            cf.prev('.alert').html(data);
	            cf.prev('.alert').slideDown('slow');
	            cf.find('img.loader').fadeOut('slow', function () {
	                jQuery(this).remove()
	            });
	            if (data.match('success') != null) cf.slideUp('slow');
	        });
	    });
	    return false;
	});


/*-----------------------------------------------------------------------------------*/
/*	Plugins
/*-----------------------------------------------------------------------------------*/

/**
 * jQuery.ScrollTo - Easy element scrolling using jQuery.
 * Copyright (c) 2007-2009 Ariel Flesler - aflesler(at)gmail(dot)com | http://flesler.blogspot.com
 * Dual licensed under MIT and GPL.
 * Date: 5/25/2009
 * @author Ariel Flesler
 * @version 1.4.2
 *
 * http://flesler.blogspot.com/2007/10/jqueryscrollto.html
 */
;(function(d){var k=d.scrollTo=function(a,i,e){d(window).scrollTo(a,i,e)};k.defaults={axis:'xy',duration:parseFloat(d.fn.jquery)>=1.3?0:1};k.window=function(a){return d(window)._scrollable()};d.fn._scrollable=function(){return this.map(function(){var a=this,i=!a.nodeName||d.inArray(a.nodeName.toLowerCase(),['iframe','#document','html','body'])!=-1;if(!i)return a;var e=(a.contentWindow||a).document||a.ownerDocument||a;return d.browser.safari||e.compatMode=='BackCompat'?e.body:e.documentElement})};d.fn.scrollTo=function(n,j,b){if(typeof j=='object'){b=j;j=0}if(typeof b=='function')b={onAfter:b};if(n=='max')n=9e9;b=d.extend({},k.defaults,b);j=j||b.speed||b.duration;b.queue=b.queue&&b.axis.length>1;if(b.queue)j/=2;b.offset=p(b.offset);b.over=p(b.over);return this._scrollable().each(function(){var q=this,r=d(q),f=n,s,g={},u=r.is('html,body');switch(typeof f){case'number':case'string':if(/^([+-]=)?\d+(\.\d+)?(px|%)?$/.test(f)){f=p(f);break}f=d(f,this);case'object':if(f.is||f.style)s=(f=d(f)).offset()}d.each(b.axis.split(''),function(a,i){var e=i=='x'?'Left':'Top',h=e.toLowerCase(),c='scroll'+e,l=q[c],m=k.max(q,i);if(s){g[c]=s[h]+(u?0:l-r.offset()[h]);if(b.margin){g[c]-=parseInt(f.css('margin'+e))||0;g[c]-=parseInt(f.css('border'+e+'Width'))||0}g[c]+=b.offset[h]||0;if(b.over[h])g[c]+=f[i=='x'?'width':'height']()*b.over[h]}else{var o=f[h];g[c]=o.slice&&o.slice(-1)=='%'?parseFloat(o)/100*m:o}if(/^\d+$/.test(g[c]))g[c]=g[c]<=0?0:Math.min(g[c],m);if(!a&&b.queue){if(l!=g[c])t(b.onAfterFirst);delete g[c]}});t(b.onAfter);function t(a){r.animate(g,j,b.easing,a&&function(){a.call(this,n,b)})}}).end()};k.max=function(a,i){var e=i=='x'?'Width':'Height',h='scroll'+e;if(!d(a).is('html,body'))return a[h]-d(a)[e.toLowerCase()]();var c='client'+e,l=a.ownerDocument.documentElement,m=a.ownerDocument.body;return Math.max(l[h],m[h])-Math.min(l[c],m[c])};function p(a){return typeof a=='object'?a:{top:a,left:a}}})(jQuery);




/*-----------------------------------------------------------------------------------*/
/*	We've finished dancing!
/*-----------------------------------------------------------------------------------*/

});