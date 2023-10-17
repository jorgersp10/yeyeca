<!DOCTYPE>
<html>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="X-UA-Compatible" content="ie=edge">
<title>Reporte de Comisiones por Fechas</title>

<div class="card-body">
    <style>
    #datos {
        text-align: left;
        font-size: 11px;
        font-family: "Times New Roman", Times, serif;
        LINE-HEIGHT: 10px;
        /* font-weight: bold; */
    }

    #letratabla {
        text-align: left;
        font-size: 12px;
        font-family: "Times New Roman", Times, serif;
        LINE-HEIGHT: 8px;
        /* font-weight: bold; */
    }

    #dictamen {
        text-align: left;
        font-size: 18px;
        font-family: "Times New Roman", Times, serif;
        /* font-weight: bold; */
    }

    #titulo {
        text-align: center;
        font-family: "Times New Roman", Times, serif;
        /* font-weight: bold; */
    }

    footer {
        /* background-color: black; */

        LINE-HEIGHT: 5px;
        font-size: 9px;
        bottom: 0;
        width: 100%;
        height: 30px;
        position: fixed;
        /* color: white; */
    }

    hr {
        height: 0, 3px;
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

    .table th,
    .table td {
        padding: 0.40rem;
        vertical-align: top;
        border-top: 1px solid #c2cfd6;
    }

    .table thead th {
        vertical-align: bottom;
        border-bottom: 1px solid #c2cfd6;
    }

    .table-bordered thead th,
    .table-bordered thead td {
        border-bottom-width: 1px;
    }

    .table-bordered th,
    .table-bordered td {
        border: 1px solid #c2cfd6;
    }

    th,
    td {
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

    .izquierda {
        float: left;
    }

    .derecha {
        float: right;
    }

    #hr {
        page-break-after: always;
        border: none;
        margin: 0;
        padding: 0;
    }

    #totales {
        background-color: rgba(255, 255, 0, 1);
        font-size: 12px;
        font-family: "Times New Roman", Times, serif;
        font-weight: bold;
    }

    #totales2 {
        background-color: #FFFFFF;
        font-size: 11px;
        font-family: "Times New Roman", Times, serif;
    }

    #marco {

        page-break-inside: auto;
    }

    body {
        margin: 1cm 1cm 1cm;
    }
    </style>

    <header>
        <h3 id="titulo" class="text-center">Tati e Hijos S.A. Suc. II</h3>
        <h3 id="titulo" class="text-center">Comisiones por rango de Fecha</h3>
        @if(($date1 == null || $date2 == null))
        <h3>Rango de Fecha: Todas las fechas</h3>
        @else
        <h3>Rango de Fecha: {{ date('d-m-Y', strtotime($date1)) }} al {{ date('d-m-Y', strtotime($date2)) }}</h3>
        @endif
        <h3>Vendedor/a: {{$nombreVen}}</h3>
        @if($ventas=="Vacio")
        <h4>No hay Comisiones</h4>
        @else
    </header>


    ***************************************************************************
    <section id="marco">
        <div>
            <table id="letratabla" class="table table-bordered table-striped table-sm">
                @php
                $total_iva=0;
                $total_venta=0;
                $total_comi=0;
                @endphp

                <thead>
                    <tr>
                        <th>Cliente</th>
                        <th>Factura/Ticket</th>
                        <th>Fecha</th>
                        {{-- <th>Total Iva</th> --}}
                        <th>Total Factura</th>
                    </tr>
                </thead>
                @foreach($ventas as $v)
                <tbody>
                    <tr>
                        <td>{{$v->nombre}}</td>
                        @if($v->fact_nro != 0)
                        <td>Fact. {{$v->fact_nro}}</td>
                        @else
                        <td>Ticket. {{$v->nro_recibo}}</td>
                        @endif
                        <td>{{ date('d-m-Y', strtotime($v->fecha)) }}</td>
                        {{-- <td>USD. {{number_format(($v->ivaTotal), 2, ".", ",")}}</td> --}}
                        <td>USD. {{number_format(($v->total), 2, ".", ",")}}</td>
                    </tr>
                    @php
                        $total_iva=$total_iva + $v->ivaTotal;
                        $total_venta=$total_venta + $v->total;
                    @endphp
                </tbody>
                @endforeach
                @php
                    $total_comi = ($total_venta*$porcentaje)/100;
                @endphp
                <tr id="totales">
                    <td>COMISION USD</td>
                    <td>USD. {{number_format(($total_comi), 2, ".", ",")}}</td>
                    <td>TOTALES USD</td>
                    {{-- <td>USD. {{number_format(($total_iva), 2, ".", ",")}}</td> --}}
                    <td>USD. {{number_format(($total_venta), 2, ".", ",")}}</td>
                </tr>
                <tr id="totales">
                    <td>COMISION Gs.</td>
                    <td>Gs. {{number_format(($total_comi * $v->dolVenta), 0, ",", ".")}}</td>
                    <td>TOTALES Gs.</td>
                    {{-- <td>Gs. {{number_format(($total_iva * $v->dolVenta), 0, ",", ".")}}</td> --}}
                    <td>Gs. {{number_format(($total_venta * $v->dolVenta), 0, ",", ".")}}</td>
                </tr>
                <tr id="totales">
                    <td>COMISION $</td>
                    <td>$. {{number_format(($total_comi * ($v->psVenta)), 0, ",", ".")}}</td>
                    <td>TOTALES $</td>
                    {{-- <td>$. {{number_format(($total_iva * ($v->psVenta)), 0, ",", ".")}}</td> --}}
                    <td>$. {{number_format(($total_venta * ($v->psVenta)), 0, ",", ".")}}</td>
                </tr>
                <tr id="totales">
                    <td>COMISION R$</td>
                    <td>R$. {{number_format(($total_comi * ($v->rsVenta)), 2, ",", ".")}}</td>
                    <td>TOTALES R$</td>
                    {{-- <td>R$. {{number_format(($total_iva * ($v->rsVenta)), 0, ",", ".")}}</td> --}}
                    <td>R$. {{number_format(($total_venta * ($v->rsVenta)), 2, ",", ".")}}</td>
                </tr>
            </table>

        </div>
    </section>
    @endif
    <footer>
        <hr>
        <p><b>SistemaControl - </b> <b>Usuario:</b> {{auth()->user()->name}}</p>
        <p><b>{{date('d-m-Y H:i:s')}}</b></p>
    </footer>

</html>