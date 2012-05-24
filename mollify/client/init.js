(function(){
	window.mollify = new function(){
		var t = this;
		t.time = new Date().getTime();
		this.settings = {};
		this.plugins = [];
		this.pluginsById = {};

		this.init = function(s) {
			t.settings = s;
			if (s.plugins) {
				for (var i=0; i < s.plugins.length; i++)
					t.registerPlugin(s.plugins[i]);
			}
		}
		
		this.setup = function(e) {
			t.env = e;
			
			t.env.addListColumnSpec({
				"id": "file-modified",
				"request-id": "core-file-modified",
				"default-title-key": "fileListColumnTitleLastModified",
				"sort": function(i1, i2, sort, data) {
					if (!i1.is_file && !i2.is_file) return 0;
					if (!data || !data["core-file-modified"]) return 0;
					
					var ts1 = data["core-file-modified"][i1.id] ? data["core-file-modified"][i1.id] * 1 : 0;
					var ts2 = data["core-file-modified"][i2.id] ? data["core-file-modified"][i2.id] * 1 : 0;
					return ((ts1 > ts2) ? 1 : -1) * sort;
				},
				"content": function(item, data) {
					if (!item.id || !item.is_file || !data || !data["core-file-modified"] || !data["core-file-modified"][item.id]) return "";
					return t.env.texts().formatInternalTime(data["core-file-modified"][item.id]);
				}
			});
			t.env.addListColumnSpec({
				"id": "item-description",
				"request-id": "core-item-description",
				"default-title-key": "fileListColumnTitleDescription",
				"sort": function(i1, i2, sort, data) {
					if (!i1.is_file && !i2.is_file) return 0;
					if (!data || !data["core-item-description"]) return 0;
					
					var d1 = data["core-item-description"][i1.id] ? data["core-item-description"][i1.id] : '';
					var d2 = data["core-item-description"][i2.id] ? data["core-item-description"][i2.id] : '';
					return ((d1 > d2) ? 1 : -1) * sort;
				},
				"content": function(item, data) {
					if (!item.id || !data || !data["core-item-description"] || !data["core-item-description"][item.id]) return "";
					var desc = data["core-item-description"][item.id];
					var stripped = desc.replace(/<\/?[^>]+(>|$)/g, '');
					return '<div class="item-description-container" title="'+stripped+'">'+desc+'</div>';
				}
			});
			
			//$.datepicker.setDefaults({
			//	dateFormat: e.texts().get('shortDateFormat').replace(/yyyy/g, 'yy')
			//});
		}
		
		this.getSettings = function() {
			return t.settings;
		}

		this.getPlugins = function() {
			return t.plugins;
		}
		
		this.getPlugin = function(id) {
			return t.pluginsById[id];
		}

		this.hasPlugin = function(id) {
			return !!t.pluginsById[id];
		}
		
		this.registerPlugin = function(p) {
			t.plugins.push(p);
			var id = p.getPluginInfo().id;
			if (id) t.pluginsById[id] = p;
		}

		this.importScript = function(url) {
			$.getScript(url);
		}
		
		this.importCss = function(url) {
			var link = $("<link>");
			link.attr({
		    	type: 'text/css',
		    	rel: 'stylesheet',
		    	href: t.urlWithParam(url, "_="+mollify.time)
			});
			$("head").append(link);
		}
		
		this.loadContent = function(id, url, cb) {
			$("#"+id).load(t.urlWithParam(url, "_="+mollify.time), function() {
				t.localize(id);
				if (cb) cb();
			});
		}
		
		this.urlWithParam = function(url, param) {
			return url + (strpos(url, "?") ? "&" : "?") + param;
		}
		
		this.localize = function(id) {
			$("#"+id+" .localized").each(function(){
				var key = $(this).attr('title-key');
				if (key)
					$(this).attr("title", t.env.texts().get(key));
				
				key = $(this).attr('text-key');
				if (key)
					$(this).text(t.env.texts().get(key));
			});
		}
		
		/*this.formatDate = function(d) {
			return $.datepicker.formatDate(getDateFormat(), d);
		}*/

		this.formatDateTime = function(time, fmt) {
			return time.format(fmt);
		}

		/*this.parseDate = function(dateFmt, date, time) {
			if (!date || date.length == 0) return null;
			
			var t = $.datepicker.parseDate(dateFmt, date);
			if (!time || time.length < 5) {
				t.setHours("00");
				t.setMinutes("00");
				t.setSeconds("00");
			} else {
				//TODO timeFmt
				t.setHours(time.substring(0,2));
				t.setMinutes(time.substring(3,5));
				t.setSeconds(time.length > 6 ? time.substring(7,9) : "00");
			}
			return t;
		}*/

		this.parseInternalTime = function(time) {
			var ts = new Date();
			ts.setYear(time.substring(0,4));
			ts.setMonth(time.substring(4,6) - 1);
			ts.setDate(time.substring(6,8));
			ts.setHours(time.substring(8,10));
			ts.setMinutes(time.substring(10,12));
			ts.setSeconds(time.substring(12,14));
			return ts;
		}

		this.formatInternalTime = function(time) {
			if (!time) return null;
			return time.format('yymmddHHMMss', time);
		}
		
		this.texts = new function(){
			var tt = this;
			this.locale = '';
			this.values = null;
			
			this.set = function(id, values) {
				tt.locale = id;
				tt.values = values;
			}
			
			this.add = function(id, values) {
				//TODO handle different locale
				for (v in values) {
					tt.values[v] = values[v];
				}
			}
		}
	}
})();

