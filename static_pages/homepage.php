<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dawa Bora Enterprises - Revolutionizing Healthcare Distribution</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include '../header_footer.php'; echo $navigation_bar;?>

    <!-- Hero Section -->
<center>
    <section class="hero">
        <div class="container">
            <h1>Welcome to Dawa Bora Enterprises</h1>
            <h2 class = "lead">Revolutionizing Healthcare Distribution</h2>
            <a href="about_us.php" class="btn btn-primary">Explore Our Solutions</a>
        </div>
    </section>
</center>
    <!-- About Us Section -->
    <section id="about-us" class="section">
        <div class="container">
            <h2>About Dawa Bora Enterprises</h2>
            <p>Dawa Bora Enterprises is a <strong style = "color: maroon;">leading provider of innovative solutions in the healthcare distribution industry.</strong> We are dedicated to streamlining the supply chain, ensuring patient safety, and optimizing the distribution process of pharmaceuticals.</p>
            <p>With our state-of-the-art technology and deep industry expertise, we empower pharmacies, healthcare providers, and pharmaceutical companies to overcome the challenges they face in delivering essential medications to patients efficiently and effectively.</p>
        </div>
    </section>

    <!-- Our Solutions Section -->
    <section id="solutions" class="section bg-light">
        <div class="container">
            <h2>Our Solutions</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="solution">
                        <h3>Patient-Centric Approach</h3>
                        <p>We prioritize patient care by providing seamless prescription management, medication reminders, and convenient home delivery options. Our user-friendly platform ensures a hassle-free experience for patients.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="solution">
                        <h3>Efficient Supply Chain Management</h3>
                        <p>Through our advanced technology and strategic partnerships, we optimize the pharmaceutical supply chain, enabling pharmacies and healthcare providers to streamline operations, reduce costs, and enhance inventory management.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="solution">
                        <h3>Collaboration and Connectivity</h3>
                        <p>We foster collaboration between doctors, pharmacists, and pharmaceutical companies by providing a secure platform for seamless communication, data sharing, and collaborative decision-making, ensuring the best possible outcomes for patients.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Testimonials Section -->
    <section id="testimonials" class="section">
        <div class="container">
            <h2>What Our Clients Say</h2>
            <div class="testimonial">
                <blockquote>
                    <p>"Dawa Bora Enterprises has been a game-changer in our pharmacy operations. Their solutions have improved our efficiency and enabled us to provide better patient care. Highly recommended!"</p>
                    <footer>- Mary Kamau, Owner of MediCare Pharmacy</footer>
                </blockquote>
            </div>
            <div class="testimonial">
                <blockquote>
                    <p>"I'm impressed with the seamless prescription management and personalized healthcare experience Dawa Bora Enterprises offers. It has transformed the way I access medications and communicate with my doctor."</p>
                    <footer>- John Ochieng, Patient</footer>
                </blockquote>
            </div>
        </div>
    </section>

    <!-- FAQ Section -->
    <section id="faq" class="section bg-light">
        <div class="container">
            <h2>Frequently Asked Questions</h2>
            <div class="faq-item">
                <h3>Q: How can I benefit from Dawa Bora Enterprises' solutions?</h3>
                <p>A: Our solutions provide pharmacies, healthcare providers, and pharmaceutical companies with advanced tools and streamlined processes to enhance patient care, optimize operations, and achieve better outcomes.</p>
            </div>
            <div class="faq-item">
                <h3>Q: Is my personal and medical information secure with Dawa Bora Enterprises?</h3>
                <p>A: Absolutely! We prioritize the privacy and security of your information. Our platform follows industry-leading practices and utilizes robust security measures to safeguard your data.</p>
            </div>
            <div class="faq-item">
                <h3>Q: How can I get started with Dawa Bora Enterprises?</h3>
                <p>A: Simply reach out to our team through our contact page, and we'll be delighted to discuss your specific needs and provide tailored solutions for your organization.</p>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php echo $footer; ?>

</body>
</html>
