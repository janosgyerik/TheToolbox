/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

mollify.registerPlugin(new ArchiverPlugin());

function ArchiverPlugin() {
	var that = this;
	
	this.getPluginInfo = function() { return { id: "plugin_archiver" }; }
	
	this.initialize = function(env) {
		that.env = env;
		that.env.addItemContextProvider(that.getItemContext);
		$.getScript(that.env.pluginUrl("Archiver") + "client/texts_" + that.env.texts().locale + ".js");
	}
	
	this.getItemContext = function(item, details) {
		if (!details["plugin_archiver"] || !details["plugin_archiver"]["action_extract"]) return null;
		
		var extractServiceUrl = details["plugin_archiver"]["action_extract"];
		
		return {
			actions : {
				secondary: [
					{ title: "-" },
					{
						title: that.env.texts().get("plugin_archiver_extractAction"),
						callback: function(item) { that.onExtract(extractServiceUrl, false); }
					}
				]
			}
		}
	}
	
	this.onExtract = function(url, allowOverwrite) {
		var wd = that.env.dialog().showWait(that.env.texts().get("pleaseWait"));
		var params = { overwrite: allowOverwrite };
		
		that.env.service().post(url, params,
			function(result) {
				wd.close();
				that.env.fileview().refresh();
			},
			function(code, error) {
				wd.close();
				if (code == 205) {
					that.env.dialog().showConfirmation({
						title: that.env.texts().get("plugin_archiver_extractFolderAlreadyExistsTitle"),
						message: that.env.texts().get("plugin_archiver_extractFolderAlreadyExistsMessage"),
						on_confirm: function() { that.onExtract(url, true); }
					});
					return;
				}
				alert("Extract error: "+code+"/"+error);
			}
		);
	}
}