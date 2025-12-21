<?php
session_start();
require_once __DIR__.'/../core/Router.php';

$router = new Router();

// Auth & dashboard
$router->add('/register', 'AuthController@registerAction', 'POST');

$router->add('/login', 'AuthController@loginForm', 'GET');
$router->add('/login', 'AuthController@loginAction', 'POST');

$router->add('/activate', 'AuthController@activate', 'GET');

$router->add('/dashboard', 'DashboardController@dashboard', 'GET');
$router->add('/logout', 'AuthController@logout', 'GET');

$router->add('/edit-profile', 'AuthController@editProfile', 'PUT');

// API AQI
$router->add('/air-condition', 'ApiService@getAirQualityOpenWeather', 'GET');
$router->add('/city-air-condition', 'ApiService@getAirQualityByCity', 'GET');
$router->add('/avg-aqi', 'ApiService@getAverageAQI', 'GET');
$router->add('/all-air-quality', 'ApiService@getAirQualityAllLocation', 'GET');
$router->add('/notif-air-quality', 'ApiService@sendNotifAir', 'GET');

// ================= User Management =================
$router->add('/users', 'UserManagementController@getAllUsers', 'GET');
$router->add('/users/(\d+)', 'UserManagementController@getUserDetail', 'GET');
$router->add('/users', 'UserManagementController@createUser', 'POST');
$router->add('/users/(\d+)', 'UserManagementController@updateUser', 'PUT');
$router->add('/users/(\d+)', 'UserManagementController@deleteUser', 'DELETE');

// ================= Location Management =================
$router->add('/location', 'LocationController@getAllLocation', 'GET');
$router->add('/location/(\d+)', 'LocationController@getDetailLocation', 'GET');
$router->add('/location', 'LocationController@createLocation', 'POST');
$router->add('/location/(\d+)', 'LocationController@updateLocation', 'PUT');
$router->add('/location/(\d+)', 'LocationController@deleteLocation', 'DELETE');
$router->add('/favorite-location', 'LocationController@createLocationFavorite', 'POST');
$router->add('/favorite-location/(\d+)', 'LocationController@deleteLocationFavorite', 'DELETE');

// ================= Motivasi Management =================
$router->add('/motivasi', 'MotivasiController@getAllMotivasi', 'GET');
$router->add('/motivasi/(\d+)', 'MotivasiController@getDetailMotivasi', 'GET');
$router->add('/motivasi', 'MotivasiController@createMotivasi', 'POST');
$router->add('/motivasi/(\d+)', 'MotivasiController@updateMotivasi', 'PUT');
$router->add('/motivasi/(\d+)', 'MotivasiController@deleteMotivasi', 'DELETE');

$router->dispatch();
