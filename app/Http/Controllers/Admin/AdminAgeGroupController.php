<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreAgeGroupRequest;
use App\Models\AgeGroup;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminAgeGroupController extends Controller
{
    public function index(): View
    {
        return view('admin.age-groups.index');
    }

    public function store(StoreAgeGroupRequest $request): RedirectResponse
    {
        AgeGroup::create($request->validated());

        return back()->with('success', 'تم إنشاء الفئة العمرية بنجاح');
    }

    public function update(StoreAgeGroupRequest $request, AgeGroup $ageGroup): RedirectResponse
    {
        $ageGroup->update($request->validated());

        return back()->with('success', 'تم تحديث الفئة العمرية بنجاح');
    }
}
