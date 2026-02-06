@extends('layouts.app')

@section('content')
<div class="form-card" style="max-width: 400px; padding: 40px; margin: 50px auto; animation: slideUp 0.6s ease-out; box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);">
    <div style="text-align: center; margin-bottom: 30px;">
        <h3 style="color: var(--primary-navy); font-weight: 700;">Admin Login</h3>
        <p style="color: var(--text-muted); font-size: 0.9rem;">Silakan masuk untuk mengakses dashboard</p>
    </div>

    @if ($errors->any())
        <div style="background: #fef2f2; color: #ef4444; padding: 10px; border-radius: 8px; margin-bottom: 20px; font-size: 0.9rem; border: 1px solid #fecaca;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" hx-boost="false">
        @csrf
        <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" required autofocus value="{{ old('email') }}" placeholder="admin@bankmci.com">
        </div>

        <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <input type="password" name="password" id="password" class="form-control" required placeholder="••••••••">
        </div>

        <div style="margin-top: 30px;">
            <button type="submit" class="btn-submit" style="background: var(--primary-navy); box-shadow: 0 10px 20px -5px rgba(15, 23, 42, 0.3);">
                Sign In <i class="fa-solid fa-right-to-bracket" style="margin-left: 8px;"></i>
            </button>
        </div>
    </form>
</div>
@endsection
