<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Caja;
use App\Models\Cliente;
use App\Models\Venta;
use App\Models\Producto;
use App\Models\Precio_historico;
use App\Models\Factura;
use App\Models\Venta_det;
use App\Models\Cuota;
use App\Models\Cuota_det;
use App\Models\User;
use App\Models\Pago;
use App\Models\Recibo_Paramorden;
use App\Models\Pago_Trn;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use App\NumerosEnLetras;
use Codedge\Fpdf\Fpdf\Fpdf;
//use Rawilk\Printing\Contracts\Printer;
use DateTime;
use Printing;
use DB;
use Response;
use PDF;

class FacturaController extends Controller
{
    protected $fpdf;
 
    public function __construct()
    {
        //$fpdf = new Fpdf;
       $fpdf = new Fpdf('P','mm',array(80,150));
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index(Request $request){
      
        if($request){
        
            $sql=trim($request->get('buscarTexto'));
            $ventas=DB::table('ventas as v')
            ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
            ->join('clientes as c','c.id','=','v.cliente_id')
            ->join('users as u','u.id','=','v.user_id')
            ->join('vendedores as ven','ven.id','=','v.vendedor_id')
            ->select('v.id','v.fact_nro','v.iva5','v.iva10','v.ivaTotal','v.exenta','v.fecha',
             'v.total','v.estado','c.nombre','v.contable','v.nro_recibo','ven.id as vendedor_id',
             'ven.nombre as vendedor')
            ->where('v.fact_nro','LIKE','%'.$sql.'%')
            ->orwhere('c.nombre','LIKE','%'.$sql.'%')
            ->orderBy('v.id','desc')
            ->groupBy('v.id','v.fact_nro','v.iva5','v.iva10','v.ivaTotal','v.exenta','v.fecha',
            'v.total','v.estado','c.nombre','v.contable','v.nro_recibo','ven.id',
            'ven.nombre')
            ->paginate(10);

            $fecha_iva = DB::table('iva_param as i')
            ->select('i.fecha_ini','i.fecha_fin')
            ->first();

            $ventas_iva = DB::table('ventas as v')
            ->select(DB::raw('sum(v.total) as total_venta'))
            ->where('v.estado', '=', "0")
            ->where('v.fact_nro', '>', "0")
            ->whereBetween('v.fecha', [$fecha_iva->fecha_ini, $fecha_iva->fecha_fin])
            ->first();

            isset($ventas_iva->total_venta) ? $total_venta = $ventas_iva->total_venta : $total_venta = 0;
           
            $compras = DB::table('compras as c')
            ->select(DB::raw('sum(c.total) as total_compra'))
            ->where('c.estado', '=', "0")
            ->where('c.contable', '=', "1")
            ->whereBetween('c.fecha', [$fecha_iva->fecha_ini, $fecha_iva->fecha_fin])
            ->first();

            isset($compras->total_compra) ? $total_compra = $compras->total_compra : $total_compra = 0;

            $gastos = DB::table('gastos as g')
            ->select(DB::raw('sum(g.total) as total_gasto'))
            ->where('g.estado', '=', "0")
            ->where('g.contable', '=', "1")
            ->whereBetween('g.fecha', [$fecha_iva->fecha_ini, $fecha_iva->fecha_fin])
            ->first();

            isset($gastos->total_gasto) ? $total_gasto = $gastos->total_gasto : $total_gasto = 0;

            $total_compra_gasto = $total_compra + $total_gasto;
            $saldoFactura = $total_compra_gasto - $total_venta;

            $cotizaciones=DB::table('cotizaciones as c')
            //->join('empresas','clientes.idempresa','=','empresas.id')
            ->select('c.id','c.moneda','c.dolCompra','c.dolVenta',
            'psCompra','psVenta','rsCompra','rsVenta','c.fecha','c.estado')
            ->orderBy('c.fecha','desc')
            ->first();

            $vendedores=DB::table('vendedores as v')
            ->select('v.id','v.nombre','v.num_documento')
            ->orderBy('v.nombre','asc')
            ->get();
 
            return view('factura.index',["ventas"=>$ventas,"total_venta"=>$total_venta,"total_venta"=>$total_venta,
            "total_compra_gasto"=>$total_compra_gasto,"saldoFactura"=>$saldoFactura,
            "cotizaciones"=>$cotizaciones,"vendedores"=>$vendedores,"buscarTexto"=>$sql]);
            
           //return $compras;
        }
    }

    public function create(){
 
        /*listar las clientes en ventana modal*/
        $clientes=DB::table('clientes')->get();
       
        /*listar los productos en ventana modal*/
        $productos=DB::table('productos as p')
        ->select(DB::raw('CONCAT(p.ArtCode," ",p.descripcion) AS producto'),'p.id')
        ->get(); 

        $nro_factura = DB::table('ventas as v')
        ->select(DB::raw('MAX(v.fact_nro) as fact_nro'))
        ->where('v.estado','=',0)
        ->get();

        $bancos=DB::table('bancos')
        ->select('bancos.id','bancos.descripcion')
        ->get();

        $cuentas=DB::table('cuentas_corriente as cc')
        ->join('bancos as b','b.id','=','cc.banco_id')
        ->select('cc.id','cc.nro_cuenta','cc.banco_id','b.descripcion as banco')
        ->get();

        $cotizaciones=DB::table('cotizaciones as c')
        //->join('empresas','clientes.idempresa','=','empresas.id')
        ->select('c.id','c.moneda','c.dolCompra','c.dolVenta',
        'psCompra','psVenta','rsCompra','rsVenta','c.fecha','c.estado')
        ->orderBy('c.id','desc')
        ->first();

        $vendedores=DB::table('vendedores as v')
        ->select('v.id','v.nombre','v.num_documento')
        ->orderBy('v.nombre','asc')
        ->get();
        //dd($nro_factura);

        return view('factura.create',["clientes"=>$clientes,"productos"=>$productos,
        "nro_factura"=>$nro_factura,"bancos"=>$bancos,"cuentas"=>$cuentas,
        "cotizaciones"=>$cotizaciones,"vendedores"=>$vendedores]);

   }

   public function getClientesVentas(Request $request)
    {
 
    	$search = $request->search;

        if($search == ''){
            $clientes = Cliente::orderby('nombre','asc')
                    ->select('id','nombre','num_documento')
                    ->limit(5)
                    ->get();
        }else{
            $search = str_replace(" ", "%", $search);
            $clientes = Cliente::orderby('nombre','asc')
                    ->select('id','nombre','num_documento')
                    ->where('nombre','like','%'.$search.'%')
                    //->orWhere('apellido','like','%'.$search.'%')
                    ->orWhere('num_documento','like','%'.$search.'%')
                    ->limit(5)
                    ->get();
        }

        $response = array();

        foreach($clientes as $cli){
            $response[] = array(
                'id' => $cli->id,
                'text' => $cli->nombre." - ".$cli->num_documento
            );
        }
        return response()->json($response);
    }

    public function getProductos(Request $request)
    {
 
    	$search = $request->search;

        if($search == ''){
            $productos = Producto::orderby('descripcion','asc')
                    ->select('id','ArtCode','descripcion','cod_barra')
                    ->limit(10)
                    ->get();
        }else{
            $search = str_replace(" ", "%", $search);
            $productos = Producto::orderby('descripcion','asc')
                    ->select('id','ArtCode','descripcion','cod_barra')
                    ->where('cod_barra','like','%'.$search.'%')
                    //->orWhere('apellido','like','%'.$search.'%')
                    ->orWhere('descripcion','like','%'.$search.'%')
                    ->limit(10)
                    ->get();
        }

        $response = array();

        foreach($productos as $prod){
            $response[] = array(
                'id' => $prod->id,
                'text' => $prod->cod_barra." - ".$prod->descripcion
            );
        }
        return response()->json($response);
    }

    public function obtenerProductosB($codigo_barra)
    {
        $productos=DB::table('productos as p')
        ->select('p.id','p.cod_barra','p.descripcion','p.stock','p.precio_venta')
        ->where('p.cod_barra','=',$codigo_barra)
        ->get();

        return $productos;
    }



   public function store(Request $request)
   {
        //dd($request);
        //DATOS PARA ENVIAR AL CREATE
         /*listar las clientes en ventana modal*/
        $clientes=DB::table('clientes')->get();
       
        /*listar los productos en ventana modal*/
        $productos=DB::table('productos as p')
        ->select(DB::raw('CONCAT(p.ArtCode," ",p.descripcion) AS producto'),'p.id')
        ->get(); 

        $nro_factura = DB::table('ventas as v')
        ->select(DB::raw('MAX(v.fact_nro) as fact_nro'))
        ->where('v.estado','=',0)
        ->get();

        $bancos=DB::table('bancos')
        ->select('bancos.id','bancos.descripcion')
        ->get();

        $cuentas=DB::table('cuentas_corriente as cc')
        ->join('bancos as b','b.id','=','cc.banco_id')
        ->select('cc.id','cc.nro_cuenta','cc.banco_id','b.descripcion as banco')
        ->get();
      
        $cotizaciones=DB::table('cotizaciones as c')
        ->select('c.id','c.dolVenta','c.psVenta','c.rsVenta')
        //->where('id','=',$nro_prof)
        ->orderby('c.id','desc')
        ->first();

        $vendedores=DB::table('vendedores as v')
        ->select('v.id','v.nombre','v.num_documento')
        ->orderBy('v.nombre','asc')
        ->get();

        $fechaEmision= Carbon::now('America/Asuncion');

        $sucursal= auth()->user()->idsucursal;
       
        $timbrados = DB::table('timbrados as t')
        ->select('id','ini_timbrado','fin_timbrado','suc_timbrado','nrof_suc','nrof_expendio')
        ->where('estado','=',0)
        ->where('suc_timbrado','=',$sucursal)
        ->get();
        
        $nro_f = DB::table('ventas as v')
        ->select('v.fact_nro')
        ->where('v.fact_nro','=',$request->fact_nro)
        ->where('v.fact_nro','>',0)
        ->where('v.estado','=',0)
        ->first();

        try
        {

            DB::beginTransaction();

            $fecha_hoy= Carbon::now('America/Asuncion');

            $venta = new Venta();
            if($request->cliente_id == null)
                $venta->cliente_id = 2;
            else
                $venta->cliente_id = $request->cliente_id;

            if(($request->fact_nro == null) || ($request->fact_nro== 0))
                $venta->fact_nro = 0;
            else
                $venta->fact_nro = $request->fact_nro;

            if($request->contable == 1)
            {
                $venta->fact_nro = $request->fact_nro;
            }
            else
            {
                $ids = DB::select('select id from recibos_paramorden where suc_recibo= ?',[1]);
                $tran_rec= Recibo_Paramorden::findOrFail($ids[0]->id);
                $tran_rec->nro_recibo=$tran_rec->nro_recibo+1;
                $nrorec=$tran_rec->nro_recibo;
                $tran_rec->update();
                $venta->nro_recibo = $nrorec;
            }
            //CREAMOS UNA TRANSACCIÓN
            $trn = Pago_Trn::findOrFail(1);
            $trn->id_transaccion = $trn->id_transaccion+1;
            $tran_nro = $trn->id_transaccion;
            $trn->update();

            $venta->transaccion = $tran_nro;
            $venta->fecha = $fecha_hoy;
            $venta->iva5 = $request->iva5;
            $venta->iva10 = $request->iva10;
            $venta->ivaTotal = $request->total_iva;
            $venta->exenta = $request->exenta;
            $venta->total = $request->total_pagar;
            $venta->tipo_factura = 0;
            $venta->estado = 0;
            $venta->user_id = auth()->user()->id;
            $venta->suc_nro = $timbrados[0]->nrof_suc;
            $venta->expendio_nro = $timbrados[0]->nrof_expendio;
            $venta->contable = $request->contable;
            $venta->cotiz_id = 0;
            $venta->vendedor_id = $request->vendedor_id;
            //dd($venta);
            $venta->save();
            if (($request->total_pagar) == null){
                return back()->with('msj', 'FAVOR AGREGAR LOS DETALLES DE LA VENTA');
            }
            //dd($request->total_pagar);
            $producto_id=$request->producto_id;
            $cantidad = str_replace(",", ".", $request->cantidad);
            $cantidad_calculo = $request->cantidad_calculo;
            $precio=str_replace(".","",$request->precio);
        
            $cont=0;
            //dd($producto_id);
            if (($producto_id) == null){
                return back()->with('msj', 'FAVOR AGREGAR LOS DETALLES DE LA VENTA');
            }

            while($cont < count($producto_id)){

                $detalle = new Venta_det();
                /*enviamos valores a las propiedades del objeto detalle*/
                /*al idcompra del objeto detalle le envio el id del objeto compra, que es el objeto que se 
                ingresó en la tabla compras de la bd*/
                $detalle->transaccion = $tran_nro;
                $detalle->venta_id = $venta->id;
                $detalle->producto_id = $producto_id[$cont];
                $detalle->cantidad = $cantidad[$cont];
                $detalle->cantidad_calculo = $cantidad_calculo[$cont];
                $detalle->precio = str_replace(".","",$precio[$cont]);    
                //dd($detalle);
                $detalle->save();

                //RESTAMOS LOS PRODUCTOS DEL STOCK
                $stockpro = Producto::findOrFail($detalle->producto_id);
                $stockpro->stock = $stockpro->stock - $detalle->cantidad;
                $stockpro->update();

                //ALMACEN DESPUES
                $cont=$cont+1;                
            }    
            
            //PASAR POR CAJA EL PAGO
            //INGRESOS
            if($request->total_pagadof == null)
            {
                $pagoEfectivo = 0;
            }
            else{
                $pagoEfectivo = str_replace(".","",$request->total_pagadof);
            }
            if($request->total_pagadoch == null)
            {
                $pagoCheque = 0;
            }
            else{
                $pagoCheque = str_replace(".","",$request->total_pagadoch);
            }
            if($request->total_pagadotc == null)
            {
                $pagoCredito = 0;
            }
            else{
                $pagoCredito = str_replace(".","",$request->total_pagadotc);
            }
            if($request->total_pagadotd == null)
            {
                $pagoDebito = 0;
            }
            else{
                $pagoDebito = str_replace(".","",$request->total_pagadotd);
            }
            if($request->total_pagadotr == null)
            {
                $pagoTransfer = 0;
            }
            else{
                $pagoTransfer = str_replace(".","",$request->total_pagadotr);
            }                   
            //EGRESOS
            if($request->total_vuelto == null)
            {
                $vueloCaja = 0;
            }
            else{
                $vueloCaja = str_replace(".","",$request->total_vuelto);
            }

            $ingreso=($pagoEfectivo + $pagoCheque + $pagoDebito + $pagoTransfer)- $vueloCaja;
            $moneda = "GS";
            if ($ingreso>0)
            {
                $now = Carbon::now();
                $pago= new Pago();
                $pago->venta_id = $venta->id;
                $pago->moneda = $moneda;
                $pago->transaccion = $tran_nro;
                $pago->iva = $venta->ivaTotal;
                $pago->total_pag = $ingreso;
                $pago->pago_est = "C";
                $pago->total_pagf = $pagoEfectivo;

                $pago->total_pagch = $pagoCheque;
                $pago->nro_cheque = isset($request->nro_cheque) ? $request->nro_cheque: 0;

                $pago->total_pagtc = $pagoCredito;
                $pago->nro_tcredito = isset($request->nro_tcredito) ? $request->nro_tcredito: 0;

                $pago->total_pagtd = $pagoDebito;
                $pago->nro_tdebito = isset($request->nro_tdebito) ? $request->nro_tdebito: 0;

                $pago->total_pagtr = $pagoTransfer;
                $pago->cuenta_id = $request->cuenta_id;

                $pago->fec_pag = $now;
                $pago->usuario_id = auth()->user()->id;

                $pago->save();
            }
            DB::commit();

            //$this->imprimirTicket($venta->id);
            return view('factura.create',["clientes"=>$clientes,"productos"=>$productos,
            "nro_factura"=>$nro_factura,"bancos"=>$bancos,"cuentas"=>$cuentas,
            "cotizaciones"=>$cotizaciones,"vendedores"=>$vendedores]);
           // return redirect()->route('imprimirTicket', ['id' => $venta->id]);

        } catch(Exception $e){
            
            DB::rollBack();
    }
    
    }
    public function id()
    {
        return $id;
    }
    public function imprimirPrueba($id_v)
    {
        //dd($id);
        
        //$id = function id();
         //$printers = Printing::printers();
         //dd($id);
        // foreach ($printers as $printer) {
        //     echo $printer->name();
        // }
        $nombreImpresora = "POS-80CPR";
        
        $connector = new WindowsPrintConnector($nombreImpresora);
       //dd($connector);
         $printer = new Printer($connector);

         $printers = Printing::printers();
         //dd($id);
        foreach ($printers as $printer) {
            echo $printer->name();
        }

        $printer->close();

        dd($printers);
        //$impre=Printing::defaultPrinter($nombreImpresora ); // returns an instance of Rawilk\Printing\Contracts\Printer if the printer is found
            //$printer = new Printer;
        //dd($printer->id());
            // or for just the id
        //dd(Printing::defaultPrinterId()); 

        //  Printing::newPrintTask()
        //  ->printer(Printing::defaultPrinterId())
        //  ->file('doc.pdf')
        //  ->send();
    }

    public function imprimirTicketESC($id)
    {
         $cabVenta=DB::table('ventas as v')
        ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        ->join('clientes as c','c.id','=','v.cliente_id')
        ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo'
        ,DB::raw('sum(vdet.cantidad_calculo*precio) as total'))
        ->where('v.id','=',$id)
        ->orderBy('v.id', 'desc')
        ->groupBy('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo')
        ->first();

        /*mostrar detalles*/
        $detallesVenta=DB::table('ventas_det as vdet')
        ->join('productos as p','vdet.producto_id','=','p.id')
        ->select('vdet.cantidad','vdet.cantidad_calculo','vdet.precio','p.descripcion as producto')
        ->where('vdet.venta_id','=',$id)
        ->orderBy('vdet.id', 'desc')->get();


        $nombreImpresora = "POS-80CPR";
        //dd($nombreImpresora);
        $connector = new WindowsPrintConnector($nombreImpresora);
       
        $printer = new Printer($connector);

        $mode= Printer::CUT_FULL;
         
        $printer->setJustification(Printer::JUSTIFY_CENTER);
        $printer->setTextSize(2, 2);
        $printer->text("QUÍMICA R&G \n");
        $printer->setTextSize(1, 1);
        $printer->text("Productos de limpieza, cobranzas y accesorios en gral. \n");
        $printer->text("Calle Argentina, Arroyo Pora, Encarnación. \n");

        $printer->feed(1);
        $printer->text("Comprobante N.°:  $cabVenta->nro_recibo \n");
        $printer->text("Cliente:  $cabVenta->nombre \n");
        $printer->text("Fecha:  $cabVenta->fecha \n");
        $printer->feed(1);

        $printer->setTextSize(1, 1);
        $total = 0;
        $sub_total = 0;
        $cero = 48;
        

             foreach ($detallesVenta as $producto) {
                $tam_precio = strlen($producto->precio);
                $sub_total = $producto->cantidad_calculo * $producto->precio;
                $total += $producto->cantidad_calculo * $producto->precio;
                $tam_total = strlen($sub_total);

                $tot_espacio = $cero - $tam_precio - $tam_total -8;
                $espacio= "";
                for($i=0; $i < $tot_espacio; $i++)
                {
                    $espacio=  $espacio." ";
                }
                /*Alinear a la izquierda para la cantidad y el nombre*/
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text($producto->cantidad . " x " . $producto->producto . "\n");
            
                /*Y a la derecha para el importe*/
                $printer->setJustification(Printer::JUSTIFY_LEFT);
                $printer->text(' Gs.' . $producto->precio.$espacio);
                $printer->setJustification(Printer::JUSTIFY_RIGHT);
                $printer->text(' Gs.' . $sub_total."\n");
                
             }
            //$printer->text('000000000000000000000000000000000000000000000000'."\n");
        //$impresora->text(new item('', '', ''));
        $printer->setJustification(Printer::JUSTIFY_RIGHT);
        $printer->text("--------\n");
        $printer->text("TOTAL: Gs". $total ."\n");
        $printer->feed(3);
        $printer->text("Sin validez legal\n");
        $printer->text("Muchas gracias por su compra\n");
        $printer->feed(5);
        $printer->cut($mode, 10);
        $printer->close();
    }

