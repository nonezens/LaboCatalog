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

        <!-- Navigation Modal -->
        <div id="navigationModal" style="display: none; position: fixed; top: 0; left: 0; width: 100%; height: 100%; background: rgba(0, 0, 0, 0.5); z-index: 1000; align-items: center; justify-content: center;">
            <div style="background: white; padding: 40px; border-radius: 12px; box-shadow: 0 8px 30px rgba(0, 0, 0, 0.3); max-width: 500px; width: 90%; text-align: center;">
                <h2 style="color: var(--dark); margin-top: 0; margin-bottom: 30px; font-size: 1.8rem;">Choose Your Route</h2>
                <p style="color: #7f8c8d; margin-bottom: 30px; font-size: 1.05rem;">How would you like to navigate to Museo de Labo?</p>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 20px;">
                    <button id="carBtn" style="padding: 20px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer; transition: 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                        <span style="font-size: 2rem;">🚗</span>
                        <span>Car / Drive</span>
                    </button>
                    <button id="walkBtn" style="padding: 20px; background: var(--primary); color: white; border: none; border-radius: 8px; font-weight: bold; font-size: 1rem; cursor: pointer; transition: 0.3s; display: flex; flex-direction: column; align-items: center; gap: 10px;">
                        <span style="font-size: 2rem;">🚶</span>
                        <span>Walk</span>
                    </button>
                </div>

                <button id="closeModalBtn" style="width: 100%; padding: 12px; background: #95a5a6; color: white; border: none; border-radius: 8px; font-weight: 600; cursor: pointer; transition: 0.3s;">
                    Cancel
                </button>
            </div>
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

    <script>
        // Museum location coordinates (Labo, Camarines Norte)
        const museumLat = 14.1333;
        const museumLng = 122.4500;
        const museumName = "Museo De Labo People's Park";

        const navigationBtn = document.getElementById('navigationBtn');
        const navigationModal = document.getElementById('navigationModal');
        const carBtn = document.getElementById('carBtn');
        const walkBtn = document.getElementById('walkBtn');
        const closeModalBtn = document.getElementById('closeModalBtn');

        // Show modal when "Show the Way" is clicked
        navigationBtn.addEventListener('click', function() {
            navigationModal.style.display = 'flex';
        });

        // Close modal
        closeModalBtn.addEventListener('click', function() {
            navigationModal.style.display = 'none';
        });

        // Close modal when clicking outside
        navigationModal.addEventListener('click', function(e) {
            if (e.target === navigationModal) {
                navigationModal.style.display = 'none';
            }
        });

        // Car navigation - opens Google Maps with driving directions
        carBtn.addEventListener('click', function() {
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${museumLat},${museumLng}&travelmode=driving`;
            window.open(googleMapsUrl, '_blank');
            navigationModal.style.display = 'none';
        });

        // Walk navigation - opens Google Maps with walking directions
        walkBtn.addEventListener('click', function() {
            const googleMapsUrl = `https://www.google.com/maps/dir/?api=1&destination=${museumLat},${museumLng}&travelmode=walking`;
            window.open(googleMapsUrl, '_blank');
            navigationModal.style.display = 'none';
        });

        // Add hover effects
        carBtn.addEventListener('mouseover', function() {
            this.style.background = 'var(--gold)';
            this.style.color = '#333';
            this.style.transform = 'translateY(-3px)';
        });

        carBtn.addEventListener('mouseout', function() {
            this.style.background = 'var(--primary)';
            this.style.color = 'white';
            this.style.transform = 'translateY(0)';
        });

        walkBtn.addEventListener('mouseover', function() {
            this.style.background = 'var(--gold)';
            this.style.color = '#333';
            this.style.transform = 'translateY(-3px)';
        });

        walkBtn.addEventListener('mouseout', function() {
            this.style.background = 'var(--primary)';
            this.style.color = 'white';
            this.style.transform = 'translateY(0)';
        });

        closeModalBtn.addEventListener('mouseover', function() {
            this.style.background = '#7f8c8d';
            this.style.transform = 'translateY(-2px)';
        });

        closeModalBtn.addEventListener('mouseout', function() {
            this.style.background = '#95a5a6';
            this.style.transform = 'translateY(0)';
        });
    </script>
</body>
</html>