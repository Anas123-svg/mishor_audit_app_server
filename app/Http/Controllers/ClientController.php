<?php

namespace App\Http\Controllers;

use App\Models\Client;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
class ClientController extends Controller
{
    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:clients',
            'password' => 'required|string|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        $profileImage = $request->profile_image ? $request->profile_image : 'https://res.cloudinary.com/dewqsghdi/image/upload/v1731457888/5045878_xmzjj4.png';
    
        $client = Client::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => $request->password,
            'profile_image' => $profileImage
        ]);
    
        return response()->json(['message' => 'You are registered successfully, Wait for admin approval'], 201);
    }
    public function showByToken(Request $request)

    {
        Log::info('API endpoint hit');

        Log::info('Function called', ['token' => $request->header('Authorization')]);
    
        $client = $request->user();  
    
        if (!$client) {
            Log::warning('Client not found or authentication failed', [
                'timestamp' => now(),
            ]);
            return response()->json(['error' => 'Client not authenticated'], 401);
        }
    
        Log::info('Client retrieved by token', [
            'client_id' => $client->id,
            'timestamp' => now(),
        ]);
    
        return response()->json($client);
    }
        
    public function login(Request $request)
    {
        Log::info(message: 'login user:');

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Client::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
            if($user->is_verified == false){
                return response()->json(['error' => 'Your account is not verified yet'], 403);
            }
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'token' => $token,
                'user' => $user,
            ], 200);
        }

        return response()->json(['error' => 'Unauthorized'], 401);
    }
    public function logout()
    {
        Auth::user()->tokens()->delete();

        return response()->json(['message' => 'Logged out successfully'], 200);
    }

    public function index()
    {
        $clients = Client::withCount('users')->get();
        
        return response()->json($clients);
    }
    

    public function show($id)
    {
        // Fetch client with relationships and handle errors if not found
        $client = Client::with(['users', 'assessments', 'ClientTemplate.template'])->find($id);
    
        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }
    
        $client->ClientTemplate = $client->ClientTemplate->map(function ($clientTemplate) {
            return [
                'id' => $clientTemplate->id,
                'client_id' => $clientTemplate->client_id,
                'template_id' => $clientTemplate->template_id,
                'created_at' => $clientTemplate->created_at,
                'updated_at' => $clientTemplate->updated_at,
                'status' => $clientTemplate->status,
                'template_name' => $clientTemplate->template->name,
                'description' => $clientTemplate->template->description,
            ];
        });
    
        return response()->json($client);
    }
    
    
    

    public function update(Request $request, $id)
    {
        $client = Client::find($id);

        if (!$client) {
            return response()->json(['error' => 'Client not found'], 404);
        }

        $client->update($request->all());

        return response()->json(['message' => 'Client updated successfully', 'client' => $client]);
    }


    public function resetPassword(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'old_password' => 'required|string|min:8',
            'new_password' => 'required|string|min:8|confirmed',
        ]);
    
        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }
    
        $client = Auth::user();
    
        if (!$client) {
            return response()->json(['error' => 'Admin not found'], 404);
        }
    
        if (!Hash::check($request->old_password, $client->password)) {
            return response()->json(['error' => 'Old password is incorrect'], 400);
        }
    
        if ($request->old_password === $request->new_password) {
            return response()->json(['error' => 'New password cannot be the same as the old password'], 400);
        }
        try{
            $client->password = $request->new_password;
            $client->save();
        }
        catch(\Exception $e){
            return response()->json(['error' => 'An error occurred while updating the password'], 400);
        }    
        return response()->json(['message' => 'Password updated successfully'], 200);
    }

    public function verify($id)
{
    $client = Client::find($id);

    if (!$client) {
        return response()->json(['error' => 'Client not found'], 404);
    }

    $client->is_verified = true;
    $client->save();

    return response()->json(['message' => 'Client verified successfully', 'client' => $client]);
}

public function destroy($id)
{
    Log::info('Attempting to delete client with ID: ' . $id);

    $client = Client::find($id);

    if (!$client) {
        Log::warning('Client not found with ID: ' . $id);
        return response()->json(['error' => 'Client not found'], 404);
    }

    \DB::beginTransaction();

    try {
        Log::info('Deleting related users for client ID: ' . $id);
        $client->users()->delete();

        Log::info('Deleting related assessments for client ID: ' . $id);
        $client->assessments()->delete();

        Log::info('Deleting client ID: ' . $id);
        $client->delete();

        \DB::commit();

        Log::info('Client and related records deleted successfully for client ID: ' . $id);
        return response()->json(['message' => 'Client and all related records deleted successfully'], 200);
    } catch (\Exception $e) {
        \DB::rollback();
        Log::error('Error occurred while deleting client with ID: ' . $id, [
            'error_message' => $e->getMessage(),
            'stack_trace' => $e->getTraceAsString(),
        ]);

        return response()->json(['error' => 'An error occurred while deleting the client'], 500);
    }
}


    public function clientStatistics(Request $request)
    {
    
        $client = $request->user();  
    
        if (!$client) {
            Log::warning('Client not found or authentication failed', [
                'timestamp' => now(),
            ]);
            return response()->json(['error' => 'Client not authenticated'], 401);
        }

    
        $userCount = $client->users()->count();
        $templateCount = $client->ClientTemplate()->count();
        $assessmentCount = $client->assessments()->count();
        $recentUsers = $client->users()->latest()->take(5)->get();
    
        $assessments = $client->assessments()->get(['created_at'])->map(function ($assessment) {
            return [
                'created_at' => $assessment->created_at,
            ];
        });
    
        return response()->json([
            'total_users' => $userCount,
            'total_assigned_template' => $templateCount,
            'assessment_count' => $assessmentCount,
            'recent_users' => $recentUsers,
            'created_assessments' => $assessments,
        ]);
    }
    public function getAllUsers(Request $request)
{
    $client = $request->user();

    if (!$client) {
        return response()->json(['error' => 'Client not authenticated'], 401);
    }

    $users = $client->users()->get();

    return response()->json([
        'users' => $users
    ]);
}

}
