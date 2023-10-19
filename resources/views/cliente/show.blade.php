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
                <h4 class="text-left">Datos del Cliente</h4><br/>
            
                <div class="form-group row">
                    <label class="col-md-2 form-control-label"><b>Nombre y Apellido:</b></label>
                    <div class="col-md-3">
                            <p>{{$client->cliente}}</p>     
                    </div>
                    <label class="col-md-2 form-control-label"><b>Documento N°:</b></label>
                    <div class="col-md-3">
                            <p>{{$client->documento}}</p>     
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 form-control-label"><b>Dirección:</b></label>
                    <div class="col-md-3">
                            <p>{{$client->direccion}}</p>     
                    </div>
                    <label class="col-md-2 form-control-label"><b>Teléfono:</b></label>
                    <div class="col-md-3">
                            <p>{{$client->telefono}}</p>     
                    </div>
                </div>

                <div class="form-group row">
                    <label class="col-md-2 form-control-label"><b>Estado Civil:</b></label>
                    <div class="col-md-2">
                            <p>{{$client->estado_civil}}</p>     
                    </div>
                    <label class="col-md-2 form-control-label"><b>Sexo:</b></label>
                    <div class="col-md-2">
                            <p>{{$client->sexo}}</p>     
                    </div>
                    <label class="col-md-1 form-control-label"><b>Edad:</b></label>
                    <div class="col-md-1">
                            <p>{{$edadcliente}}</p>     
                    </div>
                </div>

                @if($ventas == "vacio")
                <h4 class="text-left">No posee compras</h4><br/>
                @else
                <h4 class="text-left">Lista de compras</h4><br/>
                <div class="table-rep-plugin">
                        <div class="table-responsive mb-0" data-pattern="priority-columns">
                        <table id="tech-companies-1" class="table table-striped">                               
                                <thead>                            
                                <tr>
                                        <th  data-priority="1">Ver Detalle</th>
                                        <th  data-priority="1">Fecha</th>
                                        <th  data-priority="1">Fact. / Orden N°</th>
                                        <th  data-priority="1">Total</th>
                                        <th  data-priority="1">Iva</th>
                                        <th  data-priority="1">Estado</th>
                                        
                                </tr>
                                </thead>
                                <tbody>

                                        @foreach($ventas as $ven)
                                        <!-- El if realiza el filtro de datos de acuerdo a la empresa del usuario logueado
                                        Asi cada usuario solo puede ver datos de su empresa -->
                                
                                        <tr>        
                                       
                                        <td>                                     
                                                <a href="{{URL::action('App\Http\Controllers\FacturaController@show', $ven->id)}}">
                                                <button type="button" class="btn btn-success btn-sm" >
                                                        <i class="fa fa-success fa-1x"></i> Detalles
                                                </button>
                                                </a>
                                        </td>
                                        <td>{{ date('d-m-Y', strtotime($ven->fecha)) }}</td>
                                        @if($ven->contable == 1)
                                                <td>{{$ven->fact_nro}}</td>
                                        @else
                                                <td>{{$ven->nro_recibo}}</td>
                                        @endif
                                        <td>Gs. {{number_format(($ven->total), 0, ",", ".")}}</td>
                                        <td>Gs. {{number_format(($ven->total/11), 0, ",", ".")}}</td>
                                        <td>                                      
                                                @if($ven->estado==0)
                                                <button type="button" class="btn btn-primary btn-sm" >
                                                        <i class="fa fa-success fa-1x"></i> Registrado
                                                </button>

                                                @else
                                                <button type="button" class="btn btn-danger btn-sm" >
                                                        <i class="fa fa-success fa-1x"></i> Anulado
                                                </button>

                                                @endif
                                                
                                        </td>
                                        </tr> 
                                        @include('factura.delete') 
                                @endforeach
                                
                                </tbody>
                        </table>
                        </div> 
                </div> 
                @endif 
               
            </div> 
        </div>  
    </div>
    <!-- Fin ejemplo de tabla Listado -->    
</main>

@endsection