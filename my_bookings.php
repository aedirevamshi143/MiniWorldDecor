
<?php
require_once __DIR__ . "/includes/db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

$phone = "";
$bookings = [];
$error = "";
$searched = false;

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $phone = trim($_POST["phone"] ?? "");
    $searched = true;

    if (!preg_match('/^[0-9]{10}$/', $phone)) {
        $error = "Please enter a valid 10-digit phone number.";
    } else {
        try {
            $stmt = $pdo->prepare(
                "SELECT id, customer_name, event_type, guest_count,
                        event_date, event_time, venue, package, notes,
                        status, created_at
                 FROM bookings
                 WHERE phone = :phone
                 ORDER BY created_at DESC"
            );

            $stmt->execute(["phone" => $phone]);
            $bookings = $stmt->fetchAll();

        } catch (PDOException $ex) {
            error_log($ex->getMessage());
            $error = "Unable to retrieve bookings right now.";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>My Bookings | Mini World Decor</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #faf6ef;
            color: #30251d;
        }

        header {
            background: #fff;
            padding: 20px 7%;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            box-shadow: 0 2px 10px #0000000d;
        }

        .logo {
            font-size: 23px;
            font-weight: bold;
            color: #a47735;
        }

        nav {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }

        nav a {
            text-decoration: none;
            color: #49392b;
            font-size: 14px;
        }

        nav a:hover {
            color: #a47735;
        }

        .hero {
            text-align: center;
            padding: 45px 15px 25px;
        }

        .hero h1 {
            margin-bottom: 10px;
            font-size: 34px;
        }

        .hero p {
            color: #76685a;
            line-height: 1.6;
        }

        .search-box {
            background: white;
            max-width: 480px;
            margin: 15px auto 35px;
            padding: 28px;
            border-radius: 12px;
            box-shadow: 0 5px 22px #0000000b;
        }

        label {
            display: block;
            font-weight: bold;
            margin-bottom: 10px;
        }

        input {
            width: 100%;
            padding: 13px;
            border: 1px solid #d8cbb9;
            border-radius: 6px;
            font-size: 15px;
            margin-bottom: 15px;
        }

        button {
            width: 100%;
            padding: 14px;
            background: #a47735;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 15px;
            font-weight: bold;
            cursor: pointer;
        }

        button:hover {
            background: #805b27;
        }

        .message {
            max-width: 900px;
            margin: 0 auto 20px;
            padding: 15px;
            border-radius: 7px;
            text-align: center;
        }

        .error {
            background: #ffe9e9;
            color: #a32222;
        }

        .success {
            background: #e9f7e9;
            color: #246b31;
        }

        .results {
            max-width: 1100px;
            margin: 0 auto 45px;
            padding: 0 18px;
        }

        .results h2 {
            margin-bottom: 20px;
        }

        .booking-card {
            background: white;
            padding: 24px;
            border-radius: 12px;
            margin-bottom: 20px;
            border: 1px solid #eee3d4;
            box-shadow: 0 4px 15px #00000008;
        }

        .booking-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 12px;
            margin-bottom: 18px;
        }

        .booking-top h3 {
            margin: 0;
            color: #8e642e;
        }

        .status {
            display: inline-block;
            padding: 7px 12px;
            border-radius: 20px;
            background: #fff0d6;
            color: #805c2c;
            font-size: 13px;
            font-weight: bold;
        }

        .status-confirmed {
            background: #e2f5e5;
            color: #246b31;
        }

        .status-completed {
            background: #e5eaff;
            color: #314ca0;
        }

        .status-cancelled {
            background: #ffe6e6;
            color: #a32222;
        }

        .details {
            display: grid;
            grid-template-columns: repeat(2, minmax(0, 1fr));
            gap: 18px;
        }

        .detail {
            overflow-wrap: anywhere;
            line-height: 1.6;
        }

        .detail strong {
            display: block;
            color: #75675a;
            font-size: 13px;
            margin-bottom: 4px;
        }

        .empty {
            text-align: center;
            background: white;
            padding: 28px;
            border-radius: 10px;
            line-height: 1.7;
        }

        footer {
            background: #30251d;
            color: white;
            text-align: center;
            padding: 22px;
        }

        @media (max-width: 600px) {
            header {
                padding: 18px;
            }

            .hero h1 {
                font-size: 27px;
            }

            .details {
                grid-template-columns: 1fr;
            }

            .search-box {
                margin-left: 15px;
                margin-right: 15px;
            }
        }
    </style>
