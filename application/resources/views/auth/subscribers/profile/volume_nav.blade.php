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
    <div class="info-text d-flex align-items-center gap-2">
        <select class="form-select form-select-sm d-inline-block w-auto border-0 bg-transparent fw-bold"
            style="min-width: 120px; cursor: pointer; font-size: 1.1em;" onchange="window.location.href=this.value">
            @foreach($allVolumes as $vol)
                <option value="{{ route('subscriber.volume.index', $vol->id) }}" {{ $vol->id == $volumeData->id ? 'selected' : '' }}>
                    VOLUME {{ $vol->number }}
                </option>
            @endforeach
        </select>
        <button onclick="window.print()" class="btn btn-outline-secondary btn-sm print-btn-container"
            title="Print Index">
            <i class="bi bi-printer"></i>
        </button>
    </div>
</div>