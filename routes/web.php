<?php

use App\Http\Controllers\CertificationController;
use App\Http\Controllers\ContactMessageController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');
Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/certifications', [CertificationController::class, 'index'])->name('certifications.index');
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');
Route::get('/projects/{project}', [ProjectController::class, 'show'])->name('projects.show');
Route::post('/contact', [ContactMessageController::class, 'store'])->middleware('throttle:contact')->name('contact.store');
