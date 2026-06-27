<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <a href="<?= base_url('/dashboard') ?>" class="brand-link">
        <i class="fas fa-parking brand-icon"></i>
        <span class="brand-text font-weight-light">Free Parking System</span>
    </a>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <div class="info">
                <a href="#" class="d-block"><?= session()->get('full_name') ?> (<?= session()->get('role') ?>)</a>
            </div>
        </div>

        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <?php if (in_array(session()->get('role'), ['parking_dept', 'operation_manager'])): ?>    
                <li class="nav-item">
                        <a href="<?= base_url('/dashboard') ?>" class="nav-link">
                            <i class="nav-icon fas fa-tachometer-alt"></i>
                            <p>Dashboard</p>
                        </a>
                    </li>
                <?php endif; ?>
                <?php if (in_array(session()->get('role'), ['employee', 'parking_dept', 'operation_manager'])): ?>
                <li class="nav-item">
                    <a href="<?= base_url('/parkingsubmissions') ?>" class="nav-link">
                        <i class="nav-icon fas fa-file-alt"></i>
                        <p>Pengajuan Free Parking</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (in_array(session()->get('role'), ['operation_manager', 'parking_dept'])): ?>
                <li class="nav-item">
                    <a href="<?= base_url('/parkingsubmissions/approval') ?>" class="nav-link">
                        <i class="nav-icon fas fa-check-circle"></i>
                        <p>Approval Pengajuan</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Master Data Menu -->
                <?php if (in_array(session()->get('role'), ['parking_dept', 'employee'])): ?>
                <li class="nav-header">MASTER DATA</li>

                <li class="nav-item">
                    <a href="<?= base_url('/customers') ?>" class="nav-link">
                        <i class="nav-icon fas fa-users"></i>
                        <p>Data Karyawan</p>
                    </a>
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/vehicles') ?>" class="nav-link">
                        <i class="nav-icon fas fa-car"></i>
                        <p>Data Kendaraan</p>
                    </a>
                </li>

                <?php endif; ?>

                 <?php if (session()->get('role') === 'admin'): ?>
                <li class="nav-item">
                    <a href="<?= base_url('/users') ?>" class="nav-link">
                        <i class="nav-icon fas fa-user-cog"></i>
                        <p>Manajemen User</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Laporan visible to admin, operation_manager and parking_dept -->
                <?php if (in_array(session()->get('role'), ['operation_manager', 'parking_dept'])): ?>
                <li class="nav-header">LAPORAN</li>
                <li class="nav-item">
                    <a href="<?= base_url('/reports') ?>" class="nav-link">
                        <i class="nav-icon fas fa-chart-bar"></i>
                        <p>Laporan & Analytics</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <li class="nav-item">
                    <a href="<?= base_url('/auth/logout') ?>" class="nav-link">
                        <i class="nav-icon fas fa-sign-out-alt"></i>
                        <p>Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>