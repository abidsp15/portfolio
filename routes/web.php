<?php

use App\Http\Controllers\PortfolioController;
use App\Http\Controllers\Admin\AuthController;
use App\Http\Controllers\Admin\DashboardController;
use Illuminate\Support\Facades\Route;

// Redirect Root to Login or Dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('login');
});

// Authentication & Registration (Guest Only)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.submit');
    Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.submit');
});

// Logout
Route::post('/logout', [AuthController::class, 'logout'])->name('admin.logout');

// Authenticated User Dashboard & Portfolio Actions
Route::middleware(['auth'])->group(function () {
    // Dashboard & Profile Update
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('admin.dashboard');
    Route::post('/profile', [DashboardController::class, 'updateProfile'])->name('admin.profile.update');
    
    // Sorting Preferences
    Route::post('/sorting/update', [DashboardController::class, 'updateSorting'])->name('admin.sorting.update');

    // Bulk Delete Routes
    Route::post('/education/bulk-delete', [DashboardController::class, 'bulkDestroyEducation'])->name('admin.education.bulk-delete');
    Route::post('/experience/bulk-delete', [DashboardController::class, 'bulkDestroyExperience'])->name('admin.experience.bulk-delete');
    Route::post('/achievements/bulk-delete', [DashboardController::class, 'bulkDestroyAchievement'])->name('admin.achievements.bulk-delete');
    Route::post('/skills/bulk-delete', [DashboardController::class, 'bulkDestroySkill'])->name('admin.skills.bulk-delete');
    Route::post('/projects/bulk-delete', [DashboardController::class, 'bulkDestroyProject'])->name('admin.projects.bulk-delete');
    
    // Preview & PDF Resume Export (For logged-in user)
    Route::get('/preview', [PortfolioController::class, 'preview'])->name('portfolio.preview');
    Route::get('/export-pdf', [PortfolioController::class, 'exportPdf'])->name('portfolio.export');

    // Education CRUD
    Route::post('/education', [DashboardController::class, 'storeEducation'])->name('admin.education.store');
    Route::put('/education/{education}', [DashboardController::class, 'updateEducation'])->name('admin.education.update');
    Route::delete('/education/{education}', [DashboardController::class, 'destroyEducation'])->name('admin.education.destroy');

    // Experience CRUD
    Route::post('/experience', [DashboardController::class, 'storeExperience'])->name('admin.experience.store');
    Route::put('/experience/{experience}', [DashboardController::class, 'updateExperience'])->name('admin.experience.update');
    Route::delete('/experience/{experience}', [DashboardController::class, 'destroyExperience'])->name('admin.experience.destroy');

    // Achievements CRUD
    Route::post('/achievements', [DashboardController::class, 'storeAchievement'])->name('admin.achievements.store');
    Route::put('/achievements/{achievement}', [DashboardController::class, 'updateAchievement'])->name('admin.achievements.update');
    Route::delete('/achievements/{achievement}', [DashboardController::class, 'destroyAchievement'])->name('admin.achievements.destroy');

    // Skills CRUD
    Route::post('/skills', [DashboardController::class, 'storeSkill'])->name('admin.skills.store');
    Route::put('/skills/{skill}', [DashboardController::class, 'updateSkill'])->name('admin.skills.update');
    Route::delete('/skills/{skill}', [DashboardController::class, 'destroySkill'])->name('admin.skills.destroy');

    // Projects CRUD
    Route::post('/projects', [DashboardController::class, 'storeProject'])->name('admin.projects.store');
    Route::put('/projects/{project}', [DashboardController::class, 'updateProject'])->name('admin.projects.update');
    Route::delete('/projects/{project}', [DashboardController::class, 'destroyProject'])->name('admin.projects.destroy');
    Route::delete('/project-images/{projectImage}', [DashboardController::class, 'destroyProjectImage'])->name('admin.project-images.destroy');
    Route::delete('/education-images/{educationImage}', [DashboardController::class, 'destroyEducationImage'])->name('admin.education-images.destroy');
    Route::delete('/experience-images/{experienceImage}', [DashboardController::class, 'destroyExperienceImage'])->name('admin.experience-images.destroy');
});
