// resources/js/app.js

import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    // --- Elementos do formulário (GENÉRICOS para Times e Atletas) ---
    // Atletas
    const atlCepInput = document.getElementById('atl_cep');
    const atlEnderecoInput = document.getElementById('atl_endereco');
    const atlBairroInput = document.getElementById('atl_bairro');
    const atlCidadeInput = document.getElementById('atl_cidade');
    const atlEstadoInput = document.getElementById('atl_estado'); // atl_estado no lugar de atl_uf
    const atlNumeroInput = document.getElementById('atl_numero');
    const atlCelularInput = document.getElementById('atl_celular'); // atl_celular
    const atlTelefoneInput = document.getElementById('atl_telefone'); // atl_telefone
    const atlCpfInput = document.getElementById('atl_cpf');
    const atlRgInput = document.getElementById('atl_rg');
    const atletaForm = document.getElementById('atletaForm'); // Formulário de criação de Atleta
    const atletaFormEdit = document.getElementById('atletaFormEdit'); // Formulário de edição de Atleta

    // Times (manter para compatibilidade com o formulário de Times)
    const timCepInput = document.getElementById('tim_cep');
    const timEnderecoInput = document.getElementById('tim_endereco');
    const timBairroInput = document.getElementById('tim_bairro');
    const timCidadeInput = document.getElementById('tim_cidade');
    const timUfInput = document.getElementById('tim_uf');
    const timNumeroInput = document.getElementById('tim_numero');
    const timCelularInput = document.getElementById('tim_celular');
    const timTelefoneInput = document.getElementById('tim_telefone');
    const timCnpjInput = document.getElementById('tim_cnpj');
    const timeForm = document.getElementById('timeForm'); // Formulário de criação de Time

    // --- Elementos do formulário de Usuários ---
    const userCepInput = document.getElementById('cep');
    const userEnderecoInput = document.getElementById('endereco');
    const userBairroInput = document.getElementById('bairro');
    const userCidadeInput = document.getElementById('cidade');
    const userEstadoInput = document.getElementById('estado');
    const userNumeroInput = document.getElementById('numero'); // Adicionado
    const userTelefoneInput = document.getElementById('telefone');
    const userCpfInput = document.getElementById('cpf');
    const userForm = document.getElementById('userForm'); // Formulário de criação de Usuário
    const userEditForm = document.getElementById('userEditForm'); // Formulário de edição de Usuário
    const profileForm = document.getElementById('profileForm'); // Formulário de edição de Perfil

    // --- Elementos do formulário de Ginásios ---
    const ginCepInput = document.getElementById('gin_cep');
    const ginEnderecoInput = document.getElementById('gin_endereco');
    const ginBairroInput = document.getElementById('gin_bairro');
    const ginCidadeInput = document.getElementById('gin_cidade');
    const ginEstadoInput = document.getElementById('gin_estado');
    const ginNumeroInput = document.getElementById('gin_numero');
    const ginTelefoneInput = document.getElementById('gin_telefone');
    const ginasioForm = document.getElementById('ginasioForm');
    const ginasioEditForm = document.getElementById('ginasioEditForm');

    // Referência ao formulário atual que está sendo carregado (atualizado para incluir userForm e userEditForm)
    const currentForm = atletaForm || atletaFormEdit || timeForm || userForm || userEditForm || profileForm || ginasioForm || ginasioEditForm;

    // --- Funções de Máscara ---
    function unmask(value) {
        return value ? value.replace(/\D/g, '') : '';
    }

    function maskCelular(value) {
        value = unmask(value);
        if (value.length > 10) {
            return value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 6) {
            return value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            return value.replace(/^(\d{2})(\d+)/, '($1) $2');
        }
        return value;
    }

    function maskTelefone(value) {
        value = unmask(value);
        if (value.length > 6) {
            return value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            return value.replace(/^(\d{2})(\d+)/, '($1) $2');
        }
        return value;
    }

    function maskCNPJ(value) {
        value = unmask(value);
        if (value.length > 12) {
            return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
        } else if (value.length > 8) {
            return value.replace(/^(\d{2})(\d{3})(\d{3})(\d+)/, '$1.$2.$3/$4');
        } else if (value.length > 5) {
            return value.replace(/^(\d{2})(\d{3})(\d+)/, '$1.$2.$3');
        } else if (value.length > 2) {
            return value.replace(/^(\d{2})(\d+)/, '$1.$2');
        }
        return value;
    }

    function maskCPF(value) {
        value = unmask(value);
        if (value.length > 9) {
            return value.replace(/^(\d{3})(\d{3})(\d{3})(\d{2}).*/, '$1.$2.$3-$4');
        } else if (value.length > 6) {
            return value.replace(/^(\d{3})(\d{3})(\d+)/, '$1.$2.$3');
        } else if (value.length > 3) {
            return value.replace(/^(\d{3})(\d+)/, '$1.$2');
        }
        return value;
    }

    function maskRG(value) {
        value = unmask(value);
        if (value.length > 8) {
            return value.replace(/^(\d{2})(\d{3})(\d{3})(\d{1}).*/, '$1.$2.$3-$4');
        } else if (value.length > 2) {
            return value.replace(/^(\d{2})(\d+)/, '$1.$2');
        }
        return value;
    }

    function maskCEP(value) {
        value = unmask(value);
        if (value.length > 5) {
            return value.replace(/^(\d{5})(\d{3}).*/, '$1-$2');
        }
        return value;
    }

    // --- Funções de Manipulação do Endereço (do CEP) ---
    function limpaFormularioEndereco(endereco, bairro, cidade, uf, numero) {
        if (endereco) endereco.value = "";
        if (bairro) bairro.value = "";
        if (cidade) cidade.value = "";
        if (uf) uf.value = "";
        if (numero) numero.value = "";

        if (endereco) endereco.readOnly = false;
        if (bairro) bairro.readOnly = false;
        if (cidade) cidade.readOnly = false;
        if (uf) uf.readOnly = false;

        if (endereco) endereco.classList.remove('bg-gray-200', 'dark:bg-gray-600');
        if (bairro) bairro.classList.remove('bg-gray-200', 'dark:bg-gray-600');
        if (cidade) cidade.classList.remove('bg-gray-200', 'dark:bg-gray-600');
        if (uf) uf.classList.remove('bg-gray-200', 'dark:bg-gray-600');
    }

    function preencheFormularioEndereco(data, endereco, bairro, cidade, uf, numero) {
        if (endereco) endereco.value = data.logradouro || "";
        if (bairro) bairro.value = data.bairro || "";
        if (cidade) cidade.value = data.localidade || "";
        if (uf) uf.value = data.uf || "";

        if (endereco) endereco.readOnly = true;
        if (bairro) bairro.readOnly = true;
        if (cidade) cidade.readOnly = true;
        if (uf) uf.readOnly = true;

        if (endereco) endereco.classList.add('bg-gray-200', 'dark:bg-gray-600');
        if (bairro) bairro.classList.add('bg-gray-200', 'dark:bg-gray-600');
        if (cidade) cidade.classList.add('bg-gray-200', 'dark:bg-gray-600');
        if (uf) uf.classList.add('bg-gray-200', 'dark:bg-gray-600');

        if (numero) numero.focus();
    }

    // --- Lógica de CEP para Atletas e Times (agora consolidada) ---
    function setupCepLogic(cepInputEl, enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl) {
        if (cepInputEl) {
            // Aplicar máscara ao carregar (útil para edição)
            if (cepInputEl.value) {
                cepInputEl.value = maskCEP(cepInputEl.value);
                // Se o CEP já veio preenchido, aplica o estilo de "readOnly"
                if (enderecoInputEl && enderecoInputEl.value) {
                    enderecoInputEl.readOnly = true;
                    bairroInputEl.readOnly = true;
                    cidadeInputEl.readOnly = true;
                    ufInputEl.readOnly = true;
                    enderecoInputEl.classList.add('bg-gray-200', 'dark:bg-gray-600');
                    bairroInputEl.classList.add('bg-gray-200', 'dark:bg-gray-600');
                    cidadeInputEl.classList.add('bg-gray-200', 'dark:bg-gray-600');
                    ufInputEl.classList.add('bg-gray-200', 'dark:bg-gray-600');
                }
            }

            cepInputEl.addEventListener('input', (e) => {
                e.target.value = maskCEP(e.target.value);
            });

            cepInputEl.addEventListener('blur', function () {
                let cep = unmask(cepInputEl.value);

                if (cep != "" && /^[0-9]{8}$/.test(cep)) {
                    limpaFormularioEndereco(enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl);

                    fetch(`https://viacep.com.br/ws/${cep}/json/`)
                        .then(response => response.json())
                        .then(data => {
                            if (!("erro" in data)) {
                                preencheFormularioEndereco(data, enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl);
                            } else {
                                limpaFormularioEndereco(enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl);
                                alert("CEP não encontrado.");
                            }
                        })
                        .catch(error => {
                            console.error('Erro ao buscar CEP:', error);
                            limpaFormularioEndereco(enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl);
                            alert("Erro ao buscar CEP. Verifique sua conexão ou tente novamente.");
                        });
                } else if (cep === "") {
                    limpaFormularioEndereco(enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl);
                } else {
                    limpaFormularioEndereco(enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl);
                    alert("Formato de CEP inválido.");
                }
            });
            // Opcional: Iniciar com campos de endereço limpos e habilitados se não houver valor
            if (!cepInputEl.value) {
                limpaFormularioEndereco(enderecoInputEl, bairroInputEl, cidadeInputEl, ufInputEl, numeroInputEl);
            }
        }
    }

    // --- Aplica lógica de CEP e Máscaras quando a página carrega ---
    // Para formulários de Times (create e edit)
    if (timeForm || document.getElementById('timeEditForm')) {
        setupCepLogic(timCepInput, timEnderecoInput, timBairroInput, timCidadeInput, timUfInput, timNumeroInput);
        if (timCelularInput) timCelularInput.addEventListener('input', (e) => { e.target.value = maskCelular(e.target.value); });
        if (timTelefoneInput) timTelefoneInput.addEventListener('input', (e) => { e.target.value = maskTelefone(e.target.value); });
        if (timCnpjInput) timCnpjInput.addEventListener('input', (e) => { e.target.value = maskCNPJ(e.target.value); });

        // Aplica máscaras a valores existentes (para edição de times)
        if (timCelularInput && timCelularInput.value) timCelularInput.value = maskCelular(timCelularInput.value);
        if (timTelefoneInput && timTelefoneInput.value) timTelefoneInput.value = maskTelefone(timTelefoneInput.value);
        if (timCnpjInput && timCnpjInput.value) timCnpjInput.value = maskCNPJ(timCnpjInput.value);
    }

    // Para formulários de Atletas (create e edit)
    if (atletaForm || atletaFormEdit) {
        setupCepLogic(atlCepInput, atlEnderecoInput, atlBairroInput, atlCidadeInput, atlEstadoInput, atlNumeroInput); // atl_estado para UF
        if (atlCelularInput) atlCelularInput.addEventListener('input', (e) => { e.target.value = maskCelular(e.target.value); });
        if (atlTelefoneInput) atlTelefoneInput.addEventListener('input', (e) => { e.target.value = maskTelefone(e.target.value); });
        if (atlCpfInput) atlCpfInput.addEventListener('input', (e) => { e.target.value = maskCPF(e.target.value); });
        if (atlRgInput) atlRgInput.addEventListener('input', (e) => { e.target.value = maskRG(e.target.value); });

        // Aplica máscaras a valores existentes (para edição de atletas)
        if (atlCelularInput && atlCelularInput.value) atlCelularInput.value = maskCelular(atlCelularInput.value);
        if (atlTelefoneInput && atlTelefoneInput.value) atlTelefoneInput.value = maskTelefone(atlTelefoneInput.value);
        if (atlCpfInput && atlCpfInput.value) atlCpfInput.value = maskCPF(atlCpfInput.value);
        if (atlRgInput && atlRgInput.value) atlRgInput.value = maskRG(atlRgInput.value);
    }

    // Para formulários de Usuários (create e edit) e Perfil
    if (userForm || userEditForm || profileForm) {
        setupCepLogic(userCepInput, userEnderecoInput, userBairroInput, userCidadeInput, userEstadoInput, userNumeroInput);

        if (userTelefoneInput) userTelefoneInput.addEventListener('input', (e) => { e.target.value = maskCelular(e.target.value); }); // Usando maskCelular pois pode ser celular
        if (userCpfInput) userCpfInput.addEventListener('input', (e) => { e.target.value = maskCPF(e.target.value); });

        // Aplica máscaras a valores existentes
        if (userTelefoneInput && userTelefoneInput.value) userTelefoneInput.value = maskCelular(userTelefoneInput.value);
        if (userCpfInput && userCpfInput.value) userCpfInput.value = maskCPF(userCpfInput.value);
        if (userCpfInput && userCpfInput.value) userCpfInput.value = maskCPF(userCpfInput.value);
    }

    // Para formulários de Ginásios
    if (ginasioForm || ginasioEditForm) {
        setupCepLogic(ginCepInput, ginEnderecoInput, ginBairroInput, ginCidadeInput, ginEstadoInput, ginNumeroInput);
        if (ginTelefoneInput) ginTelefoneInput.addEventListener('input', (e) => { e.target.value = maskTelefone(e.target.value); });

        if (ginTelefoneInput && ginTelefoneInput.value) ginTelefoneInput.value = maskTelefone(ginTelefoneInput.value);
    }


    // --- Pre-processamento antes de enviar o formulário (remove máscaras) ---
    if (currentForm) {
        currentForm.addEventListener('submit', function () {
            if (atlCelularInput) atlCelularInput.value = unmask(atlCelularInput.value);
            if (atlTelefoneInput) atlTelefoneInput.value = unmask(atlTelefoneInput.value);
            if (atlCpfInput) atlCpfInput.value = unmask(atlCpfInput.value);
            if (atlRgInput) atlRgInput.value = unmask(atlRgInput.value);
            if (atlCepInput) atlCepInput.value = unmask(atlCepInput.value);

            // Para o formulário de times (se for o caso)
            if (timCelularInput) timCelularInput.value = unmask(timCelularInput.value);
            if (timTelefoneInput) timTelefoneInput.value = unmask(timTelefoneInput.value);
            if (timCnpjInput) timCnpjInput.value = unmask(timCnpjInput.value);
            if (timCepInput) timCepInput.value = unmask(timCepInput.value);

            // Para o formulário de usuários
            if (userTelefoneInput) userTelefoneInput.value = unmask(userTelefoneInput.value);
            if (userCpfInput) userCpfInput.value = unmask(userCpfInput.value);
            if (userCpfInput) userCpfInput.value = unmask(userCpfInput.value);
            if (userCepInput) userCepInput.value = unmask(userCepInput.value);

            // Para o formulário de ginásios
            if (ginTelefoneInput) ginTelefoneInput.value = unmask(ginTelefoneInput.value);
            if (ginCepInput) ginCepInput.value = unmask(ginCepInput.value);
        });
    }

    // --- Lógica para Mensagens Flash (Sucesso, Erro, etc.) ---
    const flashMessages = document.querySelectorAll('.flash-message');

    if (flashMessages.length > 0) {
        flashMessages.forEach(message => {
            setTimeout(() => {
                // Adiciona classes para uma transição suave
                message.classList.add('transition', 'opacity-0', 'ease-out', 'duration-500');
                // Remove o elemento do DOM após a transição
                message.addEventListener('transitionend', () => message.remove());
            }, 3000); // 3000 milissegundos = 3 segundos
        });
    }

});