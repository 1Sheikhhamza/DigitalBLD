@include('admin.layouts.topbar')
<aside class="app-sidebar shadow" data-bs-theme="dark" style="background: linear-gradient(180deg, #0052cc 0%, #003399 100%) !important;">
    <div class="sidebar-brand">
        <a href="{{ route('admin.dashboard') }}" class="brand-link">
            <img src="{{ asset('frontend/assets/img/logo.png') }}" alt="Digital Bangladesh Legal Decisions Logo"
                class="brand-image opacity-75 shadow">
            <span class="brand-text fw-light">BLD</span> </a>
    </div>
    <div class="sidebar-wrapper">
        <nav class="mt-2">
            <ul class="nav sidebar-menu flex-column" data-lte-toggle="treeview" role="menu" data-accordion="false">
                <li class="nav-item"> <a href="{{ route('admin.dashboard') }}" class="nav-link"> <i
                            class="nav-icon bi bi-speedometer"></i>
                        <p>Dashboard</p>
                    </a>
                </li>

                <li class="nav-header">FUNCTIONAL</li>
                @if(hasAnyPermissionOnResource('configuration'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-gear"></i>
                            <p>Configuration <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('configuration.company')
                                <li class="nav-item">
                                    <a href="{{ route('configuration.company') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Company Profile</p>
                                    </a>
                                </li>
                            @endcan
                            @can('configuration.homepage')
                                <li class="nav-item">
                                    <a href="{{ route('configuration.homepage') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>Homepage Section</p>
                                    </a>
                                </li>
                            @endcan

                            @can('configuration.footer')
                                <li class="nav-item">
                                    <a href="{{ route('configuration.footer') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>Footer Section</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Dictionary --}}
                @if(hasAnyPermissionOnResource('dictionary'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi-list-check"></i>
                            <p>Dictionary Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('dictionary.view')
                                <li class="nav-item">
                                    <a href="{{ route('dictionary.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Dictionary List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('dictionary.create')
                                <li class="nav-item">
                                    <a href="{{ route('dictionary.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Dictionary</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                @if(hasAnyPermissionOnResource('pages'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi-file-earmark"></i>
                            <p>Pages <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('pages.view')
                                <li class="nav-item">
                                    <a href="{{ route('pages.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Page List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('pages.create')
                                <li class="nav-item">
                                    <a href="{{ route('pages.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Page</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @if(hasAnyPermissionOnResource('user_manual'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi-file-earmark"></i>
                            <p>User Manual <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('user_manual.view')
                                <li class="nav-item">
                                    <a href="{{ route('user_manual.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>User Manual List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('user_manual.create')
                                <li class="nav-item">
                                    <a href="{{ route('user_manual.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Manual</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                @if(hasAnyPermissionOnResource('faq'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi-file-earmark"></i>
                            <p>FAQ <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('faq.view')
                                <li class="nav-item">
                                    <a href="{{ route('faq.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>FAQ List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('faq.create')
                                <li class="nav-item">
                                    <a href="{{ route('faq.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New FAQ</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Patient --}}
                @if(hasAnyPermissionOnResource('banners'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-sliders"></i>
                            <p>Banner <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('banners.view')
                                <li class="nav-item">
                                    <a href="{{ route('banners.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Banner List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('banners.create')
                                <li class="nav-item">
                                    <a href="{{ route('banners.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Banner</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Specialty --}}
                @if(hasAnyPermissionOnResource('subscribers'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Subscriber <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('subscribers.view')
                                <li class="nav-item">
                                    <a href="{{ route('subscribers.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Subscriber List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('subscribers.create')
                                <li class="nav-item">
                                    <a href="{{ route('subscribers.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Subscriber</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Appointment --}}
                @if(hasAnyPermissionOnResource('subscriptions'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-cash-stack"></i>
                            <p>Subscription Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('subscriptions.view')
                                <li class="nav-item">
                                    <a href="{{ route('subscriptions.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Subscription List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('subscriptions.create')
                                <li class="nav-item">
                                    <a href="{{ route('subscriptions.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Subscription</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Package --}}
                @if(hasAnyPermissionOnResource('package_features'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-boxes"></i>
                            <p>Package Features <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('package_features.view')
                                <li class="nav-item">
                                    <a href="{{ route('package_features.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Feature List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('package_features.create')
                                <li class="nav-item">
                                    <a href="{{ route('package_features.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Feature</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if(hasAnyPermissionOnResource('package_feature_modules'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-boxes"></i>
                            <p>Package Feature Modules <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('package_feature_modules.view')
                                <li class="nav-item">
                                    <a href="{{ route('package_feature_modules.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Module List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('package_feature_modules.create')
                                <li class="nav-item">
                                    <a href="{{ route('package_feature_modules.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Module</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                @if(hasAnyPermissionOnResource('packages'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-boxes"></i>
                            <p>Package/Plan Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('packages.view')
                                <li class="nav-item">
                                    <a href="{{ route('packages.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Package List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('packages.create')
                                <li class="nav-item">
                                    <a href="{{ route('packages.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Package</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Volume --}}
                @if(hasAnyPermissionOnResource('volumes'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-journals"></i>
                            <p>Volume <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('volumes.view')
                                <li class="nav-item">
                                    <a href="{{ route('volumes.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Volume List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('volumes.create')
                                <li class="nav-item">
                                    <a href="{{ route('volumes.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Volume</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Blog --}}
                @if(hasAnyPermissionOnResource('ocr_extractions'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-journal-text"></i>
                            <p>Legal Decision <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('ocr_extractions.view')
                                <li class="nav-item">
                                    <a href="{{ route('ocr_extractions.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Decision List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('ocr_extractions.create')
                                <li class="nav-item">
                                    <a href="{{ route('ocr_extractions.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Decision</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Blog --}}
                @if(hasAnyPermissionOnResource('blogs'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-newspaper"></i>
                            <p>Blog Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('blogs.view')
                                <li class="nav-item">
                                    <a href="{{ route('blogs.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Blog List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('blogs.create')
                                <li class="nav-item">
                                    <a href="{{ route('blogs.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Blog</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                {{-- Service --}}
                @if(hasAnyPermissionOnResource('services'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi-list-check"></i>
                            <p>Service Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('services.view')
                                <li class="nav-item">
                                    <a href="{{ route('services.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Service List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('services.create')
                                <li class="nav-item">
                                    <a href="{{ route('services.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Service</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                {{-- Client --}}
                @if(hasAnyPermissionOnResource('clients'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-people"></i>
                            <p>Usefull Link Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('clients.view')
                                <li class="nav-item">
                                    <a href="{{ route('clients.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Link List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('clients.create')
                                <li class="nav-item">
                                    <a href="{{ route('clients.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Link</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                {{-- Client Feedback --}}
                @if(hasAnyPermissionOnResource('client_feedbacks'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>Client Feedback Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('client_feedbacks.view')
                                <li class="nav-item">
                                    <a href="{{ route('client_feedbacks.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Client Feedback List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('client_feedbacks.create')
                                <li class="nav-item">
                                    <a href="{{ route('client_feedbacks.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Client Feedback</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Career --}}
                @if(hasAnyPermissionOnResource('careers'))
                    <!-- <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-file"></i>
                                        <p>Career Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('careers.view')
                                        <li class="nav-item">
                                            <a href="{{ route('careers.index') }}" class="nav-link">
                                                <i class="nav-icon bi bi-list"></i>
                                                <p>Career List</p>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('careers.create')
                                        <li class="nav-item">
                                            <a href="{{ route('careers.create') }}" class="nav-link">
                                                <i class="nav-icon bi bi-plus-circle"></i>
                                                <p>New Career</p>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li> -->
                @endif


                {{-- Inquiry --}}
                @if(hasAnyPermissionOnResource('inquiries'))
                    <!-- <li class="nav-item">
                                    <a href="#" class="nav-link">
                                        <i class="nav-icon bi bi-question-lg"></i>
                                        <p>Inquiry Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                                    </a>
                                    <ul class="nav nav-treeview">
                                        @can('inquiries.view')
                                        <li class="nav-item">
                                            <a href="{{ route('inquiries.index') }}" class="nav-link">
                                                <i class="nav-icon bi bi-list"></i>
                                                <p>Inquiry List</p>
                                            </a>
                                        </li>
                                        @endcan
                                        @can('inquiries.create')
                                        <li class="nav-item">
                                            <a href="{{ route('inquiries.create') }}" class="nav-link">
                                                <i class="nav-icon bi bi-plus-circle"></i>
                                                <p>New Inquiry</p>
                                            </a>
                                        </li>
                                        @endcan
                                    </ul>
                                </li> -->
                @endif

                {{-- Team --}}
                @if(hasAnyPermissionOnResource('teams'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-people-fill"></i>
                            <p>Team Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('teams.view')
                                <li class="nav-item">
                                    <a href="{{ route('teams.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Team List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('teams.create')
                                <li class="nav-item">
                                    <a href="{{ route('teams.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Team</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                {{-- Photo --}}
                @if(hasAnyPermissionOnResource('photos'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-image"></i>
                            <p>Photo Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('photos.view')
                                <li class="nav-item">
                                    <a href="{{ route('photos.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Photo List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('photos.create')
                                <li class="nav-item">
                                    <a href="{{ route('photos.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Photo</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif


                {{-- Video --}}
                @if(hasAnyPermissionOnResource('videos'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi-camera-reels"></i>
                            <p>Video Manage <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('videos.view')
                                <li class="nav-item">
                                    <a href="{{ route('videos.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Video List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('videos.create')
                                <li class="nav-item">
                                    <a href="{{ route('videos.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Video</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif
                {{-- Announcement --}}
                @if(hasAnyPermissionOnResource('announcements'))
                    <li class="nav-item">
                        <a href="#" class="nav-link">
                            <i class="nav-icon bi bi- megaphone"></i>
                            <p>Announcement <i class="nav-arrow bi bi-chevron-right"></i></p>
                        </a>
                        <ul class="nav nav-treeview">
                            @can('announcements.view')
                                <li class="nav-item">
                                    <a href="{{ route('admin.announcements.index') }}" class="nav-link">
                                        <i class="nav-icon bi bi-list"></i>
                                        <p>Announcement List</p>
                                    </a>
                                </li>
                            @endcan
                            @can('announcements.create')
                                <li class="nav-item">
                                    <a href="{{ route('admin.announcements.create') }}" class="nav-link">
                                        <i class="nav-icon bi bi-plus-circle"></i>
                                        <p>New Announcement</p>
                                    </a>
                                </li>
                            @endcan
                        </ul>
                    </li>
                @endif

                {{-- Chat --}}
                <li class="nav-item">
                    <a href="{{ route('admin.chats.index') }}" class="nav-link">
                        <i class="nav-icon bi bi-chat-dots-fill"></i>
                        <p>Support Chats</p>
                    </a>
                </li>


                @role('Super Admin')
                <li class="nav-header"><i class="bi bi-gear-wide-connected"></i> SETTINGS</li>
                <li class="nav-item"> <a href="#" class="nav-link"> <i class="nav-icon bi bi-person-plus-fill"></i>
                        <p>User<i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('user.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-list"></i>
                                <p>User List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('user.create') }}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>New User</p>
                            </a>
                        </li>

                    </ul>
                </li>


                <li class="nav-item"> <a href="#" class="nav-link">

                        <i class="nav-icon bi bi-person-fill-lock"></i>
                        <p>Role<i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('roles.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-list"></i>
                                <p>Role List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('roles.create') }}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>New Role</p>
                            </a>
                        </li>

                    </ul>
                </li>

                <li class="nav-item"> <a href="#" class="nav-link"> <i class="nav-icon bi bi-database-fill-lock"></i>
                        <p>Permission<i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('permission.index') }}" class="nav-link">
                                <i class="nav-icon bi bi-list"></i>
                                <p>Permission List</p>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a href="{{ route('permission.create') }}" class="nav-link">
                                <i class="nav-icon bi bi-plus-circle"></i>
                                <p>New Permission</p>
                            </a>
                        </li>

                    </ul>
                </li>


                <li class="nav-item"> <a href="#" class="nav-link"> <i class="nav-icon bi bi-key"></i>
                        <p>Assign Permission<i class="nav-arrow bi bi-chevron-right"></i></p>
                    </a>
                    <ul class="nav nav-treeview">
                        <li class="nav-item">
                            <a href="{{ route('roles.permissions.assign') }}" class="nav-link">
                                <i class="nav-icon bi bi-list"></i>
                                <p>Assign Permission to Roles</p>
                            </a>
                        </li>
                    </ul>
                </li>
                @endrole
            </ul>
        </nav>
    </div>
    <div class="sidebar-custom p-3 border-top border-secondary">
        <a href="#" class="btn btn-link link-light w-100 d-flex align-items-center justify-content-center" data-lte-toggle="sidebar">
            <i class="bi bi-arrow-bar-left fs-4" data-lte-icon="collapse"></i>
            <i class="bi bi-arrow-bar-right fs-4" data-lte-icon="expand" style="display: none;"></i>
        </a>
    </div>
</aside>