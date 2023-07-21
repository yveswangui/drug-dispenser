<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Dawa Bora Enterprises</title>
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles/styles.css">
</head>
<body>
    <!-- Navigation Bar -->
    <?php include '../header_footer.php'; echo $navigation_bar;?>

    <!-- About Us Section -->
    <section id="about-us" class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="section-title">Who We Are</h2>
                    <p>Dawa Bora Enterprises is a forward-thinking company dedicated to revolutionizing the healthcare distribution landscape. With our innovative solutions and unwavering commitment, we strive to empower pharmacies, healthcare providers, and pharmaceutical companies to enhance patient care and streamline their operations.</p>
                    <p>Our team of experienced professionals brings together expertise in technology, healthcare, and supply chain management to create cutting-edge solutions that address the challenges faced by the industry.</p>
                </div>
                <div class="col-md-6">
                    <img src="static/images/image-1.jpg" alt="About Us Image" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Vision Section -->
    <section id="our-vision" class="section bg-light">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <img src="static/images/image-2.jpg" alt="Our Vision Image" class="img-fluid">
                </div>
                <div class="col-md-6">
                    <h2 class="section-title">Our Vision</h2>
                    <p>At Dawa Bora Enterprises, we envision a future where healthcare distribution is seamless, efficient, and patient-centered. We strive to be at the forefront of innovation, providing state-of-the-art solutions that empower healthcare stakeholders and improve the well-being of individuals and communities.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Our Mission Section -->
    <section id="our-mission" class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h2 class="section-title">Our Mission</h2>
                    <p>Our mission is to transform the healthcare distribution landscape by delivering cutting-edge technology solutions that optimize operations, improve patient care, and foster collaboration among healthcare providers, pharmacies, and pharmaceutical companies.</p>
                    <p>We are dedicated to exceeding the expectations of our clients by providing innovative, reliable, and secure platforms that streamline processes, enhance efficiency, and contribute to the overall advancement of the healthcare industry.</p>
                </div>
                <div class="col-md-6">
                    <img src="static/images/image-3.jpg" alt="Our Mission Image" class="img-fluid">
                </div>
            </div>
        </div>
    </section>

    <!-- Our Values Section -->
    <section id="our-values" class="section bg-light">
        <div class="container">
            <h2 class="section-title">Our Values</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="value">
                        <img src="static/images/innovation.jpg" alt="Value Icon" class="value-icon">
                        <h3 class="value-title">Innovation</h3>
                        <p class="value-description">We embrace innovation and constantly strive to find creative solutions to the challenges faced by the healthcare distribution industry.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value">
                        <img src="static/images/excellence.jpeg" alt="Value Icon" class="value-icon">
                        <h3 class="value-title">Excellence</h3>
                        <p class="value-description">We are committed to delivering excellence in every aspect of our work, from product development to customer service.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="value">
                        <img src="static/images/collaboration.jpg" alt="Value Icon" class="value-icon">
                        <h3 class="value-title">Collaboration</h3>
                        <p class="value-description">We foster a culture of collaboration, working closely with our clients and partners to achieve shared success and meaningful impact.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Team Section -->
    <section id="our-team" class="section">
        <div class="container">
            <h2 class="section-title">Meet Our Team</h2>
            <div class="row">
                <div class="col-md-4">
                    <div class="team-member text-center">
                        <img src="static/images/user-1.jpg" alt="Team Member" class="team-member-photo">
                        <h3 class="team-member-name">Yvonne Wangui</h3>
                        <p class="team-member-role">Chief Executive Officer</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member text-center">
                        <img src="static/images/user-2.jpg" alt="Team Member" class="team-member-photo">
                        <h3 class="team-member-name">MaryLynn Ateka</h3>
                        <p class="team-member-role">Chief Technical Officer</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="team-member text-center">
                        <img src="static/images/user-3.jpg" alt="Team Member" class="team-member-photo">
                        <h3 class="team-member-name">David Lutambo</h3>
                        <p class="team-member-role">Chief Financial Officer</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <?php echo $footer; ?>

</body>
</html>
