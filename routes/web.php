<?php

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

Auth::routes();
Route::get('logout', 'Auth\LoginController@logout');
Route::get('/auth/success', [
    'as'   => 'auth.success',
    'uses' => 'Auth\RegisterController@success'
]);

Route::get('/redirect', 'SocialAuthController@redirect');
Route::get('/callback', 'SocialAuthController@callback');
Route::view('/impressum', 'legal.impressum')->name("legal.impressum");
Route::view('/datenschutz', 'legal.datenschutz');

Route::get('/order', 'OrderController@index');
Route::get('/order/create/{package}', 'OrderController@create');
Route::post('/order/{package}', 'OrderController@store');

Route::group(['middleware' => ['auth', 'EnsureClientAssigned', 'web', 'SurveyHandler']], function () {

    Route::redirect('/', '/service');
    Route::get('/home', 'HomeController@index')->name('home');
    Route::get('/mailtest', 'HomeController@mailtest');
    Route::get('/pdf','HomeController@generatePDF');
    Route::match(['get', 'post'], '/sendServicePDF', 'HomeController@sendServicePDF');
    Route::get('/userguide', 'HomeController@getUserGuide');
    Route::prefix('superadmin')->group(function () {
        Route::get('user', 'UserController@index')->name('superadmin.user');
    });

    Route::resource('qualification', 'QualificationController');

    Route::resource('user', 'UserController');
    Route::match(['get', 'post'], 'user/approve/{id}', 'UserController@approve_user');

    Route::resource('client', 'ClientController');
    Route::get('/clientapply', 'ClientController@apply')->name('clientapply');
    Route::any('/client/{clientid}/apply', 'ClientController@applyrequest')->name('clientapplyrequest');
    Route::any('/client/{clientid}/apply/revert', 'ClientController@applyrevert')->name('clientapplyrevert');
    Route::any('/client/{clientid}/removeuser/{userid}', 'ClientController@removeuser')->name('clientremoveuser');
    Route::any('/client/module', 'ClientController@module')->name('clientmodule');
    Route::get('/changeclient/{client}', 'UserController@setcurrentclient')->name('changeclient');

    Route::resource('service', 'ServiceController');
    Route::get('service/{service}/delete', 'ServiceController@delete')->name('service.delete');

    Route::resource('training', 'TrainingController');
    Route::get('training/{training}/delete', 'TrainingController@destroy')->name('training.delete');
    Route::any('/training/training_user/{training_userid}/delete/', 'TrainingController@delete_training_user')->name('delete_training_user');

    Route::resource('calendar', 'CalendarController');
    Route::get('calendar/{calendar}/delete', 'CalendarController@destroy')->name('calendar.delete');
    
    Route::resource('news', 'NewsController');
    Route::get('news/{news}/delete', 'NewsController@delete')->name('news.delete');

    Route::resource('holiday', 'HolidayController');
    Route::get('holiday/storeservice/{service}', 'HolidayController@storeService')->name('holiday.storeservice');
    Route::get('holiday/storetraining/{training}', 'HolidayController@storeTraining')->name('holiday.storetraining');

    Route::post('qualification_user/create', 'QualificationController@createQualification_User');
    Route::match(['get', 'post'], 'qualification_user/delete/{user_id}/{qualification_id}', 'QualificationController@deleteQualification_User');

    Route::post('client_user/admin', 'ClientController@adminClient_User');
    Route::post('client_user/trainingeditor', 'ClientController@trainingeditorClient_User');

    Route::match(['get', 'post'], 'position/{id}/subscribe', 'PositionController@subscribe');
    Route::match(['get', 'post'], 'position/{positionid}/subscribe_user/{userid}', 'PositionController@subscribe_user');
    Route::match(['get', 'post'], 'position/{id}/unsubscribe', 'PositionController@unsubscribe');
    Route::match(['get', 'post'], 'position/{positionid}/unsubscribe_user/{userid}', 'PositionController@unsubscribe_user');
    Route::match(['get', 'post'], 'position/{id}/authorize', 'PositionController@authorizePos');
    Route::match(['get', 'post'], 'position/{id}/deauthorize', 'PositionController@deauthorizePos');
    Route::match(['get', 'post'], 'position/list_notAuthorized', 'PositionController@index_notAuthorized')->name('position.list_notAuthorized');
    Route::match(['get', 'post'], 'position/{id}/position_user', 'PositionController@position_user');

    Route::match(['get', 'post'], 'statistic', 'StatisticController@index')->name('statistic');

    Route::resource('survey', 'SurveyController')->withoutMiddleware(['SurveyHandler']);
    Route::post('survey/vote/{surveyid}', 'SurveyController@vote')->name('survey.vote')->withoutMiddleware(['SurveyHandler']);
    Route::get('survey/postpone/{surveyid}', 'SurveyController@postpone')->name('survey.postpone')->withoutMiddleware(['SurveyHandler']);

    Route::resource('tag', 'TagController');
});
