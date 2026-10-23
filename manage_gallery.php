<?php
session_start();

if (empty($_SESSION["admin_id"])) {
    header("Location: admin_login.php");
    exit;
}

require_once __DIR__ . "/includes/db.php";

function e($value) {
    return htmlspecialchars((string)$value, ENT_QUOTES, "UTF-8");
}

if (empty($_SESSION["gallery_csrf"])) {
    $_SESSION["gallery_csrf"] = bin2hex(random_bytes(32));
}

$message = "";
$messageType = "success";

$imageDirectory = __DIR__ . "/images/";
$imageWebPath = "images/";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $csrfToken = $_POST["csrf_token"] ?? "";

    if (
        !is_string($csrfToken) ||
        !hash_equals($_SESSION["gallery_csrf"], $csrfToken)
    ) {
        $message = "Invalid request. Refresh the page and try again.";
        $messageType = "error";

    } else {

        $action = $_POST["action"] ?? "";

        // UPLOAD IMAGE
        if ($action === "upload") {

           $caption = trim($_POST["caption"] ?? "");
$eventDate = trim($_POST["event_date"] ?? "");
$eventAddress = trim($_POST["event_address"] ?? "");

$dateObject = DateTime::createFromFormat("!Y-m-d", $eventDate);

$validDate = $dateObject &&
    $dateObject->format("Y-m-d") === $eventDate;

           if ($caption === "" || mb_strlen($caption) > 150) {
    $message = "Enter a caption of 1 to 150 characters.";
    $messageType = "error";

} elseif (!$validDate) {
    $message = "Please enter a valid event date.";
    $messageType = "error";

} elseif (
    $eventAddress === "" ||
    mb_strlen($eventAddress) > 255
) {
    $message = "Enter an event address of up to 255 characters.";
    $messageType = "error";

            } elseif (
                !isset($_FILES["gallery_image"]) ||
                !is_array($_FILES["gallery_image"])
            ) {
                $message = "Please choose an image.";
                $messageType = "error";

            } else {

                $file = $_FILES["gallery_image"];

                if ($file["error"] !== UPLOAD_ERR_OK) {
                    $message = "Image upload failed. Please try again.";
                    $messageType = "error";

                } elseif (
                    $file["size"] <= 0 ||
                    $file["size"] > 5 * 1024 * 1024
                ) {
                    $message = "The image must be smaller than 5 MB.";
                    $messageType = "error";

                } else {

                    $imageInfo = @getimagesize($file["tmp_name"]);
                    $mime = $imageInfo["mime"] ?? "";

                    $allowedTypes = [
                        "image/jpeg" => "jpg",
                        "image/png"  => "png",
                        "image/webp" => "webp"
                    ];

                    if (
                        $imageInfo === false ||
                        !isset($allowedTypes[$mime])
                    ) {
                        $message = "Only valid JPG, PNG, and WEBP images are allowed.";
                        $messageType = "error";

                    } elseif (!is_dir($imageDirectory) || !is_writable($imageDirectory)) {
                        $message = "Your existing images folder is not writable. Check its permissions.";
                        $messageType = "error";

                    } else {

                        $extension = $allowedTypes[$mime];
                        $filename = "gallery_" . bin2hex(random_bytes(16))
                            . "." . $extension;

                        $destination = $imageDirectory . $filename;

                        if (!move_uploaded_file($file["tmp_name"], $destination)) {
                            $message = "Could not save the image in the images folder.";
                            $messageType = "error";

                        } else {

                            try {
                               $stmt = $pdo->prepare(
    "INSERT INTO gallery_images
        (image_name, caption, event_date, event_address)
     VALUES
        (:image_name, :caption, :event_date, :event_address)"
);

$stmt->execute([
    "image_name" => $filename,
    "caption" => $caption,
    "event_date" => $eventDate,
    "event_address" => $eventAddress
]);

                                $message = "Gallery image uploaded successfully.";

                            } catch (PDOException $ex) {
                                error_log($ex->getMessage());

                                if (is_file($destination)) {
                                    unlink($destination);
                                }

                                $message = "Could not save the image details to the database.";
                                $messageType = "error";
                            }
                        }
                    }
                }
            }

        // DELETE IMAGE
        } elseif ($action === "delete") {

            $imageId = filter_var(
                $_POST["image_id"] ?? null,
                FILTER_VALIDATE_INT
            );

            if (!$imageId || $imageId < 1) {
                $message = "Invalid gallery image.";
                $messageType = "error";

            } else {

                try {
                    $stmt = $pdo->prepare(
                        "SELECT image_name
                         FROM gallery_images
                         WHERE id = :id"
                    );

                    $stmt->execute(["id" => $imageId]);
                    $image = $stmt->fetch();

                    if (!$image) {
                        $message = "Gallery image was not found.";
                        $messageType = "error";

                    } else {
                        $filename = basename($image["image_name"]);

                        // Only delete gallery files created by this uploader.
                        if (!preg_match('/^gallery_[a-f0-9]{32}\.(jpg|png|webp)$/', $filename)) {
                            $message = "This file is not a recognized gallery upload.";
                            $messageType = "error";

                        } else {
                            $deleteStmt = $pdo->prepare(
                                "DELETE FROM gallery_images WHERE id = :id"
                            );

                            $deleteStmt->execute(["id" => $imageId]);

                            $filePath = $imageDirectory . $filename;

                            if (is_file($filePath)) {
                                if (!unlink($filePath)) {
                                    error_log(
                                        "Could not remove gallery file: " . $filename
                                    );
                                    $message = "Gallery record deleted, but the image file could not be removed.";
                                    $messageType = "error";
                                } else {
                                    $message = "Gallery image deleted successfully.";
                                }
                            } else {
                                $message = "Gallery record deleted. Its image file was already missing.";
                            }
                        }
                    }

                } catch (PDOException $ex) {
                    error_log($ex->getMessage());
                    $message = "Unable to delete this gallery image.";
                    $messageType = "error";
                }
            }

        } else {
            $message = "Invalid action.";
            $messageType = "error";
        }
    }
}

