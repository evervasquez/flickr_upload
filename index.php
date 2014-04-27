<?php
require_once("phpFlickr/phpFlickr.php");
$error = 0;
$f = null;
$status = null;
if ($_POST) {
    /* Check if both name and file are filled in */
    if (!$_POST['name'] || !$_FILES["file"]["name"]) {
        $error = 1;
    } else {
        /* Check if there is no file upload error */
        if ($_FILES["file"]["error"] > 0) {
            echo "Error: " . $_FILES["file"]["error"] . "<br />";
        } else if ($_FILES["file"]["type"] != "image/jpg" && $_FILES["file"]["type"] != "image/jpeg" && $_FILES["file"]["type"] != "image/png" && $_FILES["file"]["type"] != "image/gif") {
            /* Filter all bad file types */
            $error = 3;
        } else if (intval($_FILES["file"]["size"]) > 525000) {
            /* Filter all files greater than 512 KB */
            $error = 4;
        } else {
            $dir = dirname($_FILES["file"]["tmp_name"]);
            $newpath = $dir . "/" . $_FILES["file"]["name"];
            rename($_FILES["file"]["tmp_name"], $newpath);
            /* Call uploadPhoto on success to upload photo to flickr */
            $status = uploadPhoto($newpath, "pf4_forum-images");
            if (!$status) {
                $error = 2;
            }
        }
    }
}

function uploadPhoto($path, $title) {
    $apiKey = "cdbdc945135e3eab76782b57c413e6f5";
    $apiSecret = "8d5aa345566f9519";
    $permissions = "write";
    $token = "72157644304727396-659f761c51fd1f65";

    $f = new phpFlickr($apiKey, $apiSecret, true);
    $f->setToken($token);
    $id = $f->sync_upload($path, $title);
    $photo = $f->photos_getInfo($id,$secret = NULL );
    $photoUrlSmall = $f->buildPhotoURL($photo['photo'], "square"); 
    $photoUrlOriginal = $f->buildPhotoURL($photo['photo'], "original"); 
    $result['square'] = $photoUrlSmall;
    $result['original'] = $photoUrlOriginal;
    return $result;
}
?>
<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html>
    <head>
        <title>HackerPics</title>

        <link rel="stylesheet" href="http://yui.yahooapis.com/2.7.0/build/reset-fonts-grids/reset-fonts-grids.css" type="text/css">
        <link rel="stylesheet" href="style.css" type="text/css">
        <style>
        </style>

    </head>
    <body>
                <div id="mainbar" class="yui-t7">	  

                    <?php
                    if (isset($_POST['name']) && $error == 0) {

                        echo "<img src='" . $status['square'] . "' >";
                    } else {
                        if ($error == 1) {
                            echo "  <font color='red'>Please provide both name and a file</font>";
                        } else if ($error == 2) {
                            echo "  <font color='red'>Unable to upload your photo, please try again</font>";
                        } else if ($error == 3) {
                            echo "  <font color='red'>Please upload JPG, JPEG, PNG or GIF image ONLY</font>";
                        } else if ($error == 4) {
                            echo "  <font color='red'>Image size greater than 512KB, Please upload an image under 512KB</font>";
                        }
                        ?>
                    <div id="input-file">
                        <form  method="post" accept-charset="utf-8" enctype='multipart/form-data'>
                            <p>Picture: <input type="file" name="file"/><input type="submit" value="Subir Imagen"></p>
                        </form>
                    </div>
                        <?php
                    }
                    ?>
                </div>
            <div id="ft"><p></p></div>
        </div>
    </body>
</html>
