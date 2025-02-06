<?php

namespace App\Http\Controllers;

use App\Http\Requests\LoginRequest;
use App\Http\Requests\RegistroRequest;
use App\Http\Requests\UserupdateRequest;
use App\Http\Resources\UserResource;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;
use App\Mail\loginAdmin;
use Illuminate\Auth\Events\Registered;
//use Illuminate\Foundation\Auth\EmailVerificationRequest;
use App\Http\Requests\EmailverificationRequest;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Auth\Events\PasswordReset; 
use App\Models\Setting;

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
    public function loginAdministrador(LoginRequest $request): JsonResponse {

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

        //date_default_timezone_set('Europe/Madrid'); // Con esto ponemos que el timezone sea GMT+2
        $fecha = date('d-m-Y H:i'); // Obtenemos la fecha con el timezone indicado arriba
        $browser = $request->header('User-Agent'); // Obtenemos los datos del navegador y/o dispositivo que realiza la petición

        //$ubicacionAPI = file_get_contents("http://ipinfo.io/" . '200.150.100.10' . "/json"); // Hacemos petición de geolocalización a la API. Esto es de prueba
        $ubicacionAPI = file_get_contents("http://ipinfo.io/{$ip}/json"); // Hacemos petición de geolocalización a la API. Esta sería la versión a poner
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

        $email = Setting::where('configuracion', 'email')->first();
        Mail::to($email->valor)->send(new loginAdmin($datos)); // Enviamos correo al admin para notificarle del acceso.

        return response()->json([
            'status' => true,
            'message' => 'Usuario logueado correctamente',
            'token' => $user->createToken("API TOKEN", ['*'], now()->addDay())->plainTextToken, // Creamos token con expiración de un día
        ], 200);
    }

    /**
     * Get data from client while logged using authorization token
     */
    public function dataclient() {
        $user = auth()->user();

        if($user && $user->role_id === 3) {
            return response()->json([
                'status' => true,
                'data' => new UserResource($user)
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'No se ha podido obtener los datos.'
            ], 401);
        }
    }


    /**
     * Token validation to permit access or not to a protected page. Used to validate user while browsing in admin dashboard.
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

        event(new Registered($user)); // Registra el evento y se envía un correo para verificar al email indicado en la $request

        return response()->json([
            'status' => true,
            'message' => 'Usuario creado con éxito.'
        ], 201);
    }

    /**
     * It lets client to update email and password
     */
    public function actualizarCliente(UserupdateRequest $request)
    {
        $userId = auth()->user()->id;
        $user = User::find($userId);

        if($user) {
            $user->update([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password)
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Datos actualizados.'
            ], 200);

        } else {
            return response()->json([
                'status' => false,
                'message' => 'Usuario no encontrado.'
            ], 404);
        }
 
    }   

    /**
     * It verifies the user once you click the button inside the email. It uses custom Email verification request.
     */
    public function verificarEmail(EmailverificationRequest $request)
    {
        $user = User::find($request->id);
        //$user->email_verified_at = now(); // Marcamos el email como verificado en la base de datos
        $user->markEmailAsVerified(); // Ota forma de verificar el email
        $user->save();  // Guardar los cambios

        /* return response()->json([
            'message' => 'Error',
            'Dato' => $request->hash,
            'id' => $request->id,
            'Usuario' => $request->user()
        ]); */

        // $request->fulfill(); // Esta función también estaría mal porque pide que el usuario esté autenticado.

        // Redirección al front-end donde nos diga que el usuario se ha verificado.
        return redirect(env('APP_FRONT_URL') . '/emailverificado');
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

    /**
     * It manages forgot-password form to send email
     */
    public function ForgotPassword(Request $request)
    {
        $request->validate(['email' => 'required|email']);
     
        $status = Password::sendResetLink(
            $request->only('email')
        );
     
        return $status === Password::RESET_LINK_SENT
                    ? back()->with(['status' => __($status)])
                    : back()->withErrors(['email' => __($status)]);
    }

    /**
     * It manages reset-password form to change password with token
     */
    public function ResetPassword(Request $request) {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|confirmed',
        ]);
     
        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function (User $user, string $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->setRememberToken(Str::random(60));
     
                $user->save();
     
                event(new PasswordReset($user));
            }
            
        );
     
        return $status === Password::PASSWORD_RESET
                    ? redirect(env('APP_FRONT_URL') . '/acceso')->with('status', __($status))
                    : back()->withErrors(['email' => [__($status)]]);
    }
}
