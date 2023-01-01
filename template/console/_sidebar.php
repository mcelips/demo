<!-- Main Sidebar Container -->
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <a href="<?= route('console') ?>" class="brand-link">
        <div class="brand-text font-weight-lighter text-center">
            Welcome to Console
        </div>
    </a>

    <!-- Sidebar -->
    <div class="sidebar">
        <!-- Sidebar Menu -->
        <nav class="mt-2">
            <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
                <li class="nav-item">
                    <a href="<?= route('console') ?>" class="nav-link">
                        <i class="nav-icon fas fa-tachometer-alt"></i> Главная
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('console.magazines') ?>" class="nav-link">
                        <i class="nav-icon fas fa-book-open"></i> Журналы
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('console.competitions') ?>" class="nav-link">
                        <i class="nav-icon fas fa-award"></i> Конкурсы
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('console.coupons') ?>" class="nav-link">
                        <i class="nav-icon fas fa-money-check-alt"></i> Купоны
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('console.news') ?>" class="nav-link">
                        <i class="nav-icon fas fa-newspaper"></i> Новости
                    </a>
                </li>
                <li class="nav-item">
                    <a href="<?= route('console.users') ?>" class="nav-link">
                        <i class="nav-icon fas fa-users"></i> Пользователи
                    </a>
                </li>
            </ul>
        </nav>
        <!-- /.sidebar-menu -->
    </div>
    <!-- /.sidebar -->
</aside>