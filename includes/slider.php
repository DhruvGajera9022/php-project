<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="../dashboard/dashboard.php" class="brand-link">
        <img src="../../assets/img/AdminLTELogo.png" alt="AdminLTE Logo" class="brand-image img-circle elevation-3" style="opacity: .8">
        <span class="brand-text font-weight-light">AdminLTE 3</span>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- User panel -->
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="image">
                <img src="../../assets/img/userimage/<?php echo $image; ?>" class="img-circle elevation-2" alt="User Image">
            </div>
            <div class="info">
                <a href="#" class="d-block"><?php echo $fname; ?></a>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="../dashboard/dashboard.php" class="nav-link <?php echo $title == 'Dashboard' ? 'active' : ''; ?>">
                        <i class="nav-icon fas fa-tachometer-alt"></i>
                        <p>Dashboard</p>
                    </a>
                </li>
                <?php if ($role == "Admin") { ?>

                    <li class="nav-item">
                        <a href="../user/users.php" class="nav-link <?php echo $title == 'Users' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-user-alt"></i>
                            <p>Users</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../role/roles.php" class="nav-link <?php echo $title == 'Role' ? 'active' : ''; ?>">
                            <i class="nav-icon 	fas fa-user-cog"></i>
                            <p>Roles</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../product/products.php" class="nav-link <?php echo $title == 'Products' ? 'active' : ''; ?>">
                            <i class="nav-icon fab fa-product-hunt"></i>
                            <p>Master</p>
                        </a>
                    </li>
                    <li class="nav-item">
                        <a href="../policy/policy.php" class="nav-link <?php echo $title == 'Policy' ? 'active' : ''; ?>">
                            <i class="nav-icon fas fa-file-contract"></i>
                            <p>Policy</p>
                        </a>
                    </li>

                <?php } ?>
            </ul>
        </nav>
    </div>
    <!-- /.sidebar -->
</aside>