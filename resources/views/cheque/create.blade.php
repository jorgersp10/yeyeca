@extends('layouts.master')

@section('title') Cheques @endsection

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

                       <h2>Nuevo Cheque Recibido</h2><br/>
                       @if(session()->has('msj'))
                            <div class="alert alert-danger" role="alert">{{session('msj')}}</div>
                        @endif
                        @if(session()->has('msj2'))
                            <div class="alert alert-success" role="alert">{{session('msj2')}}</div>
                        @endif

                    </div>

                    <div class="card-body">
                        <form id="form_mora" action="{{route('cheque.store')}}" method="POST">
                            {{csrf_field()}} 
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="col-md-3 form-control-label" for="nro_cheque">N° Cheque</label>
                                    <div class="mb-3">
                                        <input type="text" id="nro_cheque" name="nro_cheque" class="form-control" placeholder="Ingrese número" required>
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <label class="col-md-3 form-control-label" for="banco_id">Banco</label>
                                    <select class="form-control" name="banco_id" id="banco_id">                                     
                                        <option value="0" disabled>Seleccione</option>                                    
                                        @foreach($bancos as $ban)
                                            <option value="{{$ban->id}}">{{$ban->descripcion}}</option>
                                        @endforeach
                                    </select>
                                </div>
                            </div>
                            <div class="row mb-2">
                                    <label class="col-md-3 form-control-label" for="cuenta_corriente">Cuenta Corriente N°</label>
                                    <div class="col-md-5">
                                    
                                    <input type="text" class="form-control" id="cuenta_corriente" name="cuenta_corriente" placeholder="Ingrese número de cuenta">
                                        
                                    </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="col-md-5 form-control-label" for="tipo_cheque_id">Tipo Cheque</label>
                                    <div class="mb-3">
                                        <select class="form-control" name="tipo_cheque_id" id="tipo_cheque_id">                                     
                                            <option value="0" disabled>Seleccione</option>                                    
                                            @foreach($tipo_cheques as $tc)
                                                <option value="{{$tc->id}}">{{$tc->tipo_cheque}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                </div>
                                
                                <div class="col-md-4">
                                    <label class="col-md-5 form-control-label" for="importe_cheque">Importe</label>
                                    <div class="mb-3">
                                        <input type="text" id="importe_cheque" name="importe_cheque" class="form-control number" placeholder="Ingrese importe" required>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="col-md-3 form-control-label" for="librador_id">Librador</label>
                                    
                                    <div class="mb-3">
                                    
                                        <select class="form-control" name="librador_id" id="librador_id" onchange="obtenerComentario()" style= "width:300px">
                                                                            
                                            <option value="0" disabled>Seleccionar Librador</option>

                                        </select>
                                    
                                    </div>
                                </div>

                                <div class="col-md-4">
                                    <label class="col-md-3 form-control-label" for="rol">Endosante</label>
                                    
                                    <div class="mb-3">
                                    
                                        <select class="form-control" name="endosante_id" id="endosante_id" onchange="obtenerComentarioEnd()" style= "width:300px">
                                                                            
                                            <option value="0" disabled>Seleccionar Endosante</option>

                                        </select>
                                    
                                    </div>
                                </div>
                            </div>
                            <div class="form-group row">
                                <div class="col-md-4">
                                    <label class="col-md-5 form-control-label" for="fec_venc">Vencimiento</label>
                                    <div class="mb-3">
                                        <input type="date" id="fec_venc" name="fec_venc" class="form-control">
                                    </div>
                                </div>
                            </div>
                                
                            <div class="modal-footer">
                                <button type="submit" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                                <button type="submit" class="btn btn-primary">Guardar</button>
                            </div>
                        </form>
                    </div>
                </div>

        </div><br>   
                     
    </main>

@endsection
@section('script')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
<script>

function obtenerComentario()//funcion para enviar datos de la empresa al form
    {
        
        var librador_id = document.getElementById("librador_id").value //aca nos trae el id del medidor para consultar  
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    }
                });

        $.ajax({
                //headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type:  'post',
                dataType: 'json',
                data:  {librador_id:librador_id},
                url:   '{{ url('/obtenerComentario') }}', //URL que indica la ruta en web.php                                    
                        
                success:  function (data) {

                    console.log((data.var[0].comentario_cliente));
                    document.getElementById("comentario_librador").value = data.var[0].comentario_cliente;
                    
                    
                }
                
        });
        
    }

</script>

<script>

function obtenerComentarioEnd()//funcion para enviar datos de la empresa al form
    {
        
        var endosante_id = document.getElementById("endosante_id").value //aca nos trae el id del medidor para consultar  
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    }
                });

        $.ajax({
                //headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type:  'post',
                dataType: 'json',
                data:  {endosante_id:endosante_id},
                url:   '{{ url('/obtenerComentarioEnd') }}', //URL que indica la ruta en web.php                                    
                        
                success:  function (data) {

                    console.log((data.var[0].comentario_cliente));
                    document.getElementById("comentario_endosante").value = data.var[0].comentario_cliente;
                    
                    
                }
                
        });
        
    }

</script>
    <script>
        const number = document.querySelector('.number');
        function formatNumber (n) {
            n = String(n).replace(/\D/g, "");
        return n === '' ? n : Number(n).toLocaleString();
        }
        number.addEventListener('keyup', (e) => {
            const element = e.target;
            const value = element.value;
        element.value = formatNumber(value);
        });
    </script>
     <script type="text/javascript">
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            $('#librador_id').select2({
                ajax:{
                    url:"{{route('getClientesLib') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 100,
                    data: function(params){
                        return{
                            _token: CSRF_TOKEN,
                            search:params.term
                        }
                    },
                    processResults: function(response){
                        return{
                            results: response
                        }
                    },
                    cache: true
                }
            });
        });
    
    </script>   
     <script type="text/javascript">
        var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
        $(document).ready(function(){
            $('#endosante_id').select2({
                ajax:{
                    url:"{{route('getClientesEnd') }}",
                    type: 'post',
                    dataType: 'json',
                    delay: 100,
                    data: function(params){
                        return{
                            _token: CSRF_TOKEN,
                            search:params.term
                        }
                    },
                    processResults: function(response){
                        return{
                            results: response
                        }
                    },
                    cache: true
                }
            });
        });
    
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