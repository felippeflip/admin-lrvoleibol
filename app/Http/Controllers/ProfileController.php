<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

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

        // Handle photo upload
        if ($request->hasFile('foto')) {
            // Delete old photo if exists
            if ($user->foto) {
                Storage::disk('user_fotos')->delete($user->foto);
            }

            $file = $request->file('foto');
            $filename = uniqid() . '_' . $file->getClientOriginalName();
            // Save new photo
            $path = Storage::disk('user_fotos')->putFileAs('/', $file, $filename);
            $user->foto = $filename;
        }

        // Campos comuns a todos
        $userData = [
            'name' => $validatedData['name'],
            'apelido' => $validatedData['apelido'],
            'email' => $validatedData['email'],
            'telefone' => $validatedData['telefone'],
            'cpf' => $validatedData['cpf'],
            'endereco' => $validatedData['endereco'],
            'numero' => $validatedData['numero'],
            'bairro' => $validatedData['bairro'],
            'cidade' => $validatedData['cidade'],
            'estado' => $validatedData['estado'],
            'cep' => $validatedData['cep'],
            'rg' => $validatedData['rg'] ?? $user->rg,
            'data_nascimento' => $validatedData['data_nascimento'] ?? $user->data_nascimento,
        ];

        // Campos específicos de Juiz
        if ($user->hasRole('Juiz')) {
            $userData['cref'] = $validatedData['cref'] ?? $user->cref;
            $userData['lrv'] = $validatedData['lrv'] ?? $user->lrv;
        }

        // Atualiza os campos no modelo do usuário
        $user->fill($userData);

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
