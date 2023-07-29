<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use App\Repositories\MarcaRepository;

class MarcaController extends Controller
{
    protected $marca;

    public function __construct(Marca $marca)
    {
        $this->marca = $marca;
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {

        $marcaRepository = new MarcaRepository($this->marca);

        if($request->has('atributos_modelos')) {
            $atributos_modelos = 'modelos:id,'.$request->atributos_modelos;
            $marcaRepository->selectAtributosRegistrosRelacionados($atributos_modelos);
        } else {
            $marcaRepository->selectAtributosRegistrosRelacionados('modelos');
        }

        if ($request->has('filtro')) {
           $marcaRepository->filtro($request->filtro);
        }

        if($request->has('atributos')) {
            $marcaRepository->selectAtributos($request->atributos);

        }

        return response()->json($marcaRepository->getResultadoPaginado(3), 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // $marca = Marca::create($request->all());
        //nome
        //imagem
        //para um erro de campo requerido
        $request->validate($this->marca->rules(), $this->marca->feedback());
        //stateless
        $imagem = $request->file('imagem');

        //para armazenar a imagem é informado 2 parametros, se em filesystem estiver local pode omitir disco
        //$image->store('path','disco');
        //a pasta imagens pode ser qualquer diretório
        $imagem_urn = $imagem->store('imagens', 'public');

        //dd($imagem_urn);
        //podem ser acessados
        //dd($request->nome);
        //dd($request->get('nome'));
        //dd($request->input('nome'));
        //dd($request->imagem);
        //dd($request->file('imagem');

        //existem duas formas de recuperar as imagens
        // 1
        $marca = $this->marca->create([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);

        //2
        /*
        $marca->nome = $request->nome;
        $marca->imagem = $imagem_urn;
        $marca->save();
        */

        //$marca = $this->marca->create($request->all());
        return response()->json($marca, 201);
    }

    /**
     * Display the specified resource.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $marca = $this->marca->with('modelos')->find($id);
        if ($marca === null)  //se é identico
        {
            return response()->json(['error' => 'Marca não encontrada'], 404);
        }

        return $marca;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Marca  $marca
     * @return \Illuminate\Http\Response
     */
    public function edit(Marca $marca)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //$marca->update($request->all());
        $marca = $this->marca->find($id);

        if ($marca === null) {
            return response()->json(['erro' => 'Impossivel realizar a atualização. O recurso solicidado não existe'], 404);
        }

        if ($request->method() === 'PATCH') {

            $regrasDinamicas = array();


            //percorrendo todas as regras definidas no model
            foreach ($marca->rules() as $input => $regra) {

                //coletar apenas as regras aplicaveis aos parametros parciais da requisição PATCH
                if (array_key_exists($input, $request->all())) {
                    $regrasDinamicas[$input] = $regra;
                }
            }

            $request->validate($regrasDinamicas, $marca->feedback());
        } else {

            $request->validate($marca->rules(), $marca->feedback());
        }

        //remove o arquivo antigo caso um novo for enviado no request
        if($request->file('imagem')) {
            Storage::disk('public')->delete($marca->imagem);
        }


        $imagem = $request->file('imagem');
        $imagem_urn = $imagem->store('imagens', 'public');

        //preencher o objeto $marca com os dados do request
        $marca->fill($request->all());
        $marca->imagem = $imagem_urn;

        $marca->save();
        /*dd($marca->getAttributes());
        $marca->update([
            'nome' => $request->nome,
            'imagem' => $imagem_urn
        ]);
        */

        return response()->json($marca, 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  Integer
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $marca = $this->marca->find($id);

        if ($marca === null) {       //reponse é um helper para manipular o codigo do status http
            return response()->json(['erro' => 'Impossivel realizar a exclusão. O recurso solicidado não existe'], 404);
        }


          //remove o arquivo antigo
            Storage::disk('public')->delete($marca->imagem);


        $marca->delete();
        return ['msg' => 'A marca foi removida com Sucesso!'];
    }
}
