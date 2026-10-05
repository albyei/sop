<?php
declare(strict_types=1);

namespace App\Model\Entity;

use Cake\ORM\Entity;

class LoginOtp extends Entity
{
    protected array $_accessible = [
        'user_id' => true,
        'otp_hash' => true,
        'expires_at' => true,
        'attempts' => true,
        'used_at' => true,
        'created' => false,
        'modified' => false,
        'user' => false,
    ];
}
