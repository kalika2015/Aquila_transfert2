<?php

require_once 'models/model.php';

/**
 * new sending file
 */

/**
 * insert emetteur
 */
function new_emetteur($nom_emetteur, $email_emetteur, $message, $random_value) {
    insert_emetteur($nom_emetteur, $email_emetteur, $message, $random_value);
}


/**
 * insert file
 */
function new_file($file_url, $id_emetteur) {
    insert_file($file_url, $id_emetteur);
}



/**
 * insert receiver
 */
function new_receiver($email_recepteur, $id_emetteur) {
    insert_receiver($email_recepteur, $id_emetteur);
}



/**
 * insert link between receiver and file
 */
function new_file_receiver_link($idfile, $idreceiver) {
    file_receiver_link($idfile, $idreceiver);
}




/**
 * get id emetteur
 * @return emetteur id
 */
function get_emetteur_id($nom_emetteur, $email_emetteur, $message, $random_value) {
    $request =  get_emetteur($nom_emetteur, $email_emetteur, $message, $random_value);

    $id = '';

    while($result = $request->fetch()) {
        $id = $result['id_emetteur'];
    }

    return $id;
}



/**
 * get file id
 * @return file id
 */
function get_file_id($id_emetteur) {
    $request = get_file($id_emetteur);

    $file_id = '';

    while($result = $request->fetch()) {
        $file_id = $result['id_fichier'];
    }

    return $file_id;
}


/**
 * get receiver id
 * @return receiver id
 */
function get_receiver_id($id_emetteur) {
    $request = get_receiver($id_emetteur);

    $receiver_id = '';

    while($result = $request->fetch()) {
        $receiver_id = $result['id_recepteur'];
    }

    return $receiver_id;
}



/**
 * Moves uploaded file to our upload folder
 */
function move_file($path) {
    $pathto = 'upload/'.$path;
    move_uploaded_file( $_FILES['new_file']['tmp_name'], $pathto) or
    die( "Le format du fichier n\'est pas compatibe !");

    return $pathto;
}


/**
 * send mail from transmitter to receiver
 */
function send_mail($mail, $message) {
    // $file_url = get_file_downloaded_url($emetteur_id);

    $header = "MIME-Version: 1.0\r\n";
    $header .= 'From:"aquila"<support@aquila.com>'."\n";
    $header .= 'Content-Type:text/html; charset="utf-8"'."\n";
    $header .= 'Content-Transfert-Encoding: 8bit';

    mail($mail,"Aquila tranfert de fichier", $message, $header);
}


/**
 * get emetteur name
 * @return emetteur name
 */
function get_emetteur_name($nom_emetteur, $email_emetteur, $message, $random_value) {
    $request =  get_emetteur($nom_emetteur, $email_emetteur, $message, $random_value);

    $name = '';

    while($result = $request->fetch()) {
        $name = $result['nom_emetteur'];
    }

    return $name;
}




/**
 * get file downloaded url
 */
function get_file_downloaded_url($id_emetteur) {
    $request = get_file_downloaded($id_emetteur);

    $file_url = '';

    while($result = $request->fetch()) {
        $file_url = $result['file_url'];
    }

    return $file_url;
}



/**
 * delete file seven days after sending
 */
function auto_delete() {
    $request = get_expired_file();
    while($result = $request->fetch()) {
        delete_file_db($result['id_fichier']);
        $file = $result['file_url'];
        if(file_exists($file)) {
            unlink($file);
        }
    }
}




/**
 * generate random value
 * @return random with 10 characters
 */
function random_value() {
    $rand = '';
    for($i = 0 ; $i < 9 ; $i++) {
        $rand .= mt_rand(0, 9);
    }

    return intval($rand);
}



/**
 * compress file uploaded in zip file
 */
function compress($nameFile) {
    // $nameFile = $_FILES['file']['name'];
    $pathinfo = pathinfo($nameFile);
    $file = $pathinfo['filename'];
    $tmpName = $_FILES['file']['tmp_name'];
    $download_folder = 'upload/';
    $filepath = "upload_tmp/" . $_FILES["file"]["name"];

    if(move_uploaded_file($tmpName, $filepath)) {
        $zip = new ZipArchive();
        $filecompress = $download_folder.$file.".zip";

        $compress = $zip->open($filecompress, ZIPARCHIVE::CREATE);

        if ($compress === true) {
            $zip->addFile($filepath);
            $zip->close();
        }
        else {
            echo "Oh No! Error";
        }
    }
    unlink($filepath);

    $zip_file_url = $download_folder . $file;
    return $zip_file_url;
}