    public function imprimirTicket($id,$tc)
    {
        $cabVenta=DB::table('ventas as v')
        ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        ->join('clientes as c','c.id','=','v.cliente_id')
        //->join('cotizaciones as cot','cot.id','=','v.cotiz_id')
        ->join('vendedores as ven','ven.id','=','v.vendedor_id')
        ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo',
        'ven.nombre as vendedor'
        ,DB::raw('sum(vdet.cantidad_calculo*precio) as total'))
        ->where('v.id','=',$id)
        ->orderBy('v.id', 'desc')
        ->groupBy('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo',
        'ven.nombre')
        ->first();

        /*mostrar detalles*/
        $detallesVenta=DB::table('ventas_det as vdet')
        ->join('productos as p','vdet.producto_id','=','p.id')
        ->select('vdet.cantidad','vdet.cantidad_calculo','vdet.precio','p.descripcion as producto','p.cod_barra as codigo')
        ->where('vdet.venta_id','=',$id)
        ->orderBy('vdet.id', 'desc')->get();
        
        //CREAMOS EL TICKET
        $fpdf = new Fpdf('P','mm',array(75,200));
        $fpdf->AddPage();
        // CABECERA
        $fpdf->SetFont('Helvetica','',10);
        $fpdf->Cell(40,4,'MF - Moda Femenina',0,1,'C');
        $fpdf->Ln(1);
        $fpdf->SetFont('Helvetica','',8);
        // $fpdf->Cell(60,4,'Productos de limpieza, cobranzas y accesorios en gral.',0,1,'C');
        // $fpdf->Cell(60,4,'Calle Argentina, Arroyo Pora, Encarnacion',0,1,'C');
        // $fpdf->Cell(60,4,'Celular: 0982-111970',0,1,'C');
        // DATOS FACTURA        
        $fpdf->Ln(2);
        $fpdf->Cell(10,3,'Comprobante Nro: '.$cabVenta->nro_recibo,0,1,'');
        $fpdf->Cell(10,4,'Cliente: '.$cabVenta->nombre,0,1,'');
        $fpdf->Cell(10,4,'Fecha: '.date('d-m-Y', strtotime($cabVenta->fecha)),0,1,'');
        $fpdf->Cell(10,4,'Vendedor/a: '.$cabVenta->vendedor,0,1,'');
        $fpdf->Ln(0);
        // COLUMNAS
        $fpdf->SetFont('Helvetica', 'B', 8);
        $fpdf->Cell(20, 10, 'Articulo', 0);
        $fpdf->Cell(5, 10, 'Ud',0,0,'R');
        $fpdf->Cell(10, 10, 'Precio',0,0,'R');
        $fpdf->Cell(15, 10, 'Total',0,0,'R');
        $fpdf->Ln(8);
        $fpdf->Cell(50,0,'','T');
        $fpdf->Ln(0);                      
        
