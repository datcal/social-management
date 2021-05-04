<?php

use Illuminate\Support\Facades\Route;
use Atymic\Twitter\Facade\Twitter;
use App\Models\UsersFriends;
use App\Models\Friends;
use Illuminate\Support\Collection;
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
    if(Session::get('access_token')){
        $twitterUser = Session::get('access_token');
        /*
        [oauth_token] => 16968534-tYPQnTuIm6npUvvqOfBAyZs7F3TEQkiyuMMcp9R14
        [oauth_token_secret] => pjJ0nwxc6X6h4IombFQdSw0zd2NMMQNu3gkcThQ8zYjK7
        [user_id] => 16968534
        [screen_name] => datcal
         * */
       /* $getFollowers = Twitter::getFollowersIds();

        foreach ($getFollowers->ids as $getFollower){
            $userFriend = new UsersFriends;
            $userFriend->user = $twitterUser['screen_name'];
            $userFriend->type = 'follower';
            $userFriend->who = $getFollower;
            $userFriend->save();
        }

        $getFriendsIds = Twitter::getFriendsIds();

       foreach ($getFriendsIds->ids as $getFriendsId){
            $userFriend = new UsersFriends;
            $userFriend->user = $twitterUser['screen_name'];
            $userFriend->type = 'friend';
            $userFriend->who = $getFriendsId;
            $userFriend->save();
        }*/
        //dd($getFriendsIds);
      //  dd($getFollowers);

        $followings =  DB::select('SELECT friends.uname,friends.name,friends.`screen_name`, friends.`description`, friends.`profile_image_url`, friends.`url` FROM `users_friends`
                                inner join friends on friends.uname = `users_friends`.who
                                WHERE `users_friends`.`user` LIKE :user AND `users_friends`.`type` = :type',['user'=>$twitterUser['screen_name'],'type'=>'friend']);

        $followers =  DB::select('SELECT friends.uname,friends.name,friends.`screen_name`, friends.`description`, friends.`profile_image_url`, friends.`url` FROM `users_friends`
                                inner join friends on friends.uname = `users_friends`.who
                                WHERE `users_friends`.`user` LIKE :user AND `users_friends`.`type` = :type',['user'=>$twitterUser['screen_name'],'type'=>'follower']);


        return view('welcome',compact('followings','followers'));
    }else{

        return Redirect::to(route('twitter.login'));
    }




});

Route::get('/friend/{id}', function ($id) {
    echo $id;
    $data = Twitter::getUsersLookup(array('user_id'=>$id))[0];
    echo $data->name;
    dd($data);
});

Route::get('/get/{type}', function ($type) {

    if(Session::get('access_token')){
        $twitterUser = Session::get('access_token');
        $userFriend = UsersFriends::where('user',$twitterUser['screen_name'])->where('type',$type)->get();

            $userFriend = collect($userFriend);
            $chunks = $userFriend->chunk(100);
            foreach ($chunks->all() as $c){
                $userIds = array();
                foreach ($c as $value){
                    $userIds[] = $value->who;
                }
                $userList = implode(',',$userIds);
                $friendsData = Twitter::getUsersLookup(array('user_id'=>$userList));
                foreach ($friendsData as $friendData){
                    $friend = Friends::where('uname',$friendData->id)->get();
                    if(!count($friend)){
                        $friend = new Friends;
                        $friend->uname = $friendData->id;
                        $friend->name = $friendData->name;
                        $friend->screen_name = $friendData->screen_name;
                        $friend->description = $friendData->description;
                        $friend->profile_image_url = $friendData->profile_image_url;
                        $friend->url = $friendData->url;
                        $friend->save();
                    }
                }
            }
    }

    return Redirect::to('/')->with('notice', 'Congrats! You\'ve successfully signed in!');
});

Route::get('/post', function () {
   Twitter::postTweet(array('status'=>'test2'));
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

            $getFollowers = Twitter::getFollowersIds();
            $IDS = array();
            foreach ($getFollowers->ids as $getFollower){
                $IDS[] = $getFollower;
                $userFriend = UsersFriends::where('who',$getFollower)->where('user',$token['screen_name'])->where('type','follower')->get();

                if(!count($userFriend)){
                    $userFriend = new UsersFriends;
                    $userFriend->user = $token['screen_name'];
                    $userFriend->type = 'follower';
                    $userFriend->who = $getFollower;
                    $userFriend->save();
                }

            }


            $userFriend = collect($IDS);
            $chunks = $userFriend->chunk(100);
            foreach ($chunks->all() as $c){
                $userIds = array();
                foreach ($c as $value){
                    $userIds[] = $value;
                }
                $userList = implode(',',$userIds);
                $friendsData = Twitter::getUsersLookup(array('user_id'=>$userList));
                foreach ($friendsData as $friendData){
                    $friend = Friends::where('uname',$friendData->id)->get();
                    if(!count($friend)){
                        $friend = new Friends;
                        $friend->uname = $friendData->id;
                        $friend->name = $friendData->name;
                        $friend->screen_name = $friendData->screen_name;
                        $friend->description = $friendData->description;
                        $friend->profile_image_url = $friendData->profile_image_url;
                        $friend->url = $friendData->url;
                        $friend->save();
                    }
                }
            }


            $getFriendsIds = Twitter::getFriendsIds();
            $IDS = array();
            foreach ($getFriendsIds->ids as $getFriendsId){
                $IDS[] = $getFriendsId;
                $userFriend = UsersFriends::where('who',$getFriendsId)->where('user',$token['screen_name'])->where('type','friend')->get();
                if(!count($userFriend)) {
                    $userFriend = new UsersFriends;
                    $userFriend->user = $token['screen_name'];
                    $userFriend->type = 'friend';
                    $userFriend->who = $getFriendsId;
                    $userFriend->save();
                }
            }

            $userFriend = collect($IDS);
            $chunks = $userFriend->chunk(100);
            foreach ($chunks->all() as $c){
                $userIds = array();
                foreach ($c as $value){
                    $userIds[] = $value;
                }
                $userList = implode(',',$userIds);
                $friendsData = Twitter::getUsersLookup(array('user_id'=>$userList));
                foreach ($friendsData as $friendData){
                    $friend = Friends::where('uname',$friendData->id)->get();
                    if(!count($friend)){
                        $friend = new Friends;
                        $friend->uname = $friendData->id;
                        $friend->name = $friendData->name;
                        $friend->screen_name = $friendData->screen_name;
                        $friend->description = $friendData->description;
                        $friend->profile_image_url = $friendData->profile_image_url;
                        $friend->url = $friendData->url;
                        $friend->save();
                    }
                }
            }



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