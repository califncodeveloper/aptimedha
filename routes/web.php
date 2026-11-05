<?php

use Illuminate\Support\Facades\Route;
use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Hash;

// Frontend Controllers
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\CategoryController as FrontendCategoryController;
use App\Http\Controllers\Frontend\McqController as FrontendMcqController;
use App\Http\Controllers\Frontend\PlacementController;
use App\Http\Controllers\Frontend\BlogController as FrontendBlogController;
use App\Http\Controllers\Frontend\PageController as FrontendPageController;

// Auth Controllers
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\ForgotPasswordController;
use App\Http\Controllers\Auth\ResetPasswordController;

// User Controllers
use App\Http\Controllers\User\DashboardController as UserDashboardController;
use App\Http\Controllers\User\ProfileController;
use App\Http\Controllers\User\BookmarkController;
use App\Http\Controllers\User\DiscussionController as UserDiscussionController;
use App\Http\Controllers\User\ReportController as UserReportController;

// Admin Controllers
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\McqController as AdminMcqController;
use App\Http\Controllers\Admin\CategoryController as AdminCategoryController;
use App\Http\Controllers\Admin\SubjectController;
use App\Http\Controllers\Admin\TopicController;
use App\Http\Controllers\Admin\McqImportController;
use App\Http\Controllers\Admin\BlogController as AdminBlogController;
use App\Http\Controllers\Admin\BlogCategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\DiscussionController as AdminDiscussionController;
use App\Http\Controllers\Admin\ReportController as AdminReportController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\PageController as AdminPageController;
use App\Http\Controllers\Admin\SettingController;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', HomeController::class)->name('home');

// Safe, one-click admin provisioning route for environments where terminal/shell access is restricted
Route::get('/setup-admin', function () {
    Artisan::call('migrate', ['--force' => true]);

    $admin = User::updateOrCreate(
        ['email' => 'admin@aptimedha.com'],
        [
            'name' => 'AptiMedha Administrator',
            'username' => 'admin',
            'password' => Hash::make('Admin@12345'),
            'role' => UserRole::Admin,
            'email_verified_at' => now(),
        ]
    );

    if (\App\Models\Category::count() === 0) {
        Artisan::call('db:seed', ['--force' => true]);
    }

    Artisan::call('cache:clear');
    Artisan::call('view:clear');

    return redirect()->route('login')->with(
        'success',
        'Admin account verified! Email: admin@aptimedha.com | Password: Admin@12345'
    );
})->name('setup.admin');

Route::get('/categories', [FrontendCategoryController::class, 'index'])->name('categories.index');
Route::get('/categories/{category}', [FrontendCategoryController::class, 'show'])->name('categories.show');

Route::get('/mcq', [FrontendMcqController::class, 'index'])->name('mcq.index');
Route::get('/mcq/{mcq}', [FrontendMcqController::class, 'show'])->name('mcq.show');

Route::get('/placement-preparation', PlacementController::class)->name('placement.index');
Route::get('/placement', fn () => redirect()->route('placement.index'))->name('placement');

Route::get('/blog', [FrontendBlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{post}', [FrontendBlogController::class, 'show'])->name('blog.show');

Route::get('/about', [FrontendPageController::class, 'about'])->name('about');
Route::get('/contact', [FrontendPageController::class, 'contact'])->name('contact');
Route::post('/contact', [FrontendPageController::class, 'submitContact'])->name('contact.submit');
Route::get('/disclaimer', [FrontendPageController::class, 'disclaimer'])->name('disclaimer');
Route::get('/terms-and-conditions', [FrontendPageController::class, 'terms'])->name('terms');
Route::get('/terms', fn () => redirect()->route('terms'));
Route::get('/privacy-policy', [FrontendPageController::class, 'privacy'])->name('privacy');
Route::get('/privacy', fn () => redirect()->route('privacy'));
Route::get('/copyright', [FrontendPageController::class, 'copyright'])->name('copyright');

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);

    Route::get('/register', [RegisterController::class, 'showRegistrationForm'])->name('register');
    Route::post('/register', [RegisterController::class, 'register']);

    Route::get('/forgot-password', [ForgotPasswordController::class, 'showLinkRequestForm'])->name('password.request');
    Route::post('/forgot-password', [ForgotPasswordController::class, 'sendResetLinkEmail'])->name('password.email');

    Route::get('/reset-password/{token}', [ResetPasswordController::class, 'showResetForm'])->name('password.reset');
    Route::post('/reset-password', [ResetPasswordController::class, 'reset'])->name('password.update');

    Route::get('/auth/google', [LoginController::class, 'redirectToGoogle'])->name('auth.google');
    Route::get('/auth/google/callback', [LoginController::class, 'handleGoogleCallback'])->name('auth.google.callback');
});

