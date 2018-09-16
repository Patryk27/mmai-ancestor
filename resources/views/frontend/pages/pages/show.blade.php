@php
    /**
     * @var \App\Pages\ValueObjects\RenderedPageVariant $renderedPageVariant
     */

    $page = $renderedPageVariant->getPage();
    $pageVariant = $renderedPageVariant->getPageVariant();
@endphp

@extends('frontend.layout', [
    'pageClass' => 'frontend--pages--pages--show',
])

@section('title', $pageVariant->title)

@section('content')
    <main class="content">
        <header>
            <h1>
                {{ $pageVariant->title }}
            </h1>
        </header>

        <article class="page-content">
            {!! $renderedPageVariant->getContent() !!}
        </article>
    </main>

    <footer class="content-footer">
        @if ($page->attachments->isNotEmpty())
            <div class="page-attachments">
                <h5>Attachments</h5> {{-- @todo translation --}}
            </div>
        @endif

        {{-- @todo footer --}}
    </footer>
@endsection
