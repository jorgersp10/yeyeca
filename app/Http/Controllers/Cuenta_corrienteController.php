<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cuenta_corriente;
use Illuminate\Support\Facades\Redirect;
use DB;
class Cuenta_corrienteController extends Controller
{
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
            $cuentas_corriente=DB::table('cuentas_corriente as cc')
            ->join('bancos as b','b.id','=','cc.banco_id')
            ->select('cc.id as id','cc.nro_cuenta','cc.banco_id','b.descripcion as banco','cc.saldo')
            ->where('cc.nro_cuenta','LIKE','%'.$sql.'%')
            ->orderBy('cc.id','asc')
            ->simplePaginate(10);

            //dd($clientes);
            return view('cuenta_corriente.index',["cuentas_corriente"=>$cuentas_corriente,"buscarTexto"=>$sql]);
            //return $clientes;
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
         /*listar los productos en ventana modal*/
        $bancos=DB::table('bancos as c')
        ->select('id','descripcion')
        ->get(); 

        return view('cuenta_corriente.create',["bancos"=>$bancos]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $cuenta_corriente= new Cuenta_corriente();        
        $cuenta_corriente->nro_cuenta = $request->nro_cuenta;
        $cuenta_corriente->banco_id = $request->banco_id;
        $cuenta_corriente->saldo = 0;

        $cuenta_corriente->save();
        return Redirect::to("cuenta_corriente");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
         $cuentas_corriente=DB::table('cuentas_corriente as cc')
        ->join('bancos as b','b.id','=','cc.banco_id')
        ->select('cc.id as id','cc.nro_cuenta','cc.banco_id','b.descripcion as banco','cc.saldo')
        ->where('cc.id','=',$id)
        //->orderBy('cc.id','asc')
        ->first();

        $bancos=DB::table('bancos as c')
        ->select('id','descripcion')
        ->get(); 

        //dd($clientes);
        return view('cuenta_corriente.show',["cuentas_corriente"=>$cuentas_corriente,"bancos"=>$bancos]);
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
        $cuenta_corriente= Cuenta_corriente::findOrFail($request->id_cuenta);
        $cuenta_corriente->nro_cuenta = $request->nro_cuenta;
        $cuenta_corriente->banco_id = $request->banco_id;
        $cuenta_corriente->saldo = 0;

        $cuenta_corriente->save();
        return Redirect::to("cuenta_corriente");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
         try{

            DB::beginTransaction();

            $cheques_emitido = DB::table('cheques_emitido as c')
                ->select('id')
                ->where('c.cuenta_corriente', '=', $id)
                ->get();
            
            if($cheques_emitido->isEmpty()){
                
                Cuenta_corriente::destroy($id);

            }
            else{
                return Redirect::to("cuenta_corriente")->with('msj', 'HAY CHEQUES ASOCIADOS A ESA CUENTA, PROCEDA A ELIMINAR ESOS CHEQUES PRIMERO');
            }

            DB::commit();

        } catch(Exception $e){
            
            DB::rollBack();
        }
        return Redirect::to("cuenta_corriente")->with('msj2', 'CUENTA CORRIENTE ELIMINADA');
    }
}
