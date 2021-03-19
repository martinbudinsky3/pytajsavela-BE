<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Question;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class QuestionPolicy
{
    use HandlesAuthorization;

    /**
     * Create a new policy instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function update(User $user, Question $question) {
        return $user->id === $question->author->id
            ? Response::allow()
            : Response::deny('User does not have a permission to update question.');
    }

    public function delete(User $user, Question $question) {
        return $user->id === $question->author->id
            ? Response::allow()
            : Response::deny('User does not have a permission to delete question.');
    }
}
