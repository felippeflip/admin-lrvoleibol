// resources/js/app.js

import './bootstrap';
import 'flowbite';

import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

document.addEventListener('DOMContentLoaded', function () {
    // Elementos do formulário
    const cepInput = document.getElementById('tim_cep');
    const enderecoInput = document.getElementById('tim_endereco');
    const bairroInput = document.getElementById('tim_bairro');
    const cidadeInput = document.getElementById('tim_cidade');
    const ufInput = document.getElementById('tim_uf');
    const numeroInput = document.getElementById('tim_numero');
    const celularInput = document.getElementById('tim_celular');
    const telefoneInput = document.getElementById('tim_telefone');
    const cnpjInput = document.getElementById('tim_cnpj');
    // Referência genérica ao formulário (serve para 'create' e 'edit')
    const currentForm = document.getElementById('timeForm') || document.getElementById('timeEditForm');

    // --- Funções de Máscara ---

    // Função genérica para remover caracteres não numéricos
    function unmask(value) {
        return value ? value.replace(/\D/g, '') : '';
    }

    // Máscara para Celular (XX) XXXXX-XXXX
    function maskCelular(value) {
        value = unmask(value);
        if (value.length > 10) { // Com 9º dígito
            value = value.replace(/^(\d{2})(\d{5})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 6) { // Sem 9º dígito (para telefones fixos ou antigos)
            value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d+)/, '($1) $2');
        }
        return value;
    }

    // Máscara para Telefone (XX) XXXX-XXXX (padrão de 8 dígitos após DDD)
    function maskTelefone(value) {
        value = unmask(value);
        if (value.length > 6) {
            value = value.replace(/^(\d{2})(\d{4})(\d{4}).*/, '($1) $2-$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d+)/, '($1) $2');
        }
        return value;
    }

    // Máscara para CNPJ XX.XXX.XXX/XXXX-XX
    function maskCNPJ(value) {
        value = unmask(value);
        if (value.length > 12) {
            value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d{4})(\d{2}).*/, '$1.$2.$3/$4-$5');
        } else if (value.length > 8) {
            value = value.replace(/^(\d{2})(\d{3})(\d{3})(\d+)/, '$1.$2.$3/$4');
        } else if (value.length > 5) {
            value = value.replace(/^(\d{2})(\d{3})(\d+)/, '$1.$2.$3');
        } else if (value.length > 2) {
            value = value.replace(/^(\d{2})(\d+)/, '$1.$2');
        }
        return value;
    }

    // Máscara para CEP XXXXX-XXX
    function maskCEP(value) {
        value = unmask(value);
        if (value.length > 5) {
            value = value.replace(/^(\d{5})(\d{3}).*/, '$1-$2');
        }
        return value;
    }

    // --- Funções de Manipulação do Endereço (do CEP) ---
    function limpaFormularioEndereco() {
        // Esta função agora só será chamada quando o usuário interagir com o CEP
        if (enderecoInput) enderecoInput.value = "";
        if (bairroInput) bairroInput.value = "";
        if (cidadeInput) cidadeInput.value = "";
        if (ufInput) ufInput.value = "";
        if (numeroInput) numeroInput.value = ""; // Também limpa o número, caso queira que o usuário redigite

        // Garante que os campos fiquem editáveis
        if (enderecoInput) enderecoInput.readOnly = false;
        if (bairroInput) bairroInput.readOnly = false;
        if (cidadeInput) cidadeInput.readOnly = false;
        if (ufInput) ufInput.readOnly = false;

        // Remove o estilo de campo bloqueado
        if (enderecoInput) enderecoInput.classList.remove('bg-gray-200', 'dark:bg-gray-600');
        if (bairroInput) bairroInput.classList.remove('bg-gray-200', 'dark:bg-gray-600');
        if (cidadeInput) cidadeInput.classList.remove('bg-gray-200', 'dark:bg-gray-600');
        if (ufInput) ufInput.classList.remove('bg-gray-200', 'dark:bg-gray-600');
    }

    function preencheFormularioEndereco(data) {
        if (enderecoInput) enderecoInput.value = data.logradouro || "";
        if (bairroInput) bairroInput.value = data.bairro || "";
        if (cidadeInput) cidadeInput.value = data.localidade || "";
        if (ufInput) ufInput.value = data.uf || "";

        // Bloqueia e estiliza campos preenchidos pela API
        if (enderecoInput) enderecoInput.readOnly = true;
        if (bairroInput) bairroInput.readOnly = true;
        if (cidadeInput) cidadeInput.readOnly = true;
        if (ufInput) ufInput.readOnly = true;

        if (enderecoInput) enderecoInput.classList.add('bg-gray-200', 'dark:bg-gray-600');
        if (bairroInput) bairroInput.classList.add('bg-gray-200', 'dark:bg-gray-600');
        if (cidadeInput) cidadeInput.classList.add('bg-gray-200', 'dark:bg-gray-600');
        if (ufInput) ufInput.classList.add('bg-gray-200', 'dark:bg-gray-600');

        if (numeroInput) numeroInput.focus(); // Foca no campo número após preencher
    }

    // --- Aplicar máscaras aos valores existentes ao carregar a página (para o formulário de edição) ---
    // Esta é a principal mudança: as máscaras são aplicadas aos valores que o Blade já inseriu.
    if (celularInput && celularInput.value) {
        celularInput.value = maskCelular(celularInput.value);
    }
    if (telefoneInput && telefoneInput.value) {
        telefoneInput.value = maskTelefone(telefoneInput.value);
    }
    if (cnpjInput && cnpjInput.value) {
        cnpjInput.value = maskCNPJ(cnpjInput.value);
    }
    if (cepInput && cepInput.value) {
        cepInput.value = maskCEP(cepInput.value);
        // Se o CEP já veio preenchido, assumimos que os campos de endereço também estão.
        // Apenas aplica o estilo de "readOnly" e a cor de fundo cinza.
        if (enderecoInput && enderecoInput.value) {
            if (enderecoInput) enderecoInput.readOnly = true;
            if (bairroInput) bairroInput.readOnly = true;
            if (cidadeInput) cidadeInput.readOnly = true;
            if (ufInput) ufInput.readOnly = true;

            if (enderecoInput) enderecoInput.classList.add('bg-gray-200', 'dark:bg-gray-600');
            if (bairroInput) bairroInput.classList.add('bg-gray-200', 'dark:bg-gray-600');
            if (cidadeInput) cidadeInput.classList.add('bg-gray-200', 'dark:bg-gray-600');
            if (ufInput) ufInput.classList.add('bg-gray-200', 'dark:bg-gray-600');
        }
    }


    // --- Aplicar máscaras aos campos conforme o usuário digita ---
    if (celularInput) {
        celularInput.addEventListener('input', (e) => {
            e.target.value = maskCelular(e.target.value);
        });
    }
    if (telefoneInput) {
        telefoneInput.addEventListener('input', (e) => {
            e.target.value = maskTelefone(e.target.value);
        });
    }
    if (cnpjInput) {
        cnpjInput.addEventListener('input', (e) => {
            e.target.value = maskCNPJ(e.target.value);
        });
    }
    if (cepInput) {
        cepInput.addEventListener('input', (e) => {
            e.target.value = maskCEP(e.target.value);
        });
    }

    // --- Event Listener para o CEP (na perda de foco - blur) ---
    if (cepInput) {
        cepInput.addEventListener('blur', function() {
            let cep = unmask(cepInput.value); // Usa a função unmask

            // Só busca o CEP se ele não estiver vazio e for um CEP válido
            if (cep != "" && /^[0-9]{8}$/.test(cep)) {
                limpaFormularioEndereco(); // Limpa os campos antes de uma nova busca
                
                fetch(`https://viacep.com.br/ws/${cep}/json/`)
                    .then(response => response.json())
                    .then(data => {
                        if (!("erro" in data)) {
                            preencheFormularioEndereco(data);
                        } else {
                            limpaFormularioEndereco();
                            alert("CEP não encontrado.");
                        }
                    })
                    .catch(error => {
                        console.error('Erro ao buscar CEP:', error);
                        limpaFormularioEndereco();
                        alert("Erro ao buscar CEP. Verifique sua conexão ou tente novamente.");
                    });
            } else if (cep === "") { // Se o CEP foi esvaziado, limpa os campos de endereço
                limpaFormularioEndereco();
            } else { // Se o formato do CEP é inválido
                limpaFormularioEndereco();
                alert("Formato de CEP inválido.");
            }
        });
        // IMPORTANTE: A LINHA ABAIXO FOI REMOVIDA.
        // limpaFormularioEndereco(); // NÃO chame esta função no DOMContentLoaded para não apagar os dados existentes!
    }

    // --- Pré-processamento antes de enviar o formulário (remove máscaras) ---
    if (currentForm) {
        currentForm.addEventListener('submit', function() {
            if (celularInput) celularInput.value = unmask(celularInput.value);
            if (telefoneInput) telefoneInput.value = unmask(telefoneInput.value);
            if (cnpjInput) cnpjInput.value = unmask(cnpjInput.value);
            if (cepInput) cepInput.value = unmask(cepInput.value);
        });
    }
});