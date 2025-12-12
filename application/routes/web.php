<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\CommonController;
use App\Http\Controllers\Admin\AdminController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use App\Http\Controllers\Admin\RolePermissionController;

use App\Http\Controllers\Admin\ConfigurationController;
use App\Http\Controllers\Admin\PageController;
use App\Http\Controllers\Admin\PackageController;
use App\Http\Controllers\Admin\BannerController;
use App\Http\Controllers\Admin\VolumeController;
use App\Http\Controllers\Admin\SubscriberController;
use App\Http\Controllers\Admin\SubscriptionController;
use App\Http\Controllers\Admin\OCRExtractionController;
use App\Http\Controllers\Admin\ClientController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\CareerController;
use App\Http\Controllers\Admin\BlogController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\PhotoController;
use App\Http\Controllers\Admin\VideoController;
use App\Http\Controllers\Admin\TeamController;
use App\Http\Controllers\Admin\ClientFeedbackController;
use App\Http\Controllers\Admin\DictionaryAdminController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\UserManualController;
use App\Http\Controllers\Admin\PackageFeatureController;
use App\Http\Controllers\Admin\PackageFeatureModuleController;
use Illuminate\Support\Facades\Auth;

// Frontend
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\SitemapController;

//Forntend Auth
use App\Http\Controllers\Auth\SubscriberAuthController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\Subscriber\DashboardController;
use App\Http\Controllers\Auth\Subscriber\LegalSearchController;
use App\Http\Controllers\Auth\Subscriber\AiAssistantController;
use App\Http\Controllers\Auth\Subscriber\AiResearchController;
use App\Http\Controllers\Auth\Subscriber\SupremeSearchController;
use App\Http\Controllers\Auth\Subscriber\ScobSearchController;
use App\Http\Controllers\Auth\Subscriber\BookmarkController;
use App\Http\Controllers\Auth\Subscriber\LegalDecesionUserNoteController;
use App\Http\Controllers\Auth\Subscriber\FolderController;
use App\Http\Controllers\Auth\Subscriber\FileManagerController;
use App\Http\Controllers\Auth\Subscriber\EventController;
use App\Http\Controllers\Auth\Subscriber\SubscriptionController as FrontSubscriptionController;
use App\Http\Controllers\Auth\Subscriber\DictionaryController;
use App\Http\Controllers\Auth\Subscriber\BlogInteractionController;

// Home & Static Pages
Route::get('/test-route-sanity', function () {
    return 'sanity check';
});
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('content/{slug}/{sslug?}', [HomeController::class, 'content'])->name('content');

// Public Preview Routes
Route::get('/preview-judgment/{id}', [HomeController::class, 'previewJudgment'])->name('preview.judgment');
Route::get('/preview-judgment/{id}/print/{type?}', [HomeController::class, 'previewPrint'])->name('preview.judgment.print');

Route::post('/inquiry', [HomeController::class, 'submitInquiry'])->name('inquiry.submit');
Route::post('/project-owner', [HomeController::class, 'storeProjectOwner'])->name('project-owner.store');

// Service
Route::get('/service', [HomeController::class, 'services'])->name('service');
Route::get('/service/{slug}', [HomeController::class, 'serviceDetails'])->name('service.details');

// Project
Route::get('/project', [HomeController::class, 'projects'])->name('project');
Route::get('/project/{slug}', [HomeController::class, 'projectDetails'])->name('project.details');

// Blog
Route::get('/blog', [HomeController::class, 'blogs'])->name('blog');
Route::get('/blog/{slug}', [HomeController::class, 'blogDetails'])->name('blog.details');

// Career
Route::get('/career', [HomeController::class, 'careers'])->name('career');
Route::get('/career/{slug}', [HomeController::class, 'careerDetails'])->name('career.details');

