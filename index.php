<?php

if(isset($_FILES['monFichier'])){
    $fichier = $_FILES['monFichier'];
    var_dump($fichier);
    if($fichier['error'] != 0){
        //erreurs
        switch($fichier['error']){
            case UPLOAD_ERR_FORM_SIZE:
                die('Le fichier est trop gros');
                break;
            default:
                die('erreur inconnue');

        }
    }else{
        // Crée un nom pour le fichier
        $nameparts = explode('.', $fichier['name']);
        $ext = end($nameparts);;
        $newname = 'i'.base_convert(rand(0, time()), 10, 26).'.'.$ext; // base_convert() -> convertit un chiffre base 10 en base 26 
        
        // Déplacer le fichier à son nouvel emplacement
        $path = $newname;
        if(!@move_uploaded_file($fichier['tmp_name'], $path)){
            die('Problème lors de la création du fichier');
        };
        
        // Redimension -- Voir manuel PHP
        $percent = 0.5;
        
        // Calcul les nouvelles dimensions
        list($width, $height) = getimagesize($newname);
        $new_width = $width * $percent;
        $new_height = $height * $percent;
        
        // Redimensionne
        $image_p = imagecreatetruecolor($new_width, $new_height);
        
        if($ext == 'jpg' || $ext == 'jpeg'){
            $image = imagecreatefromjpeg($newname);
        }elseif($ext == 'png'){
            $image = imagecreatefrompng($newname);
        }
        
        imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height);

        // Affichage
        imagejpeg($image_p, 'resized_'.$newname, 100);
    }
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Document</title>
</head>
<body>
   <?php if(!isset($_FILES['monFichier'])): ?>
        <form action="index.php" method="post" enctype="multipart/form-data"> <!-- Encodage envoit de fichier -->

           <input type="hidden" name="MAX_FILE_SIZE" value="10000000"> <!-- Définit la limite de taille du fichier -->
           <label for="monFichier">Envoyez votre fichier</label>
           <input type="file" name="monFichier" id="monFichier">
            <input type="submit" value="Envoyer">

        </form>
    <?php endif; ?>
    
    <?php if(isset($_FILES['monFichier'])): ?>
        <img src="<?php echo $newname; ?>" alt="">
        <img src="<?php echo 'resized_'.$newname; ?>" alt="">
    <?php endif; ?>
    
</body>
</html>