<?php


use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StudentsdetailController;
use App\Http\Controllers\StudentController;
use App\Http\Controllers\OrderController;
use App\Http\controllers\AuthController;
use App\Http\controllers\RateChartController;




Route::prefix('students')->controller(App\Http\Controllers\StudentController::class)->group(function () {
    Route::get('/', 'getAllStudents'); // GET All (Using Params)

    Route::post('/get-student', 'getStudents');
    Route::post('/export-student', 'exportStudent');

    Route::get('/{id}', 'getStudentByParams');
    Route::post('/show', 'getStudentsById');

    Route::post('/', 'createStudent');

    Route::put('/{id}', 'updateStudentByParams');
    Route::post('/update-student', 'updateStudent');

    Route::delete('/{id}', 'deleteStudentByParams');
    Route::post('/delete-student', 'deleteStudent');
});

// Route::put('/update-student', [StudentController::class, 'updateStudentByPayload']);
Route::get('/students/detail/data', [StudentsdetailController::class, 'getData']);
Route::get('/head-data', [StudentsdetailController::class, 'headData'])->middleware('throttle:60,1');;




Route::prefix('v1')->group(function () {
    Route::post('/register', [AuthController::class, 'registerUser']);
    Route::post('/admin-register', [AuthController::class, 'AdminRegister']);
    Route::post('/login', [AuthController::class, 'loginUser']);
    Route::get('/', [AuthController::class, 'test']);

    Route::middleware(['auth:sanctum'])->group(function () {

        Route::controller(AuthController::class)->group(function () {
            Route::prefix('user')->group(function () {
                Route::post('/get', 'getUser');
                Route::post('/update', 'updateUser');
                Route::post('/logout', 'logoutUser');
            });
        });

        Route::prefix('order')->controller(OrderController::class)->group(function () {
            Route::post('/create-order', 'createOrder');

            Route::post('/get-order', 'getOrders'); // (Filters) (All in one)
            Route::post('/export-order', 'exportOrders');
            Route::post('/get-active', 'getActiveOrders');

            // Route::post('/get-by-email', 'getOrdersByEmail');
            Route::post('/update', 'updateOrder');
            Route::post('/cancel-order', 'updateCancelOrder');
        });
    });

    Route::middleware(['auth:sanctum', 'admin_or_sub-admin'])->group(function () {
        Route::prefix('order')->controller(OrderController::class)->group(function () {
            Route::post('/order-status', 'updateOrderStatus');
        });
    });
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
    ->prefix('v1/rate')
    ->group(function () {
        Route::post('/create', 'createRate');

        Route::post('/get-rates', 'getRates');
        Route::post('/export-rates', 'exportRates');

        Route::post('/update', 'updateRate');
        Route::post('/delete', 'deleteRate');
    });
