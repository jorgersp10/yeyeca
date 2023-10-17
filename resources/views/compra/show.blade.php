@extends('layouts.master')

@section('title') Clientes @endsection

@section('css') 
        <!-- DataTables -->        
        <link rel="stylesheet" type="text/css" href="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.css')}}">
        <link href="{{ URL::asset('/assets/libs/datatables/datatables.min.css') }}" rel="stylesheet" type="text/css" />
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
                <h2>Estado de Cuenta</h2><br/>                     
            </div>

            <div class="card-body">
                <h4 class="text-left">Detalle de Compra</h4><br/>
            
                <div class="form-group row">
                    <label class="col-md-2 form-control-label"><b>Proveedor:</b></label>
                    <div class="col-md-3">
                            <p>{{$compras->nombre}}</p>     
                    </div>
                    <label class="col-md-2 form-control-label"><b>RUC:</b></label>
                    <div class="col-md-3">
                            <p>{{$compras->ruc}}</p>     
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 form-control-label"><b>Factura N°:</b></label>
                    <div class="col-md-3">
                            <p>{{$compras->fact_compra}}</p>     
                    </div>
                    <label class="col-md-2 form-control-label"><b>Fecha de Compra:</b></label>
                    <div class="col-md-3">
                            <p>{{ date('d-m-Y', strtotime($compras->fecha)) }}</p>     
                    </div>
                </div>

        <div class="form-group row border">

              <h3>Detalle de Compras</h3>

              <div class="table-responsive col-md-12">
                <table id="detalles" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr class="bg-info">
                            <th>Cantidad</th>
                            <th>Producto</th>
                            <th>Precio (USD.)</th>                        
                            <th>SubTotal (USD.)</th>
                        </tr>
                    </thead>
                    
                    <tfoot>

                        <tr>
                            <th  colspan="3"><p align="right">TOTAL:</p></th>
                            <th><p align="right">USD. {{number_format(($compras->total), 2, ".", ",")}}</p></th>
                        </tr>

                        <tr>
                            <th colspan="3"><p align="right">TOTAL IMPUESTO (10%):</p></th>
                            <th><p align="right">USD. {{number_format(($compras->total*10/100), 2, ".", ",")}}</p></th>
                        </tr>

                        <tr>
                            <th  colspan="3"><p align="right">TOTAL PAGAR:</p></th>
                            <th><p align="right">USD. {{number_format($compras->total, 2, ".", ",")}}</p></th>
                        </tr> 

                    </tfoot>

                    <tbody>
                    
                    @foreach($detalles as $det)

                        <tr>
                        <td>{{$det->cantidad}}</td>
                        <td>{{$det->producto}}</td>
                        <td>USD. {{number_format(($det->precio), 2, ".", ",")}}</td>
                        <td>USD. {{number_format(($det->cantidad*$det->precio), 2, ".", ",")}}</td>
                        </tr> 

                    @endforeach
                    
                    </tbody>
                     
                </table>
              </div>
            
            </div>

               
            </div> 
             <div hidden class="card-body">
                 <h4 class="text-left">Detalle de Pagos</h4><br/>
                  <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">                               
                                    <thead>                               
                                        <tr>
                                            <th data-priority="1">Fecha</th>
                                            <th data-priority="1">Tot. Pag</th>
                                            <th data-priority="1">Efectivo</th>
                                            <th data-priority="1">Transf</th>
                                            <th data-priority="1">Cheque</th>
                                            <th data-priority="1">T. Debito</th>
                                            <th data-priority="1">T. Crédito</th>
                                        </tr>    
                                    </thead>
                                    @php
                                        $totaldia = 0;
                                        $totalefe = 0;
                                        $totaltran = 0;
                                        $totalche = 0;
                                        $totaltd = 0;
                                        $totaltc = 0;   
                                        $saldopago = 0;                
                                    @endphp
                                    <tbody>
                                    @if($pagos=="Vacio")
                                        <tr><td><h3>NO HUBO PAGOS</h3></td></tr>
                                        </tbody>
                                        @else
                                            @foreach($pagos as $ar)
                                                <tr>                                    
                                                    <td>{{ date('d-m-Y', strtotime($ar->fec_pag)) }}</td>
                                                    <td>{{number_format(($ar->total_pag), 2, ".", ",")}} </td>  
                                                    <td>{{number_format($ar->total_pagf, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagtr, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagch, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagtd, 2, ".", ",")}}</td>
                                                    <td>{{number_format($ar->total_pagtc, 2, ".", ",")}}</td>                          
                                                </tr>  
                                                @php
                                                    $totaldia=$totaldia + $ar->total_pag;  
                                                    $totalefe=$totalefe + $ar->total_pagf; 
                                                    $totaltran=$totaltran + $ar->total_pagtr; 
                                                    $totalche=$totalche + $ar->total_pagch; 
                                                    $totaltd=$totaltd + $ar->total_pagtd; 
                                                    $totaltc=$totaltc + $ar->total_pagtc;                                                       
                                                @endphp
                                        @endforeach
                                        <tr class="table-dark">  
     
                                            <td>Total</td>                                                 
                                            <td>USD. {{number_format(($totaldia), 2, ".", ",")}}</td>  
                                            <td>USD. {{number_format(($totalefe), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totaltran), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totalche), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totaltd), 2, ".", ",")}}</td> 
                                            <td>USD. {{number_format(($totaltc), 2, ".", ",")}}</td>                                               
                                        </tr>
                                    </tbody>
                                    @endif

                                    @php
                                        $saldopago = $compras->total - $totaldia;
                                    @endphp
                                    <tr class="table-dark">  
                                            <td>Saldo a Pagar</td>                                                 
                                            <td>USD. {{number_format(($saldopago), 2, ".", ",")}}</td>                                              
                                        </tr>
                                </table>
                            </div>
                        </div>
            </div>
        </div>  
    </div>
    <!-- Fin ejemplo de tabla Listado -->    
</main>

@endsection