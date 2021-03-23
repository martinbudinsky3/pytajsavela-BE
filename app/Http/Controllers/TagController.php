<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Models\Question;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class TagController extends Controller
{
    public function index() {
        $recordsPerPage = 18;
        $page = request('page', 1);
        $offset = ($page - 1) * $recordsPerPage;
        $search = request('search', '');

        $tags = Tag::withCount('questions')
            ->where('name', 'like', $search.'%')
            ->orderBy('name', 'asc')
            ->skip($offset)
            ->take($recordsPerPage)
            ->get();

        $count = $search == '' ? Tag::count() : Tag::where('name', 'like', $search.'%')->count();

        return response()->json(['count' => $count, 'tags' => $tags], 200);

    }

    public function indexQuestions($id) {
        try {
            $tag = Tag::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Tag with id ' . $id . ' does not exist.'], 404);
        }

        $recordsPerPage = 10;
        $page = request('page', 1);
        $offset = ($page - 1) * $recordsPerPage;

        // getting ids of questions that have requested tag
        $questionsIds = Question::with('tags')
            ->whereHas('tags', function($query) use($id) {
                            $query->where('tags.id', $id);
                        })
            ->pluck('id');

        // getting requested page of list of questions that have requested tag with their info
        $questions = Question::with([
                'tags', 
                'author' => function($query) {
                    return $query->select('id', 'name');
                }
            ])
            ->withCount('answers')
            ->whereIn('id', $questionsIds)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($recordsPerPage)
            ->get();

        $count = $questionsIds->count();

        return response()->json(['count' => $count, 'questions' => $questions], 200);
    }
}
