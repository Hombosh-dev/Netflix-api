<?php

namespace App\Http\Requests\Auth;

use Illuminate\Foundation\Auth\EmailVerificationRequest as BaseEmailVerificationRequest;

class EmailVerificationRequest extends BaseEmailVerificationRequest
{
    /**
     * Get the body parameters for the request.
     *
     * @return array
     */
    public function bodyParameters()
    {
        return [];
    }

    /**
     * Get the URL parameters for the request.
     *
     * @return array
     */
    public function urlParameters()
    {
        return [
            'id' => [
                'description' => 'ID користувача, чия електронна пошта підтверджується.',
                'example' => '01HN5PXMEH6SDMF0KAVSW1DYTY',
            ],
            'hash' => [
                'description' => 'Хеш електронної пошти користувача для перевірки.',
                'example' => 'a1b2c3d4e5f6g7h8i9j0',
            ],
        ];
    }
}
