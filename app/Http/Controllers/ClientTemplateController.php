<?php
namespace App\Http\Controllers;
use App\Models\ClientTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
class ClientTemplateController extends Controller
{
    public function index()
    {
        $clientTemplates = ClientTemplate::with(['client', 'template:id,name,description'])->get();
        return response()->json($clientTemplates);
    }

    public function store(Request $request)
    {
        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'template_id' => 'required|exists:templates,id',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);
    
        $existingClientTemplate = ClientTemplate::where('client_id', $request->client_id)
                                                ->where('template_id', $request->template_id)
                                                ->first();
    
        if ($existingClientTemplate) {
            return response()->json([
                'message' => 'This template is already assigned to the client.'
            ], 409); 
        }
    
        $clientTemplate = ClientTemplate::create($request->only(['client_id', 'template_id', 'status']));
    
        return response()->json($clientTemplate, 201);
    }
    
    public function show($id)
    {
        $clientTemplate = ClientTemplate::with(['client', 'template:id,name,description'])->findOrFail($id);
        return response()->json($clientTemplate);
    }

    public function update(Request $request, $id)
    {
        $clientTemplate = ClientTemplate::findOrFail($id);

        $request->validate([
            'client_id' => 'required|exists:clients,id',
            'template_id' => 'required|exists:templates,id',
            'status' => 'nullable|in:pending,approved,rejected',
        ]);

        $clientTemplate->update($request->only(['client_id', 'template_id','status']));
        return response()->json($clientTemplate);
    }

    public function destroy($id)
    {
        $clientTemplate = ClientTemplate::findOrFail($id);
        $clientTemplate->delete();

        return response()->json(['message' => 'ClientTemplate deleted successfully']);
    }


    public function getTemplatesByClient($clientId)
    {
        $clientTemplates = ClientTemplate::with('template:id,name,description')
            ->where('client_id', $clientId)
            ->get();

        return response()->json($clientTemplates);
    }

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getTemplatesByAuthenticatedClient()
    {
        $clientId = auth()->user()->id;

        Log::info('Fetching templates for authenticated client', ['client_id' => $clientId]);

        try {
            $clientTemplates = ClientTemplate::with('template:id,name,description')
                ->where('client_id', $clientId)
                ->get();

            Log::info('Templates fetched successfully', ['count' => $clientTemplates->count()]);

            return response()->json($clientTemplates);
        } catch (\Exception $e) {
            Log::error('Error fetching templates for authenticated client', [
                'client_id' => $clientId,
                'error' => $e->getMessage()
            ]);

            return response()->json(['error' => 'Something went wrong'], 500);
        }
    }
}
