<?php

namespace App\Http\Controllers;

use App\User;
use App\SocialAccountService;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use Laravel\Socialite\Contracts\User as ProviderUser;

class SocialAuthController extends Controller
{

    public function redirect()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function callback()
    {
        try
        {
            $user = Socialite::driver('facebook')->user();
        } catch (\Exception $e) {
            return redirect('login')
                ->withErrors([
                    'email' => 'Die nötigen Daten können nicht von Facebook abrufen werden oder es wurde der App nicht zugestimmt.'
                ]);
        }
        return $this->createOrGetUser($user);
    }

    public function createOrGetUser(ProviderUser $providerUser)
    {
        $email = $providerUser->getEmail();
        if($email) {
            $user = User::whereEmail($providerUser->getEmail())->first();

            if (!$user){
                return redirect('login')
                    ->withInput([$email])
                    ->withErrors([
                        'email' => 'Diese Emailadresse kennen wir nicht.'
                    ]);
            }
            else if ($user->approved == false)
            {
                return redirect('login')
                    ->withInput([$email])
                    ->withErrors([
                        'email' => 'Der Account wurde noch nicht freigeschaltet.'
                    ]);
            }

            Auth::login($user);
            return redirect()->to('/service');
        }
        
        return redirect('login')
            ->withInput([$email])
            ->withErrors([
                'email' => 'Kann die nötigen Daten nicht von Facebook abrufen.'
            ]);

    }
}
