New User: {{$user->first_name}} {{$user->name}} {{$user->email}}
In Client: {{$client->name}}
<br>Freigeben: <a href="{{action('UserController@index')}}">{{action('UserController@index')}}</a>