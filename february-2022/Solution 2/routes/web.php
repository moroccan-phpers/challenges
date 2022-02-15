<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

#################################### microService part ##################################################
# provides the current state for a specified request
Route::get('/status/{request_id}','RequestsHandler@status');

# handles sending Requests to the email delivery service
Route::post('/send','RequestsHandler@send');

# receives the webhook call from the email delivery service and updates the status of the request
Route::post('/callback','RequestsHandler@callback');

#################################### end microService part ###############################################




#################################### Delivery service example ############################################

# example for the mail delivery service api
Route::post('/api/emails','DeliveryServiceHandler@receiver');

#################################### end Deliviry service example ########################################
