<?php

use Illuminate\Support\Facades\Route;
use Atymic\Twitter\Facade\Twitter;
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
    return view('welcome');
});




Route::get('twitter/login', ['as' => 'twitter.login', static function () {
    $token = Twitter::getRequestToken(route('twitter.callback'));

    if (isset($token['oauth_token_secret'])) {
        $url = Twitter::getAuthenticateUrl($token['oauth_token']);

        Session::put('oauth_state', 'start');
        Session::put('oauth_request_token', $token['oauth_token']);
        Session::put('oauth_request_token_secret', $token['oauth_token_secret']);

        return Redirect::to($url);
    }

    return Redirect::route('twitter.error');
}]);

Route::get('callback', ['as' => 'twitter.callback', static function () {
    // You should set this route on your Twitter Application settings as the callback
    // https://apps.twitter.com/app/YOUR-APP-ID/settings
    if (Session::has('oauth_request_token')) {
        $twitter = Twitter::usingCredentials(session('oauth_request_token'), session('oauth_request_token_secret'));
        $token = $twitter->getAccessToken(request('oauth_verifier'));

        if (!isset($token['oauth_token_secret'])) {
            return Redirect::route('twitter.error')->with('flash_error', 'We could not log you in on Twitter.');
        }

        // use new tokens
        $twitter = Twitter::usingCredentials($token['oauth_token'], $token['oauth_token_secret']);
        $credentials = $twitter->getCredentials();

        if (is_object($credentials) && !isset($credentials->error)) {
            // $credentials contains the Twitter user object with all the info about the user.
            // Add here your own user logic, store profiles, create new users on your tables...you name it!
            // Typically you'll want to store at least, user id, name and access tokens
            // if you want to be able to call the API on behalf of your users.

            // This is also the moment to log in your users if you're using Laravel's Auth class
            // Auth::login($user) should do the trick.

            Session::put('access_token', $token);

            return Redirect::to('/')->with('notice', 'Congrats! You\'ve successfully signed in!');
        }
    }

    return Redirect::route('twitter.error')
            ->with('error', 'Crab! Something went wrong while signing you up!');
}]);

Route::get('twitter/error', ['as' => 'twitter.error', function () {
    // Something went wrong, add your own error handling here
}]);

Route::get('twitter/logout', ['as' => 'twitter.logout', function () {
    Session::forget('access_token');

    return Redirect::to('/')->with('notice', 'You\'ve successfully logged out!');
}]);