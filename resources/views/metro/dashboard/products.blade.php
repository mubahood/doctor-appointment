@extends('metro.layout.layout-dashboard')

@section('dashboard-content')
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                {!! $dataTable->table() !!}
            </div>
        </div>
    </div>
@endsection

@section('footer')
    <script src="{{ url('/') }}/assets/js/custom/widgets.js"></script>
    <script src="{{ url('/') }}/assets/js/buttons.server-side.js"></script>
    {{ $dataTable->scripts() }}
@endsection
