<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\GuardController; // Added the new GuardController
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::get('/', function () {
    return view('welcome');
})->name('home');

// Visitor Self-Registration
Route::controller(VisitorController::class)->group(function () {
    Route::get('/checkin', 'create')->name('visitor.register');

    // UPDATED: Matched the URL to what our Livewire form redirects to
    Route::get('/visitor/success/{id}', 'showPass')->name('visitor.pass');
    
    // Original Form Submission Endpoint
    Route::post('/visitor', 'store')->name('visitor.store');

    // Offline Sync Endpoint (called by Service Worker Background Sync)
    // withoutMiddleware('web') so Service Worker can POST without CSRF cookie
    Route::post('/visitor/offline-sync', 'syncOffline')
        ->name('visitor.offline_sync')
        ->withoutMiddleware([\Illuminate\Foundation\Http\Middleware\VerifyCsrfToken::class]);
});

/*
|--------------------------------------------------------------------------
| Authentication Logic
|--------------------------------------------------------------------------
*/

Route::get('/login', function() { 
    return view('login'); 
})->name('login')->middleware('guest');

Route::post('/login', function(Request $request) {
    $credentials = $request->validate([
        'email' => 'required|email',
        'password' => 'required',
    ]);

    if (Auth::attempt($credentials, $request->filled('remember'))) {
        $request->session()->regenerate();
        
        $user = Auth::user();

        // Route based on role
        if ($user->role === 'admin') {
            return redirect()->intended(route('admin.analytics')); 
        } 
        
        if ($user->role === 'guard') {
            return redirect()->intended(route('guard.dashboard'));
        } 

        if ($user->role === 'host') {
            return redirect()->intended(route('dashboard'));
        }

        return redirect()->intended(route('dashboard'));
    }
    
    return back()->withErrors([
        'email' => 'The provided credentials do not match our records.',
    ])->onlyInput('email');
});

/*
|--------------------------------------------------------------------------
| Protected Routes (Requires Auth Middleware)
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {
    
    // 1. Common Dashboard (For Hosts/Staff)
    Route::get('/dashboard', [AdminController::class, 'index'])->name('dashboard');

    // ==========================================
    // HOST ACTIONS (Added specifically for Hosts)
    // ==========================================
    Route::post('/host/visitor/{id}/accept', [AdminController::class, 'acceptVisitor'])->name('host.visitor.accept');
    Route::post('/host/visitor/{id}/reject', [AdminController::class, 'rejectVisitor'])->name('host.visitor.reject');
    Route::post('/host/visitor/{id}/checkout', [AdminController::class, 'checkoutVisitor'])->name('host.visitor.checkout');

    // 2. Shared Visitor Actions (Changed to PUT to match Blade @method('PUT'))
    Route::controller(VisitorController::class)->group(function () {
        Route::get('/visitor/{id}', 'show')->name('visitor.show');
        Route::put('/approve-visitor/{id}', 'approve')->name('visitor.approve');
        Route::put('/reject-visitor/{id}', 'reject')->name('visitor.reject');
        Route::put('/visitor/checkout/{id}', 'processCheckOut')->name('visitor.checkout');
    });

    // 3. Guard Section (Updated to use GuardController and PUT methods)
    Route::middleware('can:guard-access')->group(function () {
        Route::get('/guard/dashboard', [GuardController::class, 'index'])->name('guard.dashboard');
        
        // Assuming you still want guards to manually register visitors
        Route::get('/guard/register', [GuardController::class, 'create'])->name('guard.register');
        Route::post('/guard/store', [GuardController::class, 'store'])->name('guard.store');
        
        Route::put('/guard/checkin/{id}', [GuardController::class, 'checkIn'])->name('guard.checkin');
        // Fixed route name to match the blade template (removed .guard suffix)
        Route::put('/guard/checkout/{id}', [GuardController::class, 'checkOut'])->name('guard.checkout'); 
    });

    // 4. Admin Only Section
    Route::middleware('can:admin-only')->prefix('admin')->name('admin.')->group(function () {
        Route::get('/analytics', [AdminController::class, 'analytics'])->name('analytics');
        Route::get('/audit-logs', [AdminController::class, 'showAuditLogs'])->name('audit_logs');
        Route::post('/audit-logs/clear', [AdminController::class, 'clearAuditLogs'])->name('clear_audit_logs');
        Route::get('/print-report', [AdminController::class, 'printReport'])->name('print_report');
        Route::delete('/visitor/{id}', [VisitorController::class, 'destroy'])->name('visitor.destroy');

        // User Management
        Route::get('/users', [AdminController::class, 'listUsers'])->name('users_index');
        Route::get('/users/create', [AdminController::class, 'createHost'])->name('create_host');
        Route::post('/users/store', [AdminController::class, 'storeHost'])->name('store_host');
        Route::get('/users/{id}', [AdminController::class, 'showUser'])->name('users_show');
        Route::get('/users/{id}/edit', [AdminController::class, 'editUser'])->name('users_edit'); 
        Route::put('/users/{id}', [AdminController::class, 'updateUser'])->name('update_user');
        Route::delete('/users/{id}', [AdminController::class, 'destroyUser'])->name('users_destroy');
    });

    // 5. Logout
    Route::post('/logout', function (Request $request) {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect('/'); 
    })->name('logout');
});