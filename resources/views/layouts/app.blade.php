<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    @yield('javascript')
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" rel="stylesheet">
    <link href="/css/layout.css" rel="stylesheet">
</head>
<body>
    <div id="app">
        <nav class="navbar navbar-expand-md navbar-light bg-white shadow-sm">
            <div class="container">
                <a class="navbar-brand" href="{{ url('/') }}">
                    {{ config('app.name', 'Laravel') }}
                </a>
                <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="{{ __('Toggle navigation') }}">
                    <span class="navbar-toggler-icon"></span>
                </button>

                <div class="collapse navbar-collapse" id="navbarSupportedContent">
                    <!-- Left Side Of Navbar -->
                    <ul class="navbar-nav me-auto">

                    </ul>

                    <!-- Right Side Of Navbar -->
                    <ul class="navbar-nav ms-auto">
                        <!-- Authentication Links -->
                        @guest
                            @if (Route::has('login'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('login') }}">{{ __('Login') }}</a>
                                </li>
                            @endif

                            @if (Route::has('register'))
                                <li class="nav-item">
                                    <a class="nav-link" href="{{ route('register') }}">{{ __('Register') }}</a>
                                </li>
                            @endif
                        @else
                            <li class="nav-item dropdown">
                                <a id="navbarDropdown" class="nav-link dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false" v-pre>
                                    {{ Auth::user()->name }}
                                </a>

                                <div class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdown">
                                    <a class="dropdown-item" href="{{ route('logout') }}"
                                       onclick="event.preventDefault();
                                                     document.getElementById('logout-form').submit();">
                                        {{ __('Logout') }}
                                    </a>

                                    <form id="logout-form" action="{{ route('logout') }}" method="POST" class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </li>
                        @endguest
                    </ul>
                </div>
            </div>
        </nav>
        <main class="">
            <div class="row">
             <div class="col-md-2 p-0">
                <div class="card">
                <div class="card-header">????????????</div>
                    <div class="card-body">
                    <a href="/" class="card-text" style="text-decoration: none;">
                            ????????????
                        </a>
                        <br>
                    @foreach($tags as $tag)
                        <a href="/?tag={{$tag['id']}}" class="card-text" style="text-decoration: none;">
                            {{$tag['name']}}<br>
                        </a>
                        @endforeach
                    </div>
                </div>
                </div>
                <!-- md-2 -->
             <div class="col-md-4 p-0">
                <div class="card">
                <div class="card-header card-header-flex">????????????
                    <a href="{{route('home')}}"><i class="fas fa-plus-circle"></i></a>
                </div>
                    <div class="card-body">
                        @foreach($memos as $memo)
                        <div class="mb-2">
                        <a href="/edit/{{$memo['id']}}" class="card-text" style="text-decoration: none;">
                         <span >{{$memo['content']}}</span><br>
                        @if(!empty($memo['thumbnail']))
                        <i class="fa-solid fa-image d-block"></i>
                        @endif
                        </a>
                        </div>
                        @endforeach
                    </div>
                </div>
                </div>
                <!-- md-4 -->
             <div class="col-md-6 p-0">
            @yield('content')
             </div>
             </div>
            <!-- row -->
        </main>
    </div>
</body>
</html>
