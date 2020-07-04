<?php

use Illuminate\Database\Seeder;

class realOauthClientsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        

        \DB::table('oauth_clients')->delete();
        
        \DB::table('oauth_clients')->insert(array (
            0 => 
            array (
                'id' => 1,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => '0jAfrELWxkJQeuo911kVdQPA3oxI7YBuOeEJ2yYN',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-02 21:08:02',
                'updated_at' => '2020-06-02 21:08:02',
            ),
            1 => 
            array (
                'id' => 2,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'nhocKga9s5ey7FYtbt0LcLw8pUKjEE33cHeD1YIR',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-02 21:08:02',
                'updated_at' => '2020-06-02 21:08:02',
            ),
            2 => 
            array (
                'id' => 3,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => 'ZlrAP8IFIIlFjDFs17EjqgySgZf2aSJViRPCD84X',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-06 15:12:53',
                'updated_at' => '2020-06-06 15:12:53',
            ),
            3 => 
            array (
                'id' => 4,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'JlbUZxBp7V24YucwWK6gTeXzBASEikSWwXvPBVpO',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-06 15:12:53',
                'updated_at' => '2020-06-06 15:12:53',
            ),
            4 => 
            array (
                'id' => 5,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => 'zUC9uWbBzJ0IqYZvsHjROvsZmiyjgCW2Tgmp27OA',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-07 22:11:34',
                'updated_at' => '2020-06-07 22:11:34',
            ),
            5 => 
            array (
                'id' => 6,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'P2hxj1F68CLmGAUNVVMXVTwtjk5FKqESVtoKMwje',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-07 22:11:34',
                'updated_at' => '2020-06-07 22:11:34',
            ),
            6 => 
            array (
                'id' => 7,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => '5g18EL55WpLdO9cXL94vomXxKLAI5YGu9hVmjnjm',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-07 23:11:58',
                'updated_at' => '2020-06-07 23:11:58',
            ),
            7 => 
            array (
                'id' => 8,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'ztbcj7q7Jz0HEmucufZNt3gBxXOMlIKRsa9lEOVr',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-07 23:11:58',
                'updated_at' => '2020-06-07 23:11:58',
            ),
            8 => 
            array (
                'id' => 9,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => 'v8ZRUNwZYFHfjZqeXOJGle68RgiXaLOZsJ8osCzG',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-08 09:26:45',
                'updated_at' => '2020-06-08 09:26:45',
            ),
            9 => 
            array (
                'id' => 10,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'N08hbOJuvwjc4KYYdLecYKb1CnNH8f8lYwbtUGR8',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-08 09:26:45',
                'updated_at' => '2020-06-08 09:26:45',
            ),
            10 => 
            array (
                'id' => 11,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => 'CfWevFgNF1z4HApgLnnASjDGSne9jqDLiVB7x3Ch',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-13 10:11:36',
                'updated_at' => '2020-06-13 10:11:36',
            ),
            11 => 
            array (
                'id' => 12,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'dweeuWkXEKBrTl21CYrxdvOkBSLiAzJQ56he81PJ',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-13 10:11:36',
                'updated_at' => '2020-06-13 10:11:36',
            ),
            12 => 
            array (
                'id' => 13,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => 'xVKXEX1USaxaRH3Ge3iTe6C1Q0n0aw8UdsU4Agpz',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-18 10:39:23',
                'updated_at' => '2020-06-18 10:39:23',
            ),
            13 => 
            array (
                'id' => 14,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'WmIyF7iwaEKK4B5s9YSp6TzUpFzTEmT1kFKmb4wN',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-18 10:39:23',
                'updated_at' => '2020-06-18 10:39:23',
            ),
            14 => 
            array (
                'id' => 15,
                'user_id' => NULL,
                'name' => 'Precisely Personal Access Client',
                'secret' => '7JTEGB4rfrlfFDH3VWKt6UXElUBINSpX260nCxRd',
                'redirect' => 'http://localhost',
                'personal_access_client' => 1,
                'password_client' => 0,
                'revoked' => 0,
                'created_at' => '2020-06-21 00:31:30',
                'updated_at' => '2020-06-21 00:31:30',
            ),
            15 => 
            array (
                'id' => 16,
                'user_id' => NULL,
                'name' => 'Precisely Password Grant Client',
                'secret' => 'QGhyA0fUeXduCzQ2RjgNLqgwAqFLQl1MZkNj7JIk',
                'redirect' => 'http://localhost',
                'personal_access_client' => 0,
                'password_client' => 1,
                'revoked' => 0,
                'created_at' => '2020-06-21 00:31:30',
                'updated_at' => '2020-06-21 00:31:30',
            ),
        ));
        
        
    }
}