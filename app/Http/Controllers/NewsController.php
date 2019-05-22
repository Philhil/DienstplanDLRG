<?php

namespace App\Http\Controllers;

use App\Events\OnCreateNews;
use App\News;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NewsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $newss = News::where('client_id', '=', Auth::user()->currentclient_id)->orderBy('created_at', 'DESC')->with('user')->get();
        return view('news.index', compact('newss'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $news = new News();
        return view('news.create', compact('news'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $news = new News($request->all());
        $news['user_id'] = Auth::user()->id;
        $news['client_id'] = Auth::user()->currentclient_id;
        $news->save();

        event(new OnCreateNews($news));

        return redirect(action('NewsController@index'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $news = News::findOrFail($id);
        return view('news.edit', compact('news'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        $news = News::findOrFail($id);
        $news->title = $request->get('title');
        $news->content = $request->get('content');
        $news->save();

        return redirect(action('NewsController@index'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        if(!Auth::user()->isAdmin()) {
            abort(402, "Nope.");
        }

        News::findOrFail($id)->forceDelete();

        return redirect(action('NewsController@index'));
    }

    public function delete($id)
    {
        return $this->destroy($id);
    }
}
