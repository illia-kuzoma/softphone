<?php


namespace App\Http\Controllers\Traits;

use App\Models\User;

trait UserAuth
{
    /**
     * Gets user object by token.
     *
     * @param string $token
     * @return User|\Illuminate\Http\RedirectResponse
     */
    public function getUser($token)
    {
        // |\Illuminate\Http\RedirectResponse
        try{
            $user = new User();
            $user->setUserByToken($token);
            if(!$user->checkToken()){
                return redirect()->action(
                    'SoftPhone\Auth@getAuth', ['message' => "Please enter to system."]
                );
            }
        }
        catch(\Exception $e)
        {
            return redirect()->action(
                'SoftPhone\Auth@getAuth', ['message' => $e->getMessage()]
            );
        }
        return $user;
    }
}
