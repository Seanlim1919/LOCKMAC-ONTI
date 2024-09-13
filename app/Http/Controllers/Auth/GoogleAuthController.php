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

            // Create Google Client and People Service
            $client = new Google_Client();
            $client->setAccessToken($google_user->token);
            $peopleService = new Google_Service_PeopleService($client);

            // Fetch user profile from Google People API
            $profile = $peopleService->people->get('people/me', ['personFields' => 'names,emailAddresses,birthdays,genders']);

            // Handle names
            $names = $profile->getNames();
            $fullName = !empty($names) ? $names[0]->getDisplayName() : 'Unknown User';
            $nameParts = explode(' ', $fullName);
            $firstName = $nameParts[0] ?? 'Unknown';
            $lastName = isset($nameParts[1]) ? array_pop($nameParts) : 'Unknown';
            $middleName = count($nameParts) > 1 ? implode(' ', $nameParts) : null;

            // Handle birthdays
            $birthdays = $profile->getBirthdays();
            $dateOfBirth = !empty($birthdays) ? $birthdays[0]->getDate() : null;

            // Handle genders
            $genders = $profile->getGenders();
            $gender = !empty($genders) ? $genders[0]->getValue() : null;

            // Check if user already exists in the database
            $user = User::where('email', $google_user->getEmail())->first();
            
            if (!$user) {
                // Redirect to login page with data indicating the user is not registered
                return redirect()->route('login')
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
                // Only update the user image if user already exists
                $user->update([
                    'user_image' => $google_user->getAvatar(),
                    'google_id' => $google_user->getId(),
                ]);

                // Log in the user
                Auth::login($user);

                // Redirect to the intended location or dashboard
                return redirect()->intended('dashboard');
            }
        } catch (\Exception $e) {
            // Log the exception for debugging
            Log::error('Google Auth Error: ' . $e->getMessage());
            
            // Redirect to login page with the error message
            return redirect()->route('login')->withErrors(['error' => 'An error occurred during authentication. Please try again.']);
        }
    }
}
