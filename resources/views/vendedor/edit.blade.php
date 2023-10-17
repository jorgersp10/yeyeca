@extends('layouts.master')

@section('title') Ventas @endsection

@section('css') 
        <!-- DataTables -->        
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.css')}}">
        <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
        <meta name='csrf-token' content="{{ csrf_token() }}">
@endsection

@section('content')
@component('components.breadcrumb')
        @slot('li_1') Tables @endslot
        @slot('title') {{config('global.nombre_empresa')}} @endslot
    @endcomponent
<main class="main">
    <!-- Breadcrumb -->
    <div class="container-fluid">
        <!-- Ejemplo de tabla Listado -->
        <div class="card">
            <div class="card-header">
                <div class="form-group row">
                    <div class="col-md-3">
                        <h2>Actualizar Vendedor/a</h2><br/>                     
                        @if(session()->has('msj'))
                                <div class="alert alert-danger" role="alert">{{session('msj')}}</div>    
                            @endif
                            @if(session()->has('msj2'))
                                <div class="alert alert-success" role="alert">{{session('msj2')}}</div>    
                            @endif
                    </div>
                </div>
            </div>

            <div class="card-body">    
                <form id="form_mora" action="{{route('vendedor.update','test')}}" method="POST">
                    {{method_field('patch')}}
                    @csrf
                    <input type="hidden" id="id_vendedor" name="id_vendedor" value = "{{$vendedor->id}}" class="form-control">

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nombre</label>
                        <div class="col-sm-5">
                            <input type="text" id="nombre" name="nombre" value = "{{$vendedor->nombre}}" class="form-control">
                        </div>
                    </div>

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Número documento</label>
                        <div class="col-sm-5">  
                            <input type="text" id="num_documento" name="num_documento" value = "{{$vendedor->num_documento}}" class="form-control">
                        </div> 
                    </div> 

                    <div class="row mb-4">
                        <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Porcentaje %</label>
                        <div class="col-sm-5">  
                            <input type="text" id="porcentaje" name="porcentaje" value = "{{$vendedor->porcentaje}}" class="form-control">
                        </div> 
                    </div>

                    <div class="modal-footer-centre">
                        <button type="submit" class="btn btn-primary">Guardar</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</main>
        
@endsection
@section('script')
        
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