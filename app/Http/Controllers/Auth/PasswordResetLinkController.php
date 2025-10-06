<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\ValidationException;
use App\Models\User;

class PasswordResetLinkController extends Controller
{
    /** Mostrar formulario para solicitar el enlace */
    public function create()
    {
        return view('auth.forgot-password', [
            'status' => session('status'),
        ]);
    }

    /** Enviar el enlace de restablecimiento */
    public function store(Request $request): RedirectResponse
    {
        // --- VALIDACIÓN A: solo correo ---------------
        // $request->validate([
        //     'email' => 'required|email',
        // ]);

        // --- VALIDACIÓN B: correo O usuario ----------
        $request->validate([
            'email'   => 'nullable|email',
            'usuario' => 'nullable|string',
        ]);
        if (!$request->filled('email') && !$request->filled('usuario')) {
            throw ValidationException::withMessages([
                'email' => 'Captura tu correo o tu usuario.',
            ]);
        }

        // Resolver email final (si envían usuario)
        $email = $request->input('email');
        if (!$email && $request->filled('usuario')) {
            $user = User::where('usuario', $request->usuario)->first();
            if (!$user || empty($user->email)) {
                throw ValidationException::withMessages([
                    'usuario' => 'No se encontró un usuario con email registrado.',
                ]);
            }
            $email = $user->email;
        }

        $status = Password::sendResetLink(['email' => $email]);

        if ($status === Password::RESET_LINK_SENT) {
            return back()->with('status', __($status));
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)],
        ]);
    }
}
