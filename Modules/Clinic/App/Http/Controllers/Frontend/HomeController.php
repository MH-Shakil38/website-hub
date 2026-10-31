<?php

namespace Modules\Clinic\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;

class HomeController extends Controller
{
    public function index()
    {
        return view('clinic::frontend.home');
    }

    public function contact()
    {
        return view('clinic::frontend.home');
    }

    public function page(string $page)
    {
        return view('clinic::frontend.home', compact('page'));
    }

    public function asset(string $type, string $file)
    {
        $path = module_path('Clinic', "Public/assets/{$type}/" . basename($file));
        abort_unless(is_file($path), 404);

        $mime = $type === 'css' ? 'text/css' : 'application/javascript';

        return response()->file($path, [
            'Content-Type' => $mime . '; charset=UTF-8',
            'Cache-Control' => 'public, max-age=86400',
        ]);
    }
}
