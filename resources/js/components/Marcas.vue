<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <!-- inicio do card de busca-->
                <card-component titulo="Busca de marcas">
                    <template v-slot:conteudo>

                        <div class="row g-3">
                            <div class="col mb-3">

                                <input-container-component titulo="ID" id="inputId" id-help="idHelp"
                                    texto-ajuda="Opcional. Informe o ID da marca">
                                    <input type="number" class="form-control" id="inputId" aria-describedby="idHelp"
                                        placeholder="ID" v-model="busca.id">
                                </input-container-component>

                            </div>
                            <div class="col mb-3">

                                <input-container-component titulo="Nome da marca" id="inputNome" id-help="nomeHelp"
                                    texto-ajuda="Informe o Nome da marca">
                                    <input type="text" class="form-control" id="inputNome" aria-describedby="nomeHelp"
                                        placeholder="Nome da marca" v-model="busca.nome">
                                </input-container-component>
                            </div>
                        </div>
                    </template>

                    <template v-slot:rodape>
                        <button type="submit" class="btn btn-primary btn-sm float-end" @click="pesquisar()">Pesquisar</button>
                    </template>
                </card-component>
                <!-- fim do card de busca-->

                <!-- inicio do card de listagem de marcas -->
                <card-component titulo="Relação de marcas">
                    <template v-slot:conteudo>
                        <table-component
                            :dados="marcas.data"
                            :visualizar="{visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaVisualizar'}"
                            :atualizar="true"
                            :remover="{visivel: true, dataToggle: 'modal', dataTarget: '#modalMarcaRemover'}"
                            :titulos="{
                            id: { titulo: 'ID', tipo: 'texto' },
                            nome: { titulo: 'Nome', tipo: 'texto' },
                            imagem: { titulo: 'Imagem', tipo: 'imagem' },
                            created_at: { titulo: 'Criação', tipo: 'data' },
                        }"></table-component>
                    </template>
                    <template v-slot:rodape>
                        <div class="row g-2">
                            <div class="col-9">
                                <paginate-component>
                                    <li v-for="l, key in marcas.links" :key="key"
                                        :class="l.active ? 'page-item active' : 'page-item'"
                                        @click="paginacao(l)"
                                    >
                                        <a class="page-link" v-html="l.label"></a>
                                    </li>
                                </paginate-component>
                            </div>

                            <div class="col">
                                <button type="button" class="btn btn-primary btn-sm float-end" data-toggle="modal"
                                    data-target="#modalMarca">Adicionar</button>
                            </div>

                        </div>
                    </template>
                </card-component>
                <!-- fim do card de listagem de marcas -->
            </div>
        </div>

        <!-- Inicio do modal de inclusão de marca-->
        <modal-component id="modalMarca" titulo="Adicionar marca">
            <template v-slot:alertas>
                <alert-component tipo="success" :detalhes="transacaoDetalhes" titulo="Cadastro realizado com sucesso"
                    v-if="transacaoStatus == 'adicionado'"></alert-component>
                <alert-component tipo="danger" :detalhes="transacaoDetalhes" titulo="Erro ao cadastrar a Marca"
                    v-if="transacaoStatus == 'erro'"></alert-component>
            </template>
            <template v-slot:conteudo>
                <div class="form-group">
                    <input-container-component titulo="Nome da marca" id="novoNome" id-help="novoNomeHelp"
                        texto-ajuda="Informe o Nome da marca">
                        <input type="text" class="form-control" id="novoNome" aria-describedby="novoNomeHelp"
                            placeholder="Nome da marca" v-model="nomeMarca">
                    </input-container-component>
                    {{ nomeMarca }}
                </div>

                <div class="form-group">
                    <input-container-component titulo="imagem" id="novaImagem" id-help="novaImagemHelp"
                        texto-ajuda="Selecione uma imagem no formato PNG">
                        <input type="file" class="form-control-file" id="novaImagem" aria-describedby="novaImagemHelp"
                            placeholder="Selecione uma imagem" @change="carregarImagem($event)">
                    </input-container-component>
                    {{ arquivoImagem }}
                </div>
            </template>

            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
                <button type="button" class="btn btn-primary" @click="salvar()">Salvar</button>
            </template>
        </modal-component>
        <!-- fim do modal de inclusão de marca-->

        <!-- Inicio do modal de visualização de marca-->
        <modal-component id="modalMarcaVisualizar" titulo="Visualizar marca">
            <template v-slot:alertas></template>
            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

                <input-container-component titulo="Imagem">
                    <img :src="'storage/'+$store.state.item.imagem" v-if="$store.state.item.imagem" disabled>
                </input-container-component>

                <input-container-component titulo="Data de criação">
                <input type="text" class="form-control" :value="$store.state.item.created_at" disabled>
                </input-container-component>
            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- fim do modal de visualização de marca-->

         <!-- Inicio do modal de remoção de marca-->
         <modal-component id="modalMarcaRemover" titulo="Remover marca">
            <template v-slot:alertas></template>
            <template v-slot:conteudo>
                <input-container-component titulo="ID">
                    <input type="text" class="form-control" :value="$store.state.item.id" disabled>
                </input-container-component>

                <input-container-component titulo="Nome da marca">
                    <input type="text" class="form-control" :value="$store.state.item.nome" disabled>
                </input-container-component>

            </template>
            <template v-slot:rodape>
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Fechar</button>
            </template>
        </modal-component>
        <!-- fim do modal de remoção de marca-->
    </div>
