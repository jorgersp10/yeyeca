<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Proveedor;
use App\Models\Compra;
use App\Models\Producto;
use App\Models\Precio_historico;
use App\Models\Pago_compra;
use App\Models\Factura;
use App\Models\Compra_det;
use App\Models\User;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\NumerosEnLetras;
use DateTime;
use DB;
use PDF;

class CompraController extends Controller
{
    public function index(Request $request){
      
        if($request){
        
            $sql=trim($request->get('buscarTexto'));
            $compras=DB::table('compras as c')
            ->join('compras_det as cdet','c.id','=','cdet.compra_id')
            ->join('proveedores as p','p.id','=','c.proveedor_id')
            ->join('users as u','u.id','=','c.user_id')
             ->select('c.id','c.fact_compra','c.iva','c.fecha','c.total','c.estado','p.nombre',
             'c.estado_pago','c.contable')
            ->where('c.fact_compra','LIKE','%'.$sql.'%')
            ->orwhere('p.nombre','LIKE','%'.$sql.'%')
            ->orderBy('c.id','desc')
            ->groupBy('c.id','c.fact_compra','c.iva','c.fecha','c.total','c.estado','p.nombre',
            'c.estado_pago','c.contable')
            ->paginate(10);
             
 
            return view('compra.index',["compras"=>$compras,"buscarTexto"=>$sql]);
            
           //return $compras;
        }
    }

    public function create(){
 
        /*listar las proveedores en ventana modal*/
        $proveedores=DB::table('proveedores')->get();
       
        /*listar los productos en ventana modal*/
        $productos=DB::table('productos as p')
        ->select(DB::raw('CONCAT(p.ArtCode," ",p.descripcion) AS producto'),'p.id')
        ->get(); 

        return view('compra.create',["proveedores"=>$proveedores,"productos"=>$productos]);

   }

   public function getProveedores(Request $request)
    {
 
    	$search = $request->search;

        if($search == ''){
            $proveedores = Proveedor::orderby('nombre','asc')
                    ->select('id','nombre','ruc')
                    ->limit(5)
                    ->get();
        }else{
            $search = str_replace(" ", "%", $search);
            $proveedores = Proveedor::orderby('nombre','asc')
                    ->select('id','nombre','ruc')
                    ->where('nombre','like','%'.$search.'%')
                    //->orWhere('apellido','like','%'.$search.'%')
                    ->orWhere('ruc','like','%'.$search.'%')
                    ->limit(5)
                    ->get();
        }

        $response = array();

        foreach($proveedores as $prov){
            $response[] = array(
                'id' => $prov->id,
                'text' => $prov->nombre." - ".$prov->ruc
            );
        }
        return response()->json($response);
    }

    public function getProductosCompra(Request $request)
    {
 
    	$search = $request->search;

        if($search == ''){
            $productos = Producto::orderby('descripcion','asc')
                    ->select('id','ArtCode','descripcion')
                    ->limit(10)
                    ->get();
        }else{
            $search = str_replace(" ", "%", $search);
            $productos = Producto::orderby('descripcion','asc')
                    ->select('id','ArtCode','descripcion')
                    ->where('ArtCode','like','%'.$search.'%')
                    //->orWhere('apellido','like','%'.$search.'%')
                    ->orWhere('descripcion','like','%'.$search.'%')
                    ->limit(10)
                    ->get();
        }

        $response = array();

        foreach($productos as $prod){
            $response[] = array(
                'id' => $prod->id,
                'text' => $prod->ArtCode." - ".$prod->descripcion
            );
        }
        return response()->json($response);
    }
   public function store(Request $request)
   {
        $cotizaciones=DB::table('cotizaciones as c')
        ->select('c.id','c.dolVenta','c.psVenta','c.rsVenta')
        //->where('id','=',$nro_prof)
        ->orderby('c.id','desc')
        ->first(); 
        
        try{

            DB::beginTransaction();

            $fecha_hoy= Carbon::now('America/Asuncion');

            $compra = new Compra();
            $compra->proveedor_id = $request->proveedor_id;
            $compra->cotiz_id = $cotizaciones->id;
            $compra->fact_compra = $request->fact_compra;
            $compra->fecha = $fecha_hoy->toDateString();
            $compra->iva = $request->total_iva;
            $compra->total = $request->total_pagar;
            $compra->estado = 0;
            $compra->estado_pago = "C";
            $compra->user_id = auth()->user()->id;
            if($request->contable == "on")
                $compra->contable = 1;
            else
                $compra->contable = 0;
            $compra->save();

            $producto_id=$request->producto_id;
            $cantidad = str_replace(",", ".", $request->cantidad);

            $precio = str_replace(",", ".", $request->precio);

           
            $cont=0;

             while($cont < count($producto_id)){

                $detalle = new Compra_det();
                /*enviamos valores a las propiedades del objeto detalle*/
                /*al idcompra del objeto detalle le envio el id del objeto compra, que es el objeto que se ingresó en la tabla compras de la bd*/
                $detalle->compra_id = $compra->id;
                $detalle->producto_id = $producto_id[$cont];
                $detalle->cantidad = $cantidad[$cont];
                $detalle->precio = str_replace(",", ".", $precio[$cont]);   
                $detalle->save();
                
                //ACTULIZAR EL PRECIO DE COMPRA DEL PRODUCTO
                $producto = Producto::findOrFail($producto_id[$cont]);
                if($producto->precio_compra != (str_replace(",", ".", $precio[$cont])))
                {
                    //CONSULTA PARA VERIFICAR EXISTENCIA EN EL HISTORICO Y ACTULIZAR O GENERAR REGISTRO NUEVO
                    $precio_hist=DB::table('precios_historico as ph')
                    ->select('producto_id')
                    ->where('ph.producto_id','=',$producto_id[$cont])
                    ->get();
                    //SI ES VACIO ENTONCES GENERA NUEVO REGISTRO
                    if($precio_hist->isEmpty()) 
                    {
                        $histprecio = new Precio_historico();
                        $histprecio->producto_id = $producto_id[$cont];
                        $histprecio->preciocompra_ant = 0;
                        $histprecio->preciocompra_act = str_replace(",", ".", $precio[$cont]);
                        $histprecio->save();
                    } 
                    //SINO ACTUALIZA EL REGISTRO
                    else{
                        
                        $histprecioID = Precio_historico::where('producto_id', $producto_id[$cont])->get();
                        $histprecio= Precio_historico::findOrFail($histprecioID[0]->id);                        
                        $histprecio->preciocompra_ant = $histprecio->preciocompra_act;
                        $histprecio->preciocompra_act = str_replace(",", ".", $precio[$cont]);
                        $histprecio->update();
                    }                  
                    
                }
                
                $producto->precio_compra = str_replace(",", ".", $precio[$cont]);
                $producto->update();
                $cont=$cont+1;                
            }
                
            DB::commit();

        } catch(Exception $e){
            
            DB::rollBack();
        }

        return Redirect::to('compra');
    }

