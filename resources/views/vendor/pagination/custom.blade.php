@if ($paginator->hasPages())
    <nav>
        <ul class="pagination">
            {{-- 前へリンク --}}
            @if (!$paginator->onFirstPage())
                <li><a href="{{ $paginator->previousPageUrl() }}" rel="prev">前へ</a></li>
            @endif

            {{-- ページ番号 --}}
            @php
                $start = $paginator->currentPage() - 1;
                $end = $paginator->currentPage() + 1;
                if($start < 1) {
                    $start = 1;
                    $end = 3;
                }
                if($end > $paginator->lastPage()) {
                    $end = $paginator->lastPage();
                    $start = max(1, $end - 2);
                }
            @endphp

            @for ($i = $start; $i <= $end; $i++)
                @if ($i == $paginator->currentPage())
                    <li class="active" aria-current="page"><span>{{ $i }}</span></li>
                @else
                    <li><a href="{{ $paginator->url($i) }}">{{ $i }}</a></li>
                @endif
            @endfor

            {{-- 次へリンク --}}
            @if ($paginator->hasMorePages())
                <li><a href="{{ $paginator->nextPageUrl() }}" rel="next">次へ</a></li>
            @endif
        </ul>
    </nav>
@endif