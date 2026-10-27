<?php

namespace Modules\Interior\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Services\ModuleThemeDataService;

class HomeController extends Controller
{
    public function __construct(
        protected ModuleThemeDataService $themeDataService
    ) {}

    public function index()
    {
        $t = $this->themeDataService->getFrontendData('Interior');
        return view('interior::frontend.home', compact('t'));
    }

    public function portfolio()
    {
        $t = $this->themeDataService->getFrontendData('Interior');
        return view('interior::frontend.home', compact('t'));
    }

    public function contact()
    {
        $t = $this->themeDataService->getFrontendData('Interior');
        return view('interior::frontend.home', compact('t'));
    }
}
