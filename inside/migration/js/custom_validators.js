jQuery.validator.addMethod("lettersonly", function(value, element) {
	return this.optional(element) || /^[a-z]+$/.test(value);
}, "Please enter only letters (english alphabet)."); 
jQuery.validator.addMethod("noquotes", function(value, element) {
	return this.optional(element) || ! /['"]+/.test(value);
}, "No single or double quotes, please."); 
jQuery.validator.addMethod("noescape", function(value, element) {
	return this.optional(element) || ! /[\\]+/.test(value);
}, "No backslashes, please."); 
