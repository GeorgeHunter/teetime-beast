<?php

namespace App\Http\Controllers;

use App\Event;
use Illuminate\Http\Request;

class EventsController extends Controller
{
    public function show(Event $event)
    {
        $event = Event::published()->findOrFail(1);
//        dd($event);
        return view('events.show', compact('event'));
    }
}
