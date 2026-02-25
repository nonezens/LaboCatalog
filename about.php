<?php 
include 'db.php'; 
include 'header.php'; 
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>About Us | Museum Labo Catalog</title>
    <style>
        :root { --dark: #1a1a1a; --gold: #c5a059; --bg: #fdfdfd; }
        body { font-family: 'Georgia', serif; line-height: 1.6; background-color: var(--bg); color: #333; margin: 0; }
        
        .container { max-width: 1000px; margin: auto; padding: 60px 20px; }
        
        /* Banner Style */
        .about-hero {
            background: #2c3e50;
            color: white;
            padding: 80px 20px;
            text-align: center;
        }
        .about-hero h1 { font-size: 3rem; margin: 0; color: var(--gold); }

        .content-section { margin-bottom: 50px; }
        .content-section h2 { border-bottom: 2px solid var(--gold); display: inline-block; padding-bottom: 5px; margin-bottom: 20px; }
        
        /* Information Grid */
        .info-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 40px;
            margin-top: 40px;
        }
        .info-box {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
        }
        .info-box h3 { color: var(--gold); margin-top: 0; }

        .location-map {
            width: 100%;
            height: 300px;
            background: #ddd;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-top: 20px;
            border-radius: 8px;
        }
    </style>
</head>
<body>

    <section class="about-hero">
        <h1>Our Story</h1>
        <p>Preserving the heritage of Labo for generations to come.</p>
    </section>

    <div class="container">
        <div class="content-section">
            <h2>Mission & History</h2>
            <p>Founded in 2026, the **Museum Labo Catalog** began as a digital initiative to document and preserve the rich cultural history of our region. Our mission is to provide an accessible platform where students, historians, and enthusiasts can explore artifacts that define our shared human experience.</p>
            <p>We believe that history should be interactive and inclusive. Through our digital exhibits, we bring the museum experience directly to your screen, ensuring that even the most fragile artifacts can be studied and appreciated without risk of damage.</p>
        </div>

        <div class="info-grid">
            <div class="info-box">
                <h3>🕒 Visiting Hours</h3>
                <p>Monday - Friday: 9:00 AM - 5:00 PM</p>
                <p>Saturday: 10:00 AM - 4:00 PM</p>
                <p>Sunday: Closed</p>
            </div>

            <div class="info-box">
                <h3>📍 Contact Us</h3>
                <p><strong>Address:</strong> Peoples Park, Labo, Camarines Norte, Philippines</p>
                <p><strong>Email:</strong> info@labomuseum.ph</p>
                <p><strong>Phone:</strong> +63 (054) 123-4567</p>
            </div>
        </div>

        <div class="content-section" style="margin-top: 60px;">
            <h2>Find Us</h2>
            <div class="location-map">
                <p style="color: #777;">[ https://maps.app.goo.gl/mz9quW5tLAth5yZ46 ]</p>
            </div>
        </div>
    </div>
    
    <footer style="background: var(--dark); color: white; text-align: center; padding: 30px; margin-top: 40px;">
        <p>&copy; 2026 Museum Labo Catalog</p>
    </footer>

</body>
</html>