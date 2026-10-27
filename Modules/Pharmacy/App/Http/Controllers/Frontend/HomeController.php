<?php

namespace Modules\Pharmacy\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\Pharmacy\App\Models\Medicine;

class HomeController extends Controller
{
    public function index()
    {
        $medicines = Medicine::where('is_active', true)->latest()->get();

        return view('pharmacy::frontend.home', compact('medicines'));
    }

    public function catalog()
    {
        $medicines = Medicine::where('is_active', true)->latest()->get();

        return view('pharmacy::frontend.catalog', compact('medicines'));
    }

    public function contact()
    {
        return view('pharmacy::frontend.contact');
    }
}
