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
    public function show()
    {
        return view('faculty.profile.show');
    }

    /**
     * Show the form for editing the user's profile.
     *
     * @return \Illuminate\View\View
     */
    public function edit()
    {
        return view('faculty.profile.edit');
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

        // Validate the incoming request data
        $request->validate([
            'user_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            'first_name' => 'required|string|max:255',
            'middle_name' => 'nullable|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:users,email,' . $user->id,
            'phone_number' => 'required|string|max:15',
            'password' => 'nullable|string|confirmed|min:8',
        ]);

        // Handle image upload
        if ($request->hasFile('user_image')) {
            // Delete old image if exists
            if ($user->user_image && Storage::exists(parse_url($user->user_image, PHP_URL_PATH))) {
                Storage::delete(parse_url($user->user_image, PHP_URL_PATH));
            }

            // Store new image
            $path = $request->file('user_image')->store('public/user_images');
            $user->user_image = Storage::url($path);
        }

        // Update user information
        $user->first_name = $request->input('first_name');
        $user->middle_name = $request->input('middle_name');
        $user->last_name = $request->input('last_name');
        $user->email = $request->input('email');
        $user->phone_number = $request->input('phone_number');

        // Update password if provided
        if ($request->filled('password')) {
            $user->password = bcrypt($request->input('password'));
        }

        // Save user data
        $user->save();

        return redirect()->route('profile.show')->with('success', 'Profile updated successfully');
    }
}
