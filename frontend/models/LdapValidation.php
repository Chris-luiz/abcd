<?php

namespace frontend\models;

use LDAP\Connection;
use yii\base\Model;


class LdapValidation extends Model
{

    public static function getConnection(): Connection
    {
    }

    public static function autenticar($username, $password)
    {
        $username = explode('@', $username);
        $conn = self::getConnection();
        $result = self::buscarUsuario($username[0]);

        if ($result['count'] != 0) {
            $ldapdnread = $result[0]['dn'];
            $bind = @ldap_bind($conn, $ldapdnread, $password);
            if ($bind) {
                return $result[0];
            } else {
                return false;
            }
        } else {
            return false;
        }
    }

    public static function buscarUsuario($username)
    {
        $conn = self::getConnection();
        $ldapusersearch = @ldap_search($conn, "dc=UEA.EDU,dc=BR", "uid=" . $username);
        $entryid        = @ldap_first_entry($conn, $ldapusersearch);
        $ldapdnread     = @ldap_get_dn($conn, $entryid);
        $result         = @ldap_get_entries($conn, $ldapusersearch);

        return $result;
    }

    // public static function verificarUsuarioSenha($username, $senha)
    // {
    //     $conn = self::getConnection();
    //     $result = Ldap::buscarUsuario($username);

    //     if ($result['count'] != 0) {
    //         $ldapdnread = $result[0]['dn'];
    //         $bind = @ldap_bind($conn, $ldapdnread, $senha);
    //         if ($bind) {
    //             return true;
    //         } else {
    //             return false;
    //         }
    //     } else {
    //         return false;
    //     }
    // }
}
