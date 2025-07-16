<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>
    Central Philippine University - Equipment and Facility Booking Services
  </title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link rel="stylesheet" href="{{ asset('css/public/global-styles.css') }}" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.9.0/css/bootstrap-datepicker.min.css"
    rel="stylesheet" />
  <link href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-icons/1.10.5/font/bootstrap-icons.min.css"
    rel="stylesheet" />
  <style>
    body {
      background-color: #f8f9fa;
    }

    .main-content {
      padding-top: 20px;
    }

    .form-section-card {
      background: white;
      padding: 20px;
      border: 1px solid #ddd;
      /* Replace shadow with solid stroke */
      border-radius: 0;
      /* Remove rounded corners */
      margin-bottom: 20px;
    }

    .form-section-card h5,
    .form-section-card h6 {
      color: #003366;
      margin-bottom: 15px;
    }

    .form-section-card .form-control,
    .form-section-card .form-select {
      margin-bottom: 10px;
    }

    .summary-item {
      display: flex;
      justify-content: space-between;
      padding: 8px 0;
      border-bottom: 1px solid #eee;
    }

    .summary-item:last-child {
      border-bottom: none;
    }

    .summary-item .item-details {
      flex-grow: 1;
    }

    .summary-item .item-price {
      font-weight: bold;
    }

    .calendar-header {
      background-color: #e9ecef;
      padding: 10px;
      border-radius: 5px;
      margin-bottom: 15px;
      display: flex;
      justify-content: space-between;
      align-items: center;
    }

    .calendar-grid {
      display: grid;
      grid-template-columns: repeat(7, 1fr);
      gap: 5px;
      text-align: center;
    }

    .calendar-day-name {
      font-weight: bold;
      padding: 5px;
      background-color: #f0f0f0;
    }

    .calendar-day {
      padding: 10px 5px;
      border: 1px solid #ddd;
      border-radius: 5px;
      cursor: pointer;
      background-color: #fff;
    }

    .calendar-day.selected {
      background-color: #007bff;
      color: white;
      border-color: #007bff;
    }

    .calendar-day.disabled {
      background-color: #f8f9fa;
      color: #ccc;
      cursor: not-allowed;
    }

    .selected-schedule {
      background-color: #e9f5ff;
      padding: 10px;
      border-radius: 5px;
      margin-top: 15px;
    }

    .availability-row {
      display: flex;
      align-items: center;
      margin-bottom: 10px;
    }

    .availability-row .form-control {
      flex-grow: 1;
      margin-right: 10px;
    }

    .availability-status {
      color: green;
      font-weight: bold;
    }

    .slideshow-placeholder {
      background-color: #e9ecef;
      height: 200px;
      display: flex;
      justify-content: center;
      align-items: center;
      color: #6c757d;
      border-radius: 5px;
      margin-bottom: 15px;
    }

    .included-equipment-list {
      list-style: none;
      padding: 0;
    }

    .included-equipment-list li {
      display: flex;
      justify-content: space-between;
      margin-bottom: 5px;
    }

    .quantity-control {
      display: flex;
      align-items: center;
    }

    .quantity-control .btn {
      padding: 0.25rem 0.5rem;
      font-size: 0.75rem;
    }

    .quantity-control input {
      width: 50px;
      text-align: center;
      margin: 0 5px;
    }

    .total-price {
      font-size: 1.25em;
      font-weight: bold;
    }

    .popup {
      display: none;
      position: fixed;
      top: 50%;
      left: 50%;
      transform: translate(-50%, -50%);
      background: white;
      padding: 20px;
      z-index: 1050;
      border: 1px solid #ccc;
      border-radius: 8px;
      width: 80%;
      max-width: 700px;
      box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
      max-height: 90vh;
      overflow-y: auto;
    }

    .popup.show {
      display: block;
    }

    .overlay {
      display: none;
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background: rgba(0, 0, 0, 0.5);
      z-index: 1040;
    }

    .overlay.show {
      display: block;
    }

    .col-md-6 {
      flex: 0 0 50%;
      max-width: 50%;
    }

    .row {
      display: flex;
      flex-wrap: wrap;
    }

    #reservationContent {
      transition: max-height 0.3s ease, opacity 0.3s ease;
      overflow: hidden;
    }

    #toggleReservationBtn {
      transition: transform 0.3s ease;
    }

    #toggleReservationBtn.rotate {
      transform: rotate(180deg);
    }

    .facility-card,
    .equipment-card {
      position: relative;
      /* Enable positioning for the trash bin */
      border: none;
      /* Remove grey border */
      border-radius: 0;
      /* Remove rounded corners */
      padding: 10px;
      display: flex;
      align-items: center;
      margin-bottom: 15px;
    }

    .facility-card .btn-outline-danger,
    .equipment-card .btn-outline-danger {
      position: absolute;
      top: 10px;
      right: 10px;
      /* Move trash bin to top-right */
      z-index: 1;
      /* Ensure it stays above other elements */
      border: none;
      /* Remove red border */
    }

    .facility-card img,
    .equipment-card img {
      width: 120px;
      /* Ensure consistent image size */
      height: 120px;
      object-fit: cover;
      border-radius: 0;
      /* Remove rounded corners from images */
      margin-right: 15px;
    }

    .facility-card .facility-details,
    .equipment-card .equipment-details {
      flex-grow: 1;
      margin-right: 30px;
      /* Further reduce right margin */
    }
  </style>
