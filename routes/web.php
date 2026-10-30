<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BoardMemberController as AdminBoardMemberController;
use App\Http\Controllers\Admin\CareerController as AdminCareerController;
use App\Http\Controllers\Admin\CmsActivityController;
use App\Http\Controllers\Admin\ContactController as AdminContactController;
use App\Http\Controllers\Admin\ContactInformationController as AdminContactInformationController;
use App\Http\Controllers\Admin\DashboardController as AdminDashboardController;
use App\Http\Controllers\Admin\DownloadController as AdminDownloadController;
use App\Http\Controllers\Admin\EmailVerificationController;
use App\Http\Controllers\Admin\EmployeeController as AdminEmployeeController;
use App\Http\Controllers\Admin\GovernmentAffiliationController as AdminGovernmentAffiliationController;
use App\Http\Controllers\Admin\ImportantNoticeController as AdminImportantNoticeController;
use App\Http\Controllers\Admin\NewsController as AdminNewsController;
use App\Http\Controllers\Admin\PasswordController as AdminPasswordController;
use App\Http\Controllers\Admin\ProcurementController as AdminProcurementController;
use App\Http\Controllers\Admin\PublicationController as AdminPublicationController;
use App\Http\Controllers\Admin\UserRegistrationController as AdminUserRegistrationController;
use App\Http\Controllers\Admin\WelfareSchemeController as AdminWelfareSchemeController;
use App\Http\Controllers\CareerController;
use App\Http\Controllers\ContactController;
use App\Http\Controllers\DownloadController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\LocaleController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\ProcurementController;
use App\Http\Controllers\PublicationController;
use App\Http\Controllers\SchemeController;
use App\Http\Controllers\SearchController;
use App\Http\Middleware\EnsureCmsSessionIsActive;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Official Website Routes
|--------------------------------------------------------------------------
*/

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/about', [AboutController::class, 'index'])->name('about');
Route::get('/schemes', [SchemeController::class, 'index'])->name('schemes');
Route::get('/publications', [PublicationController::class, 'index'])->name('publications');
Route::get('/publications/{publication}/download', [PublicationController::class, 'download'])->name('publications.download');
Route::get('/downloads', [DownloadController::class, 'index'])->name('downloads');
Route::get('/employees', [EmployeeController::class, 'index'])->name('employees');
Route::get('/media', [MediaController::class, 'index'])->name('media');
Route::get('/media/{slug}', [MediaController::class, 'show'])->name('media.detail');
Route::get('/careers', [CareerController::class, 'index'])->name('careers');
Route::get('/careers/{career}/documents/{documentType}', [CareerController::class, 'downloadDocument'])
    ->whereIn('documentType', ['tor', 'details', 'application'])
    ->name('careers.documents.download');
Route::get('/procurements', [ProcurementController::class, 'index'])->name('procurements');
Route::get('/procurements/{procurement}/download/{locale?}', [ProcurementController::class, 'download'])
    ->whereIn('locale', ['en', 'si', 'ta'])
    ->name('procurements.download');
Route::get('/procurements/{procurement}/view/{locale?}', [ProcurementController::class, 'view'])
    ->whereIn('locale', ['en', 'si', 'ta'])
    ->name('procurements.view');
Route::get('/contact', [ContactController::class, 'index'])->name('contact.index');
Route::post('/contact/submit', [ContactController::class, 'submit'])->middleware('throttle:5,1')->name('contact.submit');
Route::get('/search', SearchController::class)->name('search');
Route::get('/locale/{lang}', [LocaleController::class, 'switch'])
    ->whereIn('lang', ['en', 'si', 'ta'])
    ->name('locale.switch');

/*
|--------------------------------------------------------------------------
| Administrative Content Management Routes
|--------------------------------------------------------------------------
*/

