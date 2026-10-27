<?php

namespace Modules\RealEstate\App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use Modules\RealEstate\App\Models\Property;

class HomeController extends Controller
{
    public function index()
    {
        $properties = Property::where('is_active', true)->latest()->get();

        return view('realestate::frontend.home', compact('properties'));
    }

    public function properties()
    {
        $properties = Property::where('is_active', true)->latest()->get();

        return view('realestate::frontend.properties', compact('properties'));
    }

    public function contact()
    {
        return view('realestate::frontend.contact');
    }
}
