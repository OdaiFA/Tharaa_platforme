<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Models\Course;
use App\Services\EnrollmentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ApiCourseController extends Controller
{
    public function __construct(private readonly EnrollmentService $enrollments) {}

    public function index(Request $request): AnonymousResourceCollection
    {
        $courses = Course::query()
            ->published()
            ->with('ageGroups')
            ->withCount(['modules', 'enrollments'])
            ->when($request->input('level'), fn ($q, $level) => $q->forLevel($level))
            ->when($request->input('age_group_id'), fn ($q, $id) => $q->forAgeGroup($id))
            ->when($request->input('search'), fn ($q, $term) => $q->where('title', 'like', "%{$term}%"))
            ->latest()
            ->paginate(12);

        return CourseResource::collection($courses);
    }

    public function recommended(Request $request): AnonymousResourceCollection
    {
        $ageGroupId = $request->user()?->age_group_id;

        $courses = $ageGroupId
            ? Course::published()->forAgeGroup($ageGroupId)->latest()->take(6)->get()
            : collect();

        return CourseResource::collection($courses);
    }

    public function show(Request $request, Course $course): JsonResponse
    {
        if (! $course->is_published && ! $request->user()?->isAdmin()) {
            abort(404);
        }

        $course->load('ageGroups', 'modules.lessons.quiz');
        $enrollment = $request->user()
            ? $request->user()->enrollments()->where('course_id', $course->id)->first()
            : null;

        return response()->json([
            'course' => new CourseResource($course),
            'enrollment' => $enrollment,
        ]);
    }

    public function enroll(Request $request, Course $course): JsonResponse
    {
        if (! $course->is_published) {
            abort(404);
        }

        $enrollment = $this->enrollments->enroll($request->user(), $course);

        return response()->json([
            'message' => 'تم التسجيل في الدورة بنجاح',
            'enrollment' => $enrollment,
        ], 201);
    }
}
