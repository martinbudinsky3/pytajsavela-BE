<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Answer;
use App\Models\Question;
use App\Models\User;

class AnswerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $authorIds = User::pluck('id');
        $questionIds = Question::pluck('id');

        for($i = 0; $i < 50; $i++) {
            $authorId = $authorIds->get(rand(0, $authorIds->count() - 1));
            $questionId = $questionIds->get(rand(0, $questionIds->count() - 1));

            $answer = Answer::create([
                'body' => ' Answer body ' . $i,
                'user_id' => $authorId,
                'question_id' => $questionId
            ]);
        }
    }
}