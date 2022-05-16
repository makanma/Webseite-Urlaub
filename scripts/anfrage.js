function setCookie(name, value, days) {
	var expires = "";
	if (days) {
		var date = new Date();
		date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
		expires = "; expires=" + date.toUTCString();
	}
	document.cookie = name + "=" + (value || "") + expires + "; path=/";
}

function getCookie(name) {
	var nameEQ = name + "=";
	var ca = document.cookie.split(';');
	for (var i = 0; i < ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0) == ' ') c = c.substring(1, c.length);
		if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length, c.length);
	}
	return null;
}



function getAvailableHolidays() {
	formdata = {
		'action': 'getAvailableHolidays',
		'username': getCookie("username")
	}

	$.ajax({
		type: "Post",
		url: "/ajax/ajaxAnfrage.php",
		data: formdata,
		dataType: "text",
		encode: true
	})
		.done(function (data) {
			return data;
		})
		.fail(function (data) {
			console.log(data);
		})


}


$(document).ready(function () {
	let currentYear = new Date().getFullYear();
	getHolidays(currentYear, "DE");
	let diff=1;
	$('#bookdays').html(diff);
	

	$.getScript('https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', function () {
		$('#holidayStartDate').val(moment().format('YYYY-MM-DD'));
		$('#holidayEndDate').val(moment().format('YYYY-MM-DD'));

		let startDate = moment();
		let endDate = moment();




		$.getScript('https://cdnjs.cloudflare.com/ajax/libs/moment-business-days/1.2.0/index.js', function () {

			$('#holidayStartDate').on('input', function () {
				let momentStartDate = moment($('#holidayStartDate').val());

				startDate = momentStartDate;//.subtract(1, 'days');

				diff = startDate.businessDiff(endDate);
				
				$('#bookdays').html(diff);

			});

			$('#holidayEndDate').on('input', function () {
				let momentEndtDate = moment($('#holidayEndDate').val());
				let endDate = momentEndtDate.add(1, 'days');
				diff = startDate.businessDiff(endDate);
		
				$('#bookdays').html(diff);
			});

		});
	});

	$("#sendRequestButton").on("click", function () {
		if(parseInt($("#availableHolidays").html()) < diff){
			alert("nicht genug verfügbare Urlaubstage");
			return 0;
		}
		
		let holidayStartDate = new Date($('#holidayStartDate').val());
		let holidayEndDate = new Date($('#holidayEndDate').val());
		
		formdata = {
			'action': 'setHolidayRequest',
			'username': getCookie("username"),
			'holidayStartDate': $('#holidayStartDate').val(),
			'holidayEndDate': $('#holidayEndDate').val(),
			'holidays': diff
		}

		$.ajax({
			type: "Post",
			url: "/ajax/ajaxAnfrage.php",
			data: formdata,
			dataType: "text",
			encode: true
		})
			.done(function (data) {
				//append new list entry
				
				let lastID = $( "#antraglist li" ).last().attr('idvalue');
				let appendString= '<li id="holidaylist'+(parseInt(lastID)+1)+'" idvalue="'+(parseInt(lastID)+1)+'" style="background-color:#FFFFB3">'+ formatDate(holidayStartDate) + ' bis ' + formatDate(holidayEndDate) + '</li>';
				$( "#antraglist ul").append(appendString);

				//subtract booked holidays from availableHolidays, page only
				$("#availableHolidays").html(parseInt($("#availableHolidays").html())-diff);
				
			})
			.fail(function (data) {
				alert("sendRequestButton fehlgeschlagen");
			});
	});

	//delete if pending
	$("#abortRequestButton").on("click", function () {
		formdata = {
			'action': 'abortRequest',
			'username': getCookie("username"),
			'id': $("#hiddenField-idval").val()

		}

		$.ajax({
			type: "Post",
			url: "/ajax/ajaxAnfrage.php",
			data: formdata,
			dataType: "text",
			encode: true
		})
			.done(function (data) {
				$("[idvalue='"+$("#hiddenField-idval").val()+"']").remove();
				$("#availableHolidays").html(parseInt($("#availableHolidays").html())+parseInt(data));
			})
			.fail(function (data) {
				console.log(data);
			});
	});
});

$("#antraglist").on("click", "li", function () {
	$($('#hiddenField-id').val()).css("font-weight", "normal");
	$('#hiddenField-id').val("#" + $(this).attr("id"));
	$('#hiddenField-idval').val($(this).attr("idvalue"));

	$(this).css("font-weight", "bold");
});


function getHolidays(year, country) {

	formdata = {
		action: "getHolidays",
		year: year,
		countrycode: country
	}

	$.ajax({

		type: "POST",
		url: "/ajax/ajaxAnfrage.php",
		data: formdata,
		dataType: 'json',
		encode: true
	})
		.done(function (response) {
			$.getScript('https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', function () {
				$.getScript('https://cdnjs.cloudflare.com/ajax/libs/moment-business-days/1.2.0/index.js', function () {
					//console.log(response);
					let holidays2 = [];

					$.each(response, function (index, value) {
						holidays2.push(value);
					});

					moment.updateLocale('de', {
						holidays: holidays2,
						holidayFormat: 'YYYY-MM-DD'
					});
				});
			});

		})
		.fail(function (response, status, error) {
			console.log(status);
			console.log(error);
			alert("fail");
			console.log(response);
			a = Object.entries(response)
			console.log(a);
		});
}

function formatDate(date) {
    let dateObj = new Date(date);
    let year = dateObj.getFullYear();
    let day = dateObj.getDate();
    let month = dateObj.getMonth() + 1;
    if (day > 0 && day < 10) day = "0" + day.toString();
    if (month > 0 && month < 10) month = "0" + month.toString();
    dateObj = day + "." + month + "." + year;
    return dateObj;
}