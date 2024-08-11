$(document).ready(function () {

    var $registerForm = $("#registerForm");
    var $loginForm = $("#loginForm");
    var $passwordForm = $("#formPassword");
    var $settings = $("#formProfile");
    var $formAddRole = $("#formAddRole");
    var $formAddUser = $("#formAddUser");

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
                required: true
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
            password: {
                required: true
            }
        },
        messages: {
            email: {
                required: "Email must be required"
            },
            password: {
                required: "Password must be required"
            }
        }
    });

    $passwordForm.validate({
        rules: {
            password: {
                required: true
            },
            cpassword: {
                required: true
            }
        },
        messages: {
            email: {
                required: "Password must be required"
            },
            password: {
                required: "Confirm Password must be required"
            }
        }
    });

    $settings.validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true
            },
            number: {
                required: true
            },
            gender: {
                required: true
            },
            dob: {
                required: true
            },
            hobby: {
                required: true
            }
        },
        messages: {
            name: {
                required: "Name must be required"
            },
            email: {
                required: "Email must be required"
            },
            number: {
                required: "Number must be required"
            },
            gender: {
                required: "Gender must be required"
            },
            dob: {
                required: "Date of Birth must be required"
            },
            hobby: {
                required: "Hobby must be required"
            }
        }
    });

    $formAddRole.validate({
        rules: {
            fullname: {
                required: true
            }
        },
        messages: {
            fullname: {
                required: "Name must be required"
            }
        }
    });

    $formAddUser.validate({
        rules: {
            name: {
                required: true
            },
            email: {
                required: true
            },
            number: {
                required: true
            },
            gender: {
                required: true
            },
            dob: {
                required: true
            },
            hobby: {
                required: true
            }
        },
        messages: {
            name: {
                required: "Name must be required"
            },
            email: {
                required: "Email must be required"
            },
            number: {
                required: "Number must be required"
            },
            gender: {
                required: "Gender must be required"
            },
            dob: {
                required: "Date of Birth must be required"
            },
            hobby: {
                required: "Hobby must be required"
            }
        }
    });


});