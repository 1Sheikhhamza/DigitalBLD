<div class="dropdown">
    <a class="d-flex align-items-center gap-2 text-decoration-none" style="color: white;" href="#" id="userDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
        <!-- <i class="bi bi-person-circle mx-2 fs-4"></i> -->
        <img src="{{ Auth::guard('subscriber')->user()  
        ? asset('uploads/subscriber/profile/'.Auth::guard('subscriber')->user()->photo) 
        : 'https://placehold.co/140x140/e0e0e0/000000?text=Profile' }}"
            alt="Profile Photo" class="profile-photo-sm">
        <i class="bi bi-caret-down-fill"></i>
    </a>

    <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="userDropdown" style="min-width: 220px; z-index:9999999999">
        <li class="px-3 py-2">
            <div class="fw-medium">{{ Auth::guard('subscriber')->user()->name }}</div>
            <div class="text-muted small">{{ Auth::guard('subscriber')->user()->email }}</div>
        </li>
        <li>
            <hr class="dropdown-divider">
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('subscriber.dashboard') }}" style="display: inline !important;">
                <i class="bi bi-person-circle me-2"></i> Dashboard
            </a>
        </li>
        <li>
            <a class="dropdown-item" href="{{ route('subscriber.profile') }}" style="display: inline !important;">
                <i class="bi bi-person-circle me-2"></i> My Profile
            </a>
        </li>
        <li>
            <form method="POST" action="{{ route('subscriber.logout') }}">
                @csrf
                <button type="submit" class="dropdown-item d-flex">
                    <i class="bi bi-box-arrow-right me-2"></i> Sign Out
                </button>
            </form>
        </li>
    </ul>
</div>