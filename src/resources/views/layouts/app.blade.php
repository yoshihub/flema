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
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-button">ログアウト</button>
                </form>
                <a href="/mypage" class="mypage-link">マイページ</a>
                <a href="/sell" class="sell-button">出品</a>
            </nav>
            @endif
        </div>
    </header>
    <main>
        @yield('content')
    </main>

</body>

</html>
