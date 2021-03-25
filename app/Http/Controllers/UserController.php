<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Question;
use App\Models\Answer;

class UserController extends Controller
{
    public function show($id) {
        try {
            $user = User::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
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

        // getting ids of questions from the user
        $questionsIds = Question::with('author')
            ->whereHas('author', function($query) use($id) {
                            $query->where('id', $id);
                        })
            ->pluck('id');

        // getting requested page of list of questions from the user
        $questions = Question::with([
                'tags', 
            ])
            ->select('id', 'title', 'created_at')
            ->withCount('answers')
            ->whereIn('id', $questionsIds)
            ->orderBy('created_at', 'desc')
            ->get();

        $count = $questionsIds->count();

        return response()->json([
                'id' => $user->id,
                'name' => $user->name, 
                'questions' => $questions
            ], 200);
    }

    public function indexAnswers($id) {
        try {
            $user = User::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'User with id ' . $id . ' does not exist.'], 404);
        }

        // getting ids of questions from the user
        $answersIds = Answer::with('author')
            ->whereHas('author', function($query) use($id) {
                            $query->where('id', $id);
                        })
            ->pluck('id');
        
        // getting list of answers from the user
        $answers = Answer::with([
                'question' => function($query) {
                    return $query->select('id', 'title');
                }
            ])
            ->whereIn('id', $answersIds)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json([
                'id' => $user->id,
                'name' => $user->name, 
                'answers' => $answers
            ], 200);
    }
}