function isArray(o) {
	return Object.prototype.toString.call(o) === '[object Array]';
}

if(typeof String.prototype.trim !== 'function') {
	String.prototype.trim = function() {
		return this.replace(/^\s+|\s+$/g, ''); 
	}
}

function strpos(haystack, needle, offset) {
    // Finds position of first occurrence of a string within another  
    // 
    // version: 1109.2015
    // discuss at: http://phpjs.org/functions/strpos
    // +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
    // +   improved by: Onno Marsman    
    // +   bugfixed by: Daniel Esteban
    // +   improved by: Brett Zamir (http://brett-zamir.me)
    var i = (haystack + '').indexOf(needle, (offset || 0));
    return i === -1 ? false : i;
}

function CommentPlugin() {
	var that = this;
	
	this.getPluginInfo = function() { return { id: "plugin-comment" }; }
	
	this.initialize = function(env) {
		that.env = env;
		that.env.addItemContextProvider(function(item) {
			return {
				components : [{
					html: "",
					on_init: function(id, c, item, details) {
						if (!details["plugin-comment"]) return;
						
						$("#"+id).html("<div id='details-comments'><div id='details-comments-content'><div id='details-comments-icon'/><div id='details-comment-count'>"+details["plugin-comment"].count+"</div></div></div>");
						
						$("#details-comments-content").hover(
							function () { $(this).addClass("hover"); }, 
							function () { $(this).removeClass("hover"); }
						);
						$("#details-comments-content").click(function() {
							c.close();
							that.openComments(item);
						});
					}
				}]
			};
		}, function(item) {
			return {"plugin-comment":["count"]};
		});
		
		mollify.importCss(that.url("style.css"));
		mollify.importScript(that.url("texts_" + that.env.texts().locale + ".js"));
		
		env.addListColumnSpec({
			"id": "comment-count",
			"request-id": "plugin-comment-count",
			"default-title-key": "",
			"content": that.getListCellContent,
			"request": function(parent) { return {}; },
			"on-render": function() {
				var onclick = function(e) {
					var id = e.target.id.substring(19);
					var item = that.env.fileview().item(id);
					that.openComments(item);
				}
//				var tooltip = "<div class='filelist-item-comment-tooltip mollify-tooltip'>" + that.t("commentsFileListAddTitle") + "</div>";
				$(".filelist-item-comment-count,.filelist-item-comment-count-none").click(onclick);//.simpletip({content: tooltip, fixed: true, position: 'left'});
			}
		});
		
		if (mollify.hasPlugin("plugin-itemdetails"))
			mollify.getPlugin("plugin-itemdetails").addDetailsSpec({
				key: "comments-count",
				"title-key": "commentsDetailsCount"
			});
	}
	
	this.getListCellContent = function(item, data) {
		if (!item.id || item.id.length == 0 || !data || !data["plugin-comment-count"]) return "";
		var counts = data["plugin-comment-count"];

		if (!counts[item.id])
			return "<div id='item-comment-count-"+item.id+"' class='filelist-item-comment-count-none'></div>";
		
		return "<div id='item-comment-count-"+item.id+"' class='filelist-item-comment-count'>"+counts[item.id]+"</div>";
	}
	
	this.openComments = function(item) {
		that.env.dialog().showDialog({
			title: that.t("commentsDialogTitle"),
			html: "<div id='comments-dialog-content' class='loading' />",
			on_show: function(d) { that.onShowCommentsDialog(d, item); }
		});
	}

	this.onShowCommentsDialog = function(d, item) {
		mollify.loadContent("comments-dialog-content", that.url("content.html"), function() {
			d.setMinimumSizeToCurrent();
			d.center();
			
			$("#comments-item").html(item.name);
			$("#comments-dialog-content").removeClass("loading");
			$("#comments-dialog-content .mollify-actionlink").hover(
				function () { $(this).addClass("mollify-actionlink-hover"); }, 
				function () { $(this).removeClass("mollify-actionlink-hover"); }
			);

			$("#comments-dialog-add").click(function() { that.onAddComment(d, item); } );
			$("#comments-dialog-close").click(function() { d.close(); } );
			
			that.env.service().get("comment/"+item.id, function(result) {
				that.onShowComments(item, result);
			},	function(code, error) {
				alert(error);
			});
		});
	}
	
	this.onAddComment = function(d, item) {
		var comment = $("#comments-dialog-add-text").val();
		if (!comment || comment.length == 0) return;
		
		that.env.service().post("comment/"+item.id, { comment: comment }, function(result) {
			d.close();
			
			var e = document.getElementById("item-comment-count-"+item.id);
			e.innerHTML = result.count;
			e.setAttribute('class', 'filelist-item-comment-count');
		},	function(code, error) {
			alert(error);
		});
	}
	
	this.onRemoveComment = function(item, id) {		
		that.env.service().del("comment/"+item.id+"/"+id, function(result) {
			that.onShowComments(item, result);
		},	function(code, error) {
			alert(error);
		});
	}
	
	this.onShowComments = function(item, comments) {
		if (comments.length == 0) {
			$("#comments-list").html("<span class='message'>"+that.t("commentsDialogNoComments")+"</span>");
			return;
		}
		
		var isAdmin = that.env.session().isAdmin();
		var userId = that.env.session().info()['user_id'];
		
		for (var i=0,j=comments.length; i<j; i++) {
			comments[i].time = that.env.texts().formatInternalTime(comments[i].time);
			comments[i].comment = comments[i].comment.replace(new RegExp('\n', 'g'), '<br/>');
			comments[i].remove = isAdmin || (userId == comments[i]['user_id']);
		}

		$("#comment-template").tmpl(comments).appendTo($("#comments-list").empty());
		mollify.localize("comments-list");
		$(".comment-content").hover(
			function () { $(this).addClass("hover"); }, 
			function () { $(this).removeClass("hover"); }
		);
		$(".comment-remove-action").click(function(e) {
			e.preventDefault();
			var id = $(this).parent().attr('id').substring(8);
			that.onRemoveComment(item, id);
		});
	}
	
	this.url = function(p) {
		return that.env.service().getPluginUrl("Comment")+"client/"+p;
	}
	
	this.t = function(s) {
		return that.env.texts().get(s);
	}	
}

