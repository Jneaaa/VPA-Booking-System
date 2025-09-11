<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\RequisitionComment;
use Illuminate\Support\Facades\Log;

class AdminCommentsController extends Controller
{
    /**
     * Add a new comment to a requisition form
     */
    public function addComment(Request $request, $requestId)
    {
        try {
            $admin = $request->user();
            
            $validated = $request->validate([
                'comment' => 'required|string|max:1000',
            ]);

            $comment = RequisitionComment::create([
                'request_id' => $requestId,
                'admin_id' => $admin->admin_id,
                'comment' => $validated['comment'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Load the admin relationship for the response
            $comment->load('admin');

            return response()->json([
                'success' => true,
                'message' => 'Comment added successfully',
                'comment' => $comment
            ], 201);

        } catch (\Illuminate\Validation\ValidationException $e) {
            Log::error('Validation error adding comment', [
                'request_id' => $requestId,
                'admin_id' => $request->user()->admin_id ?? 'unknown',
                'errors' => $e->errors()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
            
        } catch (\Exception $e) {
            Log::error('Error adding comment to requisition', [
                'request_id' => $requestId,
                'admin_id' => $request->user()->admin_id ?? 'unknown',
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString()
            ]);
            return response()->json([
                'success' => false,
                'message' => 'Failed to add comment'
            ], 500);
        }
    }

    /**
     * Get all comments for a requisition form
     */
    public function getComments($requestId)
{
    try {
        Log::info('Fetching comments', ['request_id' => $requestId]);

        $comments = RequisitionComment::where('request_id', $requestId)
            ->with('admin')
            ->orderBy('created_at', 'asc') // Change from 'desc' to 'asc'
            ->get();

        Log::debug('Comments fetched', [
            'request_id' => $requestId,
            'comment_count' => $comments->count()
        ]);

        return response()->json([
            'success' => true,
            'comments' => $comments
        ]);

    } catch (\Exception $e) {
        Log::error('Error fetching comments', [
            'request_id' => $requestId,
            'error_message' => $e->getMessage(),
            'error_file' => $e->getFile(),
            'error_line' => $e->getLine()
        ]);
        return response()->json([
            'success' => false,
            'message' => 'Failed to fetch comments'
        ], 500);
    }
}

    
}