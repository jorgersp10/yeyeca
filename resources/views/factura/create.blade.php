@extends('layouts.master')

@section('title') Ventas @endsection

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
                        <div class="form-group row">
                            <div class="col-md-3">
                                <h2>Nueva Venta</h2><br/>                     
                                @if(session()->has('msj'))
                                        <div class="alert alert-danger" role="alert">{{session('msj')}}</div>    
                                    @endif
                                    @if(session()->has('msj2'))
                                        <div class="alert alert-success" role="alert">{{session('msj2')}}</div>    
                                    @endif
                            </div>
                        </div>
                        <div class="form-group row">
                            <form id="form_mora" action="{{ route('imprimirTicketUltimo') }}" method="POST" target="_blank">
                                @csrf
                                <div class="form-group row">
                                    <div class="col-sm-4">
                                        <button type="submit" class="btn btn-warning btn-md">
                                            <i class="fa fa-print fa-1x"></i> IMPRIMIR TICKET
                                        </button>
                                    </div>
                                    <div hidden class="col-sm-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" value="USD" class="form-control">
                                            <label class="form-check-label" for="USD">USD</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" value="PS" class="form-control">
                                            <label class="form-check-label" for="PS">PESOS</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" value="GS" class="form-control" checked>
                                            <label class="form-check-label" for "GS">GUARANÍES</label>
                                        </div>
                                    </div>
                                    <div hidden class="col-sm-4">
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" value="RS" class="form-control">
                                            <label class="form-check-label" for="RS">REALES</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="moneda" value="TODO" class="form-control">
                                            <label class="form-check-label" for="TODO">TODAS</label>
                                        </div>
                                    </div>                                    
                                </div>
                            </form>
                        </div>
                        
                        </div>
                    </div>

                    <div class="card-body">
                        {{-- <h5>Calculadora</h5>
                         <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-md-3 form-control-label" for="cantidad">Dinero</label>
                                <div class="mb-3">
                                    <input type="text" id="plata_entrega" name="plata_entrega" class="form-control number3" onchange="obtenerEstimacion()" placeholder="Ingrese cantidad">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="col-md-3 form-control-label" for="precio">Precio</label>
                                <div class="mb-3">
                                    <input type="text" id="precio_unidad" name="precio_unidad" class="form-control number4" placeholder="Ingrese precio">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-5 form-control-label" for="precio">Can. estimada</label>
                                <div class="mb-3">
                                    <input type="text" id="cantidad_estimada" name="cantidad_estimada" class="form-control" placeholder="Cantidad estimada">
                                </div>
                            </div>
                        </div> --}}
                        {{-- <div class="form-group row">
                            <div class="col-md-3">
                                <label class="col-md-3 form-control-label" for="cantidad">Dolar</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="dolVenta" name="dolVenta" value="{{number_format(($cotizaciones->dolVenta), 0, ",", ".")}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="col-md-3 form-control-label" for="precio">Peso</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="psVenta" name="psVenta" value="{{($cotizaciones->psVenta)}}" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="col-md-3 form-control-label" for="precio">Real</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="rsVenta" name="rsVenta" value="{{number_format(($cotizaciones->rsVenta), 2, ",", ".")}}" class="form-control">
                                </div>
                            </div>
                        </div> --}}
                        <form id="form_mora" action="{{route('factura.store')}}" method="POST"> 
                        {{-- <input hidden type="text" id="contable" name="contable" value=0 class="form-control"> --}}
                        <div class="form-group row">
                            <div hidden class="col-md-4">
                                <label class="col-md-5 form-control-label" for="precio">Buscador</label>
                                <div class="mb-3">
                                    <input autofocus type="text" id="buscador" name="buscador" class="form-control">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="col-md-3 form-control-label" for="cantidad">Cantidad</label>
                                <div class="mb-3">
                                    <input type="number" id="cantidad" name="cantidad" class="form-control" value=1 placeholder="Ingrese cantidad">
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="col-md-5 form-control-label" for="cantidad">Vendedor/a</label>
                                <div class="mb-3">
                                    <select style= "width:200px" class="form-control" name="vendedor_id" id="vendedor_id">  
                                        <option disabled value="0">Seleccione</option>                     
                                        @foreach($vendedores as $v)                                    
                                            <option value="{{$v->id}}">{{$v->nombre}}</option>                                        
                                        @endforeach
                                    </select>                                
                                </div>
                            </div>
                            <div class="col-md-1">
                                <label class="col-md-3 form-control-label" for="documento">FACTURAR</label>
                                
                                <div class="mb-3">
                                
                                    <select class="form-control" name="contable" id="contable">                                                                        
                                        <option disabled>Seleccione</option>
                                        <option value="1">SI</option>
                                        <option value="0">NO</option>

                                    </select>
                                
                                </div>
                            </div>                        
                            <div class="col-md-2">
                                <label class="col-md-5 form-control-label" for="precio">N° Factura</label>
                                <div class="mb-3">
                                    <input type="text" id="fact_nro" name="fact_nro" class="form-control" placeholder="Ingrese nro de factura" required>
                                </div>
                            </div>
                        </div>
                        <div id="div1" style="display:;" class="form-group row border">
                            <div class="col-md-5">  
                                <label class="col-md-3 form-control-label" for="nombre">Producto</label>
                                <div class="mb-3">
                                    <select class="form-control" name="producto_id" id="producto_id" onchange="obtenerPrecio2(), obtenerImagenProducto()" style= "width:400px">
                                                                    
                                        <option value="0" disabled>Seleccionar Producto</option>

                                    </select>
                                </div>                                
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-3 form-control-label" for="precio">Stock</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="stock" name="stock" class="form-control" placeholder="Stock">
                                </div>
                            </div>
                            <div hidden class="col-md-3">
                                <label class="col-md-3 form-control-label" for="precio">U. medida</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="medida" name="medida" class="form-control">
                                </div>
                                <input type="hidden" id="valor" name="valor" class="form-control">
                            </div>
                            <div class="col-md-3" id='imagencargando'>
                                
                            </div>
                        </div>                        
                        <div class="form-group row">
                            
                            <div class="col-md-4">
                                <label class="col-md-3 form-control-label" for="precio">Precio</label>
                                <div class="col-md-4">
                                    <input type="text" id="precio" name="precio" class="form-control number" placeholder="Ingrese precio">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-3 form-control-label" for="precio">Precio Recargo</label>
                                <div class="col-md-4">
                                    <input type="text" id="precio_recargo" name="precio_recargo" class="form-control number2" placeholder="Ingrese precio">
                                </div>
                            </div>                            
                            <div class="col-md-4">
                                <label class="col-md-6 form-control-label" for="documento">USAR PRECIO CON RECARGO</label>
                                
                                <div class="col-md-4">
                                
                                    <select class="form-control" name="recargo" id="recargo">                                                                        
                                        <option disabled>Seleccione</option>
                                        <option value="0">NO</option>
                                        <option value="1">SI</option>
                                    </select>
                                
                                </div>
                            </div>
                            <div class="col-md-3"> 
                            <label class="col-md-3 form-control-label" for="precio"></label> 
                                <div class="mb-3">                 
                                    <button type="button" onClick="agregar_producto()" class="btn btn-primary"><i class="fa fa-plus fa-1x"></i> Agregar detalle</button>
                                </div>
                            </div>
                            <div class="col-3">
                                <label for="horizontal-firstname-input" class="col-md-5 col-form-label">TOTAL Gs.</label>
                                <div class="col-md-5">
                                    <input readonly="readonly" type="text" id="total_apag_vista" name="total_apag_vista" class="form-control">
                                </div>
                            </div>
                            {{-- <div class="col-3">
                                <label for="horizontal-firstname-input" class="col-md-5 col-form-label">TOTAL Gs.</label>
                                <div class="col-md-5">
                                    <input readonly="readonly" type="text" id="total_gs_vista" name="total_gs_vista" class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <label for="horizontal-firstname-input" class="col-md-5 col-form-label">TOTAL $ Pesos</label>
                                <div class="col-md-5">
                                    <input readonly="readonly" type="text" id="total_ps_vista" name="total_ps_vista" class="form-control">
                                </div>
                            </div>
                            <div class="col-3">
                                <label for="horizontal-firstname-input" class="col-md-5 col-form-label">TOTAL R$ Reales</label>
                                <div class="col-md-5">
                                    <input readonly="readonly" type="text" id="total_rs_vista" name="total_rs_vista" class="form-control">
                                </div>
                            </div> --}}
                            <div class="col-3">
                                <label style="color: red; font-weight: bold;" for="horizontal-firstname-input" class="col-md-5 col-form-label">TOTAL DE ITEMS</label>
                                <div class="col-md-5">
                                    <input readonly="readonly" type="text" id="total_items" name="total_items" class="form-control">
                                </div>
                            </div>   
                        </div>
                        <div hidden class="form-group row">
                            <div hidden class="col-md-4">
                                <label class="col-md-3 form-control-label" for="cantidad">Precio Min.</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="precio_min" name="precio_min" class="form-control" placeholder="Precio mínimo">
                                </div>
                            </div>
                            <div hidden class="col-md-4">
                                <label class="col-md-3 form-control-label" for="precio">Precio Max</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="precio_max" name="precio_max" class="form-control" placeholder="Precio máximo">
                                </div>
                            </div>
                            <div class="col-md-4">
                                <label class="col-md-5 form-control-label" for="precio">Precio Mayorista</label>
                                <div class="mb-3">
                                    <input readonly type="text" id="precio_mayor" name="precio_mayor" class="form-control">
                                    <input readonly type="text" id="cantidad_mayor" name="cantidad_mayor" class="form-control" placeholder="Precio máximo">
                                </div>
                            </div>                            
                        </div><br>
                        <div class="form-group row border">

                            <h3>Detalle de la venta</h3>

                            <div class="table-responsive col-md-12">
                                <table id="detalles" class="table table-bordered table-striped table-sm">
                                    <thead>
                                        <tr class="bg-info">
                                            <th>Eliminar</th>
                                            <th>Producto</th>
                                            <th>Precio(Gs)</th>
                                            <th>Cantidad</th>
                                            <th>SubTotal (Gs)</th>
                                        </tr>
                                    </thead>                                
                                    <tfoot>                             
                                        <tr>
                                            <th  colspan="4"><p align="right">TOTAL:</p></th>
                                            <th><p align="right"><span id="total_html">Gs. 0.00</span></th>
                                        </tr>

                                        <tr>
                                            <th colspan="4"><p align="right">TOTAL IVA (5%):</p></th>
                                            <th><p align="right"><span id="total_iva_5_html">Gs. 0.00</span><input type="hidden" name="total_iva_5" id="total_iva_5"></p></th>
                                        </tr>

                                        <tr>
                                            <th colspan="4"><p align="right">TOTAL IVA (10%):</p></th>
                                            <th><p align="right"><span id="total_iva_html">Gs. 0.00</span><input type="hidden" name="total_iva" id="total_iva"></p></th>
                                        </tr>

                                        <tr>
                                            <th  colspan="4"><p align="right">TOTAL PAGAR:</p></th>
                                            <th><p align="right"><span align="right" id="total_pagar_html">Gs. 0.00</span> <input type="hidden" name="total_pagar" id="total_pagar"></p></th>
                                        </tr>  
                                    </tfoot>
                                    <tbody>
                                    </tbody>  
                                </table>
                            </div>
                            
                            </div>

                            
                    </div>
                    <div class="card-body">
                         <div class="form-group row">
                            <div class="col-md-4">
                                <label class="col-md-3 form-control-label" for="rol">Cliente</label>
                                
                                <div class="mb-3">
                                
                                    <select class="form-control" name="cliente_id" id="cliente_id" style= "width:330px">
                                                                        
                                        <option value="0" disabled>Seleccionar Cliente</option>

                                    </select>
                                
                                </div>
                            </div>
                        </div>
                        <h5>FORMA DE PAGO</h5>
                           <div class="form-group row">                                
                                <div class="col-3">
                                    <label for="horizontal-firstname-input" class="col-md-5 col-form-label">Total Gs.</label>
                                    <div class="col-md-5">
                                        <input readonly="readonly" type="text" id="total_apag" name="total_apag" class="form-control">
                                    </div>
                                </div>
                                {{-- <div class="col-3">
                                    <label for="horizontal-firstname-input" class="col-md-5 col-form-label">Total Gs.</label>
                                    <div class="col-md-5">
                                        <input readonly="readonly" type="text" id="total_gs" name="total_gs" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <label for="horizontal-firstname-input" class="col-md-5 col-form-label">Total $ Pesos</label>
                                    <div class="col-md-5">
                                        <input readonly="readonly" type="text" id="total_ps" name="total_ps" class="form-control">
                                    </div>
                                </div>
                                <div class="col-3">
                                    <label for="horizontal-firstname-input" class="col-md-5 col-form-label">Total R$ Reales</label>
                                    <div class="col-md-5">
                                        <input readonly="readonly" type="text" id="total_rs" name="total_rs" class="form-control">
                                    </div>
                                </div> --}}
                            </div><br>
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total Efectivo </label>
                                    <div class="col-sm-3">
                                        <input type="text" id="total_pagadof" name="total_pagadof" class="form-control number10" value="0">
                                    </div>
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nro Cheque </label>
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Banco </label>
                                </div>
            
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total Cheque </label>
                                    <div class="col-sm-3">
                                        <input type="text" id="total_pagadoch" name="total_pagadoch" class="form-control number20" value="0">
                                    </div>
                                    
                                    <div class="col-sm-3">
                                        <input type="text" id="nro_cheque" name="nro_cheque" class="form-control">
                                    </div>
                                    
                                    <div class="col-sm-3">
                                    <select class="form-control" name="ban_che_id" id="ban_che_id">                                     
                                        <option value="0" disabled>Seleccione</option>
                                        
                                        @foreach($bancos as $ban)
                                            <option value="{{$ban->id}}">{{$ban->descripcion}}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
            
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total T. Credito </label>
                                    <div class="col-sm-3">
                                        <input type="text" id="total_pagadotc" name="total_pagadotc" class="form-control number30" value="0">
                                    </div>
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Nro Comprob. </label>
                                    <div class="col-sm-3">
                                        <input type="text" id="nro_tcredito" name="nro_tcredito" class="form-control">
                                    </div>
                                </div>
            
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total T. Debito </label>
                                    <div class="col-sm-3">
                                        <input type="text" id="total_pagadotd" name="total_pagadotd" class="form-control number40" value="0">
                                    </div>
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">N° Comprob. </label>
                                    <div class="col-sm-3">
                                        <input type="text" id="nro_tdebito" name="nro_tdebito" class="form-control">
                                    </div>
                                </div>
                                <div class="row mb-4">
                                    <label for="horizontal-firstname-input" class="col-sm-3 col-form-label">Total Transferencia </label>
                                    <div class="col-sm-3">
                                        <input type="text" id="total_pagadotr" name="total_pagadotr" class="form-control number50" value="0">
                                    </div>
                                    
                                    <div hidden class="col-sm-4">
                                    <select class="form-control" name="cuenta_id" id="cuenta_id">                                     
                                        <option value="0">Seleccione</option>                                        
                                        @foreach($cuentas as $cc)
                                            <option value="{{$cc->id}}">{{$cc->nro_cuenta}} - {{$cc->banco}}</option>
                                        @endforeach
                                    </select>
                                    </div>
                                </div>
                        </div><br>
                    
                    <div class="form-group row" id="guardar">
                            
                        <div class="col-sm-3">
                            <input type="hidden" name="_token" value="{{csrf_token()}}">
                        
                            <button type="submit" class="btn btn-success"><i class="fa fa-save fa-2x"></i> Registrar</button>
                        
                        </div>
                    </div>
                </div>
            </form>  
        </div>
        <!-- Fin ejemplo de tabla Listado -->
    </div>

