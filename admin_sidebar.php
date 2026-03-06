<style>
    /* Admin Shared Layout & Styling */
    .admin-layout { display: flex; min-height: calc(100vh - 70px); font-family: 'Segoe UI', Tahoma, Geneva, sans-serif; }
    
    /* Left Sidebar */
    .sidebar { width: 250px; background: #2c3e50; color: white; padding: 30px 20px; flex-shrink: 0; position: sticky; top: 70px; height: calc(100vh - 70px); box-sizing: border-box; overflow-y: auto; }
    .sidebar h3 { color: #c5a059; margin-top: 0; margin-bottom: 20px; font-size: 1.2rem; text-transform: uppercase; letter-spacing: 1px; }
    .sidebar-menu { list-style: none; padding: 0; margin: 0; }
    .sidebar-menu li { margin-bottom: 10px; }
    .sidebar-menu a { color: #ecf0f1; text-decoration: none; display: block; padding: 12px 15px; border-radius: 6px; transition: 0.3s; font-weight: bold; }
    .sidebar-menu a:hover { background: #34495e; color: #c5a059; padding-left: 20px; }
    
    /* Right Content Area */
    .main-content { flex-grow: 1; padding: 40px; box-sizing: border-box; max-width: calc(100% - 250px); }
    
    /* Headers & Stats */
    .dashboard-header { display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px; background: white; padding: 25px; border-radius: 8px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); }
    .dashboard-header h2 { margin: 0; color: #2c3e50; font-size: 2rem; }
    .stats { display: flex; gap: 20px; font-size: 1.1rem; color: #555; font-weight: bold; margin-top: 10px; flex-wrap: wrap; }
    .stats span { color: #c5a059; }
    
    /* Buttons */
    .action-buttons { display: flex; gap: 15px; flex-wrap: wrap; }
    .btn-add { padding: 12px 20px; color: white; text-decoration: none; border-radius: 5px; font-weight: bold; transition: 0.3s; display: inline-block; text-align: center; }
    .btn-add:hover { transform: translateY(-2px); box-shadow: 0 4px 8px rgba(0,0,0,0.1); }
    .bg-exhibit { background: #27ae60; }
    .bg-category { background: #2980b9; }
    .action-btn { padding: 8px 12px; color: white; text-decoration: none; border-radius: 4px; font-size: 0.85rem; transition: 0.3s; display: inline-block; text-align: center; margin-right: 5px;}
    .action-btn:hover { opacity: 0.8; }
    .btn-edit { background: #f39c12; }
    .btn-delete { background: #e74c3c; }
    .btn-approve { background: #2ecc71; margin-bottom: 5px; display: block; }
    .btn-reject { background: #e74c3c; display: block; }
    
    /* Tables (Made scrollable for mobile!) */
    .table-title { color: #2c3e50; margin-top: 0; margin-bottom: 15px; border-bottom: 2px solid #c5a059; display: inline-block; padding-bottom: 5px; font-size: 1.5rem; }
    .table-container { background: white; border-radius: 8px; box-shadow: 0 4px 8px rgba(0,0,0,0.1); margin-bottom: 50px; overflow-x: auto; -webkit-overflow-scrolling: touch; }
    table { width: 100%; border-collapse: collapse; min-width: 700px; /* Forces table to stay wide so user can swipe left/right */ }
    th, td { padding: 15px; text-align: left; border-bottom: 1px solid #eee; vertical-align: middle; }
    th { background: #2c3e50; color: white; text-transform: uppercase; font-size: 0.85rem; letter-spacing: 0.5px; }
    tr:hover { background: #f9f9f9; }
    
    /* Badges */
    .badge { padding: 4px 10px; border-radius: 12px; font-weight: bold; font-size: 0.8rem; color: white; display: inline-block; }
    .badge-pending { background: #f1c40f; color: #333; }
    .badge-approved { background: #2ecc71; }
    .badge-rejected { background: #e74c3c; }

    /* --- RESPONSIVE ADMIN DASHBOARD --- */
    @media (max-width: 992px) {
        /* On tablets/laptops, stack the top header stats and buttons */
        .dashboard-header { flex-direction: column; align-items: flex-start; gap: 20px; }
    }

    @media (max-width: 768px) {
        /* On cellphones, move the sidebar to the top! */
        .admin-layout { flex-direction: column; }
        .sidebar { 
            width: 100%; 
            height: auto; 
            position: static; 
            padding: 20px; 
        }
        .sidebar-menu { 
            display: flex; 
            flex-wrap: wrap; 
            gap: 10px; 
        }
        .sidebar-menu li { 
            margin-bottom: 0; 
            flex: 1 1 45%; /* Makes buttons sit 2-per-row */
        }
        .sidebar-menu a {
            text-align: center;
            background: #34495e;
        }
        .main-content { 
            max-width: 100%; 
            padding: 20px; 
        }
    }

    @media (max-width: 480px) {
        /* On very small phones, stack the sidebar menu 1-per-row */
        .sidebar-menu li { flex: 1 1 100%; }
        .action-buttons { width: 100%; }
        .btn-add { width: 100%; box-sizing: border-box; }
    }
</style>

<div class="admin-layout">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <aside class="sidebar">
        <h3>Admin Menu</h3>
        <ul class="sidebar-menu">
            <li><a href="admin_dashboard.php">📊 Overview</a></li>
            <li><a href="manage_visitors.php">👥 Visitor Log</a></li>
            <li><a href="manage_artifacts.php">🖼️ Manage Artifacts</a></li>
            <li><a href="manage_departments.php">📁 Manage Departments</a></li>
            <li style="margin-top: 15px;">
                <a href="add_exhibit.php" style="color: #2ecc71; border: 1px solid #2ecc71;">➕ Add Artifact</a>
            </li>
            <li>
                <a href="add_category.php" style="color: #3498db; border: 1px solid #3498db;">➕ Add Department</a>
            </li>
        </ul>
    </aside>

    <main class="main-content">