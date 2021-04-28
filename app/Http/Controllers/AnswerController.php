<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\AnswerPostRequest;
use App\Http\Requests\AnswerPutRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use App\Models\Answer;
use App\Models\Question;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use App\Events\AnswerCreated;

class AnswerController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function store(AnswerPostRequest $request, $id) {
        // when there is no question with given id
        // return message with status code 404
        try {
            $question = Question::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Question with id ' . $id . ' does not exist.'], 404);
        }

        DB::transaction(function() use ($request, &$answer, $id) {
            // save answer to DB
            $answer = Answer::create([
                            'body' => $request->body,
                            'question_id' => $id,
                            'user_id' => auth()->id(),
                        ]);

            // save images to DB
            foreach((array)$request->file('images') as $uploadedImage) {
                $imageId = $this->imageService->store($uploadedImage);
                // save images-answer relationship in pivot table
                $answer->images()->attach($imageId);
            }
        });

        // generate answer created event to notify question author
        AnswerCreated::dispatch($answer);

        return response()->json(['id' => $answer->id], 201);
    }

    public function edit($id) {
        $answer = Answer::select('id', 'body')
            ->where('id', $id)
            ->first();

        if(!$answer) {
            return response()->json(['message' => 'Answer with id ' . $id . ' does not exist.'], 404);
        }

        return response()->json($answer, 200);
    }

    public function update(Request $request, $id) {

        // input validation
        $request->validate([
            'body' => 'required|string',
        ]);

        // when there is no question with given id
        // return message with status code 404
        try {
            $answer = Answer::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Answer with id ' . $id . ' does not exist.'], 404);
        }

        $this->authorize('update', $answer);

        $answer->body = $request->body;

        // save image with updated fields
        $answer->save();

        return response()->json(null, 204);
    }

    public function destroy($id) {
        // check if answer exists
        try {
            $answer = Answer::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Answer with id ' . $id . ' does not exist.'], 404);
        }

        $this->authorize('delete', $answer);

        DB::transaction(function() use ($answer) {
            // delete answer's images
            $answer->images()->delete();
            
            // delete answer
            $answer->delete();
        });

        return response()->json(null, 204);
    }
}
