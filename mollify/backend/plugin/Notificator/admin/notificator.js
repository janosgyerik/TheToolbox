/**
 * Copyright (c) 2008- Samuli Järvelä
 *
 * All rights reserved. This program and the accompanying materials
 * are made available under the terms of the Eclipse Public License v1.0
 * which accompanies this distribution, and is available at
 * http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
 * this entire header must remain intact.
 */

function NotificatorListView() {
	var that = this;
	this.pageUrl = "notificator.html";
	this.list = null;
	
	this.onLoadView = function() {
		$("#button-add-notification").click(that.addNotification);
		$("#button-remove-notification").click(that.onRemoveNotification);
		$("#button-refresh").click(that.onRefresh);

		$("#notifications-list").jqGrid({        
			datatype: "local",
			multiselect: false,
			autowidth: true,
			height: '100%',
		   	colNames:['ID', 'Name'],
		   	colModel:[
			   	{name:'id',index:'id', width:60, sortable:true, sorttype:"int"},
			   	{name:'name',index:'name',width:150, sortable:true}
		   	],
		   	rowNum:9999,
		   	sortname:'id',
		   	sortorder:'desc',
			onSelectRow: function(id){
				that.onNotificationSelectionChanged();
			}
		});
			
		getEventTypes(that.refreshTypes, onServerError);
	}
	
	this.groupFormatter = function(name, options, u) {
		if (u.is_group == "1") return "Group";
		return "User";
	}
	
	this.refreshTypes = function(types) {
		that.types = types;
		getUsersAndGroups(that.refreshUsersAndGroups, onServerError);
	}
	
	this.refreshUsersAndGroups = function(result) {
		that.usersAndGroups = [];
		that.usersAndGroupsById = {};
		
		for (var i=0; i < result.users.length; i++) {
			user = result.users[i];
			that.usersAndGroups.push(user);
			that.usersAndGroupsById[user.id] = user;
		}
		for (var i=0; i < result.groups.length; i++) {
			group = result.groups[i];
			that.usersAndGroups.push(group);
			that.usersAndGroupsById[group.id] = group;
		}
		that.onRefresh();
	}
	
	this.getUserOrGroup = function(id) {
		return that.usersAndGroupsById[id];
	}
		
	this.onRefresh = function() {
		getNotifications(that.refreshList, onServerError);
	}

	function getEventTypes(success, fail) {
		request("GET", 'events/types', success, fail);
	}
	
	this.refreshList = function(list) {
		that.list = list;
		that.notificationsById = {}
		
		var grid = $("#notifications-list");
		grid.jqGrid('clearGridData');
		
		for (var i=0; i < list.length; i++) {
			var r = list[i];
			
			that.notificationsById[r.id] = r;
			grid.jqGrid('addRowData', r.id, r);
		}
		that.onNotificationSelectionChanged();
	}
	
	this.getSelectedNotification = function() {
		return $("#notifications-list").getGridParam("selrow");
	}

	this.getNotification = function(id) {
		return that.list[id];
	}
	
	this.onNotificationSelectionChanged = function() {
		var n = that.getSelectedNotification();
		var selected = (n != null);
		enableButton("button-remove-notification", selected);
		that.editing = null;
		
		if (!n) {	
			$("#notification-details").html("Select notification from the list");
			if (that.list.length == 0) {
				$("#notification-details").html('<div class="message">Click "Add Notification" to create a new notification</div>');
			} else {
				$("#notification-details").html('<div class="message">Select a notification from the list to view details</div>');
			}
			return;
		}

		getNotificationDetails(n, that.showNotificationDetails, onServerError);
	}
	
	this.showNotificationDetails = function(d) {
		that.editing = d;
		
		var html = $.template("<div id='notification-details-info' class='details-info'><h1>Notification ${id}</h1></div>").apply(d);
		html += "<div id='notification-details-data' class='details-data'>";
		
		html += that.detailSection("name", "Name", that.detailValue('name', d.name), "edit-name");
		html += that.detailSection("event", "Events", that.eventsSection(d), "edit-events");
		html += that.detailSection("message", "Message", that.messageSection(d), "edit-message");
		html += that.detailSection("recipient", "Recipients", that.recipientsSection(d), "edit-recipients");
		
		html += "</div>";
		
		$("#notification-details").html(html);
		
		$("#edit-name").click(that.onEditName);
		$("#edit-message").click(that.onEditMessage);
		$("#edit-events").click(that.onEditEvents);
		$("#edit-recipients").click(that.onEditRecipients);
	}
	
	this.onEditName = function() {
		that.addEditNotification(that.editing.id, that.editing.name);
	}

	this.onEditMessage = function() {
		that.editNotificationMessage(that.editing.id, that.editing.message_title, that.editing.message);
	}

	this.onEditEvents = function() {
		that.editNotificationEvents(that.editing.id, that.editing.events);
	}

	this.onEditRecipients = function() {
		that.editNotificationRecipients(that.editing.id, that.editing.recipients);
	}
		
	this.detailSection = function(id, title, html, editId) {
		if (editId) {
			return $.template("<div id='notification-details-section-${id}' class='notification-details-section'><div class='title'>${title}<a id='${editId}'>Edit</a></div><div class='content'>${html}</div></div>").apply({id:id, title:title, html:html, editId:editId});
		}
		return $.template("<div id='notification-details-section-${id}' class='notification-details-section'><div class='title'>${title}</div><div class='content'>${html}</div></div>").apply({id:id, title:title, html:html});
	}

	this.detailValue = function(id, value) {
		return $.template("<div class='notification-details-value'>${value}</div>").apply({id:id, value:value});
	}
	
	this.messageSection = function(d) {
		var v = {
			title: (d.message_title != null && d.message_title.length > 0) ? d.message_title : '<i>No title</i>',
			message: (d.message != null && d.message.length > 0) ? d.message : '<i>No message</i>',
		}
		return $.template("<div class='notification-details-value notification-message'><div class='title'>${title}</div><div class='message'>${message}</div></div>").apply(v);
	}

	this.eventsSection = function(d) {
		if (d.events.length == 0) return "<div class='notification-details-value notification-events'><i>Any event</i></div>";

		var html = "<div class='notification-details-value notification-events'><table id='notification-event-list' class='details-table'><tr><th class='col-event-name'>Event</th></tr>";
		for (var i=0; i < d.events.length; i++) {
			html += "<tr><td class='col-event-name'>"+that.types[d.events[i]]+"</td></tr>";
		}
		return html + "</table></div>"
	}

	this.recipientsSection = function(d) {
		if (d.recipients.length == 0) return "<div class='notification-details-value notification-recipients'><i>No recipients</i></div>";
		
		var html = "<div class='notification-details-value notification-recipients'><table id='notification-recipient-list' class='details-table'><tr><th class='col-recipient-name'>Name</th><th class='col-recipient-email'>Email</th></tr>";
		for (var i=0; i < d.recipients.length; i++) {
			var u = that.getUserOrGroup(d.recipients[i]);
			html += "<tr><td class='col-recipient-name'>"+u.name+((u.is_group == "1") ? " (Group)" : "")+"</td><td class='col-recipient-email'>"+((u.is_group == "1") ? "-" : u.email)+"</td></tr>";
		}
		return html + "</table></div>"
	}
	
	function timeFormatter(time, options, obj) {
		return formatDateTime(time);
	}
	
	function notNullFormatter(o, options, obj) {
		if (o == null) return '';
		return o;
	}
	
	this.addNotification = function() {
		that.addEditNotification(null, null);
	}
	
	this.addEditNotification = function(id, name) {
		if (!that.addEditNotificationDialogInit) {
			that.addEditNotificationDialogInit = true;

			$("#add-edit-notification-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 350,
				modal: true,
				resizable: false,
				title: "",
				buttons: {}
			});
		}
		
		var cb = function() {
				var newName = $("#notification-name").val();
				if (newName.length == 0) return;
				
				if (id) {
					editNotification(id, {name: newName}, function() {
						$("#add-edit-notification-dialog").dialog('close');
						that.onNotificationSelectionChanged();
					}, onServerError);
				} else {
					addNotification(newName, function() {
						$("#add-edit-notification-dialog").dialog('close');
						that.onRefresh();
					}, onServerError);
				}
			};
		
		var buttons = {}
		if (id) {
			buttons.Edit = cb;
			$("#add-edit-notification-dialog").dialog('option', 'title', "Edit Notification Name");
		} else {
			buttons.Add = cb;
			$("#add-edit-notification-dialog").dialog('option', 'title', "Add Notification");
		}
		
		buttons.Cancel = function() {
			$(this).dialog('close');
		}
		
		$("#add-edit-notification-dialog").dialog('option', 'buttons', buttons);
		$("#notification-name").val(name == null ? "" : name);
		$("#add-edit-notification-dialog").dialog('open');
	}
	
	this.editNotificationMessage = function(id, title, message) {
		if (!that.editNotificationMessageDialogInit) {
			that.editNotificationMessageDialogInit = true;

			$("#edit-notification-message-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 650,
				modal: true,
				resizable: false,
				title: "Edit Notification Message",
				buttons: {}
			});
		}
		
		var buttons = {
			Edit: function() {
				var newTitle = $("#notification-message-title").val();
				if (newTitle.length == 0) return;
				var newMessage = $("#notification-message").val();
				if (newMessage.length == 0) return;
				
				editNotification(id, {message_title: newTitle, message: newMessage}, function() {
					$("#edit-notification-message-dialog").dialog('close');
					that.onNotificationSelectionChanged();
				}, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#edit-notification-message-dialog").dialog('option', 'buttons', buttons);
		$("#notification-message-title").val(title);
		$("#notification-message").val(message);
		$("#edit-notification-message-dialog").dialog('open');
	}

	this.editNotificationEvents = function(id, events) {
		if (!that.editNotificationEventsDialogInit) {
			that.editNotificationEventsDialogInit = true;

			$("#notification-events-list").jqGrid({        
				datatype: "local",
				multiselect: true,
				autowidth: true,
				height: '150px',
			   	colNames:['Event'],
			   	colModel:[
				   	{name:'name',index:'name',width:250, sortable:true},
			   	],
			   	rowNum:9999,
			   	sortname:'name',
			   	sortorder:'desc'
			});
	
			$("#notification-events-available-list").jqGrid({        
				datatype: "local",
				autowidth: true,
				multiselect: true,
			   	colNames:['Event'],
			   	colModel:[
				   	{name:'name',index:'name',width:250, sortable:true},
			   	],
			   	rowNum:9999,
			   	sortname:'name',
			   	sortorder:'desc'
			});
		
			$("#edit-notification-events-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 600,
				modal: true,
				resizable: true,
				title: "Events",
				buttons: {}
			});
		}
		
		var buttons = {
			OK: function() {
				var ids = $("#notification-events-list").jqGrid('getDataIDs');
				
				editNotification(id, {events: ids}, function() {
					$("#edit-notification-events-dialog").dialog('close');
					that.onNotificationSelectionChanged();
				}, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		var addRemoveEvent = function(ids, remove) {
			var selected = $("#notification-events-list");
			var available = $("#notification-events-available-list");

			for (var i=0; i < ids.length; i++) {
				var t = ids[i];
				
				if (remove) {
					selected.jqGrid('delRowData', t);
				} else {
					if (!inArray(t, selected.jqGrid('getDataIDs')))
						selected.jqGrid('addRowData', t, {"id": t, "name": that.types[t]});
				}
			}
		}
		
		$("#button-add-notification-event").click(function(){
			var sel = getValidSelections($("#notification-events-available-list").getGridParam("selarrrow"));
			if (sel.length < 1) return;
			addRemoveEvent(sel, false);
		});

		$("#button-remove-notification-event").click(function(){
			var sel = getValidSelections($("#notification-events-list").getGridParam("selarrrow"));
			if (sel.length < 1) return;
			addRemoveEvent(sel, true);
		});
		
		$("#edit-notification-events-dialog").dialog('option', 'buttons', buttons);
		
		var selected = $("#notification-events-list");
		selected.jqGrid('clearGridData');
		
		var available = $("#notification-events-available-list");
		available.jqGrid('clearGridData');
		
		for (var t in that.types) {
			var o = {"id": t, "name": that.types[t]};

			if (inArray(t,events))
				selected.jqGrid('addRowData', t, o);

			available.jqGrid('addRowData', t, o);
		}
		$("#edit-notification-events-dialog").dialog('open');
	}

	this.editNotificationRecipients = function(id, recipients) {
		if (!that.editNotificationRecipientsDialogInit) {
			that.editNotificationRecipientsDialogInit = true;

			$("#notification-recipients-list").jqGrid({        
				datatype: "local",
				multiselect: true,
				autowidth: true,
				height: '150px',
			   	colNames:['Name', 'Email', ''],
			   	colModel:[
				   	{name:'name',index:'name',width:200, sortable:true},
				   	{name:'email',index:'email',width:150, sortable:true},
				   	{name:'is_group',index:'is_group', width:50, sortable:true, formatter:that.groupFormatter}
			   	],
			   	sortname:'name',
			   	sortorder:'desc'
			});
	
			$("#notification-recipients-available-list").jqGrid({        
				datatype: "local",
				autowidth: true,
				multiselect: true,
			   	colNames:['Name', 'Email', ''],
			   	colModel:[
				   	{name:'name',index:'name',width:200, sortable:true},
				   	{name:'email',index:'email',width:150, sortable:true},
				   	{name:'is_group',index:'is_group', width:50, sortable:true, formatter:that.groupFormatter}
			   	],
			   	sortname:'name',
			   	sortorder:'desc'
			});
		
			$("#edit-notification-recipients-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 800,
				modal: true,
				resizable: true,
				title: "Recipients",
				buttons: {}
			});
		}
		
		var buttons = {
			OK: function() {
				var ids = $("#notification-recipients-list").jqGrid('getDataIDs');
				
				editNotification(id, {recipients: ids}, function() {
					$("#edit-notification-recipients-dialog").dialog('close');
					that.onNotificationSelectionChanged();
				}, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		var addRemoveRecipient = function(ids, remove) {
			var selected = $("#notification-recipients-list");
			var available = $("#notification-recipients-available-list");

			for (var i=0; i < ids.length; i++) {
				var u = ids[i];
				
				if (remove) {
					selected.jqGrid('delRowData', u);
				} else {
					if (!inArray(u, selected.jqGrid('getDataIDs')))
						selected.jqGrid('addRowData', u, that.getUserOrGroup(u));
				}
			}
		}
		
		$("#button-add-notification-recipient").click(function(){
			var sel = getValidSelections($("#notification-recipients-available-list").getGridParam("selarrrow"));
			if (sel.length < 1) return;
			addRemoveRecipient(sel, false);
		});

		$("#button-remove-notification-recipient").click(function(){
			var sel = getValidSelections($("#notification-recipients-list").getGridParam("selarrrow"));
			if (sel.length < 1) return;
			addRemoveRecipient(sel, true);
		});
		
		$("#edit-notification-recipients-dialog").dialog('option', 'buttons', buttons);
		
		var selected = $("#notification-recipients-list");
		selected.jqGrid('clearGridData');
		
		var available = $("#notification-recipients-available-list");
		available.jqGrid('clearGridData');
		
		for (var i=0; i < that.usersAndGroups.length; i++) {
			var u = that.usersAndGroups[i];

			if (inArray(u.id, recipients))
				selected.jqGrid('addRowData', u.id, u);

			available.jqGrid('addRowData', u.id, u);
		}
		$("#edit-notification-recipients-dialog").dialog('open');
	}
		
	this.onRemoveNotification = function() {
		var id = that.getSelectedNotification();
		if (id == null) return;
		removeNotification(id, that.onRefresh, onServerError);
	}
}

function getNotifications(success, fail) {
	request("GET", 'notificator/list/', success, fail);
}

function getNotificationDetails(id, success, fail) {
	request("GET", 'notificator/list/'+id, success, fail);
}

function addNotification(name, success, fail) {
	var data = JSON.stringify({name:name});
	request("POST", 'notificator/list/', success, fail, data);
}

function editNotification(id, prop, success, fail) {
	var data = JSON.stringify(prop);
	request("PUT", 'notificator/list/'+id, success, fail, data);
}

function removeNotification(id, success, fail) {
	request("DELETE", 'notificator/list/'+id, success, fail, {});
}