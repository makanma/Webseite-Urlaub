

$(window).on('load', function () {

    $('#submitButton').on("click", function () {
        submitLogin();
    });

    $(document).on('keypress',function(e) {
        if (e.which  == 13) {
            submitLogin();
        }
    });

    function submitLogin() {
        let pass = CryptoJS.MD5($('#passField').val()).toString();
        formdata = {
            'action': 'login',
            'username': $('#usernamefield').val(),
            'password': pass
        }

        $.ajax({
            type: "Post",
            url: "index.php",
            data: formdata,
            dataType: "text",
            encode: true
        })
            .done(function (data) {
                console.log(data);

                if (data.toString().includes("TRUE")) {
                    window.location.href = 'home.php';
                } else {
                    alert("Zugangsdaten nicht gefunden");
                }


            })
            .fail(function (data) {
                console.log(data);

            });
    }

});