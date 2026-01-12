<!DOCTYPE html>
<html lang="en" class="light-style customizer-hide" dir="ltr" data-theme="theme-default"
    data-assets-path="../assets/" data-template="vertical-menu-template-free">

<head>
    <meta charset="utf-8" />
    <meta name="viewport"
        content="width=device-width, initial-scale=1.0, user-scalable=no, minimum-scale=1.0, maximum-scale=1.0" />

    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Register</title>

    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="../assets/img/favicon/favicon.ico" />

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Public+Sans:ital,wght@0,300;0,400;0,500;0,600;0,700;1,300;1,400;1,500;1,600;1,700&display=swap"
        rel="stylesheet" />

    <!-- Icons. Uncomment required icon fonts -->
    <link rel="stylesheet" href="../assets/vendor/fonts/boxicons.css" />

    <!-- Core CSS -->
    <link rel="stylesheet" href="../assets/vendor/css/core.css" class="template-customizer-core-css" />
    <link rel="stylesheet" href="../assets/vendor/css/theme-default.css" class="template-customizer-theme-css" />
    <link rel="stylesheet" href="../assets/css/demo.css" />

    <!-- Vendors CSS -->
    <link rel="stylesheet" href="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.css" />

    <!-- Page CSS -->
    <!-- Page -->
    <link rel="stylesheet" href="../assets/vendor/css/pages/page-auth.css" />
    <!-- Helpers -->
    <script src="../assets/vendor/js/helpers.js"></script>
    <script src="../assets/js/config.js"></script>
</head>

<body>
    <!-- Content -->

    <div class="container-xxl">
        <div class="authentication-wrapper authentication-basic container-p-y">
            <div class="authentication-inner">
                <!-- Register Card -->
                <div class="card">
                    <div class="card-body">
                        <!-- Logo -->
                        <div class="app-brand justify-content-center">
                            <a href="javascript:void(0);" class="app-brand-link gap-2">
                                <span class="app-brand-text demo text-body fw-bolder">MyCart</span>
                            </a>
                        </div>
                        <div class="w-100">
                            <a href="{{ url('/auth/google') }}" class="btn btn-danger mb-2 w-100">
                                <i class="tf-icons bx bxl-google"></i>
                                Login with Google
                            </a>

                            <a href="{{ url('/auth/facebook') }}" class="btn btn-primary mb-2 w-100">
                                <i class="tf-icons bx bxl-facebook"></i>
                                Login with Facebook
                            </a>
                        </div>
                        <div class="text-center fw-semibold border-bottom my-3"></div>
                        <!-- /Logo -->
                        <h4 class="mb-2">Welcome to MyCart!</h4>

                        <form id="registerForm" class="mb-3">
                            <div class="mb-3">
                                <label for="username" class="form-label">Username</label>
                                <input type="text" class="form-control" id="username" name="username"
                                    placeholder="Enter your username" autofocus />
                            </div>
                            <div class="mb-3">
                                <label for="email" class="form-label">Email</label>
                                <input type="text" class="form-control" id="email" name="email"
                                    placeholder="Enter your email" />
                            </div>
                            <div class="mb-3 form-password-toggle is-invalid">
                                <label class="form-label" for="password">Password</label>
                                <div class="input-group input-group-merge">
                                    <input type="password" id="password" class="form-control" name="password"
                                        placeholder="&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;&#xb7;"
                                        aria-describedby="password" />
                                    <span class="input-group-text cursor-pointer"><i class="bx bx-hide"></i></span>
                                </div>
                            </div>

                            <button class="btn btn-primary d-grid w-100">Sign up</button>
                        </form>

                        <p class="text-center">
                            <span>Already have an account?</span>
                            <a href="{{ route('login') }}"><span>Sign in</span></a>
                        </p>
                    </div>
                </div>
                <!-- Register Card -->
            </div>
        </div>
    </div>

    <!-- / Content -->

    <!-- Core JS -->
    <!-- build:js assets/vendor/js/core.js -->
    <script src="../assets/vendor/libs/jquery/jquery.js"></script>
    <script src="../assets/vendor/libs/popper/popper.js"></script>
    <script src="../assets/vendor/js/bootstrap.js"></script>
    <script src="../assets/vendor/libs/perfect-scrollbar/perfect-scrollbar.js"></script>

    <script src="../assets/vendor/js/menu.js"></script>
    <script src="../assets/js/main.js"></script>
    <script async defer src="https://buttons.github.io/buttons.js"></script>

    <script src="{{ asset('js/ajax.js') }}"></script>

    <script>
        $(document).ready(function() {

            $('#registerForm').submit(function(e) {
                e.preventDefault();

                var formData = new FormData(this);
                
                ajaxCall('{{ route('register') }}', 'POST', formData, function(response) {
                    if (response.status == 'success') {
                        window.location.href = "{{ route('dashboard') }}";
                    }
                }, function(response) {

                    $('.is-invalid').removeClass('is-invalid');
                    $('.invalid-feedback').remove();

                    var response = JSON.parse(response.responseText);
                    if (response.message.username) {
                        var data = `<div class="invalid-feedback">${response.message.username[0]}</div>`;
                        $('#username').addClass('is-invalid').after(data);
                    }
                    if (response.message.email) {
                        var data = `<div class="invalid-feedback">${response.message.email[0]}</div>`;
                        $('#email').addClass('is-invalid').after(data);
                    }
                    if (response.message.password) {
                        var data = `<div class="invalid-feedback">${response.message.password[0]}</div>`;
                        $('#password').addClass('is-invalid');
                        $('.form-password-toggle > div').addClass('is-invalid').append(data);
                         
                    }
                });
            });
        });
    </script>
</body>

</html>
