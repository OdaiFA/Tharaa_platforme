<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreModuleRequest;
use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class AdminModuleController extends Controller
{
    public function index(Course $course): View
    {
        return view('admin.modules.index', compact('course'));
    }

    public function store(StoreModuleRequest $request): RedirectResponse
    {
        Module::create($request->validated());

        return back()->with('success', 'تم إنشاء الوحدة بنجاح');
    }

    public function update(StoreModuleRequest $request, Module $module): RedirectResponse
    {
        $module->update($request->validated());

        return back()->with('success', 'تم تحديث الوحدة بنجاح');
    }

    public function destroy(Module $module): RedirectResponse
    {
        $courseId = $module->course_id;
        $module->delete();

        return back()->with('success', 'تم حذف الوحدة بنجاح');
    }
}
