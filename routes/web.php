<?php
  use Illuminate\Support\Facades\Route;
  use App\Http\Controllers\dashboard\Analytics;
  use App\Http\Controllers\layouts\WithoutMenu;
  use App\Http\Controllers\layouts\WithoutNavbar;
  use App\Http\Controllers\layouts\Fluid;
  use App\Http\Controllers\layouts\Container;
  use App\Http\Controllers\layouts\Blank;
  use App\Http\Controllers\pages\AccountSettingsAccount;
  use App\Http\Controllers\pages\AccountSettingsNotifications;
  use App\Http\Controllers\pages\AccountSettingsConnections;
  use App\Http\Controllers\pages\MiscError;
  use App\Http\Controllers\pages\MiscUnderMaintenance;
  use App\Http\Controllers\authentications\LoginBasic;
  use App\Http\Controllers\authentications\RegisterBasic;
  use App\Http\Controllers\authentications\ForgotPasswordBasic;
  use App\Http\Controllers\cards\CardBasic;
  use App\Http\Controllers\user_interface\Accordion;
  use App\Http\Controllers\user_interface\Alerts;
  use App\Http\Controllers\user_interface\Badges;
  use App\Http\Controllers\user_interface\Buttons;
  use App\Http\Controllers\user_interface\Carousel;
  use App\Http\Controllers\user_interface\Collapse;
  use App\Http\Controllers\user_interface\Dropdowns;
  use App\Http\Controllers\user_interface\Footer;
  use App\Http\Controllers\user_interface\ListGroups;
  use App\Http\Controllers\user_interface\Modals;
  use App\Http\Controllers\user_interface\Navbar;
  use App\Http\Controllers\user_interface\Offcanvas;
  use App\Http\Controllers\user_interface\PaginationBreadcrumbs;
  use App\Http\Controllers\user_interface\Progress;
  use App\Http\Controllers\user_interface\Spinners;
  use App\Http\Controllers\user_interface\TabsPills;
  use App\Http\Controllers\user_interface\Toasts;
  use App\Http\Controllers\user_interface\TooltipsPopovers;
  use App\Http\Controllers\user_interface\Typography;
  use App\Http\Controllers\extended_ui\PerfectScrollbar;
  use App\Http\Controllers\extended_ui\TextDivider;
  use App\Http\Controllers\icons\Boxicons;
  use App\Http\Controllers\form_elements\BasicInput;
  use App\Http\Controllers\form_elements\InputGroups;
  use App\Http\Controllers\form_layouts\VerticalForm;
  use App\Http\Controllers\form_layouts\HorizontalForm;
  use App\Http\Controllers\tables\Basic as TablesBasic;
  use App\Http\Controllers\Auth\GoogleController;
  use App\Http\Controllers\Auth\EmailController;
  use App\Http\Controllers\OrderController;
  use App\Http\Controllers\Auth\ItemController;
  use App\Http\Controllers\Auth\AccountController;
  use App\Http\Controllers\Auth\MilkteaController;
  use App\Http\Controllers\Auth\BorrowsController;
  use App\Http\Controllers\UserPages\UserController;
  use App\Http\Controllers\Auth\AdminPages;
  use Illuminate\Support\Facades\Auth;
  use Illuminate\Support\Facades\Password;
  use App\Http\Controllers\Auth\QrController;
  
  
Route::get('/qr/return/{token}', [QrController::class, 'handleQrReturn'])->name('qr.return');


Route::get('/qr-login/{token}', function ($token) {
  $user = User::where('qr_token', $token)->first();
  if (!$user) {
      return redirect()->route('login')->with('error', 'Invalid QR code.');
  }
  Auth::login($user);
  return redirect()->route('borrow.returnItem'); // Redirect to return item page after login
})->name('qr.login');

