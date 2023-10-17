@section('title') Productos @endsection

@section('css') 
        <!-- DataTables -->       
        <script src="{{ URL::asset('/assets/libs/table-edits/table-edits.min.js') }}"></script>
        <script src="{{ URL::asset('/assets/js/pages/table-editable.int.js') }}"></script>
@endsection

@if(session()->has('msj2'))
<div class="alert alert-success" role="alert">{{session('msj2')}}</div>    
@endif
@if(session()->has('msj3'))
<div class="alert alert-danger" role="alert">{{session('msj3')}}</div>    
@endif
<h2>Detalle de Producto</h2><br/>
<div class="menu card-header">
    
    <button style="margin: 10px" type="button" class="btn btn-primary waves-effect waves-light" data-target="#men_det">Producto</button><br>
    <button style="margin: 10px" type="button" class="btn btn-primary waves-effect waves-light" data-target="#men_img">Imagenes</button><br>
    <a href="javascript:history.back()"><button style="margin: 10px" type="button" class="btn btn-primary waves-effect waves-light">Atrás</button></a><br>


</div>

<div class="card-body">

    <div class="card-body" data-content id="men_img">
        <form id="addImagen">                     
            @csrf
            <div class="col-sm-9">
                <input type="file" id="imagen_agregar" name="imagen_agregar" class="form-control">
            </div>
            <div class="modal-footer">
                <button type="button" onclick="history.back()" class="btn btn-light" data-bs-dismiss="modal">Cerrar</button>
                @if((auth()->user()->idrol) != "11")
                <button type="submit" class="btn btn-primary">Guardar</button>
                @endif
            </div>
        </form>  
        <br>    
        <div class="contenedor-galeria" id="contenedor"></div>
    </div>

    <div data-content id="men_det" class="active card-body">


        <form action="{{route('producto.update','test')}}" method="post" class="form-horizontal">                               
            {{method_field('patch')}}
            @csrf
    
            <input type="hidden" id="id_producto" name="id_producto"  value={{$producto->id}}>
            {{--<div class="container-fluid">--}}

         <div class="row mb-4">
                <label hidden for="horizontal-firstname-input" class="col-sm-2 col-form-label">Codigo</label>
                <div hidden class="col-sm-3">
                    <input type="text" id="ArtCode" value="{{$producto->ArtCode}}" name="ArtCode" class="form-control" placeholder="Ingrese el Nombre">
                </div>            
                @if (sizeof($imagenes) >0)
                
                    <div class="contenedor-galeria col-sm-2" id="contenedor2">
                        @if ($imagenes[0]->tipo=="F")
                        
                            <img src="../storage/img/electro/{{$imagenes[0]->imagen}}" id={{$imagenes[0]->id}} alt="" class="galeria-img">
                        
                        @else
                            <img src="{{$imagenes[0]->imagen}}" id={{$imagenes[0]->id}} alt="" class="galeria-img">
                        @endif
                    </div>
                @else 
                    <div class="contenedor-galeria col-sm-2" id="contenedor2">
                        <h3>No se registran imagenes</h3>
                    </div>
                @endif
                
            </div>
        <div class="form-group row">
            <div class="row mb-4">
                <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Cod. de Barra</label>
                <div class="col-sm-4">
                    <input type="text" id="cod_barra" value="{{$producto->cod_barra}}" name="cod_barra" class="form-control" placeholder="Ingrese el codigo">
                </div>
            </div>
        </div>
        <div class="row mb-4">
            <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Descripcion</label>
            <div class="col-sm-7">
                <input type="text" id="descripcion" value="{{$producto->descripcion}}" name="descripcion" class="form-control" placeholder="Ingrese el Nombre">
            </div>
        </div>

        <div class="form-group row">
            <div class="col-md-4">
                <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Stock</label>
                <div class="col-md-4">
                    <input type="text" id="stock" value="{{$producto->stock}}" name="stock" class="form-control" placeholder="Ingrese la cantidad">
                </div>
            </div>
        </div>
        <div class="form-group row">  
            <div class="col-md-4">
                <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Precio Compra</label>
                <div class="col-md-4">
                    <input type="text" id="precio_compra" value="{{number_format(($producto->precio_compra), 0, ",", ".")}}" name="precio_compra" class="form-control number" placeholder="Ingrese el precio compra">
                </div>
            </div>  
            <div class="col-md-4">
                <label class="col-sm-3 col-form-label">Precio Recargo</label>
                <div class="col-md-4">
                    <input type="text" id="precio_tarjeta" value="{{number_format(($producto->precio_tarjeta), 2, ".", ",")}}" name="precio_tarjeta" class="form-control number2" placeholder="Ingrese el precio venta">
                </div>
            </div>      
            <div class="col-md-4">
                <label class="col-sm-3 col-form-label">Precio Dólares</label>
                <div class="col-md-4">
                    <input type="text" id="precio_venta" value="{{number_format(($producto->precio_venta), 2, ".", ",")}}" name="precio_venta" class="form-control number2" placeholder="Ingrese el precio venta">
                </div>
            </div>
        </div>
        <div class="form-group row">     
            <div class="col-md-4">
                <label class="col-sm-4 col-form-label">Precio Guaraníes</label>
                <div class="col-md-4">
                    <input readonly type="text" id="precio_gs" value="{{number_format(($producto->precio_venta * $cotizaciones->dolVenta), 0, ",", ".")}}" name="precio_gs" class="form-control number2" placeholder="Ingrese el precio venta">
                </div>
            </div>
            <div class="col-md-4">
                <label class="col-sm-4 col-form-label">Precio Pesos</label>
                <div class="col-md-4">
                    <input readonly type="text" id="precio_ps" value="{{number_format(($producto->precio_venta*($cotizaciones->psVenta)), 2, ",", ".")}}" name="precio_ps" class="form-control number2" placeholder="Ingrese el precio venta">
                </div>
            </div>
            <div class="col-md-4">
                <label class="col-sm-4 col-form-label">Precio Reales</label>
                <div class="col-md-4">
                    <input readonly type="text" id="precio_rs" value="{{number_format(($producto->precio_venta*($cotizaciones->rsVenta)), 2, ",", ".")}}" name="precio_rs" class="form-control number2" placeholder="Ingrese el precio venta">
                </div>
            </div>
        </div>
        <div hidden class="form-group row">
            <div class="col-md-4">
                <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Precio Mínimo</label>
                <div class="mb-3">
                    <input readonly type="text" id="precio_min" value="{{number_format(($producto->precio_min), 0, ",", ".")}}" name="precio_min" class="form-control number3" placeholder="Ingrese el precio minimo">
                </div>
            </div>

            <div class="col-md-4">
                <label for="horizontal-firstname-input" class="col-sm-4 col-form-label">Precio Máximo</label>
                <div class="mb-3">
                    <input type="text" id="precio_max" value="{{number_format(($producto->precio_max), 0, ",", ".")}}" name="precio_max" class="form-control number4" placeholder="Ingrese el precio maximo">
                </div>
            </div>
        </div>
        <div hidden class="form-group row">
            <div class="col-md-4">
                <label for="horizontal-firstname-input" class="col-sm-5 col-form-label">Precio al por mayor</label>
                <div class="mb-3">
                    <input type="text" id="precio_mayor" value="{{number_format(($producto->precio_mayor), 0, ",", ".")}}" name="precio_mayor" class="form-control number4" placeholder="Precio al por mayor">
                </div>
            </div>

            <div class="col-md-4">
                <label for="horizontal-firstname-input" class="col-sm-6 col-form-label">Cantidad al por mayor</label>
                <div class="mb-3">
                    <input type="text" id="cantidad_mayor" value="{{number_format(($producto->cantidad_mayor), 0, ",", ".")}}" name="cantidad_mayor" class="form-control number4" placeholder="Precio al por mayor">
                </div>
            </div>
        </div>

        <div class="row mb-4">
            <label for="horizontal-firstname-input" class="col-sm-2 col-form-label">Comentario</label>
            <div class="mb-3">
                <textarea id="comentarios" value="{{$producto->comentarios}}" name="comentarios" class="form-control" placeholder="Ingrese comentarios del producto ..." >{{$producto->comentarios}}</textarea>    
            </div>
        </div>

        <div class="modal-footer">
                <button type="button" onclick="history.back()" class="btn btn-light">Cerrar</button>
                @if((auth()->user()->idrol) != "11")
                    <button type="submit" class="btn btn-primary">Guardar</button>
                @endif
        </div>
    </form>
        <br/>
    </div>


</div>

@section('scripts')

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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
@endsection

