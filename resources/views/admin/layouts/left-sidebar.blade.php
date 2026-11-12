<aside class="left-sidebar" data-sidebarbg="skin5">
    <div class="scroll-sidebar">
        <nav class="sidebar-nav">
            <ul id="sidebarnav">
                <li class="sidebar-item {{ request()->routeIs('admin.dashboard') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.dashboard') }}"
                        aria-expanded="false">
                        <i class="mdi mdi-av-timer"></i>
                        <span class="hide-menu">Dashboard</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.users.*') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.users.index') }}"
                        aria-expanded="false">
                        <i class="mdi mdi-account-multiple"></i>
                        <span class="hide-menu">Quản lý User</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.products.*') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.products.index') }}"
                        aria-expanded="false">
                        <i class="mdi mdi-package-variant-closed"></i>
                        <span class="hide-menu">Quản lý Product</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.histories.*') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.histories.index') }}"
                        aria-expanded="false">
                        <i class="mdi mdi-history"></i>
                        <span class="hide-menu">Lịch sử mua hàng</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.blogs.*') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.blogs.index') }}"
                        aria-expanded="false">
                        <i class="mdi mdi-book"></i>
                        <span class="hide-menu">Blog</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.country.*') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link"
                        href="{{ route('admin.country.index') }}" aria-expanded="false">
                        <i class="mdi mdi-earth"></i>
                        <span class="hide-menu">Country</span>
                    </a>
                </li>

                <li class="sidebar-item {{ request()->routeIs('admin.profile') ? 'selected' : '' }}">
                    <a class="sidebar-link waves-effect waves-dark sidebar-link" href="{{ route('admin.profile') }}"
                        aria-expanded="false">
                        <i class="mdi mdi-account-network"></i>
                        <span class="hide-menu">Profile</span>
                    </a>
                </li>
            </ul>
        </nav>
    </div>
</aside>
