<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Feedback;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class FeedbackController extends Controller
{
    /**
     * Get feedback data for chart visualization
     *
     * @param Request $request
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * Get all feedback records
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index()
    {
        try {
            $feedback = Feedback::with('requisitionForm')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($feedback);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch feedback',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the incoming request
            $validator = Validator::make($request->all(), [
                'email' => 'nullable|email|max:255',
                'request_id' => 'nullable|exists:requisition_forms,request_id',
                'system_performance' => 'required|in:poor,fair,satisfactory,very good,outstanding',
                'booking_experience' => 'required|in:poor,fair,good,very good,excellent',
                'ease_of_use' => 'required|in:very difficult,difficult,neutral,easy,very easy',
                'useability' => 'required|in:very unlikely,unlikely,neutral,likely,very likely',
                'additional_feedback' => 'nullable|string|max:1000'
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Create the feedback
            $feedback = Feedback::create($validator->validated());

            return response()->json([
                'success' => true,
                'message' => 'Feedback submitted successfully!',
                'data' => $feedback
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to submit feedback',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    public function getFeedbackData(Request $request)
    {
        try {
            $period = $request->query('period', 'all');

            // Base query
            $query = Feedback::query();

            // Apply period filter
            if ($period !== 'all') {
                $days = (int) $period;
                $query->where('created_at', '>=', now()->subDays($days));
            }

            // Get all feedback data
            $feedbackData = $query->get();

            // Prepare data structure for chart
            $chartData = [
                'system_performance' => [],
                'booking_experience' => [],
                'ease_of_use' => [],
                'useability' => [],
                'total_feedback' => $feedbackData->count(),
                'average_rating' => 0,
                'response_rate' => 0,
                'category_distribution' => []
            ];

            // Initialize rating counts for each category
            $ratingCategories = [
                'system_performance' => [
                    'poor' => 0,
                    'fair' => 0,
                    'satisfactory' => 0,
                    'good' => 0,
                    'very good' => 0,
                    'excellent' => 0
                ],
                'booking_experience' => [
                    'poor' => 0,
                    'fair' => 0,
                    'satisfactory' => 0,
                    'good' => 0,
                    'very good' => 0,
                    'excellent' => 0
                ],
                'ease_of_use' => [
                    'very difficult' => 0,
                    'difficult' => 0,
                    'neutral' => 0,
                    'easy' => 0,
                    'very easy' => 0
                ],
                'useability' => [
                    'very unlikely' => 0,
                    'unlikely' => 0,
                    'likely' => 0,
                    'very likely' => 0,
                    'outstanding' => 0
                ]
            ];

            $totalRatings = 0;
            $ratingSum = 0;

            // Count ratings for each category
            foreach ($feedbackData as $feedback) {
                // System Performance
                if (isset($feedback->system_performance)) {
                    $rating = strtolower($feedback->system_performance);
                    if (isset($ratingCategories['system_performance'][$rating])) {
                        $ratingCategories['system_performance'][$rating]++;
                    }
                }

                // Booking Experience
                if (isset($feedback->booking_experience)) {
                    $rating = strtolower($feedback->booking_experience);
                    if (isset($ratingCategories['booking_experience'][$rating])) {
                        $ratingCategories['booking_experience'][$rating]++;
                    }
                }

                // Ease of Use
                if (isset($feedback->ease_of_use)) {
                    $rating = strtolower($feedback->ease_of_use);
                    if (isset($ratingCategories['ease_of_use'][$rating])) {
                        $ratingCategories['ease_of_use'][$rating]++;
                    }
                }

                // Useability (Likely to Recommend)
                if (isset($feedback->useability)) {
                    $rating = strtolower($feedback->useability);
                    if (isset($ratingCategories['useability'][$rating])) {
                        $ratingCategories['useability'][$rating]++;
                    }
                }

                // Calculate average rating (convert textual ratings to numerical values)
                if (isset($feedback->system_performance)) {
                    $ratingValue = $this->convertRatingToNumber($feedback->system_performance);
                    $ratingSum += $ratingValue;
                    $totalRatings++;
                }
            }

            // Calculate average rating
            $chartData['average_rating'] = $totalRatings > 0 ? $ratingSum / $totalRatings : 0;

            // Calculate response rate (assuming we know total users who could provide feedback)
            $totalUsers = 100; // This should be replaced with actual user count
            $chartData['response_rate'] = $totalUsers > 0 ? round(($chartData['total_feedback'] / $totalUsers) * 100) : 0;

            // Add rating counts to chart data
            $chartData['system_performance'] = $ratingCategories['system_performance'];
            $chartData['booking_experience'] = $ratingCategories['booking_experience'];
            $chartData['ease_of_use'] = $ratingCategories['ease_of_use'];
            $chartData['useability'] = $ratingCategories['useability'];

            // Calculate category distribution
            $categoryDistribution = [
                'System Performance' => array_sum($ratingCategories['system_performance']),
                'Booking Experience' => array_sum($ratingCategories['booking_experience']),
                'Ease of Use' => array_sum($ratingCategories['ease_of_use']),
                'Likely to Recommend' => array_sum($ratingCategories['useability'])
            ];

            $chartData['category_distribution'] = $categoryDistribution;

            return response()->json($chartData);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch feedback data',
                'message' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Convert textual rating to numerical value for average calculation
     *
     * @param string $rating
     * @return int
     */
    private function convertRatingToNumber($rating)
    {
        $rating = strtolower($rating);

        $ratingMap = [
            'poor' => 1,
            'fair' => 2,
            'satisfactory' => 3,
            'good' => 4,
            'very good' => 5,
            'excellent' => 6,
            'very difficult' => 1,
            'difficult' => 2,
            'neutral' => 3,
            'easy' => 4,
            'very easy' => 5,
            'very unlikely' => 1,
            'unlikely' => 2,
            'likely' => 3,
            'very likely' => 4,
            'outstanding' => 5
        ];

        return $ratingMap[$rating] ?? 3; // Default to neutral if not found
    }

    /**
     * Get feedback statistics for dashboard cards
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFeedbackStats()
    {
        try {
            $stats = [
                'total_feedback' => Feedback::count(),
                'average_rating' => Feedback::avg(DB::raw('CASE 
                    WHEN system_performance = "excellent" THEN 6
                    WHEN system_performance = "very good" THEN 5
                    WHEN system_performance = "good" THEN 4
                    WHEN system_performance = "satisfactory" THEN 3
                    WHEN system_performance = "fair" THEN 2
                    WHEN system_performance = "poor" THEN 1
                    ELSE 0 END')) ?? 0,
                'positive_feedback' => Feedback::whereIn('system_performance', ['good', 'very good', 'excellent'])
                    ->orWhereIn('booking_experience', ['good', 'very good', 'excellent'])
                    ->count()
            ];

            return response()->json($stats);

        } catch (\Exception $e) {
            return response()->json([
                'error' => 'Failed to fetch feedback statistics',
                'message' => $e->getMessage()
            ], 500);
        }
    }
}