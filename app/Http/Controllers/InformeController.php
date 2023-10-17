<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Vendedor;
use Carbon\Carbon;
use DateTime;
use DB;
use Illuminate\Http\Request;

class InformeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //SQL PARA HALLAR EL PRECIO TOTAL DE CADA INMUEBLE

        DB::select("CALL sp_update_pagos()");

        $cuotas = DB::table('cuotas as c')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->join('clientes as cli', 'c.cliente_id', '=', 'cli.id')
            ->select('v.fact_nro', 'cli.nombre as cliente'
                , 'c.total_cuo as deuda', 'pagos_cuo as pagos', 'saldo_cuo',
                'saldo_ven', 'pagos_ven', 'cli.id as cliente_id')
            ->where('c.saldo_cuo', '>', 0)
            ->orderby('cli.nombre')
            ->simplePaginate(5);
        //dd($cuotas);

        $fecha_hoy = Carbon::now('America/Asuncion');
        $cuotasatrasadas = DB::table('cuotas_det as cdet')
            ->join('cuotas as c', 'cdet.cuota_id', '=', 'c.id')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->select('v.fact_nro as inmueble_id',
                DB::raw('count(cdet.cuota_nro) as cuotasAt'))
            ->where('cdet.fec_vto', '<', $fecha_hoy)
            ->where('cdet.estado_cuota', '=', 'P')
            ->where('c.saldo_cuo', '>', 0)
            ->groupby('v.fact_nro')
            ->get();

        return view('informe.index', ["cuotas" => $cuotas, "cuotasatrasadas" => $cuotasatrasadas]);

        //return view('informe.index',["cuotas"=>$cuotas, "pagos"=>$pagos, "cantCuotas"=>$cantCuotas]);
    }

    public function informe_fec_prev()
    {

        return view('informe.fec_prev');
    }

    public function informe_fec_prev_urb()
    {

        return view('informe.fec_prev_urb');
    }

    public function informe_fec(Request $request)
    {
        //SQL PARA HALLAR EL PRECIO TOTAdate_format()L DE CADA INMUEBLE
        //$fecha_var= date('m-d-Y','10-31-2021');

        if ($request->fec_cal == null) {$fecha_var = date('Y-m-d');} else { $fecha_var = $request->fec_cal;}

        $fecha = DateTime::createFromFormat('Y-m-d', $fecha_var); //(1) aquí se pone el formato que tiene el dato original
        $fec = $fecha->format('Y-m-d'); //(2) aquí el formato final que quieres

        DB::select('CALL sp_update_pagos_fec ("' . $fec . '")');

        //dd($fecha_var);
        $cuotas = DB::table('cuotas as c')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->join('clientes as cli', 'c.cliente_id', '=', 'cli.id')
            ->select('v.fact_nro as inmueble_id', 'cli.nombre as cliente'
                , 'c.total_cuo as deuda', 'pagos_cuo as pagos', 'saldo_cuo',
                'saldo_ven', 'pagos_ven')
            ->where('c.saldo_cuo', '>', 0)
            ->orderby('cli.nombre')
            ->simplePaginate(5);
        //dd($cuotas);

        $fecha_hoy = Carbon::now('America/Asuncion');
        $cuotasatrasadas = DB::table('cuotas_det as cdet')
            ->join('cuotas as c', 'cdet.cuota_id', '=', 'c.id')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->select('v.fact_nro as inmueble_id',
                DB::raw('count(cdet.cuota_nro) as cuotasAt'))
            ->where('cdet.fec_vto', '<', $fecha_hoy)
            ->where('cdet.estado_cuota', '=', 'P')
            ->where('c.saldo_cuo', '>', 0)
            ->groupby('v.fact_nro')
            ->get();

        return view('informe.index', ["cuotas" => $cuotas, "cuotasatrasadas" => $cuotasatrasadas]);

        //return view('informe.index',["cuotas"=>$cuotas, "pagos"=>$pagos, "cantCuotas"=>$cantCuotas]);
    }
    //BUSCADOR DE CLIENTE
    public function getClienteInforme(Request $request)
    {
        //console.log("llego aca");
        $search = $request->search;

        if ($search == '') {
            $clientes = Cliente::orderby('nombre', 'asc')
                ->select('id', 'nombre')
                ->limit(10)
                ->get();
        } else {
            $search = str_replace(" ", "%", $search);
            $clientes = Cliente::orderby('nombre', 'asc')
                ->select('id', 'nombre', 'apellido', 'num_documento')
                ->where('nombre', 'like', '%' . $search . '%')
            //->orWhere('apellido','like','%'.$search.'%')
                ->orWhere('num_documento', 'like', '%' . $search . '%')
                ->limit(10)
                ->get();
        }

        $response = array();

        foreach ($clientes as $cliente) {
            $response[] = array(
                'id' => $cliente->id,
                'text' => $cliente->nombre . " - " . $cliente->num_documento,
            );
        }
        return response()->json($response);
    }

    //BUSCADOR DE CLIENTE
    public function getProInforme(Request $request)
    {
        //console.log("llego aca");
        $search = $request->search;

        if ($search == '') {
            $productos = Producto::orderby('descripcion', 'asc')
                ->select('id', 'descripcion', 'ArtCode')
                ->limit(10)
                ->get();
        } else {
            $search = str_replace(" ", "%", $search);
            $productos = Producto::orderby('descripcion', 'asc')
                ->select('id', 'descripcion', 'ArtCode')
                ->where('descripcion', 'like', '%' . $search . '%')
            //->orWhere('apellido','like','%'.$search.'%')
                ->orWhere('ArtCode', 'like', '%' . $search . '%')
                ->limit(10)
                ->get();
        }

        $response = array();

        foreach ($productos as $pro) {
            $response[] = array(
                'id' => $pro->id,
                'text' => $pro->descripcion . " - " . $pro->ArtCode,
            );
        }
        return response()->json($response);
    }

    public function pdfResumen()
    {
        DB::select("CALL sp_update_pagos()");

        $cuotas_gs = DB::table('cuotas as c')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->join('clientes as cli', 'c.cliente_id', '=', 'cli.id')
            ->select('v.fact_nro as inmueble_id', 'cli.nombre as cliente'
                , 'c.total_cuo as deuda', 'pagos_cuo as pagos', 'saldo_cuo',
                'saldo_ven', 'pagos_ven')
            ->where('c.saldo_cuo', '>', 0)
            ->orderby('cli.nombre')
            ->get();
        //dd($cuotas);

        $fecha_hoy = Carbon::now('America/Asuncion');
        $cuotasatrasadas_gs = DB::table('cuotas_det as cdet')
            ->join('cuotas as c', 'cdet.cuota_id', '=', 'c.id')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->select('v.fact_nro as inmueble_id',
                DB::raw('count(cdet.cuota_nro) as cuotasAt'))
            ->where('cdet.fec_vto', '<', $fecha_hoy)
            ->where('cdet.estado_cuota', '=', 'P')
            ->where('c.saldo_cuo', '>', 0)
            ->groupby('v.fact_nro')
            ->get();

        $cuotas_us = DB::table('cuotas as c')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->join('clientes as cli', 'c.cliente_id', '=', 'cli.id')
            ->select('v.fact_nro inmueble_id', 'cli.nombre as cliente'
                , 'c.total_cuo as deuda', 'pagos_cuo as pagos', 'saldo_cuo',
                'saldo_ven', 'pagos_ven')
            ->where('c.saldo_cuo', '>', 0)
            ->orderby('cli.nombre')
            ->get();
        //dd($cuotas);

        $fecha_hoy = Carbon::now('America/Asuncion');
        $cuotasatrasadas_us = DB::table('cuotas_det as cdet')
            ->join('cuotas as c', 'cdet.cuota_id', '=', 'c.id')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->select('v.fact_nro as inmueble_id',
                DB::raw('count(cdet.cuota_nro) as cuotasAt'))
            ->where('cdet.fec_vto', '<', $fecha_hoy)
            ->where('cdet.estado_cuota', '=', 'P')
            ->where('c.saldo_cuo', '>', 0)
            ->groupby('v-fact_nro')
            ->get();

        //return view('informe.resumenInmuebles',["cuotas"=>$cuotas,"cuotasatrasadas"=>$cuotasatrasadas]);
        //dd("Lega hasta aca");
        return $pdf = \PDF::loadView('informe.resumenInmuebles', ["cuotas_gs" => $cuotas_gs, "cuotasatrasadas_gs" => $cuotasatrasadas_gs,
            "cuotas_us" => $cuotas_us, "cuotasatrasadas_us" => $cuotasatrasadas_us])
            ->setPaper('a4', 'landscape')
            ->stream('ResumenInmuebles.pdf');

        //return $pdf = \PDF::loadView('informe.resumenInmuebles', compact('cuotas','cuotasatrasadas'));
        //return view('informe.index',["cuotas"=>$cuotas, "pagos"=>$pagos, "cantCuotas"=>$cantCuotas]);
    }

    public function cajeros($id)
    {
        //dd($resquest);

        if (isset($id)) {
            if ($id == 0) {
                //$cajeros = User::all();
                $cajeros = DB::table('cajeros as c')
                    ->join('users', 'users.id', '=', 'c.user_id')
                    ->join('roles', 'roles.id', '=', 'users.idrol')
                    ->join('sucursales', 'users.idsucursal', '=', 'sucursales.id')
                    ->select('users.id', 'users.name', 'users.email', 'users.num_documento', 'users.direccion',
                        'users.telefono', 'users.condicion', 'users.password', 'users.dob as fecha_nacimiento', 'users.avatar'
                        , 'roles.nombre as rol', 'sucursales.sucursal as sucursal', 'roles.id as idrol', 'sucursales.id as idsucursal', 'c.caja_nro', 'c.user_id', 'c.id')
                //->where('users.name','LIKE','%'.$sql.'%')
                //->orwhere('users.num_documento','LIKE','%'.$sql.'%')
                    ->orderBy('sucursales.id', 'asc')
                    ->get();
            } else {
                //$cajeros = User::whereidsucursal($id)->get();
                $cajeros = DB::table('cajeros as c')
                    ->join('users', 'users.id', '=', 'c.user_id')
                    ->join('roles', 'roles.id', '=', 'users.idrol')
                    ->join('sucursales', 'users.idsucursal', '=', 'sucursales.id')
                    ->select('users.id', 'users.name', 'users.email', 'users.num_documento', 'users.direccion',
                        'users.telefono', 'users.condicion', 'users.password', 'users.dob as fecha_nacimiento', 'users.avatar'
                        , 'roles.nombre as rol', 'sucursales.sucursal as sucursal', 'roles.id as idrol', 'sucursales.id as idsucursal', 'c.caja_nro', 'c.user_id', 'c.id')
                    ->where('users.idsucursal', '=', $id)
                //->orwhere('users.num_documento','LIKE','%'.$sql.'%')
                    ->orderBy('sucursales.id', 'asc')
                    ->get();
            }

            //dd($cajeros);
            return response()->json(
                [
                    'lista' => $cajeros,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    public function clientes($id)
    {
        //dd($resquest);

        if (isset($id)) {
            if ($id == 0) {
                //$cajeros = User::all();
                $clientes = Inmueble::join('cuotas', 'inmuebles.id', '=', 'cuotas.inmueble_id')
                    ->join('cuotas_det', 'cuotas_det.cuota_id', '=', 'cuotas.id')
                    ->join('loteamientos', 'loteamientos.id', '=', 'inmuebles.loteamiento_id')
                    ->join('clientes', 'clientes.id', '=', 'cuotas.cliente_id')
                    ->join('estado_inm', 'inmuebles.estado', '=', 'estado_inm.id')
                    ->select('clientes.id', 'clientes.nombre', 'clientes.num_documento')
                    ->groupBy('clientes.id', 'clientes.nombre', 'clientes.num_documento')
                    ->get();
            } else {
                //$cajeros = User::whereidsucursal($id)->get();
                $clientes = Inmueble::join('cuotas', 'inmuebles.id', '=', 'cuotas.inmueble_id')
                    ->join('cuotas_det', 'cuotas_det.cuota_id', '=', 'cuotas.id')
                    ->join('loteamientos', 'loteamientos.id', '=', 'inmuebles.loteamiento_id')
                    ->join('clientes', 'clientes.id', '=', 'cuotas.cliente_id')
                    ->join('estado_inm', 'inmuebles.estado', '=', 'estado_inm.id')
                    ->select('clientes.id', 'clientes.nombre', 'clientes.num_documento')
                    ->where('loteamientos.id', '=', $id)
                    ->groupBy('clientes.id', 'clientes.nombre', 'clientes.num_documento')
                    ->get();
            }

            //dd($cajeros);
            return response()->json(
                [
                    'lista' => $clientes,
                ]
            );
        } else {
            return response()->json(
                [
                    'success' => false,
                ]
            );
        }
    }

    public function reporteDetalle()
    {

        $vendedores=DB::table('vendedores as v')
        ->select('v.id','v.nombre','v.num_documento')
        ->orderBy('v.nombre','asc')
        ->get();

        return view('informe.reporteDetalle',["vendedores"=>$vendedores]);

    }

    public function reporteDetallePDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;
        $cliente = $request->cliente_id;
        //$producto=$request->producto_id;
        //dd($request);
        //Consulta de Inmuebles

        $pagos = DB::table('pagos as p')
            ->join('cuotas as c', 'p.cuota_id', '=', 'c.id')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->join('clientes as cli', 'c.cliente_id', '=', 'cli.id')
            ->join('users as u', 'u.id', '=', 'p.usuario_id')
            ->select('v.fact_nro as inmueble_id', 'u.name', 'p.fec_pag as fechapago', 'p.cuota as cuota_nro', 'p.cuota_id as cuota_id',
                'p.cuota', 'p.capital as capital', 'p.moratorio', 'p.punitorio', 'p.iva', 'p.total_pag as totalpagado',
                'p.fec_vto', 'u.id as user_id', 'cli.nombre as nombreCliente', 'cli.num_documento', 'total_pagch',
                'total_pagtd', 'total_pagtc', 'total_pagtr', 'total_pagf', 'v.fact_nro', 'v.total as total_fact');

        //VERIFICA SI TRAE CLIENTE
        if (empty($request->cliente_id)) {
            $cliente = null;
        } else {
            $cliente = $request->cliente_id;
            $pagos = $pagos->where('c.cliente_id', '=', $request->cliente_id);

        }
        //dd($facturas);
       
        if ($date1 == null && $date2 == null) {
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            //
            $pagos = $pagos->orderBy('v.fact_nro', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $pagos = $pagos->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $pagos = $pagos->whereBetween('p.fec_pag', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $pagos = $pagos->orderBy('v.fact_nro', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $pagos = $pagos->get();

        }

        if ($pagos->isEmpty()) {
            $pagos = "Vacio";
        }
        //dd($pagos);
        $moneda = "GS";
        return $pdf = \PDF::loadView('informe.reporteDetallePDF', ["date1" => $date1, "date2" => $date2,
            "moneda" => $moneda, "cliente" => $cliente, "pagos" => $pagos])
            ->setPaper('a4', 'landscape')
            ->stream('reporteDetallePDF.pdf');
    }

    public function reporteComisionPDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;
        $vendedor_id = $request->vendedor_id;
        //$producto=$request->producto_id;
        //dd($request);
        //Consulta de Inmuebles

        $ventas = DB::table('ventas as v')
        ->join('cotizaciones as cot','cot.id','=','v.cotiz_id')
        ->join('clientes as c', 'c.id', '=', 'v.cliente_id')
        ->join('vendedores as ven', 'ven.id', '=', 'v.vendedor_id')
        ->select('v.id', 'v.fact_nro','v.nro_recibo', 'v.iva5', 'v.iva10', 'v.ivaTotal', 'v.exenta', 'v.fecha',
        'v.total', 'v.estado', 'c.nombre','cot.dolVenta','cot.psVenta','cot.rsVenta')
        ->where('v.estado', '=', "0");

        if ($date1 != null && $date2 != null) 
        {
            $ventas = $ventas->whereBetween('v.fecha', [($date1), ($date2)]);
        } 

        if($vendedor_id != "0")
        {
            $ventas = $ventas->where('v.vendedor_id','=',$vendedor_id);
        }
        $ventas = $ventas->orderBy('v.id', 'asc');
        $ventas = $ventas->get();

        if($vendedor_id != "0")
        {
            $nombreVen = Vendedor::findOrFail($vendedor_id);            
            $nombreVen = $nombreVen->nombre;

            $porcentaje = Vendedor::findOrFail($vendedor_id);            
            $porcentaje = $porcentaje->porcentaje;
        }
        else
        {
            $nombreVen = "TODOS/AS";
            $porcentaje = 2;
        }

        if ($ventas->isEmpty()) {
            $ventas = "Vacio";
        }
        //dd($ventas);
        $moneda = "GS";
        return $pdf = \PDF::loadView('informe.reporteComisionPDF', ["date1" => $date1, 
        "date2" => $date2,"nombreVen" => $nombreVen,"porcentaje" => $porcentaje,
            "ventas" => $ventas])
            ->setPaper('a4', 'portrait')
            ->stream('reporteComisionPDF.pdf');
    }

    public function reporteVentaPDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;
        //$producto=$request->producto_id;
        //dd($request);
        //Consulta de Inmuebles

        $ventas = DB::table('ventas as v')
        ->join('cotizaciones as cot','cot.id','=','v.cotiz_id')
        ->join('clientes as c', 'c.id', '=', 'v.cliente_id')
        ->select('v.id', 'v.fact_nro','v.nro_recibo', 'v.iva5', 'v.iva10', 'v.ivaTotal', 'v.exenta', 'v.fecha',
        'v.total', 'v.estado', 'c.nombre','cot.dolVenta','cot.psVenta','cot.rsVenta')
        ->where('v.estado', '=', "0");

        if ($date1 == null && $date2 == null) {

            $ventas = $ventas->orderBy('v.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $ventas = $ventas->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $ventas = $ventas->whereBetween('v.fecha', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $ventas = $ventas->orderBy('v.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $ventas = $ventas->get();

        }

        if ($ventas->isEmpty()) {
            $ventas = "Vacio";
        }
        //dd($ventas);
        $moneda = "GS";
        return $pdf = \PDF::loadView('informe.reporteVentaPDF', ["date1" => $date1, "date2" => $date2,
            "ventas" => $ventas])
            ->setPaper('a4', 'portrait')
            ->stream('reporteDetallePDF.pdf');
    }

    public function reporteCompraPDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;

        $ventas = DB::table('ventas as v')
        ->join('cotizaciones as cot','v.cotiz_id','=','v.cotiz_id')
        ->join('clientes as c', 'c.id', '=', 'v.cliente_id')
        ->select('v.id', 'v.fact_nro','v.nro_recibo', 'v.iva5', 'v.iva10', 'v.ivaTotal', 'v.exenta', 'v.fecha',
        'v.total', 'v.estado', 'c.nombre','cot.dolVenta','cot.psVenta','cot.rsVenta')
        ->where('v.estado', '=', "0");

        $compras = DB::table('compras as com')
        ->join('cotizaciones as cot','cot.id','=','com.cotiz_id')
        ->join('proveedores as p', 'p.id', '=', 'com.proveedor_id')
        ->select('com.id', 'com.fact_compra', 'com.iva', 'com.fecha',
            'com.total', 'com.estado', 'p.nombre','cot.dolVenta','cot.psVenta','cot.rsVenta')
        ->where('com.estado', '=', "0");

        if ($date1 == null && $date2 == null) {

            $compras = $compras->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $compras = $compras->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $compras = $compras->whereBetween('com.fecha', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $compras = $compras->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $compras = $compras->get();

        }

        if ($compras->isEmpty()) {
            $compras = "Vacio";
        }

        //////////////////////////////////////////////////////////////////////////

        $gastos = DB::table('gastos as com')
        ->join('proveedores as p', 'p.id', '=', 'com.proveedor_id')
        ->select('com.id', 'com.fact_compra', 'com.iva', 'com.fecha',
            'com.total', 'com.estado', 'p.nombre')
        ->where('com.estado', '=', "0");

        if ($date1 == null && $date2 == null) {

            $gastos = $gastos->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $gastos = $gastos->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $gastos = $gastos->whereBetween('com.fecha', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $gastos = $gastos->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $gastos = $gastos->get();

        }

        if ($gastos->isEmpty()) {
            $gastos = "Vacio";
        }

        //PAGO DE SALARIOS
        $salarios = DB::table('recibo_funcionarios as s')
        ->join('funcionarios as f', 'f.id', '=', 's.funcionario_id')
        ->select('s.id', 's.nro_recibo', 's.salario_cobrar', 's.fecha_recibo',
        'f.nombre');
        //->where('com.estado', '=', "0");

        if ($date1 == null && $date2 == null) {

            $salarios = $salarios->orderBy('s.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $salarios = $salarios->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $salarios = $salarios->whereBetween('s.fecha_recibo', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $salarios = $salarios->orderBy('s.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $salarios = $salarios->get();

        }

        if ($salarios->isEmpty()) {
            $salarios = "Vacio";
        }
        //dd($salarios);
        $moneda = "GS";
        return $pdf = \PDF::loadView('informe.reporteCompraPDF', ["date1" => $date1, "date2" => $date2,
            "compras" => $compras,"gastos" => $gastos,"salarios" => $salarios])
            ->setPaper('a4', 'portrait')
            ->stream('reporteDetallePDF.pdf');
    }

    public function reporteProductoPDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;
        //$producto=$request->producto_id;
        //dd($request);
        //Consulta de Inmuebles
        if (($date1 == null) || ($date2 == null)) {
            $ventas = DB::table('ventas as v')
                ->join('ventas_det as vdet', 'v.id', '=', 'vdet.venta_id')
                ->join('productos as p', 'p.id', '=', 'vdet.producto_id')
                ->select('p.cod_barra', 'p.descripcion', DB::raw('sum(vdet.cantidad) as total'))
                ->where('v.estado', '=', "0")
                ->groupBy('p.cod_barra', 'p.descripcion')
                ->orderBy('total', 'desc')
                ->get();

        } else {
            $ventas = DB::table('ventas as v')
                ->join('ventas_det as vdet', 'v.id', '=', 'vdet.venta_id')
                ->join('productos as p', 'p.id', '=', 'vdet.producto_id')
                ->select('p.cod_barra', 'p.descripcion', DB::raw('sum(vdet.cantidad) as total'))
                ->where('v.estado', '=', "0")
                ->whereBetween('v.fecha', [$date1, $date2])
                ->groupBy('p.cod_barra', 'p.descripcion')
                ->orderBy('total', 'desc')
                ->get();

        }

        //dd($ventas);
        if ($ventas->count() < 0) {
            $ventas = "Vacio";
        }

        return $pdf = \PDF::loadView('informe.reporteProductoPDF', ["date1" => $date1, "date2" => $date2,
            "ventas" => $ventas])
            ->setPaper('a4', 'portrait')
            ->stream('reporteProductoPDF.pdf');
    }

    public function reporteProductoGSPDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;

        if (($date1 == null) || ($date2 == null)) {
            $ventas = DB::table('ventas as v')
            ->join('cotizaciones as cot','cot.id','=','v.cotiz_id')
            ->join('ventas_det as vdet', 'v.id', '=', 'vdet.venta_id')
            ->join('productos as p', 'p.id', '=', 'vdet.producto_id')
            ->select('p.cod_barra', 'p.descripcion','cot.dolVenta','cot.psVenta','cot.rsVenta', 
            DB::raw('sum(vdet.cantidad) as cantidad'), 
            DB::raw('sum(vdet.cantidad * vdet.precio) as total'))
            ->where('v.estado', '=', "0")
            ->groupBy('p.cod_barra', 'p.descripcion','cot.dolVenta','cot.psVenta','cot.rsVenta')
            ->orderBy('total', 'desc')
            ->get();

        } else {
            $ventas = DB::table('ventas as v')
            ->join('cotizaciones as cot','cot.id','=','v.cotiz_id')
            ->join('ventas_det as vdet', 'v.id', '=', 'vdet.venta_id')
            ->join('productos as p', 'p.id', '=', 'vdet.producto_id')
            ->select('p.cod_barra', 'p.descripcion','cot.dolVenta','cot.psVenta','cot.rsVenta',
            DB::raw('sum(vdet.cantidad) as cantidad'), 
            DB::raw('sum(vdet.cantidad * vdet.precio) as total'))                
            ->where('v.estado', '=', "0")
            ->whereBetween('v.fecha', [$date1, $date2])
            ->groupBy('p.cod_barra', 'p.descripcion','cot.dolVenta','cot.psVenta','cot.rsVenta')
            ->orderBy('total', 'desc')
            ->get();

        }

        //dd($ventas);
        if ($ventas->count() < 0) {
            $ventas = "Vacio";
        }

        return $pdf = \PDF::loadView('informe.reporteProductoGSPDF', ["date1" => $date1, "date2" => $date2,
            "ventas" => $ventas])
            ->setPaper('a4', 'portrait')
            ->stream('reporteProductoGSPDF.pdf');
    }

    public function reporteCreditoPDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;
        $tipo = $request->tipo_fac;

        $cuotas = DB::table('cuotas as c')
            ->join('cuotas_det as cdet','cdet.cuota_id','=','c.id')
            ->join('ventas as v', 'c.factura_id', '=', 'v.id')
            ->join('clientes as cli', 'cli.id', '=', 'c.cliente_id')
            ->leftjoin('pagos as p','cdet.cuota_id','=','p.cuota_id')
            ->select('c.id', 'c.factura','cdet.fec_vto','cdet.total_cuota', 'cdet.estado_cuota', 'cli.nombre',
             DB::raw('sum(total_cuota) as total'),
             DB::raw('sum(p.capital) as total_pag'))
            ->where('cdet.estado_cuota', '=', "P");

        if ($date1 == null && $date2 == null) {

            if($tipo==2){
                $cuotas = $cuotas->groupby('c.id', 'c.factura','cdet.fec_vto','cdet.total_cuota', 'cdet.estado_cuota', 'cli.nombre');
                $cuotas = $cuotas->orderBy('c.id', 'desc');
                $cuotas = $cuotas->get();
            }
            else{
                $cuotas = $cuotas->where('v.tipo_factura', '=', $tipo);
                $cuotas = $cuotas->groupby('c.id', 'c.factura','cdet.fec_vto','cdet.total_cuota', 'cdet.estado_cuota', 'cli.nombre');
                $cuotas = $cuotas->orderBy('c.id', 'desc');
                $cuotas = $cuotas->get();
            }

        } else {  
            if ($tipo==2){     
                $cuotas = $cuotas->whereBetween('cdet.fec_vto', [($date1), ($date2)]);
                $cuotas = $cuotas->groupby('c.id', 'c.factura','cdet.fec_vto','cdet.total_cuota', 'cdet.estado_cuota', 'cli.nombre');
                $cuotas = $cuotas->orderBy('c.id', 'desc');
                $cuotas = $cuotas->get();
            }
            else{
                $cuotas = $cuotas->where('v.tipo_factura', '=', $tipo);  
                $cuotas = $cuotas->whereBetween('cdet.fec_vto', [($date1), ($date2)]);
                $cuotas = $cuotas->groupby('c.id', 'c.factura','cdet.fec_vto','cdet.total_cuota', 'cdet.estado_cuota', 'cli.nombre');
                $cuotas = $cuotas->orderBy('c.id', 'desc');
                $cuotas = $cuotas->get();
            }
            
        }

        //dd($cuotas);
        if ($cuotas->isEmpty()) {
            $cuotas = "Vacio";
        }
        if($tipo==2){
            $titulo_fac="Todas las facturas";
        }if($tipo==1){
            $titulo_fac="Crédito";
        }if($tipo==0){
            $titulo_fac="Contado";
        }

        //dd($pagos);
        $moneda = "GS";
        return $pdf = \PDF::loadView('informe.reporteCreditoPDF', ["date1" => $date1, "date2" => $date2,
            "cuotas" => $cuotas,"titulo_fac"=>$titulo_fac])
            ->setPaper('a4', 'landscape')
            ->stream('reporteDetallePDF.pdf');
    }

    public function calculoMensual(Request $request)
    {
        return view('informe.calculos');
    }

    public function controlStock(Request $request)
    {
        $productos=DB::table('productos as p')
        ->select('*')
        ->get();

        for($i = 0; $i < sizeof($productos); $i++)
        {
            $compras = DB::table('compras as c')
            ->join('compras_det as cdet','c.id','=','cdet.compra_id')
            ->select(DB::raw('SUM(cdet.cantidad) as cantidad'))
            ->where('c.estado','=',0)
            ->where('c.fecha','>=',$productos[$i]->fecha_stock)
            ->where('cdet.producto_id','=',$productos[$i]->id)
            ->get();
            
            $compras = isset($compras[0]->cantidad) ? $compras[0]->cantidad : 0;

            $ventas = DB::table('ventas as c')
            ->join('ventas_det as cdet','c.id','=','cdet.venta_id')
            ->select(DB::raw('SUM(cdet.cantidad) as cantidad'))
            ->where('c.estado','=',0)
            ->where('c.fecha','>=',$productos[$i]->fecha_stock)
            ->where('cdet.producto_id','=',$productos[$i]->id)
            ->get();
            
            $ventas = isset($ventas[0]->cantidad) ? $ventas[0]->cantidad : 0;

            $producto=Producto::findOrfail($productos[$i]->id);

            $producto->compras = $compras;
            $producto->ventas = $ventas;                    
            $producto->saldo_pro = $producto->stock_inicial + $compras - $ventas;
            $producto->save();
        }

        $product=DB::table('productos as p')
        ->select('*')
        ->where((DB::raw('p.stock-p.saldo_pro')),'!=',0)
        ->get();
        //dd($product);
        return view('informe.stock',["product"=>$product]);
        
    }

    public function reporteIvaPDF(Request $request)
    {
        //dd($request);
        $date1 = $request->fecha1;
        $date2 = $request->fecha2;

        $compras = DB::table('compras as com')
            ->join('proveedores as p', 'p.id', '=', 'com.proveedor_id')
            ->select('com.id', 'com.fact_compra', 'com.iva', 'com.fecha',
                'com.total', 'com.estado', 'p.nombre')
            ->where('com.estado', '=', "0")
            ->where('com.contable', '=', "1");

        if ($date1 == null && $date2 == null) {

            $compras = $compras->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $compras = $compras->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $compras = $compras->whereBetween('com.fecha', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $compras = $compras->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $compras = $compras->get();

        }

        if ($compras->isEmpty()) {
            $compras = "Vacio";
        }

        //////////////////////////////////////////////////////////////////////////

        $gastos = DB::table('gastos as com')
            ->join('proveedores as p', 'p.id', '=', 'com.proveedor_id')
            ->select('com.id', 'com.fact_compra', 'com.iva', 'com.fecha',
                'com.total', 'com.estado', 'p.nombre')
            ->where('com.estado', '=', "0")
            ->where('com.contable', '=', "1");

        if ($date1 == null && $date2 == null) {

            $gastos = $gastos->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $gastos = $gastos->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $gastos = $gastos->whereBetween('com.fecha', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $gastos = $gastos->orderBy('com.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $gastos = $gastos->get();

        }

        if ($gastos->isEmpty()) {
            $gastos = "Vacio";
        }

        $ventas = DB::table('ventas as v')
        ->join('clientes as c', 'c.id', '=', 'v.cliente_id')
        ->select('v.id', 'v.fact_nro', 'v.iva5', 'v.iva10', 'v.ivaTotal', 'v.exenta', 'v.fecha',
                'v.total', 'v.estado', 'c.nombre')
        ->where('v.estado', '=', "0")
        ->where('v.contable', '=', "1");

        if ($date1 == null && $date2 == null) {

            $ventas = $ventas->orderBy('v.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $ventas = $ventas->get();
            //dd($pagos);->groupby('a.id')

        } else {
            $ventas = $ventas->whereBetween('v.fecha', [($date1), ($date2)]);
            //$pagos=$pagos->orderBy('c.cliente_id','asc');
            $ventas = $ventas->orderBy('v.id', 'asc');
            //$pagos=$pagos->orderBy('p.fec_pag','asc');
            $ventas = $ventas->get();

        }

        if ($ventas->isEmpty()) {
            $ventas = "Vacio";
        }

        // //PAGO DE SALARIOS
        // $salarios = DB::table('recibo_funcionarios as s')
        // ->join('funcionarios as f', 'f.id', '=', 's.funcionario_id')
        // ->select('s.id', 's.nro_recibo', 's.salario_cobrar', 's.fecha_recibo',
        // 'f.nombre');
        // //->where('com.estado', '=', "0");

        // if ($date1 == null && $date2 == null) {

        //     $salarios = $salarios->orderBy('s.id', 'asc');
        //     //$pagos=$pagos->orderBy('p.fec_pag','asc');
        //     $salarios = $salarios->get();
        //     //dd($pagos);->groupby('a.id')

        // } else {
        //     $salarios = $salarios->whereBetween('s.fecha_recibo', [($date1), ($date2)]);
        //     //$pagos=$pagos->orderBy('c.cliente_id','asc');
        //     $salarios = $salarios->orderBy('s.id', 'asc');
        //     //$pagos=$pagos->orderBy('p.fec_pag','asc');
        //     $salarios = $salarios->get();

        // }

        // if ($salarios->isEmpty()) {
        $salarios = "Vacio";
        // }
        //dd($salarios);
        $moneda = "GS";
        return $pdf = \PDF::loadView('informe.reporteIvaPDF', ["date1" => $date1, "date2" => $date2,
            "compras" => $compras,"gastos" => $gastos,"ventas" => $ventas,"salarios" => $salarios])
            ->setPaper('a4', 'landscape')
            ->stream('reporteIvaPDF.pdf');
    }

    public function reporteInventario(Request $request)
    {   
        $productos = DB::table('productos as p')
        ->select('p.cod_barra', 'p.descripcion','p.stock','p.precio_venta')
        ->orderBy('p.descripcion', 'asc')
        ->get();

        return view('informe.reporteInventario',["productos"=>$productos]);
    }

    public function reporteInventarioPrecio(Request $request)
    {   
        $productos = DB::table('productos as p')
        ->select('p.cod_barra', 'p.descripcion','p.stock','p.precio_venta')
        ->orderBy('p.descripcion', 'asc')
        ->get();

        return view('informe.reporteInventarioPrecio',["productos"=>$productos]);
    }


    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response


    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */

}