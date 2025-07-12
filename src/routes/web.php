<?php

use App\Http\Controllers\AdminAuthController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['auth'])->group(function () {
    Route::get('/', [AttendanceController::class, 'index']);
    Route::post('/attendance/clock-in', [AttendanceController::class, 'clockIn']);
    Route::post('/attendance/clock-out', [AttendanceController::class, 'clockOut']);
    Route::post('/attendance/break-start', [AttendanceController::class, 'breakStart']);
    Route::post('/attendance/break-end', [AttendanceController::class, 'breakEnd']);
    Route::get('/attendances/list', [AttendanceController::class, 'attendanceList']);
    Route::get('/attendance/{id}', [AttendanceController::class, 'attendanceShow'])->name('attendance.show');
    Route::get('/stamp_correction_request/list', [AttendanceController::class, 'attendanceRequest']);
    Route::post('/request', [AttendanceController::class, 'storeCorrectionRequest'])->name('corrections.store');

    Route::get('/admin/attendance/list', [AttendanceController::class, 'adminAttendanceList'])->name('admin.attendance.list');
    Route::get('/admin/staff/list', [AttendanceController::class, 'staffList']);
    Route::get('/admin/attendance/staff/{id}', [AttendanceController::class, 'staffShow'])->name('attendance.staff_show');
    Route::get('admin/attendance/staff/{id}/export', [AttendanceController::class, 'exportCsv'])->name('attendance.staff_export');
    Route::get('/stamp_correction_request/approve/{attendance_correction_request}', [AttendanceController::class, 'approve'])->name('stamp_correction_request.approve');
    Route::post('/stamp_correction_request/approve/{attendance_correction_request}', [AttendanceController::class, 'approveUpdate'])->name('corrections.approve');
});

Route::post('/register', [UserController::class, 'register']);
Route::get('/admin/login', [AdminAuthController::class, 'adminLogin'])->name('admin.login');
Route::post('/admin/login', [AdminAuthController::class, 'loginForm']);