<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Laravel</title>

        <!-- Fonts -->
        <link href="https://fonts.googleapis.com/css2?family=Nunito:wght@400;600;700&display=swap" rel="stylesheet">

        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/css/bootstrap.min.css" integrity="sha384-B0vP5xmATw1+K9KRQjQERJvTumQW0nPEzvF6L/Z6nronJ3oUOFUFpCjEUQouq2+l" crossorigin="anonymous">

        <style>
            body {
                color: #555;
                background-color: #5b9eed;
                font-family: sans-serif;
            }
            .list-wrapper {
                max-width: 400px;
                margin: 50px auto;
            }
            .list {
                background: #fff;
                border-radius: 2px;
                list-style: none;
                padding: 10px 20px;
            }
            .list-item {
                display: flex;
                margin: 10px;
                padding-bottom: 5px;
                padding-top: 5px;
                border-bottom: 1px solid rgba(0, 0, 0, 0.1);
            }
            .list-item:last-child {
                border-bottom: none;
            }
            .list-item-image {
                border-radius: 50%;
                width: 64px;
            }
            .list-item-content {
                margin-left: 20px;
            }
            .list-item-content h4, .list-item-content p {
                margin: 0;
            }
            .list-item-content h4 {
                margin-top: 10px;
                font-size: 18px;
            }
            .list-item-content p {
                margin-top: 5px;
                color: #aaa;
            }



        </style>
    </head>
    <body>
    <header>
        <nav class="navbar navbar-expand-lg navbar-light bg-light">
            <a class="navbar-brand" href="#">Social Management</a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav mr-auto">
                    <li class="nav-item active">
                        <a class="nav-link" href="/">Home <span class="sr-only">(current)</span></a>
                    </li>
                   <!-- <li class="nav-item">
                        <a class="nav-link" href="/twitter/login">Twitter Login</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/get/follower">Get Followers</a>
                    </li>

                    <li class="nav-item">
                        <a class="nav-link" href="/get/friend">Get Followings</a>
                    </li>-->

                    <li class="nav-item">
                        <a class="nav-link" href="{{route('twitter.logout')}}">Logout</a>
                    </li>

                </ul>

            </div>
        </nav>
    </header>


    <div class="container">
        <div class="row">
            <div class="col-sm">

                <div class="list-wrapper">
                    <div style="font-weight: bold;text-align: center;color: #555;background-color: #fff;padding-top: 11px;">Followers ({{count($followers)}})</div>
                    <ul class="list">
                        @foreach($followers as $follower)
                            <li class="list-item">
                                <div>
                                    <img src="{{$follower->profile_image_url}}"  class="list-item-image">
                                </div>
                                <div class="list-item-content">
                                    <h4>{{$follower->name}}</h4>
                                    <p><a target="_blank" href="http://twitter.com/{{$follower->screen_name}}">{{"@".$follower->screen_name}}</a></p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
            <div class="col-sm">

                <div class="list-wrapper">
                    <div style="font-weight: bold;text-align: center;color: #555;background-color: #fff;padding-top: 11px;">Following ({{count($followings)}})</div>
                    <ul class="list">
                        @foreach($followings as $following)
                            <li class="list-item">
                                <div>
                                    <img src="{{$following->profile_image_url}}"  class="list-item-image">
                                </div>
                                <div class="list-item-content">
                                    <h4>{{$following->name}}</h4>
                                    <p><a  target="_blank" href="http://twitter.com/{{$following->screen_name}}">{{"@".$following->screen_name}}</a></p>
                                </div>
                            </li>
                        @endforeach
                    </ul>
                </div>

            </div>
        </div>
    </div>




    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js" integrity="sha384-DfXdz2htPH0lsSSs5nCTpuj/zy4C+OGpamoFVy38MVBnE+IbbVYUew+OrCXaRkfj" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/popper.js@1.16.1/dist/umd/popper.min.js" integrity="sha384-9/reFTGAW83EW2RDu2S0VKaIzap3H66lZH81PoYlFhbGU+6BZp6G7niu735Sk7lN" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.6.0/dist/js/bootstrap.min.js" integrity="sha384-+YQ4JLhjyBLPDQt//I+STsc9iw4uQqACwlvpslubQzn4u2UU2UFM80nGisd026JF" crossorigin="anonymous"></script>
    </body>
</html>