</main>

@endsection

@section('script')
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
    $(document).ready(function(){
        console.log("entra a facurrars");
        $("#contable").change( function() {
            if ($(this).val() === "0") {
                $("#fact_nro").prop("readonly", true);
            } else {
                $("#fact_nro").prop("readonly", false);
            }
        });
        
    });
    </script>

    <script>
        // Get the input field
        var input = document.getElementById("buscador");       

        // Execute a function when the user presses a key on the keyboard
        input.addEventListener("keypress", function(event) {
        // If the user presses the "Enter" key on the keyboard
            if (event.key === "Enter") {
                // Cancel the default action, if needed
                event.preventDefault();
                renderProduct(input.value);               
                console.log("enter anda");
                // Trigger the button element with a click
            }
            
    
        });
         async function renderProduct(cb){
                let p_id = document.getElementById("producto_id");
                let productos = await getProductosBarra(cb);
                if (productos.length > 0) 
                {
                    productos.forEach(producto => 
                    {
                        p_id.value = producto.id;
                        obtenerPrecio(producto.id,producto.descripcion);
                        obtenerImagen(producto.id);
                    });
                }

                
        }
    </script>

<script>
    const number10 = document.querySelector('.number10');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number10.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
</script>

<script>
    const number20 = document.querySelector('.number20');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number20.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
