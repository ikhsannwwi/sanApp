@extends('administrator.layouts.main')

@section('content')
    <!-- Basic Tables start -->
    <section class="section">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-6">
                        Log Systems
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a></li>
                                <li class="breadcrumb-item active" aria-current="page">Log System</li>
                            </ol>
                        </nav>
                    </div>
                    <div class="col-6">
                        @if (isallowed('log_system','clear'))
                        <a href="{{ route('admin.logSystems.clearLogs') }}" class="btn btn-primary mx-3 float-end">Clear</a>
                        @endif
                        <a href="javascript:void(0)" class="btn btn-primary float-end" id="filterButton">Filter</a>
                    </div>
                </div>
            </div>
            @include('administrator.logs.filter.main')
            <div class="card-body">
                <table class="table" id="datatable">
                    <thead>
                        <tr>
                            <th>No</th>
                            <th width="">User</th>
                            <th width="">Module</th>
                            <th width="">Action</th>
                            <th width="">Tanggal</th>
                        </tr>
                    </thead>
                </table>
            </div>
        </div>

    </section>
    <!-- Basic Tables end -->

    @include('administrator.logs.modal.detail')
@endsection

@push('js')
    <script type="text/javascript">
        $(document).ready(function() {
            var data_table = $('#datatable').DataTable({
                "oLanguage": {
                    "oPaginate": {
                        "sFirst": "<i class='ti-angle-left'></i>",
                        "sPrevious": "&#8592;",
                        "sNext": "&#8594;",
                        "sLast": "<i class='ti-angle-right'></i>"
                    }
                },
                processing: true,
                serverSide: true,
                order: [
                    [4, 'desc']
                ],
                scrollX: true, // Enable horizontal scrolling
                ajax: {
                    url: '{{ route('admin.logSystems.getData') }}',
                    dataType: "JSON",
                    type: "GET",
                    data: function(d) {
                        d.user = getUser();
                        d.module = getModule();
                    }

                },
                columns: [{
                        render: function(data, type, row, meta) {
                            return '<a href="javascript:void(0)" data-id="' + row.id + '" data-bs-toggle="modal" data-bs-target="#detailLogSystem">' +
                                (meta.row + meta.settings._iDisplayStart + 1) + '</a>';
                        },
                    },
                    {
                        data: 'user.name',
                        name: 'user.name'
                    },
                    {
                        data: 'module',
                        name: 'module'
                    },
                    {
                        data: 'action',
                        name: 'action'
                    },
                    {
                        data: 'created_at',
                        name: 'created_at'
                    }
                ],
            });

            $('#filterButton').on('click', function() {
                $('#filter_section').slideToggle();

            });

            $('#filter_submit').on('click', function(event) {
                event.preventDefault(); // Prevent the default form submission behavior

                // Get the filter value using the getUser() function
                var filterUser = getUser();
                var filterModule = getModule();

                // Update the DataTable with the filtered data
                data_table.ajax.url('{{ route('admin.logSystems.getData') }}?user=' + filterUser +
                        '|module=' + filterModule)
                    .load();
            });

            function getUser() {
                return $("#inputUser").val();
            }

            function getModule() {
                return $("#inputModule").val();
            }
        });
    </script>
@endpush
