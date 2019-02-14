New User: {{$user->first_name}} {{$user->name}} {{$user->email}}
In Client: {{$client->name}}
<br>Freigeben: <a href="{{action('UserController@approve_user', $user->id)}}">{{action('UserController@approve_user', $user->id)}}</a>