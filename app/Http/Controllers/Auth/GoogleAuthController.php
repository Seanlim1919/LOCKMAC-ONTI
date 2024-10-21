<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Laravel\Socialite\Facades\Socialite;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Google_Client;
use Google_Service_PeopleService;

class GoogleAuthController extends Controller
{
    public function redirect()
    {
        return Socialite::driver('google')
            ->scopes(['profile', 'email', 'openid', 'https://www.googleapis.com/auth/user.birthday.read'])
            ->redirect();
    }

    public function callbackGoogle()
    {
        try {
            $google_user = Socialite::driver('google')->user();

            $client = new Google_Client();
            $client->setAccessToken($google_user->token);
            $peopleService = new Google_Service_PeopleService($client);

            $profile = $peopleService->people->get('people/me', ['personFields' => 'names,emailAddresses,birthdays,genders']);

            // Handle names
            $names = $profile->getNames();
            $fullName = !empty($names) ? $names[0]->getDisplayName() : 'Unknown User';
            $nameParts = explode(' ', $fullName);

            // Combine first name and middle name
            $firstName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 0, -1)) : $fullName;
            $lastName = array_pop($nameParts);
            $middleName = null;

            // Handle birthdays
            $birthdays = $profile->getBirthdays();
            $dateOfBirth = !empty($birthdays) ? $birthdays[0]->getDate() : null;

            // Handle genders
            $genders = $profile->getGenders();
            $gender = !empty($genders) ? $genders[0]->getValue() : null;

            // User retrieval or registration
            $user = User::where('email', $google_user->getEmail())->first();

            if (!$user) {
                return redirect()->route('register')->withErrors(['email' => 'User is not registered yet. Please register to continue.'])
                    ->with('google_user', [
                        'first_name' => $firstName,
                        'middle_name' => $middleName,
                        'last_name' => $lastName,
                        'email' => $google_user->getEmail(),
                        'google_id' => $google_user->getId(),
                        'user_image' => $google_user->getAvatar(),
                        'date_of_birth' => $dateOfBirth,
                        'gender' => $gender,
                    ])
                    ->with('register_prompt', true);
            } else {
                $user->update([
                    'user_image' => $google_user->getAvatar(),
                    'google_id' => $google_user->getId(),
                ]);

                Auth::login($user);

                return redirect()->intended('dashboard');
            }
        } catch (\Exception $e) {
            Log::error('Google Auth Error: ' . $e->getMessage());
            return redirect()->route('login')->withErrors(['error' => 'An error occurred during authentication. Please try again.']);
        }
    }
}
