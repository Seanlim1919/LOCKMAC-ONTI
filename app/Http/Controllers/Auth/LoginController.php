<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Session;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Log the user out of the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }

    /**
     * Handle login attempts and disable account after 5 failed attempts.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);
    
        $user = User::where('email', $request->email)->first();
    
        if (!$user) {
            return back()->withErrors(['email' => 'These credentials do not match our records.']);
        }
    
        if ($user->status === 'Disabled') {
            if ($user->role !== 'Admin') {
                return back()->withErrors([
                    'email' => 'Your account has been disabled.<br>Please contact the administrator of the system.'
                ])->withInput();
            }
        }
    
        $loginAttempts = session()->get('login_attempts_' . $request->email, 0);
            if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            session()->forget('login_attempts_' . $request->email);
            return redirect()->intended($this->redirectTo);
        } else {
            session()->put('login_attempts_' . $request->email, ++$loginAttempts);
    
            if ($loginAttempts >= 5) {
                if ($user->role === 'faculty') {
                    $user->update(['status' => 'Disabled']);
                    session()->forget('login_attempts_' . $request->email);
                    return back()->withErrors(['email' => 'Your account has been disabled due to 5 failed login attempts.']);
                } else {
                    return back()->withErrors(['email' => 'Wrong password. Please try again.']);
                }
            }
    
            return back()->withErrors([
                'password' => 'Invalid credentials. ' . (5 - $loginAttempts) . ' attempt(s) remaining before your account is disabled.'
            ]);
        }
    }
    
    

    /**
     * Handle Google login.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse|\Illuminate\Http\RedirectResponse
     */
    public function googleLogin(Request $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            \Log::info('Redirecting to register page with error');
            return redirect()->route('register')->withErrors(['email' => 'User is not registered yet. Please register to continue.']);
        }

        $user = User::firstOrCreate(
            ['email' => $request->email],
            [
                'name' => $request->name,
                'google_id' => $request->google_id,
                'password' => bcrypt(str_random(16)),
                'profile_photo_path' => $request->image,
            ]
        );

        Auth::login($user);

        return response()->json(['success' => true]);
    }
}
