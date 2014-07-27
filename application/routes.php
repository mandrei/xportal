<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Simply tell Laravel the HTTP verbs and URIs it should respond to. It is a
| breeze to setup your application using Laravel's RESTful routing and it
| is perfectly suited for building large applications and simple APIs.
|
| Let's respond to a simple GET request to http://example.com/hello:
|
|		Route::get('hello', function()
|		{
|			return 'Hello World!';
|		});
|
| You can even respond to more than one URI:
|
|		Route::post(array('hello', 'world'), function()
|		{
|			return 'Hello World!';
|		});
|
| It's easy to allow URI wildcards using (:num) or (:any):
|
|		Route::put('hello/(:any)', function($name)
|		{
|			return "Welcome, $name.";
|		});
|
*/





/*
 * Login
 */
Route::get('login','backend.authentication.login@index');

Route::post('login/safe', 'backend.authentication.login@index');

//Default route redirect
Route::get('/','backend.authentication.login@index');


/*
 *
 *  Reset password
 *
 */
Route::post('reset_password','backend.authentication.login@reset_password');

Route::get('reset_password/(:any)','backend.authentication.login@reset_password');


Route::get('logout', function(){

     Users_Auth::logout();

    return Redirect::to('login');

});//logout



/*--------------------------------------------------------------------------
 *
 *
 *
 *   Admin Routes
 *
 *   Must have safe prefix
 *
 --------------------------------------------------------------------------*/


/*
 * Change password
 */
Route::get('safe/change_password', 'backend.account@change_password');
Route::post('safe/change_password', 'backend.account@change_password');



/*
 * User Account
 */
Route::get('safe/account_user/edit',   'backend.account@user_edit_account');
Route::post('safe/account_user/edit',  'backend.account@user_edit_account');

Route::get('safe/account_client/edit','backend.account@client_edit_account');
Route::post('safe/account_client/edit','backend.account@client_edit_account');


/*
 * Users
 */
Route::get('safe/user/add', 'backend.users@add');
Route::post('safe/user/add', 'backend.users@add');

Route::get('safe/user/(:num)/edit','backend.users@edit');
Route::post('safe/user/(:num)/edit','backend.users@edit');

Route::get('safe/users', 'backend.users@all');
Route::get('safe/user/(:num)', 'backend.users@details');//details

Route::get('safe/user/(:num)/delete', 'backend.users@delete');


/*
*
*  Portal
*
*/
Route::get('safe/portal', 'backend.portal@index');

Route::get('safe/portal/get_folders_and_files', 'backend.portal@folders_and_files');
Route::post('safe/portal/upload_files', 'backend.portal@upload_files');
Route::post('safe/portal/delete_files', 'backend.portal@delete_files');


Route::post('safe/portal/add_file','backend.portal@add_file');

Route::post('safe/portal/delete_folder', 'backend.portal@delete_folder');
Route::post('safe/portal/add_folder','backend.portal@add_folder');

Route::get('safe/portal/download_folder','backend.portal@download_folder');
Route::get('safe/portal/download_files','backend.portal@download_files');

Route::post('safe/portal/search','backend.portal@search');
Route::post('safe/portal/get_folder_info','backend.portal@folder_info');

Route::get('safe/portal/folder/(:num)/details','backend.folder@details');

Route::get('safe/portal/(:num)/alert_new_files','backend.portal@notify_client');

Route::post('safe/portal/refresh_list','backend.portal@refresh_list');


/*
*
*  Clients
*
*/
Route::get('safe/client/add', 'backend.clients@add');
Route::post('safe/client/add', 'backend.clients@add');

Route::get('safe/client/(:num)/edit','backend.clients@edit');
Route::post('safe/client/(:num)/edit','backend.clients@edit');

Route::get('safe/clients', 'backend.clients@index');
Route::get('safe/client/(:num)', 'backend.clients@details');//details

Route::get('safe/client/(:num)/delete', 'backend.clients@delete');


Route::get('safe/client/(:num)/view', 'backend.clients@unviewed');


Route::get('safe/clients/export','backend.clients_export@export');


/*
*
*  Settings
*
*/
Route::get('safe/settings', 'backend.settings@index');
Route::post('safe/settings', 'backend.settings@index');



/*
|--------------------------------------------------------------------------
| Application 404 & 500 Error Handlers
|--------------------------------------------------------------------------
|
| To centralize and simplify 404 handling, Laravel uses an awesome event
| system to retrieve the response. Feel free to modify this function to
| your tastes and the needs of your application.
|
| Similarly, we use an event to handle the display of 500 level errors
| within the application. These errors are fired when there is an
| uncaught exception thrown in the application.
|
*/

Event::listen('404', function()
{
	return Response::error('404');
});

Event::listen('500', function()
{
	return Response::error('500');
});





/*
|--------------------------------------------------------------------------
| Route Filters
|--------------------------------------------------------------------------
|
| Filters provide a convenient method for attaching functionality to your
| routes. The built-in before and after filters are called before and
| after every request to your application, and you may even create
| other filters that can be attached to individual routes.
|
*/





/**##########################################################################
 *
 * Check routes patterns for appropriate permission based on user type
 *
############################################################################*/
Route::filter('pattern: safe/*', array('name' => 'common_routes', function()
{
   
    if( Users_Auth::is_logged() === false )
    {

        return Redirect::to('login');

    }//if user is not logged

}));//common routes




/**##########################################################################
 *
 * Check permissions for portal ( module id = 2 )
 *
############################################################################*/
Route::filter('pattern: safe/portal*', array( 'name' => 'common_routes', function()
{
    if( !Users_Auth::has_access( 2 ) ) return Redirect::to('logout');

}));//portal routes*/

/**##########################################################################
 *
 * Check permissions for users ( module id = 6  )
 *
############################################################################*/
Route::filter('pattern: safe/user*', array( 'name' => 'common_routes', function()
{
    if( !Users_Auth::has_access( 6 ) ) return Redirect::to('logout');

}));//users routes*/

/**##########################################################################
 *
 * Check permissions for clients ( module id = 7 )
 *
############################################################################*/
Route::filter('pattern: safe/clients*', array( 'name' => 'common_routes', function()
{
    if( !Users_Auth::has_access( 7 ) ) return Redirect::to('logout');

}));//users clients*/


/**##########################################################################
 *
 * Check permissions for settings ( module id = 8 )
 *
############################################################################*/
Route::filter('pattern: safe/settings*', array( 'name' => 'common_routes', function()
{
    if( !Users_Auth::has_access( 8 ) ) return Redirect::to('logout');

}));//settings routes*/



Route::filter('csrf', function()
{
    if (Request::forged()) return Response::error('500');
});
