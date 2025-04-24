<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    @yield('css')
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <div class="logo">
                <img src="/images/coachtech-logo-white.png" alt="COACHTECH">
            </div>
            @if (Auth::check())
            <form class="search-form">
                <input type="text" placeholder="なにをお探しですか？">
            </form>
            <nav class="nav-links">
                <a href="#">ログアウト</a>
                <a href="#">マイページ</a>
                <button class="sell-button">出品</button>
            </nav>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>

</body>

</html>
