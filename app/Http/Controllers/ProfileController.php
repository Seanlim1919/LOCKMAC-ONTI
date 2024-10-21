<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    /**
     * Show the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function show(Request $request)
    {
        $clientIp = $request->getClientIp();

        if (Auth::user()->role === 'admin') {
            return view(('admin.profile.show') , ['clientIp' => $clientIp]);
        } else {
            return view('faculty.profile.show');
        }


    }
    
    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        if (Auth::user()->role === 'admin') {
            return view('admin.profile.edit');
        } else {
            return view('faculty.profile.edit');
        }
    }
    

    /**
     * Update the user's profile information.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request)
    {
        $user = Auth::user();
    
        $request->validate([
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'required|numeric|min:11',
            'gender' => 'required|string|in:male,female,other', 
            'date_of_birth' => 'required|date',
            'password' => 'nullable|string|confirmed|min:8',
        ]);
    
        // Update user fields
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');
        $user->gender = $request->input('gender');
        $user->date_of_birth = $request->input('date_of_birth');
    
        if ($user->role === 'admin') {
            $request->validate([
                'rfid_code' => 'nullable|string|max:255'
            ]);
    
            // Update RFID code in the rfids table using the user's rfid_id
            if ($request->filled('rfid_code') && $user->rfid_id) {
                $rfid = \App\Models\Rfid::find($user->rfid_id);
                if ($rfid) {
                    $rfid->rfid_code = $request->input('rfid_code');
                    $rfid->save();
                }
            }
        }
    
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }
    
        $user->save();
    
        if ($user->role === 'admin') {
            return redirect()->route('profiles.show', ['role' => 'admin'])->with('success', 'Admin profile updated successfully');
        } else {
            return redirect()->route('profiles.show', ['role' => 'faculty'])->with('success', 'Faculty profile updated successfully');
        }
    }
    
    
    
    
    
}
