<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\RFID;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB; 
use Illuminate\Support\Facades\Cache;
use App\Notifications\EmailVerificationNotification;

class RegisterController extends Controller
{
    use RegistersUsers;

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
    public function showRegistrationForm()
    {
        return view('auth.register');
    }

    public function verifyOtp(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'otp' => 'required',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return redirect()->back()->withErrors(['email' => 'User not found.']);
        }

        $otpValid = $this->verifyOtpCode($request->email, $request->otp); // Implement this method

        if ($otpValid) {
            $user->markEmailAsVerified(); // Assuming you want to mark the email as verified
            return redirect($this->redirectPath())->with('status', 'Email verified successfully.');
        } else {
            return redirect()->back()->withErrors(['otp' => 'Invalid OTP.']);
        }
    }

    public function verifyOtpCode($email, $otp)
    {
        $cachedOtp = Cache::get('otp_' . $email);
    
        if ($cachedOtp === $otp) {
            Cache::forget('otp_' . $email); // Remove OTP from cache after successful verification
            return true;
        }
    
        return false;
    }


    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();
    
        $rfidCode = $request->input('rfid');
        Log::info('Received RFID code:', ['rfid_code' => $rfidCode]);
    
        // Normalize the RFID code for consistent comparison
        $normalizedRfidCode = str_replace(' ', '', $rfidCode);
        Log::info('Normalized RFID code:', ['rfid_code' => $normalizedRfidCode]);
    
        // Check if the RFID code already exists in the database
        $existingRfid = RFID::where(DB::raw('REPLACE(rfid_code, " ", "")'), $normalizedRfidCode)
                            ->orWhere('rfid_code', $rfidCode)
                            ->first();
    
        if ($existingRfid) {
            Log::info('RFID code already exists in the database.', ['rfid_id' => $existingRfid->id]);
    
            // Check if the RFID is already assigned to a user
            $existingUser = User::where('rfid_id', $existingRfid->id)->first();
            if ($existingUser) {
                Log::error('RFID code is already assigned to another user.');
                return redirect()->back()->withInput()->withErrors(['rfid' => 'This RFID code is already registered to another user']);
            }
        } else {
            Log::info('RFID code does not exist. Proceeding to create a new RFID record.');
    
            // Start a database transaction
            DB::beginTransaction();
    
            try {
                // Create the RFID record
                $rfid = RFID::create([
                    'rfid_code' => $rfidCode,
                ]);
    
                Log::info('RFID created.', ['rfid_id' => $rfid->id]);
    
                // Create the new user
                $user = $this->create($request->all() + ['rfid_id' => $rfid->id]);
    
                Log::info('User created', ['user_id' => $user->id, 'rfid_id' => $user->rfid_id]);
    
                // Commit the transaction
                DB::commit();

                // Send OTP
                $user->notify(new EmailVerificationNotification());
    
                // Log in the user
                $this->guard()->login($user);
    
                // Redirect to the login page
                return redirect($this->redirectPath());
            } catch (\Exception $e) {
                // Rollback the transaction if something goes wrong
                DB::rollBack();
                Log::error('Registration failed', ['error' => $e->getMessage()]);
                return redirect()->back()->withInput()->withErrors(['rfid' => 'An error occurred during registration. Please try again.']);
            }
        }
    
        // Redirect back to the registration form with error if RFID already exists
        return redirect()->back()->withInput()->withErrors(['rfid' => 'RFID code already exists or is invalid.']);
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
    
    

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'first_name' => ['required', 'string', 'max:255'],
            'middle_name' => ['nullable', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users', 'ends_with:@my.cspc.edu.ph'],
            'phone_number' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female'],
            'date_of_birth' => ['required', 'date', 'date_format:Y-m-d'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rfid' => ['nullable', 'string'],
        ], [
            'email.ends_with' => 'The email address must end with @my.cspc.edu.ph',
            'date_of_birth.date_format' => 'The date of birth must be in correct format.',
        ]);
    }
    

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(array $data)
    {
        Log::info('Creating user with data:', $data);
    
        return User::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'password' => Hash::make($data['password']),
            'rfid_id' => $data['rfid_id'] ?? null, 
            'role' => 'faculty',
        ]);
    }
}
