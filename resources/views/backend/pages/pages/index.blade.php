@extends('backend.layouts.authenticated', [
    'pageClass' => 'backend--pages--pages--index',
])

@section('title', 'Pages')

@section('content')
    <div class="content-header">
        <h1 class="title">
            Pages
        </h1>

        <div class="toolbar">
            <a class="btn btn-primary" href="{{ route('backend.pages.create') }}">
                Create a page
            </a>
        </div>
    </div>

    <div id="pages-loader" data-loader-type="tile">
        <table class="table table-striped table-dark"
               data-datatable='{
                "autofocus": true,
                "loaderSelector": "#pages-loader",
                "source": "{{ route('backend.pages.search') }}"
               }'>
            <thead>
            <tr>
                <th data-datatable-column='{"name": "id", "orderable": true}'>
                    Id
                </th>

                <th data-datatable-column='{"name": "language_name", "orderable": true}'>
                    Language
                </th>

                <th data-datatable-column='{"name": "title", "orderable": true}'>
                    Title
                </th>

                <th data-datatable-column='{"name": "status"}'>
                    Status
                </th>

                <th data-datatable-column='{"name": "created_at", "orderable": true}'>
                    Created at
                </th>

                <th data-datatable-column='{"name": "actions"}'>
                    &nbsp;
                </th>
            </tr>
            </thead>

            <tbody>
            </tbody>
        </table>
    </div>
@endsection
