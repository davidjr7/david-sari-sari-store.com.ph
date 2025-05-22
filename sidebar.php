<div class="sidebar-wrapper">
    <button class="btn btn-close sidebar-close d-lg-none"></button>

    <div class="sidebar-header">
        <h3>Categories</h3>
    </div>

    <ul class="sidebar-nav">
        <!-- Static Categories -->
        <li class="nav-item">
            <a href="bigas.php" class="nav-link active">
                <i class="fas fa-bowl-rice"></i>
                <span>Bigas</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="delata.php" class="nav-link">
                <i class="fas fa-box"></i>
                <span>De Lata</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="noodles.php" class="nav-link">
                <i class="fas fa-utensils"></i>
                <span>Noodles</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="softdrinks.php" class="nav-link">
                <i class="fas fa-glass-martini-alt"></i>
                <span>Beverages</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="snacks.php" class="nav-link">
                <i class="fas fa-cookie-bite"></i>
                <span>Snacks</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="kape.php" class="nav-link">
                <i class="fas fa-mug-hot"></i>
                <span>Kape</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="alak.php" class="nav-link">
                <i class="fas fa-wine-bottle"></i>
                <span>Alak</span>
            </a>
        </li>
        <li class="nav-item">
            <a href="frozenmeat.php" class="nav-link">
                <i class="fas fa-snowflake"></i>
                <span>Frozen Goods</span>
            </a>
        </li>
        
        <!-- Dynamic Categories from Database -->
        <?php
        // Database connection
        $conn = new mysqli("localhost", "root", "", "sari_sari_store");
        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }
        
        $result = $conn->query("SELECT * FROM categories ORDER BY name ASC");
        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $filename = strtolower(str_replace(' ', '', $row['name'])) . '.php';
                echo '
                <li class="nav-item">
                    <a href="'.$filename.'" class="nav-link">
                        <i class="'.$row['icon'].'"></i>
                        <span>'.$row['name'].'</span>
                    </a>
                </li>';
            }
        }
        $conn->close();
        ?>
    </ul>

    <div class="sidebar-footer">
        <div class="sidebar-promo">
            <h5>Special Offers</h5>
            <p>Check out our weekly discounts!</p>
        </div>
        
        <!-- Simplified Complaints Section -->
        <div class="sidebar-complain">
            <p class="complaint-text">Do you have any complaints or inquiries?</p>
            <a href="sumbungan.php" class="btn btn-complain">Isumbong mo sa sumbungan ng Hotdog</a>
        </div>
    </div>
</div>

<div class="sidebar-overlay"></div>

<!-- Rest of your existing styles and scripts remain the same -->

<style>
/* Sidebar Styles */
.sidebar-wrapper {
    width: 250px;
    height: 100vh;
    position: fixed;
    top: 0;
    left: 0;
    background-color: #fff;
    box-shadow: 0 0 15px rgba(0, 0, 0, 0.1);
    z-index: 1000;
    transition: all 0.3s ease;
    padding-top: 70px; /* Adjust based on header height */
    overflow-y: auto;
    transform: translateX(-100%);
}

.sidebar-wrapper.active {
    transform: translateX(0);
}

.sidebar-header {
    padding: 15px 20px;
    border-bottom: 1px solid #eee;
}

.sidebar-header h3 {
    margin: 0;
    font-size: 1.2rem;
    color: #333;
}

.sidebar-nav {
    list-style: none;
    padding: 0;
    margin: 0;
}

.sidebar-nav .nav-item {
    border-bottom: 1px solid #f5f5f5;
}

.sidebar-nav .nav-link {
    display: flex;
    align-items: center;
    padding: 12px 20px;
    color: #555;
    text-decoration: none;
    transition: all 0.2s;
}

.sidebar-nav .nav-link:hover {
    background-color: #f8f9fa;
    color: #333;
}


.sidebar-nav .nav-link i {
    margin-right: 10px;
    width: 20px;
    text-align: center;
}

.sidebar-footer {
    padding: 15px 20px;
    border-top: 1px solid #eee;
}

.sidebar-promo {
    background-color: #f8f9fa;
    padding: 10px;
    border-radius: 5px;
}

.sidebar-promo h5 {
    font-size: 1rem;
    margin-bottom: 5px;
}

.sidebar-promo p {
    font-size: 0.8rem;
    margin-bottom: 0;
    color: #666;
}

/* Simplified Complaints Section */
.sidebar-complain {
    margin-top: 10px;
}

.complaint-text {
    font-size: 12px;
    color: #555;
    margin-bottom: 8px;
}

.btn-complain {
    display: inline-block;
    padding: 6px 12px;
    background-color:rgb(208, 161, 146);
    color: white;
    text-align: center;
    border-radius: 5px;
    text-decoration: none;
    font-size: 12px;
    font-weight: bold;
    transition: background-color 0.2s;
}

.btn-complain:hover {
    background-color:rgb(183, 203, 101);
}

.sidebar-overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.5);
    z-index: 999;
    opacity: 0;
    visibility: hidden;
    transition: all 0.3s ease;
}

.sidebar-overlay.active {
    opacity: 1;
    visibility: visible;
}

.sidebar-close {
    position: absolute;
    top: 10px;
    right: 10px;
    z-index: 1001;
}

/* Responsive Styles */
@media (min-width: 992px) {
    .sidebar-wrapper {
        transform: translateX(0);
    }

    main.container-fluid {
        padding-left: 270px; /* sidebar width + some margin */
    }
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const sidebar = document.querySelector('.sidebar-wrapper');
    const sidebarOverlay = document.querySelector('.sidebar-overlay');
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebarClose = document.querySelector('.sidebar-close');
    
    // Toggle sidebar on mobile
    if (sidebarToggle) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.add('active');
            sidebarOverlay.classList.add('active');
        });
    }
    
    // Close sidebar
    if (sidebarClose) {
        sidebarClose.addEventListener('click', function() {
            sidebar.classList.remove('active');
            sidebarOverlay.classList.remove('active');
        });
    }
    
    // Close sidebar when clicking on overlay
    sidebarOverlay.addEventListener('click', function() {
        sidebar.classList.remove('active');
        sidebarOverlay.classList.remove('active');
    });
    
    // Auto-close sidebar when clicking a nav link on mobile
    const navLinks = document.querySelectorAll('.sidebar-nav .nav-link');
    navLinks.forEach(link => {
        link.addEventListener('click', function() {
            if (window.innerWidth < 992) {
                sidebar.classList.remove('active');
                sidebarOverlay.classList.remove('active');
            }
        });
    });
});
</script>
