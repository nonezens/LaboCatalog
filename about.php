<?php 
include 'db.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Museo de Labo</title>
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="css/about.css">
</head>
<body>
    <?php include 'header.php'; ?>

    <section class="about-hero">
        <h1>About Museo de Labo</h1>
        <p>Preserving the heritage and culture of Labo, Camarines Norte</p>
    </section>

    <div class="container">
        <div class="content-section mt-40">
            <h2>Our Mission & History</h2>
            <p>
                Founded in 2026, the <strong>Museo de Labo Digital Catalog</strong> began as an initiative to document and preserve the rich cultural history of Labo, Camarines Norte. Our museum serves as the primary custodian of the municipality's historical artifacts, cultural relics, and artistic heritage.
            </p>
            <p>
                We believe that history should be accessible and inclusive. Through our digital catalog, we bring the museum experience directly to our community and to researchers worldwide, ensuring that even the most fragile artifacts can be studied and appreciated without risk of damage.
            </p>
            <p>
                Our collection spans from prehistoric times through the modern era, including indigenous artifacts, colonial relics, traditional crafts, and contemporary commemorative pieces—each telling a unique story about our shared heritage.
            </p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>⏰ Visiting Hours</h3>
                <p><strong>Monday - Friday:</strong> 8:00 AM - 5:00 PM</p>
                <p><strong>Saturday & Sunday:</strong> Closed</p>
                <p style="color: #7f8c8d; font-size: 0.9rem; margin-top: 15px;">* Extended hours available by appointment</p>
            </div>

            <div class="info-box">
                <h3>📍 Contact Information</h3>
                <p><span style="color: var(--primary);">Address:</span> Museo De Labo People's Park, Labo, Camarines Norte</p>
                <p><span style="color: var(--primary);">Email:</span> labotourism08@yahoo.com</p>
                <p><span style="color: var(--primary);">Phone:</span> (054) 885-1074 / (+63) 928-661-2138</p>
                <p style="color: #7f8c8d; font-size: 0.9rem; margin-top: 15px;">For group tours, contact us in advance</p>
            </div>

            <div class="info-box">
                <h3>🌐 Access & Admission</h3>
                <p>Our physical museum welcomes locals and visitors. For digital collection access, sign our guestbook on the login page.</p>
                <p style="color: var(--primary); font-weight: 700;">Admission: Free for students and residents</p>
            </div>
        </div>

        <div class="content-section mt-40">
            <h2>Our Location</h2>
            <div class="location-map">🗺️</div>
            <p style="text-align: center; color: #7f8c8d; margin-top: 15px;">Interactive map coming soon. Visit us in person or explore our digital collection online.</p>
        </div>

        <div class="content-section">
            <h2>Get Involved</h2>
            <p>
                We welcome donations of artifacts, historical documents, photographs, and other materials that contribute to the preservation of Labo's cultural heritage. If you have items you'd like to share with the museum, please contact us.
            </p>
            <div class="action-buttons">
                <a href="login.php">Explore Digital Collection</a>
                <a href="mailto:info@labomuseum.ph">Donate an Artifact</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
    </footer>
</body>
</html>