<!DOCTYPE html>
<html>
<head>
    <title>@yield('title')</title>
</head>
<body>
    <header>
        <h1>My App</h1>
    </header>
    <nav>
        <ul>
            <li><a href="{{ route('logout') }}">Logout</a></li>
        </ul>
    </nav>
    <main>
        @yield('content')
    </main>
    <aside>
        @yield('sidebar')
    </aside>
    <footer>
        <p>&copy; 2023 My App</p>
    </footer>
</body>
</html>
