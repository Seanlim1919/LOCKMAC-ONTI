<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Rfid;

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

    /**
     * Handle a registration request for the application.
     *
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        $rfidCode = $request->input('rfid_code');

        // Log the RFID code if present
        if ($rfidCode) {
            Log::info('RFID code read successfully', ['rfid' => $rfidCode]);

            // Save the RFID code to the `rfids` table
            $rfid = Rfid::create([
                'rfid_code' => $rfidCode,
            ]);

            // Associate the RFID with the user during creation
            $user = $this->create($request->all() + ['rfid_id' => $rfid->id]);
        } else {
            Log::error('RFID code read failed');
            return redirect()->back()->withErrors(['rfid_code' => 'RFID code is required']);
        }

        $this->guard()->login($user);

        return redirect($this->redirectPath());
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'phone_number' => ['required', 'string', 'max:20'],
            'gender' => ['required', 'string', 'in:male,female'],
            'date_of_birth' => ['required', 'date'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'rfid' => ['nullable', 'string'],
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
        return User::create([
            'first_name' => $data['first_name'],
            'middle_name' => $data['middle_name'],
            'last_name' => $data['last_name'],
            'email' => $data['email'],
            'phone_number' => $data['phone_number'],
            'gender' => $data['gender'],
            'date_of_birth' => $data['date_of_birth'],
            'password' => Hash::make($data['password']),
            'role' => 'faculty', // Default role
        ]);

            // Store RFID code
        if (isset($data['rfid'])) {
            $rfid = new Rfid;
            $rfid->rfid_code = $data['rfid'];
            $rfid->user_id = $user->id; // Assuming user_id is a foreign key in rfids table
            $rfid->save();
            Log::info('RFID registered: ' . $data['rfid']);
        } else {
            Log::error('RFID code was not provided during registration.');
        }

        return $user;
    }
}
