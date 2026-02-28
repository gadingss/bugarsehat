<?php

use App\Http\Controllers\ActivationOrderController;
use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\CheckinReportController;
use App\Http\Controllers\ConfigurationPaymentController;
use App\Http\Controllers\HistoryMembershipController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IncomeReportController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\LandingPagePreviewController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\MembershipController;
use App\Http\Controllers\MembershipReportController;
use App\Http\Controllers\MemberTransactionController;
use App\Http\Controllers\PacketMembershipController;
use App\Http\Controllers\PenggunaController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\ProductTransactionController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RoleAssignmentController;
use App\Http\Controllers\ServiceController;
use App\Http\Controllers\ServiceTransactionController;
use App\Http\Controllers\TransactionReportController;
use App\Http\Controllers\Trainer\DashboardController as TrainerDashboardController;
use App\Http\Controllers\Trainer\ScheduleController as TrainerScheduleController;
use App\Http\Controllers\Member\ScheduleController as MemberScheduleController;
use App\Http\Controllers\Member\BookingController as MemberBookingController;
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
Route::get('/cek-midtrans', function () {
    return config('midtrans.serverKey');
});
Route::get('/', [LandingPagePreviewController::class, 'index'])->name('landing_preview');
Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'index'])->name('login');
    Route::get('/register', [LoginController::class, 'register'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->name('register.post');
    Route::post('/login', [AuthController::class, 'login'])->name('web.login');
});

