<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Model;

#[Fillable(['organization_id', 'user_id', 'is_owner'])]
class OrganizationUser extends Model
{
    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
}
