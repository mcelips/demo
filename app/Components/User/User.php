<?php

namespace App\Components\User;

use App\Models\Model;

class User extends Model
{

    protected $table = 'users';

    const STATUS_ADMIN = 777;

}