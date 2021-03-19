<?php

namespace App\Http\Controllers;

use App\Models\Tag;
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

        $tags = DB::table('tags')
             ->select('tags.id', 'name', DB::raw('count(question_tags.tag_id) as questions_count'))
             ->leftJoin('question_tags', 'tags.id', '=', 'question_tags.tag_id')
             ->where('name', 'like', $search.'%')
             ->groupBy('tags.id')
             ->orderBy('tags.id', 'desc')
             ->offset($offset)
             ->limit($recordsPerPage)
             ->get();

        $count = $search == '' ? Tag::count() : Tag::where('name', 'like', $search.'%')->count();

        return response()->json(['count' => $count, 'tags' => $tags], 200);

    }

    public function indexQuestions($id) {
        
    }
}
