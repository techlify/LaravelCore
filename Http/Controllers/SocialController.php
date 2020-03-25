<?php

namespace Modules\LaravelCore\Http\Controllers;

use App\Http\Controllers\Controller;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use Laravel\Passport\Passport;
use Modules\LaravelCore\Entities\Client;
use Modules\LaravelCore\Events\ClientCreatedEvent;
use Modules\LaravelCore\Emails\WelcomeMail;
use Modules\LaravelCore\Entities\UserType;
use Socialite;

class SocialController extends Controller
{
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }
    public function callback($provider)
    {
        $user = Socialite::driver($provider)->stateless()->user();

        if (!$user->email) {
            return redirect()->to(config('laravelcore.app_frontend_link'));
        }

        $existingUser = User::where('email', $user->email)->first();

        if (!$existingUser) {
            $client = new Client();
            $client->name = "";
            $client->email = "";
            $client->phone = "";
            $client->address = "";
            $client->logo = "";
            $client->letterhead_image = "";
            $client->tin = "";

            if (!$client->save()) {
                return response()->json(['error' => 'Failed to save the Client.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            /* Lets setup our event */
            event(new ClientCreatedEvent($client));

            $characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
            $pin = mt_rand(1000000, 9999999)
                . $characters[rand(0, strlen($characters) - 1)];
            $originalPassword = str_shuffle($pin);

            $newUser = new User();
            $newUser->name = $user->name;
            $newUser->email = $user->email;
            $newUser->password = bcrypt($originalPassword);
            $newUser->is_temporary_password = true;
            $newUser->user_type_id = UserType::CLIENT_ADMIN;
            $newUser->client_id = $client->id;

            if (!$newUser->save()) {
                return response()->json(['error' => 'Failed to save the User.'], Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            Mail::to($newUser->email)->queue(new WelcomeMail($newUser, [], $originalPassword));
        }

        Passport::tokensExpireIn(Carbon::now()->addDays(30));
        Passport::refreshTokensExpireIn(Carbon::now()->addDays(60));

        $user = $existingUser ? $existingUser : $newUser;
        $objToken = $user->createToken('API Access');
        $strToken = $objToken->accessToken;

        $expiration = $objToken->token->expires_at->diffInSeconds(Carbon::now());
        $url = config('laravelcore.app_frontend_link') . '/login?access_token=' . $strToken . '&expires_in=' . $expiration;
        return redirect()->to($url);
    }
}
