<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\EmailVerificationRequest;
use Illuminate\Http\Request;
use Spatie\WelcomeNotification\WelcomesNewUsers;
use App\Http\Controllers\Auth\WelcomeController;

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


Route::get('/', function () {
    if(!Auth::check()){
        return view('auth.login',['title'=>'Login']);
    }else{
        return redirect()->route('dashboard.index');
    }
});


Route::group(['middleware' => ['web', WelcomesNewUsers::class,]], function () {
    Route::get('welcome/{user}', [WelcomeController::class, 'showWelcomeForm'])->name('welcome');
    Route::post('welcome/{user}', [WelcomeController::class, 'savePassword']);
});

Auth::routes(['verify'=>true,'register' => false]);

Route::group(['middleware' => ['auth','web','verified']], function() {
    

    //dashboard
    Route::get('/dashboard', [App\Http\Controllers\DashboardController::class, 'index'])->name('dashboard.index');
    Route::get('/dashboard-filter/{start_date}/{end_date}', [App\Http\Controllers\DashboardController::class,'filter'])->name('dashboard.filter');
    
    //relation switcher
    Route::post('relation/switch',[App\Http\Controllers\RelationController::class,'switch'])->name('relations.switch');
    Route::post('product/search',[App\Http\Controllers\RelationController::class,'search_product'])->name('relations.product.search');
    Route::post('embroidery/search',[App\Http\Controllers\RelationController::class,'search_embroidery_stock'])->name('relations.embroidery.search');
    Route::post('final/search',[App\Http\Controllers\RelationController::class,'search_final_stock'])->name('relations.final.search');
    Route::post('cut-pieces/stock/product',[App\Http\Controllers\RelationController::class,'get_product_cut_pieces_stock'])->name('relations.stock_count.product');
    Route::post('cut-pieces/stock/embroidery',[App\Http\Controllers\RelationController::class,'get_embroidery_cut_pieces_stock'])->name('relations.stock_count.embroidery');
    Route::post('cut-pieces/stock/final',[App\Http\Controllers\RelationController::class,'get_final_cut_pieces_stock'])->name('relations.stock_count.final');

    //download
    Route::post('download/',[App\Http\Controllers\DownloadController::class,'index'])->name('download.index');
    
    //products
    Route::resource('products', App\Http\Controllers\ProductController::class)->except('update');
    
    Route::get('select-products/{production}', [App\Http\Controllers\ProductController::class,'select_index'])->name('products.select');
    Route::get('products/all/export', [App\Http\Controllers\ProductController::class,'export'])->name('products.export');
    
    Route::post('products/list',[App\Http\Controllers\ProductController::class,'list'])->name('products.list');
    Route::post('products/delete-by-selection',[App\Http\Controllers\ProductController::class,'delete_by_selection'])->name('products.delete_by_selection');
    Route::post('products/{product}/update',[App\Http\Controllers\ProductController::class,'update'])->name('products.update');
    

    Route::prefix('export')->name('export.')->group(function () {
        Route::post('/products',[App\Http\Controllers\ExportController::class,'products'])->name('products')->middleware('can:product-manage');
        Route::post('/fabrics',[App\Http\Controllers\ExportController::class,'fabrics'])->name('fabrics')->middleware('can:fabric-manage');
        Route::post('/fabric/{fabric}/rolls',[App\Http\Controllers\ExportController::class,'rolls'])->name('rolls')->middleware('can:roll-manage');
        Route::post('/cut-pieces',[App\Http\Controllers\ExportController::class,'cut_pieces'])->name('cut_pieces')->middleware('can:cut-piece-manage');
        Route::post('/productions',[App\Http\Controllers\ExportController::class,'productions'])->name('productions')->middleware('can:production-manage');
        Route::post('/embroidery/stock',[App\Http\Controllers\ExportController::class,'embroidery_stocks'])->name('embroidery_stocks')->middleware('can:embroidery-stocks-manage');
        Route::post('/tailors',[App\Http\Controllers\ExportController::class,'tailors'])->name('tailors')->middleware('can:tailor-manage');
        Route::post('/stitches',[App\Http\Controllers\ExportController::class,'stitches'])->name('stitches')->middleware('can:stitches-manage');
        Route::post('/final/stock',[App\Http\Controllers\ExportController::class,'final_stocks'])->name('final_stocks')->middleware('can:final-stock-manage');
        Route::post('/shipments',[App\Http\Controllers\ExportController::class,'shipments'])->name('shipments')->middleware('can:shipment-manage');
        Route::post('/users',[App\Http\Controllers\ExportController::class,'users'])->name('users')->middleware('can:user-manage');
        Route::post('/rates',[App\Http\Controllers\ExportController::class,'rates'])->name('rates')->middleware('can:rate-manage');
    });


    
    //fabrics
    Route::resource('fabrics', App\Http\Controllers\FabricController::class)->except('update');
    Route::post('fabrics/list',[App\Http\Controllers\FabricController::class,'list'])->name('fabrics.list');
    Route::post('fabrics/delete-by-selection',[App\Http\Controllers\FabricController::class,'delete_by_selection'])->name('fabrics.delete_by_selection');
    Route::post('fabrics/{fabric}/update',[App\Http\Controllers\FabricController::class,'update'])->name('fabrics.update');
    
    //cut pieces
    Route::get('cut-pieces',[App\Http\Controllers\CutPieceController::class,'index'])->name('cut_pieces.index');
    Route::post('cut-pieces/list',[App\Http\Controllers\CutPieceController::class,'list'])->name('cut_pieces.list');
    
    //rolls
    Route::get('fabrics/{fabric}/rolls',[App\Http\Controllers\RollController::class,'index'])->name('rolls.index');
    Route::post('rolls/list',[App\Http\Controllers\RollController::class,'list'])->name('rolls.list');
    Route::post('rolls/update',[App\Http\Controllers\RollController::class,'update'])->name('rolls.update');
    Route::post('rolls/add/history',[App\Http\Controllers\RollController::class,'history'])->name('rolls.add.history');
    Route::post('rolls/quantity/history',[App\Http\Controllers\RollController::class,'quantity_history'])->name('rolls.quantity.history');
    
    
    //production
    
    Route::resource('productions', App\Http\Controllers\ProductionController::class)->except('update');
    Route::post('productions/{production}/update',[App\Http\Controllers\ProductionController::class,'update'])->name('productions.update');
    Route::post('productions/list',[App\Http\Controllers\ProductionController::class,'list'])->name('productions.list');
    Route::post('productions/{production}/receive',[App\Http\Controllers\ProductionController::class,'receive'])->name('productions.receive');
    Route::post('productions/{production}/save-challan',[App\Http\Controllers\ProductionController::class,'save_challan'])->name('productions.save_challan');
    Route::post('productions/products',[App\Http\Controllers\ProductionController::class,'products'])->name('productions.products');
    Route::post('productions/receive/history',[App\Http\Controllers\ProductionController::class,'history'])->name('productions.receive.history');
    //bucket
    Route::post('productions/bucket',[App\Http\Controllers\ProductionController::class,'bucket'])->name('productions.bucket');
    Route::get('select-embroidery-stock/{stitching}', [App\Http\Controllers\ProductionController::class,'select_embroidery_stock'])->name('productions.select_embroidery_stock');
    
    //embroidery stock
    Route::get('stock/embroidery',[App\Http\Controllers\ProductionController::class,'embroidery_stock'])->name('productions.stock');
    Route::get('productions/{production}/print',[App\Http\Controllers\ProductionController::class,'print'])->name('productions.print');
    Route::post('stock/list/embroidery',[App\Http\Controllers\ProductionController::class,'stock_list'])->name('productions.stock.list');
    
    
    //stitching
    Route::resource('stitches', App\Http\Controllers\StitchingController::class)->except('update')->parameters([
        'stitches' => 'stitching']);
        Route::post('stitches/{stitching}/update',[App\Http\Controllers\StitchingController::class,'update'])->name('stitches.update');
        Route::post('stitches/list',[App\Http\Controllers\StitchingController::class,'list'])->name('stitches.list');
        Route::post('stitches/products',[App\Http\Controllers\StitchingController::class,'products'])->name('stitches.products');
        
        Route::post('stitches/receive/history',[App\Http\Controllers\StitchingController::class,'history'])->name('stitches.receive.history');
        
        Route::post('stitches/{stitching}/receive',[App\Http\Controllers\StitchingController::class,'receive'])->name('stitches.receive');
        Route::post('stitches/{stitching}/save-challan',[App\Http\Controllers\StitchingController::class,'save_challan'])->name('stitches.save_challan');
        //bucket
        Route::post('stitches/bucket',[App\Http\Controllers\StitchingController::class,'bucket'])->name('stitches.bucket');
        Route::get('select-final-stock/{shipment}', [App\Http\Controllers\StitchingController::class,'select_final_stock'])->name('stitches.select_final_stock');
        
        //final stock
    Route::get('stock/final',[App\Http\Controllers\StitchingController::class,'stitching_stock'])->name('stitches.stock');
    Route::get('stitches/{stitching}/print',[App\Http\Controllers\StitchingController::class,'print'])->name('stitches.print');
    Route::post('stock/list/final',[App\Http\Controllers\StitchingController::class,'stock_list'])->name('stitches.stock.list');
    

    //shipment
    Route::resource('shipments', App\Http\Controllers\ShipmentController::class)->except('update');
    Route::post('shipments/{shipment}/update',[App\Http\Controllers\ShipmentController::class,'update'])->name('shipments.update');
    Route::post('shipments/list',[App\Http\Controllers\ShipmentController::class,'list'])->name('shipments.list');
    Route::post('shipments/products',[App\Http\Controllers\ShipmentController::class,'products'])->name('shipments.products');
    //bucket
    Route::post('shipments/bucket',[App\Http\Controllers\ShipmentController::class,'bucket'])->name('shipments.bucket');
    
    
    //permission
    Route::resource('permissions', App\Http\Controllers\PermissionController::class)->except('update');
    Route::post('permissions/{permission}/update',[App\Http\Controllers\PermissionController::class,'update'])->name('permissions.update');
    Route::post('permissions/list',[App\Http\Controllers\PermissionController::class,'list'])->name('permissions.list');
    
    
    //role
    Route::resource('roles', App\Http\Controllers\RoleController::class)->except('update');
    Route::post('roles/{role}/update',[App\Http\Controllers\RoleController::class,'update'])->name('roles.update');
    Route::post('roles/list',[App\Http\Controllers\RoleController::class,'list'])->name('roles.list');
    Route::get('my-permissions',[App\Http\Controllers\RoleController::class,'my_permissions'])->name('roles.my_permissions');
    
    //user
    Route::resource('users', App\Http\Controllers\UserController::class)->except('update');
    Route::post('users/{user}/update',[App\Http\Controllers\UserController::class,'update'])->name('users.update');
    
    Route::post('users/list', [App\Http\Controllers\UserController::class,'list'])->name('users.list');
    Route::post('users/{user}/ban', [App\Http\Controllers\UserController::class,'ban'])->name('users.ban');
    Route::post('users/{user}/invisible-login', [App\Http\Controllers\UserController::class,'invisible_login'])->name('users.invisible_login');
    
    Route::post('users/{user}/email-verification-resend', [App\Http\Controllers\UserController::class,'resend_email'])->name('users.email.verification');
    Route::post('users/{user}/welcome-email-resend', [App\Http\Controllers\UserController::class,'resend_welcome_email'])->name('users.email.welcome');
    //profile
    Route::get('profile', [App\Http\Controllers\UserController::class,'profile'])->name('profile.edit')->middleware('password.confirm');
    Route::post('profile', [App\Http\Controllers\UserController::class,'profile_update'])->name('profile.update')->middleware('password.confirm');


    //settings
    Route::prefix('settings')->name('settings.')->middleware('can:settings-edit')->group(function () {
        Route::get('/',[App\Http\Controllers\SettingController::class,'index'])->name('index');
        Route::post('/',[App\Http\Controllers\SettingController::class,'save'])->name('save');
    });

    //tailors
    Route::resource('tailors', App\Http\Controllers\TailorController::class)->except('update');
    Route::prefix('tailors')->name('tailors.')->group(function () {
        Route::post('/{tailor}/update',[App\Http\Controllers\TailorController::class,'update'])->name('update');
        Route::post('/list',[App\Http\Controllers\TailorController::class,'list'])->name('list'); 
    });

    //tailors
    Route::resource('rates', App\Http\Controllers\RateController::class)->except('update');
    Route::prefix('rates')->name('rates.')->group(function () {
        Route::post('/{rate}/update',[App\Http\Controllers\RateController::class,'update'])->name('update');
        Route::post('/list',[App\Http\Controllers\RateController::class,'list'])->name('list'); 
    });

    
        
});



