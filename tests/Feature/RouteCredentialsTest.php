<?php

namespace Tests\Feature;

use App\Holiday;
use App\News;
use App\Position;
use App\Service;
use App\Training;
use App\Training_user;
use App\User;
use Carbon\Carbon;
use DemoClientSeeder;
use Illuminate\Foundation\Testing\WithoutMiddleware;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Session;
use Tests\TestCase;

class RouteCredentialsTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        Artisan::call('config:clear');
    }


    protected function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test all routes without Auth.
     *
     * @return void
     */
    public function test_AllRoutesNoAuthAvailable()
    {
        //should be possible without any DB entry

        Session::start();
        $token = session('_token');

        $credentials = array(
            'email' => 'wronguser@philhil.de',
            'password' => 'wrongpass',
            '_token' => $token
        );

        $session = array(
            '_token' => $token
        );

        //index has redirect
        $this->get('/')->assertStatus(302);

        //index redirect to login page
        $this->followingRedirects()->get('/')
            ->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //Auth Login
        $this->get('/login')->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        $this->followingRedirects()->post('/login', $credentials)->assertStatus(200)
            ->assertViewIs('auth.login');

        //Auth Logout POST
        $this->followingRedirects()->post('/logout', ['_token' => $token])->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //Auth Logout GET
        $this->get('/logout')->assertStatus(302);
        $this->followingRedirects()->get('/logout')->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //Register Message GET
        $this->get('/auth/success')->assertStatus(200)
            ->assertSee('Dein Benutzer muss erst freigeschaltet werden. Du wirst per E-Mail benachrichtigt.')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //Social Auth GET
        $this->get('/redirect')->assertStatus(302);
        $this->followingRedirects()->get('/redirect');

        $this->get('/callback')->assertStatus(302);
        $this->followingRedirects()->get('/callback')
            ->assertStatus(200)
            ->assertSee('Die nötigen Daten können nicht von Facebook abrufen werden oder es wurde der App nicht zugestimmt.');

        // impressum
        $this->get('/impressum')->assertStatus(200)->assertSee('Impressum')->assertSee('Datenschutz');;

        // datenschutz
        $this->get('/datenschutz')->assertStatus(200)->assertSee('Impressum')->assertSee('Datenschutz');

        // order
        $this->get('/order')->assertStatus(200)
            ->assertSee('Dein Dienstplan')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->get('/order/create/basic')->assertStatus(200)
            ->assertSee('Basis Paket')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->get('/order/create/module')->assertStatus(200)
            ->assertSee('Modulares Paket')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->get('/order/create/support')->assertStatus(200)
            ->assertSee('Support Paket')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //following sites should not be available for guests and redirect to login

        //home
        $this->followingRedirects()->get('/home')->assertStatus(200)->assertViewIs('auth.login');

        //mailtest
        $this->followingRedirects()->get('/mailtest')->assertStatus(200)->assertViewIs('auth.login');

        //pdf extract
        $this->followingRedirects()->get('/pdf')->assertStatus(200)->assertViewIs('auth.login');

        //sendService PDF
        $this->followingRedirects()->get('/sendServicePDF')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/sendServicePDF', $session)->assertStatus(419);

        //userguide
        $this->followingRedirects()->get('/userguide')->assertStatus(200)->assertViewIs('auth.login');

        //superadmin user
        $this->followingRedirects()->get('/superadmin/user')->assertStatus(200)->assertViewIs('auth.login');

        //qualification (resource)
        $this->followingRedirects()->get('/qualification')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/qualification', $session)->assertStatus(419);
        $this->followingRedirects()->get('/qualification/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->put('/qualification/1', $session)->assertStatus(419);
        $this->delete('/qualification/1', $session)->assertStatus(419);
        $this->followingRedirects()->get('/qualification/1/edit')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/qualification/create')->assertStatus(200)->assertViewIs('auth.login');

        //user (resource)
        $this->followingRedirects()->get('/user')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/user', $session)->assertStatus(419);
        $this->followingRedirects()->get('/user/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->put('/user/1', $session)->assertStatus(419);
        $this->delete('/user/1', $session)->assertStatus(419);
        $this->followingRedirects()->get('/user/1/edit')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/user/create')->assertStatus(200)->assertViewIs('auth.login');

        //user approve
        $this->followingRedirects()->get('/user/approve/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/user/approve/1', $session)->assertStatus(419);

        //client (resource)
        $this->followingRedirects()->get('/client')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/client', $session)->assertStatus(419);
        $this->followingRedirects()->get('/client/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->put('/client/1', $session)->assertStatus(419);
        $this->delete('/client/1', $session)->assertStatus(419);
        $this->followingRedirects()->get('/client/1/edit')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/client/create')->assertStatus(200)->assertViewIs('auth.login');

        // client apply
        $this->followingRedirects()->get('/clientapply')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/client/1/apply')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/client/1/apply', $session)->assertStatus(419);
        $this->delete('/client/1/apply', $session)->assertStatus(419);
        $this->put('/client/1/apply', $session)->assertStatus(419);
        $this->followingRedirects()->get('/client/1/apply/revert')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/client/1/apply/revert', $session)->assertStatus(419);
        $this->delete('/client/1/apply/revert', $session)->assertStatus(419);
        $this->put('/client/1/apply/revert', $session)->assertStatus(419);
        $this->followingRedirects()->get('/client/1/removeuser/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/client/1/removeuser/1', $session)->assertStatus(419);
        $this->delete('/client/1/removeuser/1', $session)->assertStatus(419);
        $this->put('/client/1/removeuser/1', $session)->assertStatus(419);

        //client update module
        $this->followingRedirects()->get('/client/module')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/client/module', $session)->assertStatus(419);
        $this->delete('/client/module', $session)->assertStatus(419);
        $this->put('/client/module', $session)->assertStatus(419);

        //change client
        $this->followingRedirects()->get('/changeclient/1')->assertStatus(200)->assertViewIs('auth.login');

        //service (resource)
        $this->followingRedirects()->get('/service')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/service', $session)->assertStatus(419);
        $this->followingRedirects()->get('/service/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->put('/service/1', $session)->assertStatus(419);
        $this->delete('/service/1', $session)->assertStatus(419);
        $this->followingRedirects()->get('/service/1/edit')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/service/create')->assertStatus(200)->assertViewIs('auth.login');

        $this->followingRedirects()->get('/service/1/delete')->assertStatus(200)->assertViewIs('auth.login');

        //training (resource)
        $this->followingRedirects()->get('/training')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/training', $session)->assertStatus(419);
        $this->followingRedirects()->get('/training/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->put('/training/1', $session)->assertStatus(419);
        $this->delete('/training/1', $session)->assertStatus(419);
        $this->followingRedirects()->get('/training/1/edit')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/training/create')->assertStatus(200)->assertViewIs('auth.login');

        $this->followingRedirects()->get('/training/1/delete')->assertStatus(200)->assertViewIs('auth.login');

        //training user
        $this->followingRedirects()->get('/training/training_user/1/delete')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/training/training_user/1/delete', $session)->assertStatus(419);
        $this->delete('/training/training_user/1/delete', $session)->assertStatus(419);
        $this->put('/training/training_user/1/delete', $session)->assertStatus(419);

        //news (resource)
        $this->followingRedirects()->get('/news')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/news', $session)->assertStatus(419);
        $this->followingRedirects()->get('/news/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->put('/news/1', $session)->assertStatus(419);
        $this->delete('/news/1', $session)->assertStatus(419);
        $this->followingRedirects()->get('/news/1/edit')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/news/create')->assertStatus(200)->assertViewIs('auth.login');

        $this->followingRedirects()->get('/news/1/delete')->assertStatus(200)->assertViewIs('auth.login');

        //holiday (resource)
        $this->followingRedirects()->get('/holiday')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/holiday', $session)->assertStatus(419);
        $this->followingRedirects()->get('/holiday/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->put('/holiday/1', $session)->assertStatus(419);
        $this->delete('/holiday/1', $session)->assertStatus(419);
        $this->followingRedirects()->get('/holiday/1/edit')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/holiday/create')->assertStatus(200)->assertViewIs('auth.login');

        $this->followingRedirects()->get('/holiday/storeservice/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->followingRedirects()->get('/holiday/storetraining/1')->assertStatus(200)->assertViewIs('auth.login');

        // Qualification <-> User
        $this->post('/qualification_user/create', $session)->assertStatus(419);
        $this->followingRedirects()->get('/qualification_user/delete/1/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/qualification_user/delete/1/1', $session)->assertStatus(419);

        // Client <-> User
        $this->post('/client_user/admin', $session)->assertStatus(419);
        $this->post('/client_user/trainingeditor', $session)->assertStatus(419);

        //Position
        $this->followingRedirects()->get('/position/1/subscribe')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/1/subscribe', $session)->assertStatus(419);

        $this->followingRedirects()->get('/position/1/subscribe_user/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/1/subscribe_user/1', $session)->assertStatus(419);

        $this->followingRedirects()->get('/position/1/unsubscribe')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/1/unsubscribe', $session)->assertStatus(419);

        $this->followingRedirects()->get('/position/1/unsubscribe_user/1')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/1/unsubscribe_user/1', $session)->assertStatus(419);

        $this->followingRedirects()->get('/position/1/authorize')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/1/authorize', $session)->assertStatus(419);

        $this->followingRedirects()->get('/position/1/deauthorize')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/1/deauthorize', $session)->assertStatus(419);

        $this->followingRedirects()->get('/position/list_notAuthorized')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/list_notAuthorized', $session)->assertStatus(419);

        $this->followingRedirects()->get('/position/1/position_user')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/position/1/position_user', $session)->assertStatus(419);

        //Statistic
        $this->followingRedirects()->get('/statistic')->assertStatus(200)->assertViewIs('auth.login');
        $this->post('/statistic', $session)->assertStatus(419);
    }

    /**
     * Test all routes in a perspective of a user.
     *
     * @return void
     */
    public function test_AllRoutesAsUserAvailable()
    {
        //fill DB with demo data to act like a User
        $this->seed(DemoClientSeeder::class);

        //session
        Session::start();
        $token = session('_token');
        $credentials = array(
            'email' => 'user.demodienstplan@philhil.de',
            'password' => 'user',
            '_token' => $token
        );

        //act as User
        $user = User::where('name', '=', "User")->first();
        dd($user);

        //Login as User
        //GET|HEAD                               | login
        $this->get('/login')->assertStatus(200)
            ->assertViewIs('auth.login')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //POST                                   | login
        $this->followingRedirects()->post('/login', $credentials)->assertStatus(200)
            ->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //Login: redirect to home
        $this->actingAs($user)->followingRedirects()->get('/login')
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');


        //GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | /
        $this->actingAs($user)->followingRedirects()->get('/')
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->actingAs($user)->followingRedirects()->post('/', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->actingAs($user)->followingRedirects()->put('/', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->actingAs($user)->followingRedirects()->patch('/', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->actingAs($user)->followingRedirects()->delete('/', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');
        $this->actingAs($user)->followingRedirects()->options('/', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | auth/success
        $this->actingAs($user)->followingRedirects()->get('/auth/success')
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | callback
        $this->actingAs($user)->followingRedirects()->get('/callback')->assertStatus(200);

        //GET|HEAD                               | captcha/api/{config?}
        $this->actingAs($user)->followingRedirects()->get('/captcha/api/')
            ->assertStatus(200);

        //GET|HEAD                               | captcha/{config?}
        $this->actingAs($user)->followingRedirects()->get('/captcha')
            ->assertStatus(200);

        //GET|HEAD                               | changeclient/{client}
        $this->actingAs($user)->followingRedirects()->get('/changeclient/'.($user->currentclient_id))->assertStatus(200);
        $this->actingAs($user)->followingRedirects()->get('/changeclient/'.($user->currentclient_id+1))->assertStatus(500);

        //POST                                   | client
        $this->actingAs($user)->followingRedirects()->post('/client', ['_token' => $token])->assertStatus(402); //not allowed in demo mode

        //GET|HEAD                               | client
        $this->actingAs($user)->followingRedirects()->get('/client')->assertStatus(402); //only as superadmin

        //GET|HEAD                               | client/create
        $this->actingAs($user)->followingRedirects()->get('/client/create')->assertStatus(402); //only as superadmin

        //GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | client/module
        $this->actingAs($user)->followingRedirects()->get('/client/module')->assertStatus(402); //only as superadmin
        $this->actingAs($user)->followingRedirects()->post('/client/module', ['_token' => $token,])->assertStatus(402); //only as superadmin
        $this->actingAs($user)->followingRedirects()->put('/client/module', ['_token' => $token])->assertStatus(402); //only as superadmin
        $this->actingAs($user)->followingRedirects()->patch('/client/module', ['_token' => $token])->assertStatus(402); //only as superadmin
        $this->actingAs($user)->followingRedirects()->delete('/client/module', ['_token' => $token])->assertStatus(402); //only as superadmin
        $this->actingAs($user)->followingRedirects()->options('/client/module')->assertStatus(402); //only as superadmin

        //GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | client/{clientid}/apply
        $this->actingAs($user)->get('/client/'.$user->currentclient_id.'/apply')->assertStatus(302);
        $this->actingAs($user)->post('/client/'.$user->currentclient_id.'/apply', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->put('/client/'.$user->currentclient_id.'/apply', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->patch('/client/'.$user->currentclient_id.'/apply', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->delete('/client/'.$user->currentclient_id.'/apply', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->options('/client/'.$user->currentclient_id.'/apply')->assertStatus(302);

        //GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | client/{clientid}/apply/revert
        $this->actingAs($user)->get('/client/'.$user->currentclient_id.'/apply/revert')->assertStatus(302);
        $this->actingAs($user)->post('/client/'.$user->currentclient_id.'/apply/revert', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->put('/client/'.$user->currentclient_id.'/apply/revert', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->patch('/client/'.$user->currentclient_id.'/apply/revert', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->delete('/client/'.$user->currentclient_id.'/apply/revert', ['_token' => $token])->assertStatus(302);
        $this->actingAs($user)->options('/client/'.$user->currentclient_id.'/apply/revert')->assertStatus(302);

        //GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | client/{clientid}/removeuser/{userid}
        $this->actingAs($user)->get('/client/'.$user->currentclient_id.'/removeuser/'.$user->id)->assertStatus(402); //only if admin of client and not in demo mode
        $this->actingAs($user)->post('/client/'.$user->currentclient_id.'/removeuser/'.$user->id, ['_token' => $token])->assertStatus(402); //only if admin of client and not in demo mode
        $this->actingAs($user)->put('/client/'.$user->currentclient_id.'/removeuser/'.$user->id, ['_token' => $token])->assertStatus(402); //only if admin of client and not in demo mode
        $this->actingAs($user)->patch('/client/'.$user->currentclient_id.'/removeuser/'.$user->id, ['_token' => $token])->assertStatus(402); //only if admin of client and not in demo mode
        $this->actingAs($user)->delete('/client/'.$user->currentclient_id.'/removeuser/'.$user->id, ['_token' => $token])->assertStatus(402); //only if admin of client and not in demo mode
        $this->actingAs($user)->options('/client/'.$user->currentclient_id.'/removeuser/'.$user->id)->assertStatus(402); //only if admin of client and not in demo mode

        //PUT|PATCH                              | client/{client}
        $this->actingAs($user)->put('/client/'.$user->currentclient_id, ['_token' => $token])->assertStatus(402); //only if admin of client or superadmin and not in demo mode
        $this->actingAs($user)->patch('/client/'.$user->currentclient_id, ['_token' => $token])->assertStatus(402); //only if admin of client or superadmin and not in demo mode

        //GET|HEAD                               | client/{client}
        $this->actingAs($user)->get('/client/'.$user->currentclient_id)->assertStatus(402); //only if admin of client or superadmin

        //DELETE                                 | client/{client}
        $this->actingAs($user)->delete('/client/'.$user->currentclient_id, ['_token' => $token])->assertStatus(402); //only if superadmin and not in demo mode

        //GET|HEAD                               | client/{client}/edit
        $this->actingAs($user)->get('/client/'.$user->currentclient_id.'/edit')->assertStatus(402); //only if admin of client or superadmin

        //POST                                   | client_user/admin
        $this->actingAs($user)->post('/client_user/admin', ['_token' => $token, 'client_id' => $user->currentclient_id, 'user_id' => $user->id])
            ->assertStatus(402); //only if admin of client or superadmin

        //POST                                   | client_user/trainingeditor
        $this->actingAs($user)->post('/client_user/trainingeditor', ['_token' => $token, 'client_id' => $user->currentclient_id, 'user_id' => $user->id])
            ->assertStatus(402); //only if admin of client or superadmin

        //GET|HEAD                               | clientapply
        $this->actingAs($user)->get('/clientapply')
            ->assertStatus(200)->assertViewIs('client.apply')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | datenschutz
        $this->actingAs($user)->get('/datenschutz')
            ->assertStatus(200)->assertViewIs('legal.datenschutz')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //POST                                   | holiday
        $this->actingAs($user)->followingRedirects()->post('/holiday', ['_token' => $token, 'from' => Carbon::now(), 'to' => Carbon::now()->addDay()])
            ->assertStatus(200);

        //GET|HEAD                               | holiday
        $this->actingAs($user)->get('/holiday')
            ->assertStatus(200)->assertViewIs('holiday.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | holiday/create
        $this->actingAs($user)->get('/holiday/create')
            ->assertStatus(200)->assertViewIs('holiday.create')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | holiday/storeservice/{service}
        $this->actingAs($user)->followingRedirects()->get('/holiday/storeservice/'.Service::first()->id)
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | holiday/storetraining/{training}
        $this->actingAs($user)->followingRedirects()->get('/holiday/storetraining/'.Training::first()->id)
            ->assertStatus(200)->assertViewIs('training.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | holiday/{holiday}
        $this->actingAs($user)->get('/holiday/'.Holiday::first()->id)
            ->assertStatus(200);

        //PUT|PATCH                              | holiday/{holiday}
        $this->actingAs($user)->followingRedirects()->put('/holiday/'.Holiday::first()->id, ['_token' => $token])
            ->assertStatus(200);
        $this->actingAs($user)->followingRedirects()->patch('/holiday/'.Holiday::first()->id, ['_token' => $token])
            ->assertStatus(200);

        //GET|HEAD                               | holiday/{holiday}/edit
        $this->actingAs($user)->followingRedirects()->get('/holiday/'.Training::first()->id.'/edit')
            ->assertStatus(200)->assertViewIs('holiday.edit')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //DELETE                                 | holiday/{holiday}
        $this->actingAs($user)->get('/holiday/'.Holiday::first()->id)
            ->assertStatus(200);

        //GET|HEAD                               | home
        $this->actingAs($user)->get('/home')
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | impressum
        $this->actingAs($user)->get('/impressum')
            ->assertStatus(200)->assertViewIs('legal.impressum')
            ->assertSee('Impressum')->assertSee('Datenschutz')
            ->assertSee('Inhaltlich Verantwortlicher gem. ');

        //GET|HEAD                               | mailtest
        $this->actingAs($user)->followingRedirects()->get('/mailtest')
            ->assertStatus(200)->assertViewIs('email.serviceslist')
            ->assertSee('Stand')->assertSee('Uhr')->assertSee('Datum');

        //GET|HEAD                               | news
        $this->actingAs($user)->get('/news')
            ->assertStatus(200)->assertViewIs('news.index');

        //POST                                   | news
        $this->actingAs($user)->post('/news', ['_token' => $token])->assertStatus(402); //only admin of client

        //GET|HEAD                               | news/create
        $this->actingAs($user)->get('/news/create')->assertStatus(402); //only admin of client

        //GET|HEAD                               | news/{news}
        $this->actingAs($user)->get('/news/'.News::first()->id)->assertStatus(200);

        //DELETE                                 | news/{news}
        $this->actingAs($user)->delete('/news/'.News::first()->id, ['_token' => $token])->assertStatus(402); //only admin of client

        //PUT|PATCH                              | news/{news}
        $this->actingAs($user)->put('/news/'.News::first()->id, ['_token' => $token])->assertStatus(402); //only admin of client
        $this->actingAs($user)->patch('/news/'.News::first()->id, ['_token' => $token])->assertStatus(402); //only admin of client

        //GET|HEAD                               | news/{news}/delete
        $this->actingAs($user)->get('/news/'.News::first()->id.'/delete')->assertStatus(402); //only admin of client

        //GET|HEAD                               | news/{news}/edit
        $this->actingAs($user)->get('/news/'.News::first()->id.'/edit')->assertStatus(402); //only admin of client

        //GET|HEAD                               | order
        $this->actingAs($user)->get('/order')
            ->assertStatus(200)->assertViewIs('order.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | order/create/{package}
        $this->actingAs($user)->get('/order/create/basic')
            ->assertStatus(200)->assertViewIs('order.order')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //POST                                   | order/{package}

        //POST                                   | password/email
        $this->actingAs($user)->followingRedirects()->post('/password/email', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | password/reset
        $this->actingAs($user)->followingRedirects()->get('/password/reset')
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //POST                                   | password/reset
        $this->actingAs($user)->followingRedirects()->post('/password/reset', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | password/reset/{token}

        //GET|HEAD                               | pdf
        $response = $this->actingAs($user)->get('/pdf');
        self::assertTrue($response->headers->get("content-type") == "application/pdf");

        //GET|POST|HEAD                          | position/list_notAuthorized
        $this->actingAs($user)->get('/position/list_notAuthorized')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/position/list_notAuthorized', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|POST|HEAD                          | position/{id}/authorize
        $this->actingAs($user)->get('/position/1/authorize')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/position/1/authorize', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|POST|HEAD                          | position/{id}/deauthorize
        $this->actingAs($user)->get('/position/1/deauthorize')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/position/1/deauthorize', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|POST|HEAD                          | position/{id}/position_user
        $this->actingAs($user)->get('/position/1/position_user')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/position/1/position_user', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|POST|HEAD                          | position/{id}/subscribe
        $this->actingAs($user)->get('/position/1/subscribe')->assertStatus(200);
        $this->actingAs($user)->post('/position/1/subscribe', ['_token' => $token])->assertStatus(200);

        //GET|POST|HEAD                          | position/{id}/unsubscribe
        $this->actingAs($user)->get('/position/1/unsubscribe')->assertStatus(200);
        $this->actingAs($user)->post('/position/1/unsubscribe', ['_token' => $token])->assertStatus(200);

        //GET|POST|HEAD                          | position/{positionid}/subscribe_user/{userid}
        $this->actingAs($user)->get('/position/1/subscribe_user/1')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/position/1/subscribe_user/1', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|POST|HEAD                          | position/{positionid}/unsubscribe_user/{userid}
        $this->actingAs($user)->get('/position/1/subscribe_user/1')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/position/1/subscribe_user/1', ['_token' => $token])->assertStatus(402); //only as admin

        //POST                                   | qualification
        $this->actingAs($user)->post('/qualification', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|HEAD                               | qualification
        $this->actingAs($user)->get('/qualification')->assertStatus(402);//only as admin

        //GET|HEAD                               | qualification/create
        $this->actingAs($user)->get('/qualification/create')->assertStatus(402);//only as admin

        //PUT|PATCH                              | qualification/{qualification}
        $this->actingAs($user)->put('/qualification/1', ['_token' => $token])->assertStatus(402); //only as admin

        //DELETE                                 | qualification/{qualification}
        $this->actingAs($user)->delete('/qualification/1', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|HEAD                               | qualification/{qualification}
        $this->actingAs($user)->get('/qualification/1')->assertStatus(402);//only as admin

        //GET|HEAD                               | qualification/{qualification}/edit
        $this->actingAs($user)->get('/qualification/1/edit')->assertStatus(402);//only as admin

        //POST                                   | qualification_user/create
        $this->actingAs($user)->post('/qualification_user/create', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|POST|HEAD                          | qualification_user/delete/{user_id}/{qualification_id}
        $this->actingAs($user)->get('/qualification_user/delete/1/1')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/qualification_user/delete/1/1', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|HEAD                               | redirect
        $this->actingAs($user)->get('/redirect')->assertStatus(302); //redirect to facebook login

        //GET|HEAD                               | register
        $this->actingAs($user)->followingRedirects()->get('/register')
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //POST                                   | register
        $this->actingAs($user)->followingRedirects()->post('/register', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('home.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|POST|HEAD                          | sendServicePDF
        $this->actingAs($user)->get('/sendServicePDF')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/sendServicePDF', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|HEAD                               | service
        $this->actingAs($user)->get('/service')
            ->assertStatus(200)->assertViewIs('service.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //POST                                   | service
        $this->actingAs($user)->post('/service', ['_token' => $token])->assertStatus(403); //only as admin

        //GET|HEAD                               | service/create
        $this->actingAs($user)->get('/service/create')->assertStatus(402);//only as admin

        //DELETE                                 | service/{service}
        $this->actingAs($user)->delete('/service/1', ['_token' => $token])->assertStatus(402); //only as admin

        //PUT|PATCH                              | service/{service}
        $this->actingAs($user)->put('/service/1', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|HEAD                               | service/{service}
        $this->actingAs($user)->get('/service/1')->assertStatus(402);//only as admin

        //GET|HEAD                               | service/{service}/delete
        $this->actingAs($user)->get('/service/1/delete')->assertStatus(402);//only as admin

        //GET|HEAD                               | service/{service}/edit
        $this->actingAs($user)->get('/service/1/edit')->assertStatus(402);//only as admin

        //GET|POST|HEAD                          | statistic
        $this->actingAs($user)->get('/statistic')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/statistic', ['_token' => $token])->assertStatus(402); //only as admin

        //GET|HEAD                               | superadmin/user
        $this->actingAs($user)->get('/superadmin/user')->assertStatus(402);//only as superadmin

        //POST                                   | training
        $this->actingAs($user)->followingRedirects()->post('/training', ['_token' => $token])->assertStatus(402); //only as admin or trainingseditor

        //GET|HEAD                               | training
        $this->actingAs($user)->get('/training')
            ->assertStatus(200)->assertViewIs('training.index')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //GET|HEAD                               | training/create
        $this->actingAs($user)->get('/training/create')->assertStatus(402);//only as admin

        //GET|HEAD|POST|PUT|PATCH|DELETE|OPTIONS | training/training_user/{training_userid}/delete
        $training_user = Training_user::first();
        $this->actingAs($user)->followingRedirects()->get('/training/training_user/'.$training_user->id.'/delete')
            ->assertStatus(402);//only as admin
        $this->actingAs($user)->followingRedirects()->post('/training/training_user/'.$training_user->id.'/delete', ['_token' => $token])
            ->assertStatus(402);//only as admin
        $this->actingAs($user)->followingRedirects()->put('/training/training_user/'.$training_user->id.'/delete', ['_token' => $token])
            ->assertStatus(402);//only as admin
        $this->actingAs($user)->followingRedirects()->patch('/training/training_user/'.$training_user->id.'/delete', ['_token' => $token])
            ->assertStatus(402);//only as admin
        $this->actingAs($user)->followingRedirects()->delete('/training/training_user/'.$training_user->id.'/delete', ['_token' => $token])
            ->assertStatus(402);//only as admin

        //GET|HEAD                               | training/{training}
        $this->actingAs($user)->get('/training/1')->assertStatus(402);//only as admin or trainingeditor

        //PUT|PATCH                              | training/{training}
        $this->actingAs($user)->put('/training/1', ['_token' => $token])->assertStatus(402);//only as admin or trainingeditor

        //DELETE                                 | training/{training}
        $this->actingAs($user)->delete('/training/1', ['_token' => $token])->assertStatus(402);//only as admin or trainingeditor

        //GET|HEAD                               | training/{training}/delete
        $this->actingAs($user)->get('/training/1/delete')->assertStatus(402);//only as admin or trainingeditor

        //GET|HEAD                               | training/{training}/edit
        $this->actingAs($user)->get('/training/1/edit')->assertStatus(402);//only as admin or trainingeditor

        //POST                                   | user
        $this->actingAs($user)->post('/user', ['_token' => $token])->assertStatus(402);//not implemented

        //GET|HEAD                               | user
        $this->actingAs($user)->get('/user')->assertStatus(402);//only as admin

        //GET|POST|HEAD                          | user/approve/{id}
        $this->actingAs($user)->get('/user/approve/1')->assertStatus(402);//only as admin
        $this->actingAs($user)->post('/user/approve/1', ['_token' => $token])->assertStatus(402);//only as admin

        //GET|HEAD                               | user/create
        $this->actingAs($user)->get('/user/create')->assertStatus(402);//not implemented

        //PUT|PATCH                              | user/{user}
        //Not allowed in Demo Mode :(

        //GET|HEAD                               | user/{user}
        $this->actingAs($user)->get('/user/1')
            ->assertStatus(200)->assertViewIs('user.profile')
            ->assertSee('Impressum')->assertSee('Datenschutz');

        //DELETE                                 | user/{user}
        $this->actingAs($user)->delete('/user/1', ['_token' => $token])->assertStatus(402);//only as superadmin

        //GET|HEAD                               | user/{user}/edit
        $this->actingAs($user)->get('/user/1/edit')->assertStatus(402);//only as admin

        //GET|HEAD                               | userguide
        $response = $this->actingAs($user)->get('/userguide');
        self::assertTrue($response->headers->get("content-type") == "application/pdf");

        //POST                                   | logout
        $this->actingAs($user)->followingRedirects()->post('/logout', ['_token' => $token])
            ->assertStatus(200)->assertViewIs('auth.login');

        //GET|HEAD                               | logout
        $this->actingAs($user)->followingRedirects()->get('/logout')
            ->assertStatus(200)->assertViewIs('auth.login');
    }
}
