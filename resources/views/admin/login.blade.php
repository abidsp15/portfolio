@extends('layouts.app')

@section('title', 'Admin Login - Portfolio System')

@section('styles')
<style>
    body {
        margin: 0;
        padding: 0;
        background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 100%);
        min-height: 100vh;
        display: flex;
        align-items: center;
        justify-content: center;
        font-family: 'Outfit', sans-serif;
    }

    .login-container {
        background: rgba(255, 255, 255, 0.03);
        border: 1px rgba(255, 255, 255, 0.1) solid;
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        padding: 3rem 2.5rem;
        border-radius: 24px;
        width: 100%;
        max-width: 400px;
        box-shadow: 0 20px 40px rgba(0, 0, 0, 0.3);
        text-align: center;
        color: #f8fafc;
    }

    .login-title {
        font-family: 'Playfair Display', serif;
        font-size: 2.25rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        letter-spacing: -0.5px;
    }

    .login-subtitle {
        color: #94a3b8;
        font-size: 0.95rem;
        margin-bottom: 2.5rem;
    }

    .form-group {
        text-align: left;
        margin-bottom: 1.5rem;
    }

    .form-label {
        display: block;
        font-size: 0.85rem;
        font-weight: 500;
        color: #cbd5e1;
        margin-bottom: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .form-input {
        width: 100%;
        background: rgba(255, 255, 255, 0.05);
        border: 1px solid rgba(255, 255, 255, 0.1);
        padding: 0.85rem 1rem;
        border-radius: 12px;
        color: #fff;
        font-size: 1rem;
        box-sizing: border-box;
        transition: all 0.3s ease;
    }

    .form-input:focus {
        border-color: #6366f1;
        background: rgba(255, 255, 255, 0.08);
        box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        outline: none;
    }

    .error-text {
        color: #f87171;
        font-size: 0.8rem;
        margin-top: 0.5rem;
        display: block;
    }

    .login-button {
        width: 100%;
        background: linear-gradient(90deg, #6366f1 0%, #4f46e5 100%);
        border: none;
        color: #fff;
        font-size: 1rem;
        font-weight: 600;
        padding: 0.9rem;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
        margin-top: 1rem;
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3);
    }

    .login-button:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(99, 102, 241, 0.4);
    }

    .login-button:active {
        transform: translateY(0);
    }

    .back-link {
        display: inline-block;
        margin-top: 1.5rem;
        color: #94a3b8;
        text-decoration: none;
        font-size: 0.9rem;
        transition: color 0.3s ease;
    }

    .back-link:hover {
        color: #cbd5e1;
    }

    .back-link i {
        margin-right: 5px;
    }

    @media (max-width: 480px) {
        .login-container {
            padding: 2rem 1.5rem;
            margin: 15px;
            border-radius: 20px;
        }

        .login-title {
            font-size: 1.85rem;
        }

        .login-subtitle {
            margin-bottom: 1.75rem;
        }
    }
</style>
@section('content')
<div class="login-container">
    <h2 class="login-title">Admin Login</h2>
    <p class="login-subtitle">Sign in to manage your portfolio</p>

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        
        <div class="form-group">
            <label for="email" class="form-label">Email Address</label>
            <input type="email" id="email" name="email" class="form-input" required autocomplete="email" autofocus placeholder="admin@portfolio.com">
            @error('email')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" id="password" name="password" class="form-input" required autocomplete="current-password" placeholder="••••••••">
            @error('password')
                <span class="error-text">{{ $message }}</span>
            @enderror
        </div>

        <button type="submit" class="login-button">Sign In</button>
    </form>

    <a href="{{ route('register') }}" class="back-link">
        Need an account? <strong>Register</strong>
    </a>
</div>
@endsection
