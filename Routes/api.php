<?php

/* Roles */
Route::get("roles", "RoleController@index")
    ->middleware("TechlifyAccessControl:role_read");
Route::post("roles", "RoleController@store")
    ->middleware("TechlifyAccessControl:role_create");
Route::put("roles/{id}", "RoleController@update")
    ->middleware("TechlifyAccessControl:role_update");
Route::delete("roles/{id}", "RoleController@destroy")
    ->middleware("TechlifyAccessControl:role_delete");
Route::get("roles/{id}", "RoleController@show")
    ->middleware("TechlifyAccessControl:role_read");
Route::patch("roles/{role}/permissions/{permission}/add", "RoleController@addPermission")
    ->middleware("TechlifyAccessControl:role_permission_add");
Route::patch("roles/{role}/permissions/{permission}/remove", "RoleController@removePermission")
    ->middleware("TechlifyAccessControl:role_permission_remove");

/* Permissions */
Route::resource("permissions", "\Modules\LaravelCore\Http\Controllers\PermissionController");

/* Users */
Route::get("users", "UserController@index")
    ->middleware("TechlifyAccessControl:user_read");
Route::post("users", "UserController@store")
    ->middleware("TechlifyAccessControl:user_create");
Route::put("users/{id}", "UserController@update")
    ->middleware("TechlifyAccessControl:user_update");
Route::delete("users/{id}", "UserController@destroy")
    ->middleware("TechlifyAccessControl:user_delete");
Route::get("users/{id}", "UserController@show")
    ->middleware("TechlifyAccessControl:user_read");
Route::patch("users/{id}/enable", "\Modules\LaravelCore\Http\Controllers\UserController@enable")
    ->middleware("TechlifyAccessControl:user_enable");
Route::patch("users/{id}/disable", "\Modules\LaravelCore\Http\Controllers\UserController@disable")
    ->middleware("TechlifyAccessControl:user_disable");

Route::put("users/profile/update", "UserController@updateCurrentUserProfile");

Route::post('/user/login', "\Modules\LaravelCore\Http\Controllers\SessionController@login");
Route::post('/user/logout', "\Modules\LaravelCore\Http\Controllers\SessionController@destroy");
Route::get('/user/current', "\Modules\LaravelCore\Http\Controllers\UserController@currentUser");
Route::patch("user/current/update-password", "\Modules\LaravelCore\Http\Controllers\UserController@user_password_change_own");

/* Company Sign Up */
Route::post("company-sign-up", "UserController@companySignUp");

/* Forgot Password */
Route::post("forgot-password", "UserController@forgotPassword");

/* Client */
Route::get("clients", "ClientController@index")
    ->middleware("TechlifyAccessControl:client_read");
Route::post("clients", "ClientController@store")
    ->middleware("TechlifyAccessControl:client_create");
Route::put("clients/{id}", "ClientController@update")
    ->middleware("TechlifyAccessControl:client_update");
Route::delete("clients/{id}", "ClientController@destroy")
    ->middleware("TechlifyAccessControl:client_delete");
Route::get("clients/{id}", "ClientController@show")
    ->middleware("TechlifyAccessControl:client_read");
Route::get("clients-work-tasks-summary", "ClientController@clientWorkTaskSummary")
    ->middleware("TechlifyAccessControl:client_read");

/* Subscription Status */
Route::get("client-subscription-statuses", "ClientSubscriptionStatusController@index");

/* Client Subscription */
Route::get("client-subscriptions", "ClientSubscriptionController@index")
    ->middleware("TechlifyAccessControl:client_subscription_read");
Route::post("client-subscriptions", "ClientSubscriptionController@store")
    ->middleware("TechlifyAccessControl:client_subscription_create");
Route::put("client-subscriptions/{id}", "ClientSubscriptionController@update")
    ->middleware("TechlifyAccessControl:client_subscription_update");
Route::delete("client-subscriptions/{id}", "ClientSubscriptionController@destroy")
    ->middleware("TechlifyAccessControl:client_subscription_delete");
Route::get("client-subscriptions/{id}", "ClientSubscriptionController@show")
    ->middleware("TechlifyAccessControl:client_subscription_read");
Route::post("client-subscriptions/trial", "ClientSubscriptionController@trialSubscription");


/* Client Payments */
Route::get("client-payments", "ClientPaymentController@index")
    ->middleware("TechlifyAccessControl:client_payment_read");
Route::post("client-payments", "ClientPaymentController@store")
    ->middleware("TechlifyAccessControl:client_payment_create");
Route::put("client-payments/{id}", "ClientPaymentController@update")
    ->middleware("TechlifyAccessControl:client_payment_update");
Route::delete("client-payments/{id}", "ClientPaymentController@destroy")
    ->middleware("TechlifyAccessControl:client_payment_delete");
Route::patch("client-payments/{id}/set-paid", "ClientPaymentController@setPaid")
    ->middleware("TechlifyAccessControl:client_payment_set_paid");

    Route::get("modules", "ModuleController@index")
    ->middleware("TechlifyAccessControl:module_read");
Route::post("modules", "ModuleController@store")
    ->middleware("TechlifyAccessControl:module_create");
Route::put("modules/{id}", "ModuleController@update")
    ->middleware("TechlifyAccessControl:module_update");
Route::delete("modules/{id}", "ModuleController@destroy")
    ->middleware("TechlifyAccessControl:module_delete");
Route::get("modules/{id}", "ModuleController@show")
    ->middleware("TechlifyAccessControl:module_read");
Route::patch("modules/{id}/enable", "ModuleController@enable")
    ->middleware("TechlifyAccessControl:module_update");
Route::patch("modules/{id}/disable", "ModuleController@disable")
    ->middleware("TechlifyAccessControl:module_update");

Route::get("module-packages", "ModulePackageController@index")
    ->middleware("TechlifyAccessControl:module_package_read");

/* Module Users */
Route::get("module-users", "ModuleUserController@index")
    ->middleware("TechlifyAccessControl:module_user_read");
Route::post("module-users", "ModuleUserController@store")
    ->middleware("TechlifyAccessControl:module_user_add");
Route::post("module-users/invite", "ModuleUserController@inviteuser")
    ->middleware("TechlifyAccessControl:module_user_add");
Route::put("module-users/{id}", "ModuleUserController@destroy")
    ->middleware("TechlifyAccessControl:module_user_delete");

Route::get("current-client-modules", "ModuleController@getCurrentClientModules");
