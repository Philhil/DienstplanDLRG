<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    protected function validateLogin(Request $request)
    {
        $this->validate($request, [
            'email' => 'required',
            'password' => 'required',
        ]);
    }

    public function login (Request $request)
    {
        $this->validate($request, [
            'email' => 'required', 'password' => 'required',
        ]);


        $user = \App\User::where('email', $request->get('email'))->first();

        if (!$user){
            return redirect('login')
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Diese Emailadresse kennen wir nicht.'
                ]);
        }
        else if ($user->approved == false)
        {
            return redirect('login')
                ->withInput($request->only('email'))
                ->withErrors([
                    'email' => 'Der Account wurde noch nicht freigeschaltet.'
                ]);
        }

        $credentials = $request->only('email', 'password');
        $credentials['approved'] = true;

        if (Auth::attempt($credentials, $request->has('remember'))) {
            return redirect()->intended($this->redirectPath());
        }

        return redirect('login')
            ->withInput($request->only('email'))
            ->withErrors([
                'password' => 'Das war nicht richtig.'
            ]);
    }
}
