<?php
require_once __DIR__ . "/includes/db.php";

try {
    $stmt = $pdo->query(
        "SELECT id, event_name, event_type, description, price, image_url
         FROM events
         ORDER BY id DESC"
    );

    $events = $stmt->fetchAll();
} catch (PDOException $e) {
    $events = [];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Mini World Decor | We Decorate Your Dreams</title>

    <meta name="description"
          content="Premium birthday, wedding, engagement and anniversary decorations by Mini World Decor.">

    <link rel="stylesheet" href="css/style.css">
</head>
<body>

<!-- Navigation -->
<header class="site-header">
    <div class="container navbar">

        <a href="index.php" class="logo">
            <span class="logo-mark">MWD</span>
            <span class="logo-text">
                MINI WORLD
                <small>DECOR & CELEBRATIONS</small>
            </span>
        </a>

        <nav class="nav-links">
            <a href="index.php" class="active">Home</a>
            <a href="#services">Services</a>
            <a href="gallery.php">Gallery</a>
            <a href="#about">About Us</a>
            <a href="#contact">Contact</a>
            <a href="my_bookings.php">My Bookings</a>
        </nav>

        <a href="booking.php" class="btn btn-small">
            Book Your Event
        </a>
         <a href="admin_login.php" class="btn btn-small">
            Admin login
        </a>

    </div>
</header>

<main>

<!-- Hero Section -->
<section class="hero">
    <div class="hero-overlay"></div>

    <div class="container hero-content">
        <p class="eyebrow">EVERY DETAIL. EVERY DREAM. EVERY CELEBRATION.</p>

        <h1>We Decorate Your<br>
            <span>Dreams Into Reality</span>
        </h1>

        <p class="hero-description">
            Make every celebration unforgettable with beautiful,
            thoughtfully designed decorations for birthdays, weddings,
            engagements and anniversaries.
        </p>

        <div class="hero-buttons">
            <a href="booking.php" class="btn">Book Your Decoration</a>
            <a href="#services" class="btn btn-outline">Explore Our Services</a>
        </div>

        <div class="hero-note">
            <span class="gold-line"></span>
            Creating beautiful memories, one celebration at a time
        </div>
    </div>
</section>

<!-- Introduction -->
<section class="intro section-padding">
    <div class="container intro-inner">
        <p class="eyebrow">WELCOME TO MINI WORLD DECOR</p>

        <h2>Beautiful Spaces.<br>
            <span>Unforgettable Moments.</span>
        </h2>

        <p class="section-description">
            Every celebration tells a story. We transform your ideas
            into stunning spaces with creative themes, elegant details
            and decorations designed especially for your special day.
        </p>
    </div>
</section>

<!-- Services -->
<section class="services section-padding" id="services">
    <div class="container">

        <div class="section-heading">
            <div>
                <p class="eyebrow">CELEBRATE IN STYLE</p>
                <h2>Our Decoration <span>Services</span></h2>
            </div>

            <p>
                Thoughtful designs for life's most precious occasions.
            </p>
        </div>

        <div class="service-grid">

            <article class="service-card">
                <img
                    src="https://theballoonwala.com/cdn/shop/files/Simple_Birthday_Decoration_at_Home_with_Fairy_Lights._1.png?v=1780338701&width=1445"
                    alt="Colorful birthday party decorations"
                    loading="lazy">

                <div class="service-info">
                    <span class="service-number">01</span>
                    <h3>Birthday Celebrations</h3>
                    <p>
                        Beautiful balloon arches, themed backdrops
                        and memorable birthday setups.
                    </p>
                    <a href="booking.php?event=birthday" class="text-link">
                        Plan a Birthday <span>↗</span>
                    </a>
                </div>
            </article>

            <article class="service-card">
                <img
                    src="https://www.marriagecolours.com/wp-content/uploads/2025/04/Bhavya-Arvind-Wedding-MRC-17.jpg"
                    alt="Elegant wedding venue and floral decorations"
                    loading="lazy">

                <div class="service-info">
                    <span class="service-number">02</span>
                    <h3>Wedding Decorations</h3>
                    <p>
                        Romantic floral arrangements and elegant
                        settings for your special day.
                    </p>
                    <a href="booking.php?event=wedding" class="text-link">
                        Plan a Wedding <span>↗</span>
                    </a>
                </div>
            </article>

            <article class="service-card">
                <img
                    src="https://bandbajaweddings.com/wp-content/uploads/2025/05/WhatsApp-Image-2025-04-30-at-3.30.34-PM-1.jpeg"
                    alt="Elegant engagement and celebration setup"
                    loading="lazy">

                <div class="service-info">
                    <span class="service-number">03</span>
                    <h3>Engagement Ceremonies</h3>
                    <p>
                        Stylish backdrops and sophisticated
                        designs for your beginning together.
                    </p>
                    <a href="booking.php?event=engagement" class="text-link">
                        Plan an Engagement <span>↗</span>
                    </a>
                </div>
            </article>

            <article class="service-card">
                <img
                    src="https://images.unsplash.com/photo-1464366400600-7168b8af9bc3?auto=format&fit=crop&w=800&q=85"
                    alt="Beautiful anniversary celebration table"
                    loading="lazy">

                <div class="service-info">
                    <span class="service-number">04</span>
                    <h3>Anniversary Celebrations</h3>
                    <p>
                        Intimate, romantic decorations that make
                        every milestone more meaningful.
                    </p>
                    <a href="booking.php?event=anniversary" class="text-link">
                        Plan an Anniversary <span>↗</span>
                    </a>
                </div>
            </article>

        </div>
    </div>
</section>
<!-- About -->
<section class="about section-padding" id="about">
    <div class="container about-grid">

        <div class="about-image">
            <img
                src="https://images.unsplash.com/photo-1519167758481-83f550bb49b3?auto=format&fit=crop&w=1000&q=85"
                alt="Beautifully styled celebration venue"
                loading="lazy">
            <div class="image-caption">
                <span>Thoughtfully styled</span>
                <strong>Just for you.</strong>
            </div>
        </div>

        <div class="about-content">
            <p class="eyebrow">OUR PASSION, YOUR CELEBRATION</p>

            <h2>More Than Decor.<br>
                <span>It's Your Story.</span>
            </h2>

            <p>
                At Mini World Decor, we believe the smallest details
                can create the biggest memories. We bring your vision
                to life through carefully planned themes, beautiful
                arrangements and personalized decoration ideas.
            </p>

            <p>
                From a cozy birthday at home to a grand wedding
                celebration, we help make your important moments
                feel truly special.
            </p>

            <a href="booking.php" class="btn">Let's Plan Your Event</a>
        </div>

    </div>
</section>

<!-- Call to Action -->
<section class="cta" id="contact">
    <div class="container cta-content">
        <p class="eyebrow">YOUR SPECIAL DAY STARTS HERE</p>

        <h2>Let's Make Something<br>
            <span>Beautiful Together.</span>
        </h2>

        <p>
            Tell us about your celebration and let us help you
            create a setting worth remembering.
        </p>

        <a href="booking.php" class="btn btn-light">
            Start Your Booking
        </a>
    </div>
</section>

<section class="events-section">
    <h2>Our Decoration Services</h2>
    <p class="section-subtitle">
        Make every celebration unforgettable with Mini World Decor.
    </p>

    <div class="events-container">
        <?php if (!empty($events)): ?>

            <?php foreach ($events as $event): ?>
               
                <div class="event-card">

                    <?php if (!empty($event["image_url"])): ?>
                        <img
                            src="<?= htmlspecialchars($event["image_url"], ENT_QUOTES, 'UTF-8') ?>"
                            alt="<?= htmlspecialchars($event["event_name"], ENT_QUOTES, 'UTF-8') ?>"
                        >
                    <?php endif; ?>

                    <div class="event-content">
                        <h3>
                            <?= htmlspecialchars($event["event_name"], ENT_QUOTES, 'UTF-8') ?>
                        </h3>

                        <p class="event-type">
                            <?= htmlspecialchars($event["event_type"], ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p>
                            <?= htmlspecialchars($event["description"] ?? "", ENT_QUOTES, 'UTF-8') ?>
                        </p>

                        <p class="event-price">
                            Starting at ₹<?= number_format((float)$event["price"], 2) ?>
                        </p>

                         <a
    class="book-btn"
    href="event_details.php?id=<?= (int)$event['id'] ?>"
>
    View Details
</a>

                        <a
                            class="book-btn"
                            href="booking.php?event_type=<?= rawurlencode($event["event_type"]) ?>"
                        >
                            Book This Event
                        </a>
                    </div>

                </div>
            <?php endforeach; ?>

        <?php else: ?>

            <p class="no-events">
                No events are available right now. Please check back soon.
            </p>

        <?php endif; ?>
    </div>
</section>


</main>

<!-- Footer -->
<footer class="site-footer">
    <div class="container footer-grid">

        <div>
            <a href="index.php" class="logo footer-logo">
                <span class="logo-mark">M</span>
                <span class="logo-text">
                    MINI WORLD
                    <small>DECOR & CELEBRATIONS</small>
                </span>
            </a>

            <p class="footer-description">
                We turn your celebrations into beautiful memories
                with thoughtful and elegant decoration.
            </p>
        </div>

        <div>
            <h3>Quick Links</h3>
            <a href="index.php">Home</a>
            <a href="#services">Services</a>
            <a href="gallery.php">Gallery</a>
            <a href="#about">About Us</a>
        </div>

        <div>
            <h3>Our Services</h3>
            <a href="booking.php?event=birthday">Birthday</a>
            <a href="booking.php?event=wedding">Wedding</a>
            <a href="booking.php?event=engagement">Engagement</a>
            <a href="booking.php?event=anniversary">Anniversary</a>
        </div>

        <div>
            <h3>Get in Touch</h3>
            <p>Let's discuss your next celebration.</p>
            <a href="booking.php">Book an Event ↗</a>
        </div>

    </div>

    <div class="container footer-bottom">
        <p>&copy; <?php echo date("Y"); ?> Mini World Decor.
            All rights reserved.</p>
        <p>Made with care for your special moments.</p>
    </div>
</footer>

</body>
</html>