</template>

<script>
export default {
    computed: {
        token() {

            let token = document.cookie.split(';').find(indice => {
                return indice, indice.includes('token=')
            })

            token = token.split('=')[1]
            token = 'Berear ' + token

            return token
        }
    },
    data() {
        return {
            urlBase: 'http://localhost:8000/api/v1/marca',
            urlPaginacao: '',
            urlFiltro: '',
            nomeMarca: '',
            arquivoImagem: [],
            transacaoStatus: '',
            transacaoDetalhes: {},
            marcas: { data: [] },
            busca: { id: '', nome: ''}
        }
    },
    methods: {
        pesquisar() {


            let filtro =''

            for(let chave in this.busca) {
                if(this.busca[chave]) {
                   if(filtro != '') {
                       filtro +=";"
                   }
                   filtro += chave + ':like:' + this.busca[chave]
                }
            }
            if(filtro != '') {
                this.urlPaginacao = 'page1'
                this.urlFiltro = '&filtro='+filtro
            } else {
                this.urlFiltro =''
            }
            this.carregarLista()
        },
        paginacao(l) {
            if(l.url) {
              //  this.urlBase = l.url //ajustando a url de consulta com o parametro de pagina
              this.urlPaginacao = l.url.split('?')[1]
                this.carregarLista() //requisitando novamente os dados para nossa api
            }
        },
        carregarLista() {

            let config = {
                headers: {
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }

            let url = this.urlBase + '?' + this.urlPaginacao + this.urlFiltro
            console.log(url)

            axios.get(url)
                .then(response => {
                    this.marcas = response.data
                })
                .catch(errors => {
                    console.log(errors);
                })
        },
        carregarImagem(e) {
            this.arquivoImagem = e.target.files

        },
        salvar() {
            console.log(this.nomeMarca, this.arquivoImagem[0])

            let formData = new FormData();
            formData.append('nome', this.nomeMarca)
            formData.append('imagem', this.arquivoImagem[0])//0 para unica imagem, se for mais retira o [0]

            let config = {
                headers: {
                    'Content-Type': 'multipart/form-data',
                    'Accept': 'application/json',
                    'Authorization': this.token
                }
            }
            axios.post(this.urlBase, formData, config)
                .then(response => {
                    this.transacaoStatus = 'adicionado'
                    this.transacaoDetalhes = {
                        mensagem: 'ID do registro: ' + detalhes.data.id
                    }

                    console.log(response)
                })
                .catch(errors => {
                    this.transacaoStatus = 'erro'
                    this.transacaoDetalhes = {
                        mensagem: errors.response.data.message,
                        dados: errors.response.data.errors
                    }
                    //errors.response.data.message
                })
        }
    },
    mounted() {
        this.carregarLista()
    }

}
</script>

