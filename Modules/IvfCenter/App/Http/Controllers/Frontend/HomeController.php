<?php

namespace Modules\IvfCenter\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\IvfCenter\App\Models\Consultation;

class HomeController extends Controller
{
    public function index()
    {
        return view('ivfcenter::frontend.home');
    }

    public function bookConsultation(Request $request)
    {
        $validated = $request->validate([
            'full_name' => 'required|string|max:150',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:50',
            'service_interest' => 'nullable|string|max:100',
            'preferred_date' => 'nullable|date',
            'message' => 'nullable|string|max:1000',
        ]);

        Consultation::create($validated);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your consultation request has been received. Our medical team will contact you shortly.',
            ]);
        }

        return back()->with('success', 'Thank you! Your consultation request has been received. Our medical team will contact you shortly.');
    }

    public function media(string $folder, string $file)
    {
        $path = module_path('IvfCenter', 'Public/uploads/' . basename($folder) . '/' . basename($file));

        if (!is_file($path)) {
            abort(404);
        }

        return response()->file($path, ['Cache-Control' => 'public, max-age=604800']);
    }

    public function asset(string $type, string $file)
    {
        if (!in_array($type, ['css', 'js'], true)) {
            abort(404);
        }

        $cleanFile = basename($file);
        $path = module_path('IvfCenter', 'Public/assets/' . $type . '/' . $cleanFile);

        if (!file_exists($path)) {
            abort(404, "Asset file not found.");
        }

        $contentType = $type === 'css' ? 'text/css; charset=UTF-8' : 'application/javascript; charset=UTF-8';

        return response(file_get_contents($path), 200, [
            'Content-Type' => $contentType,
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
