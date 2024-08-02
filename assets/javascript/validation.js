$(document).ready(function () {

    var $registerForm = $("#registerForm");
    var $loginForm = $("#loginForm");

    $registerForm.validate({
        rules: {
            fname: {
                required: true
            },
            lname: {
                required: true
            },
            email: {
                required: true
            },
            password: {
                required: true,
            },
            cpassword: {
                required: true
            }
        },
        messages: {
            fname: {
                required: "First name must be required"
            },
            lname: {
                required: "Last name must be required"
            },
            email: {
                required: "Email must be required"
            },
            password: {
                required: "Password must be required"
            },
            cpassword: {
                required: "Confirm Password must be required"
            }
        }
    });

    $loginForm.validate({
        rules: {
            email: {
                required: true
            },
            password:{
                required: true
            }
        },
        messages: {
            email: {
                required: "Email must be required"
            },
            password:{
                required: "Password must be required"
            }
        }
    })

});