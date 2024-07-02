<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CadastrarPessoaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'nome' => 'required|max:250|min:3',
            'telefone' => 'required|max:11|min:11',
            // 'email' => 'required|max:250|min:10|email:rfc,dns|unique:App\Models\PessoaModel,email_pessoa',
            'email' => 'required|max:250|min:10|email:rfc,dns',
            'categoria' => 'required|array|min:1',
        ];
    }


    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'nome.required' => 'O nome é obrigatório',
            'nome.max' => 'O nome deve conter no máximo 250 caracteres',
            'nome.min' => 'O nome deve conter no minimo 3 caracteres',

            'telefone.required' => 'O telefone é obrigatório',
            'telefone.max' => 'Informe um telefone válido',
            'telefone.min' => 'Informe um telefone válido',

            'email.required' => 'O email é obrigatório',
            'email.max' => 'O email deve conter no máximo 250 caracteres',
            'email.min' => 'O email deve conter no minimo 10 caracteres',
            'email.email' => 'Informe um email válido',

            'categoria.required' => 'Por favor, selecione pelo menos uma categoria de interesse.',
            'categoria.min' => 'Por favor, selecione pelo menos uma categoria de interesse.',
        ];
    }
}
