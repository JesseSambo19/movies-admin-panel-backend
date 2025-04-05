<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\PasswordController;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Password; // For password reset functionality
// use Illuminate\Support\Facades\Log;
// use Illuminate\Auth\Events\Registered;
// use Illuminate\Foundation\Auth\EmailVerificationRequest;

class AuthController extends Controller
{
    // public function register(Request $request)
    // {
    //     $request->validate([
    //         'name' => 'required|string|max:255',
    //         'email' => 'required|string|email|max:255|unique:users',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     $user = User::create([
    //         'name' => $request->name,
    //         'email' => $request->email,
    //         'password' => Hash::make($request->password),
    //     ]);

    //     return response()->json(['message' => 'User created successfully!'], 201);
    // }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Fire the Registered event to send the verification email
        // event(new Registered($user));

        // return response()->json(['message' => 'User registered successfully. Please check your email for verification.'], 201);
        return response()->json(['message' => 'User registered successfully.'], 201);
    }

    public function verifyEmail(Request $request)
    {
        $user = User::find($request->route('id'));

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified'], 200);
        }

        if ($user->markEmailAsVerified()) {
            return response()->json(['message' => 'Email verified successfully']);
        }

        return response()->json(['message' => 'Email verification failed'], 400);
    }

    public function resendVerificationEmail(Request $request)
    {
        $request->validate(['email' => 'required|email|exists:users,email']);

        $user = User::where('email', $request->email)->first();

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email is already verified'], 200);
        }

        // Send a new verification email
        $user->sendEmailVerificationNotification();

        return response()->json(['message' => 'Verification email resent successfully']);
    }

    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|string|email',
            'password' => 'required|string',
        ]);

        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $token = $user->createToken('Movies')->plainTextToken;
            $verified = false;
            // it will check if the email is verified after generating the login token
            if ($user->hasVerifiedEmail()) {
                $verified = true;
            }

            return response()->json([
                'message' => 'Logged in successfully!',
                'token' => $token,
                'user' => $user, // Return user info (e.g., name)
                'verified' => $verified,
                'logged_in' => 1,
            ]);
        }

        return response()->json(['message' => 'Invalid credentials'], 401);
    }

    // OTP
    public function sendOtp(Request $request)
    {
        // $user = User::where('email', $request->email)->first();
        $user = Auth::user(); // Get the authenticated user
        if ($user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Your email is already verified. Please log in.'], 400);
        }

        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
        $user->generateOtp();

        return response()->json(['message' => 'OTP sent successfully!']);
    }

    public function verifyOtp(Request $request)
    {
        // $user = User::where('email', $request->email)->first();
        $user = Auth::user(); // Get the authenticated user
        if ($user->hasVerifiedEmail()) {
            return response()->json(['verified' => false, 'message' => 'Your email is already verified. Please log in.'], 400);
        }

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        if (!$user || $user->otp !== $request->otp) {
            return response()->json(['message' => 'Invalid OTP'], 400);
        }

        if (now()->greaterThan($user->otp_expires_at)) {
            return response()->json(['message' => 'OTP expired'], 400);
        }

        // OTP is valid, verify email
        $user->email_verified_at = now();
        // OTP is correct, clear it
        $user->otp = null; // Clear OTP after successful verification
        $user->otp_expires_at = null;
        $user->save();

        return response()->json(['message' => 'Email verified successfully', 'verified' => 1]);
    }

    public function logout()
    {
        Auth::user()->tokens()->delete(); // Delete all tokens to log out
        return response()->json(['message' => 'Logged out successfully!']);
    }

    // this is to ensure that the user is always having a valid token while they're logged in
    public function verifyToken(Request $request)
    {
        $token = $request->token;
        $user = Auth::guard('sanctum')->user();

        if ($user) {
            return response()->json(['isAuthenticated' => true]);
        } else {
            return response()->json(['isAuthenticated' => false], 401);
        }
    }

    // custom function send password reset link
    public function forgotPassword(Request $request)
    {
        // Validate the email
        $request->validate(['email' => 'required|email']);

        // Create an instance of PasswordController
        $passwordController = new PasswordController();

        // Call the reset method and pass the request
        $response = $passwordController->sendResetLink($request);

        return $response;
    }

    // custom function reset password
    public function resetPassword(Request $request)
    {
        // Validate the required fields
        $request->validate([
            'token' => 'required|string',
            'email' => 'required|email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        // Create an instance of PasswordController
        $passwordController = new PasswordController();

        // Call the reset method and pass the request
        $response = $passwordController->reset($request);

        // Return the response directly (since reset() already returns a response)
        return $response;
    }

    // Original Forgot Password (Send Reset Link)
    // public function forgotPassword(Request $request)
    // {
    //     // Validate the email
    //     $request->validate(['email' => 'required|email']);

    //     // Send password reset link
    //     $response = Password::sendResetLink($request->only('email'));

    //     // Return response based on result
    //     if ($response == Password::RESET_LINK_SENT) {
    //         return response()->json(['message' => 'Password reset link sent successfully!']);
    //     } else {
    //         return response()->json(['message' => 'Unable to send password reset link.'], 400);
    //     }
    // }


    // Original Reset Password (Update Password)
    // public function resetPassword(Request $request)
    // {
    //     // Validate the required fields
    //     $request->validate([
    //         'token' => 'required|string',
    //         'email' => 'required|email',
    //         'password' => 'required|string|min:8|confirmed',
    //     ]);

    //     // Attempt to reset the password
    //     $response = Password::reset(
    //         $request->only('email', 'password', 'password_confirmation', 'token'),
    //         function ($user, $password) {
    //             $user->forceFill([
    //                 'password' => Hash::make($password),
    //             ])->save();
    //         }
    //     );

    //     // Return response based on result
    //     if ($response == Password::PASSWORD_RESET) {
    //         return response()->json(['message' => 'Password reset successfully!']);
    //     } else {
    //         return response()->json(['message' => 'Failed to reset password.'], 400);
    //     }
    // }
}