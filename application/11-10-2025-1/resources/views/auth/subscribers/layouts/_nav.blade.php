<ul class="custom-menu d-flex flex-wrap gap-4 m-0 me-4 list-unstyled">
    <li>
        <a href="{{ route('subscriber.dashboard') }}"
           class="text-white text-decoration-none {{ Route::currentRouteName() === 'subscriber.dashboard' ? 'active' : '' }}">
           Home
        </a>
    </li>
    <li>
        <a href="{{ route('subscriber.leagalSearch',['new' => 1]) }}"
           class="text-white text-decoration-none {{ Route::currentRouteName() === 'subscriber.leagalSearch' ? 'active' : '' }}">
           Legal Search
        </a>
    </li>
    <li>
        <a href="{{ route('subscriber.dictionary.index') }}"
           class="text-white text-decoration-none {{ Route::currentRouteName() === 'subscriber.dictionary.index' ? 'active' : '' }}">
           Legal Terminology
        </a>
    </li>
    <li>
        <a href="{{ route('subscriber.bldVolume') }}"
           class="text-white text-decoration-none {{ Route::currentRouteName() === 'subscriber.bldVolume' ? 'active' : '' }}">
           BLD Volume
        </a>
    </li>
    <li>
        <a href="{{ route('subscriber.myFolder') }}"
           class="text-white text-decoration-none {{ Route::currentRouteName() === 'subscriber.myFolder' ? 'active' : '' }}">
           My Folder
        </a>
    </li>
    <li>
        <a href="{{ route('subscriber.bookmark') }}"
           class="text-white text-decoration-none {{ Route::currentRouteName() === 'subscriber.bookmark' ? 'active' : '' }}">
           Bookmarks
        </a>
    </li>
</ul>
