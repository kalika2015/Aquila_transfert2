<?php

require_once 'controllers/controller.php';

//delete expired file
auto_delete();

//twig config
$loader = new Twig_Loader_Filesystem('views');
$twig = new Twig_Environment($loader, [
    'cache' => false,
]);
//end twig


//get url basename
$link = basename($_SERVER['REQUEST_URI']);


//replace project name by /
if($link == 'aquila2') {
    $link = '/';
}


if($link == 'accueil' || $link == '/') {

    echo $twig->render('accueil.twig');

    //when submit button is clicked
    if(isset($_POST['submitBtn'])) {
        if(isset($_POST['name']) && isset($_POST['transmitter_email']) && isset($_POST['receiver_email']) && isset($_POST['message']) && $_FILES['file']['name']) {
            $random_value = random_value();
            $path = $_FILES['file']['name'];
            $file_url = compress($path);

            //insert new transmitter (emetteur)
            new_emetteur($_POST['name'], $_POST['transmitter_email'], $_POST['message'], $random_value);

            // echo $_POST['name'] . ' / ' . $_POST['transmitter_email'] . ' / ' . $_POST['message'] . ' / ' . $random_value;

            // get transmitter id
            $id_emetteur = get_emetteur_id($_POST['name'], $_POST['transmitter_email'], $_POST['message'], $random_value);

            //insert the file
            new_file($file_url . '.zip', $id_emetteur);

            //insert receiver
            new_receiver($_POST['receiver_email'], $id_emetteur);

            //get file id and receiver id
            $file_id = get_file_id($id_emetteur);
            $receiver_id = get_receiver_id($id_emetteur);

            //insert link between file and receiver
            new_file_receiver_link($file_id, $receiver_id);

            //send mail
            $message_for_transmitter = '
                <html>
                    <head>
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                    </head>
                    <body>
                        <div class="text-center">
                            <img src="http://papam.promo-21.codeur.online/aquila2/public/images/logo.png" alt="" width="2000">
                            <h3>Aquila</h3>
                            <small>transfert de fichier</small>
                            <h2>Fichiers envoyés à</h2>
                            <h2>' . $_POST['receiver_email'] . '</h2>
                            <p>Les fichiers seront supprimés dans 7 jours</p>
                            <p>Merci d\'avoir utilisé Aquila. Un mail de confirmation vous seras envoyé dés que vos fichiers seront téléchargés.</p>
                        </div>
                    </body>
                </html>
                ';


            send_mail($_POST['transmitter_email'], $message_for_transmitter);

            $base_url_sent = str_replace('upload/','',$file_url);
            $url_sent = base64_encode($base_url_sent);

            $message_for_receiver = '
                <html>
                    <head>
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                    </head>
                    <body>
                        <div class="text-center">
                            <img src="http://papam.promo-21.codeur.online/aquila2/public/images/logo.png" alt="" width="200">
                            <h3>Aquila</h3>
                            <small>transfert de fichier</small>
                            <h2>' . $_POST['name'] . '</h2>
                            <h4> vous a envoyé un fichier.</h4>
                            <p>Les fichiers seront supprimés dans 7 jours</p>
                            <p><strong>Message : </strong>' . $_POST['message'] . '</p>
                            <a href="http://papam.promo-21.codeur.online/aquila2/telecharger?fichier=' . $url_sent . '" target="_blank">
                                <button class="btn btn-primary">Télécharger</button>
                            </a>
                        </div>
                    </body>
                </html>
                ';

            send_mail($_POST['receiver_email'], $message_for_receiver);

            echo $twig->render('success.twig');

        }
        else {
            echo "<script>alert('Veuillez remplir tous les champs !!!');</script>";
        }
    }

} else if($link == "success") {
    echo $twig->render('success.twig');
}

else if(preg_match('#telecharger#i', $link)) {

    if(isset($_POST['downloadBtn'])) {

        if(isset($_GET['fichier'])) {
            $file = 'upload/' . base64_decode($_GET['fichier']) . '.zip';
            if (file_exists($file)) {
                header('Content-Description: File Transfer');
                header('Content-Type: application/octet-stream');
                header('Content-Disposition: attachment; filename="'.basename($file).'"');
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');
                header('Content-Length: ' . filesize($file));
                readfile($file);


                $download_feed_back = '
                <html>
                    <head>
                    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
                    </head>
                    <body>
                        <div class="text-center">
                            <img src="http://papam.promo-21.codeur.online/aquila2/public/images/logo.png" alt="" width="200">
                            <h3>Aquila</h3>
                            <small>transfert de fichier</small>
                            <h2>' . $_POST['name'] . '</h2>
                            <h4> Votre fichier vient d\'être téléchargé.</h4>
                            <p>Merci d\'avoir utilisé Aquila !!!</p>
                        </div>
                    </body>
                </html>
                ';

                send_mail($_POST['transmitter_email'], $download_feed_back);


                exit;

            }
            else {
                echo $twig->render('accueil.twig', ['error' => 'Le fichier que vous essayez de télécharger n\'existe pas ou a été supprimé. !!!']);
                // echo "<script>alert('Le fichier que vous essayez de télécharger n\'existe pas ou a été supprimé. !!!');</script>";
            }
        }
    }
    echo $twig->render('download.twig');
}

else {
    echo $twig->render('error.twig', ['link' => $link]);
}