Route::prefix('admin')->group(function () {
    // Admin Guest Auth Routes
    Route::get('/login', [AdminAuthController::class, 'loginForm'])->name('admin.login');
    Route::post('/login', [AdminAuthController::class, 'login'])->middleware('throttle:5,1')->name('admin.login.submit');
    Route::get('/register', [AdminAuthController::class, 'registerForm'])->name('admin.register');
    Route::post('/register', [AdminAuthController::class, 'register'])->middleware('throttle:5,1')->name('admin.register.submit');
    Route::get('/email/verify/{id}/{hash}', [EmailVerificationController::class, 'verify'])
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');
    Route::post('/email/verification-notification', [EmailVerificationController::class, 'resend'])
        ->middleware('throttle:5,1')
        ->name('verification.send');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('admin.logout');

    // Admin Protected Routes
    Route::middleware(['auth', EnsureCmsSessionIsActive::class])->group(function () {
        Route::get('/', [AdminDashboardController::class, 'index']);
        Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('admin.dashboard');
        Route::get('/activity', [CmsActivityController::class, 'index'])->name('admin.activity.index');
        Route::get('/activity/export', [CmsActivityController::class, 'export'])
            ->middleware('can:manage-cms-users')
            ->name('admin.activity.export');
        Route::get('/password', [AdminPasswordController::class, 'edit'])->name('admin.password.edit');
        Route::put('/password', [AdminPasswordController::class, 'update'])->name('admin.password.update');

        // News & Press Releases Management
        Route::resource('news', AdminNewsController::class, ['as' => 'admin']);

        // Important Information Ticker Management
        Route::resource('important-information', AdminImportantNoticeController::class, ['as' => 'admin'])->except('show');

        // Publications & Circulars Management
        Route::resource('publications', AdminPublicationController::class, ['as' => 'admin']);

        // Downloadable Forms & Guidelines Management
        Route::resource('downloads', AdminDownloadController::class, ['as' => 'admin']);

        // Careers & Vacancies Management
        Route::resource('careers', AdminCareerController::class, ['as' => 'admin']);

        // Procurement Notices, Tender Documents & TORs Management
        Route::patch('procurements/{procurement}/extend-deadline', [AdminProcurementController::class, 'extendDeadline'])
            ->name('admin.procurements.extend-deadline');
        Route::resource('procurements', AdminProcurementController::class, ['as' => 'admin']);

        // Employee Directory Management
        Route::resource('employees', AdminEmployeeController::class, ['as' => 'admin'])->except('show');

        // Welfare Schemes Management
        Route::resource('welfare-schemes', AdminWelfareSchemeController::class, ['as' => 'admin'])->except('show');

        Route::middleware('can:manage-cms-users')->group(function () {
            Route::resource('board-members', AdminBoardMemberController::class, ['as' => 'admin'])->except('show');
            Route::resource('government-affiliations', AdminGovernmentAffiliationController::class, ['as' => 'admin'])->except('show');
            Route::get('/contact-information/edit', [AdminContactInformationController::class, 'edit'])->name('admin.contact-information.edit');
            Route::put('/contact-information', [AdminContactInformationController::class, 'update'])->name('admin.contact-information.update');
        });

        // Citizen Inquiries Management
        Route::get('/contacts', [AdminContactController::class, 'index'])->name('admin.contacts.index');
        Route::get('/contacts/{id}', [AdminContactController::class, 'show'])->name('admin.contacts.show');
        Route::put('/contacts/{id}', [AdminContactController::class, 'update'])->name('admin.contacts.update');
        Route::delete('/contacts/{id}', [AdminContactController::class, 'destroy'])->name('admin.contacts.destroy');

        Route::middleware('can:manage-cms-users')->group(function () {
            Route::get('/users', [AdminUserRegistrationController::class, 'index'])->name('admin.users.index');
            Route::get('/users/current', [AdminUserRegistrationController::class, 'currentUsers'])->name('admin.users.current');
            Route::get('/users/{user}/edit', [AdminUserRegistrationController::class, 'edit'])->name('admin.users.edit');
            Route::put('/users/{user}', [AdminUserRegistrationController::class, 'update'])->name('admin.users.update');
            Route::post('/users/{user}/temporary-password', [AdminUserRegistrationController::class, 'setTemporaryPassword'])->name('admin.users.temporary-password');
            Route::delete('/users/{user}', [AdminUserRegistrationController::class, 'destroy'])->name('admin.users.destroy');
            Route::put('/users/{user}/approve', [AdminUserRegistrationController::class, 'approve'])->name('admin.users.approve');
            Route::delete('/users/{user}/reject', [AdminUserRegistrationController::class, 'reject'])->name('admin.users.reject');
        });
    });
});