    public function show($id){

        //dd($id);    
        /*mostrar compra*/
        //$id = $request->id;
        $compras=DB::table('compras as c')
        ->join('compras_det as cdet','c.id','=','cdet.compra_id')
        ->join('proveedores as p','p.id','=','c.proveedor_id')
        ->select('c.id','c.fact_compra','c.fecha','c.total','p.nombre','c.iva',
        'p.nombre','p.ruc'
        ,DB::raw('sum(cdet.cantidad*precio) as total'))
        ->where('c.id','=',$id)
        ->orderBy('c.id', 'desc')
        ->groupBy('c.id','c.fact_compra','c.fecha','c.total','p.nombre','c.iva',
        'p.nombre','p.ruc')
        ->first();

        /*mostrar detalles*/
        $detalles=DB::table('compras_det as cdet')
        ->join('productos as p','cdet.producto_id','=','p.id')
        ->select('cdet.cantidad','cdet.precio','p.descripcion as producto')
        ->where('cdet.compra_id','=',$id)
        ->orderBy('cdet.id', 'desc')->get();

        $pagos=DB::table('pagos_compra as pc')
        //->join('compras as c','c.id','=','pc.factura_id')
        ->select('pc.factura_id','pc.total_pag','pc.total_pagf','pc.total_pagtr','pc.total_pagch'
        ,'pc.total_pagtd','pc.total_pagtc','pc.fec_pag')
        ->where('pc.factura_id','=',$id)
        ->orderBy('pc.id','asc')
        ->get();

        if($pagos->isEmpty()){
                $pagos="Vacio";
            }
        
        return view('compra.show',['compras' => $compras,'detalles' =>$detalles,'pagos' =>$pagos]);
    }
    
    public function edit(Request $request){


            $compra = Compra::findOrFail($request->id_compra);
            $compra->estado = 1;
            $compra->save();
            return Redirect::to('compra');

    }

    public function destroy($id)
    {
        
         try{

            DB::beginTransaction();


            $compra = Compra::findOrFail($id);
            //dd($venta);

            if($compra->estado == 1){

                $pagos = DB::table('pagos_compra as p')
                ->select('id')
                ->where('p.factura_id', '=', $id)
                ->get();

                //dd($pagos);

                for($i = 0 ; $i < sizeof($pagos); $i++)
                {
                    Pago_compra::destroy($pagos[$i]->id);
                }
                
                $compras = DB::table('compras as c')
                ->select('id')
                ->where('c.id', '=', $id)
                ->first();

                if(isset($compras)){
                    $compras_det = DB::table('compras_det as cdet')
                    ->select('id')
                    ->where('cdet.compra_id', '=', $id)
                    ->get();
                //dd($ventas_det);

                    for ($i = 0; $i < sizeof($compras_det); $i++) {
                        Compra_det::destroy($compras_det[$i]->id);
                    }
                    Compra::destroy($id);

                }
                //dd($ventas_det);

            }
            else{
                return Redirect::to('compra')->with('msj', 'FACTURA COMPRA DEBE SER ANULADA ANTES DE BORRAR');

            }

            DB::commit();

        } catch(Exception $e){
            
            DB::rollBack();
        }

        return Redirect::to('compra')->with('msj2', 'FACTURA ELIMINADA');
    }

