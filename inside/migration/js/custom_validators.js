jQuery.validator.addMethod("lettersonly", function(value, element) {
	return this.optional(element) || /^[a-z]+$/.test(value);
}, "Please enter only letters (english alphabet)."); 