function ItemDetailsPlugin(conf, sp) {
	var that = this;
	that.specs = {};
	that.typeConfs = false;
	
	this.getPluginInfo = function() { return { id: "plugin-itemdetails" }; }
	
	this.initialize = function(env) {
		that.env = env;
		
		if (sp) {
			for (var i=0; i<sp.length;i++)
				that.addDetailsSpec(sp[i]);
		}
		if (conf) {
			that.typeConfs = {};
			
			for (var t in conf) {
				var parts = t.split(",");
				var c = conf[t];
				for (var i=0; i < parts.length; i++) {
					var p = parts[i].trim();
					if (p.length > 0)
						that.typeConfs[p] = c;
				}
			}
		}
		
		that.env.addItemContextProvider(function(item) {
			if (!that.typeConfs || !that.getApplicableSpec(item)) return null;
			
			return {
				components : [{
					title: that.t("fileActionDetailsTitle"),
					html: "<div id='file-item-details' class='loading'></div>",
					on_init: that.onInit,
					on_open: that.onOpen,
					on_dispose: function() { that.loaded = false; },
					index: 5
				}]
			}
		}, function(item) {
			if (!that.typeConfs) return null;
			var spec = that.getApplicableSpec(item);
			if (!spec) return null;
			
			var result = { itemdetails: [] };
			for (var k in spec)
				result.itemdetails.push(k);
			return result;
		});
	}
	
	this.onOpen = function(item, details) {
		if (that.loaded) return;
		that.loaded = true;
		
		mollify.loadContent("file-item-details", that.url("content.html"), function() {
			$("#file-item-details").removeClass("loading");
			var s = that.getApplicableSpec(item);
			var data = [];
			for (var k in s) {
				var rowSpec = s[k];
				var rowData = details.itemdetails[k];
				if (!rowData) continue;
				
				data.push({key:k, title:that.getTitle(k, rowSpec), value: that.formatData(k, rowData)});
			}
			$("#item-details-template").tmpl(data).appendTo("#mollify-file-item-details-content");
		});
	}
	
	this.addDetailsSpec = function(s) {
		if (!s || !s.key) return;
		that.specs[s.key] = s;
	}
	
	this.getApplicableSpec = function(item) {
		var ext = item.is_file ? item.extension.toLowerCase().trim() : "";
		if (ext.length == 0 || !that.typeConfs[ext]) {
			ext = item.is_file ? "[file]" : "[folder]";
			if (!that.typeConfs[ext])
				return that.typeConfs["*"];
		}
		return that.typeConfs[ext];
	}
	
	this.onInit = function(id, c, item, details) {
		if (!that.typeConfs || !details.itemdetails) return false;
		that.loaded = false;
	}
	
	this.getTitle = function(dataKey, rowSpec) {
		if (rowSpec.title) return rowSpec.title;
		if (rowSpec["title-key"]) return that.t(rowSpec["title-key"]);

		if (dataKey == 'name') return that.t('fileItemContextDataName');
		if (dataKey == 'size') return that.t('fileItemContextDataSize');
		if (dataKey == 'path') return that.t('fileItemContextDataPath');
		if (dataKey == 'extension') return that.t('fileItemContextDataExtension');
		if (dataKey == 'last-modified') return that.t('fileItemContextDataLastModified');
		if (dataKey == 'image-size') return that.t('fileItemContextDataImageSize');
		
		if (that.specs[dataKey]) {
			var spec = that.specs[dataKey];
			if (spec.title) return spec.title;
			if (spec["title-key"]) return that.t(spec["title-key"]);
		}
		return dataKey;
	}
	
	this.formatData = function(key, data) {
		if (key == 'size') return that.env.texts().formatSize(data);
		if (key == 'last-modified') return that.env.texts().formatInternalTime(data);
		if (key == 'image-size') return that.t('fileItemContextDataImageSizePixels', [data]);
		
		if (that.specs[key]) {
			var spec = that.specs[key];
			if (spec.formatter) return spec.formatter(data);
		}

		return data;
	}
		
	this.url = function(p) {
		return that.env.service().getPluginUrl("ItemDetails")+"client/"+p;
	}
	
	this.t = function(s, p) {
		return that.env.texts().get(s, p);
	}
}

