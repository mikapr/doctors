@extends('layouts.app')

@section('styles')
    <link href="{{ asset('plugins/helloweek/css/hello.week.min.css') }}" rel="stylesheet">
@endsection

@section('content')

    <div id="main-body" class="container">
        <div class="row">
            <div class="col-md-3 services"></div>
            <div class="col-md-3 doctors"></div>
            <div class="col-md-3">
                <div class="calendar" style="display: none"></div>
            </div>
            <div class="col-md-3 schedule"></div>
        </div>
        <p><strong>Мои записи:</strong></p>
        <div class="row">
            <div class="col-md-12 my-slots"></div>
        </div>

        <div class="modal fade" id="timeModal" tabindex="-1" role="dialog" aria-labelledby="timeModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="timeModalLabel">Запись на прием</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form>
                            <div class="form-group" id="slotTime"></div>
                            <div class="form-group" id="selectedDoctor"></div>
                            <div class="form-group" id="selectedService"></div>
                        </form>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary cancel-service" data-dismiss="modal">Отменить</button>
                        <button type="button" class="btn btn-primary confirm-service">Записаться</button>
                    </div>
                </div>
            </div>
        </div>

        <div class="modal fade" id="notifyModal" tabindex="-1" role="dialog" aria-labelledby="notifyModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-body">
                        Возникла ошибка
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary close" data-dismiss="modal">Ок</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('scripts')

    <script src="{{ asset('js/doc_appointment/doctors.js') }}"></script>
    <script src="{{ asset('plugins/helloweek/js/hello.week.min.js') }}"></script>


@endsection