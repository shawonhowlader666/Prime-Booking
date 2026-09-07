<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Hotel;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function show(Request $request): View
    {
        $user = Auth::user();
        $hotel = $user?->hotel ?? Hotel::first() ?? new Hotel(['name' => 'Grand Palace Hotel', 'city' => 'Dhaka', 'country' => 'Bangladesh']);

        return view('pages.my-profile', compact('user', 'hotel'));
    }

    public function update(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:100',
            'email' => 'required|email|unique:users,email,' . $user->id,
            'phone' => 'nullable|string|max:50',
            'designation' => 'nullable|string|max:100',
            'company' => 'nullable|string|max:150',
            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'about_me' => 'nullable|string',
            'password' => 'nullable|string|min:6',
        ]);

        $user->name = $validated['name'];
        $user->email = $validated['email'];
        if (isset($validated['phone'])) {
            $user->phone = $validated['phone'];
        }
        if (isset($validated['designation'])) {
            $user->designation = $validated['designation'];
        }
        $user->about_me = $request->input('about_me');

        if (!empty($validated['password'])) {
            $user->password = Hash::make($validated['password']);
        }

        $user->save();

        // If user has associated hotel, update hotel metadata as well
        if ($user->hotel) {
            $hotel = $user->hotel;
            if (!empty($validated['company'])) {
                $hotel->name = $validated['company'];
            }
            if (isset($validated['address'])) {
                $hotel->address = $validated['address'];
            }
            if (isset($validated['city'])) {
                $hotel->city = $validated['city'];
            }
            if (isset($validated['country'])) {
                $hotel->country = $validated['country'];
            }
            $hotel->save();
        }

        return redirect()->back()->with('success', 'Profile details have been updated successfully!');
    }
}