     public function pagarCompra($id)
    {
        //dd($id);
        $id = $id;

        $bancos=DB::table('bancos')
            ->select('bancos.id','bancos.descripcion')
            ->get();

        $compras=DB::table('compras as c')
            ->join('compras_det as cdet','c.id','=','cdet.compra_id')
            ->join('proveedores as p','p.id','=','c.proveedor_id')
            ->join('users as u','u.id','=','c.user_id')
            ->select('c.id','c.fact_compra','c.iva','c.fecha','c.total','c.estado','p.nombre','c.estado_pago')
            ->where('c.id','=',$id)
            ->orderBy('c.id','desc')
            ->groupBy('c.id','c.fact_compra','c.iva','c.fecha','c.total','c.estado','p.nombre','c.estado_pago')
            ->first();

        $pagos=DB::table('pagos_compra as pc')
        ->join('compras as c','c.id','=','pc.factura_id')
        ->select('pc.id','c.fact_compra','c.fecha','c.total','c.estado','c.estado_pago',
         DB::raw('sum(total_pag) as capital_pagado'))
        ->where('pc.factura_id','=',$id)
        ->orderBy('pc.id','desc')
        ->groupBy('pc.id','c.fact_compra','c.fecha','c.total','c.estado','c.estado_pago')
        ->get();
        //dd($pagos[0]->capital_pagado);
        if($pagos->isNotEmpty()){
            $saldo_pagar=$compras->total - $pagos[0]->capital_pagado;
            //dd($saldo_pagar);
            return view('compra.pagarFactura',['id' => $id,'bancos' => $bancos,'compras' => $compras,
        'saldo_pagar'=>$saldo_pagar]);
                   }
            
        else{
            $saldo_pagar=$compras->total;
            return view('compra.pagarFactura',['id' => $id,'bancos' => $bancos,'compras' => $compras,
            'saldo_pagar'=>$saldo_pagar]); 
        }

    }


    public function pagarFactCompra(Request $request)
    {
        //dd($request);
        try
            {        
                DB::beginTransaction();
                // Empezamos a cobrar
                $now = Carbon::now();
                $pago_compra= new Pago_compra();
                $pago_compra->factura_id=$request->id_factura;

                //$ingreso=strval($request->total_pagadof+$request->total_pagadoch+$request->total_pagadotc+$request->total_pagadotd+$request->total_pagadotr);

                //dd($ingreso);
                
//dd($request);
                if($request->total_pagadof == null)
                    $pago_compra->total_pagf = 0;
                else
                    $pago_compra->total_pagf = str_replace(".","",$request->total_pagadof);
                //dd($pago_compra->total_pagf);
                if($request->total_pagadoch == null)
                    $pago_compra->total_pagch = 0;
                else
                $pago_compra->total_pagch = str_replace(".","",$request->total_pagadoch);
                
                $pago_compra->nro_cheque = $request->nro_cheque;
                $pago_compra->banco_cheque = $request->ban_che_id;
                $pago_compra->librador = '';

                if($request->total_pagtc == null)
                    $pago_compra->total_pagtc = 0;
                else
                $pago_compra->total_pagtc = str_replace(".","",$request->total_pagadotc);
                
                $pago_compra->banco_tcredito = 0;
                $pago_compra->nro_tcredito = $request->nro_tcredito;

                if($request->total_pagtd == null)
                    $pago_compra->total_pagtd = 0;
                else
                $pago_compra->total_pagtd = str_replace(".","",$request->total_pagadotd);

                $pago_compra->banco_tdebito = 0;
                $pago_compra->nro_tdebito = $request->nro_tdebito;

                $request->total_pagadotr=$request->total_pagadotr == NULL ? 0 : $request->total_pagadotr;
                $pago_compra->total_pagtr = str_replace(".","",$request->total_pagadotr);

                $ingreso=$pago_compra->total_pagf+$pago_compra->total_pagch+$pago_compra->total_pagtc+$pago_compra->total_pagtd+$pago_compra->total_pagtr;

                //dd($ingreso);
                $diferencia = ($request->saldo) - $ingreso;
                $pago_compra->capital = $ingreso;
                $pago_compra->total_pag = $ingreso;

                if ($diferencia<=0){
                    $pago_compra->pago_est = "C";
                    $item = Compra::findOrFail($request->id_factura);
                    $item->estado_pago='C';
                    $item->update();
                }
                else
                {
                    $pago_compra->pago_est = "P";
                }
                $pago_compra->fec_pag = $now;
                $pago_compra->usuario_id = auth()->user()->id;

                $pago_compra->save();

                DB::commit();
            }
            catch(Exception $e){        
                    DB::rollBack();
        }
        return Redirect::to("compra");
    }

     public function compraContable(Request $request)
    {
        $compra = Compra::findOrFail($request->id_compra);

        if($compra->contable == 0)
            $compra->contable = 1;
        else
            $compra->contable = 0;
        $compra->update();

        return Redirect::to('compra')->with('msj2', 'ESTADO CONTABLE DE LA FAC. COMPRA HA CAMBIADO');
    }

}