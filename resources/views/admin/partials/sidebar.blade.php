<a href="{{ route('admin.dashboard') }}" class="sidebar-brand fs-5 d-none d-lg-flex align-items-center mb-3 px-2">
    <i class="bi bi-palette2 me-2"></i>BoteroPop
</a>

<ul class="nav nav-pills flex-column gap-1">
    <li class="nav-item">
        <a href="{{ route('admin.dashboard') }}"
           class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
            <i class="bi bi-speedometer2"></i>Dashboard
        </a>
    </li>

    <li class="sidebar-heading mt-3 mb-1 px-2">Contenu</li>

    <li class="nav-item">
        <a href="{{ route('admin.oeuvres.index') }}"
           class="nav-link {{ request()->routeIs('admin.oeuvres.*') ? 'active' : '' }}">
            <i class="bi bi-image"></i>Œuvres
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.evenements.index') }}"
           class="nav-link {{ request()->routeIs('admin.evenements.*') ? 'active' : '' }}">
            <i class="bi bi-calendar-event"></i>Évènements
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.inscriptions.index') }}"
           class="nav-link {{ request()->routeIs('admin.inscriptions.*') ? 'active' : '' }}">
            <i class="bi bi-people"></i>Inscriptions
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.tags.index') }}"
           class="nav-link {{ request()->routeIs('admin.tags.*') ? 'active' : '' }}">
            <i class="bi bi-tags"></i>Tags
        </a>
    </li>

    <li class="sidebar-heading mt-3 mb-1 px-2">Administration</li>

    <li class="nav-item">
        <a href="{{ route('admin.utilisateurs.index') }}"
           class="nav-link {{ request()->routeIs('admin.utilisateurs.*') ? 'active' : '' }}">
            <i class="bi bi-person-gear"></i>Utilisateurs
        </a>
    </li>
    <li class="nav-item">
        <a href="{{ route('admin.parametres.edit') }}"
           class="nav-link {{ request()->routeIs('admin.parametres.*') ? 'active' : '' }}">
            <i class="bi bi-gear"></i>Paramètres
        </a>
    </li>
</ul>

<form method="POST" action="{{ route('logout') }}" class="mt-auto pt-3">
    @csrf
    <button type="submit" class="btn btn-outline-light w-100">
        <i class="bi bi-box-arrow-right me-1"></i>Déconnexion
    </button>
</form>
