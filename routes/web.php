<?php

use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Frontend\ProfileController;
use App\Http\Controllers\Frontend\ActivityController;
use App\Http\Controllers\Frontend\InformationController;
use App\Http\Controllers\Frontend\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\SchoolProfileController;
use App\Http\Controllers\Admin\TeacherController;
use App\Http\Controllers\Admin\ActivityController as AdminActivityController;
use App\Http\Controllers\Admin\InformationController as AdminInformationController;
use App\Http\Controllers\Admin\ProfileController as AdminProfileController;
use App\Http\Controllers\Admin\SettingsController;
use Illuminate\Support\Facades\Route;

// Public routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/profile/school', [ProfileController::class, 'school'])->name('profile.school');
Route::get('/profile/teachers', [ProfileController::class, 'teachers'])->name('profile.teachers');
Route::get('/activities', [ActivityController::class, 'index'])->name('activities.index');
Route::get('/activities/{slug}', [ActivityController::class, 'show'])->name('activities.show');
Route::get('/information', [InformationController::class, 'index'])->name('information.index');

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
    
    // School Profile
    Route::get('/school-profile', [SchoolProfileController::class, 'index'])->name('school-profile.index');
    Route::get('/school-profile/edit', [SchoolProfileController::class, 'edit'])->name('school-profile.edit');
    Route::put('/school-profile', [SchoolProfileController::class, 'update'])->name('school-profile.update');
    
    // Teachers
    Route::resource('teachers', TeacherController::class)->except(['show']);
    
    // Activities
    Route::resource('activities', AdminActivityController::class)->except(['show']);
    
    // Information
    Route::resource('information', AdminInformationController::class)->except(['show']);
    
    // Admin Profile
    Route::get('/profile', [AdminProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [AdminProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [AdminProfileController::class, 'updatePassword'])->name('profile.password');
    
    // Settings
    Route::get('/settings/smtp', [SettingsController::class, 'smtp'])->name('settings.smtp');
    Route::put('/settings/smtp', [SettingsController::class, 'updateSmtp'])->name('settings.smtp.update');
    Route::post('/settings/smtp/test', [SettingsController::class, 'testSmtp'])->name('settings.smtp.test');
});
