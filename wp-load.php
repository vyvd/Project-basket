<?php
// reverse engineering wordpress functions to get the affiliates system to work on the new site
/*
include 'classes/idiorm.class.php';

define("DB_HOST", "localhost");     // The host you want to connect to.
define("DB_USER", "staging_nsaukrebuild");    // The database username.
define("DB_PASSWORD", "i{1X}Rcer-A,");    // The database password.
define("DB_DATABASE", "staging_nsaukrebuild");    // The database name.

// configure ORM
ORM::configure('mysql:host='.DB_HOST.';dbname='.DB_DATABASE);
ORM::configure('username', DB_USER);
ORM::configure('password', DB_PASSWORD);
ORM::configure('driver_options', array(PDO::MYSQL_ATTR_INIT_COMMAND => 'SET NAMES utf8'));
ORM::configure('error_mode', PDO::ERRMODE_WARNING);
ORM::configure('caching', true);
ORM::configure('caching_auto_clear', true);


function get_col($query = null, $x = 0) {

    if ( $query ) {
        $db_data = ORM::for_table('accounts')->raw_query($query, array())->find_many();
    }

    $new_array = array();
    $last_result = end($db_data);
    // Extract the column values.
    if ( $last_result ) {
        for ( $i = 0, $j = count( $last_result ); $i < $j; $i++ ) {
            $new_array[ $i ] = $this->get_var( null, $x, $i );
        }
    }
    return $new_array;


}

function get_data($query) {

    $db_data = ORM::for_table('accounts')->raw_query($query, array())->find_many();

    return $db_data;

}*/