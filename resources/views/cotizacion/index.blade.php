@extends('layouts.master')

@section('title') Ventas @endsection

@section('css') 
        <!-- DataTables -->        
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.css')}}">
        <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
        <meta name='csrf-token' content="{{ csrf_token() }}">
        <style>
            .btn-toolbar {
                display: none !important;
            }
        </style>
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
                       <h2>Lista de Cotizaciones</h2><br/>
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#abrirmodal">Agregar Cotización</button><br>
                        @if(session()->has('msj'))
                        <div class="alert alert-danger" role="alert">{{session('msj')}}</div>
                        @endif
                        @if(session()->has('msj2'))
                        <div class="alert alert-success" role="alert">{{session('msj2')}}</div>
                        @endif
                    </div>

                    <div class="card-body">
                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">                             
                                    <thead>                                                        
                                        <tr>        
                                            <th  data-priority="1">Borrar</th>
                                            <th  data-priority="1">Fecha</th> 
                                            {{-- <th  data-priority="1">USD Compra</th>  --}}
                                            <th  data-priority="1">USD Venta</th>
                                            {{-- <th  data-priority="1">PS Compra</th>  --}}
                                            <th  data-priority="1">PS Venta</th>
                                            {{-- <th  data-priority="1">RS Compra</th>  --}}
                                            <th  data-priority="1">RS Venta</th>
                                        </tr>
                                    </thead>
                                    <tbody>

                                         @foreach($cotizaciones as $cot)                                        
                                            <tr>          
                                                <td>                                    
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#borrarRegistro-{{$cot->id}}">
                                                        <i class="fa fa-times fa-1x"></i> Borrar
                                                    </button>                                    
                                                </td>               
                                                <td>{{ date('d-m-Y', strtotime($cot->fecha)) }}</td>
                                                {{-- <td>{{number_format(($cot->dolCompra), 0, ",", ".")}}</td> --}}
                                                <td>{{number_format(($cot->dolVenta), 0, ",", ".")}}</td>
                                                {{-- <td>{{number_format(($cot->psCompra), 0, ",", ".")}}</td> --}}
                                                <td>{{number_format(($cot->psVenta), 2, ",", ".")}}</td>
                                                {{-- <td>{{number_format(($cot->rsCompra), 0, ".", ",")}}</td> --}}
                                                <td>{{number_format(($cot->rsVenta), 2, ",", ".")}}</td>                                                                                                           
                                            </tr>  
                                            @include('cotizacion.delete')
                                        @endforeach
                                    
                                    </tbody>
                                </table>
                                
                            </div> 
                        </div> 
                        
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
                <!--Inicio del modal agregar-->
                <div class="modal fade" id="abrirmodal" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Nueva Cotización</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{route('cotizacion.store')}}" method="post" class="form-horizontal">
                                
                                        {{csrf_field()}}
                                        
                                        @include('cotizacion.form')

                                    </form>                                    
                                </div>
                                
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->


                <!--Inicio del modal actualizar-->

                <div class="modal fade" id="abrirmodalEditar" tabindex="-1" role="dialog"
                        aria-labelledby="exampleModalScrollableTitle" aria-hidden="true">
                        <div class="modal-dialog modal-lg">
                            <div class="modal-content">
                                <div class="modal-header">
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Actualizar Cotización</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{route('cotizacion.update','test')}}" method="post" class="form-horizontal">
                                        
                                        {{method_field('patch')}}
                                        {{csrf_field()}}

                                        <input type="hidden" id="id_cotizacion" name="id_cotizacion" value="">
                                        
                                        @include('cotizacion.form')

                                    </form>                                  
                                </div>
                                
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->

        </div><br>   
                     
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