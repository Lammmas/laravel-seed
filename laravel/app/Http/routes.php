<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It's a breeze. Simply tell Laravel the URIs it should respond to
| and give it the controller to call when that URI is requested.
|
*/

Route::get('/', 'WelcomeController@index');

Route::get('home', 'HomeController@index');

Route::get('/install/{key?}', [
	'as' => 'install', function ($key = null) {
		if ($key == env('APP_KEY', md5(rand(0, 100)))) {
			Log::info('Install Manager : Begin of installation');

			//Get name of migrations tables and local path to directory, or use default value
			$Migrations = Config::get('database.migrations', 'migrations');

			//If we had already installed Migrations, than just import demo data to DB
			if (Schema::hasTable($Migrations)) {
				Log::info('Install Manager : We have migrations, so just do DB seeding.');
				Artisan::call('db:seed', ['--force' => true]);
				Log::info('Install Manager = All Done!');
			} else {
				Log::info('Install Manager : running migrate:install');
				Artisan::call('migrate:install');
				Log::info('Install Manager = Done!');
				Log::info('Install Manager : Start of migrating');
				Artisan::call('migrate', ['--force' => true]);
				Log::info('Install Manager = Done!');
				Log::info('Install Manager : Start of DB seeding');
				Artisan::call('db:seed', ['--force' => true]);
				Log::info('Install Manager = All Done!');
			}

			return Response::make("All installed!", 200);
		} else {
			App::abort(404);
		}
	}
]);

Route::controllers(
	[
		'auth'     => 'Auth\AuthController',
		'password' => 'Auth\PasswordController',
	]
);
