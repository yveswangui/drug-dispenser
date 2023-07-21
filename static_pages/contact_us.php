<?
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Contact Us - Dawa Bora Enterprises</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include '../header_footer.php'; echo $navigation_bar;?>

    <!-- Contact Section -->
    <section id="contact" class="section">
        <div class="container">
            <h2 class="section-title">Contact Us</h2>
            <div class="row">
                <div class="col-md-6">
                    <h3 class="section-subtitle">Get in Touch</h3>
                    <p>We'd love to hear from you. Whether you have questions, feedback, or partnership inquiries, please feel free to reach out to us using the contact details below or by filling out the form.</p>
                    <div class="contact-info">
                        <p><i class="fas fa-map-marker-alt"></i> Ole Sangale - Keri Link Road Madaraka, Nairobi, Kenya</p>
                        <p><i class="fas fa-envelope"></i> <a href="mailto:info@dawabora.com">info@dawabora.com</a></p>
                        <p><i class="fas fa-phone"></i> <a href="tel:+254473487343">+254 473 487 343</a></p>
                    </div>
                </div>
                <div class="col-md-6">
                    <h3 class="section-subtitle">Send Us a Message</h3>
                    <form action="#" method="POST" class="contact-form">
                        <div class="form-group">
                            <input type="text" name="name" placeholder="Your Name" required>
                        </div>
                        <div class="form-group">
                            <input type="email" name="email" placeholder="Your Email" required>
                        </div>
                        <div class="form-group">
                            <textarea name="message" placeholder="Your Message" required></textarea>
                        </div>
                        <div class="form-group">
                            <button type="submit" class="btn btn-primary">Send Message</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Map Section -->
    <section id="map" class="section">
        <div class="container">
            <div class="map-responsive">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3989.278584852635!2d36.80864941536625!3d-1.2865665360501007!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x182f1b234e344f99%3A0x67623a4f6eb8c41f!2sLugulu%20Ave%2C%20Nairobi%2C%20Kenya!5e0!3m2!1sen!2suk!4v1626364664299!5m2!1sen!2suk" width="100%" height="450" style="border:0;" allowfullscreen="" loading="lazy"></iframe>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php echo $footer; ?>

</body>
</html>
