<!DOCTYPE>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Factura Venta</title>
    <div id="body" class="body">

    <style>
        #seccion {
            height: 500px;
        }
        #encabezado{
        font-size: 12px;
        }
        #alturaDiv{
        font-size: 12px;
        height: 300px;
        }
        #cabecera{
        height: 150px;
        }
        #cabecera2{
        height: 370px;
        }
        #fecha{
        font-size: 13px;
        }
        #timbrado{
        font-size: 8px;
        }
        #dir{
        font-size: 8px;
        }
        #ruc{
        font-size: 12px;
        }
        #idtd{ border: 1px solid blue; width: 30px; word-wrap: break-word; }
        card-body {
            margin: 1cm 1cm 1cm;
        }

        th.titulo { 
            width: 100px !important;  
        }
            
        td.gfg { 
            word-break: break-all; 
        } 
        
        .alturatabla { 
                height: 140px !important; 
                margin: 0px 0px 0px 0px;
            }
        .Row
        {
            display: table;
            width: 100%;
            table-layout: fixed;
            border-spacing: 10px;
        }
        .Column
        {
            display: table-cell;
        }
        .firma {
        float: right;
        }
        .total {
        float: right;
        font-size: 12px;
        }
        #numeroletra{
            padding-left: 120px;
            font-size: 12px;
        }
        th.cantidad { 
                width: 70px !important;  
        }
        td.desc { 
            width: 310px !important;  
        } 
        th.precioU { 
            width: 75px !important;  
        }
        th.exe { 
            width: 75px !important;  
        }
        th.cinco { 
            width: 75px !important;  
        }
        th.diez { 
            width: 75px !important;  
        }
        table{
            table-layout: fixed;
            width: 100%;
        }
        table, th, td {
        border: 1px solid black;
        border-collapse: collapse;
        word-wrap: break-word;
        }
        
    </style>

<section id="seccion">
        
        <div id="cabecera">
           
        </div></br>
        
        <div class="Row">
            <div style="width:60%" class="Column" id="encabezado">                
                <strong>{{$diafecha}} de {{$mesLetra}} de {{$agefecha}}</strong><br>
                <strong  >SEÑORES: {{$ventas[0]->nombre}} </strong><br>
                <strong>DIRECCION: {{$ventas[0]->direccion}} </strong><br>
            </div>
            <div class="Column" id="encabezado">
                
                <strong></strong><br>
                <strong></strong><br>
                <strong>TELEFONO: {{$ventas[0]->telefono}} </strong><br>
            </div>
            <div class="Column" id="encabezado">
                
                <strong></strong><br>
                <strong>RUC/CI: {{$ventas[0]->ruc}} </strong><br>
                @if($ventas[0]->tipo_factura == 0)
                    <strong>CONDICION: CONTADO </strong><br>
                @else
                    <strong>CONDICION: CREDITO </strong><br>
                @endif
            </div>
        </div>
        <div style="border: 1px solid #0F0807;" id="alturaDiv">
            <!-- <table class="table-borderless alturatabla> -->
            <table>
                <thead class = "table-light">
                    <tr>
                        <!-- <th class="titulo">TRANS.</th> -->
                        <th id="cant">CANT</th>
                        <!-- <th class="titulo">CUOTA NRO</th> -->
                        <th id="desc">DESCRIPCION</th>
                        <th id="precioU">PRECIO UNIT.</th>
                        <th id="exe">EXENTAS</th>
                        <th id="cinco">GRAVADOS 5%</th>
                        <th id="diez">GRAVADOS 10%</th>
                        
                    </tr>
                </thead>
                @foreach($detalles as $det)
                <tbody>
                        <tr>
                            <td style="text-align:center;" style="width:1%">{{$det->cantidad}}</td>
                            <td id="desc" style="width:40%">{{$det->producto}}</td>                    
                            <td id="exe" style="text-align:center;width:12%">{{number_format(($det->precio), 0, ",", ".")}}</td>
                            <td id="cinco" style="width:50"></td>
                            <td id="cinco" style="width:15%"></td>
                            <td id="diez" style="text-align:center;width:15% ">{{number_format(($det->subtotal), 0, ",", ".")}}</td>
                        </tr>                              
                    
                </tbody>
                @endforeach
                
            </table><br>
            <img src="{{ public_path('assets/images/footerfact.png') }}" width="100%" />
        </div>
        <div id="encabezado">
            <table>
                <tr>
                        <td style="text-align:center;" style="width:1%"></td>
                        <td id="desc" style="width:40%"></td>                    
                        <td colspan=2 id="exe" style="text-align:center;">SUBTOTALES</td>
                        <td id="cinco" style="width:50"></td>
                        <td id="cinco" style="width:50"></td>
                        <td id="diez" style="text-align:center;width:15% ">{{number_format(($ventas[0]->total), 0, ",", ".")}}</td>
                </tr>
                <tr>
                        <td style="text-align:center;" style="width:1%"></td>
                        <td id="desc" style="width:40%"></td>                    
                        <td colspan=2 id="exe" style="text-align:center;">TOTAL A PAGAR</td>
                        <td id="cinco" style="width:50"></td>
                        <td id="cinco" style="width:50"></td>
                        <td id="diez" style="text-align:center;width:15% ">{{number_format(($ventas[0]->total), 0, ",", ".")}}</td>
                            
                </tr>
            </table><br>
        </div>
        <div id="numeroletra">
            <strong></strong>TOTAL GUARANIES:{{$tot_pag_let}}<br>
        </div>
            
        <div id="numeroletra" style="border: 1px solid #0F0807;" class="Row">
            
            <div class="Column" id="encabezado">
                <strong>LIQ. IVA (5%):</strong>0<br>
            </div>
            <div class="Column" id="encabezado"> 
                <strong>LIQ. IVA (10%):</strong> {{number_format(($ventas[0]->total/11), 0, ",", ".")}}<br>
            </div>
            <div class="Column" id="encabezado"> 
                <strong>TOTAL IVA:</strong> {{number_format(($ventas[0]->total/11), 0, ",", ".")}}<br>
            </div>
        </div>
    </div>

