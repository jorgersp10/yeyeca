@extends('layouts.master')

@section('title') Ventas @endsection

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
                        <h2>Lista de Vendedores/as</h2><br/>                     
                        @if(session()->has('msj'))
                             <div class="alert alert-danger" role="alert">{{session('msj')}}</div>    
                         @endif
                         @if(session()->has('msj2'))
                             <div class="alert alert-success" role="alert">{{session('msj2')}}</div>    
                         @endif
                        <a href="vendedor/create">
                         <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                             data-bs-target="#abrirmodal">Agregar Vendedor/a</button></a>
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-6">
                            {!!Form::open(array('url'=>'vendedor','method'=>'GET','autocomplete'=>'off','role'=>'search'))!!} 
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
                                            <th  data-priority="1">Editar</th>                               
                                            <th  data-priority="1">Nombre</th>
                                            <th  data-priority="1">Documento</th> 
                                            <th  data-priority="1">Porcentaje</th>                                           
                                        </tr>
                                    </thead>
                                    <tbody>
                                         @foreach($vendedores as $ven)
                                            <tr>       
                                                <td>                                     
                                                    <a href="{{URL::action('App\Http\Controllers\VendedorController@edit', $ven->id)}}">
                                                        <button type="button" class="btn btn-success btn-sm" >
                                                            <i class="fa fa-success fa-1x"></i> Editar
                                                        </button>
                                                    </a>
                                                </td>                             
                                                <td>{{$ven->nombre}}</td>
                                                <td>{{$ven->num_documento}}</td>     
                                                <td>{{$ven->porcentaje}} %</td>                                 
                                            </tr>  
                                        @endforeach                                    
                                    </tbody>
                                </table>                                
                            </div> 
                        </div>                         
                    </div>              
            {{$vendedores->links()}} 
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

    <script>
     /*EDITAR VENDEDOR EN VENTANA MODAL*/
     $('#abrirmodalEditar').on('show.bs.modal', function (event) {
        
        /*el button.data es lo que está en el button de editar*/
        var button = $(event.relatedTarget)
        
        var nombre_modal_editar = button.data('name')
        var email_modal_editar = button.data('email')
        var num_documento_modal_editar = button.data('num_documento')
        var direccion_modal_editar = button.data('direccion')
        var telefono_modal_editar = button.data('telefono')
        var fecha_nacimiento_modal_editar = button.data('fecha_nacimiento')
        var sucursal_modal_editar = button.data('idsucursal')
        var id_vendedor = button.data('id_vendedor')
        var modal = $(this)
        // modal.find('.modal-title').text('New message to ' + recipient)
        /*los # son los id que se encuentran en el formulario*/
        modal.find('.modal-body #nombre').val(nombre_modal_editar);
        modal.find('.modal-body #email').val(email_modal_editar);
        modal.find('.modal-body #num_documento').val(num_documento_modal_editar);
        modal.find('.modal-body #direccion').val(direccion_modal_editar);
        modal.find('.modal-body #telefono').val(telefono_modal_editar);
        modal.find('.modal-body #fecha_nacimiento').val(fecha_nacimiento_modal_editar);
        modal.find('.modal-body #idsucursal').val(sucursal_modal_editar);
        modal.find('.modal-body #id_vendedor').val(id_vendedor);
    })

     /*INICIO ventana modal para cambiar el estado del vendedor*/
        
     $('#cambiarEstado').on('show.bs.modal', function (event) {       
        
        var button = $(event.relatedTarget) 
        var id_vendedor = button.data('id_vendedor')
        var modal = $(this)        
        modal.find('.modal-body #id_vendedor').val(id_vendedor);
        })
     
    /*FIN ventana modal para cambiar estado del vendedor*/
    </script> 
@endsection