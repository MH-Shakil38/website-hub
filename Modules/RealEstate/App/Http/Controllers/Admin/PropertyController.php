<?php

namespace Modules\RealEstate\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\RealEstate\App\Models\Property;

class PropertyController extends Controller
{
    public function index()
    {
        $properties = Property::latest()->get();

        return view('realestate::admin.properties.index', compact('properties'));
    }

    public function create()
    {
        return view('realestate::admin.properties.create');
    }

    public function inquiries()
    {
        return view('realestate::admin.inquiries.index');
    }

    public function themeSettings()
    {
        return view('realestate::admin.theme.settings');
    }
}