</section> 

<!-- DUPLICADO DE FACTURA -->

<section id="seccion">
        
            <div id="cabecera2">
           
            </div></br>
        
       <div class="Row">
            <div style="width:60%" class="Column" id="encabezado">                
                <strong>{{$diafecha}} de {{$mesLetra}} de {{$agefecha}}</strong><br>
                <strong  >SEÑORES: {{$ventas[0]->nombre}} </strong><br>
                <strong>DIRECCION: {{$ventas[0]->direccion}} </strong><br>
            </div>
            <div class="Column" id="encabezado">
                
                <strong></strong><br>
                <strong></strong><br>
                <strong>TELEFONO: {{$ventas[0]->telefono}} </strong><br>
            </div>
            <div class="Column" id="encabezado">
                
                <strong></strong><br>
                <strong>RUC/CI: {{$ventas[0]->ruc}} </strong><br>
                @if($ventas[0]->tipo_factura == 0)
                    <strong>CONDICION: CONTADO </strong><br>
                @else
                    <strong>CONDICION: CREDITO </strong><br>
                @endif
            </div>
        </div>
        <div style="border: 1px solid #0F0807;" id="alturaDiv">
            <!-- <table class="table-borderless alturatabla> -->
            <table>
            <thead class = "table-light">
                    <tr>
                        <!-- <th class="titulo">TRANS.</th> -->
                        <th id="cant">CANT</th>
                        <!-- <th class="titulo">CUOTA NRO</th> -->
                        <th id="desc">DESCRIPCION</th>
                        <th id="precioU">PRECIO UNIT.</th>
                        <th id="exe">EXENTAS</th>
                        <th id="cinco">GRAVADOS 5%</th>
                        <th id="diez">GRAVADOS 10%</th>
                        
                    </tr>
                </thead>
                @foreach($detalles as $det)
                <tbody>
                        <tr>
                            <td style="text-align:center;" style="width:1%">{{$det->cantidad}}</td>
                            <td id="desc" style="width:40%">{{$det->producto}}</td>                    
                            <td id="exe" style="text-align:center;width:12%">{{number_format(($det->precio), 0, ",", ".")}}</td>
                            <td id="cinco" style="width:50"></td>
                            <td id="cinco" style="width:15%"></td>
                            <td id="diez" style="text-align:center;width:15% ">{{number_format(($det->subtotal), 0, ",", ".")}}</td>
                        </tr>                              
                    
                </tbody>
                @endforeach
                
            </table><br>
            <img src="{{ public_path('assets/images/footerfact.png') }}" width="100%" />
        </div>
        <div id="encabezado">
            <table>
                <tr>
                        <td style="text-align:center;" style="width:1%"></td>
                        <td id="desc" style="width:40%"></td>                    
                        <td colspan=2 id="exe" style="text-align:center;">SUBTOTALES</td>
                        <td id="cinco" style="width:50"></td>
                        <td id="cinco" style="width:50"></td>
                        <td id="diez" style="text-align:center;width:15% ">{{number_format(($ventas[0]->total), 0, ",", ".")}}</td>
                </tr>
                <tr>
                        <td style="text-align:center;" style="width:1%"></td>
                        <td id="desc" style="width:40%"></td>                    
                        <td colspan=2 id="exe" style="text-align:center;">TOTAL A PAGAR</td>
                        <td id="cinco" style="width:50"></td>
                        <td id="cinco" style="width:50"></td>
                        <td id="diez" style="text-align:center;width:15% ">{{number_format(($ventas[0]->total), 0, ",", ".")}}</td>
                            
                </tr>
            </table><br>
        </div>
        <div id="numeroletra">
            <strong></strong>TOTAL GUARANIES:{{$tot_pag_let}}<br>
        </div>
            
        <div id="numeroletra" style="border: 1px solid #0F0807;" class="Row">
            
            <div class="Column" id="encabezado">
                <strong>LIQ. IVA (5%):</strong>0<br>
            </div>
            <div class="Column" id="encabezado"> 
                <strong>LIQ. IVA (10%):</strong> {{number_format(($ventas[0]->total/11), 0, ",", ".")}}<br>
            </div>
            <div class="Column" id="encabezado"> 
                <strong>TOTAL IVA:</strong> {{number_format(($ventas[0]->total/11), 0, ",", ".")}}<br>
            </div>
        </div>
    </div>

</section>
          
</html>
