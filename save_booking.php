
<?php

header("Content-Type: application/json; charset=utf-8");

function respond($status, $success, $message, $extra = []) {
    http_response_code($status);

    echo json_encode(
        array_merge([
            "success" => $success,
            "message" => $message
        ], $extra)
    );

    exit;
}

// Allow POST requests only.
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
    respond(405, false, "Please submit the booking form.");
}

// Connect to MySQL.
require_once __DIR__ . "/includes/db.php";

// Get submitted form values.
$name = trim($_POST["customer_name"] ?? "");
$phone = trim($_POST["phone"] ?? "");
$event = trim($_POST["event_type"] ?? "");

$guests = filter_var(
    $_POST["guest_count"] ?? null,
    FILTER_VALIDATE_INT
);

$date = trim($_POST["event_date"] ?? "");
$time = trim($_POST["event_time"] ?? "");
$venue = trim($_POST["venue"] ?? "");
$package = trim($_POST["package"] ?? "");
$notes = trim($_POST["notes"] ?? "");

// Allowed event categories.
$allowedEvents = [
    "Birthday",
    "Wedding",
    "Engagement",
    "Anniversary",
    "Other"
];

// Allowed decoration styles.
$allowedPackages = [
    "Basic",
    "Standard",
    "Premium",
    "Luxury",
    "Classic",
    "Elegant"
];

// Validate customer name.
if (
    $name === "" ||
    strlen($name) > 100 ||
    !preg_match("/^[\p{L}\s.'-]+$/u", $name)
) {
    respond(422, false, "Please enter a valid full name.");
}

// Validate phone number.
if (!preg_match("/^[0-9]{10}$/", $phone)) {
    respond(422, false, "Please enter a 10-digit phone number.");
}

// Validate event type.
if (!in_array($event, $allowedEvents, true)) {
    respond(422, false, "Please select a valid event type.");
}

// Validate guest count.
if (
    $guests === false ||
    $guests < 1 ||
    $guests > 10000
) {
    respond(422, false, "Guest count must be between 1 and 10000.");
}

// Validate event date.
$dateObject = DateTime::createFromFormat("!Y-m-d", $date);

if (
    !$dateObject ||
    $dateObject->format("Y-m-d") !== $date ||
    $date < date("Y-m-d")
) {
    respond(422, false, "Please select today or a future date.");
}

// Validate event time.
if (!preg_match("/^(?:[01]\d|2[0-3]):[0-5]\d$/", $time)) {
    respond(422, false, "Please select a valid event time.");
}

// Validate venue.
if ($venue === "" || strlen($venue) > 255) {
    respond(422, false, "Please enter the event venue.");
}

// Validate decoration package.
if (!in_array($package, $allowedPackages, true)) {
    respond(422, false, "Please select a decoration style.");
}

// Validate additional requirements.
if (strlen($notes) > 5000) {
    respond(
        422,
        false,
        "Additional requirements must be under 5000 characters."
    );
}

try {
    // Insert the booking into MySQL.
    $sql = "INSERT INTO bookings
        (
            customer_name,
            phone,
            event_type,
            guest_count,
            event_date,
            event_time,
            venue,
            package,
            notes
        )
        VALUES
        (
            :name,
            :phone,
            :event,
            :guests,
            :event_date,
            :event_time,
            :venue,
            :package,
            :notes
        )";

    $stmt = $pdo->prepare($sql);

    $stmt->execute([
        ":name"       => $name,
        ":phone"      => $phone,
        ":event"      => $event,
        ":guests"     => $guests,
        ":event_date" => $date,
        ":event_time" => $time . ":00",
        ":venue"      => $venue,
        ":package"    => $package,
        ":notes"      => $notes
    ]);

    // Get the ID of the newly saved booking.
    $bookingId = (int) $pdo->lastInsertId();

    // Return success and booking ID to booking.php.
    respond(
        200,
        true,
        "Your booking request was submitted successfully!",
        [
            "booking_id" => $bookingId
        ]
    );

} catch (PDOException $e) {
    // Log the technical error without exposing database details.
    error_log($e->getMessage());

    respond(
        500,
        false,
        "Unable to save your booking. Please check the database table."
    );
}
?>
