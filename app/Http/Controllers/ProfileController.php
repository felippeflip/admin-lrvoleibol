<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
       // dd($request); die;
        

        // Validação dos dados do formulário
        $validatedData = $request->validated();
    
        // Recupera o usuário atual
        $user = $request->user();
    
        // Atualiza os campos no modelo do usuário
        $user->fill([
            'name' => $validatedData['name'],
            'apelido' => $validatedData['apelido'],
            'email' => $validatedData['email'],
            'telefone' => $validatedData['telefone'],
            'cpf' => $validatedData['cpf'],
            'endereco' => $validatedData['endereco'],
            'bairro' => $validatedData['bairro'],
            'cidade' => $validatedData['cidade'],
            'estado' => $validatedData['estado'],
            'cep' => $validatedData['cep'],
        ]);
    
        // Verifica se o e-mail foi alterado para limpar a verificação de e-mail
        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }
    
        // Salva as alterações no banco de dados
        $user->save();
    
        // Redireciona de volta para a página de edição do perfil com uma mensagem de status
        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }
    

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }
}
