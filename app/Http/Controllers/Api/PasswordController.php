<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Support\Facades\Log;

class PasswordController extends Controller
{


    public function sendResetLink(Request $request)
    {
        // 1. Validate email
        // $request->validate(['email' => 'required|email']);

        $user = DB::table('users')->where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'We can\'t find a user with that email address.'], 404);
        }

        // 2. Generate token
        $token = Str::random(64);

        // 3. Store token (overwrite any existing one)
        DB::table('password_reset_tokens')->updateOrInsert(
            ['email' => $request->email],
            [
                'token' => hash('sha256', $token),
                'created_at' => Carbon::now()
            ]
        );

        // 4. Build reset link
        $resetLink = url("http://localhost:3000/reset-password/$token?email=" . urlencode($request->email));

        try {
            // 5. Send custom email
            Mail::send('emails.password-reset', [
                'name' => $user->name ?? 'User',
                'resetLink' => $resetLink
            ], function ($message) use ($request) {
                $message->from('no-reply@movies.com', 'Movies');
                $message->to($request->email);
                $message->subject('Reset Your Password');
            });

            return response()->json(['message' => 'Password reset link sent successfully!']);
        } catch (\Exception $e) {
            // Log::error('Password reset email failed: ' . $e->getMessage());

            return response()->json([
                'message' => 'Unable to send password reset link.',
                // Failed to send password reset email.
            ], 500);
        }
    }

    public function reset(Request $request)
    {
        // 2. Manually check if passwords match
        if ($request->password !== $request->password_confirmation) {
            return response()->json(['message' => 'Password and confirmation do not match.'], 400);
        }

        // 2. Retrieve the token record
        $record = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->where('token', hash('sha256', $request->token))
            ->first();

        if (!$record) {
            return response()->json(['message' => 'Invalid or expired token.'], 400);
        }

        // 3. Check if token is expired (after 60 minutes)
        if (Carbon::parse($record->created_at)->addMinutes(60)->isPast()) {
            return response()->json(['message' => 'This password reset link has expired.'], 400);
        }

        try {
            // 4. Update the user's password
            DB::table('users')
                ->where('email', $request->email)
                ->update([
                    'password' => Hash::make($request->password)
                ]);

            // 5. Delete the used token
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();

            return response()->json(['message' => 'Your password has been reset successfully.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Failed to reset password.'], 500);
        }
    }
}