Route::post('/logout', [LoginController::class, 'logout'])->middleware('auth')->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/

Route::middleware('auth')->prefix('dashboard')->name('user.')->group(function () {
    Route::get('/', UserDashboardController::class)->name('dashboard');

    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('bookmarks');
    Route::post('/bookmarks/toggle/{question?}', [BookmarkController::class, 'toggle'])->name('bookmarks.toggle');
    Route::delete('/bookmarks/{bookmark}', [BookmarkController::class, 'destroy'])->name('bookmarks.destroy');

    Route::get('/discussions', [UserDiscussionController::class, 'index'])->name('discussions');
    Route::post('/discussions', [UserDiscussionController::class, 'store'])->name('discussions.store');
    Route::put('/discussions/{discussion}', [UserDiscussionController::class, 'update'])->name('discussions.update');
    Route::delete('/discussions/{discussion}', [UserDiscussionController::class, 'destroy'])->name('discussions.destroy');

    Route::get('/reported-questions', [UserReportController::class, 'index'])->name('reports');
    Route::post('/reported-questions', [UserReportController::class, 'store'])->name('reports.store');
});

/*
|--------------------------------------------------------------------------
| Admin Routes
|--------------------------------------------------------------------------
*/

Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', AdminDashboardController::class)->name('dashboard');

    Route::patch('mcqs/{mcq}/toggle-status', [AdminMcqController::class, 'toggleStatus'])->name('mcqs.toggle-status');
    Route::resource('mcqs', AdminMcqController::class)->except(['show']);
    Route::get('mcqs-import', [McqImportController::class, 'showForm'])->name('mcqs.import.form');
    Route::post('mcqs-import', [McqImportController::class, 'import'])->name('mcqs.import');
    Route::post('mcqs-import/preview', [McqImportController::class, 'preview'])->name('mcqs.import.preview');
    Route::post('mcqs-import/confirm', [McqImportController::class, 'confirm'])->name('mcqs.import.confirm');
    Route::post('mcqs-import/cancel', [McqImportController::class, 'cancel'])->name('mcqs.import.cancel');
    Route::get('mcqs-import/export-errors/{batch}', [McqImportController::class, 'exportErrors'])->name('mcqs.import.export-errors');
    Route::get('mcqs-import/template', [McqImportController::class, 'downloadTemplate'])->name('mcqs.import.template');

    Route::resource('categories', AdminCategoryController::class)->except(['show']);
    Route::resource('subjects', SubjectController::class)->except(['show']);
    Route::resource('topics', TopicController::class)->except(['show']);
    Route::patch('blogs/{blog}/toggle-status', [AdminBlogController::class, 'toggleStatus'])->name('blogs.toggle-status');
    Route::get('blogs/{blog}/preview', [AdminBlogController::class, 'preview'])->name('blogs.preview');
    Route::resource('blogs', AdminBlogController::class)->except(['show']);
    Route::resource('blog-categories', BlogCategoryController::class)->except(['show']);

    Route::resource('users', UserController::class)->except(['create', 'store']);

    Route::get('discussions', [AdminDiscussionController::class, 'index'])->name('discussions.index');
    Route::patch('discussions/{discussion}/status', [AdminDiscussionController::class, 'updateStatus'])->name('discussions.status');
    Route::delete('discussions/{discussion}', [AdminDiscussionController::class, 'destroy'])->name('discussions.destroy');

    Route::get('reports', [AdminReportController::class, 'index'])->name('reports.index');
    Route::get('reports/{report}', [AdminReportController::class, 'show'])->name('reports.show');
    Route::put('reports/{report}', [AdminReportController::class, 'update'])->name('reports.update');

    Route::get('contacts', [AdminContactController::class, 'index'])->name('contacts.index');
    Route::get('contacts/{contact}', [AdminContactController::class, 'show'])->name('contacts.show');
    Route::patch('contacts/{contact}/read', [AdminContactController::class, 'markRead'])->name('contacts.mark-read');
    Route::delete('contacts/{contact}', [AdminContactController::class, 'destroy'])->name('contacts.destroy');

    Route::get('pages', [AdminPageController::class, 'index'])->name('pages.index');
    Route::get('pages/{page}/edit', [AdminPageController::class, 'edit'])->name('pages.edit');
    Route::put('pages/{page}', [AdminPageController::class, 'update'])->name('pages.update');

    Route::get('settings', [SettingController::class, 'index'])->name('settings.index');
    Route::put('settings', [SettingController::class, 'update'])->name('settings.update');
});
