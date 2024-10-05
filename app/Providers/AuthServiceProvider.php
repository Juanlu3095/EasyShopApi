<?php

namespace App\Providers;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Notifications\Messages\MailMessage;

// use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Personalización  SÓLO del email de verificación
        VerifyEmail::toMailUsing(function (object $notifiable, string $url) {
            return (new MailMessage)
                ->greeting('¡Hola!')
                ->subject('Verifica tu email')
                ->line('Para finalizar tu registro en EasyShop necesitamos que verifiques tu email presionando el siguiente botón:')
                ->action('Verifica tu correo electrónico', $url)
                ->line('Saludos,')
                ->salutation('El equipo de EasyShop');
        });
    }
}
