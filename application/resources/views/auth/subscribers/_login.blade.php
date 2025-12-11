@php
$transparent = $transparent ?? false;
@endphp

<div class="login-box {{ $transparent ? 'transparent-login' : 'solid-login' }}">
  @if($errors->any())
  <div style="color: red;">
    <ul>
      @foreach ($errors->all() as $error)
      <li>{{ $error }}</li>
      @endforeach
    </ul>
  </div>
  @endif
  <h4>Login to Your Account</h4>
  <form method="POST" action="{{ url('subscriber/login') }}">
    @csrf
    <input type="hidden" name="back" value="{{ request()->query('back') }}">

    <div class="form-group">
      <input type="email" name="email" class="form-control" placeholder="Email address" required>
    </div>
    <div class="form-group" style="position: relative;">
      <input type="password" class="form-control login-password" name="password" placeholder="Password" required>
      <span class="toggle-password" style="position: absolute; right: 10px; top: 50%; transform: translateY(-50%); cursor: pointer;">
        <i class="fa fa-eye"></i>
      </span>
    </div>

    <button type="submit" class="btn-submit">Login</button>

    <div class="text-link">
      <a href="{{ route('subscriber.password.request') }}">Forgot password?</a>
    </div>

    <hr>

    <div class="text-link">
      <span style="color: black;">Don't have an account?</span>
      <a href="{{ url('subscriber/register') }}">Sign Up</a>
    </div>
  </form>
</div>

@push('scripts')
<script>
  $(document).ready(function() {
    $(document).on('click', '.toggle-password', function() {
      const $password = $(this).siblings('.login-password');
      const $icon = $(this).find('i');

      if ($password.attr('type') === 'password') {
        $password.attr('type', 'text');
        $icon.removeClass('fa-eye').addClass('fa-eye-slash');
      } else {
        $password.attr('type', 'password');
        $icon.removeClass('fa-eye-slash').addClass('fa-eye');
      }
    });
  });
</script>
@endpush