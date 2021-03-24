<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Answer;

class UserController extends Controller
{
    public function show($id) {

        $user = User::with([
            'questions',
            'answers'
        ])
        ->where('id', $id)
        ->first();

        if(!$user) {
            return response()->json(['message' => 'User with id ' . $id . ' does not exist.'], 404);
        }

        return response()->json($user, 200);
    }

    public function indexQuestions($id) {
        try {
            $user = User::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'User with id ' . $id . ' does not exist.'], 404);
        }

        $recordsPerPage = 10;
        $page = request('page', 1);
        $offset = ($page - 1) * $recordsPerPage;

        // getting ids of questions from the user
        $questionsIds = Question::with('author')
            ->whereHas('author', function($query) use($id) {
                            $query->where('id', $id);
                        })
            ->pluck('id');

        // getting requested page of list of questions from the user
        $questions = Question::with([
                'tags', 
                'author' => function($query) {
                    return $query->select('id', 'name');
                },
                'answers'
            ])
            ->select('id', 'title', 'created_at', 'user_id')
            ->withCount('answers')
            ->whereIn('id', $questionsIds)
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($recordsPerPage)
            ->get();

        $count = $questionsIds->count();

        return response()->json(['count' => $count, 'questions' => $questions], 200);
    }

    public function indexAnswers($id) {
        try {
            $user = User::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'User with id ' . $id . ' does not exist.'], 404);
        }

        $recordsPerPage = 10;
        $page = request('page', 1);
        $offset = ($page - 1) * $recordsPerPage;

        // getting ids of questions from the user
        $answersIds = Answer::with('author')
            ->whereHas('author', function($query) use($id) {
                            $query->where('id', $id);
                        })
            ->pluck('id');
        
        // getting requested page of list of questions from the user
        $answers = Answer::with([
            'author' => function($query) {
                return $query->select('id', 'name');
            },
            'question'
        ])
        ->select('id', 'body', 'created_at', 'user_id', 'question_id')
        ->whereIn('id', $answersIds)
        ->orderBy('created_at', 'desc')
        ->skip($offset)
        ->take($recordsPerPage)
        ->get();

        $count = $answersIds->count();

        return response()->json(['user' => $user, 'count' => $count, 'answers' => $answers], 200);
    }
}
