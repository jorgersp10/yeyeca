<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Producto;
use Illuminate\Support\Facades\Redirect;
use DB;
class ProductoController extends Controller
{
    //
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if($request){
            //Buscador de texto en el view y tambien la consula para mostrar datos en el index
            $sql=trim($request->get('buscarTexto'));
            $sql = str_replace(" ", "%", $sql);
            $productos=DB::table('productos')
            ->join('unidades_medida as uni','uni.id','=','productos.medida_id')
            //->leftjoin('electro_img as img','img.producto_id','=','productos.id')
            ->select('productos.id','productos.descripcion','productos.ArtCode','productos.stock'
            ,'productos.precio_compra','productos.precio_venta','productos.precio_min','productos.precio_max',
            'uni.unidad_medida','productos.cod_barra')
            ->where('productos.descripcion','LIKE','%'.$sql.'%')
            ->orWhere('productos.cod_barra','LIKE','%'.$sql.'%')
            ->orderBy('productos.ordenar','asc')
            ->orderBy('productos.descripcion','asc')
            ->paginate(10)
            ->withQueryString();

            $cotizaciones=DB::table('cotizaciones')
            ->select('id','dolVenta','psVenta','rsVenta')
            ->orderby('id','desc')
            ->first();

            //dd($ruta);
            return view('producto.index',["productos"=>$productos,"cotizaciones"=>$cotizaciones,
            "buscarTexto"=>$sql]);
            //return $clientes;
        }
        
    }
    

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //dd($request);
        $producto= new Producto();
        $producto->descripcion = strtoupper($request->descripcion);
        if ($producto->stock=="null")
            $producto->stock = 0;
        else
            $producto->stock = $request->stock;

        //dd($producto->stock);
        // $prodcod=DB::table('productos')
        // ->select('ArtCode')->where()

        $prodcod = Producto::whereRaw('ArtCode = (select max(`ArtCode`) from productos)')->first();

        //dd($prodcod);
        if($prodcod == null)
        {
             $ArtCo = '0000001';
        }
        else{
            //dd($prodcod->ArtCode);
            if ($prodcod->ArtCode < 9) {
                $ArtCo = '000000' . strval($prodcod->ArtCode + 1);
            } elseif ($prodcod->ArtCode < 99) {
                $ArtCo = '00000' . strval($prodcod->ArtCode + 1);
            } elseif ($prodcod->ArtCode < 999) {
                $ArtCo = '0000' . strval($prodcod->ArtCode + 1);
            } elseif ($prodcod->ArtCode < 9999) {
                $ArtCo = '000' . strval($prodcod->ArtCode + 1);
            } elseif ($prodcod->ArtCode < 99999) {
                $ArtCo = '00' . strval($prodcod->ArtCode + 1);
            }
        //dd($ArtCo);
        }
        
        $producto->ArtCode = $ArtCo;

        if ($request->cod_barra == null) {
            $producto->cod_barra = 0;
        } else {
            $producto->cod_barra = $request->cod_barra;
        }       

        if ($request->precio_venta==null)
            $producto->precio_venta = 0;
        else
            $producto->precio_venta = str_replace(".","",$request->precio_venta);

        if ($request->precio_tarjeta==null)
            $producto->precio_tarjeta = 0;
        else
            $producto->precio_tarjeta = str_replace(".","",$request->precio_tarjeta);
            
        if ($request->precio_compra==null)
            $producto->precio_compra = 0;
        else
            $producto->precio_compra = str_replace(".","",$request->precio_compra);
        
        if ($request->precio_min==null)
            $producto->precio_min = 0;
        else
        $producto->precio_min = str_replace(".","",$request->precio_min);

        if ($request->precio_max==null)
            $producto->precio_max = 0;
        else
            $producto->precio_max = str_replace(".","",$request->precio_max);

        if ($request->precio_mayor==null)
            $producto->precio_mayor = 0;
        else
            $producto->precio_mayor = str_replace(".","",$request->precio_mayor);
        
        if ($request->cantidad_mayor==null)
            $producto->cantidad_mayor = 0;
        else
            $producto->cantidad_mayor = str_replace(".","",$request->cantidad_mayor);
        
        if ($request->ordenar==null)
            $producto->ordenar = 0;
        else
            $producto->ordenar = $request->ordenar;

        if ($request->comentarios==null)
            $producto->comentarios = "";
        else   
        $producto->comentarios = strtoupper($request->comentarios);
        $producto->medida_id = 5;
        $producto->user_mod = auth()->user()->id;

        $producto->save();
        return Redirect::to("producto")->with('msj2', 'PRODUCTO REGISTRADO');
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
        //
        //dd($request);
        $producto=Producto::findOrFail($request->id_producto);
        $producto->descripcion = strtoupper($request->descripcion);
        if ($producto->stock == null) {
            $producto->stock = 0;
        } else {
            $producto->stock = $request->stock;
        }
        if ($request->ArtCode == null) {
            $producto->ArtCode = 0;
        } else {
            $producto->ArtCode = str_replace(".", "", $request->ArtCode);
        }

        if ($request->cod_barra == null) {
            $producto->cod_barra = 0;
        } else {
            $producto->cod_barra = $request->cod_barra;
        }

        //dd($request);
        if ($request->precio_venta==null)
            $producto->precio_venta = 0;
        else
            $producto->precio_venta = str_replace(".","",$request->precio_venta);

        if ($request->precio_tarjeta==null)
            $producto->precio_tarjeta = 0;
        else
            $producto->precio_tarjeta = str_replace(".","",$request->precio_tarjeta);
            
        if ($request->precio_compra==null)
            $producto->frente = 0;
        else
            $producto->precio_compra = str_replace(".","",$request->precio_compra);
        
        if ($request->precio_min==null)
            $producto->contrafrente = 0;
        else
        $producto->precio_min = str_replace(".","",$request->precio_min);

        if ($request->precio_max==null)
            $producto->precio_max = 0;
        else
            $producto->precio_max = str_replace(".","",$request->precio_max);

        if ($request->precio_mayor==null)
            $producto->precio_mayor = 0;
        else
            $producto->precio_mayor = str_replace(".","",$request->precio_mayor);
        
        if ($request->cantidad_mayor==null)
            $producto->cantidad_mayor = 0;
        else
            $producto->cantidad_mayor = str_replace(".","",$request->cantidad_mayor);

        if ($request->ordenar==null)
            $producto->ordenar = 0;
        else
            $producto->ordenar = $request->ordenar;

        if ($request->comentarios==null)
            $producto->comentarios = "";
        else   
        $producto->comentarios = strtoupper($request->comentarios);
        $producto->medida_id = 5;
        $producto->user_mod = auth()->user()->id;
        //dd($producto);
        $producto->save();
        return Redirect::to("producto")->with('msj2', 'PRODUCTO ACTUALIZADO');
    }
    

    public function show($id)
    {
        $producto=DB::table('productos as p')
        //$producto=Producto::join('categorias','productos.categoria_id','=','categorias.id')
        ->select('p.id','p.descripcion','p.ArtCode','p.stock'
        ,'p.precio_compra','p.precio_venta','p.precio_min',
        'p.precio_max','p.comentarios','p.precio_mayor','p.cantidad_mayor',
        'medida_id','cod_barra','ordenar','p.precio_tarjeta')
        ->where('p.id','=',$id)
        ->orderBy('p.descripcion','desc')->first();

        $cotizaciones=DB::table('cotizaciones')
            ->select('id','dolVenta','psVenta','rsVenta')
            ->orderby('id','desc')
            ->first();

        $imagenes=DB::table('electro_img')
        ->select('id','imagen','tipo')
        ->where('producto_id','=',$id)
        ->get();

        return view('producto.show',["producto"=>$producto,"cotizaciones"=>$cotizaciones,
        "imagenes"=>$imagenes]);

    }
    
    public function destroy($id)
    {
        //dd($id);
        Producto::destroy($id);
        return Redirect::to("producto");
    }
}