<?php

namespace App\Http\Controllers;

use App\PlatformEvent;
use App\Providers\PlatformEventHandlerServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Input;

class PlatformEventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return view('platform_events.index',[
            'platformEvents' => PlatformEvent::all()->forPage((Input::get("page")?Input::get("page"):1),20)
        ]);
    }

    /**
     * @param $eventId
     * @return mixed
     */
    public function codeExplorer($eventId)
    {
        $event = PlatformEvent::whereId($eventId)->first();
        dd($event->json_value);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(PlatformEvent::whereId($id)->exists()) {
            PlatformEvent::whereId($id)->delete();
        }

        return back();
    }
}
