 <style>
        .sidebar {
            min-height: 100vh;
            background-color: #343a40;
        }
        .sidebar .nav-link {
            color: rgba(255, 255, 255, 0.75);
        }
        .sidebar .nav-link:hover, .sidebar .nav-link.active {
            color: white;
            background-color: rgba(255, 255, 255, 0.1);
        }
        .main-content {
            background-color: #f8f9fa;
            min-height: 100vh;
        }
    </style>
<div class="col-md-3 col-lg-2 sidebar p-0">
    <div class="p-3 text-white">
        <h4>SuperAdmin Panel</h4>
        <p class="mb-0">Welcome, <?php echo $_SESSION['user']['username']; ?></p>
    </div>
    
    <ul class="nav flex-column">
        <li class="nav-item">
            <a class="nav-link active" href="superadmin_dashboard.php">
                Dashboard
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="collapse" href="#createUserCollapse">
                Create User
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="superadmin_products.php">
               Add Product
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" href="add_categories_superadmin.php">
               Categories
            </a>
        </li>
         <li class="nav-item">
            <a class="nav-link" href="superadmin_settings.php">
                Settings
            </a>
        </li>
        <div class="collapse show" id="createUserCollapse">
            <div class="p-3 bg-dark">
                <form method="POST">
                    <div class="mb-3">
                        <input type="text" class="form-control form-control-sm" name="username" placeholder="Username" required>
                    </div>
                    <div class="mb-3">
                        <input type="password" class="form-control form-control-sm" name="password" placeholder="Password" required>
                    </div>
                    <div class="mb-3">
                        <select class="form-select form-select-sm" name="role" required>
                            <option value="">Select Role</option>
                            <option value="superadmin">SuperAdmin</option>
                            <option value="admin">Admin</option>
                            <option value="editor">Editor</option>
                            <option value="client">Client</option>
                        </select>
                    </div>
                    <button type="submit" name="create_user" class="btn btn-primary btn-sm w-100">Create</button>
                </form>
            </div>
        </div>
        <li class="nav-item">
            <a class="nav-link" href="logout.php">
                Logout
            </a>
        </li>
    </ul>
</div>