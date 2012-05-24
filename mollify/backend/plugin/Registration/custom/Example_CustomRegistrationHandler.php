<?php
	function onRegisterCustomData($registrationData) {
		// This function is called when the user submits the registration form.
		// NOTE! This registration is not yet confirmed, but you need to store custom data here.
		
		// Add your registration code here, use $registrationData["id"] to connect in confirmation.
		// Any possible custom data is in $registrationData["data"]

		// For example:
		// - open connection to your database
		// - store pending registration data: insert values $registrationData["id"] and your custom fields from $registrationData["data"]
	}
	
	function onConfirmCustomData($registrationData, $userId) {
		// This function is called when the registration is confirmed, and Mollify user has been created.
		// Add your confirmation data here.
		
		// NOTE! registration data does NOT contain custom data, as this is not stored by Mollify
		// if you need it here, you must save it in "onRegisterCustomData"
		
		// Value "$userId" is the Mollify id for the newly created user
		// Id from $registrationData["id"] is the same as in onRegisterCustomData

		// For example:
		// - open connection to your database
		// - get custom data stored with $registrationData["id"] (in function "onRegisterCustomData")
		// - do whatever you need to complete registration, tie it with Mollify user $userId
	}
?>