</script>
<script>
    const number30 = document.querySelector('.number30');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number30.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
</script>
<script>
    const number40 = document.querySelector('.number40');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number40.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
</script>
<script>
    const number50 = document.querySelector('.number50');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number50.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
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

<script>
    const number2 = document.querySelector('.number2');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number2.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
</script>

<script>
    const number3 = document.querySelector('.number3');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number3.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
</script>

<script>
    const number4 = document.querySelector('.number4');
    function formatNumber (n) {
        n = String(n).replace(/\D/g, "");
    return n === '' ? n : Number(n).toLocaleString();
    }
    number4.addEventListener('keyup', (e) => {
        const element = e.target;
        const value = element.value;
    element.value = formatNumber(value);
    });
</script>
<script type="text/javascript">
    var CSRF_TOKEN = $('meta[name="csrf-token"]').attr('content');
    $(document).ready(function(){
        $('#cliente_id').select2({
            ajax:{
                url:"{{route('getClientesVentas') }}",
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
        $('#producto_id').select2({
            ajax:{
                url:"{{route('getProductos') }}",
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
                cache: true,
                closeOnSelect: false
            }
        });
    });


    async function obtenerPrecio(p_id,p_nombre)//funcion para enviar datos de la empresa al form
    {
        
        var producto_id = document.getElementById("producto_id").value 
        var combo = document.getElementById("producto_id")

        if (combo.selectedIndex > 0){
            var producto_nombre = combo.options[combo.selectedIndex].text;
        }
        

        p_id = (typeof p_id === 'undefined') ? producto_id : p_id;
        p_nombre = (typeof p_nombre === 'undefined') ? producto_nombre : p_nombre;
        console.log('valor -> '+p_id);//aca nos trae el id del medidor para consultar  
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    }
                });

        await $.ajax({
                //headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type:  'post',
                dataType: 'json',
                data:  {producto_id:p_id},
                url:   '{{ url('/obtenerPrecio') }}', //URL que indica la ruta en web.php                                    
                        
                success:  function (data) {

                    //funcion para agregar separador de miles
                    var formatNumber = {
                            separador: ".", // separador para los miles
                            sepDecimal: ',', // separador para los decimales
                            formatear:function (num){
                            num +='';
                            var splitStr = num.split('.');
                            var splitLeft = splitStr[0];
                            //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
                            var regx = /(\d+)(\d{3})/;
                            while (regx.test(splitLeft)) {
                            splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
                            }
                            return this.simbol + splitLeft;
                            },
                            new:function(num, simbol){
                            this.simbol = simbol ||'';
                            return this.formatear(num);
                            }
                        }
                    //let numero1 =  (data.var[0].precio_venta).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    console.log("STOCK: "+data.var[0].stock);
                    document.getElementById("precio").value = formatNumber.new(data.var[0].precio_venta);
                    document.getElementById("precio_recargo").value = formatNumber.new(data.var[0].precio_tarjeta);
                    // document.getElementById("precio_unidad").value = formatNumber.new(data.var[0].precio_venta);
                    // document.getElementById("precio_min").value = formatNumber.new(data.var[0].precio_min);
                    // document.getElementById("precio_max").value = formatNumber.new(data.var[0].precio_max);
                    document.getElementById("stock").value = data.var[0].stock;
                    document.getElementById("medida").value = data.var2[0].unidad_medida;

                    document.getElementById("precio_mayor").value = formatNumber.new(data.var[0].precio_mayor);
                    document.getElementById("cantidad_mayor").value = (data.var[0].cantidad_mayor);
                }
                
        });
        //console.log("entrnado agg "+document.getElementById("precio").value);
        agregar_producto(p_id,p_nombre);
        
    }

    </script>

    <script>
    function obtenerPrecio2()//funcion para enviar datos de la empresa al form
    {
        
        var producto_id = document.getElementById("producto_id").value 
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    }
                });

        $.ajax({
                //headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type:  'post',
                dataType: 'json',
                data:  {producto_id:producto_id},
                url:   '{{ url('/obtenerPrecio') }}', //URL que indica la ruta en web.php                                    
                        
                success:  function (data) {

                    //funcion para agregar separador de miles
                    var formatNumber = {
                            separador: ".", // separador para los miles
                            sepDecimal: ',', // separador para los decimales
                            formatear:function (num){
                            num +='';
                            var splitStr = num.split('.');
                            var splitLeft = splitStr[0];
                            //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
                            var regx = /(\d+)(\d{3})/;
                            while (regx.test(splitLeft)) {
                            splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
                            }
                            return this.simbol + splitLeft;
                            },
                            new:function(num, simbol){
                            this.simbol = simbol ||'';
                            return this.formatear(num);
                            }
                        }
                    //let numero1 =  (data.var[0].precio_venta).replace(/\B(?=(\d{3})+(?!\d))/g, '.');

                    console.log("STOCK: "+data.var[0].stock);

                    document.getElementById("precio").value = formatNumber.new(data.var[0].precio_venta);
                    document.getElementById("precio_recargo").value = formatNumber.new(data.var[0].precio_tarjeta);
                    // document.getElementById("precio_unidad").value = formatNumber.new(data.var[0].precio_venta);
                    // document.getElementById("precio_min").value = formatNumber.new(data.var[0].precio_min);
                    // document.getElementById("precio_max").value = formatNumber.new(data.var[0].precio_max);
                    document.getElementById("stock").value = data.var[0].stock;
                    document.getElementById("medida").value = data.var2[0].unidad_medida;

                    document.getElementById("precio_mayor").value = formatNumber.new(data.var[0].precio_mayor);
                    document.getElementById("cantidad_mayor").value = (data.var[0].cantidad_mayor);
                }
                
        });
        
    }
    </script>

    <script>
    function obtenerImagen(p_id)//funcion para enviar datos de la empresa al form
    {
        
        var producto_id = p_id; 
        //var producto_id = document.getElementById("buscador").value 
        console.log("obt"+producto_id);
        $.ajaxSetup({
                    headers: {
                        'X-CSRF-TOKEN': '<?= csrf_token() ?>'
                    }
                });

        $.ajax({
                //headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                type:  'post',
                dataType: 'json',
                data:  {producto_id:producto_id},
                url:   '{{ url('/obtenerImagen') }}', //URL que indica la ruta en web.php                                    
                        
                success:  function (data) {

                   
                    console.log("URL IMAGEN  ORIGIANL: "+data.var4[0].imagen);                    
                    foto = data.var4[0].imagen;
                    console.log("FOTO: "+foto); 
                    imagen = `<img src="../storage/img/electro/${foto}" width="200" height="auto"/>`
                    console.log("IMAGEN: "+imagen); 
                    document.getElementById('imagencargando').innerHTML = imagen;
                        
                }
                
        });
        
    }
    </script>
    <script>
    function obtenerImagenProducto()//funcion para enviar datos de la empresa al form
    {        
        //var producto_id = p_id; 
        var producto_id = document.getElementById("producto_id").value 
        obtenerImagen(producto_id);
        
    }
    </script>

