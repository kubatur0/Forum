<?php

namespace App\Http\Controllers;

use App\Rules\SpamFree;
use App\Thread;
use App\Channel;
use App\Filters\ThreadFilters;
use App\Trending;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redis;


class ThreadsController extends Controller
{
    private $n = 20;
    private $queens = [];

    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'show']);
    }

    public function index(Channel $channel, ThreadFilters $filters, Trending $trending)
    {

        $a = [0,1,3,0,5,2,0,1,0,0,0,2,0];


        $cur = 0;

        while($cur < sizeof($a)){

            if($a[$cur] == 0){
                array_push($a, 0);
                unset($a[$cur]);
            }

            $cur++;
        }

        foreach ($a as $value){
            echo $value.' ';
        }
        die;

        $threads = Thread::latest()->filter($filters);

        if ($channel->exists) {
            $threads->where('channel_id', $channel->id);
        }


        $threads = $threads->paginate(25);

        return view('forum.index', [
            'threads' => $threads,
            'trending' => $trending->get()
        ]);
    }

    public function show($channel, Thread $thread, Trending $trending)
    {

        if (auth()->check()) {
            auth()->user()->read($thread);
        }
//rozwiazanie dla klasy
//        $thread->visits()->record();

        $thread->increment('visits_count');

        $trending->push($thread);

        return view('forum.show', [
            'thread' => $thread
        ]);
    }

    public function create()
    {
        return view('forum.create');
    }

    public function store(Request $request)
    {

        $request->validate([
            'title' => ['required', new SpamFree],
            'channel_id' => 'required',
            'body' => ['required', new SpamFree]
        ]);

        $thread = Thread::create([
            'channel_id' => request('channel_id'),
            'title' => request('title'),
            'body' => request('body'),
            'user_id' => auth()->id()
        ]);

        return redirect($thread->path())
            ->with('flash', 'Your thread has been published');
    }

    public function destroy($channel, Thread $thread)
    {
        $this->authorize('update', $thread);

        $thread->delete();

        if (request()->wantsJson()) {
            return response([], 200);
        }

        return redirect(' / threads');

    }
}
