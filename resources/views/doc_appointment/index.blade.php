@extends('mainscreen')

@section('content')

    <div id="main-body" class="container"></div>

@endsection


@section('scripts')

    <script src="{{ asset('js/react.min.js') }}"></script>
    <script src="{{ asset('js/doctors.min.js') }}"></script>

@endsection