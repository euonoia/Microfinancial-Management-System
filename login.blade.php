@if(session('message'))
<div class="alert">
    <i class="fas fa-exclamation-triangle"></i>
    {{ session('message') }}
</div>
@endif

<form method="POST" action="{{ route('login') }}">
    @csrf
    <div class="form-group">
        <label class="form-label"><i class="fas fa-envelope"></i> Email Address</label>
        <input type="email" name="email" class="form-control" required placeholder="Enter your email">
        <i class="fas fa-user input-icon"></i>
    </div>

    <div class="form-group">
        <label class="form-label"><i class="fas fa-lock"></i> Password</label>
        <input type="password" name="password" class="form-control" required placeholder="Enter your password">
        <i class="fas fa-lock input-icon"></i>
    </div>

    <button type="submit" class="btn-login">
        <i class="fas fa-sign-in-alt"></i> Login
        <div class="loading"></div>
    </button>
</form>
