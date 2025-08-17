@extends('layouts.auth')

@section('login')
<style>
    body.login-page {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        min-height: 100vh;
        font-family: 'Poppins', sans-serif;
    }
    
    .login-box {
        width: 400px;
        margin: 5% auto;
    }
    
    .login-box-body {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        border-radius: 20px;
        box-shadow: 0 20px 40px rgba(0,0,0,0.1);
        border: 1px solid rgba(255, 255, 255, 0.3);
        padding: 40px;
        position: relative;
        overflow: hidden;
    }
    
    .login-box-body::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        background: linear-gradient(90deg, #667eea, #764ba2, #f093fb, #f5576c);
    }
    
    .login-logo {
        text-align: center;
        margin-bottom: 30px;
    }
    
    .login-logo img {
        border-radius: 50%;
        padding: 15px;
        background: rgba(102, 126, 234, 0.1);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.2);
        transition: transform 0.3s ease;
    }
    
    .login-logo img:hover {
        transform: scale(1.05);
    }
    
    .welcome-text {
        text-align: center;
        margin-bottom: 25px;
    }
    
    .welcome-text h3 {
        color: #333;
        font-weight: 600;
        margin-bottom: 5px;
        font-size: 24px;
    }
    
    .welcome-text p {
        color: #666;
        font-size: 14px;
        margin: 0;
    }
    
    .form-group {
        position: relative;
        margin-bottom: 20px;
    }
    
    .form-control {
        height: 50px;
        border-radius: 12px;
        border: 2px solid #e1e5e9;
        padding-left: 50px;
        font-size: 14px;
        transition: all 0.3s ease;
        background: rgba(255, 255, 255, 0.9);
    }
    
    .form-control:focus {
        border-color: #667eea;
        box-shadow: 0 0 0 3px rgba(102, 126, 234, 0.1);
        background: #fff;
        outline: none;
    }
    
    .form-control-feedback {
        position: absolute;
        top: 50%;
        left: 15px;
        transform: translateY(-50%);
        color: #667eea;
        font-size: 16px;
        z-index: 3;
        pointer-events: none;
    }
    
    .help-block {
        color: #e74c3c;
        font-size: 12px;
        margin-top: 5px;
        padding: 5px 10px;
        background: rgba(231, 76, 60, 0.1);
        border-radius: 8px;
        border-left: 3px solid #e74c3c;
    }
    

    
    .btn-primary {
        background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
        border: none;
        border-radius: 12px;
        height: 50px;
        font-weight: 600;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 6px 15px rgba(102, 126, 234, 0.3);
    }
    
    .btn-primary:hover, .btn-primary:focus {
        transform: translateY(-2px);
        box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4);
        background: linear-gradient(135deg, #764ba2 0%, #667eea 100%);
        outline: none;
    }
    
    .btn-primary:active {
        transform: translateY(0);
    }
    
    /* Error state styling */
    .has-error .form-control {
        border-color: #e74c3c;
        box-shadow: 0 0 0 3px rgba(231, 76, 60, 0.1);
    }
    
    .has-error .form-control-feedback {
        color: #e74c3c;
    }
    
    /* Hide iCheck default styling if needed */
    .icheckbox_square-blue {
        display: none !important;
    }
    
    /* Mobile responsiveness */
    @media (max-width: 480px) {
        .login-box {
            width: 90%;
            margin: 10% auto;
        }
        
        .login-box-body {
            padding: 25px;
        }
        
        .welcome-text h3 {
            font-size: 20px;
        }
    }
</style>

<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

<div class="login-box">
    <div class="login-box-body">
        <div class="login-logo">
            <img src="{{ url($setting->path_logo) }}" alt="logo.png" width="80">
        </div>
        
        <div class="welcome-text">
            <h3>Welcome Back! ✨</h3>
            <p>Sign in to continue your journey</p>
        </div>

        <form action="{{ route('login') }}" method="post" class="form-login">
            @csrf
            <div class="form-group has-feedback @error('email') has-error @enderror">
                <input type="email" name="email" class="form-control" placeholder="Enter your email" required value="{{ old('email') }}" autofocus>
                <span class="fa fa-envelope form-control-feedback"></span>
                @error('email')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="form-group has-feedback @error('password') has-error @enderror">
                <input type="password" name="password" class="form-control" placeholder="Enter your password" required>
                <span class="fa fa-lock form-control-feedback"></span>
                @error('password')
                    <span class="help-block">{{ $message }}</span>
                @enderror
            </div>
            
            <div class="row">
                <div class="col-xs-12">
                    <button type="submit" class="btn btn-primary btn-block btn-flat">Sign In</button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    $(document).ready(function() {
        // Add smooth focus effects
        $('.form-control').on('focus', function() {
            $(this).parent().addClass('focused');
        }).on('blur', function() {
            $(this).parent().removeClass('focused');
        });
    });
</script>
@endsection