</head>

<body>

<header>
    <div class="logo">Mini World Decor</div>

    <nav>
        <a href="index.php">Home</a>
        <a href="index.php#events">Events</a>
        <a href="booking.php">Book Now</a>
        <a href="my_bookings.php">My Bookings</a>
    </nav>
</header>

<section class="hero">
    <h1>My Bookings</h1>
    <p>View your event details and check your booking status.</p>
</section>

<section class="search-box">
    <form method="POST" action="my_bookings.php">
        <label for="phone">Registered Phone Number</label>

        <input
            type="tel"
            id="phone"
            name="phone"
            placeholder="Enter your 10-digit phone number"
            pattern="[0-9]{10}"
            maxlength="10"
            minlength="10"
            inputmode="numeric"
            value="<?= e($phone) ?>"
            required
        >

        <button type="submit">Find My Bookings</button>
    </form>
</section>

<?php if ($error !== ""): ?>
    <div class="message error">
        <?= e($error) ?>
    </div>
<?php endif; ?>

<?php if ($searched && $error === ""): ?>
    <section class="results">

        <?php if (count($bookings) > 0): ?>

            <h2>Your Booking History</h2>

            <?php foreach ($bookings as $booking): ?>
                <?php
                    $status = $booking["status"] ?: "Pending";

                    $statusClass = strtolower(
                        preg_replace(
                            '/[^a-zA-Z0-9_-]/',
                            '',
                            $status
                        )
                    );
                ?>

                <div class="booking-card">
                    <div class="booking-top">
                        <h3>
                            Booking #<?= e($booking["id"]) ?>
                            — <?= e($booking["event_type"]) ?>
                        </h3>

                        <span class="status status-<?= e($statusClass) ?>">
                            <?= e($status) ?>
                        </span>
                    </div>

                    <div class="details">

                        <div class="detail">
                            <strong>Customer Name</strong>
                            <?= e($booking["customer_name"]) ?>
                        </div>

                        <div class="detail">
                            <strong>Event Type</strong>
                            <?= e($booking["event_type"]) ?>
                        </div>

                        <div class="detail">
                            <strong>Event Date</strong>
                            <?= e(date(
                                "d M Y",
                                strtotime($booking["event_date"])
                            )) ?>
                        </div>

                        <div class="detail">
                            <strong>Event Time</strong>
                            <?= e(date(
                                "h:i A",
                                strtotime($booking["event_time"])
                            )) ?>
                        </div>

                        <div class="detail">
                            <strong>Guest Count</strong>
                            <?= e($booking["guest_count"]) ?> guests
                        </div>

                        <div class="detail">
                            <strong>Venue</strong>
                            <?= e($booking["venue"]) ?>
                        </div>

                        <div class="detail">
                            <strong>Package</strong>
                            <?= e($booking["package"] ?: "Not specified") ?>
                        </div>

                        <div class="detail">
                            <strong>Booking Submitted</strong>
                            <?= e(date(
                                "d M Y, h:i A",
                                strtotime($booking["created_at"])
                            )) ?>
                        </div>

                        <?php if (!empty($booking["notes"])): ?>
                            <div class="detail">
                                <strong>Additional Notes</strong>
                                <?= nl2br(e($booking["notes"])) ?>
                            </div>
                        <?php endif; ?>

                    </div>
                </div>

            <?php endforeach; ?>

        <?php else: ?>

            <div class="empty">
                <h2>No Bookings Found</h2>
                <p>
                    We could not find bookings for this phone number.
                    Please check the number you entered.
                </p>
                <a href="booking.php">Book Your Event</a>
            </div>

        <?php endif; ?>

    </section>
<?php endif; ?>

<footer>
    <p>&copy; <?= date("Y") ?> Mini World Decor. All rights reserved.</p>
</footer>

</body>
</html>
