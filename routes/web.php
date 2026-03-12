<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\AdminController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
});

// Visitor Self-Registration (Public)
Route::controller(VisitorController::class)->group(function () {
    Route::get('/checkin', 'create')->name('visitor.register');
    Route::post('/register-visitor', 'store')->name('visitor.store');
    Route::get('/registration-success/{id}', 'showPass')->name('visitor.pass');
});

// Authentication Routes
Route::get('/login', function() { 
    return view('login'); 
})->name('login')->middleware('guest');

Route::post('/login', function(Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
        // Simply redirect to /dashboard; the VisitorController@index handles the rest
        return redirect()->intended('/dashboard');
    }
    return back()->withErrors(['email' => 'The provided credentials do not match our records.']);
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Authenticated Users Only)
|--------------------------------------------------------------------------
*/
Route::middleware('auth')->group(function () {
    
    // Main Entry Point for Admin and Hosts
    Route::get('/dashboard', [VisitorController::class, 'index'])->name('dashboard');

    // Shared Visitor Actions
    Route::controller(VisitorController::class)->group(function () {
        Route::get('/visitor/{id}', 'show')->name('visitor.show');
        Route::post('/approve-visitor/{id}', 'approve')->name('visitor.approve');
        Route::post('/reject-visitor/{id}', 'reject')->name('visitor.reject');
        Route::post('/visitor/checkout/{id}', 'processCheckOut')->name('visitor.checkout');
    });

    /*
    | Guard Section
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:guard-access')->group(function () {
        Route::get('/guard/dashboard', [VisitorController::class, 'guardDashboard'])->name('guard.dashboard');
        Route::get('/guard/register', [VisitorController::class, 'guardCreate'])->name('guard.register');
        Route::post('/guard/store', [VisitorController::class, 'guardStore'])->name('guard.store');
        Route::post('/guard/checkin/{id}', [VisitorController::class, 'processCheckIn'])->name('guard.checkin');
    });

    /*
    | Admin Only Section
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:admin-only')->prefix('admin')->name('admin.')->group(function () {
        
        // 1. Visitor Management (The fix for your error)
        Route::delete('/visitor/{id}', [VisitorController::class, 'destroy'])->name('visitor.destroy');

        // 2. Analytics & Detailed Logs
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/visitor-details/{id}', [AdminController::class, 'visitorDetails'])->name('visitor_details');
        Route::get('/security-logs', [AdminController::class, 'securityLogs'])->name('security_logs');
        Route::get('/security-logs/export', [AdminController::class, 'exportSecurityLogs'])->name('security_logs.export');
        Route::delete('/security-logs/clear', [AdminController::class, 'clearSecurityLogs'])->name('security_logs.clear');
        
        // 3. User (Host) Management
        Route::get('/users', [AdminController::class, 'listUsers'])->name('users_index');
        Route::get('/users/create', [AdminController::class, 'createHost'])->name('create_host');
        Route::post('/users/store', [AdminController::class, 'storeHost'])->name('store_host');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('edit_user');
        Route::match(['put', 'patch'], '/users/{id}', [AdminController::class, 'update_user'])->name('update_user');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('destroy_user');
    });

    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});