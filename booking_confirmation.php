
<?php
session_start();
require_once __DIR__ . "/includes/db.php";

$bookingId = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$bookingId || $bookingId < 1) {
    http_response_code(400);
    exit("Invalid booking reference.");
}

$stmt = $pdo->prepare(
    "SELECT id, customer_name, event_type, event_date,
            event_time, venue, status
     FROM bookings
     WHERE id = :id"
);

$stmt->execute(["id" => $bookingId]);
$booking = $stmt->fetch();

if (!$booking) {
    http_response_code(404);
    exit("Booking not found.");
}

function e($value) {
    return htmlspecialchars((string)($value ?? ""), ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking Confirmation | Mini World Decor</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .confirmation-card {
            max-width: 650px;
            margin: 60px auto;
            padding: 35px;
            background: #fffaf3;
            border-radius: 14px;
            box-shadow: 0 4px 18px rgba(0,0,0,.10);
        }

        .confirmation-card h1 {
            color: #6b4226;
            text-align: center;
        }

        .success-icon {
            text-align: center;
            font-size: 48px;
            color: #26834a;
        }

        .booking-reference {
            padding: 14px;
            background: #f3e4cb;
            border-radius: 8px;
            text-align: center;
            font-size: 20px;
            font-weight: bold;
        }

        .booking-info p {
            padding: 8px 0;
            border-bottom: 1px solid #eadfce;
        }

        .confirmation-btn {
            display: inline-block;
            margin-top: 15px;
            padding: 12px 20px;
            background: #a77b36;
            color: white;
            text-decoration: none;
            border-radius: 6px;
        }

        @media (max-width: 700px) {
            .confirmation-card {
                margin: 20px;
                padding: 22px;
            }
        }
    </style>
</head>
<body>

<div class="confirmation-card">
    <div class="success-icon">&#10003;</div>

    <h1>Booking Received!</h1>

    <p style="text-align:center;">
        Thank you, <?= e($booking["customer_name"]) ?>.
        Your decoration booking has been received.
    </p>

    <div class="booking-reference">
        Booking Reference: #<?= (int)$booking["id"] ?>
    </div>

    <div class="booking-info">
        <p><strong>Event:</strong> <?= e($booking["event_type"]) ?></p>

        <p><strong>Date:</strong>
            <?= e(date("d M Y", strtotime($booking["event_date"]))) ?>
        </p>

        <p><strong>Time:</strong>
            <?= e(date("h:i A", strtotime($booking["event_time"]))) ?>
        </p>

        <p><strong>Venue:</strong> <?= e($booking["venue"]) ?></p>

        <p><strong>Status:</strong> <?= e($booking["status"]) ?></p>
    </div>

    <p>Our team will review your booking and confirm the details.</p>

    <a class="confirmation-btn" href="index.php">Back to Home</a>
</div>

</body>
</html>
