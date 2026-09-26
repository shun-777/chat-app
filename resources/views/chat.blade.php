<!DOCTYPE html>
<html lang="ja">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>チャットアプリ</title>
  <script src="https://cdn.jsdelivr.net/npm/@tailwindcss/browser@4"></script>
  <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@1/index.js"></script>
</head>
<body class="bg-gray-50">
  <div class="max-w-md mx-auto h-screen flex flex-col bg-gray-200 shadow-lg">

    <header class="bg-white px-4 py-3 flex items-center justify-between shadow-sm">
      <a href="/rooms" class="text-sm text-gray-500">← 一覧</a>
      <h1 class="font-bold">{{ $room->name }}</h1>
      <!-- <h1 class="font-bold">{{ $room->name }}（{{ count($messages) }}）</h1> -->
      <a href="/enter" class="text-sm text-gray-500">{{ session('nickname') }}</a>
    </header>

    <main class="flex-1 overflow-y-auto p-4">
      <div id="messages">
      @foreach ($messages as $message)
        @if ($message->name === session('nickname'))
          <div class="mb-3 text-right">
            <p class="text-xs text-gray-500 mb-1">{{ $message->name }} {{ $message->created_at->format('H:i') }}</p>
            <div class="bg-green-500 text-white rounded-2xl px-4 py-2 inline-block max-w-[75%] text-left">
              <p>{{ $message->body }}</p>
            </div>
            <div class="mt-1">
              <a href="/messages/{{ $message->id }}/edit" class="text-xs text-gray-500">編集</a>
              <form action="/messages/{{ $message->id }}" method="POST" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="text-xs text-red-400">削除</button>
              </form>
            </div>
          </div>
        @else
          <div class="mb-3 flex gap-2">
            <div class="w-8 h-8 shrink-0 rounded-full bg-gray-400 text-white text-sm flex items-center justify-center">{{ mb_substr($message->name, 0, 1) }}</div>
            <div class="max-w-[75%]">
              <p class="text-xs text-gray-500 mb-1">{{ $message->name }} {{ $message->created_at->format('H:i') }}</p>
              <div class="bg-white rounded-2xl px-4 py-2 shadow-sm">
                <p>{{ $message->body }}</p>
              </div>
            </div>
          </div>
        @endif
      @endforeach
      </div>
    </main>
    <div id="emoji-picker-container" class="hidden absolute bottom-16 left-4 z-50 shadow-xl rounded-2xl overflow-hidden border border-gray-200">
      <emoji-picker style="--num-columns: 6; width: 300px; height: 320px;"></emoji-picker>
    </div>
    <footer class="bg-white p-3">
      <form action="/rooms/{{ $room->id }}" method="POST" class="flex gap-2 items-center">
        @csrf
        <button type="button" id="emoji-toggle-btn" class="shrink-0 bg-gray-100 hover:bg-gray-200 text-lg rounded-full w-10 h-10 flex items-center justify-center transition">
          😊
        </button>
        <input type="text" id="body" name="body" placeholder="メッセージを入力" value="{{ old('body') }}" autocomplete="off"
          class="flex-1 bg-gray-100 rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-green-500">
        <button type="submit" class="shrink-0 bg-green-500 text-white font-bold rounded-full px-5 py-2 hover:bg-green-600 transition">送信</button>
      </form>
      @error('body')
        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
      @enderror
    </footer>
  </div>
  <script>
    // --- 絵文字ピッカー制御 ---
    const toggleBtn = document.querySelector('#emoji-toggle-btn');
    const pickerContainer = document.querySelector('#emoji-picker-container');
    const picker = document.querySelector('emoji-picker');
    const bodyInput = document.querySelector('#body');

    // ボタンクリックでピッカーの表示/非表示切り替え
    toggleBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      pickerContainer.classList.toggle('hidden');
    });

    // 絵文字がクリックされたら入力欄の末尾に追加
    picker.addEventListener('emoji-click', event => {
      bodyInput.value += event.detail.unicode;
      bodyInput.focus();
    });

    // ピッカー外をクリックしたら閉じる
    document.addEventListener('click', (e) => {
      if (!pickerContainer.contains(e.target) && e.target !== toggleBtn) {
        pickerContainer.classList.add('hidden');
      }
    });

    // --- ポーリング（自動更新） ---
    setInterval(async () => {
      const res = await fetch(location.href);
      const html = await res.text();
      const doc = new DOMParser().parseFromString(html, 'text/html');
      document.querySelector('#messages').innerHTML = doc.querySelector('#messages').innerHTML;
    }, 5000);
  </script>
</body>
</html>