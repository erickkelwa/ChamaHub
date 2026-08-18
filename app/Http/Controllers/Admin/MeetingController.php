<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Meeting;
use Illuminate\Http\Request;

class MeetingController extends Controller
{
    /**
     * Display a listing of the meetings.
     */
    public function index(Request $request)
    {
        $query = Meeting::with('creator');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where('title', 'like', "%{$search}%")
                  ->orWhere('venue', 'like', "%{$search}%");
        }

        $meetings = $query->latest('meeting_date')->paginate(15);
        return view('admin.meetings.index', compact('meetings'));
    }

    /**
     * Show the form for creating a new meeting.
     */
    public function create()
    {
        return view('admin.meetings.create');
    }

    /**
     * Store a newly created meeting in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:150',
            'agenda' => 'required|string',
            'venue' => 'required|string|max:200',
            'meeting_date' => 'required|date',
            'minutes' => 'nullable|string',
        ]);

        $data = $request->all();
        $data['created_by'] = auth()->id();

        Meeting::create($data);

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting scheduled successfully.');
    }

    /**
     * Show the form for editing the specified meeting.
     */
    public function edit($id)
    {
        $meeting = Meeting::findOrFail($id);
        return view('admin.meetings.edit', compact('meeting'));
    }

    /**
     * Update the specified meeting in storage.
     */
    public function update(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);

        $request->validate([
            'title' => 'required|string|max:150',
            'agenda' => 'required|string',
            'venue' => 'required|string|max:200',
            'meeting_date' => 'required|date',
            'minutes' => 'nullable|string',
        ]);

        $meeting->update($request->all());

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting details updated successfully.');
    }

    /**
     * Display the specified meeting and its attendance.
     */
    public function show($id)
    {
        $meeting = Meeting::findOrFail($id);
        $users = \App\Models\User::whereIn('role', ['member', 'admin'])->get();
        $attendances = \App\Models\MeetingAttendance::where('meeting_id', $id)->get()->keyBy('user_id');
        
        return view('admin.meetings.show', compact('meeting', 'users', 'attendances'));
    }

    /**
     * Save meeting attendance.
     */
    public function saveAttendance(Request $request, $id)
    {
        $meeting = Meeting::findOrFail($id);
        
        $request->validate([
            'attendance' => 'required|array',
            'attendance.*' => 'in:present,absent,apology',
        ]);

        foreach ($request->attendance as $userId => $status) {
            \App\Models\MeetingAttendance::updateOrCreate(
                ['meeting_id' => $meeting->id, 'user_id' => $userId],
                ['status' => $status]
            );
        }

        return redirect()->route('admin.meetings.show', $meeting->id)->with('success', 'Attendance saved successfully.');
    }

    /**
     * Remove the specified meeting from storage.
     */
    public function destroy($id)
    {
        $meeting = Meeting::findOrFail($id);
        $meeting->delete();

        return redirect()->route('admin.meetings.index')->with('success', 'Meeting deleted successfully.');
    }
}
