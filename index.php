<?php 
include 'db.php'; 
include 'header.php'; 

// 1. Fetch 3 Categories for the "Departments" section
$categories = $conn->query("SELECT * FROM categories LIMIT 3");

// 2. Fetch the 4 most recently added artifacts
$latest_exhibits = $conn->query("SELECT * FROM exhibits ORDER BY id DESC LIMIT 4");
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Museum Home</title>
    <style>
        :root { --dark: #1a1a1a; --gold: #c5a059; --light: #f4f4f4; }
        body { font-family: 'Georgia', serif; margin: 0; background: var(--light); }
        
        /* Hero Section */
        .hero {
            height: 80vh;
            background: linear-gradient(rgba(0,0,0,0.7), rgba(0,0,0,0.7)), url('https://images.unsplash.com/photo-1566127444979-b3d2b654e3d7?q=80&w=2070&auto=format&fit=crop');
            background-size: cover;
            background-position: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            color: white;
            text-align: center;
            padding: 20px;
        }

        .hero h1 { font-size: 4rem; margin: 0; color: var(--gold); }
        .hero p { font-size: 1.5rem; max-width: 800px; margin: 20px 0; font-style: italic; }

        /* General Layout */
        .container { max-width: 1200px; margin: auto; padding: 60px 20px; }
        .section-title { text-align: center; font-size: 2.5rem; margin-bottom: 40px; border-bottom: 2px solid var(--gold); display: inline-block; width: auto; margin-left: 50%; transform: translateX(-50%); }

        /* Grid Styling */
        .grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 30px; }
        .card { background: white; border-radius: 4px; overflow: hidden; box-shadow: 0 10px 20px rgba(0,0,0,0.1); transition: 0.3s; position: relative; }
        .card:hover { transform: translateY(-5px); }
        .card img { width: 100%; height: 250px; object-fit: cover; }
        .card-body { padding: 20px; text-align: center; }

        .btn-gold { 
            background: var(--gold); color: white; padding: 12px 30px; 
            text-decoration: none; border-radius: 2px; font-weight: bold; transition: 0.3s;
        }
        .btn-gold:hover { background: #b48a3d; }
    </style>
</head>
<body>

    <section class="hero">
        <h1>THE GREAT ARCHIVE</h1>
        <p>A window into the past, preserved for the future.</p>
        <a href="exhibits.php" class="btn-gold">Explore Collection</a>
    </section>

    <div class="container">
        <h2 class="section-title">Departments</h2>
        <div class="grid">
            <?php while($cat = $categories->fetch_assoc()): ?>
                <div class="card">
                    <img src="uploads/<?php echo $cat['image_path']; ?>" alt="Category Image">
                    <div class="card-body">
                        <h3><?php echo $cat['name']; ?></h3>
                        <a href="exhibits.php?cat=<?php echo $cat['id']; ?>" style="color: var(--gold); text-decoration: none;">View Department &rarr;</a>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div style="background: #fff; padding: 60px 0;">
        <div class="container">
            <h2 class="section-title">Latest Acquisitions</h2>
            <div class="grid">
                <?php while($item = $latest_exhibits->fetch_assoc()): ?>
                    <div class="card" style="box-shadow: none; border: 1px solid #eee;">
                        <img src="uploads/<?php echo $item['image_path']; ?>" alt="Artifact">
                        <div class="card-body" style="text-align: left;">
                            <span style="color: #888; font-size: 0.8rem; text-transform: uppercase;"><?php echo $item['artifact_year']; ?></span>
                            <h4 style="margin: 5px 0;"><?php echo $item['title']; ?></h4>
                            <p style="font-size: 0.9rem; color: #555;"><?php echo substr($item['description'], 0, 80); ?>...</p>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </div>
    </div>

    <footer style="background: var(--dark); color: white; text-align: center; padding: 40px;">
        <p>&copy; 2026 Museum Labo Catalog. All Rights Reserved.</p>
    </footer>

</body>
</html>