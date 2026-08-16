<?php

namespace App\Http\Controllers;

use App\Models\Article;
use App\Models\Course;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    public function __invoke(Request $request): View
    {
        $term = trim((string) $request->input('q', ''));

        $courses = collect();
        $articles = collect();

        if ($term !== '') {
            $courses = Course::search($term)->where('is_published', true)->get();
            $articles = Article::search($term)->where('is_published', true)->get();
        }

        return view('search.results', compact('term', 'courses', 'articles'));
    }
}
