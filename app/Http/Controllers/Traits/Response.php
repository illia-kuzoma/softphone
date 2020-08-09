<?php


namespace App\Http\Controllers\Traits;

use App\Models\User;

trait Response
{

    public function getResponse(User $user)
    {
        return array_merge(
            [
                'user' => $user->toArray(),
                'token' => $user->getToken(),
                'departments' => (new \App\Models\Department())->getAllArr()
            ],$this->getAgentsArr()
        );
    }

    public function getAgentsArr()
    {
        return [
            'agents' => User::getAllAgentIDFullName(),
        ];
    }
}
