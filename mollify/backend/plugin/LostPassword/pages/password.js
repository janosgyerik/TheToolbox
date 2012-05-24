/**
	Copyright (c) 2008- Samuli Järvelä

	All rights reserved. This program and the accompanying materials
	are made available under the terms of the Eclipse Public License v1.0
	which accompanies this distribution, and is available at
	http://www.eclipse.org/legal/epl-v10.html. If redistributing this code,
	this entire header must remain intact.
*/

var servicePath = null;
var session = null;
var preRequestCallback = null;
var postRequestCallback = null;
var protocolVersion = "3";

function init(path) {
	servicePath = path;
	getSessionInfo(onSession, onError);
};

function onSession(session) {
	if (!session["authentication_required"]) {
		onError({error:"Configuration Error", details:"Current Mollify configuration does not require authentication, and reset password is disabled"});
		return;
	}
	if (!session.features["lost_password"]) {
		onError({error:"Configuration Error", details:"Lost password plugin not installed"});
		return;
	}
	$("#reset-button").click(onDoReset);
	$("#reset-password-form").show();
}

function onDoReset() {
	$(".reset-password-field").removeClass("invalid");
	$(".reset-password-field-hint").html("");
	
	var email = $("#email-field").val();
	
	if (email.length == 0) {
		$("#email-field").addClass("invalid");
		$("#email-hint").html("Enter your email");
		return;
	}
		
	reset(email, onReset, onResetError);
}

function onResetError(response) {
	if (response.code == 101)
		onError({code:101, error:"No user found with given email"});
	else if (response.code == 108)
		onError({code:108, error:"Resetting password failed"});
	else
		onError(response);
}

function onReset(response) {
	window.location = 'pages/reset_success.html';
}

function onError(error) {
	var errorHtml = $.template("<div class='error'><div class='title'>${title}</div><div class='details'>${details}</div></div>");
	$("body").html(errorHtml, {title: error.error, details: error.details });	
}

function getSession() {
	return session;
}

function getSessionInfo(success, fail) {
	request("GET", 'session/info/'+protocolVersion, success, fail);
}

function reset(email, success, fail) {
	var data = JSON.stringify({email:email});
	request("POST", 'lostpassword', success, fail, data);
}

function request(type, url, cb, fail, data) {
	if (preRequestCallback) preRequestCallback();
	
	var t = type;
	if (getSession() != null && getSession().features["limited_http_methods"]) {
		if (t == 'PUT' || t == 'DELETE') t = 'POST';
	}

	$.ajax({
		type: t,
		url: servicePath+"r.php/"+url,
		data: data,
		dataType: "json",
		success: function(result) {
			if (postRequestCallback) postRequestCallback();
			cb(result.result);
		},
		error: function (xhr, desc, exc) {
			if (postRequestCallback) postRequestCallback();
			
			var e = xhr.responseText;
			if (!e) fail({code:999, error:"Unknown error", details:"Request failed, no response received"});
			else if (e.substr(0, 1) != "{") fail({code:999, error:"Unknown error", details:"Invalid response received: " + e});
			else fail(JSON.parse(e));
		},
		beforeSend: function (xhr) {
			xhr.setRequestHeader("mollify-http-method", type);
		}
	});
}
