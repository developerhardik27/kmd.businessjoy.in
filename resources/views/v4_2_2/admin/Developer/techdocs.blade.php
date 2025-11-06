@php
    $folder = session('folder_name');
@endphp
@extends($folder . '.admin.Layout.mastertable')

@section('page_title')
    {{ config('app.name') }} - Technical Documents
@endsection
@section('table_title')
    Technical Documents
@endsection

@section('style')
    <style>
        .ui-widget-header {
            background: #1518b117 !important;
            border: 1px solid #e1ded9 !important;
        }

        .btn-info {
            background-color: #253566 !important;
            border-color: #253566 !important;
            color: white;
        }

        .btn-info:hover {
            background-color: #39519b !important;
            color: rgb(255, 255, 255);
        }

        .btn-success {
            background-color: #67d5a5d9 !important;
            border-color: var(--iq-success) !important;
            color: black !important;
        }

        .btn-success:hover {
            background-color: #16d07ffa !important;
            border-color: var(--iq-success) !important;
            color: rgb(250, 250, 250) !important;
        }
    </style>
@endsection
@section('table-content')
    @if ($files->isEmpty())
        <div class="alert alert-warning">
            No documentation files found.
        </div>
    @else
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Last Updated</th>
                    <th>Download</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($files as $file)
                    <tr>
                        <td>{{ $file['name'] }}</td>
                        <td>{{ $file['updated_on'] }}</td>
                        <td>
                            <a href="{{ $file['download_url'] }}" target="_blank" download>
                                <button class="btn btn-sm btn-info" data-toggle="tooltip" data-placement="bottom" data-original-title="Downalod File">
                                    <i class="ri-download-line"></i>
                                </button>
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <p class="text-muted mt-3">
        Developers can update these files and push via Git. Files are stored in <code>public/docs/</code>.
    </p>
@endsection


@push('ajax')
    <script>
        $('document').ready(function() {

            loaderhide();

        });
    </script>
@endpush
