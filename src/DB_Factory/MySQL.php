<?php
namespace App\DB_Factory;

use mysqli;

class MySQL implements DBInterface
{

    public function connect($parameter)
    {
        //Connect to the database
        $con = mysqli_connect(
            $parameter['HOSTNAME'],
            $parameter['USERNAME'],
            $parameter['PASSWORD'],
            $parameter['DATABASE']
        )
        or die (
            "html>script language='JavaScript'>alert('Impossible de se connecter à la base de données ! Réessayez plus tard.'),history.go(-1)/script>/html>"
        );
        return $con;
    }

    public function query($connexion, $query): ?array
    {
        /*$response = null;

        // Check connection
        if ($connexion->connect_error) {
            die("Connection failed: " . $connexion->connect_error);
        }

        if ($connexion->query($query) === TRUE) {
            $response =  ["New record created successfully"];
        } else {
            $response = ["Error: " . $query . "<br>" . $connexion->error];
        }

        $connexion->close();*/
        $response = null;
        //Check if records exist
        $search = mysqli_query($connexion, $query);

        if(is_object($search)){
            foreach ($search as $row ) {
                $response[] = $row;
            }
        }

        return $response;
    }
}