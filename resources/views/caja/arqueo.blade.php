@extends('layouts.master')

@section('title') Caja @endsection

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
        @slot('title') {{config('global.nombre_empresa')}} @endslot
    @endcomponent
    <main class="main">
            <!-- Breadcrumb -->
        <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">
                        <h2>Arqueo del dia: {{ date('d-m-Y', strtotime($fechaahora)) }} - Cajero: {{ $cajeroNombre }}</h2><br/>    
                        
                    </div>
                    <div class="card-body">
                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">                               
                                    <thead>                               
                                        <tr>
                                            {{-- <th data-priority="1">Fecha</th> --}}
                                            <th data-priority="1">Cliente</th>
                                            <th data-priority="1">Pago / Factura Nro</th>
                                            <th data-priority="1">Importe</th>
                                            <th data-priority="1">Efectivo</th>
                                            <th data-priority="1">Transf</th>
                                            <th data-priority="1">Cheque</th>
                                            <th data-priority="1">T. Debito</th>
                                            <th data-priority="1">T. Crédito</th>
                                        </tr>    
                                    </thead>
                                    
                                    <tbody>
                                    @php
                                        $totaldia = 0;
                                        $totalefe = 0;
                                        $totaltran = 0;
                                        $totalche = 0;
                                        $totaltd = 0;
                                        $totaltc = 0;                   
                                    @endphp
                                    @if($arqueo=="Vacio")
                                        <tr><td><h3>NO HUBO COBROS</h3></td></tr>
                                        </tbody>
                                        @else
                                            @foreach($arqueo as $ar)
                                                <tr>                                    
                                                    {{-- <td>{{ date('d-m-Y', strtotime($ar->fechapago)) }}</td> --}}
                                                    <td>{{$ar->cliente}}</td>
                                                    <td>Ticket {{$ar->producto}}</td>
                                                    <td>{{number_format(($ar->importe), 2, ".", ",")}} </td>  
                                                    <td>{{number_format($ar->total_pagf, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagtr, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagch, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagtd, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagtc, 2, ".", ",")}}</td>                          
                                                </tr>  
                                                @php
                                                    $totaldia=$totaldia + $ar->importe;  
                                                    $totalefe=$totalefe + $ar->total_pagf; 
                                                    $totaltran=$totaltran + $ar->total_pagtr; 
                                                    $totalche=$totalche + $ar->total_pagch; 
                                                    $totaltd=$totaltd + $ar->total_pagtd; 
                                                    $totaltc=$totaltc + $ar->total_pagtc;                                                       
                                                @endphp
                                        @endforeach
                                        <tr class="table-dark">  
                                            <td></td>     
                                            <td>Total USD</td>                                                 
                                            <td>USD. {{number_format(($totaldia), 2, ".", ",")}}</td>  
                                            <td>USD. {{number_format(($totalefe), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totaltran), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totalche), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totaltd), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totaltc), 2, ".", ",")}}</td>                                               
                                        </tr>
                                        <tr class="table-dark">  
                                            <td></td>     
                                            <td>Total Gs.</td>                                                 
                                            <td>Gs. {{number_format(($totaldia * $ar->dolVenta), 0, ",", ".")}}</td>  
                                            <td>Gs. {{number_format(($totalefe * $ar->dolVenta), 0, ",", ".")}}</td> 
                                            <td>Gs. {{number_format(($totaltran * $ar->dolVenta), 0, ",", ".")}}</td> 
                                            <td>Gs. {{number_format(($totalche * $ar->dolVenta), 0, ",", ".")}}</td> 
                                            <td>Gs. {{number_format(($totaltd * $ar->dolVenta), 0, ",", ".")}}</td> 
                                            <td>Gs. {{number_format(($totaltc * $ar->dolVenta), 0, ",", ".")}}</td>                                               
                                        </tr>
                                        <tr class="table-dark">  
                                            <td></td>     
                                            <td>Total $</td>                                                 
                                            <td>$. {{number_format(($totaldia * ($ar->psVenta)), 0, ",", ".")}}</td>  
                                            <td>$. {{number_format(($totalefe * ($ar->psVenta)), 0, ",", ".")}}</td> 
                                            <td>$. {{number_format(($totaltran * ($ar->psVenta)), 0, ",", ".")}}</td> 
                                            <td>$. {{number_format(($totalche * ($ar->psVenta)), 0, ",", ".")}}</td> 
                                            <td>$. {{number_format(($totaltd * ($ar->psVenta)), 0, ",", ".")}}</td> 
                                            <td>$. {{number_format(($totaltc * ($ar->psVenta)), 0, ",", ".")}}</td>                                               
                                        </tr>
                                        <tr class="table-dark">  
                                            <td></td>     
                                            <td>Total R$</td>                                                 
                                            <td>R$. {{number_format(($totaldia * ($ar->rsVenta)), 2, ",", ".")}}</td>  
                                            <td>R$. {{number_format(($totalefe * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                                            <td>R$. {{number_format(($totaltran * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                                            <td>R$. {{number_format(($totalche * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                                            <td>R$. {{number_format(($totaltd * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                                            <td>R$. {{number_format(($totaltc * ($ar->rsVenta)), 2, ",", ".")}}</td>                                               
                                        </tr>
                                    </tbody>
                                    @endif
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card">
            <div class="card-header">
                <h2>Arqueo de dias anteriores</h2><br/>
            </div>
            <div class="card-body">
                <div class="form-group row">
                    <div class="col-md-12">
                        <form id="detalle_producto_pdf" action="{{route('arqueo_dias')}}" method="POST"target="_blank">
                        {{csrf_field()}}
                        <!-- FECHAS DE INICIO Y FIN  -->
                        <div class="row mb-2">
                            <label for="horizontal-firstname-input" class="col-sm-1 col-form-label">Fecha</label>
                            <div class="col-sm-4">
                                <input type="date" id="fecha1" name="fecha1" class="form-control">
                            </div>
                        </div>
                        <div>
                            <button type="submit" class="btn btn-danger float-left"><i class="fa fa-file fa-1x"></i> Generar PDF</button>
                        </div>
                        </form>
                    </div>
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
    <script src="{{ URL::asset('/assets/js/caja/caja.js') }}" defer></script>
    
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection