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

class AnswerController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function show($id) {

        $answer = Answer::with([
                'author' => function($query) {
                    return $query->select('id', 'name');
                }, 
                'question.tags', 
                'question.images',
                'question.author' => function($query) {
                    return $query->select('id', 'name');
                }, 
                'images'
            ])
            ->where('id', $id)
            ->first();

        if(!$answer) {
            return response()->json(['message' => 'Answer with id ' . $id . ' does not exist.'], 404);
        }

        return response()->json($answer, 200);
    }

    public function store(AnswerPostRequest $request, $id) {
        // when there is no question with given id
        // return message with status code 404
        try {
            $question = Question::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Question with id ' . $id . ' does not exist.'], 404);
        }

        // if no body was given in the request 
        // return message with status code 422
        if (empty($request->body)) {
            return response()->json(['message' => 'Request does not pass valiation.'], 422);
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

    public function update(AnswerPutRequest $request, $id) {

        // if no body was given in the request 
        // return message with status code 422
        if (empty($request->body)) {
            return response()->json(['message' => 'Request does not pass valiation.'], 422);
        }

        // when there is no question with given id
        // return message with status code 404
        try {
            $answer = Answer::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Answer with id ' . $id . ' does not exist.'], 404);
        }

        $this->authorize('update', $answer);

        $answer->body = $request->body;

        DB::transaction(function() use ($request, $answer) {
            // save image with updated fields
            $answer->save();

            // delete images
            Image::destroy($request->deleted_images);
            // save new images to DB
            foreach((array)$request->file('images') as $uploadedImage) {
                $imageId = $this->imageService->store($uploadedImage);
                // save images-asnwer relationship in pivot table
                $answer->images()->attach($imageId);
            }
        });

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
            Image::destroy($answer->images()->pluck('image_id'));

            // delete answer
            $answer->delete();
        });

        return response()->json(null, 204);
    }
}
