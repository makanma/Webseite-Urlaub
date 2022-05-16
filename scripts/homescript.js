$(window).on('load', function () {
	$("#maincontent").load("pages/anfrage.php");
	$("#Anfrage").css("background-color", "#369");
	$('#homehiddenField-id').val("#" + $("#Anfrage").attr("id"));
	$('#homehiddenField-color').val($( "#Anfrage" ).css( "background-color" ));

	$('#createUserTab').on("click", function () {
		$("#maincontent").load("pages/createUser.php");
	});

	$('#manageAnfragen').on("click", function () {
		$("#maincontent").load("pages/manageAnfragen.php");
	});
	 
	$('#Anfrage').on("click", function () {
		$("#maincontent").load("pages/anfrage.php");
	});

	$('#logoutTab').on("click", function () {
		
		formdata = {
			'action': 'endSession'
		}
	
		$.ajax({
			type: "Post",
			url: "/index.php",
			data: formdata,
			dataType: "text",
			encode: true
		})
			.done(function (data) {
				console.log(data);

	
			})
			.fail(function (data) {
				console.log(data);
				alert("logout fail");
			});

		window.location.href="index.php";
	});

});

$(document).ready(function(){
	
	// handles marking selected nav element
	$("nav").on("click", "li", function () {
		
		//save clicked element background-color in hidden field
		$('#homehiddenField-color').val($( this ).css( "background-color" ));

		//get last element and set last color
		$($('#homehiddenField-id').val()).css("background-color", $('#homehiddenField-color').val());

		//save clicked element id to hidden field
		$('#homehiddenField-id').val("#" + $(this).attr("id"));
		
	
		$(this).css("background-color", "#369");
	});
	


});