</head>

<body>
  @extends('layouts.app')

  @section('title', 'Reservation Form')
  @section('content')

    <div class="container main-content">
    <!-- Complete Your Reservation Section -->
    <div class="row">
      <div class="col-12">
      <div class="form-section-card">
        <div class="d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Complete Your Reservation</h5>
        <button id="toggleReservationBtn" class="btn btn-sm btn-outline-secondary"
          style="height: 100%; align-self: center">
          <i class="bi bi-chevron-up"></i>
        </button>
        </div>
        <div id="reservationContent" style="padding-top: 10px">
        <p class="text-muted">
          To confirm your request, please fill out the necessary details
          below. We need this information to process your booking
          efficiently and provide complete details on how to proceed. A
          confirmation email will be sent to your registered email address
          once your submission is reviewed and approved.
        </p>
        <div class="d-flex justify-content-start gap-2 mb-4">
          <a href="policies.html" class="btn btn-primary">
          Read Policies
          </a>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Two-column grid layout -->
    <div class="row">
      <div class="col-md-6 d-flex flex-column">
      <div class="form-section-card flex-grow-1">
        <h5>Your Contact Information</h5>
        <div class="row">
        <div class="col-md-12">
          <select id="applicantType" class="form-select mb-2" aria-label="Type of Applicant">
          <option selected>Type of Applicant</option>
          <option value="Internal">Internal</option>
          <option value="External">External</option>
          </select>
        </div>
        <div class="col-md-12">
          <input type="text" class="form-control mb-2" placeholder="Organization Name (optional)" />
        </div>
        <div class="col-md-12">
          <input type="email" class="form-control mb-2" placeholder="Email" required />
        </div>
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="First Name" required />
        </div>
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="Last Name" required />
        </div>
        <div id="studentIdField" class="col-md-6">
          <input type="text" class="form-control" placeholder="CPU Student ID" />
        </div>
        <div class="col-md-6">
          <input type="text" class="form-control" placeholder="Contact Number" />
        </div>
        </div>
      </div>
      </div>

      <div class="col-md-6 d-flex flex-column">
      <div class="form-section-card flex-grow-1">
        <div class="d-flex justify-content-between align-items-center">
        <h5>Booking Schedule</h5>
        </div>
        <p id="selectedDateTime" class="text-muted">
        No date and time selected.
        </p>
        <div class="row">
        <div class="col-md-6">
          <label for="startDateField" class="form-label">Start Date</label>
          <input type="date" id="startDateField" class="form-control mb-2" />
        </div>
        <div class="col-md-6">
          <label for="startTimeField" class="form-label">Start Time</label>
          <select id="startTimeField" class="form-select mb-2" onchange="adjustEndTime()">
          <!-- Predefined 12-hour intervals -->
          <option value="12:00 AM">12:00 AM</option>
          <option value="12:30 AM">12:30 AM</option>
          <option value="01:00 AM">01:00 AM</option>
          <option value="01:30 AM">01:30 AM</option>
          <option value="02:00 AM">02:00 AM</option>
          <option value="02:30 AM">02:30 AM</option>
          <option value="03:00 AM">03:00 AM</option>
          <option value="03:30 AM">03:30 AM</option>
          <option value="04:00 AM">04:00 AM</option>
          <option value="04:30 AM">04:30 AM</option>
          <option value="05:00 AM">05:00 AM</option>
          <option value="05:30 AM">05:30 AM</option>
          <option value="06:00 AM">06:00 AM</option>
          <option value="06:30 AM">06:30 AM</option>
          <option value="07:00 AM">07:00 AM</option>
          <option value="07:30 AM">07:30 AM</option>
          <option value="08:00 AM">08:00 AM</option>
          <option value="08:30 AM">08:30 AM</option>
          <option value="09:00 AM">09:00 AM</option>
          <option value="09:30 AM">09:30 AM</option>
          <option value="10:00 AM">10:00 AM</option>
          <option value="10:30 AM">10:30 AM</option>
          <option value="11:00 AM">11:00 AM</option>
          <option value="11:30 AM">11:30 AM</option>
          <option value="12:00 PM">12:00 PM</option>
          <option value="12:30 PM">12:30 PM</option>
          <option value="01:00 PM">01:00 PM</option>
          <option value="01:30 PM">01:30 PM</option>
          <option value="02:00 PM">02:00 PM</option>
          <option value="02:30 PM">02:30 PM</option>
          <option value="03:00 PM">03:00 PM</option>
          <option value="03:30 PM">03:30 PM</option>
          <option value="04:00 PM">04:00 PM</option>
          <option value="04:30 PM">04:30 PM</option>
          <option value="05:00 PM">05:00 PM</option>
          <option value="05:30 PM">05:30 PM</option>
          <option value="06:00 PM">06:00 PM</option>
          <option value="06:30 PM">06:30 PM</option>
          <option value="07:00 PM">07:00 PM</option>
          <option value="07:30 PM">07:30 PM</option>
          <option value="08:00 PM">08:00 PM</option>
          <option value="08:30 PM">08:30 PM</option>
          <option value="09:00 PM">09:00 PM</option>
          <option value="09:30 PM">09:30 PM</option>
          <option value="10:00 PM">10:00 PM</option>
          <option value="10:30 PM">10:30 PM</option>
          <option value="11:00 PM">11:00 PM</option>
          <option value="11:30 PM">11:30 PM</option>
          </select>
        </div>
        </div>
        <div class="row">
        <div class="col-md-6">
          <label for="endDateField" class="form-label">End Date</label>
          <input type="date" id="endDateField" class="form-control mb-2" />
        </div>
        <div class="col-md-6">
          <label for="endTimeField" class="form-label">End Time</label>
          <select id="endTimeField" class="form-select mb-3">
          <!-- Predefined 12-hour intervals -->
          <option value="12:00 AM">12:00 AM</option>
          <option value="12:30 AM">12:30 AM</option>
          <option value="01:00 AM">01:00 AM</option>
          <option value="01:30 AM">01:30 AM</option>
          <option value="02:00 AM">02:00 AM</option>
          <option value="02:30 AM">02:30 AM</option>
          <option value="03:00 AM">03:00 AM</option>
          <option value="03:30 AM">03:30 AM</option>
          <option value="04:00 AM">04:00 AM</option>
          <option value="04:30 AM">04:30 AM</option>
          <option value="05:00 AM">05:00 AM</option>
          <option value="05:30 AM">05:30 AM</option>
          <option value="06:00 AM">06:00 AM</option>
          <option value="06:30 AM">06:30 AM</option>
          <option value="07:00 AM">07:00 AM</option>
          <option value="07:30 AM">07:30 AM</option>
          <option value="08:00 AM">08:00 AM</option>
          <option value="08:30 AM">08:30 AM</option>
          <option value="09:00 AM">09:00 AM</option>
          <option value="09:30 AM">09:30 AM</option>
          <option value="10:00 AM">10:00 AM</option>
          <option value="10:30 AM">10:30 AM</option>
          <option value="11:00 AM">11:00 AM</option>
          <option value="11:30 AM">11:30 AM</option>
          <option value="12:00 PM">12:00 PM</option>
          <option value="12:30 PM">12:30 PM</option>
          <option value="01:00 PM">01:00 PM</option>
          <option value="01:30 PM">01:30 PM</option>
          <option value="02:00 PM">02:00 PM</option>
          <option value="02:30 PM">02:30 PM</option>
          <option value="03:00 PM">03:00 PM</option>
          <option value="03:30 PM">03:30 PM</option>
          <option value="04:00 PM">04:00 PM</option>
          <option value="04:30 PM">04:30 PM</option>
          <option value="05:00 PM">05:00 PM</option>
          <option value="05:30 PM">05:30 PM</option>
          <option value="06:00 PM">06:00 PM</option>
          <option value="06:30 PM">06:30 PM</option>
          <option value="07:00 PM">07:00 PM</option>
          <option value="07:30 PM">07:30 PM</option>
          <option value="08:00 PM">08:00 PM</option>
          <option value="08:30 PM">08:30 PM</option>
          <option value="09:00 PM">09:00 PM</option>
          <option value="09:30 PM">09:30 PM</option>
          <option value="10:00 PM">10:00 PM</option>
          <option value="10:30 PM">10:30 PM</option>
          <option value="11:00 PM">11:00 PM</option>
          <option value="11:30 PM">11:30 PM</option>
          </select>
        </div>
        </div>
        <div class="d-flex justify-content-end gap-2">
        <button id="clearSelectionBtn" class="btn btn-outline-secondary">
          Clear Selection
        </button>
        <button id="checkAvailabilityBtn" class="btn btn-primary">
          Check Availability
        </button>
        </div>
      </div>
      </div>
    </div>

    <!-- Single column for Reservation Details -->
    <div class="row">
      <div class="col-12">
      <div class="form-section-card">
        <h5>Reservation Details</h5>
        <div class="row">
        <div class="col-md-6">
          <input type="number" class="form-control mb-2" placeholder="No. of Participants" />
        </div>
        <div class="col-md-6">
          <select class="form-select mb-2" aria-label="Activity/Purpose">
          <option selected>Activity/Purpose:</option>
          <option value="1">Meeting</option>
          <option value="2">Workshop</option>
          <option value="3">Event</option>
          </select>
        </div>
        <div class="col-md-6">
          <textarea class="form-control mb-2" rows="2"
          placeholder="Write a brief description of the purpose of your requisition."></textarea>
        </div>
        <div class="col-md-6 mt-0">
          <textarea class="form-control mb-2" rows="2" placeholder="Other Purpose"></textarea>
        </div>
        <div class="col-md-6">
          <label class="form-label mt-3">Please attach formal letter.</label>
          <div class="position-relative">
          <input type="file" class="form-control mb-3" id="attachLetter"
            onchange="toggleRemoveButton('attachLetter', 'removeAttachLetterBtn')" />
          <button type="butt.on" id="removeAttachLetterBtn"
            class="btn btn-sm position-absolute top-50 end-0 translate-middle-y me-2 d-none"
            style="color: black; background: none; border: none"
            onclick="removeFile('attachLetter', 'removeAttachLetterBtn')">
            x
          </button>
          </div>
        </div>
        <div class="col-md-6">
          <textarea class="form-control mb-2" rows="3" placeholder="Additional Requests"></textarea>
        </div>
        </div>
      </div>
      </div>
    </div>

    <!-- Bottom 2-column grid -->
    <div class="row">
      <div class="col-md-6">
      <div class="form-section-card">
        <h5>Requested Facilities</h5>
        <div id="facilityList">
        <!-- Facility items will be dynamically added here -->
        </div>
      </div>
      </div>

      <div class="col-md-6">
      <div class="form-section-card">
        <h5>Requested Equipment</h5>
        <div id="equipmentList">
        <!-- Equipment items will be dynamically added here -->
        </div>
      </div>
      </div>
    </div>

    <div class="d-flex justify-content-center my-4">
      <button id="submitFormBtn" class="btn btn-primary me-2">
      Submit Form
      </button>
      <button type="reset" class="btn btn-secondary">Cancel</button>
    </div>
    </div>

    <div id="facilityPopup" class="popup">
    <h5>Select Available Facilities</h5>
    <p>
      This is a placeholder for facility selection content. Here you would
      list available facilities, possibly with a date/time filter.
    </p>
    <div class="d-flex justify-content-end">
      <button onclick="togglePopup('facilityPopup')" class="btn btn-danger">
      Close
      </button>
    </div>
    </div>

    <div id="equipmentPopup" class="popup">
    <h5>Select Equipment</h5>
    <p>
      This is a placeholder for equipment selection content. Here you would
      list available equipment based on the selected date/time.
    </p>
    <div class="d-flex justify-content-end">
      <button onclick="togglePopup('equipmentPopup')" class="btn btn-danger">
      Close
      </button>
    </div>
    </div>

  @endsection

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
  <script>
    function togglePopup(id) {
      const popup = document.getElementById(id);
      const overlay = document.getElementById("overlay");
      popup.classList.toggle("show");
      overlay.classList.toggle("show");
    }

    document.addEventListener("DOMContentLoaded", function () {
      $("#miniCalendar")
        .datepicker({
          format: "mm/dd/yyyy",
          todayHighlight: true,
          weekStart: 0, // Sunday
          autoclose: true,
        })
        .on("changeDate", function (e) {
          console.log("Selected date:", e.date);
        });
    });

    document.addEventListener("DOMContentLoaded", function () {
      const selectedDateTime = document.getElementById("selectedDateTime");
      const startDateField = document.getElementById("startDateField");
      const endDateField = document.getElementById("endDateField");
      const startTimeField = document.getElementById("startTimeField");
      const endTimeField = document.getElementById("endTimeField");
      const clearSelectionBtn = document.getElementById("clearSelectionBtn");

      function formatDateToLong(dateString) {
        const options = { year: "numeric", month: "long", day: "numeric" };
        const date = new Date(dateString);
        return date.toLocaleDateString("en-US", options);
      }

      function updateSelectedDateTime() {
        const startDate = startDateField.value;
        const endDate = endDateField.value;
        const startTime = startTimeField.value;
        const endTime = endTimeField.value;
        if (startDate && endDate && startTime && endTime) {
          const formattedStartDate = formatDateToLong(startDate);
          const formattedEndDate = formatDateToLong(endDate);
          selectedDateTime.textContent = `Selected: ${formattedStartDate} ${startTime} to ${formattedEndDate} ${endTime}`;
        } else {
          selectedDateTime.textContent = "No date and time selected.";
        }
      }

      startDateField.addEventListener("change", updateSelectedDateTime);
      endDateField.addEventListener("change", updateSelectedDateTime);
      startTimeField.addEventListener("change", updateSelectedDateTime);
      endTimeField.addEventListener("change", updateSelectedDateTime);

      clearSelectionBtn.addEventListener("click", function () {
        startDateField.value = "";
        endDateField.value = "";
        startTimeField.value = "";
        endTimeField.value = "";
        updateSelectedDateTime();
      });

      const toggleReservationBtn = document.getElementById(
        "toggleReservationBtn"
      );
      const reservationContent =
        document.getElementById("reservationContent");

      toggleReservationBtn.addEventListener("click", function () {
        if (
          reservationContent.style.maxHeight === "0px" ||
          !reservationContent.style.maxHeight
        ) {
          reservationContent.style.maxHeight =
            reservationContent.scrollHeight + "px";
          reservationContent.style.opacity = "1";
          toggleReservationBtn.innerHTML = '<i class="bi bi-chevron-up"></i>';
        } else {
          reservationContent.style.maxHeight = "0px";
          reservationContent.style.opacity = "0";
          toggleReservationBtn.innerHTML =
            '<i class="bi bi-chevron-down"></i>';
        }
      });

      // Initialize max-height for smooth animation
      reservationContent.style.maxHeight =
        reservationContent.scrollHeight + "px";
    });

    document.addEventListener("DOMContentLoaded", function () {
      const applicantType = document.getElementById("applicantType");
      const studentIdField = document.getElementById("studentIdField");

      applicantType.addEventListener("change", function () {
        if (applicantType.value === "External") {
          studentIdField.style.display = "none";
        } else {
          studentIdField.style.display = "block";
        }
      });
    });

    document.addEventListener("DOMContentLoaded", function () {
      const facilityList = document.getElementById("facilityList");
      const equipmentList = document.getElementById("equipmentList");
      const submitButton = document.querySelector("button[type='submit']");

      function saveSelectionsToLocalStorage() {
        const selectedFacilities = Array.from(
          facilityList.querySelectorAll(".facility-card")
        ).map((card) => card.querySelector("h6").textContent);
        const selectedEquipment = Array.from(
          equipmentList.querySelectorAll(".equipment-card")
        ).map((card) => card.querySelector("h6").textContent);

        localStorage.setItem(
          "selectedFacilities",
          JSON.stringify(selectedFacilities)
        );
        localStorage.setItem(
          "selectedEquipment",
          JSON.stringify(selectedEquipment)
        );

        // Trigger storage event for other pages
        const event = new Event("storage");
        window.dispatchEvent(event);
      }

      function updateEmptyMessage(list, message) {
        // Check if there are any cards in the container
        const hasCards = list.querySelector(
          ".facility-card, .equipment-card"
        );

        // Find or create the empty message element
        let emptyMessage = list.querySelector(".empty-message");

        if (!hasCards) {
          // If no cards and no message exists, create one
          if (!emptyMessage) {
            emptyMessage = document.createElement("p");
            emptyMessage.className = "text-muted empty-message";
            emptyMessage.textContent = message;
            list.appendChild(emptyMessage);
          } else {
            // If message exists, make sure it's visible
            emptyMessage.style.display = "block";
          }
        } else {
          // If there are cards, hide the message if it exists
          if (emptyMessage) {
            emptyMessage.style.display = "none";
          }
        }
      }

      function toggleSubmitButton() {
        const hasFacilities = facilityList.querySelector(".facility-card");
        const hasEquipment = equipmentList.querySelector(".equipment-card");
        submitButton.disabled = !(hasFacilities || hasEquipment);
      }

      facilityList.addEventListener("click", function (event) {

        if (event.target.closest(".btn-outline-danger")) {
          const card = event.target.closest(".facility-card");
          const roomSetupField = card.nextElementSibling; // Assume room setup field is directly after the card
          if (
            roomSetupField &&
            roomSetupField.classList.contains("attach-room-setup")
          ) {
            roomSetupField.remove();
          }
          card.remove();
          updateRoomSetupVisibility();
          updateEmptyMessage(facilityList, "No facility added yet.");
          toggleSubmitButton();
          saveSelectionsToLocalStorage();
        }
      });

      equipmentList.addEventListener("click", function (event) {
        if (event.target.closest(".btn-outline-danger")) {
          const card = event.target.closest(".equipment-card");
          card.remove();
          updateEmptyMessage(equipmentList, "No equipment added yet.");
          toggleSubmitButton();
          saveSelectionsToLocalStorage();
        }
      });

      function updateRoomSetupVisibility() {
        const facilityCards = facilityList.querySelectorAll(".facility-card");
        const attachRoomSetupFields =
          facilityList.querySelectorAll(".attach-room-setup");

        // Ensure each facility card has a corresponding room setup field
        facilityCards.forEach((card, index) => {
          if (attachRoomSetupFields[index]) {
            attachRoomSetupFields[index].style.display = "block";
          }
        });

        // Hide extra room setup fields if no corresponding facility card exists
        for (
          let i = facilityCards.length;
          i < attachRoomSetupFields.length;
          i++
        ) {
          attachRoomSetupFields[i].style.display = "none";
        }
      }

      // Initialize empty messages and submit button state
      updateEmptyMessage(facilityList, "No facility added yet.");
      updateEmptyMessage(equipmentList, "No equipment added yet.");
      toggleSubmitButton();
      updateRoomSetupVisibility();
      saveSelectionsToLocalStorage();
    });

    function removeFile(inputId, buttonId) {
      const inputField = document.getElementById(inputId);
      const button = document.getElementById(buttonId);
      if (inputField) {
        inputField.value = ""; // Clear the file input
        if (button) {
          button.classList.add("d-none"); // Hide the 'x' button
        }
      }
    }

    function toggleRemoveButton(inputId, buttonId) {
      const inputField = document.getElementById(inputId);
      const button = document.getElementById(buttonId);
      if (inputField && button) {
        if (inputField.value) {
          button.classList.remove("d-none"); // Show the 'x' button
        } else {
          button.classList.add("d-none"); // Hide the 'x' button
        }
      }
    }

    function adjustEndTime() {
      const startTimeField = document.getElementById("startTimeField");
      const endTimeField = document.getElementById("endTimeField");

      if (startTimeField && endTimeField) {
        const startTimeIndex = startTimeField.selectedIndex;
        if (startTimeIndex !== -1) {
          endTimeField.selectedIndex = Math.min(
            startTimeIndex + 2,
            endTimeField.options.length - 1
          ); // Ensure at least 1 hour gap
        }
      }
    }

    document
      .getElementById("submitFormBtn")
      .addEventListener("click", async (e) => {
        e.preventDefault();
        const facilities = Array.from(
          document.querySelectorAll("#facilityList .facility-card")
        ).map((card) => ({
          facility_id: card.querySelector("h6").textContent, // Replace with actual facility ID
          type: "facility",
        }));
        const equipment = Array.from(
          document.querySelectorAll("#equipmentList .equipment-card")
        ).map((card) => ({
          facility_id: card.querySelector("h6").textContent, // Replace with actual equipment ID
          type: "equipment",
        }));

        const data = [...facilities, ...equipment];

        try {
          await fetch("http://127.0.0.1:8000/api/requisition/add-item", {
            method: "POST",
            headers: {
              "Content-Type": "application/json",
            },
            body: JSON.stringify(data),
          });
          alert("Form submitted successfully!");
        } catch (error) {
          console.error("Error:", error);
        }
      });

    // Listen for storage events to handle updates from the booking catalog
    window.addEventListener("storage", async () => {
      const selectedFacilities =
        JSON.parse(localStorage.getItem("selectedFacilities")) || [];
      const selectedEquipment =
        JSON.parse(localStorage.getItem("selectedEquipment")) || [];

      const facilityList = document.getElementById("facilityList");
      const equipmentList = document.getElementById("equipmentList");

      // Clear existing items
      facilityList.innerHTML = "";
      equipmentList.innerHTML = "";

      // Fetch all facilities and equipment data from the API
      let facilitiesData = [];
      try {
        const response = await fetch("http://127.0.0.1:8000/api/facilities");
        const data = await response.json();
        facilitiesData = data.data || [];
      } catch (error) {
        console.error("Failed to fetch facilities:", error);
      }

      // Add facilities to the Requested Facilities container
      selectedFacilities.forEach((facilityId) => {
        const facility = facilitiesData.find(
          (f) => f.facility_id === parseInt(facilityId)
        );
        if (facility) {
          const facilityCard = document.createElement("div");
          facilityCard.className = "facility-card";
          facilityCard.innerHTML = `
              <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i>
              </button>
              <div class="facility-details">
                <h6 class="mb-1">${facility.facility_name}</h6>
                <p class="text-muted mb-1">${facility.description || "No description available."
            }</p>
              </div>
            `;
          facilityList.appendChild(facilityCard);
        }
      });

      // Add equipment to the Requested Equipment container
      selectedEquipment.forEach((equipmentId) => {
        const equipment = facilitiesData.find(
          (e) => e.facility_id === parseInt(equipmentId)
        ); // Replace with actual equipment API if available
        if (equipment) {
          const equipmentCard = document.createElement("div");
          equipmentCard.className = "equipment-card";
          equipmentCard.innerHTML = `
              <button class="btn btn-outline-danger btn-sm">
                <i class="bi bi-trash"></i>
              </button>
              <div class="equipment-details">
                <h6 class="mb-1">${equipment.facility_name}</h6>
                <p class="text-muted mb-1">${equipment.description || "No description available."
            }</p>
              </div>
            `;
          equipmentList.appendChild(equipmentCard);
        }
      });
    });

    // Trigger storage event manually on page load to populate containers
    window.dispatchEvent(new Event("storage"));

    document.addEventListener('DOMContentLoaded', function () {
      const dropdownToggle = document.querySelectorAll('.dropdown-toggle');
      dropdownToggle.forEach(function (toggle) {
        toggle.addEventListener('click', function (event) {
          event.preventDefault();
          const dropdownMenu = this.nextElementSibling;
          dropdownMenu.classList.toggle('show');
        });
      });
    });
  </script>
</body>

</html>