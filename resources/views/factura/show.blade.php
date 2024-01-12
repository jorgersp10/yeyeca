@extends('layouts.master')

@section('title') Detalle Venta @endsection

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
                <h4 class="text-left">Detalle de Venta</h4><br/>
                <form id="form_venta" action="{{route('update_facNro')}}" method="POST">
                    {{csrf_field()}} 
                    <input type="hidden" id="id_venta" name="id_venta" class="form-control" value="{{$ventas->id}}">  

                    <div class="form-group row">
                        <label class="col-md-2 form-control-label"><b>Cliente:</b></label>
                        <div class="col-md-3">
                                <p>{{$ventas->nombre}}</p>     
                        </div>
                        <label class="col-md-2 form-control-label"><b>Documento:</b></label>
                        <div class="col-md-3">
                                <p>{{$ventas->num_documento}}</p>     
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 form-control-label"><b>Factura N°:</b></label>
                        <div class="col-md-3">
                            <input readonly type="text" id="fact_nro" name="fact_nro" class="form-control" value="{{$ventas->fact_nro}}">  
                        </div>
                        <label class="col-md-2 form-control-label"><b>Fecha de Compra:</b></label>
                        <div class="col-md-3">
                                <p>{{ date('d-m-Y', strtotime($ventas->fecha)) }}</p>     
                        </div>
                    </div>

                    <div class="form-group row">
                        <label class="col-md-2 form-control-label"><b>Tipo Factura:</b></label>
                        <div class="col-md-3">
                            @if($ventas->tipo_factura == 0)
                                <p>Contado</p>  
                                @else
                                <p>Crédito</p>     
                            @endif
                        </div>
                    </div>

            <div class="form-group row border">

                <h3>Detalle de Ventas</h3>

                <div class="table-responsive col-md-12">
                    <table id="detalles" class="table table-bordered table-striped table-sm">
                    <thead>
                        <tr class="bg-info">
                            <th>Cantidad</th>
                            <th>Producto</th>
                            <th>Precio (Gs.)</th>                        
                            <th>SubTotal (Gs.)</th>
                        </tr>
                    </thead>
                    
                    <tfoot>

                        <tr>
                            <th  colspan="3"><p align="right">TOTAL:</p></th>
                            <th><p align="right">Gs. {{number_format(($ventas->total), 0, ".", ",")}}</p></th>
                        </tr>

                        <tr>
                            <th colspan="3"><p align="right">TOTAL IMPUESTO (10%):</p></th>
                            <th><p align="right">Gs. {{number_format(($ventas->total/11), 0, ".", ",")}}</p></th>
                        </tr>

                        <tr>
                            <th  colspan="3"><p align="right">TOTAL PAGAR Gs:</p></th>
                            <th><p align="right">Gs. {{number_format($ventas->total, 0, ".", ",")}}</p></th>
                        </tr> 

                    </tfoot>

                    <tbody>
                    
                    @foreach($detalles as $det)

                        <tr>
                        <td>{{number_format(($det->cantidad_calculo), 0, ".", ",")}}</td>
                        <td>{{$det->cod_barra}} - {{$det->producto}}</td>
                        <td>Gs. {{number_format(($det->precio), 0, ".", ",")}}</td>
                        <td>Gs. {{number_format(($det->cantidad_calculo*$det->precio), 0, ".", ",")}}</td>
                        </tr> 


                    @endforeach
                    
                    </tbody>
                    
                    
                    </table>
                </div>
                
                </div>
                <!-- <div class="modal-footer">
                    <button type="submit" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                    <button type="submit" class="btn btn-primary">Guardar</button>
                </div> -->

            </form>
            </div> 
        </div>  
    </div>
    <!-- Fin ejemplo de tabla Listado -->    
</main>

@endsection