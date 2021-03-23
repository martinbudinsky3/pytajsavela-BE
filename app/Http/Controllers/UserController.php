<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;

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
        
    }

    public function indexAnswers($id) {
        
    }
}
