/**
	Copyright (c) 2008- Samuli Järvelä

	All rights reserved. This program and the accompanying materials
	are made available under the terms of the Eclipse Public License v1.0
	which accompanies this distribution, and is available at
	http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	this entire header must remain intact.
*/

function MollifyUsersConfigurationView() {
	var that = this;
	
	this.pageUrl = "users/users.html";
	this.users = null;
	this.groups = null;
	this.folders = null;
	this.userGroups = null;
	this.userFolders = null;
		
	this.onLoadView = onLoadView;
	
	function onLoadView() {
		loadScript("users/common.js", that.init);
	}
	
	this.init = function() {
		$("#button-add-user").click(that.openAddUser);
		$("#button-remove-user").click(that.onRemoveUser);
		$("#button-edit-user").click(that.onEditUser);
		$("#button-change-password").click(that.onChangePassword);
		$("#button-refresh-users").click(that.refresh);

		$("#button-add-user-groups").click(that.openAddUserGroups);
		$("#button-remove-user-group").click(that.onRemoveUserGroup);
		$("#button-refresh-user-groups").click(that.refreshUserGroups);

		$("#button-add-user-folder").click(that.openAddUserFolder);
		$("#button-edit-user-folder").click(that.openEditUserFolder);
		$("#button-remove-user-folder").click(that.onRemoveUserFolder);
		$("#button-refresh-user-folders").click(that.refreshUserFolders);
		
		that.auth_methods = getSession()['authentication_methods'];
		that.auth_options = that.auth_methods.length > 1;
		that.default_auth_method = getSession()['authentication_methods'][0].toLowerCase();
		
		var cols = that.auth_options ? [
		   	{name:'id',index:'id', width:20, sortable:true, sorttype:"int"},
	   		{name:'name',index:'name', width:200, sortable:true},
	   		{name:'email',index:'email', width:150, sortable:true},
	   		{name:'auth',index:'auth', width:75, sortable:true, formatter:that.authFormatter},
			{name:'permission_mode',index:'permission_mode',width:75, sortable:true, formatter:permissionModeFormatter},
			{name:'expiration',index:'expiration', width:200, sortable:true, formatter:dateFormatter}
	   	] : [
		   	{name:'id',index:'id', width:20, sortable:true, sorttype:"int"},
	   		{name:'name',index:'name', width:200, sortable:true},
	   		{name:'email',index:'email', width:200, sortable:true},
			{name:'permission_mode',index:'permission_mode',width:150, sortable:true, formatter:permissionModeFormatter},
			{name:'expiration',index:'expiration', width:200, sortable:true, formatter:dateFormatter}
	   	];
	   	var colNames = that.auth_options ? ['ID', 'Name', 'E-mail', 'Authentication', 'Permission Mode', "Expiration"] : ['ID', 'Name', 'E-mail', 'Permission Mode', "Expiration"];
	   	
		$("#users-list").jqGrid({        
			datatype: "local",
			multiselect: false,
			autowidth: true,
			height: '100%',
		   	colNames: colNames,
		   	colModel: cols,
		   	sortname:'id',
		   	sortorder:'asc',
		   	rowNum:9999,
			onSelectRow: function(id){
				that.onUserSelectionChanged();
			}
		});

		$("#user-groups-list").jqGrid({        
			datatype: "local",
			multiselect: false,
			autowidth: false,
			height: '100%',
		   	colNames:['ID', 'Name', 'Description'],
		   	colModel:[
			   	{name:'id',index:'id', width:20, sortable:true, sorttype:"int"},
		   		{name:'name',index:'name', width:200, sortable:true},
		   		{name:'description',index:'description', width:300, sortable:true},
		   	],
		   	rowNum:9999,
		   	sortname:'id',
		   	sortorder:'asc',
			onSelectRow: function(id){
				that.onUserGroupSelectionChanged();
			}
		});

		$("#user-folders-list").jqGrid({        
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
				that.onUserFolderSelectionChanged();
			}
		});

		that.refresh();
	}
		
	this.folderNameFormatter = function(name, options, folder) {
		if (!folder.name) return folder['default_name'];
		return folder.name;
	}

	this.defaultFolderNameFormatter = function(name, options, folder) {
		if (!folder.name) return "";
		return folder['default_name'];
	}

	this.authFormatter = function(name, options, user) {
		var a = user.auth;
		if (!a) return "Default ("+that.formatAuth(that.auth_methods[0])+")";
		return that.formatAuth(a);
	}
	
	this.formatAuth = function(a) {
		var u = a.toUpperCase();
		if (u == 'PW') return "Password";
		if (u == 'REMOTE') return "Remote";
		if (u == 'LDAP') return "LDAP";
		return "";
	}

	this.refresh = function() {
		getUsers(refreshUsers, onServerError);
	}
	
	function refreshUsers(users) {
		that.users = {};

		var grid = $("#users-list");
		grid.jqGrid('clearGridData');
		
		for(var i=0;i < users.length;i++) {
			var user = users[i];
			that.users[user.id] = user;
			if (user.expiration) user.expiration = parseInternalTime(user.expiration);
			grid.jqGrid('addRowData', user.id, user);
		}
		
		that.onUserSelectionChanged();
		
		getUserGroups(function(groups) { that.refreshGroups(groups); getFolders(that.refreshFolders, onServerError) }, onServerError);
		
		$(window).resize(function() {
			if (!that.getSelectedUser()) return;
			that.resizeGrids();
		});
	}
	
	this.resizeGrids = function() {
		$("#user-groups-list").fluidGrid({ example:"#user-groups-section", offset:-10 });
		$("#user-folders-list").fluidGrid({ example:"#user-folders-section", offset:-10 });
	}
	
	this.refreshGroups = function(groups) {
		that.groups = {};
		
		for (var i=0; i < groups.length; i++) {
			group = groups[i];
			that.groups[group.id] = group;
		}
	}

	this.refreshFolders = function(folders) {
		that.folders = {};
		
		for (var i=0; i < folders.length; i++) {
			folder = folders[i];
			that.folders[folder.id] = folder;
		}
	}
		
	this.refreshUserDetails = function() {
		var user = that.getSelectedUser();
		
		getUsersGroups(user, function(groups) {
			that.onRefreshUserGroups(groups);
			getUserFolders(user, that.onRefreshUserFolders, onServerError);
		}, onServerError);
	}
	
	this.refreshUserGroups = function() {
		getUsersGroups(that.getSelectedUser(), that.onRefreshUserGroups, onServerError);
	}
	
	this.onRefreshUserGroups = function(groups) {
		that.userGroups = {};
		
		var grid = $("#user-groups-list");
		grid.jqGrid('clearGridData');

		for (var i=0; i < groups.length; i++) {
			var group = groups[i];
			that.userGroups[group.id] = group;
			grid.jqGrid('addRowData', group.id, group);
		}
				
		that.onUserGroupSelectionChanged();
	}

	this.refreshUserFolders = function() {
		getUserFolders(that.getSelectedUser(), that.onRefreshUserFolders, onServerError);
	}

	this.onRefreshUserFolders = function(folders) {
		that.userFolders = {};
		
		var grid = $("#user-folders-list");
		grid.jqGrid('clearGridData');

		for (var i=0; i < folders.length; i++) {
			var folder = folders[i];
			that.userFolders[folder.id] = folder;
			grid.jqGrid('addRowData', folder.id, folder);
		}
				
		that.onUserFolderSelectionChanged();
	}
	
	this.getSelectedUser = function() {
		return $("#users-list").getGridParam("selrow");
	}

	this.getSelectedUserGroup = function() {
		return $("#user-groups-list").getGridParam("selrow");
	}

	this.getSelectedUserFolder = function() {
		return $("#user-folders-list").getGridParam("selrow");
	}
	
	this.getUser = function(id) {
		return that.users[id];
	}
	
	this.onUserSelectionChanged = function() {
		var user = that.getSelectedUser();
		var selected = (user != null);
		if (selected) user = that.getUser(user);
		var changePwEnabled = false;
		
		if (selected && ((user.auth && user.auth == 'PW') || (!user.auth && that.default_auth_method == 'pw')))
			changePwEnabled = true;
		
		enableButton("button-remove-user", selected);
		enableButton("button-edit-user", selected);
		enableButton("button-change-password", changePwEnabled);
		
		$("#user-groups-list").jqGrid('clearGridData');
		$("#user-folders-list").jqGrid('clearGridData');
		
		if (that.users.length == 0) {
			$("#user-details-info").html('<div class="message">Click "Add User" to create a new user</div>');
		} else {
			if (selected) {
				$("#user-details-info").html("<h1>User '"+user.name+"'</h1>");
				
				that.refreshUserDetails();
			} else {
				$("#user-details-info").html('<div class="message">Select a user from the list to view details</div>');
			}
		}
		
		if (!selected) {
			$("#user-details-data").hide();
		} else {
			$("#user-details-data").show();
			that.resizeGrids();
		}
	}
	
	this.onUserGroupSelectionChanged = function() {
		var group = that.getSelectedUserGroup();
		var selected = (group != null);
		
		enableButton("button-remove-user-group", selected);
	}
	
	this.onUserFolderSelectionChanged = function() {
		var folder = that.getSelectedUserFolder();
		var selected = (folder != null);
		
		enableButton("button-edit-user-folder", selected);
		enableButton("button-remove-user-folder", selected);
	}
			
	this.validateUserData = function(edit) {
		if (edit) $("#edit-user-dialog > .form-data").removeClass("invalid");
		else $("#user-dialog > .form-data").removeClass("invalid");
	
		var result = true;
		
		if (edit){
			if ($("#edit-username").val().length == 0) {
				$("#edit-user-username").addClass("invalid");
				result = false;
			}		
		} else {
			if ($("#username").val().length == 0) {
				$("#user-username").addClass("invalid");
				result = false;
			}
			
			var auth = $("#auth").val();
			if (auth == '-') auth = that.default_auth_method;
			var pw_required = (auth == 'pw');
			
			if (pw_required && $("#password").val().length == 0) {
				$("#user-password").addClass("invalid");
				result = false;
			}
		}
		return result;
	}
	
	this.openAddUser = function() {
		if (!that.addUserDialogInit) {
			that.addUserDialogInit = true;

			if (that.auth_methods.length > 1) {
				$.each(that.getAuthOptions(), function(key, value) {   
				     $('#auth').append($("<option></option>").attr("value",key).text(value)); 
				});
			}
			
			$("#add-user-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 270,
				modal: true,
				resizable: false,
				title: "Add User"
			});
			$("#button-generate-user-password").click(function() {
				$("#password").val(generatePassword());
			});
			
			$("#expiration-date").datepicker();
		}
		
		var buttons = {
			Add: function() {
				if (!that.validateUserData(false)) return;
				
				var name = $("#username").val();
				var email = $("#email").val();
				var pw = $("#password").val();
				var permission = $("#permission").val();
				var auth = $("#auth").val();
				if (auth == '-') auth = null;
				var expiration = $("#expiration-date").val();
				if (expiration.length > 0) {
					try {
						expiration = parseDate(expiration);
					} catch (e) {
						alert("Invalid expiration date");
						return;
					}
				} else {
					expiration = null;
				}
		
				onSuccess = function() {
					$("#add-user-dialog").dialog('close');
					that.refresh();
				}
				addUser(name, pw, email, permission, auth, expiration, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		if (that.auth_options) {
			$("#user-auth").show();
		} else {
			$("#user-auth").hide();
		}
		$("#add-user-dialog").dialog('option', 'buttons', buttons);
		$("#username").val("");
		$("#email").val("");
		$("#password").val("");
		$("#permission").val("ro");
		$("#auth").val("-");
		$("#expiration-date").val("");
		$("#add-user-dialog").dialog('open');
	}
	
	this.getAuthOptions = function() {
		var def = 'Default ('+that.formatAuth(that.default_auth_method)+')';
		var result = {
			"-" : def
		};
		for (var i=0; i < that.auth_methods.length; i++) {
			var a = that.auth_methods[i];
			result[a.toLowerCase()] = that.formatAuth(a);
		}
		return result;
	}

	this.openEditUser = function(id) {
		var user = that.getUser(id);
		
		if (!that.editUserDialogInit) {
			that.editUserDialogInit = true;
			
			if (that.auth_methods.length > 1) {
				$.each(that.getAuthOptions(), function(key, value) {   
				     $('#edit-auth').append($("<option></option>").attr("value",key).text(value)); 
				});
			}

			$("#edit-user-dialog").dialog({
				bgiframe: true,
				height: 'auto',
				width: 270,
				modal: true,
				resizable: false,
				autoOpen: false,
				title: "Edit User"
			});
			$("#edit-expiration-date").datepicker();
		}
		
		var buttons = {
			Edit: function() {
				if (!that.validateUserData(true)) return;
				
				var name = $("#edit-username").val();
				var email = $("#edit-email").val();
				var permission = $("#edit-permission").val();
				var auth = $("#edit-auth").val();
				if (auth == '-') auth = null;
				var expiration = $("#edit-expiration-date").val();
				if (expiration.length > 0) {
					try {
						expiration = parseDate(expiration);
					} catch (e) {
						alert("Invalid expiration date");
						return;
					}
				} else {
					expiration = null;
				}
				
				onSuccess = function() {
					$("#edit-user-dialog").dialog('close');
					that.refresh();
				}
				editUser(id, name, email, permission, auth, expiration, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		if (that.auth_options) {
			$("#edit-user-auth").show();
			
			if (user["auth"] != null)
				$("#edit-auth").val(user["auth"].toLowerCase());
			else
				$("#edit-auth").val("-");
		} else
			$("#edit-user-auth").hide();
		$("#edit-user-dialog").dialog('option', 'buttons', buttons);
		$("#edit-username").val(user.name);
		$("#edit-email").val(user.email);
		$("#edit-permission").val(user["permission_mode"].toLowerCase());
		$("#edit-expiration-date").val(user["expiration"] ? formatDate(user["expiration"]) : "");
		
		$("#edit-user-dialog").dialog('open');
	}

	this.onChangePassword = function() {
		var id = that.getSelectedUser();
		if (id == null) return;
		
		if (!that.changePasswordDialogInit) {
			that.changePasswordDialogInit = true;

			$("#change-password-dialog").dialog({
				autoOpen: false,
				bgiframe: true,
				height: 'auto',
				width: 270,
				modal: true,
				resizable: false,
				title: "Change Password"
			});
			$("#button-generate-user-change-password").click(function() {
				$("#change-password-field").val(generatePassword());
			});
		}
		
		var buttons = {
			Change: function() {
				$("#change-password-dialog > .form-data").removeClass("invalid");
				if ($("#change-password-field").val().length == 0) {
					$("#change-password").addClass("invalid");
					return;
				}
				onSuccess = function() {
					$("#change-password-dialog").dialog('close');
					notify("Password changed");
				}
				
				var pw = $("#change-password-field").val();
				changePassword(id, pw, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#change-password-dialog").dialog('option', 'buttons', buttons);
		$("#change-password-field").val("");
		$("#change-password-dialog").dialog('open');
	}
	
	this.onRemoveUser = function() {
		var id = that.getSelectedUser();
		if (id == null) return;
		removeUser(id, that.refresh, onServerError);
	}
	
	this.onEditUser = function() {
		var id = that.getSelectedUser();
		if (id == null) return;
		that.openEditUser(id);
	}
	
	this.openAddUserGroups = function() {
		if (that.groups == null) return;
		
		var availableGroups = that.getAvailableGroups();
		if (availableGroups.length == 0) {
			alert("No more groups available");
			return;
		}
		
		var grid = $("#add-groups-list");
		
		if (!that.addGroupsDialogInit) {
			$("#add-groups-list").jqGrid({        
				datatype: "local",
				autowidth: true,
				multiselect: true,
			   	colNames:['ID', 'Name', 'Description'],
			   	colModel:[
				   	{name:'id',index:'id', width:60, sortable:true, sorttype:"int"},
			   		{name:'name',index:'name', width:200, sortable:true},
			   		{name:'description',index:'description',width:300, sortable:true},
			   	],
			   	sortname:'id',
			   	sortorder:'asc'
			});
		} else {
			grid.jqGrid('clearGridData');
		}
		
		for(var i=0;i < availableGroups.length;i++) {
			grid.jqGrid('addRowData', availableGroups[i].id, availableGroups[i]);
		}

		if (!that.addGroupsDialogInit) {
			that.addGroupsDialogInit = true;
			
			$("#add-user-groups-dialog").dialog({
				bgiframe: true,
				height: 'auto',
				width: 630,
				modal: true,
				resizable: true,
				autoOpen: false,
				title: "Add Groups"
			});
		}
		
		var buttons = {
			Add: function() {
				var sel = getValidSelections($("#add-groups-list").getGridParam("selarrrow"));
				if (sel.length == 0) return;

				var onSuccess = function() {
					$("#add-user-groups-dialog").dialog('close');
					that.refreshUserGroups();
				}
				
				addUsersGroups(that.getSelectedUser(), sel, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#add-user-groups-dialog").dialog('option', 'buttons', buttons);
		$("#add-user-groups-dialog").dialog('open');
	}
	
	this.getAvailableGroups = function() {
		var result = [];
		for (id in that.groups) {
			if (!that.userGroups[id])
				result.push(that.groups[id]);
		}
		return result;
	}
	
	this.onRemoveUserGroup = function() {
		var id = that.getSelectedUserGroup();
		if (id == null) return;
		removeGroupUsers(id, [that.getSelectedUser()], that.refreshUserGroups, onServerError);
	}

	this.openAddUserFolder = function() {
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
			
			$("#add-user-folder-dialog").dialog({
				bgiframe: true,
				height: 'auto',
				width: 270,
				modal: true,
				resizable: true,
				autoOpen: false,
				title: "Add User Folder"
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
					$("#add-user-folder-dialog").dialog('close');
					that.refreshUserFolders();
				}
				
				addUserFolder(that.getSelectedUser(), folder, name, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#add-user-folder-dialog").dialog('option', 'buttons', buttons);
		$("#use-default-folder-name").attr('checked', true);
		$("#use-default-folder-name").click(onFolderOrDefaultChanged);
		onFolderOrDefaultChanged();
		$("#add-user-folder-dialog").dialog('open');
	}

	this.openEditUserFolder = function() {
		if (that.folders == null) return;
		var selected = that.getSelectedUserFolder();
		if (selected == null) return;
		selected = that.userFolders[selected];
			
		var onFolderOrDefaultChanged = function() {
			var sel = that.getSelectedUserFolder();
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
			
			$("#edit-user-folder-dialog").dialog({
				bgiframe: true,
				height: 200,
				height: 'auto',
				modal: true,
				resizable: true,
				autoOpen: false,
				title: "Edit User Folder",
				buttons: buttons
			});
		}
		
		var buttons = {
			Edit: function() {
				if (!that.validateFolder(true)) return;
				
				var useDefault = $("#edit-use-default-folder-name").attr('checked');
				var name = useDefault ? null : $("#edit-published-folder-name").val();
				
				var onSuccess = function() {
					$("#edit-user-folder-dialog").dialog('close');
					that.refreshUserFolders();
				}
				
				editUserFolder(that.getSelectedUser(), that.getSelectedUserFolder(), name, onSuccess, onServerError);
			},
			Cancel: function() {
				$(this).dialog('close');
			}
		}
		
		$("#edit-user-folder-dialog").dialog('option', 'buttons', buttons);
		$("#published-folder-path").val(selected.path);
		$("#edit-use-default-folder-name").attr('checked', (selected.name == null));
		
		$("#edit-use-default-folder-name").click(onFolderOrDefaultChanged);
		onFolderOrDefaultChanged();
		if (selected.name) $("#edit-published-folder-name").val(selected.name);
		
		$("#edit-user-folder-dialog").dialog('open');
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
			if (!that.userFolders[id])
				result.push(that.folders[id]);
		}
		return result;
	}
	
	this.onRemoveUserFolder = function() {
		var id = that.getSelectedUserFolder();
		if (id == null) return;
		removeUserFolder(that.getSelectedUser(), id, that.refreshUserFolders, onServerError);
	}
}