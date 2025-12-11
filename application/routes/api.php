<?php

use App\Http\Controllers\api\v1\AppointmentController;
use App\Http\Controllers\api\v1\BlogController;
use App\Http\Controllers\api\v1\CancelationController;
use App\Http\Controllers\api\v1\DoctorController;
use App\Http\Controllers\api\v1\HospitalController;
use App\Http\Controllers\api\v1\PatientController;
use App\Http\Controllers\api\v1\ReviewController;
use App\Http\Controllers\api\v1\ScheduleController;
use App\Http\Controllers\api\v1\SpecialtyController;
use App\Http\Controllers\api\v1\BranchController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\api\v1\AuthController;
use App\Http\Controllers\api\v1\ProfileController;


/* Route::middleware(['auth:api'])->group(function () {
    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('profile-update', [ProfileController::class, 'profileUpdate']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);
    Route::post('change-pin', [ProfileController::class, 'changePin']);
    
    Route::get('user', [AuthController::class, 'getUser']); // Correct syntax
    Route::post('logout', [AuthController::class, 'logout']);
}); */

Route::controller(AuthController::class)->group(function($route){

    $route->post('email-verification','emailVerification');
    $route->post('otp-verify','verifyOtp');
    $route->post('registeration', 'register');
    $route->post('login', 'login');
    $route->post('email-exist-verification','forgetEmailVerification');
    $route->post('reset-password','resetPassword');

    /// ALl route related of blog
    $route->group(['prefix' => 'blog'], function($route){
        $route->get('/', [BlogController::class,'index']);
    });

    /// ALl route related of cancelation
    $route->group(['prefix' => 'cancelation'], function($route){
        $route->get('/', [CancelationController::class,'index']);
    });

    /// ALl route related of doctor
    $route->group(['prefix' => 'doctor'], function($route){
        $route->get('/', [DoctorController::class,'index']);
        $route->get('/details/{id}/{branchId?}', [DoctorController::class,'show']);
        $route->get('/schedule/{doctor_id}/{schedule_id}/{appointment_date}', [DoctorController::class,'doctorSchedule']);
        $route->get('/profile/{id}', [DoctorController::class,'profile']);
    });

    /// ALl route related of hospital
    $route->group(['prefix' => 'hospital'], function($route){
        $route->get('/', [HospitalController::class,'index']);
        $route->get('/{id}', [HospitalController::class,'show']);
    });

    /// ALl route related of review
    $route->group(['prefix' => 'review'], function($route){
        $route->get('/', [ReviewController::class,'index']);
    });

    /// ALl route related of specscheduleiality
    $route->group(['prefix' => 'schedule'], function($route){
        $route->get('/', [ScheduleController::class,'index']);
    });

    /// ALl route related of speciality
    $route->group(['prefix' => 'speciality'], function($route){
        $route->get('/', [SpecialtyController::class,'index']);
    });

    $route->group(['prefix' => 'branch'], function($route){
        $route->get('/{doctor_id?}', [BranchController::class,'index']);
    });
});


Route::group(['prefix' => '/', 'middleware' => 'auth:api', 'namespace' => 'api\v1',], function($route) {

    Route::get('profile', [ProfileController::class, 'profile']);
    Route::post('profile-update', [ProfileController::class, 'profileUpdate']);
    Route::post('change-password', [ProfileController::class, 'changePassword']);
    Route::post('change-pin', [ProfileController::class, 'changePin']);
    
    Route::get('user', [AuthController::class, 'getUser']); // Correct syntax
    Route::post('logout', [AuthController::class, 'logout']);

    /// ALl route related of appointment    
    $route->group(['prefix' => 'appointment'], function($route){
        $route->get('/{type}', [AppointmentController::class,'index']);
        $route->get('/details/{id}', [AppointmentController::class,'show']);
        $route->post('/submit', [AppointmentController::class,'store']);
        $route->post('/cancel/{id}', [AppointmentController::class,'cancelAppointment']);
    });

    $route->group(['prefix' => 'patient'], function($route){
        $route->get('/familyList', [PatientController::class,'index']);
        $route->post('/addFamily', [PatientController::class,'store']);
        $route->post('/updateFamily/{id}', [PatientController::class,'updateFamily']);
        $route->delete('/deleteFamily/{id}', [PatientController::class,'deleteFamily']);
        $route->post('/restoreFamily/{id}', [PatientController::class,'restoreFamily']);
        $route->delete('/forceDeleteFamily/{id}', [PatientController::class,'forceDeleteFamily']);

        /** Document APIs */
        $route->get('/documentList', [PatientController::class,'documentList']);
        $route->post('/documentUpload', [PatientController::class,'documentUpload']);
        $route->post('/documentUpdate/{id}', [PatientController::class,'documentUpdate']);
        $route->delete('/deleteDocument/{id}', [PatientController::class,'deleteDocument']);
        $route->post('/restoreDocument/{id}', [PatientController::class,'restoreDocument']);
        $route->delete('/forceDeleteDocument/{id}', [PatientController::class,'forceDeleteDocument']);
    });
});
