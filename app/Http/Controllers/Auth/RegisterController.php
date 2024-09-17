<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;

class RegisterController extends Controller
{
    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        // Validate the form inputs
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'otp' => ['nullable', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rfid' => ['required', 'string', 'size:10'],
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        // Handle OTP verification or sending
        if ($request->filled('otp')) {
            if (!$this->verifyOtpCode($request->input('email'), $request->input('otp'))) {
                return redirect()->back()->withErrors(['otp' => 'Invalid OTP.']);
            }
        } else {
            // Send OTP and notify user
            $this->sendOtp($request->input('email'));
            return redirect()->back()->with('status', 'OTP sent successfully.');
        }

        // Create and log in the user
        $user = User::create([
            'first_name' => $request->input('first_name'),
            'middle_name' => $request->input('middle_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
            'rfid' => $request->input('rfid'),
        ]);

        Auth::login($user);

        return redirect()->route('home')->with('success', 'Registration successful!');
    }

    /**
     * Verify OTP code.
     *
     * @param string $email
     * @param string $otp
     * @return bool
     */
    protected function verifyOtpCode($email, $otp)
    {
        $cachedOtp = Cache::get('otp_' . $email);

        if ($cachedOtp === $otp) {
            Cache::forget('otp_' . $email); // Remove OTP from cache after successful verification
            return true;
        }

        return false;
    }

    /**
     * Send OTP to the user email.
     *
     * @param string $email
     * @return void
     */
    protected function sendOtp($email)
    {
        $otp = rand(100000, 999999); // Generate a 6-digit OTP
        Cache::put('otp_' . $email, $otp, now()->addMinutes(10)); // Store OTP in cache for 10 minutes

        // Send OTP to email
        Mail::to($email)->send(new OtpMail($otp));
    }

    public function verifyEmail(Request $request)
{
    $email = $request->input('email');

    // Validate email format
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return response()->json(['success' => false, 'message' => 'Invalid email format'], 400);
    }

    // Check if email is already in use
    if (User::where('email', $email)->exists()) {
        return response()->json(['success' => false, 'message' => 'Email is already registered'], 400);
    }

    // Send OTP if email is valid and not in use
    try {
        $this->sendOtp($email);
        return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
    } catch (\Exception $e) {
        // Log the error and return a generic error message
        \Log::error('OTP sending failed: '.$e->getMessage());
        return response()->json(['success' => false, 'message' => 'Failed to send OTP'], 500);
    }
}


}
