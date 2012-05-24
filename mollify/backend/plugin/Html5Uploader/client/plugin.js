mollify.registerPlugin(new Html5UploaderPlugin());

function Html5UploaderPlugin() {
	var that = this;
	
	this.getPluginInfo = function() { return { id: "plugin_html5uploader" }; }
	
	this.initialize = function(env) {
		that.env = env;
		that.env.addUploader(that.onUpload);
		that.logDebug("Plugin init");
	}
	
	this.onUpload = function(folder, listener) {
		that.env.dialog().showDialog({
			title: that.t('html5UploaderDialogTitle'),
			html: "<div id='html5uploader-dialog-content'><div id='html5uploader-content-loading' /></div>",
			on_show: function(d) { that.onShowUploadDialog(d, folder, listener); }
		});
	}
	
	this.onShowUploadDialog = function(d, f, l) {
		that.d = d;
		
		mollify.loadContent("html5uploader-dialog-content", that.url("uploader.html"), function() {
			d.setMinimumSizeToCurrent();
			d.center();

			var uploader = new Html5Uploader(that.getSettings(f));

			$('#html5uploader-cancel-select-button').click(function(e) {
				uploader.destroy();
				d.close();
			});
			$('#html5uploader-cancel-upload-button').click(function(e) {
				uploader.stop();
				uploader.destroy();
				d.close();
			});
			$('#html5uploader-upload-button').click(function(e) {
				e.preventDefault();
				if (uploader.files.length == 0) return;

				//that.fakeUpload();
				//return;
				
				that.env.service().post("html5uploader/"+f.id+"/check/", {files: uploader.files}, function(result) {
					if (!result.ok) {
						return;
					}
					
					$('#html5uploader-header-text').html(that.t("html5uploaderHeaderUploading"));
					$('#html5uploader-select-button').remove();
					
					$('#html5uploader-footer-select').hide();
					$('#html5uploader-footer-upload').show();
					$('#html5uploader-files').addClass("uploading");
					$(".html5uploader-file-remove").remove();
					
					uploader.start();
				},	function(code, error) {
					alert(error);
				});
			});
			
			that.uploader = uploader;
			uploader.init();
			
			window.setTimeout(function() {
				if (!that.initialized) {
					that.d.hide();
					alert("HTML 5 uploader not supported.");
				}
			}, 5000);
		});
	}
	
	this.getSettings = function(f) {
		var settings = mollify.getSettings()["html5uploader"];
		settings["url"] = that.serviceUrl(f.id);
		return settings;
	}
	
	this.addNoFilesLabel = function() {
		$("#html5uploader-files-empty").tmpl({}).appendTo("#html5uploader-files");
		mollify.localize("html5uploader-files");
	}
	
	this.removeFile = function(id) {
		var f = that.uploader.getFile(id);
		if (f) {
			that.uploader.removeFile(f);
			that.uploader.refresh();
		}
		$('#'+id).remove();
		if (that.uploader.files.length == 0) that.addNoFilesLabel();
	}
	
	this.formatFileSize = function(s) {
		return that.env.texts().formatSize(s);
	}
	
	this.fakeUpload = function() {
		$('#html5uploader-footer-select').hide();
		$('#html5uploader-footer-upload').show();
		$('#html5uploader-files').addClass("uploading");
		$(".html5uploader-file-remove").remove();
		
		var first = that.uploader.files[0];
		$('#'+first.id).addClass("active");
		$('#'+first.id+'-progress').show();
		that.onFileProgress({id: first.id, percent:40});
		
		var second = that.uploader.files[1];
    	$('#'+second.id+'-progress').html('');
    	$('#'+second.id).addClass("complete");
	}
	
	this.onFileProgress = function(file) {
		$('#'+file.id+'-progress').html(file.percent+"%");
	}
	
	this.url = function(p) {
		var url = that.env.service().getPluginUrl("html5uploader")+"client/";
		if (!p) return url;
		return url + p;
	}

	this.serviceUrl = function(id) {
		return that.env.service().getUrl("html5uploader")+"/"+id+"/";
	}
		
	this.t = function(s) {
		return that.env.texts().get(s);
	}
	
	this.logDebug = function(s) {
		that.env.log().debug("pluginHtml5uploader: " + s);
	}
}