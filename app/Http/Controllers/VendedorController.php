<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vendedor;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Carbon\Carbon;
use DateTime;
use DB;

class VendedorController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(request $request)
    {
        if($request){

            $sql=trim($request->get('buscarTexto'));
            $vendedores=DB::table('vendedores as v')
            ->select('v.id','v.nombre','v.num_documento','v.porcentaje')
            ->where('v.nombre','LIKE','%'.$sql.'%')
            ->orwhere('v.num_documento','LIKE','%'.$sql.'%')
            ->orderBy('v.id','desc')
            ->paginate(10);

            return view('vendedor.index',["vendedores"=>$vendedores,"buscarTexto"=>$sql]);
        
            //return $vendedores;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('vendedor.create');
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
        $vendedor= new Vendedor();
        $vendedor->nombre = strtoupper($request->nombre);
        $vendedor->num_documento = $request->num_documento;
        $vendedor->porcentaje = str_replace(",",".",$request->porcentaje);
        $vendedor->condicion = '0'; 

        $vendedor->save();
        return Redirect::to("vendedor")->with('msj2','VENDEDOR/A REGISTRADO');
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
        $vendedor=DB::table('vendedores as v')
        ->select('v.id','v.nombre','v.num_documento','v.porcentaje')
        ->orwhere('v.id','=',$id)
        ->first();

        return view('vendedor.edit',["vendedor"=>$vendedor]);
        
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
        $vendedor= Vendedor::findOrFail($request->id_vendedor);
        $vendedor->nombre = strtoupper($request->nombre);
        $vendedor->num_documento = $request->num_documento;
        $vendedor->porcentaje = str_replace(",",".",$request->porcentaje);
        $vendedor->condicion = '0'; 

        $vendedor->update();
        return Redirect::to("vendedor")->with('msj2','VENDEDOR/A ACTUALIZADO');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        $vendedor= Vendedor::findOrFail($request->id_vendedor);
         
         if($vendedor->condicion=="1"){

                $vendedor->condicion= '0';
                $vendedor->save();
                return Redirect::to("vendedor");

           }else{

                $vendedor->condicion= '1';
                $vendedor->save();
                return Redirect::to("vendedor");

            }
    }
}