<script type="text/javascript">
   async function getProductosBarra(codigo_barra) {
    let url = "";
    // asi para local
    url = '../obtenerProductosB/' + codigo_barra;
    //asi es para la web
    //url = 'obtenerProductosB/' + codigo_barra;
    
    try {
        let res = await fetch(url);
        return await res.json();
    } catch (error) {
        console.log(error);
    }
    };

</script>

    <script>
        var cont=0;           
        subtotal=[];
        totalVista=0;
        total=0;
        cant_total=[];
        sumaitems=0;
        subtotalVista=[];
        producto_acu=[];
        cantidad_acu=[];
       async function agregar_producto(p_id,p_nombre)
        {          
            agregar();

                function agregar()
                {
                    //obtenerPrecio();
                    producto_id = (typeof p_id === 'undefined') ? $("#producto_id").val() : p_id;
                    producto = (typeof p_nombre === 'undefined') ? $("#producto_id option:selected").text() : p_nombre;

                    //producto_id= $("#producto_id").val();
                    //producto= $("#producto_id option:selected").text();
                    cantidad= $("#cantidad").val();
                    recargo= $("#recargo").val();
                    console.log("recargo: "+recargo);
                    if(recargo == 0)
                    {
                        precio= $("#precio").val();
                    }
                    else
                    {
                        precio= $("#precio_recargo").val();
                    }                    
                    stockpro = $("#stock").val();
                    precio_minimo= $("#precio_min").val();
                    precio_maximo= $("#precio_max").val();
                    unidad_medida= $("#medida").val();
                    valor= 1;
                    precio_mayor= $("#precio_mayor").val();
                    cantidad_mayor= $("#cantidad_mayor").val();
                    iva=11;
                    if(parseFloat(stockpro) < parseFloat(cantidad)){
                        
                        Swal.fire({
                        type: 'error',
                        title: 'Cuidado',
                        text: 'Stock insuficiente!'
                        })
                        
                    }
                    
                    if(producto_id !="" && cantidad!="" && cantidad>0 && precio!="" && precio!="null")
                    {
                        if((parseFloat(cantidad) >= parseFloat(cantidad_mayor))&&(precio_mayor > 0 && cantidad_mayor > 0))
                        {
                            precio = precio_mayor;
                        }
                        precioFinal = precio.replaceAll(".","");
                        cantidad_calculo = cantidad/valor;                      
                        subtotal[cont]=(cantidad_calculo*precioFinal);
                        total= total+subtotal[cont];
                        cant_total[cont] = parseFloat(cantidad);
                        sumaitems= sumaitems + cant_total[cont];
                        //funcion para agregar separador de miles
                        var formatNumber = {
                            separador: ".", // separador para los miles
                            sepDecimal: ',', // separador para los decimales
                            formatear:function (num){
                            num +='';
                            var splitStr = num.split('.');
                            var splitLeft = splitStr[0];
                            //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
                            var regx = /(\d+)(\d{3})/;
                            while (regx.test(splitLeft)) {
                            splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
                            }
                            return this.simbol + splitLeft;
                            },
                            new:function(num, simbol){
                            this.simbol = simbol ||'';
                            return this.formatear(num);
                            }
                        }

                            //totales para la vista
                            subtotalVista[cont]=formatNumber.new(subtotal[cont]);
                            totalVista=formatNumber.new (total);

                            var fila= '<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar('+cont+
                            ');"><i class="fa fa-times fa-2x"></i></button></td> <td><input  style="width:400px" type="hidden" name="producto_id[]" value="'+producto_id+'">'+producto+
                            '</td> <td><input readonly style="width:100px" type="text" id="precio[]" name="precio[]"  value="'+precio+
                            '"> </td>  <td><input style="width:40px" readonly type="number" name="cantidad[]" id="cantidad[]" class="ent_cantidad" value="'+cantidad+
                            '"> </td> <td hidden><input readonly type="number" name="cantidad_calculo[]" value="'+cantidad_calculo+
                            '"> <td>Gs. <input style="width:95px" type="text" class="totallinea" name="totallinea[]" id="totallinea[]" value="' + subtotalVista[cont] + '"> </td></tr>';

                            cont++;
                            limpiar();
                            totales();
                            
                            $('#detalles').append(fila);
                    }
                    else
                    {   
                        Swal.fire({
                        type: 'error',
                        //title: 'Oops...',
                        text: 'Rellene todos los campos del detalle de la venta',                   
                        })               
                    }            
                }
        
            
                function limpiar(){
                
                $("#precio").val("");
                $("#precio_recargo").val("");
                $("#buscador").val("");
                
        
                }
        
                function totales(){

                    var formatNumber = {
                            separador: ".", // separador para los miles
                            sepDecimal: ',', // separador para los decimales
                            formatear:function (num){
                            num +='';
                            var splitStr = num.split('.');
                            var splitLeft = splitStr[0];
                            //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
                            var regx = /(\d+)(\d{3})/;
                            while (regx.test(splitLeft)) {
                            splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
                            }
                            return this.simbol + splitLeft;
                            },
                            new:function(num, simbol){
                            this.simbol = simbol ||'';
                            return this.formatear(num);
                            }
                        }
        
                $("#total_html").html("Gs. " + formatNumber.new(total));
                $("#total").html("Gs. " + total);
                //total_iva=total*iva/100;
                total_iva=Math.round(total/iva);
                total_pagar=total;
                $("#total_iva_html").html("Gs. " + formatNumber.new(total_iva));
                $("#total_pagar_html").html("Gs. " + formatNumber.new(total_pagar));
                $("#total_pagar").val(total_pagar);
                $("#total_iva").val(total_iva);

                //TOTAL PARA EL DETALLE DE COBRO total_pagadof
                $("#total_apag").val(formatNumber.new(total_pagar));
                $("#total_apag_vista").val(formatNumber.new(total_pagar));
                $("#total_pagadof").val(formatNumber.new(total_pagar));

                $("#total_items").val(sumaitems);
                
                }       
        
                function evaluar(){

                    if(total>0){
        
                    $("#guardar").show();
        
                    } else{
                        
                    $("#guardar").hide();
                    }
                }
        
                function eliminar(index){

                    var formatNumber = {
                    separador: ".", // separador para los miles
                    sepDecimal: ',', // separador para los decimales
                    formatear:function (num){
                    num +='';
                    var splitStr = num.split('.');
                    var splitLeft = splitStr[0];
                    //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
                    var regx = /(\d+)(\d{3})/;
                    while (regx.test(splitLeft)) {
                    splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
                    }
                    return this.simbol + splitLeft;
                    },
                    new:function(num, simbol){
                    this.simbol = simbol ||'';
                    return this.formatear(num);
                    }
                }
        
                total=total-subtotal[index];
                sumaitems=sumaitems-cant_total[index];
                //total_iva= total*11/100;
                total_iva=Math.round(total/iva);
                total_pagar_html = total;
                
                $("#total_html").html("Gs." + total);
                $("#total_iva_html").html("Gs." + total_iva);
                $("#total_pagar_html").html("Gs." + total_pagar_html);
                $("#total_pagar").val(total_pagar_html);

                //TOTAL PARA EL DETALLE DE COBRO total_pagadof
                $("#total_apag").val(formatNumber.new(total));
                $("#total_apag_vista").val(formatNumber.new(total));
                $("#total_pagadof").val(formatNumber.new(total));

                $("#total_items").val(sumaitems);
                
                $("#fila" + index).remove();
                //evaluar();
                }
        }
    </script>

    <script>
     
     $(document).ready(function(){
        
        $("#agregar_detalle").click(function(){
   
            agregar();
        });
   
     });
   
      var cont=0;
      total=0;
      cant_total=[];
      sumaitems=0;
      subtotal=[];
      totalVista=0;
      subtotalVista=[];
      producto_acu=[];
      cantidad_acu=[];
      console.log("pasa por aca detalle");
   
        function agregar()
        {
   
            producto_id= $("#producto_id").val();
            producto= $("#producto_id option:selected").text();
            cantidad= $("#cantidad").val();
            recargo= $("#recargo").val();
            console.log("recargo: "+recargo);
            if(recargo == 0)
            {
                precio= $("#precio").val();
            }
            else
            {
                precio= $("#precio_recargo").val();
            } 

            stockpro = $("#stock").val();
            precio_minimo= $("#precio_min").val();
            precio_maximo= $("#precio_max").val();
            unidad_medida= $("#medida").val();
            valor= 1;
            precio_mayor= $("#precio_mayor").val();
            cantidad_mayor= $("#cantidad_mayor").val();
            iva=11;
           
            //real = realA.replaceAll(".","");
             if(parseFloat(stockpro) < parseFloat(cantidad)){
                
                Swal.fire({
                type: 'error',
                title: 'Cuidado',
                text: 'Stock insuficiente!'
                })
                
            }
             
            if(producto_id !="" && cantidad!="" && cantidad>0 && precio!="" && precio!="null")
            {
               if(parseFloat(cantidad) >= parseFloat(cantidad_mayor))
               {
                precio = precio_mayor;
               }
               precioFinal = precio.replaceAll(",","");
               cantidad_calculo = cantidad/valor;
               
               subtotal[cont]=(cantidad_calculo*precioFinal);
               total= total+subtotal[cont];
               cant_total[cont] = parseFloat(cantidad);
               sumaitems= sumaitems + cant_total[cont];

               //funcion para agregar separador de miles
              var formatNumber = {
                    separador: ".", // separador para los miles
                    sepDecimal: ',', // separador para los decimales
                    formatear:function (num){
                    num +='';
                    var splitStr = num.split('.');
                    var splitLeft = splitStr[0];
                    //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
                    var regx = /(\d+)(\d{3})/;
                    while (regx.test(splitLeft)) {
                    splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
                    }
                    return this.simbol + splitLeft;
                    },
                    new:function(num, simbol){
                    this.simbol = simbol ||'';
                    return this.formatear(num);
                    }
                }

               //totales para la vista
               subtotalVista[cont]=formatNumber.new(subtotal[cont]);
               totalVista=formatNumber.new (total);

                var fila= '<tr class="selected" id="fila'+cont+'"><td><button type="button" class="btn btn-danger btn-sm" onclick="eliminar('+cont+
                ');"><i class="fa fa-times fa-2x"></i></button></td> <td><input  style="width:400px" type="hidden" name="producto_id[]" value="'+producto_id+'">'+producto+
                '</td> <td><input readonly style="width:100px" type="text" id="precio[]" name="precio[]"  value="'+precio+
                '"> </td>  <td><input style="width:40px" readonly type="number" name="cantidad[]" id="cantidad[]" class="ent_cantidad" value="'+cantidad+
                '"> </td> <td hidden><input readonly type="number" name="cantidad_calculo[]" value="'+cantidad_calculo+
                '"> <td>Gs. <input style="width:95px" type="text" class="totallinea" name="totallinea[]" id="totallinea[]" value="' + subtotalVista[cont] + '"> </td></tr>';

                cont++;
                limpiar();
                totales();
                
                //evaluar();
                $('#detalles').append(fila);
               }
               else
               {   
                   Swal.fire({
                   type: 'error',
                   //title: 'Oops...',
                   text: 'Rellene todos los campos del detalle de la venta',                   
                   })               
               }            
        }
   
       
        function limpiar(){
           
           $("#buscador").val("");
           $("#precio").val("");
           $("#precio_recargo").val("");
           
   
        }
   
        function totales(){

            var formatNumber = {
            separador: ".", // separador para los miles
            sepDecimal: ',', // separador para los decimales
            formatear:function (num){
            num +='';
            var splitStr = num.split('.');
            var splitLeft = splitStr[0];
            //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
            var regx = /(\d+)(\d{3})/;
            while (regx.test(splitLeft)) {
            splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
            }
            return this.simbol + splitLeft;
            },
            new:function(num, simbol){
            this.simbol = simbol ||'';
            return this.formatear(num);
            }
        }
   
        $("#total_html").html("Gs. " + formatNumber.new(total));
        $("#total").html("Gs. " + total);
        //total_iva=total*iva/100;
        total_iva=Math.round(total/iva);
        total_pagar=total;
        $("#total_iva_html").html("Gs. " + formatNumber.new(total_iva));
        $("#total_pagar_html").html("Gs. " + formatNumber.new(total_pagar));
        $("#total_pagar").val(total_pagar);
        $("#total_iva").val(total_iva);

        //TOTAL PARA EL DETALLE DE COBRO total_pagadof
        $("#total_apag").val(formatNumber.new(total_pagar));
        $("#total_apag_vista").val(formatNumber.new(total_pagar));
        $("#total_pagadof").val(formatNumber.new(total_pagar));

        $("#total_items").val(sumaitems);
        
        }
   
        function eliminar(index){

            var formatNumber = {
                    separador: ".", // separador para los miles
                    sepDecimal: ',', // separador para los decimales
                    formatear:function (num){
                    num +='';
                    var splitStr = num.split('.');
                    var splitLeft = splitStr[0];
                    //var splitRight = splitStr.length > 1 ? this.sepDecimal + splitStr[1] : '';
                    var regx = /(\d+)(\d{3})/;
                    while (regx.test(splitLeft)) {
                    splitLeft = splitLeft.replace(regx, '$1' + this.separador + '$2');
                    }
                    return this.simbol + splitLeft;
                    },
                    new:function(num, simbol){
                    this.simbol = simbol ||'';
                    return this.formatear(num);
                    }
                }
   
            total=total-subtotal[index];
            sumaitems=sumaitems-cant_total[index];
            //total_iva= total*11/100;
            total_iva=Math.round(total/iva);
            total_pagar_html = total;
            
            $("#total_html").html("Gs." + total);
            $("#total_iva_html").html("Gs." + total_iva);
            $("#total_pagar_html").html("Gs." + total_pagar_html);
            $("#total_pagar").val(total_pagar_html);

            //TOTAL PARA EL DETALLE DE COBRO total_pagadof
            $("#total_apag").val(formatNumber.new(total));
            $("#total_apag_vista").val(formatNumber.new(total));
            $("#total_pagadof").val(formatNumber.new(total));
            
            $("#total_items").val(sumaitems);
          
           $("#fila" + index).remove();
           //evaluar();
        }
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