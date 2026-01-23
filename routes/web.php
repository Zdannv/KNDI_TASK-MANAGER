<?php

use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\SkillController;
use App\Http\Controllers\ProjectOwnerController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\LogtimeController;
use App\Http\Controllers\ImportController;
use App\Http\Controllers\LogController;
// Tambahkan controller Attendance di sini
use App\Http\Controllers\AttendanceController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
});

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard')->middleware('role:other,pm');

    Route::get('/user', [UserController::class, 'index'])->name('user.list')->middleware('role:other,pm');
    Route::post('/user', [UserController::class, 'store'])->name('user.store')->middleware('role:other,pm');
    Route::put('/user/{id}', [UserController::class, 'update'])->name('user.update')->middleware('role:other,pm');
    Route::delete('/user/{id}', [UserController::class, 'destroy'])->name('user.destroy')->middleware('role:other,pm');

    Route::get('/skill', [SkillController::class, 'index'])->name('skill.list');
    Route::post('/skill', [SkillController::class, 'store'])->name('skill.store');
    Route::delete('/skill/{id}', [SkillController::class, 'destroy'])->name('skill.destroy');
    Route::get('/skill/export', [SkillController::class, 'export'])->name('skill.export')->middleware('role:other,co');

    Route::get('/project-owner', [ProjectOwnerController::class, 'index'])->name('client.list')->middleware('role:other,pm,co');
    Route::post('/project-owner', [ProjectOwnerController::class, 'store'])->name('client.store')->middleware('role:other,pm,co');
    Route::put('/project-owner/{id}', [ProjectOwnerController::class, 'update'])->name('client.update')->middleware('role:other,pm,co');
    Route::delete('/project-owner/{id}', [ProjectOwnerController::class, 'destroy'])->name('client.destroy')->middleware('role:other,pm,co');

    Route::get('/project', [ProjectController::class, 'index'])->name('project.list');
    Route::post('/project', [ProjectController::class, 'store'])->name('project.store')->middleware('role:other,pm,co');
    Route::put('/project/{id}', [ProjectController::class, 'update'])->name('project.update')->middleware('role:other,pm,co');
    Route::delete('/project/{id}', [ProjectController::class, 'destroy'])->name('project.destroy')->middleware('role:other,pm,co');

    Route::get('/task', [TaskController::class, 'index'])->name('task.list');
    Route::post('/task', [TaskController::class, 'store'])->name('task.store')->middleware('role:other,pm,co');
    Route::put('/task/{id}/edit', [TaskController::class, 'editData'])->name('task.editData')->middleware('role:other,pm,co');
    Route::put('/task/{id}/assign', [TaskController::class, 'assignTask'])->name('task.assignTask')->middleware('role:other,pm');
    Route::put('/task/{id}/pr', [TaskController::class, 'prTask'])->name('task.prTask');
    Route::post('/task/{id}/comment', [TaskController::class, 'commentTask'])->name('task.commentTask');
    Route::post('/task/{id}/reply', [TaskController::class, 'replyTask'])->name('task.replyTask');
    Route::put('/task/{id}/review/{reviewerId}/complete', [TaskController::class, 'markReviewComplete'])->name('task.review.complete')->middleware('auth');
    Route::put('/task/{id}/close', [TaskController::class, 'close'])->name('task.close');
    Route::get('/task/{id}', [TaskController::class, 'show'])->name('task.show');
    Route::post('/task/{id}', [TaskController::class, 'changeIsActive'])->name('task.changeIsActive')->middleware('role:other,pm,co');
    Route::delete('/task/{id}', [TaskController::class, 'destroy'])->name('task.destroy')->middleware('role:other,pm,co');

    Route::get('/logtime', [LogtimeController::class, 'index'])->name('logtime.list');
    Route::get('/logtime/export', [LogtimeController::class, 'export'])->name('logtime.export')->middleware('role:other,co');
    Route::post('/logtime', [LogtimeController::class, 'store'])->name('logtime.store');
    Route::delete('/logtime/{id}', [LogtimeController::class, 'destroy'])->name('logtime.destroy')->middleware('role:other,co');

    Route::get('/log', [LogController::class, 'index'])->name('log.list')->middleware('role:pm');

    Route::get('/import', [ImportController::class, 'index'])->name('import.index')->middleware('role:other,co');
    Route::post('/import', [ImportController::class, 'store'])->name('import.store')->middleware('role:other,co');
    Route::get('/import/template', [ImportController::class, 'template'])->name('import.template')->middleware('role:other,co');

    Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance')->middleware('role:other');
    Route::get('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.export')->middleware('role:other');
});

Route::get('/recognize', function () {
    return Inertia::render('recognize/Index');
})->name('attendance.recognize');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';