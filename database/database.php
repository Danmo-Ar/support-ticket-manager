<?php
class database
{
    public static $connect = null;

    public static function connection()
    {
        $dbHost = 'localhost';
        $dbname = 'ticket_manager';
        $dbport = 3306;
        $dbuser = 'root';
        $dbpass = '';


        try {

            $connect = new PDO("mysql:host=" . $dbHost . ";dbname=" . $dbname . ";port=" . $dbport, $dbuser, $dbpass);
        } catch (PDOEXception $e) {
            die($e->getMessage());
        }
        return $connect;
    }

    public static function dconnection()
    {
        $connect = null;
    }
}
