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
            ->select('id', 'title', 'created_at', 'user_id')
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
                'answers' => function($query) {
                    return $query->orderBy('created_at', 'asc');
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
        $question = Question::with(['tags'])
            ->select('id', 'title', 'body')
            ->where('id', $id)
            ->first();

        if(!$question) {
            return response()->json(['message' => 'Question with id ' . $id . ' does not exist.'], 404);
        }

        return response()->json($question, 200);
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
            // save question with updated fields
            $question->save();

            // delete tags-question relationship
            $question->tags()->detach($request->deleted_tags);
            // save tags-question relationship
            $question->tags()->attach($request->tags);
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
            $question->images()->delete();

            // delete question
            $question->delete();
        });

        return response()->json(null, 204);
    }
}
