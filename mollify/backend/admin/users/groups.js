/**
	Copyright (c) 2008- Samuli Järvelä

	All rights reserved. This program and the accompanying materials
	are made available under the terms of the Eclipse Public License v1.0
	which accompanies this distribution, and is available at
	http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	this entire header must remain intact.
*/

function MollifyUserGroupsConfigurationView() {
	var that = this;
	
	this.pageUrl = "users/groups.html";
	this.users = null;
	this.groups = null;
	this.groupUsers = null;
		
	this.onLoadView = onLoadView;
	
	function onLoadView() {
		loadScript("users/common.js", that.init);
	}
	
	this.init = function() {		
		$("#button-add-group").click(that.openAddGroup);
		$("#button-remove-group").click(that.onRemoveGroup);
		$("#button-edit-group").click(that.onEditGroup);
		$("#button-refresh-groups").click(that.refresh);
		
		$("#button-add-group-folder").click(that.openAddGroupFolder);
		$("#button-edit-group-folder").click(that.openEditGroupFolder);
		$("#button-remove-group-folder").click(that.onRemoveGroupFolder);
		$("#button-refresh-group-folders").click(that.refreshGroupFolders);

		$("#groups-list").jqGrid({        
			datatype: "local",
			multiselect: false,
			autowidth: true,
			height: '100%',
		   	colNames:['ID', 'Name', 'Description'],
		   	colModel:[
			   	{name:'id',index:'id', width:60, sortable:true, sorttype:"int"},
		   		{name:'name',index:'name', width:200, sortable:true},
		   		{name:'description',index:'description', width:300, sortable:true},
		   	],
		   	rowNum:9999,
		   	sortname:'id',
		   	sortorder:'asc',
			onSelectRow: function(id){
				that.onGroupSelectionChanged();
			}
		});

		$("#button-add-group-users").click(that.openAddGroupUsers);
		$("#button-remove-group-users").click(that.onRemoveGroupUsers);
		$("#button-refresh-group-users").click(that.refreshGroupUsers);

		$("#group-users-list").jqGrid({        
			datatype: "local",
			multiselect: true,
			autowidth: false,
			height: '100%',
		   	colNames:['ID', 'Name'],
		   	colModel:[
			   	{name:'id', index:'id', width:10, sortable:true, sorttype:"int"},
		   		{name:'name', index:'name', width:10, sortable:true}
		   	],
		   	rowNum:9999,
		   	sortname:'id',
		   	sortorder:'asc',
			onSelectRow: function(id){
				that.onGroupUserSelectionChanged();
			}
		});

		$("#button-add-group-folder").click(that.openAddGroupFolder);
		$("#button-edit-group-folder").click(that.openEditGroupFolder);
		$("#button-remove-group-folder").click(that.onRemoveGroupFolder);

		$("#group-folders-list").jqGrid({        
			datatype: "local",
			multiselect: false,
			autowidth: false,
			height: '100%',
		   	colNames:['ID', 'Name', 'Default Name', 'Path'],
		   	colModel:[
			   	{name:'id',index:'id', width:20, sortable:true, sorttype:"int"},
		   		{name:'name',index:'name', width:150, sortable:true, formatter:that.folderNameFormatter},
		   		{name:'default_name',index:'name', width:150, sortable:true, formatter:that.defaultFolderNameFormatter},
				{name:'path',index:'path',width:200, sortable:true},
		   	],
		   	rowNum:9999,
		   	sortname:'id',
		   	sortorder:'asc',
			onSelectRow: function(id){
				that.onGroupFolderSelectionChanged();
			}
		});
		
		$("#add-users-list").jqGrid({        
			datatype: "local",
			autowidth: false,
			multiselect: true,
		   	colNames:['ID', 'Name'],
		   	colModel:[
			   	{name:'id',index:'id', width:60, sortable:true, sorttype:"int"},
		   		{name:'name',index:'name', width:200, sortable:true}
		   	],
		   	rowNum:9999,
		   	sortname:'id',
		   	sortorder:'asc'
		});
		
		that.refresh();
		
		$(window).resize(function() {
			if (!that.getSelectedGroup()) return;
			that.resizeGrids();
		});
	}
	
	this.resizeGrids = function() {
		$("#group-users-list").fluidGrid({ example:"#group-users-section", offset:-10 });
		$("#group-folders-list").fluidGrid({ example:"#group-folders-section", offset:-10 });
	}
	
	this.getUserGroup = function(id) {
		return that.groups[id];
	}

	this.getGroupUser = function(id) {
		return that.groupUsers[id];
	}

	this.getSelectedGroup = function() {
		return $("#groups-list").getGridParam("selrow");
	}

	this.getSelectedGroupUsers = function() {
		return getValidSelections($("#group-users-list").getGridParam("selarrrow"));
	}

	this.getSelectedGroupFolder = function() {
		return $("#group-folders-list").getGridParam("selrow");
	}
	
	this.refresh = function() {
		getUserGroups(function(groups) { that.refreshGroups(groups); getFolders(that.refreshFolders, onServerError) }, onServerError);
	}
	
	this.refreshGroups = function(groups) {
		that.groups = {};

		var grid = $("#groups-list");
		grid.jqGrid('clearGridData');
		
		for(var i=0;i < groups.length;i++) {
			var group = groups[i];
			that.groups[group.id] = group;
			grid.jqGrid('addRowData', group.id, group);
		}
		
		that.onGroupSelectionChanged();
		
		getUsers(that.refreshUsers, onServerError);
	}
	
	this.refreshUsers = function(users) {
		that.users = {};
		
		for (var i=0; i < users.length; i++) {
			user = users[i];
			that.users[user.id] = user;
		}
	}
	
	this.refreshFolders = function(folders) {
		that.folders = {};
		
		for (var i=0; i < folders.length; i++) {
			folder = folders[i];
			that.folders[folder.id] = folder;
		}
	}
	
	this.refreshGroupUsers = function() {
		var id = $("#groups-list").getGridParam("selrow");
		if (!id) return;
		
		getGroupUsers(id, that.onRefreshGroupUsers, onServerError);
	}
	
	this.onRefreshGroupUsers = function(groupUsers) {
		that.groupUsers = {};
		
		var grid = $("#group-users-list");
		grid.jqGrid('clearGridData');

		for (var i=0; i < groupUsers.length; i++) {
			var groupUser = groupUsers[i];
			that.groupUsers[groupUser.id] = groupUser;
			grid.jqGrid('addRowData', groupUser.id, groupUser);
		}
		
		that.onGroupUserSelectionChanged();
	}

	this.refreshGroupFolders = function() {
		var id = $("#groups-list").getGridParam("selrow");
		if (!id) return;

		getUserFolders(id, that.onRefreshGroupFolders, onServerError);
	}
	
	this.onRefreshGroupFolders = function(folders) {
		that.groupFolders = {};
		
		var grid = $("#group-folders-list");
		grid.jqGrid('clearGridData');

		for (var i=0; i < folders.length; i++) {
			var folder = folders[i];
			that.groupFolders[folder.id] = folder;
			grid.jqGrid('addRowData', folder.id, folder);
		}
		
		that.onGroupFolderSelectionChanged();
	}

	this.refreshGroupDetails = function() {
		var id = $("#groups-list").getGridParam("selrow");
		if (!id) return;
		
		getGroupUsers(id, function(users) {
			that.onRefreshGroupUsers(users);
			getUserFolders(id, that.onRefreshGroupFolders, onServerError);
		}, onServerError);		
	}
	
	this.onGroupSelectionChanged = function() {
		var group = that.getSelectedGroup();
		var selected = (group != null);
		if (selected) group = that.getUserGroup(group);
		
		that.groupUsers = null;
		
		enableButton("button-remove-group", selected);
		enableButton("button-edit-group", selected);
		
		if (that.groups.length == 0) {
			$("#group-details-info").html('<div class="message">Click "Add Group" to create a new user group</div>');
		} else {
			if (selected) {
				$("#group-users-list").jqGrid('setGridWidth', $("#group-details").width(), true);
				$("#group-details-info").html("<h1>Group '"+group.name+"'</h1>");
				
				that.refreshGroupDetails();
			} else {
				$("#group-details-info").html('<div class="message">Select a group from the list to view details</div>');
			}
		}
		
		if (!selected) {
			$("#group-details-data").hide();
		} else {
			$("#group-details-data").show();
			that.resizeGrids();
		}
	}

	this.onGroupUserSelectionChanged = function() {
		var selected = (that.getSelectedGroupUsers().length > 0);
		enableButton("button-remove-group-users", selected);
	}

	this.onGroupFolderSelectionChanged = function() {
		var folder = that.getSelectedGroupFolder();
		var selected = (folder != null);
		
		enableButton("button-edit-group-folder", selected);
		enableButton("button-remove-group-folder", selected);
	}

	this.validateGroupData = function() {
		$("#group-dialog > .form-data").removeClass("invalid");
	
		var result = true;
		if ($("#group-name-field").val().length == 0) {
			$("#group-name").addClass("invalid");
			result = false;
		}
		return result;
	}

	this.openAddGroup = function() {
		that.openAddEditGroup(null);
	}
	
	this.openAddEditGroup = function(id) {
		if (!that.addEditGroupDialogInit) {
			that.addEditGroupDialogInit = true;
					
			$("#group-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 270,
				modal: true,
				resizable: false,
				buttons: {}
			});
		}
		
		var buttons = {}

		var action = function() {
			if (!that.validateGroupData()) return;
			
			var name = $("#group-name-field").val();
			var desc = $("#group-description-field").val();
			
			onSuccess = function() {
				$("#group-dialog").dialog('close');
				that.refresh();
			}

			if (id)
				editUserGroup(id, name, desc, onSuccess, onServerError);
			else
				addUserGroup(name, desc, onSuccess, onServerError);
		}
		
		if (id)
			buttons["Edit"] = action;
		else
			buttons["Add"] = action;
		
		buttons["Cancel"] = function() {
			$(this).dialog('close');
		}
		
		$("#group-dialog").dialog('option', 'buttons', buttons);
		
		if (id) {
			var group = that.getUserGroup(id);
			$("#group-name-field").val(group.name);
			$("#group-description-field").val(group.description);
			$("#group-dialog").dialog('option', 'title', 'Edit Group');
		} else {
			$("#group-name-field").val("");
			$("#group-description-field").val("");
			$("#group-dialog").dialog('option', 'title', 'Add Group');
		}
		
		$("#group-dialog").dialog('open');
	}
	
	this.onRemoveGroup = function() {
		var id = that.getSelectedGroup();
		if (id == null) return;
		removeUserGroup(id, that.refresh, onServerError);
	}
	
	this.onEditGroup = function() {
		var id = that.getSelectedGroup();
		if (id == null) return;
		that.openAddEditGroup(id);
	}
	
	this.openAddGroupUsers = function() {
		if (that.users == null) return;
		
		var availableUsers = that.getAvailableGroupUsers();
		if (availableUsers.length == 0) {
			alert("No more users available");
			return;
		}
		
		var grid = $("#add-users-list");
		grid.jqGrid('clearGridData');
		
		for(var i=0;i < availableUsers.length;i++) {
			grid.jqGrid('addRowData', availableUsers[i].id, availableUsers[i]);
		}

		if (!that.addUserDialogInit) {
			that.addUserDialogInit = true;
					
			$("#add-group-users-dialog").dialog({
				bgiframe: true,
				height: 'auto',
				width: 330,
				modal: true,
				resizable: true,
				autoOpen: false,
				title: "Add Users to Group"
			});
		}
		
		var buttons = {
			Add: function() {
				var sel = getValidSelections($("#add-users-list").getGridParam("selarrrow"));
				if (sel.length == 0) return;

				var onSuccess = function() {
					$("#add-group-users-dialog").dialog('close');
					that.refreshGroupUsers();
				}
				
				addGroupUsers(that.getSelectedGroup(), sel, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#add-group-users-dialog").dialog('option', 'buttons', buttons);
		$("#add-group-users-dialog").dialog('open');
	}
	
	this.getAvailableGroupUsers = function() {
		var result = [];
		for (id in that.users) {
			if (!that.groupUsers[id])
				result.push(that.users[id]);
		}
		return result;
	}

	this.onRemoveGroupUsers = function() {
		var sel = that.getSelectedGroupUsers();
		if (sel.length == 0) return;
		removeGroupUsers(that.getSelectedGroup(), sel, that.refreshGroupUsers, onServerError);
	}
	
	this.openAddGroupFolder = function() {
		if (that.folders == null) return;
		
		var availableFolders = that.getAvailableFolders();
		if (availableFolders.length == 0) {
			alert("No more folders available");
			return;
		}
		
		$("#published-folder-list").html('');
		var item = $.template('<option value="${id}">${name} (${path})</option>');
		
		for(var i=0;i < availableFolders.length;i++) {
			$("#published-folder-list").append(item, availableFolders[i]);
		}
				
		var onFolderOrDefaultChanged = function() {
			var sel = $("#published-folder-list").val();
			$("#published-folder-default-name").val(that.folders[sel].name);
			var useDefault = $("#use-default-folder-name").attr('checked');
			
			if (!useDefault) {
				$("#published-folder-name").removeAttr("disabled");
				$("#folder-name").show();
			} else {
				$("#folder-name").removeClass("invalid");
				$("#folder-name").hide();
				$("#published-folder-name").val(that.folders[sel].name);
				$("#published-folder-name").attr("disabled", true);
			}
		}

		if (!that.addFoldersDialogInit) {
			that.addFoldersDialogInit = true;
			
			$("#add-group-folder-dialog").dialog({
				bgiframe: true,
				height: 'auto',
				width: 270,
				modal: true,
				resizable: true,
				autoOpen: false,
				title: "Add Group Folder"
			});
			
			$("#published-folder-list").change(onFolderOrDefaultChanged);
			$("#use-default-folder-name").click(onFolderOrDefaultChanged);
		}
		
		var buttons = {
			Add: function() {
				if (!that.validateFolder(false)) return;
				
				var useDefault = $("#use-default-folder-name").attr('checked');
				var folder = $("#published-folder-list").val();
				var name = useDefault ? null : $("#published-folder-name").val();
				
				var onSuccess = function() {
					$("#add-group-folder-dialog").dialog('close');
					that.refreshGroupFolders();
				}
				
				addUserFolder(that.getSelectedGroup(), folder, name, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#add-group-folder-dialog").dialog('option', 'buttons', buttons);
		$("#use-default-folder-name").attr('checked', true);
		$("#use-default-folder-name").click(onFolderOrDefaultChanged);
		onFolderOrDefaultChanged();
		$("#add-group-folder-dialog").dialog('open');
	}
	
	this.openEditGroupFolder = function() {
		if (that.folders == null) return;
		var selected = that.getSelectedGroupFolder();
		if (selected == null) return;
		selected = that.groupFolders[selected];
			
		var onFolderOrDefaultChanged = function() {
			var sel = that.getSelectedGroupFolder();
			$("#edit-published-folder-default-name").val(that.folders[sel].name);
			var useDefault = $("#edit-use-default-folder-name").attr('checked');
			
			if (!useDefault) {
				$("#edit-published-folder-name").removeAttr("disabled");
				$("#edit-folder-name").show();
			} else {
				$("#edit-folder-name").removeClass("invalid");
				$("#edit-folder-name").hide();
	
				$("#edit-published-folder-name").val(that.folders[sel].name);
				$("#edit-published-folder-name").attr("disabled", true);
			}
		}

		if (!that.editFolderDialogInit) {
			that.editFolderDialogInit = true;
			
			$("#edit-group-folder-dialog").dialog({
				bgiframe: true,
				height: 200,
				height: 'auto',
				modal: true,
				resizable: true,
				autoOpen: false,
				title: "Edit Group Folder",
				buttons: buttons
			});
		}
		
		var buttons = {
			Edit: function() {
				if (!that.validateFolder(true)) return;
				
				var useDefault = $("#edit-use-default-folder-name").attr('checked');
				var name = useDefault ? null : $("#edit-published-folder-name").val();
				
				var onSuccess = function() {
					$("#edit-group-folder-dialog").dialog('close');
					that.refreshGroupFolders();
				}
				
				editUserFolder(that.getSelectedGroup(), that.getSelectedGroupFolder(), name, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#edit-group-folder-dialog").dialog('option', 'buttons', buttons);
		$("#published-folder-path").val(selected.path);
		$("#edit-use-default-folder-name").attr('checked', (selected.name == null));
		
		$("#edit-use-default-folder-name").click(onFolderOrDefaultChanged);
		onFolderOrDefaultChanged();
		if (selected.name) $("#edit-published-folder-name").val(selected.name);
		
		$("#edit-group-folder-dialog").dialog('open');
	}

	this.onRemoveGroupFolder = function() {
		var id = that.getSelectedGroupFolder();
		if (id == null) return;
		removeUserFolder(that.getSelectedGroup(), id, that.refreshGroupFolders, onServerError);
	}
	
	this.validateFolder = function(edit) {
		if (edit) $("#edit-folder-name").removeClass("invalid");
		else $("#folder-name").removeClass("invalid");
		
		var useDefault = $(edit ? "#edit-use-default-folder-name" : "#use-default-folder-name").attr('checked');
		var value = $(edit ? "#edit-published-folder-name" : "#published-folder-name").val();
		
		if (!useDefault && value.length == 0) {
			$(edit ? "#edit-published-folder-name" : "#published-folder-name").addClass("invalid");
			return false;
		}
		return true;
	}
	
	this.getAvailableFolders = function() {
		var result = [];
		for (id in that.folders) {
			if (!that.groupFolders[id])
				result.push(that.folders[id]);
		}
		return result;
	}
}