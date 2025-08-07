@extends('layouts.app')

@section('title', 'Rate Our Booking Services')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}">
    <style>
        body {
            background: url('{{ asset('assets/cpu-pic1.jpg') }}') center/cover no-repeat fixed;
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            margin: 0;
            font-family: Arial, sans-serif;
        }

        .main-content-wrapper {
            flex-grow: 1;
            padding: 20px 0;
        }

        .feedback-container {
            background-color: rgba(255, 255, 255, 0.5);
            border-radius: 10px;
            padding: 3rem;
            box-shadow: 0 0 15px rgba(0, 0, 0, 0.2);
        }

        .rating-options .btn {
            border: 1px solid #ccc;
            background-color: #f8f9fa;
            color: #333;
            margin: 0 5px 10px 0;
            padding: 8px 15px;
            border-radius: 5px;
            cursor: pointer;
            transition: all 0.2s ease-in-out;
        }

        .rating-options .btn:hover {
            background-color: #e9ecef;
            border-color: #b0b0b0;
        }

        .rating-options .btn.active {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
            box-shadow: 0 2px 5px rgba(0, 123, 255, 0.3);
        }

        .word-count {
            font-size: 0.85em;
            color: #e9ebed;
            text-align: right;
        }

        .thank-you-popup {
            display: none;
            /* Hidden by default */
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1050;
            /* Above all other content */
            text-align: center;
            width: 90%;
            max-width: 400px;
            /* Adjust max-width as needed */
        }

        .thank-you-popup.show {
            display: block;
        }

        .thank-you-popup h5 {
            color: #003366;
            margin-bottom: 15px;
        }

        .thank-you-popup p {
            color: #555;
            margin-bottom: 25px;
        }

        .thank-you-popup .btn {
            margin: 0 10px;
            padding: 10px 20px;
        }

        footer {
            background-color: #003366;
            color: white;
            text-align: center;
            padding: 1rem 0;
            margin-top: auto;
            /* Pushes footer to the bottom */
        }
    </style>
    <div class="container main-content-wrapper d-flex justify-content-center align-items-center">
        <div class="feedback-container col-md-8 col-lg-7">
            <h3 class="text-center mb-4">Share Your Experience</h3>
            <form id="feedbackForm">
                <div class="mb-4">
                    <label class="form-label d-block mb-2">1. How would you rate the system's performance?</label>
                    <div class="btn-group rating-options d-flex flex-wrap" role="group"
                        aria-label="System Performance Rating">
                        <input type="radio" class="btn-check" name="performanceRating" id="perfPoor" value="Poor"
                            autocomplete="off">
                        <label class="btn" for="perfPoor">Poor</label>

                        <input type="radio" class="btn-check" name="performanceRating" id="perfFair" value="Fair"
                            autocomplete="off">
                        <label class="btn" for="perfFair">Fair</label>

                        <input type="radio" class="btn-check" name="performanceRating" id="perfSatisfactory"
                            value="Satisfactory" autocomplete="off">
                        <label class="btn" for="perfSatisfactory">Satisfactory</label>

                        <input type="radio" class="btn-check" name="performanceRating" id="perfVeryGood" value="Very Good"
                            autocomplete="off">
                        <label class="btn" for="perfVeryGood">Very Good</label>

                        <input type="radio" class="btn-check" name="performanceRating" id="perfOutstanding"
                            value="Outstanding" autocomplete="off">
                        <label class="btn" for="perfOutstanding">Outstanding</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label d-block mb-2">2. How satisfied were you with your booking
                        experience?</label>
                    <div class="btn-group rating-options d-flex flex-wrap" role="group"
                        aria-label="Booking Experience Satisfaction">
                        <input type="radio" class="btn-check" name="satisfactionRating" id="satPoor" value="Poor"
                            autocomplete="off">
                        <label class="btn" for="satPoor">Poor</label>

                        <input type="radio" class="btn-check" name="satisfactionRating" id="satFair" value="Fair"
                            autocomplete="off">
                        <label class="btn" for="satFair">Fair</label>

                        <input type="radio" class="btn-check" name="satisfactionRating" id="satGood" value="Good"
                            autocomplete="off">
                        <label class="btn" for="satGood">Good</label>

                        <input type="radio" class="btn-check" name="satisfactionRating" id="satVeryGood" value="Very Good"
                            autocomplete="off">
                        <label class="btn" for="satVeryGood">Very Good</label>

                        <input type="radio" class="btn-check" name="satisfactionRating" id="satExcellent" value="Excellent"
                            autocomplete="off">
                        <label class="btn" for="satExcellent">Excellent</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label d-block mb-2">3. How easy was it to use our booking system?</label>
                    <div class="btn-group rating-options d-flex flex-wrap" role="group" aria-label="Ease of Use">
                        <input type="radio" class="btn-check" name="easeRating" id="easeVeryDifficult"
                            value="Very difficult" autocomplete="off">
                        <label class="btn" for="easeVeryDifficult">Very difficult</label>

                        <input type="radio" class="btn-check" name="easeRating" id="easeDifficult" value="Difficult"
                            autocomplete="off">
                        <label class="btn" for="easeDifficult">Difficult</label>

                        <input type="radio" class="btn-check" name="easeRating" id="easeNeutral" value="Neutral"
                            autocomplete="off">
                        <label class="btn" for="easeNeutral">Neutral</label>

                        <input type="radio" class="btn-check" name="easeRating" id="easeEasy" value="Easy"
                            autocomplete="off">
                        <label class="btn" for="easeEasy">Easy</label>

                        <input type="radio" class="btn-check" name="easeRating" id="easeVeryEasy" value="Very Easy"
                            autocomplete="off">
                        <label class="btn" for="easeVeryEasy">Very Easy</label>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="form-label d-block mb-2">4. How likely are you to use our system again?</label>
                    <div class="btn-group rating-options d-flex flex-wrap" role="group"
                        aria-label="Likelihood to Use Again">
                        <input type="radio" class="btn-check" name="likelihoodRating" id="likelyVeryUnlikely"
                            value="Very Unlikely" autocomplete="off">
                        <label class="btn" for="likelyVeryUnlikely">Very Unlikely</label>

                        <input type="radio" class="btn-check" name="likelihoodRating" id="likelyUnlikely" value="Unlikely"
                            autocomplete="off">
                        <label class="btn" for="likelyUnlikely">Unlikely</label>

                        <input type="radio" class="btn-check" name="likelihoodRating" id="likelyNeutral" value="Neutral"
                            autocomplete="off">
                        <label class="btn" for="likelyNeutral">Neutral</label>

                        <input type="radio" class="btn-check" name="likelihoodRating" id="likelyLikely" value="Likely"
                            autocomplete="off">
                        <label class="btn" for="likelyLikely">Likely</label>

                        <input type="radio" class="btn-check" name="likelihoodRating" id="likelyVeryLikely"
                            value="Very Likely" autocomplete="off">
                        <label class="btn" for="likelyVeryLikely">Very Likely</label>
                    </div>
                </div>

                <div class="mb-3">
                    <label for="additionalFeedback" class="form-label">Additional feedback (How can we improve our
                        system?)</label>
                    <textarea class="form-control" id="additionalFeedback" rows="4" maxlength="250"></textarea>
                    <div class="word-count">
                        <span id="charCount">0</span>/250 words
                    </div>
                </div>

                <div class="text-center">
                    <button type="submit" class="btn btn-primary">Submit Feedback</button>
                </div>
            </form>
        </div>
    </div>
    <div id="thankYouPopup" class="thank-you-popup">
        <h5>Thank You for Your Feedback!</h5>
        <p>Your input helps us improve our services for everyone. We appreciate your time!</p>
        <div class="d-flex justify-content-center">
            <button class="btn btn-outline-primary" onclick="window.location.href='index.html'">Back to Home</button>
            <button class="btn btn-primary ms-3" onclick="window.location.href='bookingpage.html'">Book Again</button>
        </div>
    </div>
@endsection
