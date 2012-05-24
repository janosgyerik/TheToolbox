<!--
	Add here your own form HTML without head or body (this is injected directly into the form).

	Example:
-->
			<div class="registration-form-field">
				<div class="registration-field-title">My field1:</div>
				<input type="text" id="my-field1" class="registration-field custom-field"></input>
			</div>

<!--
	Define here javascript code that validates and provides the saved data.
-->
<script>
	function onValidateCustomFields() {
		// Validate custom fields, and show your notification on validation errors here.
		// Return value indicates if fields are valid, return false if registration should not continue
		return true;
	}
	
	function getCustomRegistrationData() {
		// Return here JSON object containing all data from the custom fields, for example:
		return {
			"my-field1": $("#my-field1").val()
		};
	}
</script>