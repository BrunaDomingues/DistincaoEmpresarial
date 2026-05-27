<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
             Responder: {{ $formulario->titulo }}
        </h2>
    </x-slot>
@php
    $fatoresConhecimento = $fatoresSatisfacao->where('resposta_obrigatoria', false)->values();
    $fatoresMotivo = $fatoresSatisfacao->where('resposta_obrigatoria', true)->values();
    $fatoresConhecimentoIds = $fatoresConhecimento->pluck('id')->values();
    $fatoresMotivoIds = $fatoresMotivo->pluck('id')->values();
    $regrasProgresso = [];
    foreach ($formulario->passos->sortBy('ordem') as $passo) {
        foreach ($passo->perguntas as $pergunta) {
            $regrasProgresso[$pergunta->id] = [
                'obrigatorio' => (bool) $pergunta->obrigatorio,
                'usa_fatores' => (bool) $pergunta->usa_fatores_satisfacao,
                'fatores_conhecimento' => $pergunta->usa_fatores_satisfacao
                    ? $fatoresConhecimentoIds->all()
                    : [],
                'fatores_motivo' => $pergunta->usa_fatores_satisfacao
                    ? $fatoresMotivoIds->all()
                    : [],
            ];
        }
    }
@endphp
    <div class="py-6">
        <div class="max-w-85 mx-auto sm:px-6 lg:px-8 relative" x-data="formularioResponder()" x-init="init()">

            {{-- Bloqueio sem localização --}}
            <div x-show="localizacaoStatus !== 'ok'" x-cloak
                class="absolute inset-0 z-40 flex items-start justify-center bg-gray-900/40 px-4 pt-8 sm:pt-16">
                <div class="w-full max-w-md rounded-lg bg-white p-6 shadow-xl dark:bg-gray-800">
                    <div class="flex items-start gap-3">
                        <i class="bx bx-map-pin text-3xl text-indigo-600 shrink-0"></i>
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900 dark:text-white">Localização necessária</h3>
                            <p class="mt-2 text-sm text-gray-600 dark:text-gray-300" x-text="mensagemLocalizacao"></p>
                        </div>
                    </div>
                    <div class="mt-4 flex flex-col gap-2 sm:flex-row">
                        <button type="button" @click="solicitarLocalizacao()"
                            class="flex-1 rounded bg-indigo-600 px-4 py-2 text-sm font-medium text-white hover:bg-indigo-700"
                            :disabled="localizacaoStatus === 'loading'">
                            <span x-show="localizacaoStatus !== 'loading'">Ativar localização</span>
                            <span x-show="localizacaoStatus === 'loading'">Obtendo localização...</span>
                        </button>
                    </div>
                    <p class="mt-3 text-xs text-gray-500 dark:text-gray-400">
                        No celular, permita o acesso à localização nas configurações do navegador ou do site.
                    </p>
                </div>
            </div>

            {{-- Barra de Progresso --}}
            <div class="fixed right-4 z-50" style="top: 25%;"
                x-data="{
                    circumference: 25 * 2 * Math.PI,
                    percent: 0,
                    updateProgress() {
                        this.percent = calcularProgressoObrigatorio();
                    }
                }"
                x-init="updateProgress();
                        const atualizarProgresso = () => updateProgress();
                        document.querySelectorAll('.resposta-input, .fator-input').forEach(input => {
                            input.addEventListener('change', atualizarProgresso);
                            input.addEventListener('input', atualizarProgresso);
                        });"
                >
                <div class="flex items-center px-4 py-2 bg-white dark:bg-gray-800 shadow-xl rounded-lg h-12">
                    <div class="relative flex items-center justify-center w-12 h-12 overflow-hidden bg-white dark:bg-gray-900 rounded-full">
                        <svg class="w-full h-full" viewBox="0 0 60 60">
                            <circle class="text-gray-300 dark:text-gray-600" stroke-width="5" stroke="currentColor" fill="transparent"
                                r="25" cx="30" cy="30" />
                            <circle class="text-blue-600" stroke-width="5" :stroke-dasharray="circumference" :stroke-dashoffset="circumference - percent / 100 * circumference"
                                stroke-linecap="round" stroke="currentColor" fill="transparent" r="25" cx="30" cy="30" />
                        </svg>
                        <span class="absolute text-sm font-semibold text-blue-700 dark:text-blue-400" x-text="`${percent}%`"></span>
                    </div>
                    <p class="ml-4 text-sm text-gray-700 dark:text-gray-300">Progresso</p>
                </div>
            </div>

            <form id="formulario-resposta" x-ref="form" @submit.prevent="handleSubmit"
                :class="localizacaoStatus !== 'ok' ? 'pointer-events-none select-none opacity-40' : ''"
                :aria-hidden="localizacaoStatus !== 'ok'">
                <input type="hidden" name="latitude" id="latitude">
                <input type="hidden" name="longitude" id="longitude">
                <input type="hidden" name="rua" id="rua">
                <input type="hidden" name="bairro" id="bairro">
                <input type="hidden" name="cidade" id="cidade">
                <input type="hidden" name="estado" id="estado">
                <input type="hidden" name="cep" id="cep">
                <div>
                    <!-- Navegação das etapas -->
                    <div class="flex mb-3 gap-2 overflow-x-auto">
                        @foreach($formulario->passos->sortBy('ordem') as $i => $passo)
                            <button type="button" class="px-4 py-2 rounded text-sm font-medium focus:outline-none cursor-not-allowed"
                                :class="etapa === {{ $i }} ? 'bg-indigo-600 text-white' : 'bg-gray-200 dark:bg-gray-700 dark:text-gray-300'"
                               >
                                {{ $passo->titulo }}
                            </button>
                        @endforeach
                    </div>

                    <!-- Conteúdo dos passos -->
                    @foreach($formulario->passos->sortBy('ordem') as $i => $passo)
                        <div x-show="etapa === {{ $i }}" x-transition >
                            @if($i === 0)
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-3 border border-indigo-100 dark:border-indigo-900/40"
                                    x-show="localizacaoStatus === 'ok'"
                                    x-transition>
                                    <div class="flex items-start gap-3 mb-4">
                                        <i class="bx bx-map text-2xl text-indigo-600 shrink-0"></i>
                                        <div>
                                            <h3 class="font-medium text-gray-900 dark:text-white">Confirme sua localização</h3>
                                            <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
                                                Detectamos onde você está. Confira o endereço abaixo e ajuste se necessário.
                                            </p>
                                            <p class="mt-1 text-xs text-gray-500 dark:text-gray-500"
                                                x-show="enderecoExibicao.cidade"
                                                x-text="[enderecoExibicao.cidade, enderecoExibicao.estado].filter(Boolean).join(' — ')">
                                            </p>
                                        </div>
                                    </div>
                                    <div class="grid gap-4 sm:grid-cols-2">
                                        <div class="sm:col-span-2">
                                            <label for="rua-confirmacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Rua
                                            </label>
                                            <input type="text" id="rua-confirmacao"
                                                placeholder="Nome da rua"
                                                x-model="enderecoExibicao.rua"
                                                @input="enderecoConfirmado = false; sincronizarEnderecoHidden()"
                                                class="w-full border rounded p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        </div>
                                        <div>
                                            <label for="cep-confirmacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                CEP <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="cep-confirmacao" inputmode="numeric" maxlength="9"
                                                placeholder="00000-000"
                                                x-model="enderecoExibicao.cep"
                                                @input="aoEditarCep()"
                                                @blur="buscarBairroPorCep()"
                                                class="w-full border rounded p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        </div>
                                        <div>
                                            <label for="bairro-confirmacao" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                Bairro <span class="text-red-500">*</span>
                                            </label>
                                            <input type="text" id="bairro-confirmacao"
                                                placeholder="Nome do bairro"
                                                x-model="enderecoExibicao.bairro"
                                                @input="enderecoConfirmado = false; sincronizarEnderecoHidden()"
                                                class="w-full border rounded p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600">
                                        </div>
                                    </div>
                                    <p class="mt-2 text-xs text-amber-600 dark:text-amber-400" x-show="erroCep" x-text="erroCep"></p>
                                    <label class="mt-4 flex items-start gap-2 cursor-pointer">
                                        <input type="checkbox"
                                            x-model="enderecoConfirmado"
                                            class="mt-1 rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                                        <span class="text-sm text-gray-700 dark:text-gray-300">
                                            Confirmo que o endereço (rua, bairro e CEP) está correto
                                        </span>
                                    </label>
                                </div>
                            @endif
                            @foreach($passo->perguntas as $pergunta)
                                <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 mb-3 pergunta-card"
                                    data-pergunta-id="{{ $pergunta->id }}"
                                    @if($pergunta->usaFatoresSatisfacao())
                                    x-data="{
                                        fatorSelecionado: '',
                                        textoNome: '',
                                        fatoresConhecimentoIds: {{ $fatoresConhecimentoIds->toJson() }},
                                        fatoresMotivoIds: {{ $fatoresMotivoIds->toJson() }},
                                        selecionouConhecimento() {
                                            return this.fatorSelecionado !== ''
                                                && this.fatoresConhecimentoIds.includes(Number(this.fatorSelecionado));
                                        },
                                        textoPreenchido() {
                                            return this.textoNome.trim() !== '';
                                        },
                                        mostrarMotivo() {
                                            if (this.selecionouConhecimento()) return false;
                                            return this.textoPreenchido()
                                                || (this.fatorSelecionado !== ''
                                                    && this.fatoresMotivoIds.includes(Number(this.fatorSelecionado)));
                                        },
                                        limparMotivoSeOculto() {
                                            if (!this.mostrarMotivo()
                                                && this.fatorSelecionado !== ''
                                                && this.fatoresMotivoIds.includes(Number(this.fatorSelecionado))) {
                                                this.fatorSelecionado = '';
                                            }
                                        },
                                        alternarFatorConhecimento(id, event) {
                                            const idStr = String(id);
                                            if (this.fatorSelecionado === idStr) {
                                                event.preventDefault();
                                                this.fatorSelecionado = '';
                                                this.notificarProgresso(event.target);
                                                return;
                                            }
                                            this.fatorSelecionado = idStr;
                                            this.textoNome = '';
                                            this.limparMotivoSeOculto();
                                            this.notificarProgresso(event.target);
                                        },
                                        liberarParaIndicarNome() {
                                            this.fatorSelecionado = '';
                                            this.$nextTick(() => {
                                                this.$refs.textoResposta{{ $pergunta->id }}?.focus();
                                                this.notificarProgresso();
                                            });
                                        },
                                        aoInteragirComTexto() {
                                            if (this.selecionouConhecimento()) {
                                                this.fatorSelecionado = '';
                                            }
                                        },
                                        notificarProgresso(el) {
                                            this.$nextTick(() => {
                                                (el || this.$refs.textoResposta{{ $pergunta->id }})
                                                    ?.dispatchEvent(new Event('change', { bubbles: true }));
                                            });
                                        }
                                    }"
                                    @endif
                                >
                                    <label class="block font-medium text-gray-700 dark:text-gray-200 mb-1">
                                        {{ $pergunta->pergunta }}
                                        @if($pergunta->obrigatorio)
                                            <span class="text-red-500">*</span>
                                        @endif
                                    </label>

                                    @if($pergunta->tipo === 'texto')
                                        <input type="text" name="respostas[{{ $pergunta->id }}]" data-question-id="{{ $pergunta->id }}"
                                            x-ref="textoResposta{{ $pergunta->id }}"
                                            class="w-full border rounded p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600 resposta-input"
                                            @if($pergunta->usaFatoresSatisfacao())
                                            x-model="textoNome"
                                            @focus="aoInteragirComTexto()"
                                            @input="aoInteragirComTexto()"
                                            x-effect="limparMotivoSeOculto()"
                                            placeholder="Escreva o nome da empresa ou profissional"
                                            @endif
                                            {{ $pergunta->obrigatorio ? 'required' : '' }}>
                                    @elseif(in_array($pergunta->tipo, ['radio', 'checkbox']))
                                        <div class="space-y-1">
                                            @foreach($pergunta->opcoes as $i => $opcao)
                                                <label class="inline-flex items-center gap-2 px-4">
                                                    <input type="{{ $pergunta->tipo }}"  data-question-id="{{ $pergunta->id }}" class="resposta-input"
                                                        name="respostas[{{ $pergunta->id }}]{{ $pergunta->tipo === 'checkbox' ? '[]' : '' }}"
                                                        value="{{ $opcao->opcao }}"
                                                        {{ ($pergunta->obrigatorio && $pergunta->tipo === 'radio' && $i === 0) ? 'required' : '' }}>
                                                    <span class="text-gray-700 dark:text-gray-300">{{ $opcao->opcao }}</span>
                                                </label>
                                            @endforeach
                                        </div>
                                    @elseif($pergunta->tipo === 'select')
                                        <select name="respostas[{{ $pergunta->id }}]"  data-question-id="{{ $pergunta->id }}"
                                            class="w-full border rounded p-2 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600 resposta-input"
                                            {{ $pergunta->obrigatorio ? 'required' : '' }}>
                                            <option value="">Selecione</option>
                                            @foreach($pergunta->opcoes as $opcao)
                                                <option value="{{ $opcao->opcao }}">{{ $opcao->opcao }}</option>
                                            @endforeach
                                        </select>
                                    @endif

                                    {{-- Fatores de satisfação em dois blocos (como na planilha) --}}
                                    @if($pergunta->usaFatoresSatisfacao())
                                        @if($fatoresConhecimento->isNotEmpty())
                                            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600">
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-1">
                                                    Caso não lembre o nome
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                    Marque uma opção ou escreva o nome acima. Clique de novo na opção para desmarcar.
                                                </p>
                                                <div class="space-y-2">
                                                    @foreach($fatoresConhecimento as $fs)
                                                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                                            <input type="radio"
                                                                name="fatores[{{ $pergunta->id }}]"
                                                                value="{{ $fs->id }}"
                                                                data-question-id="{{ $pergunta->id }}"
                                                                data-fator-grupo="conhecimento"
                                                                :checked="fatorSelecionado == '{{ $fs->id }}'"
                                                                @click="alternarFatorConhecimento('{{ $fs->id }}', $event)"
                                                                class="fator-input text-blue-600 focus:ring-blue-500">
                                                            <span>{{ $fs->titulo }}</span>
                                                        </label>
                                                    @endforeach
                                                </div>
                                                <button type="button"
                                                    x-show="selecionouConhecimento()"
                                                    @click="liberarParaIndicarNome()"
                                                    class="mt-2 text-sm text-indigo-600 hover:text-indigo-800 dark:text-indigo-400 dark:hover:text-indigo-300 underline">
                                                    Prefiro indicar o nome
                                                </button>
                                            </div>
                                        @endif

                                        @if($fatoresMotivo->isNotEmpty())
                                            <div class="mt-4 pt-3 border-t border-gray-200 dark:border-gray-600"
                                                x-show="mostrarMotivo()"
                                                x-transition>
                                                <p class="text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                                    Por que lembra dessa empresa? <span class="text-red-500">*</span>
                                                </p>
                                                <p class="text-xs text-gray-500 dark:text-gray-400 mb-2">
                                                    Preencha o nome acima e escolha um motivo.
                                                </p>
                                                <div class="space-y-2">
                                                    @foreach($fatoresMotivo as $fs)
                                                        <label class="flex items-center gap-2 text-sm text-gray-700 dark:text-gray-300 cursor-pointer">
                                                            <input type="radio"
                                                                name="fatores[{{ $pergunta->id }}]"
                                                                value="{{ $fs->id }}"
                                                                data-question-id="{{ $pergunta->id }}"
                                                                data-fator-grupo="motivo"
                                                                data-fator-titulo="{{ $fs->titulo }}"
                                                                x-model="fatorSelecionado"
                                                                class="fator-input text-blue-600 focus:ring-blue-500">
                                                            <span>{{ $fs->titulo }}</span>

                                                            @if($fs->usa_input_extra)
                                                                <input type="text"
                                                                    data-input-fator data-pergunta-id="{{ $pergunta->id }}" data-fator-id="{{ $fs->id }}"
                                                                    name="fator_extra[{{ $pergunta->id }}][{{ $fs->id }}]"
                                                                    data-backend-name="input_fatores[{{ $pergunta->id }}]"
                                                                    class="ml-2 border rounded p-1 bg-white dark:bg-gray-900 dark:text-white dark:border-gray-600"
                                                                    placeholder="Descreva..."
                                                                    x-show="fatorSelecionado == '{{ $fs->id }}'"
                                                                    x-bind:required="fatorSelecionado == '{{ $fs->id }}'"
                                                                    x-transition>
                                                            @endif
                                                        </label>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    @endforeach

                    <!-- Navegação entre etapas -->
                    <div class="mt-8 flex justify-between">
                        <button type="button" @click="etapa = Math.max(etapa - 1, 0)"
                            class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600"
                            x-show="etapa > 0">
                            Voltar
                        </button>                       
                        <button type="button" @click="if (validarConfirmacaoEndereco() && validarEtapa(etapa)) etapa++"
                            class="px-4 py-2 bg-indigo-600 text-white rounded hover:bg-indigo-700"
                            x-show="etapa < {{ $formulario->passos->count() - 1 }}"
                            :disabled="localizacaoStatus !== 'ok'">
                            Próximo
                        </button>
                        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded hover:bg-green-700 disabled:opacity-50 disabled:cursor-not-allowed"
                            x-show="etapa === {{ $formulario->passos->count() - 1 }}"
                            :disabled="enviando || localizacaoStatus !== 'ok'"
                            >
                            <template x-if="!enviando">
                                <span>Finalizar</span>
                            </template>
                            <template x-if="enviando">
                                <span>Enviando...</span>
                            </template>
                            </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
