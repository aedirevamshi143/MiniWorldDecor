
<?php
require_once __DIR__ . "/includes/db.php";

$id = filter_input(INPUT_GET, "id", FILTER_VALIDATE_INT);

if (!$id || $id < 1) {
    http_response_code(400);
    exit("Invalid event ID.");
}

$stmt = $pdo->prepare(
    "SELECT id, event_name, event_type, description, price, image_url
     FROM events
     WHERE id = :id"
);
$stmt->execute(["id" => $id]);
$event = $stmt->fetch();

if (!$event) {
    http_response_code(404);
    exit("Event not found.");
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
    <title><?= e($event["event_name"]) ?> | Mini World Decor</title>
    <link rel="stylesheet" href="css/style.css">

    <style>
        .details-container {
            max-width: 1000px;
            margin: 50px auto;
            padding: 25px;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 35px;
            align-items: center;
            background: #fffaf3;
            border-radius: 14px;
        }

        .details-image {
            width: 100%;
            max-height: 430px;
            object-fit: cover;
            border-radius: 12px;
        }

        .details-content h1 {
            color: #6b4226;
        }

        .details-price {
            color: #80552b;
            font-size: 24px;
            font-weight: bold;
        }

        .details-book-btn {
            display: inline-block;
            padding: 13px 24px;
            background: #a77b36;
            color: white;
            border-radius: 6px;
            text-decoration: none;
        }

        .details-book-btn:hover {
            background: #80552b;
        }

        .back-link {
            display: inline-block;
            margin-top: 20px;
            color: #80552b;
        }

        @media (max-width: 700px) {
            .details-container {
                grid-template-columns: 1fr;
                margin: 20px;
            }
        }
    </style>
</head>
<body>

    <main class="details-container">
        <div>
            <?php if (!empty($event["image_url"])): ?>
                <img
                    class="details-image"
                    src="<?= e($event["image_url"]) ?>"
                    alt="<?= e($event["event_name"]) ?>"
                >
            <?php endif; ?>
        </div>

        <div class="details-content">
            <h1><?= e($event["event_name"]) ?></h1>

            <p><strong>Category:</strong> <?= e($event["event_type"]) ?></p>

            <p><?= nl2br(e($event["description"])) ?></p>

            <p class="details-price">
                Starting at ₹<?= number_format((float)$event["price"], 2) ?>
            </p>

            <a
                class="details-book-btn"
                href="booking.php?event_type=<?= rawurlencode($event["event_type"]) ?>"
            >
                Book This Event
            </a>

            <br>

            <a class="back-link" href="index.php">
                &larr; Back to Home
            </a>
        </div>
    </main>

</body>
</html>
