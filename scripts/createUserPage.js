function checkInvalidCharacters(idField){
	if($('#'+idField).val().toString().includes("<") || $$('#'+idField).val().toString().includes(">")){
		return true;
	}
}

$(document).ready(function () {
	$('#hiddenField-id').val("startValue");

});

$("#createUserButton").on("click", function () {
	$('form').submit(false);

	if(checkInvalidCharacters('createUserPassword')||checkInvalidCharacters('hiddenField-idval')||checkInvalidCharacters('createUserName')){
		return 0;
	}

	$('#form_createNewUser').validate({ // initialize plugin
		// rules & options, 
		rules: {
			createUserName: {
				required: true,
				minlength: 3
			},
			createUserPassword: {
				required: true,
				minlength: 5
			},
			confirm_createUserPassword: {
				required: true,
				minlength: 5,
				equalTo: createUserPassword
			}
		},
		messages: {
			createUserName: "Please enter your firstname",
			createUserPassword: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			},
			confirm_createUserPassword: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long",
				equalTo: "not equal"
			}
		},
		submitHandler: function (form) {
			// your ajax would go here
			let pass = CryptoJS.MD5($('#createUserPassword').val()).toString();
			formdata = {
				'action': 'CreateNewUser',
				'idvalue': $('#hiddenField-idval').val(),
				'username': $('#createUserName').val(),
				'password': pass,
				'userrole': $('#createUserRole').val(),
			}

			$.ajax({
				type: "Post",
				url: "/pages/createUser.php",
				data: formdata,
				dataType: "text",
				encode: true
			})
				.done(function (data) {
					
					//$("#list" + $('#hiddenField-id').val()).css("background-color", "#9999ff");
					$($('#hiddenField-id').val()).css("color", "green");


				})
				.fail(function (data) {
					
					alert("username bereits vorhanden");

				});


			//alert('simulated ajax submit');
			//$(form).ajaxSubmit();
			return false;  // blocks regular submit since you have ajax
		}
	});


});


$("#resetPassword").on("click", function () {
	$('form').submit(false);

	if($('#newPassword').val().toString().includes("<") || $('#newPassword').val().toString().includes(">")){
		return 0;
	}

	if ($('#hiddenField-id').val() == "startValue") {
		alert("Zuerst einen Benutzer auswählen");
	}



	$('#form_customizeUser').validate({ // initialize plugin
		// rules & options, 
		rules: {
			newPassword: {
				required: true,
				minlength: 5
			}
		},
		messages: {
			newPassword: {
				required: "Please provide a password",
				minlength: "Your password must be at least 5 characters long"
			}
		},
		submitHandler: function (form) {
			// your ajax would go here
			
			let pass = CryptoJS.MD5($('#newPassword').val()).toString();
			formdata = {
				'action': 'resetPass',
				'idvalue': $('#hiddenField-idval').val(),
				'newPassword': pass

			}

			$.ajax({
				type: "Post",
				url: "/pages/createUser.php",
				data: formdata,
				dataType: "text",
				encode: true
			})
				.done(function (data) {
					console.log(data);


				})
				.fail(function (data) {
					console.log(data);

				});

			alert('simulated ajax submit');
			//$(form).ajaxSubmit();
			return false;  // blocks regular submit since you have ajax
		}
	});


});

$("#changeRole").on("click", function () {
	$('form').submit(false);

	if ($('#hiddenField-id').val() == "startValue") {
		alert("Zuerst einen Benutzer auswählen");
	}

	formdata = {
		'action': 'changeUserRole',
		'idvalue': $('#hiddenField-idval').val(),
		'newUserRole': $('#newUserRole').val()

	}

	$.ajax({
		type: "Post",
		url: "/pages/createUser.php",
		data: formdata,
		dataType: "text",
		encode: true
	})
		.done(function (data) {
			console.log(data);


		})
		.fail(function (data) {
			console.log(data);

		});
});

$("#deleteUserButton").on("click", function () {
	$('form').submit(false);

	let confirm = window.confirm("Wirklich löschen?");
	if (!confirm) return false;

	if ($('#hiddenField-id').val() == "startValue") {
		alert("Zuerst einen Benutzer auswählen");
	}


	// your ajax would go here
	let pass = CryptoJS.MD5($('#newPassword').val()).toString();
	formdata = {
		'action': 'deleteUser',
		'idvalue': $('#hiddenField-idval').val()


	}

	$.ajax({
		type: "Post",
		url: "/pages/createUser.php",
		data: formdata,
		dataType: "text",
		encode: true
	})
		.done(function (data) {
			console.log(data);
			$($('#hiddenField-id').val()).css("color", "black");
		})
		.fail(function (data) {
			console.log(data);

		});

});


$("#personenlist").on("click", "li", function () {
	
	$($('#hiddenField-id').val()).css("font-weight", "normal");

	$('#hiddenField-id').val("#" + $(this).attr("id"));
	$('#hiddenField-idval').val($(this).attr("idvalue"));
	$('#hiddenField-firstName').val($(this).attr("firstName"));
	$('#hiddenField-lastName').val($(this).attr("lastName"));

	$(this).css("font-weight", "bold");




});