<a href="twitter/login"> twitter</a> <br>
<br><br><br>

Followers<br>
<div class="list-wrapper">
    <ul class="list">
        @foreach($followers as $follower)
            <li class="list-item">
                <div>
                    <img src="{{$follower->profile_image_url}}"  class="list-item-image">
                </div>
                <div class="list-item-content">
                    <h4>{{$follower->name}}</h4>
                    <p><a href="http://twitter/{{$follower->screen_name}}">{{"@".$follower->screen_name}}</a></p>
                </div>
            </li>
        @endforeach
    </ul>
</div>


<div class="list-wrapper">
    <ul class="list">
        @foreach($followers as $follower)
            <li class="list-item">
                <div>
                    <img src="{{$follower->profile_image_url}}"  class="list-item-image">
                </div>
                <div class="list-item-content">
                    <h4>{{$follower->name}}</h4>
                    <p><a href="http://twitter/{{$follower->screen_name}}">{{"@".$follower->screen_name}}</a></p>
                </div>
            </li>
        @endforeach
    </ul>
</div>
