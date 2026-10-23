
<?php
session_start();

if (empty($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . "/includes/db.php";

// Update booking status
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $id = filter_input(INPUT_POST, "booking_id", FILTER_VALIDATE_INT);
    $status = $_POST["status"] ?? "";

    $allowedStatuses = [
        "Pending", "Confirmed", "Rejected", "Completed"
    ];

    if ($id && in_array($status, $allowedStatuses, true)) {
        $stmt = $pdo->prepare(
            "UPDATE bookings SET status = :status WHERE id = :id"
        );
        $stmt->execute([
            ":status" => $status,
            ":id" => $id
        ]);
    }

    header("Location: admin.php");
    exit;
}

// Retrieve bookings
$stmt = $pdo->query(
    "SELECT * FROM bookings ORDER BY created_at DESC"
);
$bookings = $stmt->fetchAll();

function h($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | Mini World Decor</title>

    <style>
        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Arial, sans-serif;
            background: #fbf8f2;
            color: #333;
        }

        header {
            background: #b2dff4;
            color: white;
            padding: 22px 30px;
        }

        header h1 {
            margin: 0;
            color: #e4c28b;
        }

        header p { margin-bottom: 0; }

        .container {
            max-width: 1250px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .summary {
            background: white;
            padding: 22px;
            border-radius: 10px;
            margin-bottom: 25px;
            box-shadow: 0 3px 15px #0000000d;
        }

        .summary strong {
            font-size: 28px;
            color: #a67c42;
        }

        .table-container {
            background: white;
            border-radius: 10px;
            padding: 20px;
            overflow-x: auto;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 1050px;
        }

        th, td {
            padding: 13px;
            text-align: left;
            border-bottom: 1px solid #eee5d8;
            vertical-align: top;
        }

        th {
            background: #f2e7d6;
            color: #57432b;
        }

        select, button {
            padding: 9px;
            border-radius: 5px;
            border: 1px solid #d5c3a9;
        }

        button {
            background: #a67c42;
            color: white;
            border: none;
            cursor: pointer;
            margin-top: 6px;
        }

        .empty {
            text-align: center;
            padding: 30px;
            color: #777;
        }

        .home-link {
            display: inline-block;
            margin-top: 20px;
            color: #a67c42;
        }
    </style>
</head>

<body>

<header>
    <h1>Mini World Decor</h1>
    <p>Admin Dashboard — Manage Customer Bookings</p>
    
<div class="dashboard-header">
    <h1>Mini World Decor - Admin Dashboard</h1>

    <div class="header-links">
        <a href="manage_gallery.php">Manage Gallery</a>
        <a href="index.php">View Website</a>
        <a href="admin_logout.php">Logout</a>
        <a href="manage_events.php">Manage Events</a>
    </div>
</div>

</header>

<main class="container">

    <section class="summary">
        <h2>Total Bookings</h2>
        <strong><?= count($bookings) ?></strong>
    </section>

    <section class="table-container">
        <h2>Customer Bookings</h2>

        <?php if (count($bookings) === 0): ?>
            <p class="empty">No bookings found yet.</p>
        <?php else: ?>

        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Customer</th>
                    <th>Phone</th>
                    <th>Event</th>
                    <th>Guests</th>
                    <th>Date</th>
                    <th>Time</th>
                    <th>Venue</th>
                    <th>Decoration</th>
                    <th>Notes</th>
                    <th>Status</th>
                </tr>
            </thead>

            <tbody>
            <?php foreach ($bookings as $booking): ?>
                <tr>
                    <td><?= h($booking["id"]) ?></td>
                    <td><?= h($booking["customer_name"]) ?></td>
                    <td><?= h($booking["phone"]) ?></td>
                    <td><?= h($booking["event_type"]) ?></td>
                    <td><?= h($booking["guest_count"]) ?></td>
                    <td><?= h($booking["event_date"]) ?></td>
                    <td><?= h(substr($booking["event_time"], 0, 5)) ?></td>
                    <td><?= h($booking["venue"]) ?></td>
                    <td><?= h($booking["package"]) ?></td>
                    <td><?= h($booking["notes"]) ?></td>
                    <td>
                        <form method="POST" action="admin.php">
                            <input type="hidden"
                                   name="booking_id"
                                   value="<?= h($booking["id"]) ?>">

                            <select name="status">
                                <?php foreach (
                                    ["Pending", "Confirmed", "Rejected", "Completed"]
                                    as $option
                                ): ?>
                                    <option value="<?= h($option) ?>"
                                        <?= $booking["status"] === $option
                                            ? "selected" : "" ?>>
                                        <?= h($option) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>

                            <button type="submit">Update</button>
                        </form>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>

        <?php endif; ?>
    </section>

    <a class="home-link" href="index.php">
        &larr; Return to Website
    </a>

</main>
</body>
</html>
