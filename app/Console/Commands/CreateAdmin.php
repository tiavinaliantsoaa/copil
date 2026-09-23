<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class CreateAdmin extends Command
{
    protected $signature = 'escm:create-admin {email?}';
    protected $description = 'Crée ou réinitialise le premier compte administrateur ESCM COPIL';

    public function handle(): int
    {
        $email = $this->argument('email') ?: $this->ask('Adresse email de l’administrateur');
        $name = $this->ask('Nom complet', 'Administrateur ESCM');
        $password = $this->secret('Mot de passe (laisser vide pour en générer un)') ?: Str::password(20);

        if (mb_strlen($password) < 12) {
            $this->error('Le mot de passe doit contenir au moins 12 caractères.');
            return self::FAILURE;
        }

        $user = User::updateOrCreate(['email' => $email], [
            'name' => $name,
            'role' => 'admin',
            'active' => true,
            'password' => Hash::make($password),
        ]);

        $this->info("Administrateur créé: {$user->email}");
        $this->warn("Mot de passe initial: $password");
        return self::SUCCESS;
    }
}
