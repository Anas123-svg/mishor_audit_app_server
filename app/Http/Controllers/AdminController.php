<?php
namespace App\Http\Controllers;

use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use App\Models\Client;
use App\Models\Template;
use App\Models\Assessment;
use App\Models\User;

//admin
class AdminController extends Controller
{

    public function register(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:admins',
            'password' => 'required|string|min:8',
            'role' => 'required|in:admin,moderator,superAdmin'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $admin = Admin::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' =>$request->password, 
            'role' => $request->role,
            'profile_image' => $request->profile_image
        ]);
        $token = $admin->createToken('authToken')->plainTextToken;


        return response()->json(['token'=>$token,'message' => 'Admin registered successfully', 'admin' => $admin], 201);
    }


    public function login(Request $request)
    {
        Log::info(message: 'login user:');

        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        $user = Admin::where('email', $request->email)->first();

        if ($user && Hash::check($request->password, $user->password)) {
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
        $admins = Admin::all();
        return response()->json($admins);
    }

    public function show($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        return response()->json($admin);
    }

    public function showByToken(Request $request)
    {
        $admin = $request->user();
        return response()->json($admin);
    }
    public function update(Request $request, $id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        $admin->update($request->all());

        return response()->json(['message' => 'Admin updated successfully', 'admin' => $admin]);
    }

    public function destroy($id)
    {
        $admin = Admin::find($id);

        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }

        $admin->delete();

        return response()->json(['message' => 'Admin deleted successfully']);
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
    
        $admin = Auth::user();
    
        if (!$admin) {
            return response()->json(['error' => 'Admin not found'], 404);
        }
    
        if (!Hash::check($request->old_password, $admin->password)) {
            return response()->json(['error' => 'Old password is incorrect'], 400);
        }
    
        if ($request->old_password === $request->new_password) {
            return response()->json(['error' => 'New password cannot be the same as the old password'], 400);
        }
        try{
            $admin->password = $request->new_password;
            $admin->save();
        }
        catch(\Exception $e){
            return response()->json(['error' => 'An error occurred while updating the password'], 400);
        }    
        return response()->json(['message' => 'Password updated successfully'], 200);
    }


    public function getStatistics()
    {
        $totalClients = Client::count();
        $totalTemplates = Template::count();
        $totalUsers = User::count();
    
$createdAssessments = Assessment::where('complete_by_user', true)->get(['created_at']);
        $recentClients = Client::latest()->withCount('users')->take(5)->get();
    
        return response()->json([
            'total_clients' => $totalClients,
            'total_templates' => $totalTemplates,
            'total_users' => $totalUsers,
            'created_assessments' => $createdAssessments,
            'recent_clients' => $recentClients
        ], 200);
    }
    
    

    
}