<script>
const regrasProgresso = @json($regrasProgresso);

const fatoresObrigamRespostaTexto = @json(
    $fatoresSatisfacao->filter(fn($fs) => $fs->resposta_obrigatoria)->pluck('titulo')->values()
);

function perguntaExigePreenchimento(regra) {
    return regra.usa_fatores || regra.obrigatorio;
}

function isPerguntaCompleta(id, regra, scope = document) {
    if (!perguntaExigePreenchimento(regra)) {
        return false;
    }

    if (regra.usa_fatores) {
        const fator = scope.querySelector(`[name="fatores[${id}]"]:checked`);
        if (!fator) {
            return false;
        }

        const fatorId = Number(fator.value);
        const idsConhecimento = (regra.fatores_conhecimento || []).map(Number);
        if (idsConhecimento.includes(fatorId)) {
            return true;
        }

        const texto = scope.querySelector(`[name="respostas[${id}]"]`);
        return texto && texto.value.trim() !== '';
    }

    const texto = scope.querySelector(`[name="respostas[${id}]"]`);
    if (texto && texto.type === 'text') {
        return texto.value.trim() !== '';
    }

    const select = scope.querySelector(`select[name="respostas[${id}]"]`);
    if (select) {
        return select.value !== '';
    }

    if (scope.querySelector(`[name="respostas[${id}][]"]`)) {
        return scope.querySelectorAll(`[name="respostas[${id}][]"]:checked`).length > 0;
    }

    return scope.querySelectorAll(`[name="respostas[${id}]"]:checked`).length > 0;
}

