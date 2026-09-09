<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PushSubscription;
use Illuminate\Http\Request;

class PushSubscriptionController extends Controller
{
    /**
     * Store or update a push subscription for the authenticated user.
     * Called by the frontend when the user grants notification permission.
     */
    public function store(Request $request)
    {
        $request->validate([
            'endpoint'   => 'required|url',
            'keys'       => 'required|array',
            'keys.p256dh'=> 'required|string',
            'keys.auth'  => 'required|string',
        ]);

        $user = $request->user();

        // Upsert — update if endpoint already exists for this user
        PushSubscription::updateOrCreate(
            [
                'user_id'  => $user->id,
                'endpoint' => $request->endpoint,
            ],
            [
                'p256dh_key' => $request->keys['p256dh'],
                'auth_key'   => $request->keys['auth'],
                'user_agent' => substr($request->userAgent() ?? '', 0, 255),
            ]
        );

        return response()->json(['message' => 'Push subscription saved.'], 201);
    }

    /**
     * Remove a push subscription (user opts out or logs out).
     */
    public function destroy(Request $request)
    {
        $request->validate(['endpoint' => 'required|url']);

        PushSubscription::where('user_id', $request->user()->id)
            ->where('endpoint', $request->endpoint)
            ->delete();

        return response()->json(['message' => 'Push subscription removed.']);
    }
}
