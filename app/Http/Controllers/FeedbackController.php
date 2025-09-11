<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Feedback;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class FeedbackController extends Controller
{
public function store(Request $request)
{
    try {
        $validated = $request->validate([
            'request_id'          => 'nullable|exists:requisition_forms,request_id',
            'system_performance'  => 'required|in:poor,fair,satisfactory,very good,outstanding',
            'booking_experience'  => 'required|in:poor,fair,good,very good,excellent',
            'ease_of_use'         => 'required|in:very difficult,difficult,neutral,easy,very easy',
            'useability'          => 'required|in:very unlikely,unlikely,neutral,likely,very likely',
            'additional_feedback' => 'nullable|string|max:1000',
            'email'               => 'nullable|email|max:255',
        ]);

        $feedback = Feedback::create($validated);

        Log::info('Feedback stored successfully', ['feedback' => $feedback->toArray()]);

        return response()->json([
            'message' => 'Thank you for your feedback!',
            'data' => $feedback
        ]);

    } catch (\Exception $e) {
        Log::error('Error storing feedback', [
            'message' => $e->getMessage(),
            'stack' => $e->getTraceAsString(),
            'request' => $request->all()
        ]);

        return response()->json([
            'message' => 'Error submitting feedback: ' . $e->getMessage()
        ], 500);
    }
}


}
