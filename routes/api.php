<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsdetailController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\OrderController;
use App\Http\controllers\AuthController;
use App\Http\controllers\RateChartController;



Route::get('/abc', [StudentsdetailController::class, 'exportStudents']);

Route::prefix('students')->controller(App\Http\Controllers\StudentController::class)->group(function () {
    Route::get('/', 'getAllStudents'); // GET All (Using Params)
    Route::post('/fetch-all', 'fetchAllStudents'); // GET All (Using Payload)
    Route::get('/{id}', 'showStudentByParams'); // Get Student (Using Params)
    Route::post('/show', 'showStudentByPayload'); // POST Student (Using Payload)
    Route::post('/', 'storeStudent'); // Store new Students
    Route::put('/{id}', 'updateStudentByParams'); // Update the Student (Using Params) 
    Route::post('/update-student', 'updateStudentByPayload'); // Update the Student (Using payload)
    Route::delete('/{id}', 'destroyStudent');
    Route::post('/delete-student', 'destroyStudentByPayload'); // DELETE using Payload
});


// Route::put('/update-student', [StudentController::class, 'updateStudentByPayload']);
Route::get('/students/detail/data', [StudentsdetailController::class, 'getData']);
Route::get('/head-data', [StudentsdetailController::class, 'headData'])->middleware('throttle:60,1');;




// Route::post('/orders', [OrderController::class, 'createOrder']);
// Route::post('/get-all-orders', [OrderController::class, 'getAllOrders']);  // Get All Orders (Using Payload) without data {}
// Route::post('/get-order-id', [OrderController::class, 'getOrderById']);    // Get Order by ID (Using Payload)
// Route::post('/get-orders-by-email', [OrderController::class, 'getOrdersByEmail']);

// Route::post('/get-order', [OrderController::class, 'getOrders']);  // GET All (Using Payload with Filters) (All in one)

// Route::post('/update-order', [OrderController::class, 'updateOrder']);
// Route::post('/delete-order', [OrderController::class, 'deleteOrder']);



Route::post('/register', [AuthController::class, 'registerUser']);
Route::post('/login', [AuthController::class, 'loginUser']);
Route::get('/', [AuthController::class, 'test']);

// Protected routes (requires authentication)
Route::middleware(['auth:sanctum'])->group(function () {

    Route::post('/user', [AuthController::class, 'getUser']);
    Route::post('/logout', [AuthController::class, 'logoutUser']);
    Route::post('/user/update', [AuthController::class, 'updateUser']);

    Route::post('/orders', [OrderController::class, 'createOrder']);
    Route::post('/get-all-orders', [OrderController::class, 'getAllOrders']);  // Get All Orders (Using Payload) without data {}
    // Route::post('/get-order-id', [OrderController::class, 'getOrderById']);    // Get Order by ID (Using Payload)
    Route::post('/get-orders-by-email', [OrderController::class, 'getOrdersByEmail']);

    Route::post('/get-order', [OrderController::class, 'getOrders']);  // GET All (Using Payload with Filters) (All in one)

    Route::post('/update-order', [OrderController::class, 'updateOrder']);
    Route::post('/delete-order', [OrderController::class, 'deleteOrder']);
});



// Route::middleware(['auth:sanctum', 'auth.admin'])->group(function () {
//     Route::post('/rate/create', [RateChartController::class, 'create']);
//     Route::post('/rate/get-all', [RateChartController::class, 'getAllRates']);
//     Route::post('/rate/get', [RateChartController::class, 'getRateById']);
//     Route::post('/rate/update', [RateChartController::class, 'updateRate']);
//     Route::post('/rate/delete', [RateChartController::class, 'deleteRate']);
// });


// Route::middleware(['auth:sanctum', 'auth.sub-admin'])->group(function () {
//     Route::post('/rate/create', [RateChartController::class, 'create']);
//     Route::post('/rate/get-all', [RateChartController::class, 'getAllRates']);
//     Route::post('/rate/get', [RateChartController::class, 'getRateById']);
//     Route::post('/rate/update', [RateChartController::class, 'updateRate']);
//     Route::post('/rate/delete', [RateChartController::class, 'deleteRate']);
// });


Route::middleware(['auth:sanctum', 'admin_or_sub-admin'])
    ->controller(RateChartController::class)
    ->prefix('rate')
    ->group(function () {
        Route::post('/create', 'createRate');
        // Route::post('/get-all', 'getAllRates');
        Route::post('/get-rates', 'getRates');
        Route::post('/get', 'getRateById');
        Route::post('/update', 'updateRate');
        Route::post('/delete', 'deleteRate');
    });


// Route::middleware(['auth:sanctum', 'admin_or_sub-admin'])
//     ->controller(RateChartController::class)
//     ->prefix('admin')
//     ->group(function () {
//         Route::post('/user', [AuthController::class, 'getUser']);
//         Route::post('/logout', [AuthController::class, 'logoutUser']);
//         Route::post('/user/update', [AuthController::class, 'updateUser']);
    
//         Route::post('/orders', [OrderController::class, 'createOrder']);
//         Route::post('/get-all-orders', [OrderController::class, 'getAllOrders']);  // Get All Orders (Using Payload) without data {}
//         // Route::post('/get-order-id', [OrderController::class, 'getOrderById']);    // Get Order by ID (Using Payload)
//         Route::post('/get-orders-by-email', [OrderController::class, 'getOrdersByEmail']);
    
//         Route::post('/get-order', [OrderController::class, 'getOrders']);  // GET All (Using Payload with Filters) (All in one)
    
//         Route::post('/update-order', [OrderController::class, 'updateOrder']);
//         Route::post('/delete-order', [OrderController::class, 'deleteOrder']);
//     });











// Route::get('/orders/{orderId}', [OrderController::class, 'getOrder']);
// Route::delete('/orders/{orderId}', [OrderController::class, 'deleteOrder']);

// Route::get('/orders/{id}', [OrderController::class, 'getOrderById']);
// Route::get('/orders/user/{email}', [OrderController::class, 'getOrdersByEmail']);