        $total_deuda=0;
        $total_pagos=0;
        $total_vencido=0;
        $total_cantidad=0;

        if($tc == "TODO")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio), 2, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "USD ".number_format(($row->precio*$row->cantidad_calculo), 2, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total * ($cabVenta->psVenta);
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);    
            $fpdf->Cell(25, 7, 'TOTAL USD. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            $fpdf->Ln(3); 
            $fpdf->Cell(25, 7, 'TOTAL Gs. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "Gs. ".number_format(($totalGs), 0, ",", "."),0,0,'R');
            $fpdf->Ln(3);
            $fpdf->Cell(25, 7, 'TOTAL $. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "$. ".number_format(($totalPs), 2, ",", "."),0,0,'R');
            $fpdf->Ln(3);
            $fpdf->Cell(25, 7, 'TOTAL R$. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "R$. ".number_format(($totalRs), 2, ",", "."),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($tc == "USD")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio), 2, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "USD ".number_format(($row->precio*$row->cantidad_calculo), 2, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total * ($cabVenta->psVenta);
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);    
            $fpdf->Cell(25, 7, 'TOTAL USD. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($tc == "PS")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio*$cabVenta->psVenta), 0, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "$ ".number_format(($row->precio*$row->cantidad_calculo*$cabVenta->psVenta), 0, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total * ($cabVenta->psVenta);
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "$ ".number_format(($totalPs), 0, ".", ","),0,0,'R');            
            //SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);   
            $fpdf->Cell(25, 7, 'TOTAL $. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "$. ".number_format(($totalPs), 0, ",", "."),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($tc == "GS")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio), 0, ",", "."),0,0,'R');
                $fpdf->Cell(15, -5, "Gs. ".number_format(($row->precio*$row->cantidad_calculo), 0, ",", "."),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total;
            $totalPs = $cabVenta->total;
            $totalRs = $cabVenta->total;
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "Gs. ".number_format(($totalGs), 0, ",", "."),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);               
            $fpdf->Cell(25, 7, 'TOTAL Gs. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "Gs. ".number_format(($totalGs), 0, ",", "."),0,0,'R');
            $fpdf->Ln(3);           
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($tc == "RS")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio*$cabVenta->rsVenta), 2, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "R$ ".number_format(($row->precio*$row->cantidad_calculo*$cabVenta->rsVenta), 2, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total;
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "R$ ".number_format(($totalRs), 2, ".", ","),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);   
           
            $fpdf->Cell(25, 7, 'TOTAL R$. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "R$. ".number_format(($totalRs), 2, ",", "."),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
    }

    public function imprimirTicketUltimo(Request $request)
    {
        //$monedaSelec = $request->
        $cabVenta=DB::table('ventas as v')
        ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        ->join('clientes as c','c.id','=','v.cliente_id')
        //->join('cotizaciones as cot','cot.id','=','v.cotiz_id')
        ->join('vendedores as ven','ven.id','=','v.vendedor_id')
        ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo',
        'ven.nombre as vendedor'
        ,DB::raw('sum(vdet.cantidad_calculo*precio) as total'))
        ->orderBy('v.id', 'desc')
        ->groupBy('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo',
        'ven.nombre')
        ->first();
        //dd($cabVenta);
        if($cabVenta == null)
        {
            return back()->with('msj', 'AUN NO HA HECHO NINGUNA VENTA');
        }
        $venta_id = $cabVenta->id;

        /*mostrar detalles*/
        $detallesVenta=DB::table('ventas_det as vdet')
        ->join('productos as p','vdet.producto_id','=','p.id')
        ->select('vdet.cantidad','vdet.cantidad_calculo','vdet.precio','p.descripcion as producto','p.cod_barra as codigo')
        ->where('vdet.venta_id','=',$venta_id)
        ->orderBy('vdet.id', 'desc')->get();
        
        //CREAMOS EL TICKET
        $fpdf = new Fpdf('P','mm',array(75,200));
        $fpdf->AddPage();
        // CABECERA
        $fpdf->SetFont('Helvetica','',10);
        $fpdf->Cell(40,4,'MF - Moda Femenina',0,1,'C');
        $fpdf->Ln(1);
        $fpdf->SetFont('Helvetica','',8);
        // $fpdf->Cell(60,4,'Productos de limpieza, cobranzas y accesorios en gral.',0,1,'C');
        // $fpdf->Cell(60,4,'Calle Argentina, Arroyo Pora, Encarnacion',0,1,'C');
        // $fpdf->Cell(60,4,'Celular: 0982-111970',0,1,'C');
        // DATOS FACTURA        
        $fpdf->Ln(2);
        $fpdf->Cell(10,3,'Comprobante Nro: '.$cabVenta->nro_recibo,0,1,'');
        $fpdf->Cell(10,4,'Cliente: '.$cabVenta->nombre,0,1,'');
        $fpdf->Cell(10,4,'Fecha: '.date('d-m-Y', strtotime($cabVenta->fecha)),0,1,'');
        $fpdf->Cell(10,4,'Vendedor/a: '.$cabVenta->vendedor,0,1,'');
        $fpdf->Ln(0);
        // COLUMNAS
        $fpdf->SetFont('Helvetica', 'B', 8);
        $fpdf->Cell(20, 10, 'Articulo', 0);
        $fpdf->Cell(5, 10, 'Ud',0,0,'R');
        $fpdf->Cell(10, 10, 'Precio',0,0,'R');
        $fpdf->Cell(15, 10, 'Total',0,0,'R');
        $fpdf->Ln(8);
        $fpdf->Cell(50,0,'','T');
        $fpdf->Ln(0);                  
        
        $total_deuda=0;
        $total_pagos=0;
        $total_vencido=0;
        $total_cantidad=0;

        if($request->moneda == "TODO")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio), 2, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "USD ".number_format(($row->precio*$row->cantidad_calculo), 2, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total;
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);    
            $fpdf->Cell(25, 7, 'TOTAL USD. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            $fpdf->Ln(3); 
            $fpdf->Cell(25, 7, 'TOTAL Gs. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "Gs. ".number_format(($totalGs), 0, ",", "."),0,0,'R');
            $fpdf->Ln(3);
            $fpdf->Cell(25, 7, 'TOTAL $. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "$. ".number_format(($totalPs), 2, ",", "."),0,0,'R');
            $fpdf->Ln(3);
            $fpdf->Cell(25, 7, 'TOTAL R$. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "R$. ".number_format(($totalRs), 2, ",", "."),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($request->moneda == "USD")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio), 2, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "USD ".number_format(($row->precio*$row->cantidad_calculo), 2, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total * ($cabVenta->psVenta);
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);    
            $fpdf->Cell(25, 7, 'TOTAL USD. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "USD ".number_format(($cabVenta->total), 2, ".", ","),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($request->moneda == "PS")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio*$cabVenta->psVenta), 0, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "$ ".number_format(($row->precio*$row->cantidad_calculo*$cabVenta->psVenta), 0, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total * ($cabVenta->psVenta);
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "$ ".number_format(($totalPs), 0, ".", ","),0,0,'R');            
            //SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);   
            $fpdf->Cell(25, 7, 'TOTAL $. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "$. ".number_format(($totalPs), 0, ",", "."),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($request->moneda == "GS")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio), 0, ",", "."),0,0,'R');
                $fpdf->Cell(15, -5, "Gs. ".number_format(($row->precio*$row->cantidad_calculo), 0, ",", "."),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total;
            $totalPs = $cabVenta->total;
            $totalRs = $cabVenta->total;
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "Gs. ".number_format(($totalGs), 0, ",", "."),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);               
            $fpdf->Cell(25, 7, 'TOTAL Gs. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "Gs. ".number_format(($totalGs), 0, ",", "."),0,0,'R');
            $fpdf->Ln(3);           
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        if($request->moneda == "RS")
        {
            foreach($detallesVenta as $row)
            {
                $fpdf->SetFont('Helvetica', '', 7);
                $fpdf->MultiCell(20,4,$row->producto,0,'L'); 
                $fpdf->Cell(25, -5, number_format(($row->cantidad), 0, ",", "."),0,0,'R');
                $fpdf->Cell(10, -5, number_format(($row->precio*$cabVenta->rsVenta), 2, ".", ","),0,0,'R');
                $fpdf->Cell(15, -5, "R$ ".number_format(($row->precio*$row->cantidad_calculo*$cabVenta->rsVenta), 2, ".", ","),0,0,'R');
                $fpdf->Ln(3);

                $total_cantidad = $total_cantidad + $row->cantidad;
                
            }  
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(1);
            //HALLAMOS LOS PRECIOS EN OTRAS MONEDAS
            $totalGs = $cabVenta->total * $cabVenta->dolVenta;
            $totalPs = $cabVenta->total * ($cabVenta->psVenta);
            $totalRs = $cabVenta->total * ($cabVenta->rsVenta);
            
            $fpdf->SetFont('Helvetica', '', 7);
            $fpdf->MultiCell(20,4,"Total ITEMS",0,'L'); 
            $fpdf->Cell(25, -5, number_format(($total_cantidad), 0, ",", "."),0,0,'R');
            $fpdf->Cell(10, -5, "-",0,0,'R');
            $fpdf->Cell(15, -5, "R$ ".number_format(($totalRs), 2, ".", ","),0,0,'R');
            
            // SUMATORIO DE LOS PRODUCTOS Y EL IVA
            $fpdf->Ln(6);
            $fpdf->Cell(50,0,'','T');
            $fpdf->Ln(2);   
           
            $fpdf->Cell(25, 7, 'TOTAL R$. ', 0);    
            $fpdf->Cell(10, 7, '', 0);
            $fpdf->Cell(15, 7, "R$. ".number_format(($totalRs), 2, ",", "."),0,0,'R');
            $fpdf->Ln(4);
            $fpdf->Cell(50, 7, 'COMPROBANTE SIN VALOR CONTABLE. ', 0); 
            //$fpdf->Cell(0,10,'Printing line number '.$i,0,1);
            //$fpdf->Output('ficha.pdf','F');
            $fpdf->Output("OfficeForm.pdf","I");

            exit;
        }
        
    }

    public function show($id)
    {

        //dd($id);    
        // $ventas=DB::table('ventas as v')
        // ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        // ->join('clientes as c','c.id','=','v.cliente_id')
        // ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        // 'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo'
        // ,DB::raw('sum(vdet.cantidad_calculo*precio) as total'))
        // ->where('v.id','=',$id)
        // ->orderBy('v.id', 'desc')
        // ->groupBy('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        // 'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo')
        // ->first();

        $ventas=DB::table('ventas as v')
        ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        ->join('clientes as c','c.id','=','v.cliente_id')
        //->join('cotizaciones as cot','cot.id','=','v.cotiz_id')
        ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo',
        DB::raw('sum(vdet.cantidad_calculo*precio) as total'))
        ->where('v.id','=',$id)
        ->orderBy('v.id', 'desc')
        ->groupBy('v.id','v.fact_nro','v.fecha','v.total','c.nombre','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento','v.nro_recibo')
        ->first();

        /*mostrar detalles*/
        $detalles=DB::table('ventas_det as vdet')
        ->join('productos as p','vdet.producto_id','=','p.id')
        ->select('vdet.cantidad','vdet.cantidad_calculo','vdet.precio','p.descripcion as producto','p.cod_barra')
        ->where('vdet.venta_id','=',$id)
        ->orderBy('vdet.id', 'desc')->get();
        
        return view('factura.show',['ventas' => $ventas,'detalles' =>$detalles]);
    }

     public function factura_pdf($id){

        //dd($id);    
        /*mostrar compra*/
        //$id = $request->id;
        $ventas=DB::table('ventas as v')
        ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        ->join('clientes as c','c.id','=','v.cliente_id')
        ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','c.num_documento as ruc',
        'c.direccion','c.telefono','v.iva5',
        'v.iva10','v.ivaTotal','v.exenta','v.tipo_factura','c.num_documento')
        ->where('v.id','=',$id)
        ->orderBy('v.id', 'desc')
        ->get();

        /*mostrar detalles*/
        $detalles=DB::table('ventas_det as vdet')
        ->join('productos as p','vdet.producto_id','=','p.id')
        ->select('vdet.id','vdet.precio','p.descripcion as producto',
        DB::raw('sum(vdet.cantidad*precio) as subtotal'),
        DB::raw('sum(vdet.cantidad) as cantidad'))
        ->where('vdet.venta_id','=',$id)
        ->groupby('vdet.precio','p.descripcion','vdet.id')
        ->orderBy('vdet.id', 'desc')->get();
        //dd($detalles);
        $fechaahora2 = Carbon::parse($ventas[0]->fecha);
        //dd($fechaahora2);
        //EL DIA DE LA FECHA FORMATEADO CON CARGBON
        $diafecha = Carbon::parse($ventas[0]->fecha)->format('d');
        //dd($diafecha);
        $mesLetra = ($fechaahora2->monthName); //y con esta obtengo el mes al fin en espaﾃｱol!
        //OBTENER EL Aﾃ前
        $agefecha = Carbon::parse($fechaahora2)->year;
        //dd($agefecha);

        $tp = $ventas[0]->total;
        //dd($tp);
       
        $tot_pag_let=NumerosEnLetras::convertir($tp,'Guaranies',false,'Centavos');
        
        //dd($detalles);
        //return view('factura.facturaPDF',['ventas' => $ventas,'detalles' =>$detalles]);
        return $pdf= PDF::loadView('factura.facturaPDF',['ventas' => $ventas,'detalles' =>$detalles,
        'diafecha' =>$diafecha,'mesLetra' =>$mesLetra,'agefecha' =>$agefecha,'tot_pag_let' =>$tot_pag_let])
         ->setPaper([0, 0, 702.2835, 1150.087], 'portrait')
         ->stream('Factura'.$id.'.pdf');
    }
    
    public function edit(Request $request)
    {
        //dd($request);
        $cod_cajero = $request->cod_verificar;
        
        $cod_verificador = DB::table('users as u')
            ->select('u.verificador')
            ->where('u.idrol', '=', 1)
            ->first();

        $cod_verificador =  $cod_verificador->verificador;
        if(auth()->user()->idrol != 1)
            if($cod_cajero == $cod_verificador)
            {
                try 
                {

                    DB::beginTransaction();
                    //CAMBIAMOS EL ESTADO DE LA VENTA A ANULADO
                    $venta_id = $request->id_venta;
                    $venta = Venta::findOrFail($venta_id);            
                    $venta->estado = 1;
                    $venta->save();
                    //BUSCAMOS LOS DETALLES DE VENTA
                    $detallesVenta = DB::table('ventas_det as od')
                    ->select('od.id', 'od.venta_id','od.producto_id','od.cantidad')
                    ->where('od.venta_id','=',$venta_id)
                    ->get();
                    //dd($detallesVenta);

                    //RECORREMOS LOS DETALLES PARA ACTUALIZAR EL STOCK
                    for ($k = 0; $k < sizeof($detallesVenta); $k++) 
                    {
                        
                        $stockpro = Producto::findOrFail($detallesVenta[$k]->producto_id);
                        $stockpro->stock = $stockpro->stock + $detallesVenta[$k]->cantidad;
                        $stockpro->update();
                    }

                    //ANULAMOS EL PAGO
                    $id_pago= DB::select('select max(id) as id from pagos where venta_id= ?',[$venta_id]);
                    $id_pago = $id_pago[0]->id;

                    $pago = Pago::findOrFail($id_pago);            
                    $pago->pago_est = "A";
                    $pago->save();

                    DB::commit();
                    
                } catch (Exception $e) {

                    DB::rollBack();
                }
                return Redirect::to('factura')->with('msj', 'VENTA ANULADA');
            }
            else
            {
                return Redirect::to('factura')->with('msj', 'SOLICITAR CODIGO AL ADMINISTRADOR');
            }
        else
        {
            try 
                {

                    DB::beginTransaction();
                    //CAMBIAMOS EL ESTADO DE LA VENTA A ANULADO
                    $venta_id = $request->id_venta;
                    $venta = Venta::findOrFail($venta_id);            
                    $venta->estado = 1;
                    $venta->save();
                    //BUSCAMOS LOS DETALLES DE VENTA
                    $detallesVenta = DB::table('ventas_det as od')
                    ->select('od.id', 'od.venta_id','od.producto_id','od.cantidad')
                    ->where('od.venta_id','=',$venta_id)
                    ->get();
                    //dd($detallesVenta);

                    //RECORREMOS LOS DETALLES PARA ACTUALIZAR EL STOCK
                    for ($k = 0; $k < sizeof($detallesVenta); $k++) 
                    {
                        
                        $stockpro = Producto::findOrFail($detallesVenta[$k]->producto_id);
                        $stockpro->stock = $stockpro->stock + $detallesVenta[$k]->cantidad;
                        $stockpro->update();
                    }

                    //ANULAMOS EL PAGO
                    $id_pago= DB::select('select max(id) as id from pagos where venta_id= ?',[$venta_id]);
                    $id_pago = $id_pago[0]->id;

                    $pago = Pago::findOrFail($id_pago);            
                    $pago->pago_est = "A";
                    $pago->save();

                    DB::commit();
                    
                } catch (Exception $e) {

                    DB::rollBack();
                }
                return Redirect::to('factura')->with('msj', 'VENTA ANULADA');
        }

    }

    public function obtenerPrecio(Request $request)
    {
        $precio = DB::select('select * from productos where id = ?', [$request->producto_id]);
        error_log("PRECIO: ".json_encode($precio));
        $medida = DB::select('select unidad_medida from unidades_medida where id = ?', [$precio[0]->medida_id]);
        $valor = DB::select('select valor from unidades_medida where id = ?', [$precio[0]->medida_id]);

        return response()->json(['var'=>$precio,'var2'=>$medida,'var3'=>$valor]);
    }
    
    public function obtenerImagen(Request $request)
    {
        $imagen = DB::select('select imagen from electro_img where producto_id = ?', [$request->producto_id]);

        return response()->json(['var4'=>$imagen]);
    }

    public function destroy($id)
    {
        
         try{

            DB::beginTransaction();


            $venta = Venta::findOrFail($id);
            //dd($venta);

            if($venta->estado == 1){

                $pagos = DB::table('pagos as p')
                ->select('id')
                ->where('p.factura_id', '=', $id)
                ->get();

                //dd($pagos);

                for($i = 0 ; $i < sizeof($pagos); $i++)
                {
                    Pago::destroy($pagos[$i]->id);
                }

                $cuotas = DB::table('cuotas as c')
                ->select('id')
                ->where('c.factura_id', '=', $id)
                ->first();
                //dd(isset($cuotas));
                if(isset($cuotas)){
                    $cuotas_det = DB::table('cuotas_det as cdet')
                    ->select('id')
                    ->where('cdet.cuota_id', '=', $cuotas->id)
                    ->get();

                    for ($i = 0; $i < sizeof($cuotas_det); $i++) {
                        Cuota_det::destroy($cuotas_det[$i]->id);
                    }
                    Cuota::destroy($cuotas->id);

                }
                
                $ventas = DB::table('ventas as v')
                ->select('id')
                ->where('v.id', '=', $id)
                ->first();

                if(isset($ventas)){
                    $ventas_det = DB::table('ventas_det as vdet')
                    ->select('id')
                    ->where('vdet.venta_id', '=', $id)
                    ->get();
                //dd($ventas_det);

                    for ($i = 0; $i < sizeof($ventas_det); $i++) {
                        Venta_det::destroy($ventas_det[$i]->id);
                    }
                    Venta::destroy($id);

                }
                //dd($ventas_det);

            }
            else{
                return Redirect::to('factura')->with('msj', 'FACTURA DEBE SER ANULADA ANTES DE BORRAR');

            }

            DB::commit();

        } catch(Exception $e){
            
            DB::rollBack();
        }

        return Redirect::to('factura')->with('msj2', 'FACTURA ELIMINADA');
    }

    public function update_facNro( Request $request){     
        
        $nro_f = DB::table('ventas as v')
        ->select('v.fact_nro')
        ->where('v.fact_nro','=',$request->fact_nro)
        ->where('v.fact_nro','>',0)
        ->where('v.estado','=',0)
        ->first();

        if($nro_f != null){
            return Redirect::to('factura')->with('msj', 'N° FACTURA REPETIDO');
        }
        else{
            $venta = Venta::findOrFail($request->id_venta);
            $venta->fact_nro = $request->fact_nro;
            $venta->save();

            return Redirect::to('factura')->with('msj2', 'FACTURA ACTUALIZADA');
        }       

    }
     public function factura_pdf_orden($id)
     {
        //$id = $request->id;
        $ventas=DB::table('ventas as v')
        ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        ->join('clientes as c','c.id','=','v.cliente_id')
        ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','c.num_documento as ruc',
        'c.direccion','c.telefono','v.iva5','v.iva10','v.ivaTotal','v.exenta','v.tipo_factura',
        'c.num_documento','v.nro_recibo')
        ->where('v.id','=',$id)
        ->orderBy('v.id', 'desc')
        ->get();

        /*mostrar detalles*/
        $detalles=DB::table('ventas_det as vdet')
        ->join('productos as p','vdet.producto_id','=','p.id')
        ->select('vdet.id','vdet.precio','p.descripcion as producto',
        DB::raw('sum(vdet.cantidad*precio) as subtotal'),
        DB::raw('sum(vdet.cantidad) as cantidad'))
        ->where('vdet.venta_id','=',$id)
        ->groupby('vdet.precio','p.descripcion','vdet.id')
        ->orderBy('vdet.id', 'desc')->get();
        //dd($detalles);
        $fechaahora2 = Carbon::parse($ventas[0]->fecha);
        //dd($fechaahora2);
        //EL DIA DE LA FECHA FORMATEADO CON CARGBON
        $diafecha = Carbon::parse($ventas[0]->fecha)->format('d');
        //dd($diafecha);
        $mesLetra = ($fechaahora2->monthName); //y con esta obtengo el mes al fin en espaﾃｱol!
        //OBTENER EL Aﾃ前
        $agefecha = Carbon::parse($fechaahora2)->year;
        //dd($agefecha);

        $tp = $ventas[0]->total;
        //dd($tp);
       
        $tot_pag_let=NumerosEnLetras::convertir($tp,'Guaranies',false,'Centavos');
        
        //dd($detalles);
        //return view('factura.facturaPDF',['ventas' => $ventas,'detalles' =>$detalles]);
        return $pdf= PDF::loadView('factura.facturaOrdenPDF',['ventas' => $ventas,'detalles' =>$detalles,
        'diafecha' =>$diafecha,'mesLetra' =>$mesLetra,'agefecha' =>$agefecha,'tot_pag_let' =>$tot_pag_let])
         ->setPaper([0, 0, 702.2835, 1150.087], 'portrait')
         ->stream('Factura'.$id.'.pdf');
    }

    public function ultimo_recibo()
     {
        //$id = $request->id;
        $ventas=DB::table('ventas as v')
        ->join('ventas_det as vdet','v.id','=','vdet.venta_id')
        ->join('clientes as c','c.id','=','v.cliente_id')
        ->select('v.id','v.fact_nro','v.fecha','v.total','c.nombre','c.num_documento as ruc',
        'c.direccion','c.telefono','v.iva5','v.iva10','v.ivaTotal','v.exenta','v.tipo_factura',
        'c.num_documento','v.nro_recibo')
        //->where('v.id','=',$id)
        ->orderBy('v.id', 'desc')
        ->first();
        dd($ventas);
        if($ventas == null)
        {
            return back()->with('msj', 'AUN NO HA HECHO NINGUNA VENTA');
        }

        /*mostrar detalles*/
        $detalles=DB::table('ventas_det as vdet')
        ->join('productos as p','vdet.producto_id','=','p.id')
        ->select('vdet.id','vdet.precio','p.descripcion as producto',
        DB::raw('sum(vdet.cantidad*precio) as subtotal'),
        DB::raw('sum(vdet.cantidad) as cantidad'))
        ->where('vdet.venta_id','=',$ventas->id)
        ->groupby('vdet.precio','p.descripcion','vdet.id')
        ->orderBy('vdet.id', 'desc')->get();
        //dd($detalles);
        $fechaahora2 = Carbon::parse($ventas->fecha);
        //dd($fechaahora2);
        //EL DIA DE LA FECHA FORMATEADO CON CARGBON
        $diafecha = Carbon::parse($ventas->fecha)->format('d');
        //dd($diafecha);
        $mesLetra = ($fechaahora2->monthName); //y con esta obtengo el mes al fin en espaﾃｱol!
        //OBTENER EL Aﾃ前
        $agefecha = Carbon::parse($fechaahora2)->year;
        //dd($agefecha);

        $tp = $ventas->total;
        //dd($tp);
       
        $tot_pag_let=NumerosEnLetras::convertir($tp,'Guaranies',false,'Centavos');
        
        //dd($detalles);
        //return view('factura.facturaPDF',['ventas' => $ventas,'detalles' =>$detalles]);
        return $pdf= PDF::loadView('factura.ultimoRecibo',['ventas' => $ventas,'detalles' =>$detalles,
        'diafecha' =>$diafecha,'mesLetra' =>$mesLetra,'agefecha' =>$agefecha,'tot_pag_let' =>$tot_pag_let])
         ->setPaper([0, 0, 702.2835, 1150.087], 'portrait')
         ->stream('Factura'.$ventas->id.'.pdf');
    }

    public function buscador(Request $request0)
    {
        $data=trim($request->valor);
        $result=DB::table('productos')
        ->where('descripcion','LIKE','%'.$data.'%')
        ->orWhere('cod_barra','LIKE','%'.$data.'%')
        ->limit(5)
        ->get();
        return response()->json([
            "result"=> $result
        ]);
    }

}