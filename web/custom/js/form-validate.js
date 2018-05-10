$(document).ready(function() {
	$('#contactform').bootstrapValidator({
		message: 'This value is not valid',
		feedbackIcons: {
			/*valid: 'glyphicon glyphicon-ok',
			invalid: 'glyphicon glyphicon-remove',
			validating: 'glyphicon glyphicon-refresh'*/
			valid: 'fa fa-check',
			invalid: 'fa fa-times',
			validating: 'fa fa-refresh'
		},
		fields: {
			name: {
				message: 'The name is not valid',
				validators: {
					notEmpty: {
						message: 'The name is required and can\'t be empty'
					},
					stringLength: {
						min: 2,
						message: 'The name must be more than 2 characters long'
					},
					/*regexp: {
						regexp: /^[a-zA-Z]+$/,
						message: 'The name can only consist of alphabetical characters'
					}*/
				}
			},
			email: {
				validators: {
					notEmpty: {
						message: 'The email address is required and can\'t be empty'
					},
					emailAddress: {
						message: 'The input is not a valid email address'
					}
				}
			},
			subject: {
				validators: {
					notEmpty: {
						message: 'The subject is required and can\'t be empty'
					},
					stringLength: {
						min: 2,
						message: 'The subject must be more than 2 characters long'
					}
				}
			},
			message: {
				validators: {
					notEmpty: {
						message: 'The message is required and can\'t be empty'
					},
					stringLength: {
						min: 2,
						message: 'The message must be more than 2 characters long'
					}
				}
			}
		}
	});
});