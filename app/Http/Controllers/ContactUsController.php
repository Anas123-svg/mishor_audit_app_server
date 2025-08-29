<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;
use App\Models\Admin;

class ContactUsController extends Controller
{
    public function submitContactUs(Request $request)
    {
        // Validate the incoming request
        $request->validate([
            'email' => 'required|email',
            'message' => 'required|string',
            'phone' => 'required|string',
        ]);

        $userEmail = $request->input('email');
        $userMessage = $request->input('message');
        $userPhone = $request->input('phone');

        // Send an email to the user
        Mail::send([], [], function ($message) use ($userEmail) {
            $message->to($userEmail)
                ->subject('Your Request Has Been Received')
                ->text('Thank you for reaching out to us. We have received your request and will be in touch with you shortly.');
        });

        // Fetch all admin emails
        $adminEmails = Admin::all()->pluck('email')->toArray();

        // Debug admin emails
        if (empty($adminEmails)) {
            return response()->json(['error' => 'No admin emails found.'], 500);
        }

        // Log admin emails for debugging
        \Log::info('Admin Emails: ', $adminEmails);


        // Send an email to all admins
        Mail::send([], [], function ($message) use ($adminEmails, $userEmail, $userMessage, $userPhone) {
            $message->to($adminEmails)
                ->subject('New Contact Us Request')
                ->text("A new user has sent a message:\nEmail: $userEmail\nPhone: $userPhone\nMessage: $userMessage");
        });

        return response()->json([
            'message' => 'Your request has been submitted successfully.'
        ], 200);
    }
}