Route::get('/show-qr-code', [QrController::class, 'showQrCode'])->name('qr.show');



  Route::get('auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('password.request');
  Route::post('auth/forgot-password-basic', [ForgotPasswordBasic::class, 'sendResetLinkEmail'])->name('password.email');
  Route::get('auth/reset-password/{token}', [ForgotPasswordBasic::class, 'showResetForm'])->name('password.reset');
  Route::post('auth/reset-password', [ForgotPasswordBasic::class, 'reset'])->name('password.update');


  // Routes that should be protected from the login page
Route::middleware(['RedirectIfAuthenticated'])->group(function () {
    Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
    // Other routes that should be protected
});

  Route::get('/recent-borrowed-remotes', [UserController::class, 'recentBorrowedRemotes'])->name('recent.borrowed.remotes');
  Route::get('/user-returned', [UserController::class, 'history'])->name('user.returned');


Route::post('/google', [GoogleController::class, 'GoogleAuth']);

Route::get('/', [Analytics::class, 'index'])->name('dashboard-analytics');
Route::post('/login', [LoginBasic::class, 'store'])->name('login');
Route::get('/login', function () {
    return redirect()->route('auth-login-basic');
});
  Route::get('/register', [RegisterBasic::class, 'index'])->name('register');
  Route::post('/register', [RegisterBasic::class, 'store'])->name('register.store');
  Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');
  Route::post('auth/login-basic', [LoginBasic::class, 'store'])->name('store');

  

  // layout
  Route::get('/layouts/without-menu', [WithoutMenu::class, 'index'])->name('layouts-without-menu');
  Route::get('/layouts/without-navbar', [WithoutNavbar::class, 'index'])->name('layouts-without-navbar');
  Route::get('/layouts/fluid', [Fluid::class, 'index'])->name('layouts-fluid');
  Route::get('/layouts/container', [Container::class, 'index'])->name('layouts-container');
  Route::get('/layouts/blank', [Blank::class, 'index'])->name('layouts-blank');

  // pages
  Route::get('/pages/account-settings-account', [AccountSettingsAccount::class, 'index'])->name(
    'pages-account-settings-account'
  );
  Route::get('/pages/account-settings-notifications', [AccountSettingsNotifications::class, 'index'])->name(
    'pages-account-settings-notifications'
  );
  Route::get('/pages/account-settings-connections', [AccountSettingsConnections::class, 'index'])->name(
    'pages-account-settings-connections'
  );
  Route::get('/pages/misc-error', [MiscError::class, 'index'])->name('pages-misc-error');
  Route::get('/pages/misc-under-maintenance', [MiscUnderMaintenance::class, 'index'])->name(
    'pages-misc-under-maintenance'
  );
  // authentication
  Route::get('/auth/login-basic', [LoginBasic::class, 'index'])->name('auth-login-basic');
  Route::get('/auth/register-basic', [RegisterBasic::class, 'index'])->name('auth-register-basic');
  Route::get('/auth/forgot-password-basic', [ForgotPasswordBasic::class, 'index'])->name('auth-reset-password-basic');

  // cards
  Route::get('/cards/basic', [CardBasic::class, 'index'])->name('cards-basic');

  // extended ui
  Route::get('/extended/ui-perfect-scrollbar', [PerfectScrollbar::class, 'index'])->name(
    'extended-ui-perfect-scrollbar'
  );
  Route::get('/extended/ui-text-divider', [TextDivider::class, 'index'])->name('extended-ui-text-divider');

  // icons
  Route::get('/icons/boxicons', [Boxicons::class, 'index'])->name('icons-boxicons');

  // form elements
  Route::get('/forms/basic-inputs', [BasicInput::class, 'index'])->name('forms-basic-inputs');
  Route::get('/forms/input-groups', [InputGroups::class, 'index'])->name('forms-input-groups');

  // form layouts
  Route::get('/form/layouts-vertical', [VerticalForm::class, 'index'])->name('form-layouts-vertical');
  Route::get('/form/layouts-horizontal', [HorizontalForm::class, 'index'])->name('form-layouts-horizontal');

  // tables
  Route::get('/tables/basic', [TablesBasic::class, 'index'])->name('tables-basic');

  //logout
  Route::post('/logout', [LoginBasic::class, 'logout'])->name('logout');

  Route::middleware(['CheckRole'])->group(function () {
    Route::get('/dashboard', [Analytics::class, 'index'])->name('dashboard-analytics');
});
Route::middleware(['auth'])->group(function () {
  Route::get('/user_pages/dashboard_user', [UserController::class, 'landingGuest'])->name('guest');
  // Other routes that require authentication
});



Route::middleware(['CheckRole'])->group(function () {
  Route::group(['checkrole' => ['prevent-back-history']], function() {
    Route::get('/', function() {
        return view('content.authentications.auth-login-basic');
    });
});


Route::get('/key', [ItemController::class, 'index'])->name('key.view');

// Route for creating a new key
Route::post('/key', [ItemController::class, 'add'])->name('key.add');

/// Update a key by ID
Route::post('/key/update', [ItemController::class, 'update'])->name('key.update');

// Delete a key by ID
Route::post('/key/delete', [ItemController::class, 'delete'])->name('key.delete');
Route::delete('/borrows/delete-all-returned', [BorrowsController::class, 'deleteAllReturned'])->name('borrows.deleteAllReturned');

Route::post('/borrow/return', [ItemController::class, 'return'])->name('borrow.return');
  
Route::get('/users', [AccountController::class, 'index'])->name('user.view');
Route::post('/users/add', [AccountController::class, 'store'])->name('user.store');
Route::post('/users/delete', [AccountController::class, 'delete'])->name('user.delete');
Route::post('/user/approve', [AccountController::class, 'approve'])->name('user.approve');
Route::post('/users/update-role', [AccountController::class, 'updateRole'])->name('user.updateRole');

Route::get('/borrow/manage', [BorrowsController::class, 'index'])->name('borrows.view');
Route::post('/borrow/update', [BorrowsController::class, 'update'])->name('borrow.update');

Route::get('/borrow/history', [BorrowsController::class, 'history'])->name('history.view');
Route::delete('/borrows/delete-by-month', [BorrowsController::class, 'deleteByMonth'])->name('borrows.deleteByMonth');

Route::get('/key/products',[AdminPages::class, 'index'])->name('view');
Route::get('/key/products',[AdminPages::class, 'requestApproval'])->name('view');



 });

// Route::post('/user_pages/borrow', [UserController::class, 'borrow'])->name('borrow.borrow');
Route::get('/user_pages/dashboard_user',[UserController::class, 'landingGuest'])->name('guest');
Route::get('/recent-borrowed-keys', [UserController::class, 'recentBorrowedKeys'])->name('recent.borrowed.keys');
// Route::post('/borrow/return', [UserController::class, 'return'])->name('borrow.return');
Route::get('/keys/search', [UserController::class, 'search'])->name('keys.search');
