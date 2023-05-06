<!DOCTYPE html>
<html>
<head>
    <title>Laravel 8 CRUD Application</title>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.0.0-alpha/css/bootstrap.css" rel="stylesheet">
    
</head>
<body>
      <div class="container">
        <a class="navbar-brand" href="{{ route('logout') }}">Logout</a>
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
  
    </div>
<div class="container">
    @yield('content')
</div>
   
</body>
</html>