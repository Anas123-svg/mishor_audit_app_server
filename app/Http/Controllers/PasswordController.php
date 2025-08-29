<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class PasswordController extends Controller
{
    /**
     * Handle forgot password request.
     */
    public function sendResetCode(Request $request)
    {
        $request->validate(['email' => 'required|email']);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Email not found'], 404);
        }

        $resetCode = rand(100000, 999999); // Generate a 6-digit reset code

        // Save the reset code to the database
        DB::table('password_resets')->updateOrInsert(
            ['email' => $request->email],
            ['token' => $resetCode, 'created_at' => now()]
        );

        // Send email to the user
        Mail::send('emails.reset_code', ['code' => $resetCode], function ($message) use ($user) {
            $message->to($user->email);
            $message->subject('Password Reset Code');
        });

        return response()->json(['message' => 'Reset code sent successfully']);
    }

    /**
     * Handle reset password request.
     */
    public function resetPassword(Request $request)
    {
        // Validate the incoming data
        $request->validate([
            'reset_code' => 'required|numeric',
            'password' => 'required|min:8|confirmed',
        ]);
    
        // Check if the reset code exists in the password_resets table
        $resetRecord = DB::table('password_resets')
            ->where('token', $request->reset_code)
            ->first();
    
        // If no reset record found, return an error
        if (!$resetRecord) {
            return response()->json(['message' => 'Invalid reset code'], 400);
        }
    
        // Check if the reset code has expired (valid for 15 minutes)
        if (now()->diffInMinutes($resetRecord->created_at) > 15) {
            return response()->json(['message' => 'Reset code expired'], 400);
        }
    
        // Retrieve the user associated with the email stored in the reset record
        $user = User::where('email', $resetRecord->email)->first();
    
        if (!$user) {
            return response()->json(['message' => 'User not found'], 404);
        }
    
        $user->password = Hash::make($request->password);
        $user->save();
    
        DB::table('password_resets')->where('email', $resetRecord->email)->delete();
    
        return response()->json(['message' => 'Password reset successfully']);
    }
    
}
