<?php

use App\Http\Controllers\Api;
use App\Services\RouteService;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| User API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::name('api.')->group(function () {
    RouteService::apiResource('activities', Api\ActivityController::class, ['index', 'store']);
    RouteService::apiResource('configurations', Api\ConfigurationController::class, ['index', 'store']);
    RouteService::apiResource('csv-import', Api\CsvImportController::class, ['store']);
    RouteService::apiResource('data-collector-actions/add-product', Api\DataCollectorActions\AddProductController::class, ['store']);
    RouteService::apiResource('data-collector-actions/import-as-stocktake', Api\DataCollectorActions\ImportAsStocktakeController::class)->only(['store']);
    RouteService::apiResource('data-collector', Api\DataCollectorController::class);
    RouteService::apiResource('heartbeats', Api\HeartbeatsController::class, ['index']);
    RouteService::apiResource('inventory-movements', Api\InventoryMovementController::class, ['store', 'index']);
    RouteService::apiResource('inventory', Api\InventoryController::class, ['index', 'store']);
    RouteService::apiResource('jobs', Api\JobsController::class, ['store', 'index']);
    RouteService::apiResource('logs', Api\LogController::class, ['index']);
    RouteService::apiResource('mail-templates', Api\MailTemplateController::class, ['index', 'update']);
    RouteService::apiResource('modules', Api\ModuleController::class, ['index', 'update']);
    RouteService::apiResource('navigation-menu', Api\NavigationMenuController::class);
    RouteService::apiResource('orders-addresses', Api\OrderAddressController::class, ['index', 'store']);
    RouteService::apiResource('orders-statuses', Api\OrderStatusController::class);
    RouteService::apiResource('orders', Api\OrderController::class, ['index', 'store', 'update']);
    RouteService::apiResource('picklist', Api\PicklistController::class, ['index']);
    RouteService::apiResource('print-jobs', Api\PrintJobController::class, ['store']);
    RouteService::apiResource('products-aliases', Api\ProductAliasController::class, ['index', 'store']);
    RouteService::apiResource('products', Api\ProductController::class, ['index', 'store']);
    RouteService::apiResource('quantity-discount-product', Api\QuantityDiscountProductsController::class, ['index', 'store', 'destroy']);
    RouteService::apiResource('quantity-discounts', Api\QuantityDiscountsController::class);
    Route::apiResource('reports/inventory-transfers', Api\Reports\InventoryTransfersController::class)->only(['index']);
    Route::apiResource('reports/inventory', Api\Reports\InventoryController::class)->only(['index']);
    Route::apiResource('reports/picks', Api\Reports\PicksController::class)->only(['index']);
    Route::apiResource('reports/stocktake-suggestions', Api\Reports\StockTakeSuggestionsController::class)->only(['index']);
    RouteService::apiResource('restocking', Api\RestockingController::class)->only(['index']);
    RouteService::apiResource('shipments', Api\ShipmentController::class)->only(['store']);
    RouteService::apiResource('shipping-labels', Api\ShippingLabelController::class)->only(['store']);
    RouteService::apiResource('shipping-services', Api\ShippingServiceController::class)->only(['index']);
    RouteService::apiResource('stocktake-suggestions-details', Api\StocktakeSuggestionDetailController::class)->only(['index']);
    RouteService::apiResource('stocktake-suggestions', Api\StocktakeSuggestionController::class)->only(['index']);
    RouteService::apiResource('stocktakes', Api\StocktakesController::class)->only(['store']);
    RouteService::apiResource('transactions', Api\TransactionController::class)->only(['update']);
    RouteService::apiResource('warehouses', Api\WarehouseController::class)->only(['index', 'store', 'update', 'destroy']);
});

