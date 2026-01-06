@extends('layouts.app')

@section('title', 'Rate Our Booking Services')

@section('content')
    <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}">
    <style>
        /* Change background of active (checked) rating buttons */
        input.btn-check:checked+label.btn {
            background-color: #003366;
            /* your custom color */
            color: white;
            /* ensure text is readable */
            border-color: #003366;
            /* optional: match border to bg */
        }

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
            background-color: #007bff !important;
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
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            z-index: 1050;
            text-align: center;
            width: 90%;
            max-width: 400px;
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
        }
    </style>

<div class="container main-content-wrapper d-flex justify-content-center align-items-center">
    <div class="feedback-container col-md-8 col-lg-7">
        <h3 class="text-center mb-4">Share Your Experience</h3>
        <form id="feedbackForm">
            @csrf
            <input type="hidden" name="request_id" value="{{ $request_id ?? '' }}">

            <div class="mb-4">
                <label class="form-label d-block mb-2">1. How would you rate the system's performance?</label>
                <div class="btn-group rating-options d-flex flex-wrap" role="group"
                    aria-label="System Performance Rating">
                    <input type="radio" class="btn-check" name="system_performance" id="perfPoor" value="poor"
                        autocomplete="off" required>
                    <label class="btn" for="perfPoor">1</label>

                    <input type="radio" class="btn-check" name="system_performance" id="perfFair" value="fair"
                        autocomplete="off">
                    <label class="btn" for="perfFair">2</label>

                    <input type="radio" class="btn-check" name="system_performance" id="perfSatisfactory"
                        value="satisfactory" autocomplete="off">
                    <label class="btn" for="perfSatisfactory">3</label>

                    <input type="radio" class="btn-check" name="system_performance" id="perfVeryGood" value="very good"
                        autocomplete="off">
                    <label class="btn" for="perfVeryGood">4</label>

                    <input type="radio" class="btn-check" name="system_performance" id="perfOutstanding"
                        value="outstanding" autocomplete="off">
                    <label class="btn" for="perfOutstanding">5</label>
                </div>
                <div class="d-flex justify-content-between mt-0">
                    <small class="text-muted">Poor</small>
                    <small class="text-muted">Outstanding</small>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label d-block mb-2">2. How satisfied were you with your booking
                    experience?</label>
                <div class="btn-group rating-options d-flex flex-wrap" role="group"
                    aria-label="Booking Experience Satisfaction">
                    <input type="radio" class="btn-check" name="booking_experience" id="satPoor" value="poor"
                        autocomplete="off" required>
                    <label class="btn" for="satPoor">1</label>

                    <input type="radio" class="btn-check" name="booking_experience" id="satFair" value="fair"
                        autocomplete="off">
                    <label class="btn" for="satFair">2</label>

                    <input type="radio" class="btn-check" name="booking_experience" id="satGood" value="good"
                        autocomplete="off">
                    <label class="btn" for="satGood">3</label>

                    <input type="radio" class="btn-check" name="booking_experience" id="satVeryGood" value="very good"
                        autocomplete="off">
                    <label class="btn" for="satVeryGood">4</label>

                    <input type="radio" class="btn-check" name="booking_experience" id="satExcellent" value="excellent"
                        autocomplete="off">
                    <label class="btn" for="satExcellent">5</label>
                </div>
                <div class="d-flex justify-content-between mt-0">
                    <small class="text-muted">Poor</small>
                    <small class="text-muted">Excellent</small>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label d-block mb-2">3. How easy was it to use our booking system?</label>
                <div class="btn-group rating-options d-flex flex-wrap" role="group" aria-label="Ease of Use">
                    <input type="radio" class="btn-check" name="ease_of_use" id="easeVeryDifficult"
                        value="very difficult" autocomplete="off" required>
                    <label class="btn" for="easeVeryDifficult">1</label>

                    <input type="radio" class="btn-check" name="ease_of_use" id="easeDifficult" value="difficult"
                        autocomplete="off">
                    <label class="btn" for="easeDifficult">2</label>

                    <input type="radio" class="btn-check" name="ease_of_use" id="easeNeutral" value="neutral"
                        autocomplete="off">
                    <label class="btn" for="easeNeutral">3</label>

                    <input type="radio" class="btn-check" name="ease_of_use" id="easeEasy" value="easy"
                        autocomplete="off">
                    <label class="btn" for="easeEasy">4</label>

                    <input type="radio" class="btn-check" name="ease_of_use" id="easeVeryEasy" value="very easy"
                        autocomplete="off">
                    <label class="btn" for="easeVeryEasy">5</label>
                </div>
                <div class="d-flex justify-content-between mt-0">
                    <small class="text-muted">Very difficult</small>
                    <small class="text-muted">Very easy</small>
                </div>
            </div>

            <div class="mb-4">
                <label class="form-label d-block mb-2">4. How likely are you to use our system again?</label>
                <div class="btn-group rating-options d-flex flex-wrap" role="group"
                    aria-label="Likelihood to Use Again">
                    <input type="radio" class="btn-check" name="useability" id="likelyVeryUnlikely"
                        value="very unlikely" autocomplete="off" required>
                    <label class="btn" for="likelyVeryUnlikely">1</label>

                    <input type="radio" class="btn-check" name="useability" id="likelyUnlikely" value="unlikely"
                        autocomplete="off">
                    <label class="btn" for="likelyUnlikely">2</label>

                    <input type="radio" class="btn-check" name="useability" id="likelyNeutral" value="neutral"
                        autocomplete="off">
                    <label class="btn" for="likelyNeutral">3</label>

                    <input type="radio" class="btn-check" name="useability" id="likelyLikely" value="likely"
                        autocomplete="off">
                    <label class="btn" for="likelyLikely">4</label>

                    <input type="radio" class="btn-check" name="useability" id="likelyVeryLikely" value="very likely"
                        autocomplete="off">
                    <label class="btn" for="likelyVeryLikely">5</label>
                </div>
                <div class="d-flex justify-content-between mt-0">
                    <small class="text-muted">Very unlikely</small>
                    <small class="text-muted">Very likely</small>
                </div>
            </div>

            <div class="mb-3">
                <label for="additional_feedback" class="form-label">Additional feedback (How can we improve our
                    system?)</label>
                <textarea class="form-control" id="additional_feedback" name="additional_feedback" rows="4"
                    maxlength="1000"></textarea>
                <div class="word-count">
                    <span id="charCount">0</span>/1000 characters
                </div>
            </div>

            <div class="mb-3">
                <label for="email" class="form-label">Email (Optional)</label>
                <input type="email" class="form-control" id="email" name="email" maxlength="255">
            </div>

            <div class="text-center">
                <button type="submit" class="btn btn-primary">Submit Feedback</button>
            </div>
        </form>
    </div>
