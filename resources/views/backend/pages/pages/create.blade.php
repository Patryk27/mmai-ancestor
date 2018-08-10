@extends('backend.layouts.authenticated', [
    'pageClass' => 'backend--pages--pages--create',
])

@section('title', 'Creating page')

@section('content')
    <div class="content-header">
        <h1 class="title">
            Creating page
        </h1>
    </div>

    <div id="page-form"
         data-method="post"
         data-url="{{ route('backend.pages.store') }}">
        @include('backend.pages.pages.create-edit.form', [
            'page' => new \App\Pages\Models\Page(),
        ])
    </div>
@endsection