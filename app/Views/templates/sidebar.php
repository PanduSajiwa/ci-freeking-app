<style>
.nav-sidebar .nav-item .nav-link { display: flex !important; opacity: 1 !important; visibility: visible !important; }
.nav-sidebar .nav-item { display: block !important; }
.nav-sidebar .nav-link p { display: block !important; }
</style>

<aside class="main-sidebar sidebar-dark-primary elevation-4" style="background: linear-gradient(135deg, #1e3c72 0%, #2a5298 100%);">
    <a href="<?= base_url('/dashboard') ?>" class="brand-link" style="border-bottom: 2px solid rgba(255,255,255,0.1); padding-bottom: 15px;">
        <i class="fas fa-parking brand-icon" style="font-size: 24px;"></i>
        <span class="brand-text font-weight-bold" style="font-size: 16px; letter-spacing: 0.5px;">Free Parking System</span>
    </a>

    <div class="sidebar" style="display: block !important;">
        <!-- User Panel -->
        <div class="user-panel mt-3 pb-3 mb-3 p-3" style="background: rgba(255,255,255,0.1); border-radius: 8px; margin: 15px;">
            <div class="info">
                <div style="color: #fff; font-weight: 600; margin-bottom: 5px;">
                    <?= session()->get('full_name') ?>
                </div>
                <small style="color: rgba(255,255,255,0.8); display: inline-block; background: rgba(255,255,255,0.15); padding: 3px 8px; border-radius: 12px;">
                    <i class="fas fa-shield-alt"></i> <?= ucfirst(str_replace('_', ' ', session()->get('role'))) ?>
                </small>
            </div>
        </div>

        <nav class="mt-2" style="display: block !important;">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false" style="padding: 0 10px; display: flex !important; flex-direction: column !important;">
                <?php if (in_array(session()->get('role'), ['parking_dept', 'operation_manager', 'admin', 'employee'])): ?>
                <li class="nav-item">
                    <a href="<?= base_url('/dashboard') ?>" class="nav-link" style="border-radius: 6px; margin-bottom: 8px;">
                        <i class="nav-icon fas fa-chart-line" style="color: #FFD700;"></i>
                        <p style="font-weight: 500;">Dashboard</p>
                    </a>
                </li>
                <?php endif; ?>

                <?php if (in_array(session()->get('role'), ['employee', 'parking_dept', 'operation_manager', 'admin'])): ?>
                <li class="nav-item">
                    <a href="<?= base_url('/parkingsubmissions') ?>" class="nav-link" style="border-radius: 6px; margin-bottom: 8px;">
                        <i class="nav-icon fas fa-clipboard-list" style="color: #87CEEB;"></i>
                        <p style="font-weight: 500;">Pengajuan Free Parking</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <?php if (in_array(session()->get('role'), ['operation_manager', 'parking_dept'])): ?>
                <li class="nav-item">
                    <a href="<?= base_url('/parkingsubmissions/approval') ?>" class="nav-link" style="border-radius: 6px; margin-bottom: 8px; background: rgba(255,255,255,0.05);">
                        <i class="nav-icon fas fa-check-double" style="color: #90EE90;"></i>
                        <p style="font-weight: 500;">Approval Pengajuan</p>
                    </a>
                </li>
                <?php endif; ?>
                
                <!-- Master Data Menu -->
                <?php if (in_array(session()->get('role'), ['employee'])): ?>
                <li class="nav-header" style="color: rgba(255,255,255,0.6); font-weight: 600; font-size: 11px; margin-top: 15px;">
                    <i class="fas fa-database"></i> DATA MASTER
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/vehicles') ?>" class="nav-link" style="border-radius: 6px; margin-bottom: 8px;">
                        <i class="nav-icon fas fa-car" style="color: #FFB6C1;"></i>
                        <p style="font-weight: 500;">Data Kendaraan</p>
                    </a>
                </li>

                <?php endif; ?>

                <!-- Parking Dept Data Menu -->
                <?php if (in_array(session()->get('role'), ['parking_dept'])): ?>
                <li class="nav-header" style="color: rgba(255,255,255,0.6); font-weight: 600; font-size: 11px; margin-top: 15px;">
                    <i class="fas fa-database"></i> DATA MASTER
                </li>

                <li class="nav-item">
                    <a href="<?= base_url('/customers') ?>" class="nav-link" style="border-radius: 6px; margin-bottom: 8px;">
                        <i class="nav-icon fas fa-user-plus" style="color: #FFB6C1;"></i>
                        <p style="font-weight: 500;">Register Karyawan</p>
                    </a>
                </li>

                <?php endif; ?>

                 <?php if (session()->get('role') === 'admin'): ?>
                <li class="nav-item">
                    <a href="<?= base_url('/users') ?>" class="nav-link" style="border-radius: 6px; margin-bottom: 8px;">
                        <i class="nav-icon fas fa-users-cog" style="color: #FF6347;"></i>
                        <p style="font-weight: 500;">Manajemen User</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Laporan visible only to admin and parking_dept -->
                <?php if (in_array(session()->get('role'), ['admin', 'parking_dept'])): ?>
                <li class="nav-header" style="color: rgba(255,255,255,0.6); font-weight: 600; font-size: 11px; margin-top: 15px;">
                    <i class="fas fa-chart-line"></i> LAPORAN
                </li>
                <li class="nav-item">
                    <a href="<?= base_url('/reports') ?>" class="nav-link" style="border-radius: 6px; margin-bottom: 8px;">
                        <i class="nav-icon fas fa-chart-bar" style="color: #DDA0DD;"></i>
                        <p style="font-weight: 500;">Laporan & Analytics</p>
                    </a>
                </li>
                <?php endif; ?>

                <!-- Logout Button -->
                <li class="nav-item" style="margin-top: 20px; border-top: 1px solid rgba(255,255,255,0.1); padding-top: 15px;">
                    <a href="<?= base_url('/auth/logout') ?>" class="nav-link" style="border-radius: 6px; background: rgba(255,255,255,0.1);">
                        <i class="nav-icon fas fa-sign-out-alt" style="color: #FF6B6B;"></i>
                        <p style="font-weight: 600;">Logout</p>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>