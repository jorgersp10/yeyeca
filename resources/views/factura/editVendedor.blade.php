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
                        <h2>Cambiar Vendedor/a</h2><br/>                     
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
                <form id="form_mora" action="{{route('venta.update','test')}}" method="POST">
                    {{method_field('patch')}}
                    @csrf
                    <input type="hidden" id="id_venta" name="id_venta" value = "{{$ventas->id}}" class="form-control">

                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Cliente</label>
                            <div class="col-sm-5">
                                <input readonly type="text" id="cliente" name="cliente" value = "{{$ventas->nombre}}" class="form-control">
                            </div>
                        </div>

                        <div class="col-md-5">
                            <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Recibo N°</label>
                            <div class="col-sm-5">  
                                <input readonly type="text" id="nro_recibo" name="nro_recibo" value = "{{$ventas->nro_recibo}}" class="form-control">
                            </div> 
                        </div> 
                        
                    </div><br>
                    <div class="form-group row">
                        <div class="col-md-5">
                            <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">TOTAL USD</label>
                            <div class="col-sm-5">  
                                <input readonly type="text" id="total" name="total" value = "USD {{$ventas->total}}" class="form-control">
                            </div> 
                        </div> 
                        <div class="col-md-5">
                            <label class="col-md-5 form-control-label" for="cantidad">Vendedor/a</label>
                            <div class="mb-3">
                                <select style= "width:280px" class="form-control" name="vendedor_id" id="vendedor_id">  
                                    <option disabled value="0">Seleccione</option>                     
                                    @foreach($vendedores as $v)                                    
                                        <option value="{{$v->id}}" @php
                                            echo ($v->id==$vendedor_id)?"selected":"";
                                            @endphp>{{$v->nombre}}
                                        </option>                                        
                                    @endforeach
                                </select>                                
                            </div>
                        </div>
                    </div><br>

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