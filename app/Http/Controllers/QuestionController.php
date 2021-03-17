<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\QuestionPostRequest;
use Illuminate\Support\Facades\Log;
use App\Models\Image;
use App\Models\Question;
use App\Services\ImageService;

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

        return response()->json(['id' => $question->id], 201);
    }

    public function edit($id) {

    }

    public function update(Request $request, $id) {

    }

    public function destroy($id) {

    }
}
