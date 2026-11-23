<?php

namespace Modules\IvfCenter\App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Modules\IvfCenter\App\Models\Consultation;
use Modules\IvfCenter\App\Models\ModuleSetting;

class AdminController extends Controller
{
    public function consultations()
    {
        $consultations = Consultation::latest()->paginate(15);
        return view('ivfcenter::admin.consultations', compact('consultations'));
    }

    public function destroyConsultation($id)
    {
        Consultation::findOrFail($id)->delete();
        return back()->with('success', 'Consultation record deleted successfully.');
    }

    public function settings()
    {
        $settings = ModuleSetting::pluck('value', 'key')->toArray();
        return view('ivfcenter::admin.settings', compact('settings'));
    }

    public function saveSettings(Request $request)
    {
        $data = $request->except('_token');
        foreach ($data as $key => $value) {
            ModuleSetting::updateOrCreate(
                ['key' => $key],
                ['value' => $value, 'group' => 'theme']
            );
        }

        return back()->with('success', 'Theme settings updated successfully.');
    }
}