try {
  $stmt = $pdo->query(
    "SELECT id, image_name, caption, event_date,
            event_address, created_at
     FROM gallery_images
     ORDER BY id DESC"
);

    $images = $stmt->fetchAll();

} catch (PDOException $ex) {
    error_log($ex->getMessage());
    $images = [];
    $message = "Unable to load gallery images.";
    $messageType = "error";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Manage Gallery | Mini World Decor</title>

    <style>
        * {
            box-sizing: border-box;
        }

        body {
            margin: 0;
            padding: 25px;
            background: #faf6ef;
            color: #30251d;
            font-family: Arial, sans-serif;
        }

        .container {
            max-width: 1100px;
            margin: auto;
        }

        header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 25px;
        }

        h1, h2 {
            color: #8e642e;
        }

        a {
            color: #8e642e;
        }

        .links {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
        }

        .panel {
            background: white;
            padding: 25px;
            border-radius: 12px;
            margin-bottom: 25px;
            box-shadow: 0 4px 18px #0000000a;
        }

        label {
            display: block;
            font-weight: bold;
            margin: 15px 0 8px;
        }

        input[type="text"],
        input[type="file"] {
            width: 100%;
            padding: 12px;
            border: 1px solid #d8cbb9;
            border-radius: 6px;
        }

        button {
            border: 0;
            border-radius: 6px;
            padding: 11px 17px;
            cursor: pointer;
            font-weight: bold;
        }

        .upload-button {
            margin-top: 18px;
            background: #a47735;
            color: white;
        }

        .delete-button {
            background: #a72d2d;
            color: white;
        }

        .message {
            padding: 13px;
            border-radius: 6px;
            margin-bottom: 20px;
        }

        .success {
            background: #e6f5e8;
            color: #246b31;
        }

        .error {
            background: #ffe8e8;
            color: #a32222;
        }

        .gallery {
            display: grid;
            grid-template-columns: repeat(3, minmax(0, 1fr));
            gap: 20px;
        }

        .card {
            border: 1px solid #eee3d4;
            border-radius: 10px;
            overflow: hidden;
            background: white;
        }

        .card img {
            width: 100%;
            height: 210px;
            object-fit: cover;
            display: block;
            background: #eee5d8;
        }

        .card-content {
            padding: 15px;
        }

        .card-content h3 {
            overflow-wrap: anywhere;
        }

        .empty {
            background: white;
            padding: 20px;
            border-radius: 8px;
        }

        @media (max-width: 750px) {
            .gallery {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }
        }

        @media (max-width: 500px) {
            body {
                padding: 14px;
            }

            .gallery {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>

<body>
<div class="container">

    <header>
        <h1>Manage Gallery</h1>

        <div class="links">
            <a href="admin.php">Admin Dashboard</a>
            <a href="gallery.php" target="_blank">View Gallery</a>
            <a href="index.php">View Website</a>
            <a href="admin_logout.php">Logout</a>
        </div>
    </header>

    <?php if ($message !== ""): ?>
        <div class="message <?= e($messageType) ?>">
            <?= e($message) ?>
        </div>
    <?php endif; ?>

    <section class="panel">
        <h2>Upload a Gallery Image</h2>

        <p>Choose a JPG, PNG, or WEBP image up to 5 MB.</p>

        <form method="POST" enctype="multipart/form-data">

            <input
                type="hidden"
                name="csrf_token"
                value="<?= e($_SESSION["gallery_csrf"]) ?>"
            >

            <input type="hidden" name="action" value="upload">

            <label for="caption">Image Caption</label>
            <input
                type="text"
                id="caption"
                name="caption"
                maxlength="150"
                required
                placeholder="Example: Elegant Wedding Decoration"
            >
            <label for="event_date">Event Date</label>
<input
    type="date"
    id="event_date"
    name="event_date"
    required
>


            <label for="gallery_image">Choose Image</label>
            <input
                type="file"
                id="gallery_image"
                name="gallery_image"
                accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp"
                required
            >
<label for="event_address">Event Address</label>
<input
    type="text"
    id="event_address"
    name="event_address"
    maxlength="255"
    required
    placeholder="Enter the venue or event address"
>
            <button type="submit" class="upload-button">
                Upload Image
            </button>
        </form>
    </section>

    <section class="panel">
        <h2>Uploaded Gallery Images</h2>

        <?php if (count($images) > 0): ?>

            <div class="gallery">

                <?php foreach ($images as $image): ?>
                    <?php $filename = basename($image["image_name"]); ?>

                    <article class="card">
                        <img
                            src="images/<?= rawurlencode($filename) ?>"
                            alt="<?= e($image["caption"]) ?>"
                            loading="lazy"
                        >

                        <div class="card-content">
                            <h3><?= e($image["caption"]) ?></h3>
                            

                            <form
                                method="POST"
                                onsubmit="return confirm('Delete this gallery image?');"
                            >
                                <input
                                    type="hidden"
                                    name="csrf_token"
                                    value="<?= e($_SESSION["gallery_csrf"]) ?>"
                                >

                                <input
                                    type="hidden"
                                    name="action"
                                    value="delete"
                                >

                                <input
                                    type="hidden"
                                    name="image_id"
                                    value="<?= (int)$image["id"] ?>"
                                >

                                <button
                                    type="submit"
                                    class="delete-button"
                                >
                                    Delete Image
                                </button>
                            </form>
                        </div>
                    </article>

                <?php endforeach; ?>

            </div>

        <?php else: ?>

            <div class="empty">
                No gallery images uploaded yet.
            </div>

        <?php endif; ?>
    </section>

</div>
</body>
</html>