function calcularProgressoObrigatorio(scope = document) {
    const ids = Object.keys(regrasProgresso).filter(id => perguntaExigePreenchimento(regrasProgresso[id]));
    if (ids.length === 0) {
        return 0;
    }

    const preenchidas = ids.filter(id => isPerguntaCompleta(id, regrasProgresso[id], scope)).length;
    return Math.round((preenchidas / ids.length) * 100);
}

function formularioResponder() {
    return {
        etapa: 0,
        enviando: false,
        inicioTimestamp: null,
        localizacaoStatus: 'loading',
        mensagemLocalizacao: 'Aguardando permissão para usar sua localização...',
        enderecoConfirmado: false,
        erroCep: '',
        enderecoExibicao: {
            rua: '',
            bairro: '',
            cep: '',
            cidade: '',
            estado: '',
        },
        init() {
            this.inicioTimestamp = null;

            document.querySelectorAll('.resposta-input').forEach(input => {
                input.addEventListener('input', () => {
                    if (!this.inicioTimestamp) {
                        this.inicioTimestamp = new Date().toISOString();
                    }
                }, { once: true });
            });

            this.solicitarLocalizacao();
        },
        possuiLocalizacao() {
            const lat = document.getElementById('latitude')?.value;
            const lng = document.getElementById('longitude')?.value;
            return lat !== '' && lng !== '' && lat != null && lng != null;
        },
        mensagemErroGeolocalizacao(error) {
            if (!error) {
                return 'Não foi possível obter sua localização. Tente novamente.';
            }
            if (error.code === 1) {
                return 'Você negou o acesso à localização. Ative a permissão nas configurações do navegador para continuar.';
            }
            if (error.code === 2) {
                return 'Localização indisponível no momento. Verifique se o GPS está ligado e tente novamente.';
            }
            if (error.code === 3) {
                return 'Tempo esgotado ao buscar a localização. Tente novamente em um local com melhor sinal.';
            }
            return 'Não foi possível obter sua localização. Tente novamente.';
        },
        limparCamposLocalizacao() {
            ['latitude', 'longitude', 'rua', 'bairro', 'cidade', 'estado', 'cep'].forEach(id => {
                const el = document.getElementById(id);
                if (el) {
                    el.value = '';
                }
            });
            this.enderecoConfirmado = false;
            this.erroCep = '';
            this.enderecoExibicao = { rua: '', bairro: '', cep: '', cidade: '', estado: '' };
        },
        sincronizarEnderecoHidden() {
            document.getElementById('rua').value = (this.enderecoExibicao.rua || '').trim();
            document.getElementById('bairro').value = (this.enderecoExibicao.bairro || '').trim();
            document.getElementById('cep').value = this.normalizarCep(this.enderecoExibicao.cep);
            this.enderecoExibicao.cep = document.getElementById('cep').value;
        },
        carregarEnderecoParaConfirmacao() {
            this.enderecoExibicao = {
                rua: document.getElementById('rua')?.value || '',
                bairro: document.getElementById('bairro')?.value || '',
                cep: document.getElementById('cep')?.value || '',
                cidade: document.getElementById('cidade')?.value || '',
                estado: document.getElementById('estado')?.value || '',
            };
            this.enderecoConfirmado = false;
        },
        aoEditarCep() {
            this.erroCep = '';
            this.enderecoConfirmado = false;
            this.sincronizarEnderecoHidden();
        },
        async buscarBairroPorCep() {
            const cep = this.normalizarCep(this.enderecoExibicao.cep);
            if (!cep) {
                this.erroCep = '';
                this.sincronizarEnderecoHidden();
                return;
            }

            this.enderecoExibicao.cep = cep;
            this.sincronizarEnderecoHidden();

            const dados = await this.buscarEnderecoPorCep(cep);
            if (!dados) {
                this.erroCep = 'CEP não encontrado. Verifique o número digitado.';
                return;
            }

            this.erroCep = '';
            if (dados.bairro && !this.enderecoExibicao.bairro.trim()) {
                this.enderecoExibicao.bairro = dados.bairro.trim();
            }
            if (dados.localidade) {
                document.getElementById('cidade').value = dados.localidade;
                this.enderecoExibicao.cidade = dados.localidade;
            }
            if (dados.uf) {
                document.getElementById('estado').value = dados.uf;
                this.enderecoExibicao.estado = dados.uf;
            }
            if (dados.logradouro && !this.enderecoExibicao.rua.trim()) {
                this.enderecoExibicao.rua = dados.logradouro.trim();
            }
            this.sincronizarEnderecoHidden();
        },
        preencherCoordenadas(lat, lng) {
            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;
        },
        extrairCidade(address) {
            return address.city || address.town || address.village || address.municipality || '';
        },
        extrairBairro(address) {
            const cidade = this.extrairCidade(address).trim().toLowerCase();
            const candidatos = [
                address.suburb,
                address.neighbourhood,
                address.quarter,
                address.borough,
                address.residential,
            ];

            for (const valor of candidatos) {
                if (!valor) {
                    continue;
                }

                const bairro = valor.trim();
                if (!bairro || bairro.toLowerCase() === cidade) {
                    continue;
                }

                return bairro;
            }

            return '';
        },
        normalizarCep(cep) {
            const digitos = String(cep || '').replace(/\D/g, '');
            if (digitos.length !== 8) {
                return '';
            }

            return `${digitos.slice(0, 5)}-${digitos.slice(5)}`;
        },
        async buscarEnderecoPorCep(cep) {
            const cepNormalizado = this.normalizarCep(cep);
            if (!cepNormalizado) {
                return null;
            }

            try {
                const res = await fetch(`https://viacep.com.br/ws/${cepNormalizado.replace('-', '')}/json/`);
                const dados = await res.json();

                if (dados.erro) {
                    return null;
                }

                return dados;
            } catch (e) {
                console.warn('Erro ao buscar CEP:', e);
                return null;
            }
        },
        async reverseGeocode(lat, lng) {
            try {
                const url = `https://nominatim.openstreetmap.org/reverse?format=json&lat=${lat}&lon=${lng}&zoom=18&addressdetails=1&accept-language=pt-BR`;
                const res = await fetch(url);
                const data = await res.json();
                const address = data.address || {};
                const cep = this.normalizarCep(address.postcode);

                document.getElementById('rua').value = address.road || '';
                document.getElementById('cidade').value = this.extrairCidade(address);
                document.getElementById('estado').value = address.state || '';
                document.getElementById('cep').value = cep;

                let bairro = this.extrairBairro(address);

                if (!bairro && cep) {
                    const dadosCep = await this.buscarEnderecoPorCep(cep);
                    if (dadosCep?.bairro) {
                        bairro = dadosCep.bairro.trim();
                    }
                }

                document.getElementById('bairro').value = bairro;
            } catch (e) {
                console.warn('Erro ao buscar endereço:', e);
            }
        },
        obterPosicao(options) {
            return new Promise((resolve, reject) => {
                navigator.geolocation.getCurrentPosition(resolve, reject, options);
            });
        },
        async solicitarLocalizacao() {
            if (!('geolocation' in navigator)) {
                this.localizacaoStatus = 'unsupported';
                this.mensagemLocalizacao = 'Seu navegador não suporta geolocalização. Use Chrome, Safari ou Firefox atualizado.';
                this.limparCamposLocalizacao();
                return;
            }

            this.localizacaoStatus = 'loading';
            this.mensagemLocalizacao = 'Aguardando permissão para usar sua localização...';
            this.limparCamposLocalizacao();

            try {
                const pos = await this.obterPosicao({ enableHighAccuracy: true, timeout: 10000, maximumAge: 0 });
                await this.aplicarLocalizacao(pos.coords.latitude, pos.coords.longitude);
                return;
            } catch (error) {
                if (error.code === 1) {
                    this.localizacaoStatus = 'denied';
                    this.mensagemLocalizacao = this.mensagemErroGeolocalizacao(error);
                    this.limparCamposLocalizacao();
                    return;
                }

                if (error.code === 3) {
                    try {
                        const pos = await this.obterPosicao({ enableHighAccuracy: false, timeout: 8000, maximumAge: 0 });
                        await this.aplicarLocalizacao(pos.coords.latitude, pos.coords.longitude);
                        return;
                    } catch (error2) {
                        this.localizacaoStatus = 'error';
                        this.mensagemLocalizacao = this.mensagemErroGeolocalizacao(error2);
                        this.limparCamposLocalizacao();
                        return;
                    }
                }

                this.localizacaoStatus = 'error';
                this.mensagemLocalizacao = this.mensagemErroGeolocalizacao(error);
                this.limparCamposLocalizacao();
            }
        },
        async aplicarLocalizacao(lat, lng) {
            this.preencherCoordenadas(lat, lng);
            await this.reverseGeocode(lat, lng);
            this.carregarEnderecoParaConfirmacao();
            this.localizacaoStatus = 'ok';
            this.mensagemLocalizacao = '';
        },
        validarConfirmacaoEndereco(forcar = false) {
            if (!forcar && this.etapa !== 0) {
                return true;
            }

            this.sincronizarEnderecoHidden();

            const bairro = (this.enderecoExibicao.bairro || '').trim();
            const cep = this.normalizarCep(this.enderecoExibicao.cep);

            if (!cep || !bairro) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Localização incompleta',
                    text: 'Informe o CEP e o bairro para continuar.',
                });
                if (forcar) {
                    this.etapa = 0;
                }
                return false;
            }

            if (!this.enderecoConfirmado) {
                Swal.fire({
                    icon: 'info',
                    title: 'Confirme sua localização',
                    text: 'Marque a opção confirmando que o endereço está correto.',
                });
                if (forcar) {
                    this.etapa = 0;
                }
                return false;
            }

            return true;
        },
        validarEtapa(index) {
            if (this.localizacaoStatus !== 'ok') {
                Swal.fire({
                    icon: 'warning',
                    title: 'Localização obrigatória',
                    text: 'Ative a localização do dispositivo para responder o formulário.',
                });
                return false;
            }
            const etapaAtual = document.querySelectorAll(`[x-show="etapa === ${index}"]`)[0];
            let faltaFator = false;
            let faltaRespostaParaFator = false;
            let faltaInputExtra = false;
            let faltaObrigatoria = false;

            etapaAtual.querySelectorAll('.pergunta-card[data-pergunta-id]').forEach(card => {
                const id = card.dataset.perguntaId;
                const regra = regrasProgresso[id];
                if (!regra || !perguntaExigePreenchimento(regra)) {
                    return;
                }

                if (regra.usa_fatores && !etapaAtual.querySelector(`[name="fatores[${id}]"]:checked`)) {
                    faltaFator = true;
                }

                if (!isPerguntaCompleta(id, regra, etapaAtual)) {
                    if (regra.usa_fatores) {
                        const fator = etapaAtual.querySelector(`[name="fatores[${id}]"]:checked`);
                        if (fator) {
                            faltaRespostaParaFator = true;
                        }
                    } else {
                        faltaObrigatoria = true;
                    }
                }

                const fatorMarcado = etapaAtual.querySelector(`[name="fatores[${id}]"]:checked`);
                if (fatorMarcado) {
                    const fatorInputExtra = etapaAtual.querySelector(
                        `[name="fator_extra[${id}][${fatorMarcado.value}]"]`
                    );
                    if (fatorInputExtra && fatorInputExtra.offsetParent !== null && !fatorInputExtra.value.trim()) {
                        faltaInputExtra = true;
                    }
                }
            });

            if (faltaObrigatoria) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Perguntas incompletas',
                    text: 'Por favor, responda todas as perguntas obrigatórias antes de avançar.'
                });
                return false;
            }

            if (faltaFator) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Fatores de Satisfação',
                    text: 'Por favor, selecione o fator de satisfação para todas as perguntas que possuem essa opção.'
                });
                return false;
            }

            if (faltaRespostaParaFator) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Resposta obrigatória',
                    text: 'Para o motivo selecionado, informe o nome da empresa ou profissional.'
                });
                return false;
            }

            if (faltaInputExtra) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Campo adicional obrigatório',
                    text: 'Por favor, preencha o campo adicional exigido pelo fator de satisfação selecionado.'
                });
                return false;
            }

            return true;
        },
        validarProgressoMinimo() {
            const progressComponent = document.querySelector('[x-data*="circumference"]');
            const percent = Alpine.evaluate(progressComponent, 'percent');
            
            if (percent < 70) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Atenção',
                    text: 'É necessário preencher pelo menos 70% do formulário para enviar.',
                });
                return false;
            }
            return true;
        },
        async handleSubmit() {
            if (this.enviando) return;

            if (this.localizacaoStatus !== 'ok' || !this.possuiLocalizacao()) {
                Swal.fire({
                    icon: 'warning',
                    title: 'Localização obrigatória',
                    text: 'Ative a localização do dispositivo para enviar o formulário.',
                });
                this.solicitarLocalizacao();
                return;
            }

            this.sincronizarEnderecoHidden();

            if (!this.validarConfirmacaoEndereco(true) || !this.validarEtapa(this.etapa) || !this.validarProgressoMinimo()) {
                return;
            }

            this.enviando = true; // começa o envio

            const form = this.$refs.form;
            const formData = new FormData(form);

            // Adiciona manualmente os input_fatores
            form.querySelectorAll('[data-input-fator]').forEach(input => {
                if (input.offsetParent !== null) {
                    const backendName = input.getAttribute('data-backend-name');
                    const hidden = document.createElement('input');
                    hidden.type = 'hidden';
                    hidden.name = backendName;
                    hidden.value = input.value;
                    form.appendChild(hidden);
                }
            });

            const fimTimestamp = new Date().toISOString();
            const duracao = this.inicioTimestamp
                ? Math.floor((new Date(fimTimestamp) - new Date(this.inicioTimestamp)) / 1000)
                : 0;

            formData.append('duracao_em_segundos', duracao);
            formData.append('inicio_resposta', this.inicioTimestamp || '');
            formData.append('fim_resposta', fimTimestamp);


            try {
                const response = await fetch("{{ route('respostas.store', $formulario) }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                        'Accept': 'application/json',
                    },
                    body: formData
                });

                if (response.ok) {
                    const data = await response.json();
                    Swal.fire({
                        icon: 'success',
                        title: 'Formulário enviado com sucesso!',
                        text: data?.message || 'Obrigado por sua resposta!',
                        showConfirmButton: true
                    }).then(() => {
                        window.location.reload();
                    })
                } else {
                    const error = await response.json();
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro ao enviar',
                        text: error?.message || 'Ocorreu um erro ao enviar o formulário.',
                    });
                    this.enviando = false; // reabilita o botão
                }
            } catch (err) {
                console.error(err);
                Swal.fire({
                    icon: 'error',
                    title: 'Erro inesperado',
                    text: 'Não foi possível enviar o formulário.',
                });
                this.enviando = false; // reabilita o botão

            }
        }
    }
}

</script>
</x-app-layout>
