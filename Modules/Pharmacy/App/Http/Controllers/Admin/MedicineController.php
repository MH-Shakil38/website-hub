<?php

namespace Modules\Pharmacy\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Modules\Pharmacy\App\Models\Medicine;

class MedicineController extends Controller
{
    public function index()
    {
        $medicines = Medicine::latest()->get();

        return view('pharmacy::admin.medicines.index', compact('medicines'));
    }

    public function create()
    {
        return view('pharmacy::admin.medicines.create');
    }

    public function orders()
    {
        return view('pharmacy::admin.orders.index');
    }

    public function themeSettings()
    {
        return view('pharmacy::admin.theme.settings');
    }
}
