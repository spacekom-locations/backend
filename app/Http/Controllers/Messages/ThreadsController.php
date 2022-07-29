<?php

namespace App\Http\Controllers\Messages;

use App\Http\Controllers\Controller;
use App\Http\Requests\Messages\SendMessageRequest;
use App\Http\Requests\Messages\ShowThreadRequest;
use App\Http\Requests\Messages\StoreThreadRequest;
use App\Models\Location;
use Carbon\Carbon;
use Cmgmyr\Messenger\Models\Message;
use Cmgmyr\Messenger\Models\Participant;
use Cmgmyr\Messenger\Models\Thread;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ThreadsController extends Controller
{
    /**
     * Show all of the message threads to the user.
     *
     * @return mixed
     */
    public function index()
    {
        $user = auth('users')->user();
        $threads = Thread::forUser($user->id)->with(['participants.user', 'location.user'])->get();
        return $this->sendData($threads);
    }

    public function show(ShowThreadRequest $request, string $id)
    {
        $user = auth('users')->user();
        $thread = Thread::with(['messages', 'location', 'participants.user'])->where('id', $id)->first();
        if (!$thread) {
            return $this->sendError('Thread not found', Response::HTTP_NOT_FOUND);
        }
        if (!in_array($user->id, $thread->participantsUserIds($user->id))) {
            return $this->sendError('Access Denied', Response::HTTP_UNAUTHORIZED);
        }
        return $this->sendData($thread);
    }

    public function store(StoreThreadRequest $request)
    {
        $location = Location::find($request->input('location_id'));

        if (!$location) return $this->sendError('location not found', Response::HTTP_NOT_FOUND);

        $sender = auth('users')->user();
        //if the user already send message for this location do not create new thread
        $thread = Thread::where('location_id', $location->id)->whereHas('participants', function ($query) use ($sender) {
            return $query->where('user_id', '=', $sender->id);
        })->first();
        if ($thread) {
            
            if ($thread->creator()->id == $sender->id) {
                return $this->sendError('this thread is already created', Response::HTTP_NOT_ACCEPTABLE);
            }
        }


        $hasFlexibleDates = $request->input('has_flexible_dates');
        $hasFlexibleDates = intval($hasFlexibleDates);
        $bookingInputs = $request->input('booking_inputs');

        $thread = Thread::create([
            'location_id' => $location->id,
            'has_flexible_dates' => $hasFlexibleDates,
            'booking_inputs' => $bookingInputs,
        ]);

        $message = $request->input('message');

        // Message
        Message::create([
            'thread_id' => $thread->id,
            'user_id' => $sender->id,
            'body' => $message,
        ]);

        // Sender
        Participant::create([
            'thread_id' => $thread->id,
            'user_id' => $sender->id,
            'last_read' => Carbon::now(),
        ]);

        // Recipients . ** the owner of the location **
        $locationOwner = $location->user;
        $thread->addParticipant($locationOwner->id);
    }

    public function sendMessage(SendMessageRequest $request, string $id)
    {
        $user = auth('users')->user();
        $thread = Thread::with(['messages', 'location', 'participants.user'])->where('id', $id)->first();
        if (!$thread) {
            return $this->sendError('Thread not found', Response::HTTP_NOT_FOUND);
        }
        if (!in_array($user->id, $thread->participantsUserIds($user->id))) {
            return $this->sendError('Access Denied', Response::HTTP_UNAUTHORIZED);
        }

        $message = Message::create([
            'thread_id' => $thread->id,
            'user_id' => $user->id,
            'body' =>  $request->input('message'),
        ]);

        $participant = $thread->getParticipantFromUser($user->id);
        $participant->update(['last_read' => Carbon::now(),]);

        return $this->sendData($message);
    }
}
