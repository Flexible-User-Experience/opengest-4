<?php

namespace App\Enum;

/**
 * UserRolesEnum class.
 *
 * @category Enum
 *
 * @author   Wils Iglesias <wiglesias83@gmail.com>
 */
class UserRolesEnum
{
    const ROLE_USER = 'ROLE_USER';
    const ROLE_CMS = 'ROLE_CMS';
    const ROLE_MANAGER = 'ROLE_MANAGER';
    const ROLE_ADMIN = 'ROLE_ADMIN';
    const ROLE_SUPER_ADMIN = 'ROLE_SUPER_ADMIN';

    /**
     * @return array
     */
    public static function getEnumArray()
    {
        return array(
            self::ROLE_USER => 'Usuari',
            self::ROLE_CMS => 'Editor',
            self::ROLE_MANAGER => 'Gestor',
            self::ROLE_ADMIN => 'Administrador',
            self::ROLE_SUPER_ADMIN => 'Superadministrador',
        );
    }
}
