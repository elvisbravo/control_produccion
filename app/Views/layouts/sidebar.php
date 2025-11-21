<nav class="col-md-2 d-md-block bg-dark sidebar vh-100 position-fixed">
    <div class="position-sticky pt-3">
        <div class="text-center mb-4">
            <h5 class="text-white">Control de Producción</h5>
            <small class="text-muted">ES Consultores</small>
        </div>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link <?= (current_url() == base_url('dashboard')) ? 'active text-white' : 'text-white-50' ?>" href="/dashboard">
                    <i class="fas fa-tachometer-alt me-2"></i> Dashboard
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'servicios') !== false) ? 'active text-white' : 'text-white-50' ?>" href="/servicios">
                    <i class="fas fa-briefcase me-2"></i> Servicios
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'clientes') !== false) ? 'active text-white' : 'text-white-50' ?>" href="/clientes">
                    <i class="fas fa-users me-2"></i> Clientes
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'calendario') !== false) ? 'active text-white' : 'text-white-50' ?>" href="/calendario">
                    <i class="fas fa-calendar-alt me-2"></i> Calendario
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'feriados') !== false) ? 'active text-white' : 'text-white-50' ?>" href="/feriados">
                    <i class="fas fa-calendar-times me-2"></i> Feriados
                </a>
            </li>
            <li class="nav-item">
                <a class="nav-link <?= (strpos(current_url(), 'usuarios') !== false) ? 'active text-white' : 'text-white-50' ?>" href="/usuarios">
                    <i class="fas fa-user-cog me-2"></i> Usuarios
                </a>
            </li>
            <li class="nav-item mt-5">
                <a class="nav-link text-danger" href="/auth/logout">
                    <i class="fas fa-sign-out-alt me-2"></i> Cerrar Sesión
                </a>
            </li>
        </ul>
    </div>
</nav>