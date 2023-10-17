<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cotizacion;
use Illuminate\Support\Facades\Redirect;
use DB;
use Carbon\Carbon;
class cotizacionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
            $cotizaciones=DB::table('cotizaciones as c')
            //->join('empresas','clientes.idempresa','=','empresas.id')
            ->select('c.id','c.moneda','c.dolCompra','c.dolVenta',
            'psCompra','psVenta','rsCompra','rsVenta','c.fecha','c.estado')
            ->orderBy('c.id','desc')
            ->get();

            //dd($cotizaciones);
            return view('cotizacion.index',["cotizaciones"=>$cotizaciones]);
            //return $clientes;
        
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cotizacion= new Cotizacion();        
        //$cotizacion->moneda = $request->moneda;
        $cotizacion->dolCompra =0;
        $cotizacion->dolVenta =$request->dolVenta;

        $cotizacion->psCompra =0;
        $cotizacion->psVenta =$request->psVenta;

        $cotizacion->rsCompra =0;
        $cotizacion->rsVenta =$request->rsVenta;

        $fecha_hoy= isset($request->fecha) ? $request->fecha :Carbon::now('America/Asuncion');
        $cotizacion->fecha = $fecha_hoy;
        $cotizacion->estado = 0;


        $cotizacion->save();
        return Redirect::to("cotizacion");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $cotizacion= Cotizacion::findOrFail($request->id_cotizacion);
        //$cotizacion->moneda = $request->moneda;
        $cotizacion->dolCompra =str_replace(".","",$request->dolCompra);
        $cotizacion->dolVenta =str_replace(".","",$request->dolVenta);

        $cotizacion->psCompra =str_replace(".","",$request->psCompra);
        $cotizacion->psVenta =str_replace(".","",$request->psVenta);

        $cotizacion->rsCompra =str_replace(".","",$request->rsCompra);
        $cotizacion->rsVenta =str_replace(".","",$request->rsVenta);

        $fecha_hoy= isset($request->fecha) ? $request->fecha :Carbon::now('America/Asuncion');
        $cotizacion->fecha = $fecha_hoy;
        $cotizacion->estado = 0;

        $cotizacion->save();
        return Redirect::to("cotizacion");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        Cotizacion::destroy($id);
        return Redirect::to("cotizacion");
    }
}