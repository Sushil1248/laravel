@extends('auth.master')

@section('content')
<h1 class="login-header">Reset your password</h1>
<h6>Please enter your email to receive password reset email.</h6>
<div class="card card-body">
    <form class="user" method="POST" action="{{ route('set-newpassword') }}" id="passwordreset_form" >@csrf
    <x-alert/>
        <div class="form-group required">
            <label >Password</label>
            <input type="password" class="form-control @error('password') is-invalid @enderror" id="password" name="password" placeholder="Enter Password" autofocus>
            @error('password')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group required">
            <label >Confirm Password</label>
            <input type="password" class="form-control @error('password_confirmation') is-invalid @enderror"  name="password_confirmation" placeholder="Enter Confirm Password" autofocus>
            @error('password_confirmation')
                <span class="invalid-feedback" role="alert">
                    <strong>{{ $message }}</strong>
                </span>
            @enderror
        </div>
        <div class="form-group d-flex password-feild">
            <a class="ml-auto border-link small-xl" href="{{ route('login') }}">{{ __('Login') }}</a>
        </div>
        <div class="form-group pt-1">
            <button class="btn btn-primary btn-block sign_in" type="submit">{{ __('Set Password') }}</button>
        </div>
    </form>	

    

</div>
@endsection

@section('js-scripts')
<script>
    $( document ).ready(function() {
        $("form[id='passwordreset_form']").validate({
            
            rules: {
                    password : {
                        required: true,
                        minlength : 6
                    },
                    password_confirmation : {
                        required: true,
                        equalTo : "#password"
                    }
                },
                messages: {
                    password: {
                        required: 'Password field is required',
                        minlength: 'Please enter minimum 6 length password'
                    },
                    password_confirmation: {
                        required: 'Confirm Password field is required',
                        equalTo : "Confirm Password must be same as password"
                    }
            },
            errorPlacement: function(error, element) {
                var placement = $(element).data('error');
                if (placement) {
                    $(placement).append(error)
                } else {
                    error.insertAfter(element.parent());
                }
            },
            submitHandler: function(form) {
                form.submit();
            }
        });
    });
    
</script>
@endsection