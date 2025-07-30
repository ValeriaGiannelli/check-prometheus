<aside class="sidebar">
    <div class="p-3">
        <h4>Menu</h4>
        <ul class="nav flex-column">
            <!--lista clienti-->
            <li class="nav-item">
                <a class="nav-link @if(Route::is('customer_metrics')) active @endif" href="{{ route('customer.metrics') }}">Clienti</a>
            </li>

            <!--Aggiunta/modifica clienti-->
            <li class="nav-item">
                <a class="nav-link @if(Route::is('customers.index')) active @endif" href="{{ route('customers.index') }}">Gestione clienti</a>
            </li>

            <!--BOX-->
            <li class="nav-item">
                <a class="nav-link @if(Route::is('home')) active @endif" href="{{ route('home') }}">Box</a>
            </li>

            <!--SERVIZI-->
            <li class="nav-item">
                <a class="nav-link @if(Route::is('services')) active @endif" href="{{ route('services') }}">Servizi</a>
            </li>

            <!--SQL-->
             <li class="nav-item">
                <a class="nav-link @if(Route::is('sql_metrics')) active @endif" href="{{ route('sql_metrics') }}">SQL</a>
            </li>

            <!--VERSIONI-->
            <li class="nav-item">
                <a class="nav-link @if(Route::is('info_version')) active @endif" href="{{ route('info_version') }}">Versioni</a>
            </li>
            
            <!--LOG-->
            <li class="nav-item">
                <a class="nav-link @if(Route::is('log')) active @endif" href="{{ route('logs.index') }}">Log</a>
            </li>
            <!-- Add more menu items as needed -->
        </ul>
    </div>
</aside>