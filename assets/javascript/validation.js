$(document).ready(function () {

    var $registerForm = $("#registerForm");
    var $loginForm = $("#loginForm");
    var $passwordForm = $("#formPassword");
    var $settings = $("#formProfile");
    var $formAddRole = $("#formAddRole");
    var $formAddUser = $("#formAddUser");
    var $pform = $("#pform");

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
            fname: {
                required: true
            },
            lname: {
                required: true
            },
            email: {
                required: true
            },
            number: {
                required: true
            },
            password: {
                required: true
            },
            cpassword: {
                required: true
            },
            role: {
                required: true
            }
        },
        messages: {
            fname: {
                required: "First Name must be required"
            },
            lname: {
                required: "Last Name must be required"
            },
            email: {
                required: "Email must be required"
            },
            number: {
                required: "Number must be required"
            },
            password: {
                required: "Password must be required"
            },
            cpassword: {
                required: "Confirm Password must be required"
            },
            role: {
                required: "Role must be required"
            }
        }
    });

    $pform.validate({
        rules: {
            pname: {
                required: true
            },
            pdescription: {
                required: true
            },
            pslug: {
                required: true
            },
            pcategory: {
                required: true
            },
            psize: {
                required: true
            },
            pweight: {
                required: true
            },
            poldprice: {
                required: true
            },
            pnewprice: {
                required: true
            },
            pimage: {
                required: true
            },
            pstatus: {
                required: true
            },
        },
        messages: {
            pname: {
                required: "Product Name must be required"
            },
            pdescription: {
                required: "Product Description must be required"
            },
            pslug: {
                required: "Product Slug must be required"
            },
            pcategory: {
                required: "Product Category must be required"
            },
            psize: {
                required: "Product Size must be required"
            },
            pweight: {
                required: "Product Weight must be required"
            },
            poldprice: {
                required: "Product Old Price must be required"
            },
            pnewprice: {
                required: "Product New Price must be required"
            },
            pimage: {
                required: "Product Image must be required"
            },
            pstatus: {
                required: "Product Status must be required"
            },
        },
    });

});