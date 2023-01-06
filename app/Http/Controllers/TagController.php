<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Tag;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class TagController extends Controller
{
    public function __construct()
    {
        $this->middleware(function ($request, $next) {
            if(!Auth::user()->isAdmin()) {
                abort(402, "Nope.");
            }

            return $next($request);
        });
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tags = \App\Tag::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('name')->get();
        return view('tag.index')->with('tags', $tags);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $tag= new Tag();
        return view('tag.create', compact('tag'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreTagRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTagRequest $request)
    {
        if($request->has('id') &&  $request->get('id')) {
            $tag = Tag::findOrFail($request->get('id'));

            //check if quali is of own client
            if($tag['client_id'] != Auth::user()->currentclient_id) {
                abort(402, "Nope.");
            }

            $tag->fill($request->except(['id']));
            $tag['client_id'] = Auth::user()->currentclient_id;
            $tag->save();
            Session::flash('successmessage', 'Kategorie '. $tag->name .' gespeichert');

        } else {
            $tag = new Tag($request->except(['id']));
            $tag['client_id'] = Auth::user()->currentclient_id;
            $tag->save();
            Session::flash('successmessage', 'Kategorie '. $tag->name .' angelegt');
        }

        return redirect(action('TagController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function show(Tag $tag)
    {
        if($tag['client_id'] != Auth::user()->currentclient_id) {
            abort(402, "Nope.");
        }
        return view('tag.create', compact('tag'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function edit(Tag $tag)
    {
        if($tag['client_id'] != Auth::user()->currentclient_id) {
            abort(402, "Nope.");
        }
        return view('tag.create', compact('tag'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateTagRequest  $request
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function update(UpdateTagRequest $request, Tag $tag)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Tag  $tag
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tag $tag)
    {
        //check if quali is of own client
        if($tag['client_id'] != Auth::user()->currentclient_id) {
            abort(402, "Nope.");
        }

        $tag->delete();
        Session::flash('successmessage', 'Kategorie erfolgreich gelöscht');
        return redirect()->back();
    }
}
