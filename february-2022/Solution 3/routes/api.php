<?php

use App\Http\Controllers\EmailRequestController;
use App\Http\Controllers\WebhookController;
use Illuminate\Support\Facades\Route;

Route::post('/send', [EmailRequestController::class, 'send']);
Route::get('/status', [EmailRequestController::class, 'status']);

// Webhooks
Route::post('/webhook', [WebhookController::class, 'webhook']);
