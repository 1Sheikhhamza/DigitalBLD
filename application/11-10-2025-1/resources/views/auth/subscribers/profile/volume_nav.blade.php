<div class="top-navbar d-flex justify-content-between align-items-center">
    <ul class="nav nav-tabs border-0">
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('subscriber.volume.index') ? 'active' : '' }}"
               href="{{ route('subscriber.volume.index', $volumeData->id) }}">
               Index
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('subscriber.volume.appellate') ? 'active' : '' }}"
               href="{{ route('subscriber.volume.appellate', $volumeData->id) }}">
               Appellate Division
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link {{ request()->routeIs('subscriber.volume.highcourt') ? 'active' : '' }}"
               href="{{ route('subscriber.volume.highcourt', $volumeData->id) }}">
               High Court Division
            </a>
        </li>
    </ul>
    <div class="info-text">
        VOLUME {{ $volumeData->number }}
    </div>
</div>
