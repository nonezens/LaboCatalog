<?php 
include 'db.php'; 
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Museum Labo Catalog</title>
    
    <!-- CSS -->
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/about.css">
</head>
<body>

    <section class="about-hero">
        <h1>Our Story</h1>
        <p>Preserving the heritage of Labo for generations to come.</p>
    </section>

    <div class="container">
        <div class="content-section">
            <h2>Mission & History</h2>
            <p>Founded in 2026, the <strong>Museum Labo Catalog</strong> began as a digital initiative to document and preserve the rich cultural history of our region. Our mission is to provide an accessible platform where students, historians, and enthusiasts can explore artifacts that define our shared human experience.</p>
            <p>We believe that history should be interactive and inclusive. Through our digital exhibits, we bring the museum experience directly to your screen, ensuring that even the most fragile artifacts can be studied and appreciated without risk of damage.</p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>🕒 Visiting Hours</h3>
                <p>Monday - Friday: 8:00 AM - 5:00 PM</p>
                <p>Weekends: Closed</p>
            </div>

            <div class="info-box">
                <h3>📍 Contact Us</h3>
                <p><strong>Address:</strong> 123 Heritage Lane, Labo, Philippines</p>
                <p><strong>Email:</strong> info@labomuseum.ph</p>
                <p><strong>Phone:</strong> +63 (054) 123-4567</p>
            </div>
        </div>

        <div class="content-section mt-40">
            <h2>Our Location</h2>
            <div style="position: relative; margin-top: 20px;">
                <iframe 
                    src="https://www.google.com/maps?q=Labo+Museum,+Labo,+Camarines+Norte,+Philippines&t=m&z=15&output=embed&iwloc=near" 
                    width="100%" 
                    height="450" 
                    style="border:0; border-radius: 8px;" 
                    allowfullscreen="" 
                    loading="lazy" 
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
            </div>
            <div style="text-align: center; margin-top: 20px;">
                <button id="navigationBtn" style="display: inline-block; background: var(--gold); color: #333; padding: 12px 30px; text-decoration: none; border-radius: 5px; font-weight: bold; cursor: pointer; transition: background 0.3s; border: none; font-size: 1rem;">
                    📍 Show the Way
                </button>
            </div>
    </div>

    <footer>
        <p>&copy; 2026 Museum Labo Catalog</p>
    </footer>

</body>
</html>

