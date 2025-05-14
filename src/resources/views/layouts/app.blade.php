<!DOCTYPE html>
<html lang="ja">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="{{ asset('css/sanitize.css') }}">
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @yield('css')
</head>

<body>
    <header class="site-header">
        <div class="header-inner">
            <div class="logo">
                <a href="/">
                    <img src="{{ asset('images/logo.svg') }}" alt="ロゴ">
                </a>
            </div>
            <div class="search-form">
                <input type="text" id="search-input" placeholder="なにをお探しですか？">
            </div>
            @if (Auth::check())
            <nav class="nav-links">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="logout-button">ログアウト</button>
                </form>
                <a href="/mypage" class="a-link-color-white">マイページ</a>
                <a href="/sell" class="sell-button">出品</a>
            </nav>
            @else
            <nav class="nav-links" style="margin-left:auto;">
                <a href="/login" class="a-link-color-white login-button">ログイン</a>
                <a href="/register" class="a-link-color-white">登録</a>
            </nav>
            @endif
        </div>
    </header>
    <main>
        @if (session('message'))
        <div class="flash-message">
            {{ session('message') }}
        </div>
        @endif

        @yield('content')
    </main>
    @yield('js')
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // 検索フィールドの参照
            const searchInput = document.getElementById('search-input');

            // URLから検索キーワードを取得して検索フィールドに設定
            const urlParams = new URLSearchParams(window.location.search);
            const searchParam = urlParams.get('search');
            if (searchParam) {
                searchInput.value = searchParam;
            }

            // おすすめとマイリストのリンクを取得
            const links = document.querySelectorAll('.index-link, .mylist-link');

            // 各リンクにクリックイベントを追加
            links.forEach(link => {
                link.addEventListener('click', function(event) {
                    // デフォルトの遷移をキャンセル
                    event.preventDefault();

                    // 現在のリンクのURL
                    const linkUrl = new URL(this.href);

                    // 検索フィールドの値を取得
                    const searchValue = searchInput.value.trim();

                    // 検索値があれば、URLに追加
                    if (searchValue) {
                        linkUrl.searchParams.set('search', searchValue);
                    }

                    // 修正したURLに遷移
                    window.location.href = linkUrl.toString();
                });
            });
        });
    </script>
</body>

</html>