// Team
Route::get('/team', [HomeController::class, 'teams'])->name('team');
Route::get('/package', [HomeController::class, 'packages'])->name('package');
Route::get('/video', [HomeController::class, 'videos'])->name('video');
Route::get('/clientfeedback', [HomeController::class, 'feedbacks'])->name('clientfeedback');

// Feedback
Route::get('/feedback', [HomeController::class, 'feedbacks'])->name('feedback');
Route::get('/photo  ', [HomeController::class, 'photos'])->name('photo');
// FAQ
Route::get('/faq', [HomeController::class, 'faq'])->name('faq');
Route::get('/user-manual', [HomeController::class, 'userManual'])->name('user_manual');

// Clients
Route::get('/client', [HomeController::class, 'clients'])->name('client');

// Sitemap
Route::get('sitemap.xml', [SitemapController::class, 'index']);
Route::get('sitemap', [HomeController::class, 'sitemap'])->name('sitemap');

// Subscriber routes
Route::prefix('subscriber')->group(function () {

    Route::post('register/send-otp', [SubscriberAuthController::class, 'sendOtp'])->name('subscriber.sendOtp');

    // AI Summarizer (Placed here for visibility/testing)
    Route::post('/ai/summarize-judgment', [AiAssistantController::class, 'summarizeJudgment'])->name('ai.summarize');

    // AI Research (Using new Controller)
    Route::get('ai-research', [AiResearchController::class, 'index'])->name('ai.research');
    Route::post('ai-research/ask', [AiResearchController::class, 'ask'])->name('ai.research.ask');

    // Guest routes — only accessible if NOT logged in as subscriber
    Route::middleware('guest:subscriber')->group(function () {
        Route::get('login', [SubscriberAuthController::class, 'showLoginForm'])->name('subscriber.login');
        Route::post('login', [SubscriberAuthController::class, 'login']);
        Route::get('register', [SubscriberAuthController::class, 'showRegisterForm'])->name('subscriber.register');
        // Route::post('register', [SubscriberAuthController::class, 'register']);


        Route::post('register/send-otp', [SubscriberAuthController::class, 'sendOtp'])->name('subscriber.sendOtp');
        Route::post('register/resend-otp', [SubscriberAuthController::class, 'resend'])->name('subscriber.otp.resend');
        Route::get('register/otp', [SubscriberAuthController::class, 'showOtpForm'])->name('subscriber.otp');
        Route::post('register/verify-otp', [SubscriberAuthController::class, 'verifyOtp'])->name('subscriber.verifyOtp');
        Route::get('register/set-password', [SubscriberAuthController::class, 'showPasswordForm'])->name('subscriber.setPassword');
        Route::post('register/complete', [SubscriberAuthController::class, 'completeRegistration'])->name('subscriber.completeRegistration');
        Route::get('register/success', [SubscriberAuthController::class, 'success'])->name('subscriber.success');

        /////// Forget Password Routes ////////////
        Route::get('forgot-password', [ForgotPasswordController::class, 'showOtpRequestForm'])->name('subscriber.password.request');
        Route::post('forgot-password', [ForgotPasswordController::class, 'sendOtp'])->name('subscriber.password.email');
        Route::get('verify-otp', [ForgotPasswordController::class, 'showOtpVerificationForm'])->name('subscriber.otp.verify');
        Route::post('verify-otp', [ForgotPasswordController::class, 'verifyOtp'])->name('subscriber.otp.check');
        Route::get('reset-password', [ForgotPasswordController::class, 'showResetPasswordForm'])->name('subscriber.password.reset');
        Route::post('reset-password', [ForgotPasswordController::class, 'resetPassword'])->name('subscriber.password.resetPassword');
    });

    // Keep-alive route
    Route::middleware('auth:subscriber')->get('/subscriber/keep-alive', function () {
        session()->put('last_keepalive', now());
        return response()->json(['status' => 'ok']);
    })->name('subscriber.keepalive');

    // Session status check route
    Route::get('/subscriber/session-status', function () {
        if (Auth::guard('subscriber')->check()) {
            return response()->json(['active' => true]);
        } else {
            return response()->json(['active' => false], 401);
        }
    })->name('subscriber.session.status');



    Route::name('subscriber.')->middleware(['auth:subscriber', 'single.device.subscriber'])->group(function () {
        // Blog Interactions
        Route::post('blog/{id}/comment', [BlogInteractionController::class, 'storeComment'])->name('blog.comment');
        Route::delete('blog/comment/{id}', [BlogInteractionController::class, 'deleteComment'])->name('blog.comment.delete');
        Route::post('blog/{id}/like', [BlogInteractionController::class, 'toggleLike'])->name('blog.like');

        Route::post('logout', [SubscriberAuthController::class, 'logout'])->name('logout');
        Route::get('dashboard', [DashboardController::class, 'dashboard'])->name('dashboard');

        Route::get('upgrade-plan', [DashboardController::class, 'upgradePlan'])->name('upgrade.plan');



        // Supreme Search Route
        Route::get('supreme-search', [SupremeSearchController::class, 'index'])->name('supremeSearch')->middleware('has.subscription');
        Route::get('supreme-search/results', [SupremeSearchController::class, 'results'])->name('supremeSearch.results')->middleware('has.subscription');

        // Temporarily simplified for debugging
        Route::get('supreme-search/proxy', [SupremeSearchController::class, 'proxy'])->name('supremeSearch.proxy');

        Route::get('supreme-search/view-pdf', [SupremeSearchController::class, 'viewPdf'])->name('supremeSearch.viewPdf')->middleware('has.subscription');
        Route::get('supreme-search/proxy-pdf', [SupremeSearchController::class, 'proxyPdf'])->name('supremeSearch.proxyPdf')->middleware('has.subscription');
        Route::get('supreme-search/case-types', [SupremeSearchController::class, 'getCaseTypes'])->name('supremeSearch.caseTypes')->middleware('has.subscription');



        // Autocomplete Route (Bypass check.subscriber.permission, but keep has.subscription)
        Route::get('leagalSearch/suggestions', [LegalSearchController::class, 'getActSuggestions'])
            ->name('search.act.suggestions')
            ->middleware('has.subscription');

        Route::get('leagalSearch/parties-suggestions', [LegalSearchController::class, 'getPartiesSuggestions'])
            ->name('search.parties.suggestions')
            ->middleware('has.subscription');

        // Protected routes that require active subscription
        Route::middleware(['has.subscription', 'check.subscriber.permission'])->group(function () {
            // Legal Search
            Route::get('leagalSearch', [LegalSearchController::class, 'leagalSearch'])->name('leagalSearch');
            Route::post('searchResult', [LegalSearchController::class, 'handleSearch'])->name('searchResult');
            Route::get('showResults', [LegalSearchController::class, 'showResults'])->name('showResults');

            // BLD Volume
            Route::get('bldVolume', [LegalSearchController::class, 'bldVolume'])->name('bldVolume');
            // Route::get('legalDecision/{volume_id}', [LegalSearchController::class, 'legalDecision'])->name('legalDecision');

            Route::get('/legalDecisionIndex/{volume_id}', [LegalSearchController::class, 'legalDecisionIndex'])->name('volume.index');
            Route::get('/legalDecisionAppellate/{volume_id}', [LegalSearchController::class, 'legalDecisionAppellate'])->name('volume.appellate');
            Route::get('/legalDecisionHighCourt/{volume_id}', [LegalSearchController::class, 'legalDecisionHighCourt'])->name('volume.highcourt');

            // Single Legal Decision
            Route::get('myDecision/{id}', [LegalSearchController::class, 'myDecision'])->name('myDecision');
            Route::get('singleDecision/{id}/{type?}', [LegalSearchController::class, 'singleDecision'])->name('singleDecision');

            Route::get('sharedDecision/{id}', [LegalSearchController::class, 'sharedDecision'])->name('sharedDecision');

            // Download PDF
            Route::get('/legal-search/{id}/download-pdf', [LegalSearchController::class, 'downloadPdf'])->name('legal-search.downloadPdf');

            //  Print View
            Route::get('/legal-search/{id}/{print}', [LegalSearchController::class, 'printView'])->name('legal-search.print');

            // Bookmark
            Route::post('bookmark/toggle', [BookmarkController::class, 'toggle'])->name('bookmark.toggle');
            Route::get('bookmark', [BookmarkController::class, 'index'])->name('bookmark');

            // Remove Bookmark
            Route::delete('/bookmark/{id}', [BookmarkController::class, 'removeBookmark'])->name('bookmark.destroy');

            // Edit Legal Decision
            Route::get('myDecision/edit-note/{id}/{folderId}', [LegalDecesionUserNoteController::class, 'editDecisionNote'])->name('myDecision.editNote');
            Route::post('myDecision/{id}/update-note', [LegalDecesionUserNoteController::class, 'updateDecisionNote'])->name('myDecision.updateNote');

            // Comment
            Route::post('decision-comment/add', [LegalDecesionUserNoteController::class, 'addComment'])->name('add.comment');

            // Edit Comment
            Route::post('decision-comment/update', [LegalDecesionUserNoteController::class, 'update'])->name('comment.update');

            // Delete Comment
            Route::post('decision-comment/delete', [LegalDecesionUserNoteController::class, 'destroy'])->name('comment.delete');

            // Share Decision
            Route::post('decision/share', [LegalDecesionUserNoteController::class, 'share'])->name('decision.share');

            // Revoke Shared Decision
            Route::delete('decision/share/{id}/revoke', [LegalDecesionUserNoteController::class, 'revoke'])->name('decision.share.revoke');



            // Legal Dictionary
            Route::get('dictionary', [DictionaryController::class, 'index'])->name('dictionary.index');
            Route::get('dictionary/autocomplete', [DictionaryController::class, 'autocomplete'])->name('dictionary.autocomplete');


            // My Folder
            Route::get('/myFolder', [FolderController::class, 'index'])->name('myFolder');

            // Create Folder
            Route::post('/folders', [FolderController::class, 'store'])->name('folder.create');

            // Edit Folder
            Route::get('/folders/{id}/edit', [FolderController::class, 'edit'])->name('folder.edit');
            Route::put('/folders/{id}', [FolderController::class, 'update'])->name('folder.update');

            // Delete Event
            Route::delete('/folders/{id}', [FolderController::class, 'destroy'])->name('folder.destroy');

            // Download Folder as Zip
            Route::get('/folder/download/{id}', [FolderController::class, 'downloadFolder'])->name('folder.download');

            // Shared Folder
            Route::get('shared-decisions', [LegalDecesionUserNoteController::class, 'sharedWithMe'])->name('shared.decisions');

            // Copy Decision to Another Folder
            Route::post('decision/copy-to-folder', [FolderController::class, 'copyDecisionToFolder'])->name('decision.copy.to.folder');

            // Remove Decision from Folder
            Route::post('remove-decision-from-folder', [FolderController::class, 'removeDecisionFromFolder'])->name('remove-decision-from-folder');


            // Folder Decision
            Route::get('/files/{encryptedFolderId}', [FileManagerController::class, 'index'])->name('files.index');

            // Shared Folder Decisions
            Route::get('/my-shared-decision', [FileManagerController::class, 'sharedDecision'])->name('folders.shared');
            Route::delete('/shared-decision/{id}', [FileManagerController::class, 'deleteSharedDecision'])->name('sharedDecision.delete');



            Route::post('/files/upload', [FileManagerController::class, 'upload'])->name('file.upload');
            Route::post('/files/move', [FileManagerController::class, 'move'])->name('file.move');
            Route::post('/files/copy', [FileManagerController::class, 'copy'])->name('file.copy');
            Route::delete('/files/{id}', [FileManagerController::class, 'destroy'])->name('file.destroy');
            Route::post('/files/duplicate', [FileManagerController::class, 'duplicate'])->name('file.duplicate');
            Route::get('/file/download/{id}', [FileManagerController::class, 'downloadFile'])->name('file.download');
            Route::post('/files/download-multiple', [FileManagerController::class, 'download    Multiple'])->name('files.downloadMultiple');

            // Events Calender
            Route::get('/events', [EventController::class, 'index'])->name('events.index');
            Route::get('/events/json', [EventController::class, 'loadEventJson'])->name('events.json');
            Route::get('/events/{event}', [EventController::class, 'show'])->name('events.show');

            // Create Event
            Route::post('/events', [EventController::class, 'store'])->name('events.store');

            // Update Event
            Route::put('/events/{event}', [EventController::class, 'update'])->name('events.update');

            // Delete Event
            Route::delete('/events/{event}', [EventController::class, 'destroy'])->name('events.destroy');

        });

        // Chat
        Route::post('/chat/send', [\App\Http\Controllers\Auth\Subscriber\ChatController::class, 'store'])->name('chat.send');
        Route::get('/chat/messages', [\App\Http\Controllers\Auth\Subscriber\ChatController::class, 'fetchMessages'])->name('chat.messages');



        // My Account
        Route::get('profile', [DashboardController::class, 'profile'])->name('profile');
        Route::get('profile/edit', [DashboardController::class, 'edit'])->name('profile.edit');
        Route::post('profile/update', [DashboardController::class, 'update'])->name('profile.update');
        Route::get('/profile/password', [DashboardController::class, 'editPassword'])->name('profile.password.edit');
        Route::post('/profile/password', [DashboardController::class, 'updatePassword'])->name('profile.password.update');

        Route::get('mySubscription', [DashboardController::class, 'mySubscription'])->name('mySubscription');
        // Subscription and payment
        Route::get('subscription/{package}', [FrontSubscriptionController::class, 'form'])->name('subscription.form');
        Route::post('subscription/submit/{package_id}', [FrontSubscriptionController::class, 'subscriptionSubmit'])->name('subscription.submit');
        Route::post('subscription/package/success', [FrontSubscriptionController::class, 'success'])->name('subscription.success');
        Route::post('subscription/package/fail', [FrontSubscriptionController::class, 'fail'])->name('subscription.fail');
        Route::post('subscription/package/cancel', [FrontSubscriptionController::class, 'cancel'])->name('subscription.cancel');

        // Reminders
        Route::post('/reminders/mark-as-read', [\App\Http\Controllers\Auth\Subscriber\ReminderController::class, 'markAsRead'])->name('reminders.markAsRead');

    });
});


