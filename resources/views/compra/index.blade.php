@extends('layouts.master')

@section('title') Compras @endsection

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
        @slot('li_1') Tables @endslot
        @slot('title') {{config('global.nombre_empresa')}} @endslot
    @endcomponent
<main class="main">
            <!-- Breadcrumb -->
            <div class="container-fluid">
                <!-- Ejemplo de tabla Listado -->
                <div class="card">
                    <div class="card-header">

                       <h2>Lista de Facturas Compra</h2><br/>                     
                       @if(session()->has('msj'))
                            <div class="alert alert-danger" role="alert">{{session('msj')}}</div>    
                        @endif
                        @if(session()->has('msj2'))
                            <div class="alert alert-success" role="alert">{{session('msj2')}}</div>    
                        @endif
                    <a href="compra/create">
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#abrirmodal">Agregar Factura</button></a>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                            {!!Form::open(array('url'=>'compra','method'=>'GET','autocomplete'=>'off','role'=>'search'))!!} 
                                <div class="input-group">
                                   
                                    <input type="text" name="buscarTexto" class="form-control" placeholder="Buscar texto" value="{{$buscarTexto}}">
                                    <button type="submit"  class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                </div>
                            {{Form::close()}}
                            </div>
                        </div><br>
                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">                               
                                    <thead>                            
                                            <tr>      
                                            @if(auth()->user()->idrol == 1)   
                                            <th  data-priority="1">Borrar</th>
                                            @endif
                                            <th  data-priority="1">Ver Detalle</th>
                                            <th  data-priority="1">Fecha</th>
                                            <th  data-priority="1">Fact. N°</th>
                                            <th  data-priority="1">Proveedor</th>
                                            <th  data-priority="1">Total</th>
                                            <th  data-priority="1">Est. Pago</th>
                                            {{-- <th  data-priority="1">Contable</th> --}}
                                            <th  data-priority="1">Estado</th>
                                            <th  data-priority="1">Cambiar Estado</th>
                                            {{-- <th  data-priority="1">Acción</th> --}}
                                        </tr>
                                    </thead>
                                    <tbody>

                                         @foreach($compras as $co)
                                            <!-- El if realiza el filtro de datos de acuerdo a la empresa del usuario logueado
                                            Asi cada usuario solo puede ver datos de su empresa -->
                                        
                                            <tr>                
                                                @if(auth()->user()->idrol == 1)   
                                                <td>                                    
                                                    <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#borrarRegistroFac-{{$co->id}}">
                                                        <i class="fa fa-times fa-1x"></i> Borrar
                                                    </button>                                    
                                                </td>
                                                @endif
                                                <td>                                     
                                                    <a href="{{URL::action('App\Http\Controllers\CompraController@show', $co->id)}}">
                                                        <button type="button" class="btn btn-success btn-sm" >
                                                            <i class="fa fa-success fa-1x"></i> Detalles
                                                        </button>
                                                    </a>
                                                </td>
                                                <td>{{$co->fecha}}</td>
                                                <td>{{$co->fact_compra}}</td>
                                                <td>{{$co->nombre}}</td>
                                                <td>USD. {{number_format(($co->total), 2, ".", ",")}}</td>
                                                @if($co->estado_pago == "P")
                                                    <td>PENDIENTE</td>
                                                @else
                                                    <td>CANCELADO</td>
                                                @endif
                                                <td>                                      
                                                    @if($co->estado==0)
                                                        <button type="button" class="btn btn-primary btn-sm" >
                                                            <i class="fa fa-success fa-1x"></i> Registrado
                                                        </button>

                                                    @else
                                                    <button type="button" class="btn btn-danger btn-sm" >
                                                            <i class="fa fa-success fa-1x"></i> Anulado
                                                        </button>

                                                    @endif
                                                    
                                                </td>

                                                {{-- <td>
                                                    @if($co->contable==0)
                                                        <button type="button" class="btn btn-danger btn-sm" data-id_compra="{{$co->id}}" data-bs-toggle="modal" data-bs-target="#cambiarCompraContable">
                                                            <i class="fa fa-times fa-1x"></i> No Contable
                                                        </button>

                                                        @else

                                                        <button type="button" class="btn btn-warning btn-sm" data-id_compra="{{$co->id}}" data-bs-toggle="modal" data-bs-target="#cambiarCompraContable">
                                                            <i class="fa fa-check fa-1x"></i> Contable
                                                        </button>
                                                    @endif
                                                
                                                </td> --}}
                                                    
                                                    <td>
                                                         @if($co->estado==0)
                                                            <button type="button" class="btn btn-danger btn-sm" data-id_compra="{{$co->id}}" data-bs-toggle="modal" data-bs-target="#cambiarEstadoCompra">
                                                                <i class="fa fa-times fa-1x"></i> Anular Compra
                                                            </button>

                                                            @else

                                                            <button type="button" class="btn btn-success btn-sm">
                                                                <i class="fa fa-lock fa-1x"></i> Anulado
                                                            </button>
                                                        @endif
                                                    
                                                    </td>
                                                    
                                                    {{-- @if($co->estado_pago == "P")
                                                    <td>                                     
                                                        <a href="{{URL::action('App\Http\Controllers\CompraController@pagarCompra', $co->id)}}">
                                                            <button type="button" class="btn btn-info btn-sm" >
                                                                <i class="fa fa-success fa-1x"></i> Pagar
                                                            </button>
                                                        </a>
                                                    </td>
                                                    @else
                                                    <td>                                     
                                                        <a>
                                                            <button disabled type="button" class="btn btn-info btn-sm" >
                                                                <i class="fa fa-success fa-1x"></i> Pagar
                                                            </button>
                                                        </a>
                                                    </td>
                                                    @endif --}}
                                                    
                                                </tr>  
                                                @include('compra.delete') 
                                        @endforeach
                                    
                                    </tbody>
                                </table>
                            </div> 
                        </div> 
                        {{$compras->links()}}
                    </div>
                </div>
                <!-- Fin ejemplo de tabla Listado -->
            </div>

             <!-- CAMBIAR DE ESTADO -->
             <div class="modal fade" id="cambiarEstadoCompra" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Cambiar Estado de la compra</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('compra.edit','test')}}" method="GET">
                                {{method_field('edit')}}
                                {{csrf_field()}} 

                                <input type="hidden" id="id_compra" name="id_compra" value="">

                                <p>¿Estas seguro de cambiar el estado?</p>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Aceptar</button>
                                </div>

                            </form>
                        </div>                                    
                    </div>
                </div>
            </div>   

            <!-- CAMBIAR ESTADO CONTABLE DE LA COMPRA -->

             <!-- CAMBIAR DE ESTADO -->
             <div class="modal fade" id="cambiarCompraContable" data-bs-backdrop="static" data-bs-keyboard="false"
                tabindex="-1" role="dialog" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="staticBackdropLabel">Cambiar Estado Contable de la compra</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <form action="{{route('compraContable','test')}}" method="POST">                                
                                {{csrf_field()}} 

                                <input type="hidden" id="id_compra" name="id_compra" value="">

                                <p>¿Estas seguro de cambiar el estado?</p>
                                <div class="modal-footer">
                                    <button type="submit" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                    <button type="submit" class="btn btn-primary">Aceptar</button>
                                </div>

                            </form>
                        </div>                                    
                    </div>
                </div>
            </div> 
        </main>

@endsection
@section('script')

    <script>
         /*INICIO ventana modal para cambiar estado de Compra*/
        
        $('#cambiarEstadoCompra').on('show.bs.modal', function (event) {
       
       //console.log('modal abierto');
       
       var button = $(event.relatedTarget) 
       var id_compra = button.data('id_compra')
       var modal = $(this)
       // modal.find('.modal-title').text('New message to ' + recipient)
       
       modal.find('.modal-body #id_compra').val(id_compra);
       })
        
       /*FIN ventana modal para cambiar estado de la compra*/
    </script>

     <script>
         /*INICIO ventana modal para cambiar estado de Compra*/
        
        $('#cambiarCompraContable').on('show.bs.modal', function (event) {
       
       //console.log('modal abierto');
       
       var button = $(event.relatedTarget) 
       var id_compra = button.data('id_compra')
       var modal = $(this)
       // modal.find('.modal-title').text('New message to ' + recipient)
       
       modal.find('.modal-body #id_compra').val(id_compra);
       })
        
       /*FIN ventana modal para cambiar estado de la compra*/
    </script>
        
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