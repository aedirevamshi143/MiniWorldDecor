
<?php
session_start();

if (empty($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . "/includes/db.php";

$message = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $action = $_POST["action"] ?? "";

    if ($action === "add") {
        $name = trim($_POST["event_name"] ?? "");
        $type = $_POST["event_type"] ?? "";
        $description = trim($_POST["description"] ?? "");
        $price = $_POST["price"] ?? "";
        $image = trim($_POST["image_url"] ?? "");

        $allowedTypes = [
            "Birthday", "Wedding", "Engagement",
            "Anniversary", "Other"
        ];

        if (
            $name === "" ||
            !in_array($type, $allowedTypes, true) ||
            !is_numeric($price) ||
            (float)$price < 0
        ) {
            $message = "Please enter valid event details and a non-negative price.";
        } else {
            $stmt = $pdo->prepare(
                "INSERT INTO events
                (event_name, event_type, description, price, image_url)
                VALUES (?, ?, ?, ?, ?)"
            );

            $stmt->execute([
                $name, $type, $description,
                (float)$price, $image
            ]);

            header("Location: manage_events.php?added=1");
            exit;
        }
    } elseif ($action === "delete") {
        $id = filter_input(INPUT_POST, "event_id", FILTER_VALIDATE_INT);

        if ($id) {
            $stmt = $pdo->prepare("DELETE FROM events WHERE id = ?");
            $stmt->execute([$id]);
            header("Location: manage_events.php?deleted=1");
            exit;
        }

        $message = "Invalid event ID.";
    }
}

if (isset($_GET["added"])) {
    $message = "Event added successfully!";
} elseif (isset($_GET["deleted"])) {
    $message = "Event deleted successfully!";
}

$events = $pdo->query(
    "SELECT * FROM events ORDER BY created_at DESC"
)->fetchAll();

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>Manage Events | Mini World Decor</title>
<style>
* { box-sizing: border-box; }

body {
    margin: 0;
    padding: 25px;
    background: #f7f0e8;
    color: #3b2520;
    font-family: Arial, sans-serif;
}

.container {
    max-width: 1100px;
    margin: auto;
}

header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    flex-wrap: wrap;
    gap: 12px;
    margin-bottom: 25px;
}

a, button { cursor: pointer; }

.link {
    display: inline-block;
    padding: 10px 14px;
    margin: 3px;
    border-radius: 6px;
    text-decoration: none;
    background: #b88a44;
    color: white;
    border: none;
}

.panel {
    background: white;
    padding: 24px;
    margin-bottom: 25px;
    border-radius: 12px;
    box-shadow: 0 4px 15px #0000000d;
}

.form-grid {
    display: grid;
    grid-template-columns: repeat(2, minmax(0, 1fr));
    gap: 16px;
}

label {
    display: block;
    margin-bottom: 6px;
    font-weight: bold;
}

input, select, textarea {
    width: 100%;
    padding: 11px;
    border: 1px solid #d6c9bd;
    border-radius: 6px;
    font: inherit;
}

textarea { min-height: 90px; }

.full { grid-column: 1 / -1; }

button {
    padding: 11px 16px;
    background: #b88a44;
    color: white;
    border: none;
    border-radius: 6px;
    font-weight: bold;
}

.message {
    background: #e6f6e8;
    padding: 12px;
    border-radius: 6px;
    margin-bottom: 16px;
}

.error { background: #fde8e8; }

.table-wrap { overflow-x: auto; }

table {
    width: 100%;
    border-collapse: collapse;
    min-width: 650px;
}

th, td {
    padding: 12px;
    text-align: left;
    border-bottom: 1px solid #eee2d7;
}

th { background: #f4e9db; }

.delete { background: #a93226; }

@media (max-width: 600px) {
    body { padding: 12px; }
    .form-grid { grid-template-columns: 1fr; }
    .full { grid-column: auto; }
}
</style>
</head>
<body>
<div class="container">
    <header>
        <h1>Manage Events</h1>
        <nav>
            <a class="link" href="admin.php">Dashboard</a>
            <a class="link" href="index.php">View Website</a>
            <a class="link" href="admin_logout.php">Logout</a>
        </nav>
    </header>

    <?php if ($message !== ""): ?>
        <div class="message <?= str_starts_with($message, "Please") || str_starts_with($message, "Invalid") ? "error" : "" ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h2>Add Decoration Event</h2>

        <form method="POST">
            <input type="hidden" name="action" value="add">

            <div class="form-grid">
                <div>
                    <label for="event_name">Event Name</label>
                    <input id="event_name" name="event_name"
                           maxlength="100" required>
                </div>

                <div>
                    <label for="event_type">Event Type</label>
                    <select id="event_type" name="event_type" required>
                        <option value="">Select type</option>
                        <option>Birthday</option>
                        <option>Wedding</option>
                        <option>Engagement</option>
                        <option>Anniversary</option>
                        <option>Other</option>
                    </select>
                </div>

                <div>
                    <label for="price">Starting Price (₹)</label>
                    <input id="price" type="number" name="price"
                           min="0" step="0.01" required>
                </div>

                <div>
                    <label for="image_url">Image URL (optional)</label>
                    <input id="image_url" type="url" name="image_url"
                           maxlength="255" placeholder="https://example.com/image.jpg">
                </div>

                <div class="full">
                    <label for="description">Description</label>
                    <textarea id="description" name="description"
                              placeholder="Describe the decoration package"></textarea>
                </div>

                <div class="full">
                    <button type="submit">Add Event</button>
                </div>
            </div>
        </form>
    </section>

    <section class="panel">
        <h2>Existing Events</h2>

        <div class="table-wrap">
        <table>
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Event Name</th>
                    <th>Type</th>
                    <th>Price</th>
                    <th>Description</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
            <?php if (!$events): ?>
                <tr>
                    <td colspan="6">No events added yet.</td>
                </tr>
            <?php else: ?>
                <?php foreach ($events as $event): ?>
                <tr>
                    <td><?= e($event["id"]) ?></td>
                    <td><?= e($event["event_name"]) ?></td>
                    <td><?= e($event["event_type"]) ?></td>
                    <td>₹<?= number_format((float)$event["price"], 2) ?></td>
                    <td><?= e($event["description"]) ?></td>
                    <td>
                        <form method="POST"
                              onsubmit="return confirm('Delete this event?');">
                            <input type="hidden" name="action" value="delete">
                            <input type="hidden" name="event_id"
                                   value="<?= e($event["id"]) ?>">
                            <button class="delete" type="submit">Delete</button>
                        </form>
                    </td>
                </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            </tbody>
        </table>
        </div>
    </section>
</div>
</body>
</html>