// midtrans notification callback (no auth because it comes from external service)
Route::post('/midtrans/notification', [\App\Http\Controllers\MidtransController::class, 'notification'])->name('midtrans.notification');
Route::middleware('auth:web')->group(function () {
    Route::get('/home', [HomeController::class, 'index'])->name('home')->middleware(['can:home']);
    Route::get('/pengguna', [PenggunaController::class, 'index'])->name('pengguna.index')->middleware(['can:pengguna']);
    Route::get('/role_assignment', [RoleAssignmentController::class, 'index'])->name('role_assignment')->middleware(['can:role_assignment']);
    Route::get('/packet_membership', [PacketMembershipController::class, 'index'])->name('packet_membership')->middleware(['can:packet_membership']);
    Route::get('/packet_membership/{id}', [PacketMembershipController::class, 'show'])->name('packet_membership.show')->middleware(['can:packet_membership']);
    Route::get('/packet_membership/{id}/edit', [PacketMembershipController::class, 'edit'])->name('packet_membership.edit')->middleware(['can:packet_membership']);
    Route::put('/packet_membership/{id}', [PacketMembershipController::class, 'update'])->name('packet_membership.update')->middleware(['can:packet_membership']);
    Route::delete('/packet_membership/{id}', [PacketMembershipController::class, 'destroy'])->name('packet_membership.destroy')->middleware(['can:packet_membership']);

    // New routes for packet selection and purchase
    Route::get('/packet-membership', [PacketMembershipController::class, 'index'])->name('packet_membership');
    Route::get('/packet-membership/checkout/{id}', [PacketMembershipController::class, 'selectPacket'])->name('packet_membership.checkout');
    Route::post('/packet-membership/purchase/{id}', [PacketMembershipController::class, 'purchasePacket'])->name('packet_membership.purchase');
    Route::get('/packet-membership/payment/{id}', [PacketMembershipController::class, 'paymentPage'])->name('packet_membership.payment');
    Route::get('/packet-membership/success/{id}', [PacketMembershipController::class, 'successPage'])->name('packet_membership.success');
    Route::post('/packet-membership/activate/{id}', [PacketMembershipController::class, 'activateMembership'])->name('packet_membership.activate');



    // Profile Management Routes
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [ProfileController::class, 'changePassword'])->name('profile.change-password');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.update-password');
    Route::get('/profile/membership-status', [ProfileController::class, 'membershipStatus'])->name('profile.membership-status');
    Route::get('/profile/visit-history', [ProfileController::class, 'visitHistory'])->name('profile.visit-history');

    // Membership Management Routes
    Route::get('/membership', [MembershipController::class, 'index'])->name('membership.index');
    Route::get('/membership/history', [MembershipController::class, 'history'])->name('membership.history');
    Route::get('/membership/renewal', [MembershipController::class, 'renewal'])->name('membership.renewal');
    Route::post('/membership/renewal', [MembershipController::class, 'processRenewal'])->name('membership.process-renewal');
    Route::get('/membership/{id}/payment', [MembershipController::class, 'payment'])->name('membership.payment');
    Route::get('/membership/{id}/activate', [MembershipController::class, 'activate'])->name('membership.activate');
    Route::get('/membership/{id}/success', [MembershipController::class, 'success'])->name('membership.success');
    Route::delete('/membership/{id}/cancel', [MembershipController::class, 'cancel'])->name('membership.cancel');

    // New Member Features
    Route::get('/membership/upgrade', [MembershipController::class, 'upgrade'])->name('membership.upgrade');
    Route::post('/membership/upgrade', [MembershipController::class, 'processUpgrade'])->name('membership.process-upgrade');
    Route::get('/membership/statistics', [MembershipController::class, 'statistics'])->name('membership.statistics');
    Route::get('/membership/benefits', [MembershipController::class, 'benefits'])->name('membership.benefits');
    Route::get('/membership/packages', [MembershipController::class, 'packages'])->name('membership.packages');

    // Staff Features - Member Management
    Route::prefix('staff')->name('staff.')->middleware(['role:User:Staff|User:Owner'])->group(function () {
        Route::get('/member-management', [\App\Http\Controllers\Staff\MemberManagementController::class, 'index'])->name('member-management.index');
        Route::get('/members', [\App\Http\Controllers\Staff\MemberManagementController::class, 'members'])->name('member-management.members');
        Route::get('/members/{id}', [\App\Http\Controllers\Staff\MemberManagementController::class, 'show'])->name('member-management.show');
        Route::get('/members/{id}/edit', [\App\Http\Controllers\Staff\MemberManagementController::class, 'edit'])->name('member-management.edit');
        Route::put('/members/{id}', [\App\Http\Controllers\Staff\MemberManagementController::class, 'update'])->name('member-management.update');
        Route::get('/members/{id}/membership', [\App\Http\Controllers\Staff\MemberManagementController::class, 'manageMembership'])->name('member-management.membership');
        Route::post('/members/{id}/assign-membership', [\App\Http\Controllers\Staff\MemberManagementController::class, 'assignMembership'])->name('member-management.assign-membership');
        Route::post('/members/{id}/extend-membership', [\App\Http\Controllers\Staff\MemberManagementController::class, 'extendMembership'])->name('member-management.extend-membership');
        Route::post('/members/{id}/suspend-membership', [\App\Http\Controllers\Staff\MemberManagementController::class, 'suspendMembership'])->name('member-management.suspend-membership');
        Route::post('/members/{id}/reactivate-membership', [\App\Http\Controllers\Staff\MemberManagementController::class, 'reactivateMembership'])->name('member-management.reactivate-membership');
        Route::get('/membership-report', [\App\Http\Controllers\Staff\MemberManagementController::class, 'membershipReport'])->name('member-management.report');

        // Transaction Management
        Route::get('/transaction-management', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'index'])->name('transaction-management.index');
        Route::get('/transactions', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'transactions'])->name('transaction-management.transactions');
        Route::get('/transactions/{id}', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'show'])->name('transaction-management.show');
        Route::post('/transactions/{id}/validate', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'validateTransaction'])->name('transaction-management.validate');
        Route::post('/transactions/{id}/cancel', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'cancel'])->name('transaction-management.cancel');
        Route::post('/transactions/{id}/refund', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'refund'])->name('transaction-management.refund');
        Route::get('/transaction-reports', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'reports'])->name('transaction-management.reports');
        Route::post('/transaction-reports/export', [\App\Http\Controllers\Staff\TransactionManagementController::class, 'exportReport'])->name('transaction-management.export');

        // Product Purchase Validation
        Route::get('/product-purchase-validation', [\App\Http\Controllers\Staff\ProductApprovalController::class, 'index'])->name('product-purchase-validation.index');
    });

    // Owner Features - System Monitoring & Business Analytics
    Route::prefix('owner')->name('owner.')->middleware(['role:User:Owner'])->group(function () {
        // System Monitoring
        Route::get('/system-monitoring', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'index'])->name('system-monitoring.index');
        Route::get('/system-monitoring/server-status', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'serverStatus'])->name('system-monitoring.server-status');
        Route::get('/system-monitoring/database-health', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'databaseHealth'])->name('system-monitoring.database-health');
        Route::get('/system-monitoring/user-activity', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'userActivity'])->name('system-monitoring.user-activity');
        Route::get('/system-monitoring/logs', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'systemLogs'])->name('system-monitoring.logs');
        Route::get('/system-monitoring/security', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'securityStatus'])->name('system-monitoring.security');
        Route::get('/system-monitoring/performance', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'performanceReport'])->name('system-monitoring.performance');
        Route::get('/system-monitoring/alerts', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'alerts'])->name('system-monitoring.alerts');
        Route::post('/system-monitoring/alerts/{id}/resolve', [\App\Http\Controllers\Owner\SystemMonitoringController::class, 'resolveAlert'])->name('system-monitoring.resolve-alert');

        // Business Analytics
        Route::get('/business-analytics', [\App\Http\Controllers\Owner\BusinessAnalyticsController::class, 'index'])->name('business-analytics.index');
        Route::get('/business-analytics/revenue', [\App\Http\Controllers\Owner\BusinessAnalyticsController::class, 'revenueReport'])->name('business-analytics.revenue');
        Route::get('/business-analytics/members', [\App\Http\Controllers\Owner\BusinessAnalyticsController::class, 'memberAnalytics'])->name('business-analytics.members');
        Route::get('/business-analytics/products', [\App\Http\Controllers\Owner\BusinessAnalyticsController::class, 'productAnalytics'])->name('business-analytics.products');
        Route::get('/business-analytics/operations', [\App\Http\Controllers\Owner\BusinessAnalyticsController::class, 'operationalMetrics'])->name('business-analytics.operations');
        Route::get('/business-analytics/forecast', [\App\Http\Controllers\Owner\BusinessAnalyticsController::class, 'financialForecast'])->name('business-analytics.forecast');
        Route::post('/business-analytics/export', [\App\Http\Controllers\Owner\BusinessAnalyticsController::class, 'exportReport'])->name('business-analytics.export');

        // --- Owner Member Management (FIXED & MOVED HERE) ---
        Route::prefix('members')->name('member.')->group(function () {
            Route::get('/', [PenggunaController::class, 'daftarMember'])->name('index');
            Route::post('/', [PenggunaController::class, 'storeMember'])->name('store');
            Route::put('/{id}', [PenggunaController::class, 'updateMember'])->name('update');
            Route::delete('/{id}', [PenggunaController::class, 'destroyMember'])->name('destroy');
        });
    });

    // Product Routes
    Route::get('/products', [ProductController::class, 'index'])->name('products.index');
    Route::get('/products/{id}', [ProductController::class, 'show'])->name('products.show');
    Route::get('/products/catalog', [ProductController::class, 'catalog'])->name('products.catalog');
    Route::post('/products/{id}/purchase', [ProductController::class, 'purchase'])->name('products.purchase');
    Route::get('/products/payment/{transactionId}', [ProductController::class, 'payment'])->name('products.payment');
    Route::post('/products/payment/{transactionId}/confirm', [ProductController::class, 'confirmPayment'])->name('products.confirm-payment');
    Route::get('/products/success/{transactionId}', [ProductController::class, 'success'])->name('products.success');
    Route::get('/my-products', [ProductController::class, 'myProducts'])->name('products.my-products');
    Route::post('/products/use/{membershipProductId}', [ProductController::class, 'useProduct'])->name('products.use');
    // Tambahkan ini untuk tombol Beli
    Route::post('/products/purchase/{id}', [ProductController::class, 'purchase'])->name('products.purchase');
    Route::post('/products/payment/{transactionId}/confirm', [ProductController::class, 'confirmPayment'])->name('products.confirm-payment');


    // Service Routes
    Route::get('/services', [ServiceController::class, 'index'])->name('services.index');
    Route::get('/services/{id}', [ServiceController::class, 'show'])->name('services.show');
    Route::post('/services/book/{id}', [ServiceController::class, 'book'])->name('services.book');
    Route::post('/services/{id}/quick-book', [ServiceController::class, 'quickBook'])->name('services.quick-book');
    Route::get('/services/payment/{transactionId}', [ServiceController::class, 'payment'])->name('services.payment');
    Route::post('/services/payment/{transactionId}/confirm', [ServiceController::class, 'confirmPayment'])->name('services.confirm-payment');
    Route::get('/services/success/{transactionId}', [ServiceController::class, 'bookingSuccess'])->name('services.booking-success');
    Route::get('/my-bookings', [ServiceController::class, 'myBookings'])->name('services.my-bookings');
    Route::delete('/services/booking/{transactionId}/cancel', [ServiceController::class, 'cancelBooking'])->name('services.cancel-booking');
    Route::get('/services/qr-scan', [ServiceController::class, 'qrScan'])->name('services.qr-scan');
    Route::post('/services/qr-process', [ServiceController::class, 'processQrService'])->name('services.qr-process');

    // Check-in Routes
    Route::get('/checkin', [CheckinController::class, 'index'])->name('checkin.index');
    Route::post('/checkin', [CheckinController::class, 'checkin'])->name('checkin.checkin');
    Route::post('/checkout', [CheckinController::class, 'checkout'])->name('checkin.checkout');
    Route::get('/checkin/qr-scan', [CheckinController::class, 'qrScan'])->name('checkin.qr-scan');
    Route::post('/checkin/qr-process', [CheckinController::class, 'processQrCheckin'])->name('checkin.qr-process');
    Route::get('/checkin/history', [CheckinController::class, 'history'])->name('checkin.history');
    Route::get('/checkin/generate-qr', [CheckinController::class, 'generateQr'])->name('checkin.generate-qr');
    Route::post('/staff/checkin/manual', [App\Http\Controllers\CheckinController::class, 'manualCheckinByStaff'])->name('checkin.manual-staff');

    // Staff QR Scanning Routes
    Route::middleware(['role:User:Staff|User:Owner'])->group(function () {
        Route::get('/checkin/staff-scanner', [CheckinController::class, 'staffQrScanner'])->name('checkin.staff-scanner');
        Route::post('/checkin/staff-scan-qr', [CheckinController::class, 'staffScanQr'])->name('checkin.staff-scan-qr');
        Route::get('/checkin/staff-recent-checkins', [CheckinController::class, 'getRecentCheckins'])->name('checkin.staff-recent-checkins');
    });

    // Owner QR Scanning Routes
    Route::middleware(['role:User:Owner'])->group(function () {
        Route::get('/checkin/owner-scanner', [CheckinController::class, 'ownerQrScanner'])->name('checkin.owner-scanner');
        Route::get('/checkin/owner-recent-checkins', [CheckinController::class, 'getRecentCheckins'])->name('checkin.owner-recent-checkins');
        Route::post('/checkin/{checkin}/force-checkout', [CheckinController::class, 'forceCheckout'])->name('checkin.force-checkout');
    });

    Route::get('/history_membership', [HistoryMembershipController::class, 'index'])->name('history_membership')->middleware(['can:history_membership']);
    Route::get('/activation_order', [ActivationOrderController::class, 'index'])->name('activation_order')->middleware(['can:activation_order']);
    Route::post('/activation_order/{membershipId}/validate_payment', [ActivationOrderController::class, 'validatePayment'])->name('activation_order.validate_payment')->middleware(['can:activation_order']);
    Route::post('/activation_order/{membershipId}/activate_membership', [ActivationOrderController::class, 'activateMembership'])->name('activation_order.activate_membership')->middleware(['can:activation_order']);

    // New routes for membership extension and application
    Route::get('/activation_order/extension/create', [ActivationOrderController::class, 'createExtension'])->name('activation_order.extension.create')->middleware(['can:activation_order']);
    Route::post('/activation_order/extension', [ActivationOrderController::class, 'storeExtension'])->name('activation_order.extension.store')->middleware(['can:activation_order']);
    Route::get('/activation_order/application/create', [ActivationOrderController::class, 'createApplication'])->name('activation_order.application.create')->middleware(['can:activation_order']);
    Route::post('/activation_order/application', [ActivationOrderController::class, 'storeApplication'])->name('activation_order.application.store')->middleware(['can:activation_order']);
    Route::get('/activation_order/{id}/edit', [ActivationOrderController::class, 'edit'])->name('activation_order.edit')->middleware(['can:activation_order']);
    Route::put('/activation_order/{id}', [ActivationOrderController::class, 'update'])->name('activation_order.update')->middleware(['can:activation_order']);
    Route::delete('/activation_order/{id}', [ActivationOrderController::class, 'destroy'])->name('activation_order.destroy')->middleware(['can:activation_order']);
    Route::post('/activation_order/{id}/approve', [ActivationOrderController::class, 'approve'])->name('activation_order.approve')->middleware(['can:activation_order']);
    Route::post('/activation_order/{id}/reject', [ActivationOrderController::class, 'reject'])->name('activation_order.reject')->middleware(['can:activation_order']);
    Route::get('/product', [ProductController::class, 'index'])->name('product')->middleware(['can:product']);
    Route::get('/service', [ServiceController::class, 'index'])->name('service')->middleware(['can:service']);
    Route::get('/product_transaction', [ProductTransactionController::class, 'index'])->name('product_transaction')->middleware(['can:product_transaction']);
    Route::get('/member_transaction', [MemberTransactionController::class, 'index'])->name('member_transaction')->middleware(['can:member_transaction']);
    Route::get('/service_transaction', [ServiceTransactionController::class, 'index'])->name('service_transaction')->middleware(['can:service_transaction']);
    Route::get('/service_transaction/{serviceTransaction}', [ServiceTransactionController::class, 'show'])->name('service_transaction.show');
    Route::post('/service_transaction/{serviceTransaction}/cancel', [ServiceTransactionController::class, 'cancel'])->name('service_transaction.cancel');
    Route::post('/service_transaction/{serviceTransaction}/approve', [ServiceTransactionController::class, 'approve'])->name('service_transaction.approve');
    Route::post('/service_transaction/{serviceTransaction}/reject', [ServiceTransactionController::class, 'reject'])->name('service_transaction.reject');
    Route::get('/landing_page', [LandingPageController::class, 'index'])->name('landing_page.index')->middleware(['can:landing_page']);

    // Public landing page route
    Route::get('/public_landing_page', [LandingPageController::class, 'publicLandingPage'])->name('landing_page.public');

    Route::get('/landing_page/berita', [LandingPageController::class, 'index'])->name('landing_page.berita')->middleware(['can:landing_page']);
    Route::get('/landing_page/gallery', [LandingPageController::class, 'index'])->name('landing_page.gallery')->middleware(['can:landing_page']);

    // Berita routes
    Route::get('/landing_page/berita/create', [LandingPageController::class, 'createBerita'])->name('landing_page.berita.create')->middleware(['can:landing_page']);
    Route::post('/landing_page/berita', [LandingPageController::class, 'storeBerita'])->name('landing_page.berita.store')->middleware(['can:landing_page']);
    Route::get('/landing_page/berita/{id}/edit', [LandingPageController::class, 'editBerita'])->name('landing_page.berita.edit')->middleware(['can:landing_page']);
    Route::put('/landing_page/berita/{id}', [LandingPageController::class, 'updateBerita'])->name('landing_page.berita.update')->middleware(['can:landing_page']);
    Route::delete('/landing_page/berita/{id}', [LandingPageController::class, 'destroyBerita'])->name('landing_page.berita.destroy')->middleware(['can:landing_page']);

    // Gallery routes
    Route::get('/landing_page/gallery/create', [LandingPageController::class, 'createGallery'])->name('landing_page.gallery.create')->middleware(['can:landing_page']);
    Route::post('/landing_page/gallery', [LandingPageController::class, 'storeGallery'])->name('landing_page.gallery.store')->middleware(['can:landing_page']);
    Route::get('/landing_page/gallery/{id}/edit', [LandingPageController::class, 'editGallery'])->name('landing_page.gallery.edit')->middleware(['can:landing_page']);
    Route::put('/landing_page/gallery/{id}', [LandingPageController::class, 'updateGallery'])->name('landing_page.gallery.update')->middleware(['can:landing_page']);
    Route::delete('/landing_page/gallery/{id}', [LandingPageController::class, 'destroyGallery'])->name('landing_page.gallery.destroy')->middleware(['can:landing_page']);

    Route::get('/landing_page/promo', [LandingPageController::class, 'index'])->name('landing_page.promo')->middleware(['can:landing_page']);

    // --- BLOK YANG DIPERBAIKI ---
    // Rute Laporan Transaksi (Urutan Diperbaiki)
    Route::get('/transaction_report', [TransactionReportController::class, 'index'])->name('transaction_report')->middleware(['can:transaction_report']);
    Route::get('/transaction_report/create', [TransactionReportController::class, 'create'])->name('transaction_report.create')->middleware(['can:transaction_report']);
    Route::get('/transaction_report/export', [TransactionReportController::class, 'export'])->name('transaction_report.export')->middleware(['can:transaction_report']);
    Route::get('/transaction_report/dashboard', [TransactionReportController::class, 'dashboard'])->name('transaction_report.dashboard')->middleware(['can:transaction_report']);
    Route::post('/transaction_report', [TransactionReportController::class, 'store'])->name('transaction_report.store')->middleware(['can:transaction_report']);

    // Rute dengan parameter harus di bawah rute statis
    Route::get('/transaction_report/{transaction}', [TransactionReportController::class, 'show'])->name('transaction_report.show')->middleware(['can:transaction_report']);
    Route::get('/transaction_report/{transaction}/edit', [TransactionReportController::class, 'edit'])->name('transaction_report.edit')->middleware(['can:transaction_report']);
    Route::put('/transaction_report/{transaction}', [TransactionReportController::class, 'update'])->name('transaction_report.update')->middleware(['can:transaction_report']);
    Route::delete('/transaction_report/{transaction}', [TransactionReportController::class, 'destroy'])->name('transaction_report.destroy')->middleware(['can:transaction_report']);
    Route::post('/transaction_report/{transaction}/validate', [TransactionReportController::class, 'validateTransaction'])->name('transaction_report.validate')->middleware(['can:transaction_report']);
    Route::post('/transaction_report/{transaction}/cancel', [TransactionReportController::class, 'cancelTransaction'])->name('transaction_report.cancel')->middleware(['can:transaction_report']);
    // --- AKHIR BLOK PERBAIKAN ---

    Route::get('/membership_report', [MembershipReportController::class, 'index'])->name('membership_report')->middleware(['can:membership_report']);

    Route::get('/checkin_report', [CheckinReportController::class, 'index'])->name('checkin_report')->middleware(['can:checkin_report']);
    // Menambahkan route untuk export PDF dan Excel
    Route::get('/checkin/export/pdf', [CheckinReportController::class, 'exportPdf'])->name('checkin.export.pdf');
    Route::get('/checkin/export/excel', [CheckinReportController::class, 'exportExcel'])->name('checkin.export.excel');

    Route::get('/income_report', [IncomeReportController::class, 'index'])->name('income_report')->middleware(['can:income_report']);
    Route::get('/income_report/excel', [IncomeReportController::class, 'exportExcel'])->name('excel')->middleware(['can:income_report']);
    // ✅ PERBAIKAN: Tambahkan route untuk export PDF di sini
    Route::get('/income_report/pdf', [IncomeReportController::class, 'exportPdf'])->name('pdf')->middleware(['can:income_report']);

    Route::get('/configuration_payment', [ConfigurationPaymentController::class, 'index'])->name('configuration_payment')->middleware(['can:configuration_payment']);

    Route::get('/profile', [ProfileController::class, 'index'])->name('profile')->middleware(['can:profile']);
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update-post');


    Route::get('/logout', [AuthController::class, 'logout'])->name('logout.get');
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // ✅ Tambahkan ini:
    Route::post('/packet_membership', [PacketMembershipController::class, 'store'])
        ->name('packet_membership.store');

    Route::get('/membership_report', [MembershipReportController::class, 'index'])->name('membership_report')->middleware(['can:membership_report']);

    // PASTIKAN BARIS INI ADA DI BAWAHNYA
    Route::get('/membership_report/export', [MembershipReportController::class, 'export'])->name('laporan.membership.export')->middleware(['can:membership_report']);

    Route::get('/checkin_report', [CheckinReportController::class, 'index'])->name('checkin_report')->middleware(['can:checkin_report']);

    // --- Packet Membership Routes ---
    Route::get('/packet_membership', [PacketMembershipController::class, 'index'])->name('packet_membership')->middleware(['can:packet_membership']);
    Route::post('/packet_membership', [PacketMembershipController::class, 'store'])->name('packet_membership.store')->middleware(['can:packet_membership']);
    Route::get('/packet_membership/{id}', [PacketMembershipController::class, 'show'])->name('packet_membership.show')->middleware(['can:packet_membership']);
    Route::get('/packet_membership/{id}/edit', [PacketMembershipController::class, 'edit'])->name('packet_membership.edit')->middleware(['can:packet_membership']);
    Route::put('/packet_membership/{id}', [PacketMembershipController::class, 'update'])->name('packet_membership.update')->middleware(['can:packet_membership']);
    Route::delete('/packet_membership/{id}', [PacketMembershipController::class, 'destroy'])->name('packet_membership.destroy')->middleware(['can:packet_membership']);

    // --- Member Purchase Flow Routes (Sudah Benar) ---
    Route::get('/packet_membership/{id}/select', [PacketMembershipController::class, 'selectPacket'])->name('packet_membership.select')->middleware(['can:packet_membership']);
    Route::post('/packet_membership/{id}/purchase', [PacketMembershipController::class, 'purchasePacket'])->name('packet_membership.purchase')->middleware(['can:packet_membership']);
    Route::get('/membership/{id}/payment', [PacketMembershipController::class, 'paymentPage'])->name('packet_membership.payment')->middleware(['can:packet_membership']);
    Route::get('/membership/{id}/activate', [PacketMembershipController::class, 'activateMembership'])->name('packet_membership.activate')->middleware(['can:packet_membership']);
    Route::get('/membership/{id}/success', [PacketMembershipController::class, 'successPage'])->name('packet_membership.success')->middleware(['can:packet_membership']);

    // ---Tambah pengguna pada owner  ---
    Route::get('/pengguna/create', [PenggunaController::class, 'create'])->name('pengguna.create');
    Route::post('/pengguna/store', [PenggunaController::class, 'store'])->name('pengguna.store');
    Route::get('/user_membership', [PenggunaController::class, 'daftarMember'])->name('user_membership')->middleware(['can:user_membership']);

    Route::get('/pengguna/{pengguna}/edit', [PenggunaController::class, 'edit'])->name('pengguna.edit')->middleware(['can:pengguna']);
    Route::put('/pengguna/{pengguna}', [PenggunaController::class, 'update'])->name('pengguna.update')->middleware(['can:pengguna']);
    Route::delete('/pengguna/{pengguna}', [PenggunaController::class, 'destroy'])->name('pengguna.destroy')->middleware(['can:pengguna']);

    // --- configuratiin payemnt --
    Route::get('/konfigurasi-pembayaran', [ConfigurationPaymentController::class, 'index'])->name('configuration.payment.index');
    Route::post('/konfigurasi-pembayaran', [ConfigurationPaymentController::class, 'update'])->name('configuration.payment.update');

    // --- Transaksi & Riwayat ---
    // ✅ INI ADALAH ROUTE YANG MEMPERBAIKI ERROR ANDA
    Route::get('/transactions/history', [MemberTransactionController::class, 'index'])->name('transaction.history')->middleware(['can:member_transaction']);

    Route::middleware(['auth'])->prefix('products')->name('product.')->group(function () {
        Route::post('/{product}/approve', [ActivationOrderController::class, 'approveProduct'])->name('approve');
        Route::post('/{product}/reject', [ActivationOrderController::class, 'rejectProduct'])->name('reject');
        Route::get('/{product}/edit', [ActivationOrderController::class, 'editProduct'])->name('edit');
        Route::delete('/{product}/destroy', [ActivationOrderController::class, 'destroyProduct'])->name('destroy');
        // Pastikan juga rute untuk update ada jika diperlukan
        Route::put('/{product}/update', [ActivationOrderController::class, 'updateProduct'])->name('update');
    });

    // Grup route untuk aksi pada pengajuan Layanan
    Route::middleware(['auth'])->prefix('services')->name('service.')->group(function () {
        Route::post('/{service}/approve', [ActivationOrderController::class, 'approveService'])->name('approve');
        Route::post('/{service}/reject', [ActivationOrderController::class, 'rejectService'])->name('reject');
        Route::get('/{service}/edit', [ActivationOrderController::class, 'editService'])->name('edit');
        Route::put('/{service}/update', [ActivationOrderController::class, 'updateService'])->name('update');
        Route::delete('/{service}/destroy', [ActivationOrderController::class, 'destroyService'])->name('destroy');
    });

    // --- Trainer Routes ---
    // Mengelola jadwal latihan/kelas
    Route::prefix('trainer')->name('trainer.')->middleware(['role:User:Trainer'])->group(function () {
        Route::get('/dashboard', [TrainerDashboardController::class, 'index'])->name('dashboard');
        Route::resource('schedule', TrainerScheduleController::class);

        // Member relations
        Route::get('/members', [\App\Http\Controllers\Trainer\TrainerController::class, 'members'])->name('members.index');

        // Progress Latihan
        Route::get('/progress', [\App\Http\Controllers\Trainer\TrainerController::class, 'progressIndex'])->name('progress.index');
        Route::get('/progress/create', [\App\Http\Controllers\Trainer\TrainerController::class, 'createProgress'])->name('progress.create');
        Route::post('/progress', [\App\Http\Controllers\Trainer\TrainerController::class, 'storeProgress'])->name('progress.store');

        // Ketersediaan Waktu
        Route::get('/availability', [\App\Http\Controllers\Trainer\TrainerController::class, 'availability'])->name('availability.index');
        Route::post('/availability', [\App\Http\Controllers\Trainer\TrainerController::class, 'storeAvailability'])->name('availability.store');
        Route::delete('/availability/{id}', [\App\Http\Controllers\Trainer\TrainerController::class, 'destroyAvailability'])->name('availability.destroy');
    });

    // --- Member Schedule & Booking Routes ---
    // Melihat jadwal dan melakukan booking
    Route::prefix('member')->name('member.')->middleware(['role:User:Member'])->group(function () {
        Route::get('/schedule', [MemberScheduleController::class, 'index'])->name('schedule.index');
        Route::get('/booking', [MemberBookingController::class, 'index'])->name('booking.index');
        Route::post('/booking', [MemberBookingController::class, 'store'])->name('booking.store');
        Route::delete('/booking/{id}', [MemberBookingController::class, 'destroy'])->name('booking.destroy');
        Route::get('/progress', [\App\Http\Controllers\Member\ProgressController::class, 'index'])->name('progress.index');
    });

});
