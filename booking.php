
<?php
// Read the selected event from the URL.
$allowedEventTypes = [
    "Birthday",
    "Wedding",
    "Engagement",
    "Anniversary",
    "Other"
];

$selectedEvent = trim($_GET["event_type"] ?? "");

// Normalize event names in case the URL contains a longer event name.
$eventAliases = [
    "birthday celebration" => "Birthday",
    "wedding decoration" => "Wedding",
    "engagement decoration" => "Engagement",
    "anniversary decoration" => "Anniversary"
];

$aliasKey = strtolower($selectedEvent);

if (isset($eventAliases[$aliasKey])) {
    $selectedEvent = $eventAliases[$aliasKey];
} else {
    foreach ($allowedEventTypes as $allowedType) {
        if (strcasecmp($selectedEvent, $allowedType) === 0) {
            $selectedEvent = $allowedType;
            break;
        }
    }
}

if (!in_array($selectedEvent, $allowedEventTypes, true)) {
    $selectedEvent = "";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Book Your Event | Mini World Decor</title>

    <link rel="stylesheet" href="css/style.css">

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            background: #fbf8f2;
            color: #333;
            font-family: Arial, sans-serif;
        }

        .top-nav {
            padding: 22px;
            text-align: center;
            background: #fff;
        }

        .top-nav a {
            color: #a67c42;
            text-decoration: none;
            font-weight: bold;
            margin: 0 8px;
        }

        .booking-container {
            max-width: 950px;
            margin: 40px auto;
            padding: 35px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 5px 25px rgba(0,0,0,.08);
        }

        .booking-heading {
            text-align: center;
            margin-bottom: 30px;
        }

        .booking-heading h1 {
            color: #a67c42;
            font-size: 34px;
            margin-bottom: 10px;
        }

        .booking-heading p {
            color: #777;
            line-height: 1.6;
        }

        .booking-form {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 22px;
        }

        .form-group {
            display: flex;
            flex-direction: column;
            gap: 8px;
            min-width: 0;
        }

        .full-width {
            grid-column: 1 / -1;
        }

        .form-group label {
            font-size: 14px;
            font-weight: bold;
        }

        .form-group input,
        .form-group select,
        .form-group textarea {
            width: 100%;
            padding: 13px;
            border: 1px solid #ddd0bc;
            border-radius: 7px;
            background: white;
            font: inherit;
        }

        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            outline: none;
            border-color: #a67c42;
        }

        .form-group textarea {
            min-height: 110px;
            resize: vertical;
        }

        .submit-button {
            grid-column: 1 / -1;
            padding: 15px;
            background: #a67c42;
            color: white;
            border: none;
            border-radius: 7px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
        }

        .submit-button:hover {
            background: #805c2c;
        }

        .submit-button:disabled {
            opacity: .6;
            cursor: wait;
        }

        #bookingMessage {
            margin-top: 20px;
            text-align: center;
            font-weight: bold;
            line-height: 1.6;
        }

        .back-link {
            display: block;
            text-align: center;
            margin-top: 20px;
            color: #a67c42;
            text-decoration: none;
        }

        @media (max-width: 650px) {
            .booking-container {
                margin: 20px 12px;
                padding: 22px 16px;
            }

            .booking-form {
                grid-template-columns: 1fr;
            }

            .full-width,
            .submit-button {
                grid-column: auto;
            }

            .booking-heading h1 {
                font-size: 27px;
            }
        }
    </style>
</head>

<body>

<nav class="top-nav">
    <a href="index.php">MINI WORLD DECOR</a>
    |
    <a href="index.php">Home</a>
</nav>

