<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class ProfileController extends Controller
{
    // Profile Information
    public function getProfile(Request $request)
    {

        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
        ]);

        $user->update([
            'name' => $request->name,
            'email' => $request->email,
        ]);

        return response()->json(['message' => 'Profile updated successfully!']);
    }

    public function updatePassword(Request $request)
    {
        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        $request->validate([
            'current_password' => 'required|string',
            'new_password' => 'required|string|min:8|confirmed',
        ]);

        // Check if current password is correct
        if (!Hash::check($request->current_password, $user->password)) {
            return response()->json(['message' => 'Current password is incorrect'], 400);
        }

        // Update password
        $user->update([
            'password' => Hash::make($request->new_password),
        ]);

        return response()->json(['message' => 'Password updated successfully!']);
    }

    public function deleteAccount()
    {
        $user = Auth::user(); // Get the authenticated user
        if (!$user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Please verify your email.'], 400);
        }

        // Revoke all tokens (logs out user)
        $user->tokens()->delete();

        // Delete user account
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully!']);
    }
}