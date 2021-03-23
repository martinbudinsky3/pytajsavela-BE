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
        $recordsPerPage = 10;
        $page = request('page', 1);
        $offset = ($page - 1) * $recordsPerPage;

        // getting requested page of list of questions
        $questions = Question::with([
                'tags', 
                'author' => function($query) {
                    return $query->select('id', 'name');
                }
            ])
            ->withCount('answers')
            ->orderBy('created_at', 'desc')
            ->skip($offset)
            ->take($recordsPerPage)
            ->get();

        $count = Question::count();

        return response()->json(['count' => $count, 'questions' => $questions], 200);
    }

    public function show($id) {
        $question = Question::with([
                'author' => function($query) {
                    return $query->select('id', 'name');
                }, 
                'answers.images', 
                'answers.author' => function($query) {
                    return $query->select('id', 'name');
                }, 
                'images', 
                'tags'
            ])
            ->where('id', $id)
            ->first();

        if(!$question) {
            return response()->json(['message' => 'Question with id ' . $id . ' does not exist.'], 404);
        }

        return response()->json($question, 200);
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
            foreach((array)$request->file('images') as $uploadedImage) {
                $imageId = $this->imageService->store($uploadedImage);
                // save images-question relationship in pivot table
                $question->images()->attach($imageId);
            }
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

        $this->authorize('update', $question);

        $question->title = $request->title;
        $question->body = $request->body;

        DB::transaction(function() use ($request, $question) {
            // save image with updated fields
            $question->save();

            // delete tags-question relationship
            $question->tags()->detach($request->deleted_tags);
            // save tags-question relationship
            $question->tags()->attach($request->tags);

            // delete images
            Image::destroy($request->deleted_images);
            // save new images to DB
            foreach((array)$request->file('images') as $uploadedImage) {
                $imageId = $this->imageService->store($uploadedImage);
                // save images-question relationship in pivot table
                $question->images()->attach($imageId);
            }
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

        $this->authorize('delete', $question);

        DB::transaction(function() use ($question) {
            // delete question's images
            Image::destroy($question->images()->pluck('image_id'));

            // delete question
            $question->delete();
        });

        return response()->json(null, 204);
    }
}
