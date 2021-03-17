<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\QuestionPostRequest;
use App\Http\Requests\QuestionPutRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use App\Models\Question;
use App\Services\ImageService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    protected $imageService;

    public function __construct(ImageService $imageService)
    {
        $this->imageService = $imageService;
    }

    public function index() {

    }

    public function show($id) {

    }

    public function store(QuestionPostRequest $request) {
        DB::transaction(function() use ($request, &$question) {
            // save question to DB
            $question = Question::create([
                            'title' => $request->title,
                            'body' => $request->body,
                            'user_id' => auth()->id()
                        ]);

            // save tags-question relationship in pivot table
            $question->tags()->attach($request->tags);

            // save images to DB
            $imagesIds = collect([]);
            foreach($request->file('images') as $uploadedImage) {
                $imageId = $this->imageService->store($uploadedImage);
                $imagesIds->push($imageId);
            }

            // save images-question relationship in pivot table
            $question->images()->attach($imagesIds);
        });

        return response()->json(['id' => $question->id], 201);
    }

    public function edit($id) {

    }

    public function update(QuestionPutRequest $request, $id) {
        try {
            $question = Question::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Question with id ' . $id . ' does not exist.'], 404);
        }

        $question->title = $request->title;
        $question->body = $request->body;

        DB::transaction(function() use ($request, $question) {
            // save image with updated fields
            $question->save();

            $question->tags()->detach($request->deleted_tags);
            $question->tags()->attach($request->tags);

            Image::destroy($request->deleted_images);

            // save images to DB
            $imagesIds = collect([]);
            foreach((array)$request->file('images') as $uploadedImage) {
                $imageId = $this->imageService->store($uploadedImage);
                $imagesIds->push($imageId);
            }

            // save images-question relationship in pivot table
            $question->images()->attach($imagesIds);
        });

        return response()->json(null, 204);
    }

    public function destroy($id) {
        // check if question exists
        try {
            $question = Question::findOrFail($id);
        } catch(ModelNotFoundException $exception) {
            return response()->json(['message' => 'Question with id ' . $id . ' does not exist.'], 404);
        }

        DB::transaction(function() use ($question) {
            // delete question's images
            Image::destroy($question->images()->pluck('image_id'));

            // delete question
            $question->delete();
        });

        return response()->json(null, 204);
    }
}
