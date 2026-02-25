<?php 
include 'db.php'; 
include 'header.php'; 

$result = $conn->query("SELECT * FROM categories");
?>

<div style="padding: 20px; font-family: sans-serif;">
    <h1 style="text-align:center;">Explore Departments</h1>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
        
        <?php while($cat = $result->fetch_assoc()): ?>
            <div style="border: 1px solid #eee; text-align: center; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <img src="uploads/<?php echo $cat['image_path']; ?>" style="width:100%; height:200px; object-fit:cover;">
                <div style="padding: 15px;">
                    <h3><?php echo $cat['name']; ?></h3>
                    <a href="exhibits.php?cat=<?php echo $cat['id']; ?>" 
                       style="display:inline-block; padding:8px 15px; background:#2c3e50; color:white; text-decoration:none; border-radius:4px;">
                       View Exhibits
                    </a>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>