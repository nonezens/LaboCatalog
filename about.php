<?php 
include 'db.php'; 
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us | Museo de Labo</title>
    <link rel="stylesheet" href="style.css">
    <style>
        .about-hero {
            background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%);
            color: white;
            padding: 80px 20px;
            text-align: center;
        }

        .about-hero h1 {
            font-size: 3rem;
            margin: 0;
            color: var(--gold);
            font-weight: 800;
        }

        .about-hero p {
            font-size: 1.2rem;
            color: rgba(255, 255, 255, 0.85);
            max-width: 600px;
            margin: 15px auto 0;
        }

        .content-section {
            margin-bottom: 50px;
        }

        .content-section h2 {
            color: var(--dark);
            border-bottom: 3px solid var(--gold);
            display: inline-block;
            padding-bottom: 10px;
            margin-bottom: 20px;
            font-size: 2rem;
        }

        .content-section p {
            font-size: 1.1rem;
            line-height: 1.8;
            color: #555;
        }

        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 30px;
            margin-top: 40px;
        }

        .info-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: var(--shadow);
            border-top: 4px solid var(--gold);
            transition: 0.3s;
        }

        .info-box:hover {
            transform: translateY(-5px);
            box-shadow: var(--shadow);
        }

        .info-box h3 {
            color: var(--primary);
            margin-top: 0;
            font-size: 1.4rem;
        }

        .info-box p {
            margin: 10px 0;
            font-size: 1rem;
            color: #555;
        }

        .info-box strong {
            color: var(--dark);
            font-weight: 700;
        }

        .location-map {
            width: 100%;
            height: 400px;
            background: linear-gradient(135deg, rgba(19, 113, 55, 0.1), rgba(197, 160, 89, 0.1));
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 30px;
            border-radius: 8px;
            border: 2px solid var(--border);
            font-size: 3rem;
            color: #ccc;
        }

        .action-buttons {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            flex-wrap: wrap;
        }

        @media (max-width: 768px) {
            .about-hero h1 { font-size: 2.2rem; }
            .about-hero { padding: 60px 20px; }
            .info-grid { grid-template-columns: 1fr; }
            .location-map { height: 300px; }
        }
    </style>
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
                <p><strong>Monday - Friday:</strong> 9:00 AM - 5:00 PM</p>
                <p><strong>Saturday:</strong> 10:00 AM - 4:00 PM</p>
                <p><strong>Sunday:</strong> Closed</p>
                <p style="color: #7f8c8d; font-size: 0.9rem; margin-top: 15px;">* Extended hours available by appointment</p>
            </div>

            <div class="info-box">
                <h3>📍 Contact Information</h3>
                <p><span style="color: var(--primary);">Address:</span> Labo Heritage Center, Labo, Camarines Norte</p>
                <p><span style="color: var(--primary);">Email:</span> info@labomuseum.ph</p>
                <p><span style="color: var(--primary);">Phone:</span> +63 (054) 123-4567</p>
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
                <a href="login.php" class="btn-secondary">Explore Digital Collection</a>
                <a href="mailto:info@labomuseum.ph" class="btn-secondary">Donate an Artifact</a>
            </div>
        </div>
    </div>

    <footer>
        <p>&copy; 2026 Museo De Labo Catalog. Preserving Our Cultural Heritage for Future Generations.</p>
    </footer>
</body>
</html>