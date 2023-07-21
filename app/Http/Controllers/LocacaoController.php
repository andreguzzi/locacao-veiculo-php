<?php

namespace App\Http\Controllers;

use App\Models\Locacao;
use App\Http\Requests\StoreLocacaoRequest;
use App\Http\Requests\UpdateLocacaoRequest;

class LocacaoController extends Controller
{
    public function __construct(Locacao $locacao)
    {
        $this->locacao = $locacao;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $locacaoRepository = new LocacaoRepository($this->locacao);

        if($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $locacaoRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $locacaoRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
           $locacaoRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $locacaoRepository->selectAtributos($request->atributos);

        }

        return response()->json($locacaoRepository->getResultado(), 200);
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
     * @param  \App\Http\Requests\StoreLocacaoRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        /$request->validate($this->locacao->rules(), $this->locacao->feedback());
        //stateless
    
        $locacao = $this->locacao->create([
            'nome' => $request->nome,
           
        ]);

   
        return response()->json($locacao, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $locacao = $this->locacao->with('modelos')->find($id);
        if ($locacao === null)  //se é identico
        {
            return response()->json(['error' => 'locacao não encontrada'], 404);
        }

        return $locacao;
    }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function edit(Locacao $locacao)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateLocacaoRequest  $request
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function update($request, Locacao $id)
    {
        $locacao = $this->locacao->find($id);

        if ($locacao === null) {
            return response()->json(['erro' => 'Impossivel realizar a atualização. O recurso solicidado não existe'], 404);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();


            //percorrendo todas as regras definidas no model
            foreach ($locacao->rules() as $input => $locacao) {

                //coletar apenas as regras aplicaveis aos parametros parciais da requisição PATCH
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas);
        } else {

            $request->validate($locacao->rules());
        }



        return response()->json($locacao, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Locacao  $locacao
     * @return \Illuminate\Http\Response
     */
    public function destroy(Locacao $locacao)
    {
        $locacao = $this->locacao->find($id);

        if ($locacao === null) {       //reponse é um helper para manipular o codigo do status http
            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicidado não existe'], 404);
        }


        $locacao->delete();
        return ['msg' => 'A locacao foi removida com Sucesso!'];
    }
}
