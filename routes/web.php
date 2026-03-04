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

// 1. Landing Page
Route::get('/', function () {
    return view('welcome');
});

// 2. Visitor Self-Registration
Route::get('/checkin', [VisitorController::class, 'create'])->name('visitor.register');
Route::post('/register-visitor', [VisitorController::class, 'store'])->name('visitor.store');

// Success page/pass (GET route to prevent form re-submission)
Route::get('/registration-success/{id}', [VisitorController::class, 'showPass'])->name('visitor.pass');

// 3. Authentication (Login)
Route::get('/login', function() { 
    return view('login'); 
})->name('login');

Route::post('/login', function(Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials)) {
        $request->session()->regenerate();
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
    
    /**
     * FIX: Changed from VisitorController to AdminController
     * This ensures the $stats variable (Arrivals, Pending, Inside) is passed 
     * to the dashboard view correctly as per our AdminController update.
     */
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // Visitor Management Actions
    Route::post('/approve-visitor/{id}', [VisitorController::class, 'approve'])->name('visitor.approve');
    Route::post('/reject-visitor/{id}', [VisitorController::class, 'reject'])->name('visitor.reject');
    Route::get('/visitor/{id}', [VisitorController::class, 'show'])->name('visitor.show');
    Route::post('/checkout/{id}', [VisitorController::class, 'checkout'])->name('visitor.checkout');

    /*
    |--------------------------------------------------------------------------
    | Admin Only Section
    |--------------------------------------------------------------------------
    */
    Route::middleware('can:admin-only')->group(function () {
        
        // Analytics & Reports
        Route::get('/admin/analytics', [AdminController::class, 'analytics'])->name('admin.analytics');
        
        // Host/User Management
        Route::get('/admin/users', [AdminController::class, 'listUsers'])->name('admin.users.index');
        Route::get('/admin/create-host', [AdminController::class, 'createHost'])->name('admin.host.create');
        Route::post('/admin/store-host', [AdminController::class, 'storeHost'])->name('admin.host.store');
        Route::get('/admin/user/{id}/edit', [AdminController::class, 'editUser'])->name('admin.user.edit');
        
        // Standardizing to PATCH or PUT for updates is best practice
        Route::patch('/admin/user/{id}', [AdminController::class, 'updateUser'])->name('admin.user.update');
        Route::delete('/admin/user/{id}', [AdminController::class, 'destroyUser'])->name('admin.user.delete');
    });

    // Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/login');
    })->name('logout');
});