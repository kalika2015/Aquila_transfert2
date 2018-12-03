<?php

/**
 * database connexion function
 * @return pdo object 
 */

 function database_connexion() {
    try {
        $connexion = new PDO('mysql:host=localhost;dbname=aquila_file_transfert;charset=utf8','root','');

        return $connexion;
    }
    catch(Exception $ex) {
        die('Erreur de connexion à la base de données => ' . $ex->getMessage());
    }
}



/**
 * all insert functions
 */




/**
 * insert new user (transmitter)
 * @param string nom
 * @param string email_emetteur
 * @param string message
 */
function insert_emetteur($nom, $email_emetteur, $message, $random_value) {
    $con = database_connexion();
    $query = 'INSERT INTO emetteurs(nom_emetteur, email_emetteur, file_message, random_value) VALUES (?, ?, ?, ?)';
    $request = $con->prepare($query);
    $request->execute(array($nom, $email_emetteur, $message, $random_value));
}


/**
 * insert file url
 * @param string file url
 * @param string date inserting file
 * @param integer id transmitter (emetteur)
 */
function insert_file($url, $id_emetteur) {
    $con = database_connexion();
    $query = 'INSERT INTO fichiers(file_url, date_transfert, id_emetteur) VALUES (?, NOW(), ?)';
    $request = $con->prepare($query);
    $request->execute(array($url, $id_emetteur));
}



/**
 * insert receiver (destinataire)
 * @param string receiver email
 */
function insert_receiver($email_recepteur, $id_emetteur) {
    $con = database_connexion();
    $query = 'INSERT INTO recepteurs(email_recepteur, id_emetteur) VALUES (?, ?)';
    $request = $con->prepare($query);
    $request->execute(array($email_recepteur, $id_emetteur));
}



/**
 * insert link between file and receiver
 * @param integer id file
 * @param integer id receiver
 */
function file_receiver_link($id_file, $id_receiver) {
    $con = database_connexion();
    $query = 'INSERT INTO fichier_recepteur(id_fichier, id_recepteur) VALUES (?, ?)';
    $request = $con->prepare($query);
    $request->execute(array($id_file, $id_receiver));
}




/**
 * end insert functions
 */




 /**
  * all select functions
  */




/**
 * get emetteur id
 * @return request
 */
function get_emetteur($nom_emetteur, $email_emetteur, $message, $random_value) {
    $con = database_connexion($nom_emetteur, $email_emetteur, $message);
    $query = 'SELECT * FROM emetteurs WHERE nom_emetteur = ? AND email_emetteur = ? AND file_message = ? AND random_value = ?';
    $request = $con->prepare($query);
    $request->execute(array($nom_emetteur, $email_emetteur, $message, $random_value));

    return $request;
}


/**
 * get file id
 * @return request
 */
function get_file($id_emetteur) {
    $con = database_connexion();
    $query = 'SELECT * FROM fichiers WHERE id_emetteur = ?';
    $request = $con->prepare($query);
    $request->execute(array($id_emetteur));

    return $request;
}


/**
 * get receiver id
 * @return request
 */
function get_receiver($id_emetteur) {
    $con = database_connexion();
    $query = 'SELECT * FROM recepteurs WHERE id_emetteur = ?';
    $request = $con->prepare($query);
    $request->execute(array($id_emetteur));

    return $request;
}



/**
 * get file downloaded
 * @return request
 */
function get_file_downloaded($id_emetteur) {
    $con = database_connexion();
    $query = 'SELECT file_url FROM fichiers, recepteurs, fichier_recepteur WHERE fichiers.id_fichier = fichier_recepteur.id_fichier AND recepteurs.id_recepteur = fichier_recepteur.id_recepteur AND recepteurs.id_emetteur = ?';
    $request = $con->prepare($query);
    $request->execute(array($id_emetteur));

    return $request;
}




/**
  * get file with more than 7 days
  */
  function get_expired_file() {
    $con = database_connexion();
    $query = 'SELECT * FROM fichiers WHERE ADDDATE(date_transfert, INTERVAL 7 DAY) <= NOW()';
    $request = $con->prepare($query);
    $request->execute();

    return $request;
}





/**
 * end select functions
 */



 
