<aside class="sidebar">
    <div class="p-3">
        <h4>Menu</h4>
        <ul class="nav flex-column">
            <li class="nav-item">
                <a class="nav-link @if(Route::is('home')) active @endif" href="{{ route('home') }}">Home</a>
            </li>
            <li class="nav-item">
                <a class="nav-link @if(Route::is('services')) active @endif" href="{{ route('services') }}">Servizi</a>
            </li>
            <!-- Add more menu items as needed -->
        </ul>
    </div>
</aside>