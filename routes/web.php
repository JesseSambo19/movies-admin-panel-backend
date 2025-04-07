<?php

use App\Http\Controllers\NoteController;
use App\Http\Controllers\ProfileController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Log;

// Route::get('/', function () {
//     return view('welcome');
// });

// Route::get('/dashboard', function () {
//     return view('dashboard');
// })->middleware(['auth', 'verified'])->name('dashboard');

// the / now is dashboard
// whener the code tries to redirect to /dashboard it will redirect to / then to /dashboard
Route::redirect('/', '/note')->name('dashboard');


Route::middleware(['auth', 'verified'])->group(function () {
    // associate name on every route
// Route::get('/note', [NoteController::class,'index'])->name('note.index');
// Route::get('/note/create', [NoteController::class,'create'])->name('note.create');
// Route::post('/note', [NoteController::class,'store'])->name('note.store');
// Route::get('/note/{id}', [NoteController::class,'show'])->name('note.show');
// Route::get('/note/{id}/edit', [NoteController::class,'edit'])->name('note.edit');
// Route::put('/note/{id}', [NoteController::class,'update'])->name('note.update');
// Route::delete('/note/{id}', [NoteController::class,'destroy'])->name('note.destroy');

    // this single line will generate all of the routes declared above
    Route::resource('note', NoteController::class);
});

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

Route::get('/debug-db', function () {
    try {
        // Check DB connection
        DB::connection()->getPdo();
        $dbName = DB::connection()->getDatabaseName();

        // Run migrations
        Artisan::call('migrate', ['--force' => true]);

        return "✅ Connected to DB: {$dbName}<br>✅ Migrations ran successfully.";
    } catch (\Exception $e) {
        return "❌ DB connection failed:<br><pre>" . $e->getMessage() . "</pre>";
    }
});

Route::get('/test-error', function () {
    try {
        return view('auth.login');
    } catch (\Exception $e) {
        Log::error('Login view error: ' . $e->getMessage());
        return response()->json(['error' => $e->getMessage()]);
    }
});


require __DIR__ . '/auth.php';
