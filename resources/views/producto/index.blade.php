@extends('layouts.master')

@section('title') Productos @endsection

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
     
                       <h2>Lista de Productos</h2><br/>
                       @if(session()->has('msj'))
                            <div class="alert alert-danger" role="alert">{{session('msj')}}</div>    
                        @endif
                        @if(session()->has('msj2'))
                            <div class="alert alert-success" role="alert">{{session('msj2')}}</div>    
                        @endif
                        
                        <button type="button" class="btn btn-primary waves-effect waves-light" data-bs-toggle="modal"
                            data-bs-target="#abrirmodal">Agregar Producto</button>                    
                    </div>

                    <div class="card-body">
                        <div class="form-group row">
                            <div class="col-md-4">
                                {!!Form::open(array('url'=>'producto','method'=>'GET','autocomplete'=>'off','role'=>'search'))!!} 
                                <label class="col-md-4 form-control-label" for="rol">Texto</label>
                                    <div class="input-group">                                   
                                        <input type="text" name="buscarTexto" class="form-control" placeholder="Buscar texto" value="{{$buscarTexto}}">
                                        <button type="submit"  class="btn btn-primary"><i class="fa fa-search"></i> Buscar</button>
                                    </div>
                                {{Form::close()}}
                            </div>                     
                         </div>
                        <br>
                        <div class="table-rep-plugin">
                            <div class="table-responsive mb-0" data-pattern="priority-columns">
                                <table id="tech-companies-1" class="table table-striped">                             
                                    <thead>                            
                                            <tr>  
                                            @if(auth()->user()->idrol == 1)
                                            <th  data-priority="1">Acciones</th> 
                                            @endif      
                                            @if(auth()->user()->id == 1)
                                            <th  data-priority="1">Imagen</th> 
                                            @endif                      
                                            <th  data-priority="1">Codigo/Imprimir</th>                                            
                                            <th  data-priority="1">Nombre</th> 
                                            <th  data-priority="1">Stock</th> 
                                            <th  data-priority="1">Precio Gs.</th> 
                                        </tr>
                                    </thead>
                                    <tbody>

                                         @foreach($productos as $prod)
                                            @if(auth()->user()->idrol == 1) 
                                        <tr> 
                                            <td>
                                    
                                                <button type="button" class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#borrarRegistro-{{$prod->id}}">
                                                    <i class="fa fa-times fa-1x"></i> Borrar
                                                </button>
                                    
                                                <a href="{{URL::action('App\Http\Controllers\ProductoController@show', $prod->id)}}">
                                                    <button type="button" class="btn btn-success btn-sm" >
                                                        <i class="fa fa-success fa-1x"></i> Detalles
                                                    </button>
                                                </a>
                                            </td> 
                                            @endif
                                            @if(auth()->user()->id == 1)
                                                <td>
                                                    <button type="button" class="btn btn-info btn-sm" data-bs-toggle="modal" data-bs-target="#verImagen-{{$prod->imagen_id}}">
                                                        <i class="fa fa-image"></i> Ver Imagen
                                                    </button>
                                                </td>
                                            @endif
                                                <td>
                                                    <a href="{{URL::action('App\Http\Controllers\ProductoController@imprimirBarcode', $prod->id)}}" target="_blank">
                                                    <button type="button" class="btn btn-info btn-sm" >
                                                        <i class="fa fa-print fa-1x"></i> {{$prod->cod_barra}}
                                                    </button>
                                                    </a>
                                                </td>
                                                <td>{{$prod->descripcion}}</td>
                                                <td>{{$prod->stock}}</td> 
                                                <td>Gs. {{number_format(($prod->precio_venta), 0, ",", ".")}}</td>                                                                        
                                            </tr>  
                                            @include('producto.delete')
                                            @include('producto.verImagen')
                                        @endforeach
                                    
                                    </tbody>
                                </table>                                
                            </div> 
                            <div class="d-flex justify-content-end">
                                {{$productos->links()}}
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
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Nuevo Producto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{route('producto.store')}}" method="post" class="form-horizontal">
                                
                                        {{csrf_field()}}
                                        
                                        @include('producto.form')

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
                                    <h5 class="modal-title" id="exampleModalScrollableTitle">Actualizar Producto</h5>
                                    <button type="button" class="btn-close" data-bs-dismiss="modal"
                                        aria-label="Close"></button>
                                </div>
                                <div class="modal-body">
                                    <form action="{{route('producto.update','test')}}" method="post" class="form-horizontal">
                                        
                                        {{method_field('patch')}}
                                        {{csrf_field()}}

                                        <input type="hidden" id="id_producto" name="id_producto" value="">
                                        
                                        @include('producto.form')

                                    </form>                                  
                                </div>
                                
                            </div><!-- /.modal-content -->
                        </div><!-- /.modal-dialog -->
                    </div><!-- /.modal -->
  
        </div><br>   
                     
    </main>

@endsection
@section('script')

    <!-- Required datatable js -->
    <script src="{{ URL::asset('/assets/libs/datatables/datatables.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/jszip/jszip.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/libs/pdfmake/pdfmake.min.js') }}"></script>
    <script src="{{ URL::asset('assets/libs/rwd-table/rwd-table.min.js')}}"></script>
    <!-- Init js-->
    <script src="{{ URL::asset('assets/js/pages/table-responsive.init.js')}}"></script>
    <script src="{{ URL::asset('/assets/js/pages/datatables.init.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/inmueble/show.js') }}"></script>
    {{-- <script src="{{ URL::asset('/assets/js/inmueble/cuotas.js') }}"></script> --}}
    <script src="{{ URL::asset('/assets/js/moment.min.js') }}"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.8/js/select2.min.js" defer></script>
        <!-- Table Editable plugin -->
    <script src="{{ URL::asset('/assets/libs/table-edits/table-edits.min.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/pages/table-editable.int.js') }}"></script>
    <script src="{{ URL::asset('/assets/js/inmueble/show_tab.js') }}" defer></script>
        

 

    <!-- Datatable init js -->

    <script>
        /*EDITAR CLIENTE EN VENTANA MODAL*/
        $('#abrirmodalEditar').on('show.bs.modal', function (event) {
        
            /*el button.data es lo que está en el button de editar*/
            var button = $(event.relatedTarget)
            
            var nombre_descripcion = button.data('descripcion')
            console.log(nombre_descripcion)

            var modal = $(this)
            // modal.find('.modal-title').text('New message to ' + recipient)
            /*los # son los id que se encuentran en el formulario*/
            modal.find('.modal-body #descripcion').val(nombre_descripcion);
        })
    </script>
@endsection

