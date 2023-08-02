<?php

namespace backend\models;

class Ldap
{

    private static function getConnection()
    {
        // $ldap_host = config('production_server.ip')[0];
        $ldap_host = "172.16.1.64";
        $ldap_port = 389;
        $conn = @ldap_connect($ldap_host, $ldap_port) or die("Could not connect to $ldap_host");
        @ldap_set_option($conn, LDAP_OPT_PROTOCOL_VERSION, 3);
        @ldap_set_option($conn, LDAP_OPT_REFERRALS, 0);
        @ldap_set_option(NULL, LDAP_OPT_DEBUG_LEVEL, 7);
        return $conn;
    }

    public static function autenticar($username, $password) {
        $username = explode('@', $username);
        $conn = self::getConnection();
        $result = Ldap::buscarUsuario($username[0]);
        
        if($result['count'] !=0){
            $ldapdnread = $result[0]['dn'];        
            $bind = @ldap_bind($conn, $ldapdnread, $password);
            if ($bind) {            
                return $result[0];
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

    public static function buscarUsuario($username){        
        $conn = self::getConnection();
        $ldapusersearch = @ldap_search( $conn, "dc=UEA.EDU,dc=BR", "uid=" . $username );
        $entryid        = @ldap_first_entry( $conn, $ldapusersearch );
        $ldapdnread     = @ldap_get_dn( $conn, $entryid );
        $result         = @ldap_get_entries($conn, $ldapusersearch);

        return $result;
    }

    public static function verificarUsuarioSenha($username, $senha){
        $conn = self::getConnection();
        $result = Ldap::buscarUsuario($username);

        if($result['count'] !=0){
            $ldapdnread = $result[0]['dn'];        
            $bind = @ldap_bind($conn, $ldapdnread, $senha);
            if ($bind) {            
                return true;
            } else {
                return false;
            }
        }else{
            return false;
        }
    }

}
