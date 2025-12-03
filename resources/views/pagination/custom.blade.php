@if ($paginator->hasPages())
    <nav aria-label="Page navigation">
        <ul class="pagination justify-content-center">
            {{-- Previous Page Link --}}
            @if ($paginator->onFirstPage())
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.previous')">
                    <span class="page-link" aria-hidden="true">&lsaquo;</span>
                </li>
            @else
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->previousPageUrl() }}" rel="prev"
                        aria-label="@lang('pagination.previous')">&lsaquo;</a>
                </li>
            @endif

            {{-- Pagination Elements --}}
            @foreach ($elements as $element)
                {{-- "Three Dots" Separator --}}
                @if (is_string($element))
                    <li class="page-item disabled" aria-disabled="true"><span class="page-link">{{ $element }}</span></li>
                @endif

                {{-- Array Of Links --}}
                @if (is_array($element))
                    @foreach ($element as $page => $url)
                        @if ($page == $paginator->currentPage())
                            <li class="page-item active" aria-current="page"><span class="page-link">{{ $page }}</span></li>
                        @else
                            <li class="page-item"><a class="page-link" href="{{ $url }}">{{ $page }}</a></li>
                        @endif
                    @endforeach
                @endif
            @endforeach

            {{-- Next Page Link --}}
            @if ($paginator->hasMorePages())
                <li class="page-item">
                    <a class="page-link" href="{{ $paginator->nextPageUrl() }}" rel="next"
                        aria-label="@lang('pagination.next')">&rsaquo;</a>
                </li>
            @else
                <li class="page-item disabled" aria-disabled="true" aria-label="@lang('pagination.next')">
                    <span class="page-link" aria-hidden="true">&rsaquo;</span>
                </li>
            @endif
        </ul>
    </nav>

    <style>
        /* Pagination Styling with Theme Colors */
        .pagination .page-link {
            color: var(--color-primary);
            background-color: var(--bg-primary);
            border-color: var(--border-color);
            transition: all var(--transition-base);
        }

        .pagination .page-link:hover {
            color: var(--color-primary-dark);
            background-color: var(--color-gray-100);
            border-color: var(--color-primary-light);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: white;
        }

        .pagination .page-item.disabled .page-link {
            color: var(--color-gray-500);
            background-color: var(--bg-secondary);
            border-color: var(--border-color);
        }

        /* Dark Mode Pagination */
        body.bg-dark .pagination .page-link {
            background-color: #1e2124;
            border-color: #2a2f35;
            color: #e6e6e6;
        }

        body.bg-dark .pagination .page-link:hover {
            background-color: #2a2f35;
            border-color: var(--color-primary);
            color: var(--color-primary-light);
        }

        body.bg-dark .pagination .page-item.active .page-link {
            background-color: var(--color-primary);
            border-color: var(--color-primary);
            color: #fff;
        }

        body.bg-dark .pagination .page-item.disabled .page-link {
            background-color: #1a1d20;
            border-color: #2a2f35;
            color: #6c757d;
        }
    </style>
@endif