</div>

<div class="modal fade" id="thankYouModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content text-center">
      
      <!-- Modal Header with close button only -->
      <div class="modal-header border-0">
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body p-4">
        <!-- Big chat heart icon -->
        <i class="bi bi-chat-square-heart-fill text-danger mb-3" style="font-size: 4rem;"></i>

        <h5>Thank You for Your Feedback!</h5>
        <p>Your input helps us improve our services for everyone. We appreciate your time!</p>

        <div class="mt-5">
          <button type="button" class="btn btn-primary" onclick="window.location.href='{{ asset('index') }}'">
            Back to Home
          </button>
        </div>
      </div>
    </div>
  </div>
</div>





    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const textarea = document.getElementById('additional_feedback');
            const charCount = document.getElementById('charCount');

            textarea.addEventListener('input', function () {
                charCount.textContent = this.value.length;
            });

            const feedbackForm = document.getElementById('feedbackForm');
            feedbackForm.addEventListener('submit', async function (e) {
                e.preventDefault();

                const formData = new FormData(this);

                try {
                    const response = await fetch("/api/feedback", {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    });

                    const result = await response.json();
                    console.log('Server response:', result);

                    if (!response.ok) {
                        console.error('Server returned error:', result);
                        alert('Error submitting form: ' + (result.message || response.statusText));
                        return;
                    }

                    // Show the Bootstrap modal
                    const thankYouModalEl = document.getElementById('thankYouModal');
                    if (thankYouModalEl) {
                        const modal = new bootstrap.Modal(thankYouModalEl);
                        modal.show();


                    } else {
                        console.warn('Thank You modal element not found. Redirecting...');
                        window.location.href = "{{ asset('index') }}";
                    }

                } catch (err) {
                    console.error('Fetch error:', err);
                    alert('Error submitting form: ' + err.message);
                }
            });
        });


    </script>
@endsection