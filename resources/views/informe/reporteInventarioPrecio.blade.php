@extends('layouts.master')

@section('title') Inventario de Productos - {{date('d-m-Y H:i:s')}} @endsection

@section('css') 
        <!-- DataTables -->        
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.css')}}">
        <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
        <style>
            .btn-toolbar {
                display: none !important;
            }
        </style>
@endsection

@section('content')
@component('components.breadcrumb')
        @slot('li_1') INICIO @endslot
        @slot('title') Fastersys @endslot
    @endcomponent
    <main class="main">
            <!-- Breadcrumb -->
        <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">

                    <div class="card-header">
                        <h2>Lista de Productos</h2><br/>
                    </div>

                    <div class="card-body">
                        <div class="table-rep-plugin">
                            <div id="encabezado" class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="datatable-buttons" class="table table-striped table-bordered dt-responsive nowrap">
                                    <thead>                            
                                        <tr>   
                                            <th  data-priority="1">Nombre</th>
                                            <th  data-priority="1">Código de Barra</th>  
                                            <th  data-priority="1">Stock</th>
                                            <th  data-priority="1">Precio Venta</th>
                                            <th  data-priority="1">Total</th> 
                                        </tr>
                                    </thead>
                                    @php 
                                        $total_cant = 0;
                                        $total_contado = 0;
                                    @endphp
                                    <tbody>
                                         @foreach($productos as $prod) 
                                            <tr> 
                                                @php
                                                    $total_cant = $total_cant + $prod->stock;                                                    
                                                    $sub_total = $prod->precio_venta*$prod->stock;
                                                    $total_contado =  $total_contado + $sub_total;
                                                @endphp                                                
                                                <td>{{$prod->descripcion}}</td>
                                                <td>{{$prod->cod_barra}}</td>
                                                <td>{{$prod->stock}}</td>
                                                <td>Gs. {{number_format(($prod->precio_venta), 0, ",", ".")}}</td>
                                                <td>Gs. {{number_format(($sub_total), 0, ",", ".")}}</td>                                                                                                                                   
                                            </tr>  
                                        @endforeach                                        
                                             <tr class="table-danger">      
                                                <td>z-TOTAL</td>                           
                                                <td></td>
                                                <td>{{$total_cant}}</td>
                                                <td></td>
                                                <td>Gs. {{number_format(($total_contado), 0, ",", ".")}}</td> 
                                            </tr> 
                                    </tbody>
                                </table>
                                
                            </div> 
                        </div> 
                        
                    </div>
                </div>
        </div><br>   
                     
    </main>

@endsection
@section('script')

    <!-- <script>
        $(document).ready(function () {
            $('#datatable-buttons').DataTable({
                language: {
                    decimal: ',',
                    thousands: '.',
                },
            });
        });
    </script> -->
    <script>
        $(document).ready(function () {
            $('#datatable').DataTable({
                order: [[0, 'asc']],
            });
        });
    </script>

   <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.js')}}"></script>
    <!-- Init js-->
    <script src="{{ URL::asset('assets/js/pages/table-responsive.init.js')}}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/producto/show.js') }}"></script>
    {{-- <script src="{{ URL::asset('/assets/js/inmueble/cuotas.js') }}"></script> --}}
    <script src="{{ URL::asset('/assets/js/moment.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
        <!-- Table Editable plugin -->
    <script src="{{ URL::asset('/assets/libs/table-edits/table-edits.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/table-editable.int.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/producto/show_tab.js') }}" defer></script>
    <script src="https://cdn.datatables.net/1.12.1/css/jquery.dataTables.min.css" defer></script>
        

@endsection

