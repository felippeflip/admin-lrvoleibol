<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use App\Helpers\Helpers;

class ProfileUpdateRequest extends FormRequest
{
    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\Rule|array|string>
     */
    public function rules(): array
    {

        // Limpar o CPF antes de salvar
        // Remove caracteres especiais do CPF antes de validá-lo
        $this->merge(['cpf' => removeSpecialCharsFromCPF($this->cpf)]);
        return [
            'name'              => ['required', 'string', 'max:255'],
            'email'             => ['required', 'string', 'lowercase', 'email', 'max:255', Rule::unique(User::class)->ignore($this->user()->id)],
            'apelido'           => ['nullable', 'string', 'max:255'],
            'telefone'          => ['nullable', 'string', 'max:20'],
            'cpf'               => ['required', 'string', 'max:11'],
            'endereco'          => ['nullable', 'string', 'max:255'],
            'bairro'            => ['nullable', 'string', 'max:255'],
            'cidade'            => ['nullable', 'string', 'max:255'],
            'estado'            => ['nullable', 'string', 'max:255'],
            'cep'               => ['nullable', 'string', 'max:10'],
        ];
    }
}
