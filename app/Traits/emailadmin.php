<?php

namespace App\Traits;

use App\Models\Setting;

trait Emailadmin {

    /**
     * It gets email for admin purposes, like sending email from shop.
     */
    public function adminEmail(): string
    {
        $email = Setting::where('configuracion', 'email')->first();
        return $email->valor;
    }
}

?>