<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Answer;
use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\HandlesAuthorization;

class AnswerPolicy
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

    public function update(User $user, Answer $answer) {
        return $user->id === $answer->author->id
            ? Response::allow()
            : Response::deny('User does not have a permission to update answer.');
    }

    public function delete(User $user, Answer $answer) {
        return $user->id === $answer->author->id
            ? Response::allow()
            : Response::deny('User does not have a permission to delete answer.');
    }
}