<main class="booking-container">

    <div class="booking-heading">
        <h1>Book Your Event</h1>

        <p>
            We decorate your dreams into reality.
            Share your requirements for your special celebration.
        </p>
    </div>

    <form
        id="bookingForm"
        class="booking-form"
        method="POST"
        action="save_booking.php"
    >

        <div class="form-group">
            <label for="customer_name">Full Name *</label>

            <input
                type="text"
                id="customer_name"
                name="customer_name"
                maxlength="100"
                placeholder="Enter your full name"
                required
            >
        </div>

        <div class="form-group">
            <label for="phone">Phone Number *</label>

            <input
                type="tel"
                id="phone"
                name="phone"
                pattern="[0-9]{10}"
                maxlength="10"
                inputmode="numeric"
                placeholder="Enter 10-digit phone number"
                required
            >
        </div>

        <div class="form-group">
            <label for="event_type">Event Type *</label>

            <select id="event_type" name="event_type" required>
                <option value="">Select Event Type</option>

                <option value="Birthday"
                    <?= $selectedEvent === "Birthday" ? "selected" : "" ?>>
                    Birthday
                </option>

                <option value="Wedding"
                    <?= $selectedEvent === "Wedding" ? "selected" : "" ?>>
                    Wedding
                </option>

                <option value="Engagement"
                    <?= $selectedEvent === "Engagement" ? "selected" : "" ?>>
                    Engagement
                </option>

                <option value="Anniversary"
                    <?= $selectedEvent === "Anniversary" ? "selected" : "" ?>>
                    Anniversary
                </option>

                <option value="Other"
                    <?= $selectedEvent === "Other" ? "selected" : "" ?>>
                    Other
                </option>
            </select>
        </div>

        <div class="form-group">
            <label for="guest_count">Number of Guests *</label>

            <input
                type="number"
                id="guest_count"
                name="guest_count"
                min="1"
                max="10000"
                placeholder="Enter guest count"
                required
            >
        </div>

        <div class="form-group">
            <label for="event_date">Event Date *</label>

            <input
                type="date"
                id="event_date"
                name="event_date"
                required
            >
        </div>

        <div class="form-group">
            <label for="event_time">Event Time *</label>

            <input
                type="time"
                id="event_time"
                name="event_time"
                required
            >
        </div>

        <div class="form-group full-width">
            <label for="venue">Event Venue / Address *</label>

            <input
                type="text"
                id="venue"
                name="venue"
                maxlength="255"
                placeholder="Enter event venue or address"
                required
            >
        </div>

        <div class="form-group full-width">
            <label for="package">Decoration Style *</label>

            <select id="package" name="package" required>
                <option value="">Select decoration style</option>
                <option value="Basic">Basic Decoration</option>
                <option value="Standard">Standard Decoration</option>
                <option value="Premium">Premium Decoration</option>
                <option value="Luxury">Luxury Decoration</option>
                <option value="Classic">Classic Decoration</option>
                <option value="Elegant">Elegant Decoration</option>
            </select>
        </div>

        <div class="form-group full-width">
            <label for="notes">Additional Requirements</label>

            <textarea
                id="notes"
                name="notes"
                maxlength="5000"
                placeholder="Describe your preferred theme, colors, or budget"
            ></textarea>
        </div>

        <button
            type="submit"
            id="submitButton"
            class="submit-button"
        >
            Submit Booking
        </button>

    </form>

    <p id="bookingMessage" role="status" aria-live="polite"></p>

    <a href="index.php" class="back-link">
        &larr; Back to Home
    </a>

</main>

<script>
const form = document.getElementById("bookingForm");
const message = document.getElementById("bookingMessage");
const submitButton = document.getElementById("submitButton");
const dateInput = document.getElementById("event_date");

function getLocalDate() {
    const now = new Date();
    const year = now.getFullYear();
    const month = String(now.getMonth() + 1).padStart(2, "0");
    const day = String(now.getDate()).padStart(2, "0");

    return `${year}-${month}-${day}`;
}

dateInput.min = getLocalDate();

form.addEventListener("submit", async function (event) {
    event.preventDefault();

    message.textContent = "Submitting your booking...";
    message.style.color = "#805c2c";
    submitButton.disabled = true;

    try {
        const response = await fetch("save_booking.php", {
            method: "POST",
            body: new FormData(form)
        });

        const result = await response.json();

        if (
            response.ok &&
            result.success === true &&
            result.booking_id
        ) {
            message.textContent = "Booking successful! Opening confirmation...";
            message.style.color = "green";

            // Redirect to the confirmation page using the saved booking ID.
            window.location.href =
                "booking_confirmation.php?id=" +
                encodeURIComponent(result.booking_id);

            return;
        }

        message.textContent =
            result.message || "Unable to save your booking. Please try again.";

        message.style.color = "red";

    } catch (error) {
        message.textContent =
            "Unable to process the response. Please check save_booking.php and try again.";

        message.style.color = "red";

    } finally {
        submitButton.disabled = false;
    }
});
</script>

</body>
</html>
