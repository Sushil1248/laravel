<!DOCTYPE html>
<html>
<head>
    <title>{{ config('app.name') }} - Login</title>
    <!--meta-tags-->
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!--css-->
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/login.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/css/common-responsive.css') }}">
    <style>
    .login-inner-content .form-group label.error{color:red}
    </style>
</head>
<body>
<section class="login-page">
    <div class="container-box">
        <div class="login-inner d-flex justify-content-center">
            <div class="Login-form bg_white">
                <div class="login-inner-content">
                    @yield('content')
                </div>
            </div>
            <div class="vendor-slider">
                <div class="slider_inner">
                    <img src="{{asset('assets/images/logo-freight.png')}}" style="width:500px" alt="logo_image">
                    <h1 class="lgn-app-name">FREIGHT MANAGEMENT</h1>
                </div>
            </div>
        </div>
    </div>
</section>
<footer>
    <div class="copyright-text d-flex align-items-center justify-content-between">
        <div class="copyright-text-left">
            <p>Copyright {{ config('app.name') }} {{ now()->format("Y") }}. All rights reserved
            </p>
        </div>
        <div class="copyright-text-right">
            <ul>
                <li style="border:none"><a href="#">&nbsp;</a></li>
            </ul>
        </div>
    </div>
</footer>
<!--JS-->
<script src="{{ asset('assets/js/jquery.min.js') }}"></script>
<script src="{{ asset('assets/js/bootstrap.bundle.min.js') }}"></script>
<script src="https://cdn.jsdelivr.net/npm/jquery-validation@1.19.5/dist/jquery.validate.js"></script>
@yield('js-scripts')
<script>
    $(document).ready(function(){
        $("form#login_form").validate({
            rules: {
                email: {
                    required: true
                },
                password: {
                    required: true,
                }
            },
            // Specify validation error messages
            messages: {
                email: {
                    required: 'Email address is required',
                    email: 'Provide a valid Email address',
                },
                password: {
                    required: 'Password is required',
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
</script>
</body>
</html>
