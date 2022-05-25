<?php

namespace App\Http\Controllers\Supervisors;

use App\Http\Controllers\Controller;
use App\Http\Requests\ForbiddenWords\DestroyWordRequest;
use App\Http\Requests\ForbiddenWords\IndexWordRequest;
use App\Http\Requests\ForbiddenWords\ShowWordRequest;
use App\Http\Requests\ForbiddenWords\StoreWordRequest;
use App\Http\Requests\ForbiddenWords\UpdateWordRequest;
use App\Models\ForbiddenWord;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ForbiddenWordsController extends Controller
{
    public function store(StoreWordRequest $request)
    {
        $word = trim($request->input('text'));
        $word = ForbiddenWord::create(['text' => $word]);
        return $this->sendData($word);
    }

    public function index(IndexWordRequest $request)
    {
        $words = ForbiddenWord::all();
        return $this->sendData($words);
    }

    public function show(ShowWordRequest $request, int $id)
    {
        $word = ForbiddenWord::find($id);
        if (!$word) {
            return $this->sendError([__('misc.not_found')], Response::HTTP_NOT_FOUND);
        }
        return $this->sendData($word);
    }

    public function update(UpdateWordRequest $request, int $id)
    {
        $word = ForbiddenWord::find($id);
        if (!$word) {
            return $this->sendError([__('misc.not_found')], Response::HTTP_NOT_FOUND);
        }
        $word->update($request->all());
        return $this->sendData($word->refresh());
    }

    public function destroy(DestroyWordRequest $request, int $id)
    {
        $word = ForbiddenWord::find($id);
        if (!$word) {
            return $this->sendError([__('misc.not_found')], Response::HTTP_NOT_FOUND);
        }
        $word->delete();
        return $this->sendData($word);
    }
}
