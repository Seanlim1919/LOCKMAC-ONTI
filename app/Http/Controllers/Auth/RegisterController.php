<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\RFID;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use App\Mail\OtpMail;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class RegisterController extends Controller
{
    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = '/login';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Show the registration form.
     *
     * @return \Illuminate\View\View
     */
    public function showRegistrationForm(Request $request)
    {
        $clientIp = $request->getClientIp();
        return view('auth.register', ['clientIp' => $clientIp]);
    }

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'otp' => ['nullable', 'string', 'size:6'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rfid' => ['required', 'string'],
            'phone_number' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d'],
            'google_id' => ['nullable', 'string', 'max:255'], 
            'user_image' => ['nullable', 'string'] 
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->filled('otp')) {
            if (!$this->isOtpValid($request->input('email'), $request->input('otp'))) {
                return redirect()->back()->withErrors(['otp' => 'Invalid OTP.']);
            }
        } else {
            $this->sendOtp($request->input('email'));
            return redirect()->back()->with('status', 'OTP sent successfully.');
        }

        $rfidCode = str_replace(' ', '', $request->input('rfid'));
        DB::beginTransaction();

        try {
            $existingRfid = RFID::firstOrCreate(['rfid_code' => $rfidCode]);

            if (User::where('rfid_id', $existingRfid->id)->exists()) {
                throw new \Exception('This RFID code is already registered to another user');
            }

            $user = User::create([
                'first_name' => $request->input('first_name'),
                'middle_name' => $request->input('middle_name'),
                'last_name' => $request->input('last_name'),
                'email' => $request->input('email'),
                'phone_number' => $request->input('phone_number'),
                'gender' => $request->input('gender'),
                'date_of_birth' => $request->input('date_of_birth'),
                'password' => Hash::make($request->input('password')),
                'rfid_id' => $existingRfid->id,
                'role' => 'faculty',
                'google_id' => $request->input('google_id'),
                'user_image' => $request->input('user_image'),
            ]);

            DB::commit();

            Auth::login($user);
            return redirect()->route('dashboard')->with('success', 'Registration successful!');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Registration failed', ['error' => $e->getMessage()]);
            return redirect()->back()->withInput()->withErrors(['rfid' => 'An error occurred during registration. Please try again.']);
        }
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

        Log::info('Verify OTP', [
            'email' => $email,
            'cachedOtp' => $cachedOtp,
            'providedOtp' => $otp
        ]);

        if ((string) $cachedOtp === (string) $otp) {
            Cache::forget('otp_' . $email);
            return true;
        }

        Log::warning('Invalid OTP attempt', [
            'email' => $email,
            'cachedOtp' => $cachedOtp,
            'providedOtp' => $otp
        ]);

        return false;
    }

    protected function isOtpValid($email, $otp)
    {
        $cachedOtp = Cache::get('otp_' . $email);
        return (string) $cachedOtp === (string) $otp;
    }

    protected function sendOtp($email)
    {
        $otp = rand(100000, 999999);
        Cache::put('otp_' . $email, (string) $otp, now()->addMinutes(10));
        Mail::to($email)->send(new OtpMail($otp));
    }

    /**
     * Verify email for registration.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyEmail(Request $request)
    {
        $email = $request->input('email');

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return response()->json(['success' => false, 'message' => 'Invalid email format'], 400);
        }

        if (User::where('email', $email)->exists()) {
            return response()->json(['success' => false, 'message' => 'Email is already registered'], 400);
        }

        try {
            $this->sendOtp($email);
            return response()->json(['success' => true, 'message' => 'OTP sent successfully']);
        } catch (\Exception $e) {
            Log::error('OTP sending failed: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Failed to send OTP'], 500);
        }
    }

    /**
     * Verify OTP code from the request.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required|numeric|digits:6',
        ]);

        $email = $request->input('email');
        $otp = $request->input('otp');

        return response()->json(['valid' => $this->isOtpValid($email, $otp)]);
    }
}
