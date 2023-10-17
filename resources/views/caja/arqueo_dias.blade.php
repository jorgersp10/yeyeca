<!DOCTYPE>
<html>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Reporte por Fechas</title>
    
    <div class="card-body">
    <style> 
        #datos{
        text-align: left;
        font-size: 11px;
        font-family: "Times New Roman", Times, serif;
        LINE-HEIGHT:10px;
        /* font-weight: bold; */
        }
        #letratabla{
        text-align: left;
        font-size: 12px;
        font-family: "Times New Roman", Times, serif;
        LINE-HEIGHT:8px;
        /* font-weight: bold; */
        }
        #dictamen{
        text-align: left;
        font-size: 18px;
        font-family: "Times New Roman", Times, serif;
        /* font-weight: bold; */
        }
        #titulo{
        text-align: center;
        font-family: "Times New Roman", Times, serif;
        /* font-weight: bold; */
        }
        footer {
            /* background-color: black; */
            
            LINE-HEIGHT:5px;
            font-size: 9px;
            bottom: 0;
            width: 100%;
            height: 30px;
            position: fixed;
            /* color: white; */
            }
            hr {
            height: 0,3px;
            background-color: black;
            }
            .table {
            display: table;
            width: 100%;
            max-width: 100%;
            margin-bottom: 1rem;
            background-color: transparent;
            border-collapse: collapse;
            font-size: 12px;
            font-family: "Times New Roman", Times, serif;
        }
        .table-bordered {
            border: 1px solid #c2cfd6;
        }
        thead {
            display: table-header-group;
            vertical-align: middle;
            border-color: inherit;
        }
        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }
        .table th, .table td {
            padding: 0.40rem;
            vertical-align: top;
            border-top: 1px solid #c2cfd6;
        }
        .table thead th {
            vertical-align: bottom;
            border-bottom: 1px solid #c2cfd6;
        }
        .table-bordered thead th, .table-bordered thead td {
            border-bottom-width: 1px;
        }
        .table-bordered th, .table-bordered td {
            border: 1px solid #c2cfd6;
        }
        th, td {
            display: table-cell;
            vertical-align: inherit;
        }
        th {
            font-weight: bold;
            text-align: -internal-center;
            text-align: left;
        }
        tbody {
            display: table-row-group;
            vertical-align: middle;
            border-color: inherit;
        }
        tr {
            display: table-row;
            vertical-align: inherit;
            border-color: inherit;
        }
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }
        .izquierda{
            float:left;
        }
        .derecha{
            float:right;
        }

        #hr{
		page-break-after: always;
		border: none;
		margin: 0;
		padding: 0;
	    }
        #totales{		
            background-color: rgba(255,255,0,1);
            font-size: 12px;
            font-family: "Times New Roman", Times, serif;
            font-weight: bold;
        }
        #totales2{		
            background-color: #FFFFFF;
            font-size: 11px;
            font-family: "Times New Roman", Times, serif;
        }
        #marco{
		
            page-break-inside: auto;
	    }
        body {
            margin: 1cm 1cm 1cm;
        }
    </style>
   
    <header>
        <h3 id="titulo" class="text-center">Tati e Hijos S.A. Suc. II</h3>
        <h3 id="titulo" class="text-center">Arqueo del dia: {{ date('d-m-Y', strtotime($fecha_arqueo)) }} - Cajero: {{ $cajeroNombre }}</h3>   
    </header>
    <section id="marco">
        <div>
            <table id="letratabla" class="table table-bordered table-striped table-sm">  
           <thead>                               
                <tr>
                    {{-- <th>Fecha</th> --}}
                    <th>Cliente</th>
                    <th>Recibo/Fact.</th>
                    <th>Importe</th>
                    <th>Efectivo</th>
                    <th>Transf</th>
                    <th>Cheque</th>
                    <th>T. Debito</th>
                    <th>T. Crédito</th>
                </tr>    
            </thead>
            
            <tbody>
            @php
                $totaldia = 0;
                $totalefe = 0;
                $totaltran = 0;
                $totalche = 0;
                $totaltd = 0;
                $totaltc = 0;                   
            @endphp
            @if($arqueo=="Vacio")
                <tr><td><h3>NO HUBO COBROS</h3></td></tr>
                </tbody>
                @else
                    @foreach($arqueo as $ar)
                        <tr>                                    
                            {{-- <td>{{ date('d-m-Y', strtotime($ar->fechapago)) }}</td> --}}
                            <td>{{$ar->cliente}}</td>
                            <td>{{$ar->producto}}</td>
                            <td>USD {{number_format(($ar->importe), 2, ".", ",")}} </td>  
                            <td>USD {{number_format($ar->total_pagf, 2, ".", ",")}}</td>
                            <td>USD {{number_format($ar->total_pagtr, 2, ".", ",")}}</td>
                            <td>USD {{number_format($ar->total_pagch, 2, ".", ",")}}</td>
                            <td>USD {{number_format($ar->total_pagtd, 2, ".", ",")}}</td>
                            <td>USD {{number_format($ar->total_pagtc, 2, ".", ",")}}</td>                          
                        </tr>  
                        @php
                            $totaldia=$totaldia + $ar->importe;  
                            $totalefe=$totalefe + $ar->total_pagf; 
                            $totaltran=$totaltran + $ar->total_pagtr; 
                            $totalche=$totalche + $ar->total_pagch; 
                            $totaltd=$totaltd + $ar->total_pagtd; 
                            $totaltc=$totaltc + $ar->total_pagtc;                                                       
                        @endphp
                @endforeach
                <tr id="totales">  
                    <td></td>     
                    <td>Total USD</td>                                                 
                    <td>USD. {{number_format(($totaldia), 2, ".", ",")}}</td>  
                    <td>USD. {{number_format(($totalefe), 2, ".", ",")}}</td> 
                    <td>USD. {{number_format(($totaltran), 2, ".", ",")}}</td> 
                    <td>USD. {{number_format(($totalche), 2, ".", ",")}}</td> 
                    <td>USD. {{number_format(($totaltd), 2, ".", ",")}}</td> 
                    <td>USD. {{number_format(($totaltc), 2, ".", ",")}}</td>                                               
                </tr>
                <tr id="totales">  
                    <td></td>     
                    <td>Total Gs.</td>                                                 
                    <td>Gs. {{number_format(($totaldia * $ar->dolVenta), 0, ",", ".")}}</td>  
                    <td>Gs. {{number_format(($totalefe * $ar->dolVenta), 0, ",", ".")}}</td> 
                    <td>Gs. {{number_format(($totaltran * $ar->dolVenta), 0, ",", ".")}}</td> 
                    <td>Gs. {{number_format(($totalche * $ar->dolVenta), 0, ",", ".")}}</td> 
                    <td>Gs. {{number_format(($totaltd * $ar->dolVenta), 0, ",", ".")}}</td> 
                    <td>Gs. {{number_format(($totaltc * $ar->dolVenta), 0, ",", ".")}}</td>                                               
                </tr>
                <tr id="totales">  
                    <td></td>     
                    <td>Total $</td>                                                 
                    <td>$. {{number_format(($totaldia * ($ar->psVenta)), 0, ",", ".")}}</td>  
                    <td>$. {{number_format(($totalefe * ($ar->psVenta)), 0, ",", ".")}}</td> 
                    <td>$. {{number_format(($totaltran * ($ar->psVenta)), 0, ",", ".")}}</td> 
                    <td>$. {{number_format(($totalche * ($ar->psVenta)), 0, ",", ".")}}</td> 
                    <td>$. {{number_format(($totaltd * ($ar->psVenta)), 0, ",", ".")}}</td> 
                    <td>$. {{number_format(($totaltc * ($ar->psVenta)), 0, ",", ".")}}</td>                                               
                </tr>
                <tr id="totales">  
                    <td></td>     
                    <td>Total R$</td>                                                 
                    <td>R$. {{number_format(($totaldia * ($ar->rsVenta)), 2, ",", ".")}}</td>  
                    <td>R$. {{number_format(($totalefe * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                    <td>R$. {{number_format(($totaltran * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                    <td>R$. {{number_format(($totalche * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                    <td>R$. {{number_format(($totaltd * ($ar->rsVenta)), 2, ",", ".")}}</td> 
                    <td>R$. {{number_format(($totaltc * ($ar->rsVenta)), 2, ",", ".")}}</td>                                               
                </tr>
            </tbody>
            @endif
        </table>
        </div>
    </section>  
  <footer>
    <hr>
    <p><b>SistemaControl</b> <b>Usuario:</b> {{auth()->user()->name}}</p>
    <p><b>{{date('d-m-Y H:i:s')}}</b></p>
  </footer>
</html>