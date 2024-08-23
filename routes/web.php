<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\FacultyController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\AttendanceController;
use App\Http\Controllers\Admin\FacultyManagementController;
use App\Http\Controllers\Admin\StudentManagementController;
use App\Http\Controllers\Admin\ScheduleManagementController;
use App\Http\Controllers\Admin\CourseController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\UserController;


Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
Route::get('/settings', [UserController::class, 'edit'])->name('settings.edit');
Route::post('/settings', [UserController::class, 'update'])->name('settings.update');

Route::middleware(['auth'])->group(function () {
    Route::get('/dashboard', function () {
        return redirect(auth()->user()->role === 'admin' ? '/admin' : '/faculty');
    })->name('dashboard');

    Route::middleware(['role:admin'])->group(function () {
        Route::get('/admin', [AdminController::class, 'index'])->name('admin.dashboard');
        Route::resource('admin/faculty', FacultyManagementController::class)->names([
            'index' => 'admin.faculty.index',
            'create' => 'admin.faculty.create',
            'store' => 'admin.faculty.store',
            'edit' => 'admin.faculty.edit',
            'update' => 'admin.faculty.update',
            'destroy' => 'admin.faculty.destroy',
        ]);
        Route::resource('admin/course', CourseController::class)->names([
            'index' => 'admin.course.index',
            'create' => 'admin.course.create',
            'store' => 'admin.course.store',
            'edit' => 'admin.course.edit',
            'update' => 'admin.course.update',
            'destroy' => 'admin.course.destroy',
        ]);
        Route::resource('admin/students', StudentManagementController::class)->names([
            'index' => 'admin.students.index',
            'create' => 'admin.students.create',
            'store' => 'admin.students.store',
            'edit' => 'admin.students.edit',
            'update' => 'admin.students.update',
            'destroy' => 'admin.students.destroy',
        ]);
        Route::resource('admin/schedule', ScheduleManagementController::class)->names([
            'index' => 'admin.schedule.index',
            'create' => 'admin.schedule.create',
            'store' => 'admin.schedule.store',
            'edit' => 'admin.schedule.edit',
            'update' => 'admin.schedule.update',
            'destroy' => 'admin.schedule.destroy',
        ]);
        Route::get('schedule-export', [ScheduleManagementController::class, 'export'])->name('admin.schedule.export');
        Route::get('/attendance/export', [AttendanceController::class, 'exportFacultyAttendance'])->name('attendance.export');
        Route::get('export-pdf', [ScheduleManagementController::class, 'exportPdf'])->name('admin.schedule.exportPdf');
        Route::get('/attendance/export/pdf', [AttendanceController::class, 'exportFacultyAttendancePdf'])->name('attendance.export.pdf');


        // Route for faculty attendance in the admin panel
        Route::get('admin/attendance', [AttendanceController::class, 'showFacultyAttendance'])->name('admin.attendance');
    });

    Route::middleware(['role:faculty'])->group(function () {
        Route::get('/faculty', [FacultyController::class, 'index'])->name('faculty.dashboard');
        Route::resource('/students', StudentController::class)->except(['show']);
        Route::get('students/import', [StudentController::class, 'import'])->name('students.import');
        Route::post('students/import', [StudentController::class, 'import'])->name('students.import');
        Route::get('students/import-pdf', [StudentController::class, 'importPDF'])->name('students.import-pdf');
        Route::post('students/import-pdf', [StudentController::class, 'importPDF'])->name('students.import-pdf'); 
        Route::get('/attendance', [AttendanceController::class, 'index'])->name('attendance.students');
        Route::post('/attendance/export', [AttendanceController::class, 'export'])->name('attendance.students.export');
        Route::post('/attendance/export-logbook-pdf', [AttendanceController::class, 'exportLogbookPdf'])->name('attendance.students.exportPdf');

        // Route for student attendance in the faculty panel
        Route::get('faculty/attendance', [AttendanceController::class, 'showStudentAttendance'])->name('faculty.attendance');

    });
    
});
