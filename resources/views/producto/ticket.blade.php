<!DOCTYPE>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>BARCODE</title>
    <style>
       
        #timbrado{
        font-size: 8px;
        }
       @page {
            margin-top: 0.5em;
            margin-left: 0.6em;
            margin-right: 0.6em;
            margin-bottom: 0.6em;
       }
       
    </style>
    <div id="body" class="body">   
        <section id="seccion1">        
            <div>
                <div style="text-align:center;" id="timbrado">
                    <img src="{{ public_path('assets/images/modafem.jpg') }}" alt="logo" height="60px" width="80px"/><br>
                    <p style="text-align:center;">{!! DNS1D::getBarcodeHTML($barcode, 'EAN13',1.8,35) !!}</p>
                    <strong>Codigo:</strong> <b>{{$producto->cod_barra}}</b><br>
                    <strong>Producto:</strong> <b>{{$producto->descripcion}}</b><br>
                    <strong>Precio:</strong> <b>Gs. {{number_format(($producto->precio_venta), 0, ",", ".")}}</strong>
                </div> 
            </div>
        </section> 
    </div>    
</html>
