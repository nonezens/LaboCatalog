<?php 
include 'db.php'; 
include 'header.php'; 

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$result = $conn->query("SELECT * FROM categories");
?>

<div style="padding: 20px; font-family: sans-serif;">
    <h1 style="text-align:center;">Explore Departments</h1>
    
    <?php if (isset($_GET['deleted'])): ?>
        <div style="background: #d4edda; color: #155724; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; text-align: center;">Category deleted successfully!</div>
    <?php endif; ?>
    <?php if (isset($_GET['error']) && $_GET['error'] === 'has_exhibits'): ?>
        <div style="background: #f8d7da; color: #721c24; padding: 12px 20px; border-radius: 4px; margin-bottom: 20px; text-align: center;">Cannot delete category with exhibits. Please delete all artifacts in this category first.</div>
    <?php endif; ?>
    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-top: 30px;">
        
        <?php while($cat = $result->fetch_assoc()): ?>
            <div style="border: 1px solid #eee; text-align: center; border-radius: 8px; overflow: hidden; box-shadow: 0 4px 6px rgba(0,0,0,0.1);">
                <img src="uploads/<?php echo $cat['image_path']; ?>" style="width:100%; height:200px; object-fit:cover;">
                <div style="padding: 15px;">
                    <h3><?php echo $cat['name']; ?></h3>
                    <a href="exhibits.php?cat=<?php echo $cat['id']; ?>" 
                       style="display:inline-block; padding:8px 15px; background:#2c3e50; color:white; text-decoration:none; border-radius:4px; margin-right: 5px;">
                       View Exhibits
                    </a>
                    <?php if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in']): ?>
                        <a href="edit_categories.php?id=<?php echo $cat['id']; ?>" 
                           style="display:inline-block; padding:8px 15px; background:#555; color:white; text-decoration:none; border-radius:4px; margin-right: 5px;">
                           Edit
                        </a>
                        <a href="delete_categories.php?id=<?php echo $cat['id']; ?>" 
                           style="display:inline-block; padding:8px 15px; background:#d32f2f; color:white; text-decoration:none; border-radius:4px;" 
                           onclick="return confirm('Are you sure you want to delete this category?');">
                           Delete
                        </a>
                    <?php endif; ?>
                </div>
            </div>
        <?php endwhile; ?>

    </div>
</div>