Route::prefix('modules')->name('api.modules.')->group(function () {
    Route::apiResource('api2cart/connections', Api\Modules\Api2cart\Api2cartConnectionController::class, ['as' => 'api2cart'])->only(['index', 'store', 'destroy']);
    Route::apiResource('api2cart/products', Api\Modules\Api2cart\ProductsController::class, ['as' => 'api2cart'])->only(['index']);
    Route::apiResource('automations/config', Api\Modules\OrderAutomations\ConfigController::class, ['as' => 'automations'])->only(['index']);
    Route::apiResource('automations', Api\Modules\OrderAutomations\AutomationController::class);
    Route::apiResource('dpd-ireland/connections', Api\Modules\DpdIreland\DpdIrelandController::class, ['as' => 'dpd-ireland'])->only(['index', 'store', 'destroy']);
    Route::apiResource('dpd-uk/dpd-uk-connections', Api\Modules\DpdUk\DpdUkConnectionController::class, ['as' => 'dpd-uk'])->only(['index', 'store', 'destroy']);
    Route::apiResource('printnode/clients', Api\Modules\Printnode\ClientController::class, ['as' => 'printnode'])->only(['index', 'store', 'destroy']);
    Route::apiResource('printnode/printers', Api\Modules\Printnode\PrinterController::class)->only(['index']);
    Route::apiResource('printnode/printjobs', Api\Modules\Printnode\PrintJobController::class, ['as' => 'printnode'])->only(['store']);
    Route::apiResource('rms_api/connections', Api\Modules\Rmsapi\RmsapiConnectionController::class, ['as' => 'rmsapi'])->only(['index', 'store', 'destroy']);
    Route::apiResource('slack/config', Api\Modules\Slack\ConfigController::class)->only(['index', 'store']);
    Route::apiResource('stocktake-suggestions/configuration', Api\Modules\StocktakeSuggestions\ConfigurationController::class)->only(['index', 'store']);
    Route::apiResource('webhooks/subscriptions', Api\Modules\Webhooks\SubscriptionController::class, ['as' => 'webhooks'])->only(['index', 'store']);
    Route::apiResource('magento-api/connections', Api\Modules\MagentoApi\MagentoApiConnectionController::class, ['as' => 'magento-api'])->except(['show']);
    Route::apiResource('magento2msi/connections', Api\Modules\Magento2msi\Magento2MsiConnectionController::class)->except(['show']);
    Route::apiResource('active-orders-inventory-reservations/configuration', App\Modules\ActiveOrdersInventoryReservations\src\Http\Controllers\Api\Modules\ActiveOrdersInventoryReservationsController::class)->only(['index', 'update']);
});

Route::apiResource('admin/user/roles', Api\UserRoleController::class, ['as' => 'admin.users'])->only(['index']);
Route::apiResource('admin/users', Api\UserController::class);
Route::apiResource('csv-import/data-collections', Api\CsvImport\DataCollectionsImportController::class)->names('csv-import-data-collections')->only(['store']);
Route::apiResource('data-collector-records', Api\DataCollectorRecordController::class)->only(['store', 'index']);
Route::apiResource('data-collector/comments', Api\DataCollectionCommentController::class)->only(['index', 'store']);
Route::apiResource('modules/autostatus/picking/configuration', Api\Modules\AutoStatus\ConfigurationController::class, ['as' => 'modules.autostatus.picking'])->only('index', 'store');
Route::apiResource('order/addresses', Api\OrderAddressController::class, ['as' => 'orders'])->only(['update']);
Route::apiResource('order/comments', Api\OrderCommentController::class, ['as' => 'orders'])->only(['index', 'store']);
Route::apiResource('order/payments', Api\OrderPaymentController::class, ['as' => 'orders'])->only(['index']);
Route::apiResource('order/products', Api\OrderProductController::class, ['as' => 'orders'])->only(['index', 'update']);
Route::apiResource('order/shipments', Api\OrderShipmentController::class, ['as' => 'orders'])->only(['index', 'store']);
Route::apiResource('orders/products/shipments', Api\OrderProductShipmentController::class)->only(['store']);
Route::apiResource('packlist/order', Api\PacklistOrderController::class, ['as' => 'packlist'])->only(['index']);
Route::apiResource('picklist/picks', Api\Picklist\PicklistPickController::class)->only(['store', 'destroy']);
Route::apiResource('product/aliases', Api\ProductAliasController::class, ['as' => 'product'])->only(['index']);
Route::apiResource('product/tags', Api\ProductTagController::class)->only(['index']);
Route::apiResource('settings/modules/automations/run', Api\Modules\OrderAutomations\RunAutomationController::class, ['as' => 'settings.modules.automations'])->only(['store']);
Route::apiResource('settings/user/me', Api\UserMeController::class)->only(['index', 'store']);
Route::apiResource('settings/widgets', Api\WidgetController::class)->only(['store', 'update']);
Route::post('pdf/download', [Api\PDF\PdfDownloadController::class, 'update']);
Route::post('pdf/preview', [Api\PDF\PdfPreviewController::class, 'update']);
Route::post('pdf/print', [Api\PDF\PdfPrintController::class, 'update']);
Route::post('transaction/receipt-print', [Api\TransactionController::class, 'printReceipt']);
Route::post('transaction/receipt', [Api\TransactionController::class, 'sendReceipt']);
Route::put('print/order/{order_number}/{view}', [Api\PrintOrderController::class, 'update']);
