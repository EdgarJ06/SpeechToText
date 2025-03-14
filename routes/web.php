<?php

use App\Http\Controllers\SpeechController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

/*Route::get('/', function () {
    return view('welcome');
});*/


Route::get('/', [SpeechController::class, 'index'])->name('speech.index');
Route::post('/speech/transcribir', [SpeechController::class, 'transcribirAudio'])->name('speech.transcribir');
