jQuery(document).ready(function() {
	jQuery("." + jQuery("#cdn").val()).show();
	jQuery("#cdn").change(function() {
		if (this.value == "Origin") {
			jQuery(".cst-specific-options").hide();
		} else {
			jQuery(".cst-specific-options").show();
		}
		jQuery("." + this.value).show().siblings().hide();
	});
});