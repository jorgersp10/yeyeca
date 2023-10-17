@extends('layouts.master')

@section('title') Cuentas Corrientes @endsection

@section('css')
        <!-- DataTables -->
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.css')}}">
        <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
        <meta name='csrf-token' content="{{ csrf_token() }}">
@endsection

@section('content')
@component('components.breadcrumb')
        @slot('li_1') Tables @endslot
        @slot('title'){{config('global.nombre_empresa')}} @endslot
    @endcomponent
<main class="main">
            <!-- Breadcrumb -->
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">

                       <h2>Agregar Cuenta Corriente</h2><br/>
                       @if(session()->has('msj'))
                            <div class="alert alert-danger" role="alert">{{session('msj')}}</div>
                        @endif
                        @if(session()->has('msj2'))
                            <div class="alert alert-success" role="alert">{{session('msj2')}}</div>
                        @endif

                    </div>

                    <div class="card-body">
                        <form id="form_mora" action="{{route('cuenta_corriente.store')}}" method="POST">
                             {{csrf_field()}}
                        <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-md-3 form-control-label" for="precio">N° Cuenta</label>
                                <div class="mb-3">
                                    <input type="text" id="nro_cuenta" name="nro_cuenta" class="form-control" placeholder="Ingrese nro de cuenta" required>
                                </div>
                            </div>
                            <div class="col-sm-3">
                                <label class="col-md-3 form-control-label" for="precio">Banco</label>
                                <select class="form-control" name="banco_id" id="banco_id">                                     
                                    <option value="0" disabled>Seleccione</option>
                                    
                                    @foreach($bancos as $ban)
                                        <option value="{{$ban->id}}">{{$ban->descripcion}}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>
                        
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
            <!-- Fin ejemplo de tabla Listado -->
        </div>

</main>

@endsection

@section('script')
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <!-- Plugins js -->
    <script src="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.js')}}"></script>
    <!-- Init js-->
    <script src="{{ URL::asset('assets/js/pages/table-responsive.init.js')}}"></script>
        <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <!-- Datatable init js -->
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>

@endsection
