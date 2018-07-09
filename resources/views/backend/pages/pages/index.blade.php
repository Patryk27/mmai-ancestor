@extends('backend.layouts.authenticated', [
    'pageClass' => 'backend--pages--pages--index',
])

@section('content')
    <div class="title-wrapper">
        <h1 class="title">
            Pages
        </h1>

        <div class="toolbar">
            <a class="btn btn-primary" href="{{ route('backend.pages.create') }}">
                Create a page
            </a>
        </div>
    </div>

    <table class="table table-striped table-dark"
           data-datatable='{
            "source": "{{ route('backend.pages.search') }}",
            "autofocus": true
           }'>
        <thead>
        <tr>
            <th data-datatable-column='{"name": "id", "orderable": true}'>Id</th>
            <th data-datatable-column='{"name": "language", "orderable": true}'>Language</th>
            <th data-datatable-column='{"name": "route", "orderable": true}'>Route</th>
            <th data-datatable-column='{"name": "title", "orderable": true}'>Title</th>
            <th data-datatable-column='{"name": "status", "orderable": true}'>Status</th>
            <th data-datatable-column='{"name": "actions"}'>&nbsp;</th>
        </tr>
        </thead>

        <tbody>
        </tbody>
    </table>
@endsection