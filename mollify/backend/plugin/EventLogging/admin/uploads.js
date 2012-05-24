/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

function MollifyUploadsView() {
	var that = this;
	this.pageUrl = "uploads.html";
	
	this.onLoadView = function() {
		if (!getSession().features["event_logging"]) {
			onError("Event logging not enabled");
			return;
		}
		$("#button-search").click(that.onSearch);
				
		$("#uploads-range-start").datepicker();
		$("#uploads-range-end").datepicker();
		
		$("#uploads-list").jqGrid({        
			datatype: "local",
			multiselect: false,
			autowidth: true,
			height: '100%',
		   	colNames:['ID', 'Time', 'User', 'IP', 'File'],
		   	colModel:[
			   	{name:'id',index:'id', width:60, sortable:true, sorttype:"int"},
		   		{name:'time',index:'time', width:150, sortable:true, formatter:timeFormatter},
				{name:'user',index:'user',width:150, sortable:true, formatter:notNullFormatter},
				{name:'ip',index:'ip',width:150, sortable:true, formatter:notNullFormatter},
				{name:'item',index:'item',width:250, sortable:true, formatter:notNullFormatter}
		   	],
		   	rowNum:9999,
		   	sortname:'item',
		   	sortorder:'asc',
			onSelectRow: function(id){
				that.onEventSelectionChanged();
			}
		});
		
		that.onEventSelectionChanged();
	}
		
	this.onSearch = function() {
		var start = $("#uploads-range-start").val();
		if (start.length > 0) {
			try {
				start = parseDate(start);
			} catch (e) {
				alert("Invalid start date");
				return;
			}
		} else {
			start = null;
		}
		
		var end = $("#uploads-range-end").val();
		if (end.length > 0) {
			try {
				end = parseDate(end);
			} catch (e) {
				alert("Invalid end date");
				return;
			}
		} else {
			end = null;
		}
		
		if (start && end && start > end) {
			alert("Start date cannot be after end date");
			return;
		}

		var item = $("#uploads-item-text").val();
		if (!item || item.length == 0) item = null

		that.lastSearch = {start:start, end:end};
		getUploads(start, end, item, that.onRefreshUploads, onServerError);
	}
	
	this.onRefreshUploads = function(result) {
		that.events = result.events;
		
		var grid = $("#uploads-list");
		grid.jqGrid('clearGridData');

		for(var i=0;i < that.events.length;i++) {
			var event = that.events[i];
			event.time = parseInternalTime(event.time);
			grid.jqGrid('addRowData', i, event);
		}

		that.onEventSelectionChanged();
	}
	
	this.inArray = function(a, o) {
		for (var i=0; i < a.length; i++)
			if (a[i] == o) return true;
		return false;
	}
	
	this.getSelectedEvent = function() {
		return $("#uploads-list").getGridParam("selrow");
	}
	
	this.onEventSelectionChanged = function() {
		var event = that.getSelectedEvent();
		var selected = (event != null);
		event = selected ? that.events[event] : null;
		
		if (!selected) {
			$("#upload-details-data").hide();
			
			if (!that.files)
				$("#upload-details-info").html('<div class="message">Enter search criteria and click "Search"</div>');
			else if (that.files.length == 0)
				$("#upload-details-info").html('<div class="message">No uploads</div>');
			else
				$("#upload-details-info").html('<div class="message">Select file from the list to view details</div>');
		} else {
			$("#upload-details-info").html("");
		}
	}
}

function getUploads(start, end, file, success, fail) {
	var data = {}
	if (start) data["start_time"] = formatInternalTime(start);
	if (end) data["end_time"] = formatInternalTime(end);
	if (file) data["item"] = file;
	data["type"] = "filesystem/upload";
	
	request("POST", 'eventlog/query', success, fail, JSON.stringify(data));
}