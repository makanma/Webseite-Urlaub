$(document).ready(function () {


    $('#employee-select').change("option", function () {
        getHolidayRequestsOfUser($("#employee-select :selected").attr("userid"));

    });


});

function getHolidayRequestsOfUser(userid) {
    formdata = {
        'action': 'getHolidayRequestsOfUser',
        'userid': userid
    }

    $.ajax({
        type: "Post",
        url: "/ajax/manageAnfragenAjax.php",
        data: formdata,
        dataType: "json",
        encode: true
    })
        .done(function (data) {
            let backgroundColor = '';
            let strResult;
            let str;
            $("#requestList ul > li").each(function () {
                $(this).remove();
            });
            data.forEach(function (item) {
                switch (item[3]) {
                    case 'approved':
                        backgroundColor = 'green';
                        break;
                    case 'pending':
                        backgroundColor = '#FFFFB3';
                        break;
                    case 'denied':
                        backgroundColor = 'red';
                        break;
                    case 'aborted':
                        backgroundColor = '#23afff';
                        break;
                }
                str = '<li id="requestlist' + item[0] + '" holidayid="' + item[0] + ' "style=background-color:' + backgroundColor + ' ">' + formatDate(item[1]) + ' bis ' + formatDate(item[2]) + '; Urlaubstage: ' + item[4] + '</li>';
                //console.log(str);
                //console.log(strResult);
                strResult += str;
                
            });
                //.replace('undefined','')
            $("#requestList ul").append(strResult.toString());


        })
        .fail(function (data) {
            alert("getHolidayRequestsOfUser() fehlgeschlagen");
        });
}

//handles list selection bold
$("#requestList").on("click", "li", function () {
    
    $($('#hiddenField-id').val()).css("font-weight", "normal");
    $('#hiddenField-id').val("#" + $(this).attr("id"));
    $('#hiddenField-idval').val($(this).attr("holidayid"));

    $(this).css("font-weight", "bold");


    
});


$("#button-comfirm-changeSelectedRequest").on("click", function () {
    let holidayID = $($("#hiddenField-id").val()).attr("holidayid");
    let changeAction = $("#holiday-select-aprovedeny option:selected").text();
    let color;
    switch (changeAction) {
        case 'genehmigen':
            changeAction = "approved";
            color = "green";
            break;
        case 'ablehnen':
            changeAction = "denied";
            color = "red";
            break;
        case 'noch zu verarbeiten':
            changeAction = "pending";
            color = "#FFFFB3";
            break;

        default:
            break;
    }
    
    //alert(holidayID+"  "+changeAction)
    if (changeAction != "--Urlaub genehmigen oder ablehnen--" && holidayID!=null)
        requestHolidayStatusChange(holidayID, changeAction, color);

});

function requestHolidayStatusChange(holidayID, changeAction, color) {
    formdata = {
        'action': 'holidayStatusChange',
        'holidayid': holidayID,
        'changeAction': changeAction
    }

    $.ajax({
        type: "Post",
        url: "/ajax/manageAnfragenAjax.php",
        data: formdata,
        dataType: "text",
        encode: true
    })
        .done(function (data) {
            $($("#hiddenField-id").val()).css("background-color", color)

            // reduce pendingHolidayRequests by 1
            if (changeAction == "approved" || changeAction == "denied") {
                $("#pendingHolidayRequests").html(parseInt($("#pendingHolidayRequests").text())-1) ;
            } else{
                $("#pendingHolidayRequests").html(parseInt($("#pendingHolidayRequests").text())+1) ;
            }

        })
        .fail(function (data, status, error) {
            console.log(status);
            console.log(error.Message);
            console.log("requestHolidayStatusChange() fehlgeschlagen");
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