Route::get('/admin/force-logout', function () {
    Auth::logout();
    request()->session()->invalidate();
    request()->session()->regenerateToken();

    return redirect('/')->with('message', 'You have been logged out.');
});



Route::middleware('guest:administration')->group(function () {
    Route::get('/administration', [AuthController::class, 'showLogin'])->name('admin.login');
    Route::post('login', [AuthController::class, 'login'])->name('admin.login.submit');
    Route::get('password/reset', [AuthController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('password/email', [AuthController::class, 'sendResetLinkEmail'])->name('password.email');
});

Route::prefix('admin')->middleware('auth:administration')->group(function () {
    // Dashboard & logout
    Route::get('dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    Route::post('logout', [AuthController::class, 'logout'])->name('admin.logout');

    Route::middleware(['check.permission'])->group(function () {
        // Configuration Routes
        // Route::get('configuration', [ConfigurationController::class, 'edit'])->name('configuration.edit');
        // Route::put('configuration', [ConfigurationController::class, 'update'])->name('configuration.update');

        Route::prefix('configuration')->group(function () {
            Route::get('company', [ConfigurationController::class, 'company'])->name('configuration.company');
            Route::put('company', [ConfigurationController::class, 'updateCompany'])->name('configuration.company.update');

            Route::get('homepage', [ConfigurationController::class, 'homepage'])->name('configuration.homepage');
            Route::put('homepage', [ConfigurationController::class, 'updateHomepage'])->name('configuration.homepage.update');

            Route::get('footer', [ConfigurationController::class, 'footer'])->name('configuration.footer');
            Route::put('footer', [ConfigurationController::class, 'updateFooter'])->name('configuration.footer.update');
        });


        // User and Roles
        Route::resource('user', UserController::class);
        Route::resource('roles', RoleController::class);
        Route::resource('permission', PermissionController::class);

        // Modules Routes
        Route::resource('pages', PageController::class);
        Route::resource('package_features', PackageFeatureController::class);
        Route::resource('package_feature_modules', PackageFeatureModuleController::class);
        Route::resource('packages', PackageController::class);
        Route::resource('banners', BannerController::class);
        Route::resource('volumes', VolumeController::class);
        Route::get('subscribers/suggestions', [SubscriberController::class, 'suggestions'])->name('subscribers.suggestions');
        Route::get('subscribers/export', [SubscriberController::class, 'export'])->name('subscribers.export');
        Route::resource('subscribers', SubscriberController::class);
        Route::resource('subscriptions', SubscriptionController::class);
        Route::resource('ocr_extractions', OCRExtractionController::class);
        Route::resource('blogs', BlogController::class);
        Route::resource('clients', ClientController::class);
        Route::resource('careers', CareerController::class);
        Route::resource('photos', PhotoController::class);
        Route::resource('videos', VideoController::class);
        Route::resource('services', ServiceController::class);
        Route::resource('inquiries', InquiryController::class);
        Route::resource('teams', TeamController::class);
        Route::resource('client_feedbacks', ClientFeedbackController::class);
        Route::resource('dictionary', DictionaryAdminController::class);
        Route::resource('faq', FaqController::class);
        Route::resource('user_manual', UserManualController::class);

        // Route::get('/documents/download/{filename}', [VolumeController::class, 'downloadDocument'])->name('documents.download');
        Route::get('/document/download/{path}/{originalName}', [VolumeController::class, 'downloadDocument'])
            ->where('path', '.*')
            ->name('document.download');

        Route::post('page/update-sequence', [PageController::class, 'updateSequence'])->name('page.updateSequence');
        Route::post('page/swipe-sequence', [PageController::class, 'swapSequence'])->name('page.swapSequence');


        // Role Permission Routes
        Route::get('/assign-role-permissions', [RolePermissionController::class, 'assignPermissions'])->name('roles.permissions.assign');
        Route::post('/assign-role-permissions', [RolePermissionController::class, 'saveAssignedPermissions'])->name('roles.permissions.save');
        Route::get('/get-permissions-by-module/{module}', [RolePermissionController::class, 'getPermissionsByModule'])->name('permissions.byModule');
        Route::get('/get-permissions-by-role/{roleId}', [RolePermissionController::class, 'getPermissionsByRole'])->name('permissions.byRole');

        // Common Utility Routes
        Route::any('permissions', [CommonController::class, 'permissions'])->name('permissions');
        Route::get('update-slug', [CommonController::class, 'updateSlug'])->name('update.slug');
        Route::any('masterdelete', [CommonController::class, 'deletedata'])->name('masterdelete');

        // Announcements
        Route::resource('announcements', \App\Http\Controllers\Admin\AnnouncementController::class, ['as' => 'admin']);

        // Chat
        Route::controller(\App\Http\Controllers\Admin\ChatController::class)->prefix('chats')->name('admin.chats.')->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/{id}', 'show')->name('show');
            Route::post('/{id}/reply', 'reply')->name('reply');
            Route::get('/{id}/messages', 'fetchMessages')->name('messages');
        });
    });

});


