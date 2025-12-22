<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\ActivityController;
use App\Http\Controllers\Frontend\InformationController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Frontend\PmbController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\InformationController as AdminInformationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Admin\SocialMediaController;
use App\Http\Controllers\Admin\AcademicYearController;
use App\Http\Controllers\Admin\PmbRegistrationController as AdminPmbRegistrationController;
use App\Http\Controllers\Admin\CommitteeController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profile/school', [ProfileController::class, 'school'])->name('profile.school');
Route::get('/profile/teachers', [ProfileController::class, 'teachers'])->name('profile.teachers');
Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/activities/{slug}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/information', [InformationController::class, 'index'])->name('information.index');
Route::get('/pmb', [PmbController::class, 'index'])->name('pmb.index');
Route::post('/pmb', [PmbController::class, 'store'])->name('pmb.store');
Route::get('/pmb/status', [PmbController::class, 'status'])->name('pmb.status');
Route::get('/pmb/download-pdf/{registration_number}', [PmbController::class, 'downloadPdf'])->name('pmb.downloadPdf');
Route::get('/pmb/print/{registration_number}', [PmbController::class, 'printCard'])->name('pmb.print');

// Auth routes
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Password Reset routes
Route::get('/forgot-password', [AuthController::class, 'showForgotPassword'])->name('password.request');
Route::post('/forgot-password', [AuthController::class, 'sendResetLink'])->name('password.email');
Route::get('/reset-password/{token}', [AuthController::class, 'showResetPassword'])->name('password.reset');
Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');

// Admin routes (protected)
Route::middleware(['auth'])->prefix('admin')->name('admin.')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Routes accessible by Admin and Teacher
    Route::middleware(['role:admin,teacher'])->group(function () {
        // School Profile
        Route::get('/school-profile', [SchoolProfileController::class, 'index'])->name('school-profile.index');
        Route::get('/school-profile/edit', [SchoolProfileController::class, 'edit'])->name('school-profile.edit');
        Route::put('/school-profile', [SchoolProfileController::class, 'update'])->name('school-profile.update');

        // Teachers
        Route::resource('teachers', TeacherController::class)->except(['show']);

        // Classes & Students
        Route::resource('classes', \App\Http\Controllers\Admin\SchoolClassController::class)->except(['show']);
        Route::get('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'import'])->name('students.import');
        Route::post('/students/import', [\App\Http\Controllers\Admin\StudentController::class, 'storeImport'])->name('students.import.store');
        Route::get('/students/import/template', [\App\Http\Controllers\Admin\StudentController::class, 'downloadTemplate'])->name('students.import.template');
        Route::resource('students', \App\Http\Controllers\Admin\StudentController::class)->except(['show']);

        // Information
        Route::resource('information', AdminInformationController::class)->except(['show']);
    });

    // Committee (Admin, Teacher, Admin Komite)
    Route::middleware(['role:admin,teacher,admin_komite'])->group(function () {
        Route::prefix('committee')->name('committee.')->group(function () {
            Route::get('/nominal', [CommitteeController::class, 'indexNominal'])->name('nominal.index');
            Route::get('/nominal/{academicYear}/set', [CommitteeController::class, 'setNominal'])->name('nominal.set');
            Route::post('/nominal/{academicYear}/store', [CommitteeController::class, 'storeNominal'])->name('nominal.store');

            Route::get('/payments', [CommitteeController::class, 'indexPayments'])->name('payments.index');
            Route::get('/payments/class/{schoolClass}', [CommitteeController::class, 'studentPayments'])->name('payments.students');
            Route::get('/payments/student/{student}', [CommitteeController::class, 'recordPayment'])->name('payments.record');
            Route::post('/payments/student/{student}', [CommitteeController::class, 'storePayment'])->name('payments.store');
            Route::get('/payments/receipt/{committeePayment}', [CommitteeController::class, 'receipt'])->name('payments.receipt');
        });
    });

    // Routes accessible by Admin, Teacher, and Student
    Route::middleware(['role:admin,teacher,student'])->group(function () {
        // Activities
        Route::resource('activities', AdminActivityController::class)->except(['show']);

        // Social Media
        Route::resource('social-media', SocialMediaController::class)->except(['show']);
    });

    // Admin Only Routes
    Route::middleware(['role:admin'])->group(function () {
        // User Management
        Route::resource('users', \App\Http\Controllers\Admin\UserController::class)->except(['show']);

        // Admin Profile (User restricted "Profil Admin" to admin only for teacher)
        Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
        Route::put('/profile', [AdminProfileController::class, 'updateProfile'])->name('profile.update');
        Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');

        // Settings
        Route::get('/settings/smtp', [SettingsController::class, 'smtp'])->name('settings.smtp');
        Route::put('/settings/smtp', [SettingsController::class, 'updateSmtp'])->name('settings.smtp.update');
        Route::post('/settings/smtp/test', [SettingsController::class, 'testSmtp'])->name('settings.smtp.test');

        // System Updates & Fixes
        Route::get('/system', [\App\Http\Controllers\Admin\SystemController::class, 'index'])->name('system.index');
        Route::post('/system/storage-link', [\App\Http\Controllers\Admin\SystemController::class, 'storageLink'])->name('system.storage-link');
        Route::post('/system/update', [\App\Http\Controllers\Admin\SystemController::class, 'updateApp'])->name('system.update');
        Route::post('/system/cache-clear', [\App\Http\Controllers\Admin\SystemController::class, 'cacheClear'])->name('system.cache-clear');
    });

    // PMB Management (Admin, Admin Komite)
    Route::middleware(['role:admin,admin_komite'])->group(function () {
        Route::get('/settings/pmb', [SettingsController::class, 'pmb'])->name('settings.pmb');
        Route::put('/settings/pmb', [SettingsController::class, 'updatePmb'])->name('settings.pmb.update');

        // Academic Years
        Route::resource('academic-years', AcademicYearController::class)->except(['create', 'show', 'edit']);

        // PMB Registrations Management
        Route::get('/pmb-registrations', [AdminPmbRegistrationController::class, 'index'])->name('pmb-registrations.index');
        Route::get('/pmb-registrations/{pmbRegistration}', [AdminPmbRegistrationController::class, 'show'])->name('pmb-registrations.show');
        Route::put('/pmb-registrations/{pmbRegistration}/status', [AdminPmbRegistrationController::class, 'updateStatus'])->name('pmb-registrations.status');
    });
});
