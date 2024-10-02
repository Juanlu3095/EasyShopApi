<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistroRequest;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\loginAdmin;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Foundation\Auth\EmailVerificationRequest;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    /**
     * Login ONLY for users with client role.
     */
    public function loginUser(LoginRequest $request): JsonResponse {
        // Probamos si se hace el login con el rol de cliente, si no es correcto el email y/o contraseña, error 401
        $auth = Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => 3]);

        if(!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Email y/o contraseña no válidos.'
            ], 401);

        } else {
        // Si el login es correcto, localizamos el usuario en la base de datos y generamos el token
        $user = User::where('email', $request->email)->first();

        return response()->json([
            'status' => true,
            'message' => 'Usuario logueado correctamente',
            'token' => $user->createToken("API TOKEN C", ['*'], now()->addDay())->plainTextToken
        ], 200);

        }
 
    }

    /**
     * Login ONLY for users with admin role.
     */
    public function loginAdmin(LoginRequest $request): JsonResponse {

        // Intentamos buscar el usuario con la request con el rol de admin
        $auth = Auth::attempt(['email' => $request->email, 'password' => $request->password, 'role_id' => 1]);

        if(!$auth) {
            return response()->json([
                'status' => false,
                'message' => 'Email y/o contraseña no válidos.'
            ], 401);
        }

        // Si el login es correcto, localizamos el usuario en la base de datos y generamos el token. Luego enviamos email a ADMIN
        $user = User::where('email', $request->email)->first();

        $ip = $request->ip(); // Obtenemos la IP del usuario que realiza la petición
        $nombre = $user->name; // Obtenemos el nombre de la request
        $email = $user->email; // Obtenemos el email de la request

        date_default_timezone_set('Europe/Madrid'); // Con esto ponemos que el timezone sea GMT+2
        $fecha = date('d-m-Y H:i'); // Obtenemos la fecha con el timezone indicado arriba
        $browser = $request->header('User-Agent'); // Obtenemos los datos del navegador y/o dispositivo que realiza la petición

        $ubicacionAPI = file_get_contents("http://ipinfo.io/" . '200.150.100.10' . "/json"); // Hacemos petición de geolocalización a la API. Esto es de prueba
        //$ubicacionAPI = file_get_contents("http://ipinfo.io/{$ip}/json"); // Hacemos petición de geolocalización a la API. Esta sería la versión a poner
        $ubicacion = json_decode($ubicacionAPI); // Decodificamos el json

        $datos = array(
            'ip' => $ip,
            'nombre' => $nombre,
            'email' => $email,
            'fecha' => $fecha,
            'aplicacion' => $browser,
            'ciudad' => $ubicacion->city,
            'pais' => $ubicacion->country,
            'host' => $ubicacion->hostname
        );

        Mail::to('jcooldevelopment@gmail.com')->send(new loginAdmin($datos)); // Enviamos correo al admin para notificarle del acceso.

        return response()->json([
            'status' => true,
            'message' => 'Usuario logueado correctamente',
            'token' => $user->createToken("API TOKEN", ['*'], now()->addDay())->plainTextToken, // Creamos token con expiración de un día
        ], 200);
    }

    /**
     * Token validation to permit access or not to a protected page.
     */
    public function validarTokenA(Request $request): JsonResponse {
        
        $user = $request->user(); // Obtenemos los datos del usuario a partir del token enviado en la cabecera de petición de Angular

        if (!$user) { // Si el usuario no ha enviado token
            return response()->json([
                'status' => false,
                'message' => 'Usuario no autenticado.'
            ], 401);
        }

        $role = $user->role_id; // Obtenemos el rol del usuario al que pertenece el token

        if($role == 1) {
            return response()->json([
                'status' => true,
                'message' => 'Usuario correcto.'
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no autorizado.'
            ], 403);
        }
        
    }

    /**
     * Register ONLY for users with client role. Send an email for account activation.
     */
    public function registroCliente(RegistroRequest $request): JsonResponse
    {
        $user = new User;
        $user->name = $request->name;
        $user->email = $request->email;
        $user->password = Hash::make($request->password);
        $user->role_id = 3;
        $user->save();

        //$user->sendEmailVerificationNotification(); // Enviamos correo de verificación

        event(new Registered($user));

        return response()->json([
            'status' => true,
            'message' => 'Usuario creado con éxito.'
        ], 201);
    }

    // No funciona correctamente para verificar email
    public function verify(EmailVerificationRequest $request)
    {
            // Obtener el ID del usuario desde la solicitud
        $userId = $request->route('id');

        // Depurar: Verificar si estamos obteniendo correctamente el ID
        Log::info('User ID from request: ' . $userId);

        // Cargar el usuario usando el ID
        $user = User::find($userId);

        // Depurar: Verificar si el usuario fue encontrado
        if (!$user) {
            Log::error('User not found with ID: ' . $userId);
            return response()->json(['message' => 'User not found'], 404);  // Retorna un error si el usuario no es encontrado
        }

        // Depurar: Verificar si el usuario ya tiene el email verificado
        if ($user->hasVerifiedEmail()) {
            Log::info('Email already verified for user ID: ' . $userId);
            return response()->json(['message' => 'Email already verified'], 200);  // Si el correo ya ha sido verificado
        }

        // Verificar que el hash en la URL coincida con el hash del correo electrónico del usuario
        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            Log::error('Hash mismatch for user ID: ' . $userId);
            return response()->json(['message' => 'Invalid verification link'], 403);  // Si el hash no coincide
        }

        // Marcar el email como verificado
        $user->markEmailAsVerified();

        // Depurar: Confirmar que el email ha sido marcado como verificado
        Log::info('Email successfully verified for user ID: ' . $userId);

        return response()->json(['message' => 'Email verified successfully'], 200);
    }

    /**
     * Logouts and deletes token used.
     */
    public function logout(Request $request): JsonResponse {
        $request->user()->currentAccessToken()->delete(); // El token viene en el header 'Authorization' de la petición desde Angular y con ello reconoce el usuario.

        return response()->json([
            'status' => true,
            'message' => 'Cierre de sesión satisfactorio'
        ], 200);
    }
}