function ExifDetails() {
	var t = this;
	
	this.formatExif = function(d) {
		var html = "<div id='item-details-exif'><table id='item-details-exif-values'>";
		for (var s in d) {
			var first = true;
			for (var k in d[s]) {
				var v = t.formatValue(s, k, d[s][k]);
				if (!v) continue;
				
				html += '<tr id="exif-row-'+s+'-'+k+'" class="'+(first?'exif-row-section-first':'exif-row')+'"><td class="exif-section">'+(first?s:'')+'</td><td class="exif-key">'+k+'</td><td class="exif-value">'+v+'</td></tr>';
				first = false;
			}
		}
		return html + "</table></div>";
	}
	
	this.formatValue = function(section, key, value) {
		if (section == 'FILE' && key == 'SectionsFound') return false;
		//TODO format values?
		return value;
	}
	
	return {
		key: "exif",
		"title-key": "fileItemDetailsExif",
		formatter: t.formatExif
	}
}

function SharePlugin() {
	var that = this;
	
	this.getPluginInfo = function() { return { id: "plugin_share" }; }
	
	this.initialize = function(env) {
		that.env = env;
		
		if (window.ZeroClipboard) {
			that.testclip = new ZeroClipboard.Client();
			that.testclip.addEventListener('load', function(client) {
				//console.log("Clipboard support detected");
				that.testclip.hide();
				
				that.clip = new ZeroClipboard.Client();
				that.clip.setHandCursor(true);
				that.clip.setCSSEffects(false);
				that.clip.addEventListener('onMouseOver', function() { that.onClipMouse(true); });
				that.clip.addEventListener('onMouseOut', function() { that.onClipMouse(false); });
				that.clip.addEventListener('onComplete', that.onClipClick);
				
				if (that.shares.length > 0) $(".share-link-copy-container").removeClass("hidden");
			});
		}

		that.clip = false;
		that.hoverId = false;
		
		mollify.importCss(that.url("style.css"));
		mollify.importScript(that.url("texts_" + that.env.texts().locale + ".js"));
		
		that.env.addItemContextProvider(function(item) {
			/*return {
				components : [{
					html: "<div id='file-item-share'></div>",
					on_init: function(id, c, item, details) {
						if (!details["plugin-share"]) return;
						
						$("#"+id).html("<div id='details-share'><div id='details-share-content'><div id='details-share-icon'/>"+that.t('itemContextShareTitle')+"</div></div>");
						
						$("#details-share-content").hover(
							function () { $(this).addClass("hover"); }, 
							function () { $(this).removeClass("hover"); }
						);
						$("#details-share-content").click(function() {
							c.close();
							that.openShares(item);
						});
					},
					index: 6
				}]
			}*/
			return {
				actions: {
					secondary: [
						{ title: "-" },
						{
							title: that.t('itemContextShareMenuTitle'),
							callback: function(item) {
								that.openShares(item);
							}
						}
					]
				}
			};
		}, function(item) {
			return {"plugin-share":[]};
		});
	}
	
	this.openShares = function(item) {
		that.env.dialog().showDialog({
			modal: false,
			title: that.t("shareDialogTitle"),
			html: "<div id='share-dialog-content' class='loading'></div>",
			on_show: function(d) { that.onShowSharesDialog(d, item); }
		});
	}

	this.onShowSharesDialog = function(d, item) {
		mollify.loadContent("share-dialog-content", that.url("content.html"), function() {
			$("#share-item-title").html(that.t(item.is_file ? 'shareDialogShareFileTitle' : 'shareDialogShareFolderTitle'));
			$("#share-item-name").html(item.name);
			$("#share-dialog-content").removeClass("loading");

			$("#add-share-btn").click(function() { that.onAddShare(item); } );
			$("#share-dialog-close").click(function() { d.close(); } );
			if (that.testclip) that.testclip.glue("clip-test");
			
			that.env.service().get("share/items/"+item.id, function(result) {
				that.refreshShares(item, result);
				that.updateShareList(item);
				d.setMinimumSizeToCurrent();
				d.center();
			},	function(code, error) {
				alert(error);
			});
		});
	}
	
	this.refreshShares = function(item, shares) {
		that.shares = shares;
		that.shareIds = [];
		
		for (var i=0, j=shares.length; i<j; i++)
			that.shareIds.push(shares[i].id);
	}
	
	this.getShare = function(id) {
		return that.shares[that.shareIds.indexOf(id)];
	}
	
	this.updateShareList = function(item) {
		$("#share-items").empty();
		
		if (that.shares.length == 0) {
			$("#share-items").html('<div class="no-share-items">'+that.t("shareDialogNoShares")+'</div>');
			return;
		}
		
		var opt = {
			name : function() {
				if (!this.data.name || this.data.name.length == 0)
					return '<text key="shareDialogUnnamedShareTitle" />';
				return this.data.name;
			},
			itemClass : function() {
				var c = "item-share";
				if (!this.data.active)
					c = c + " item-share-inactive";
				if (!this.data.name || this.data.name.length == 0)
					c = c + " item-share-unnamed";
				return c;
			},
			link : function() {
				return that.env.service().getUrl("public/"+this.data.id);
			}
		};
		
		$("#share-template").tmpl(that.shares, opt).appendTo("#share-items");
		mollify.localize("share-list");
		
		if (that.clip) $(".share-link-copy-container").removeClass("hidden");
		//else console.log("Clipboard support not detected");
		
		var initClipboard = function(id) {
			if (!that.clip) return;
			if (!that.getShare(id).active) {
				that.clip.setText("");
				that.clip.hide();
				return;
			}
			
			that.clip.show();
			that.clip.setText(that.env.service().getUrl("public/"+id));
			var el = $("#share-copy-"+id);
			if (that.clip.div) {
				that.clip.reposition(el[0]);
			} else {
				that.clip.glue(el[0]);
			}
		}

		$(".item-share").hover(
			function() {
				$(".item-share").removeClass("item-share-hover");
				
				var el = $(this);
				var id = el.attr('id').substring(6);
				el.addClass("item-share-hover");
				that.hoverId = id;
				
				initClipboard(id);
			},
			function() {
			}
		);
		//initClipboard(that.shares[0].id);
		
		var idFunction = function(i, f) {
			var p = $(i).hasClass('item-share') ? i : $(i).parentsUntil(".item-share").parent()[0];
			var id = p.id.substring(6);
			f(item, id);
		};
		$(".share-link-toggle-title").click(function() {
			var id = $(this).parent()[0].id.substring(6);
			if (!that.getShare(id).active) return;
			
			var linkContainer = $(this).next();
			var open = linkContainer.hasClass("open");
			if (!open) $(".share-link-content").removeClass("open");
			linkContainer.toggleClass("open");
			return false;
		}).hover(
			function() { $(this).addClass("hover"); },
			function() { $(this).removeClass("hover"); }
		);
		
		$(".share-edit").click(function(e) {
			idFunction(this, that.onEditShare);
			return false;
		});
		$(".share-remove").click(function(e) {
			idFunction(this, that.removeShare);
			return false;
		});
	}
	
	this.onClipMouse = function(over) {
		if (!that.hoverId) return;
		
		if (over) $("#share-copy-"+that.hoverId).addClass("hover");
		else $("#share-copy-"+that.hoverId).removeClass("hover");
	}

	this.onClipClick = function() {
		if (!that.hoverId) return;
		
		var el = $("#share-copy-"+that.hoverId);
		el.addClass("click");
		window.setTimeout(function() { el.removeClass("click"); }, 200);
	}
	
	this.closeAddEdit = function() {
		$("#share-items").removeClass("minimized");
		$("#share-context").addClass("minimized");
		$(".share-context-toolbar-option").hide();
		$("#add-share-btn").show();
	}
	
	this.openContext = function(toolbarId, contentTemplateId) {
		$("#share-items").addClass("minimized");
		$("#share-context").removeClass("minimized");
		$(".share-context-toolbar-option").hide();
		$("#"+toolbarId).show();
		$("#"+contentTemplateId).tmpl({}).appendTo($("#share-context-content").empty());
		mollify.localize("share-context-content");
		//$("#share-validity-expirationdate-value").datepicker();
	}
	
	this.onAddShare = function(item) {
		that.openContext('add-share-title', 'share-context-addedit-template');
		
		$("#share-general-name").val('');
		$('#share-general-active').attr('checked', true);

		$("#share-addedit-btn-ok").click(function() {
			var name = $("#share-general-name").val();
			var active = $("#share-general-active").is(":checked");
			var expiration = null;//mollify.parseDate(that.t('shortDateFormat'), $("#share-validity-expirationdate-value").val(), $("#share-validity-expirationtime-value").val());
			
			$("#share-items").empty().append('<div class="loading"/>');
			that.closeAddEdit();
			that.addShare(item, name || '', expiration, active);
		});
		
		$("#share-addedit-btn-cancel").click(function() {
			that.closeAddEdit();
		});
	}
	
	this.onEditShare = function(item, id) {
		that.openContext('edit-share-title', 'share-context-addedit-template');
		
		var share = that.getShare(id);
		
		$("#share-general-name").val(share.name);
		$("#share-general-active").attr("checked", share.active);
		
		$("#share-addedit-btn-ok").click(function() {
			var name = $("#share-general-name").val();
			var active = $("#share-general-active").is(":checked");
			var expiration = mollify.parseDate(that.t('shortDateFormat'), $("#share-validity-expirationdate-value").val(), $("#share-validity-expirationtime-value").val());
			
			$("#share-items").empty().append('<div class="loading"/>')
			that.closeAddEdit();
			that.editShare(item, share.id, name || '', expiration, active);
		});
		
		$("#share-addedit-btn-cancel").click(function() {
			that.closeAddEdit();
		});
	}
	
	this.addShare = function(item, name, expiration, active) {
		that.env.service().post("share/items/"+item.id, { item: item.id, name: name, expiration: mollify.formatInternalTime(expiration), active: active }, function(result) {
			that.refreshShares(item, result);
			that.updateShareList(item);
		},	function(code, error) {
			alert(error);
		});
	}

	this.editShare = function(item, id, name, expiration, active) {
		that.env.service().put("share/"+id, { id: id, name: name, expiration: mollify.formatInternalTime(expiration), active: active }, function(result) {
			var share = that.getShare(id);
			share.name = name;
			share.active = active;
			share.expiration = expiration;
			that.updateShareList(item);
		},	function(code, error) {
			alert(error);
		});
	}
	
	this.removeShare = function(item, id) {
		that.env.service().del("share/"+id, function(result) {
			var i = that.shareIds.indexOf(id);
			that.shareIds.splice(i, 1);
			that.shares.splice(i, 1);
			that.updateShareList(item);
		},	function(code, error) {
			alert(error);
		});
	}
	
	this.url = function(p) {
		return that.env.service().getPluginUrl("Share")+"client/"+p;
	}
	
	this.t = function(s, p) {
		return that.env.texts().get(s, p);
	}
}