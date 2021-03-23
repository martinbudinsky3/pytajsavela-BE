<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Question;
use App\Models\User;
use App\Models\Tag;

class QuestionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $tagIds = Tag::pluck('id');
        $authorIds = User::pluck('id');

        for($i = 0; $i < 50; $i++) {
            $authorId = $authorIds->get(rand(0, $authorIds->count() - 1));
            $question = Question::create([
                'title' => 'Question title ' . $i,
                'body' => ' Question body ' . $i,
                'user_id' => $authorId
            ]);

            for($j = 0; $j < 3; $j++) {
                $tagIndex = ($i * $j) % $tagIds->count();
                $tagId = $tagIds->get($tagIndex);
                $question->tags()->attach($tagId);
            }
        }
    }
}
