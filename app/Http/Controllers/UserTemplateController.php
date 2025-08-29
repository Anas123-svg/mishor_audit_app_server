<?php

namespace App\Http\Controllers;

use App\Models\UserTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserTemplateController extends Controller
{
    /**
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $userTemplates = UserTemplate::with(['client', 'template', 'user'])->get();
        return response()->json($userTemplates);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'template_id' => 'required|exists:templates,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $userTemplate = UserTemplate::create($request->only(['client_id', 'template_id', 'user_id']));
        return response()->json($userTemplate, 201);
    }

    /**
     * @param  \App\Models\UserTemplate  $userTemplate
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $userTemplate = UserTemplate::with(['client', 'template', 'user'])->findOrFail($id);
        return response()->json($userTemplate);
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\UserTemplate  $userTemplate
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $userTemplate = UserTemplate::findOrFail($id);

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'template_id' => 'required|exists:templates,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $userTemplate->update($request->only(['client_id', 'template_id', 'user_id']));
        return response()->json($userTemplate);
    }

    /**
     * @param  \App\Models\UserTemplate  $userTemplate
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $userTemplate = UserTemplate::findOrFail($id);
        $userTemplate->delete();

        return response()->json(['message' => 'UserTemplate deleted successfully']);
    }

    public function getTemplatesByUser($userId)
    {
        $userTemplates = UserTemplate::with('template')
            ->where('user_id', $userId)
            ->get();

        return response()->json($userTemplates);
    }
    public function getTemplatesByAuthenticatedUser()
    {
        $userId = auth()->user()->id;

        Log::info('Fetching templates for authenticated user', ['user_id' => $userId]);

        try {
            $userTemplates = UserTemplate::with('template')
                ->where('user_id', $userId)
                ->get();

            Log::info('Templates fetched successfully', ['count' => $userTemplates->count()]);

            return response()->json($userTemplates);
        } catch (\Exception $e) {
            Log::error('Error fetching templates for authenticated user', [
                'user_id' => $userId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }


}
