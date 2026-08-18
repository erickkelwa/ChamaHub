<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Poll;
use App\Models\Vote;
use Carbon\Carbon;

class PollController extends Controller
{
    /**
     * Display a listing of polls and decisions.
     */
    public function index()
    {
        // Get all polls, with counts of votes
        $polls = Poll::with(['creator', 'votes'])->orderBy('created_at', 'desc')->get();
        
        // Calculate results
        foreach ($polls as $poll) {
            $totalVotes = $poll->votes->count();
            $poll->yes_votes = $poll->votes->where('choice', 'yes')->count();
            $poll->no_votes = $poll->votes->where('choice', 'no')->count();
            $poll->abstain_votes = $poll->votes->where('choice', 'abstain')->count();
            
            $poll->yes_percentage = $totalVotes > 0 ? ($poll->yes_votes / $totalVotes) * 100 : 0;
            $poll->no_percentage = $totalVotes > 0 ? ($poll->no_votes / $totalVotes) * 100 : 0;
            $poll->abstain_percentage = $totalVotes > 0 ? ($poll->abstain_votes / $totalVotes) * 100 : 0;
            
            // Check if current user has voted
            $poll->has_voted = $poll->votes->where('user_id', auth()->id())->isNotEmpty();
            if ($poll->has_voted) {
                $poll->user_vote = $poll->votes->where('user_id', auth()->id())->first()->choice;
            }
            
            // Auto close if expired
            if ($poll->status == 'active' && $poll->expires_at && Carbon::now()->greaterThan($poll->expires_at)) {
                $poll->update(['status' => 'closed']);
                $poll->status = 'closed';
            }
        }

        return view('polls.index', compact('polls'));
    }

    /**
     * Store a newly created poll.
     */
    public function store(Request $request)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'treasurer') {
            abort(403, 'Unauthorized action.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'expires_at' => 'nullable|date|after:today',
        ]);

        Poll::create([
            'title' => $request->title,
            'description' => $request->description,
            'created_by' => auth()->id(),
            'status' => 'active',
            'expires_at' => $request->expires_at ? Carbon::parse($request->expires_at) : null,
        ]);

        return redirect()->route('decisions.index')->with('success', 'Poll created successfully.');
    }

    /**
     * Record a member's vote.
     */
    public function vote(Request $request, Poll $poll)
    {
        $request->validate([
            'choice' => 'required|in:yes,no,abstain',
        ]);

        if ($poll->status !== 'active') {
            return redirect()->back()->with('error', 'This poll is already closed.');
        }
        
        if ($poll->expires_at && Carbon::now()->greaterThan($poll->expires_at)) {
             return redirect()->back()->with('error', 'This poll has expired.');
        }

        $existingVote = Vote::where('poll_id', $poll->id)->where('user_id', auth()->id())->first();

        if ($existingVote) {
            return redirect()->back()->with('error', 'You have already voted on this poll.');
        }

        Vote::create([
            'poll_id' => $poll->id,
            'user_id' => auth()->id(),
            'choice' => $request->choice,
        ]);

        return redirect()->back()->with('success', 'Your vote has been recorded.');
    }

    /**
     * Admin forcefully closes a poll early.
     */
    public function close(Poll $poll)
    {
        if (auth()->user()->role !== 'admin' && auth()->user()->role !== 'treasurer') {
            abort(403, 'Unauthorized action.');
        }

        $poll->update(['status' => 'closed']);

        return redirect()->back()->with('success', 'Poll has been closed manually.');
    }
}
