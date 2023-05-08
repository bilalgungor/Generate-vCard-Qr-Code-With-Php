<?php
require_once __DIR__ . '/vendor/autoload.php';

use chillerlan\QRCode\QRCode;
use chillerlan\QRCode\QROptions;


if (isset($_POST['generate'])) {
    $name = $_POST['name'];
    $lname = $_POST['lname'];
    $phone = $_POST['phone'];
    $dphone = $_POST['dphone'];
    $mphone = $_POST['mphone'];
    $email = $_POST['email'];
    $orgName = $_POST['orgName'];
    $position = $_POST['position'];

    $sortName = $lname . ";" . $name;
    $fullName = $name . " " . $lname;

    $codeContents  = 'BEGIN:VCARD' . "\n";
    $codeContents .= 'VERSION:3.0' . "\n";
    $codeContents .= 'N:' . $sortName . "\n";
    $codeContents .= 'FN:' . $fullName . "\n";
    $codeContents .= 'ORG:' . $orgName . "\n";
    $codeContents .= 'TITLE:' . $position . "\n";

    if($mphone != "") $codeContents .= 'TEL;TYPE=İş Mobil:' . $mphone . "\n";
    $codeContents .= 'TEL;TYPE=İş Telefon:' . $phone. "\n";
    $codeContents .= 'TEL;TYPE=Dahili:' . $dphone . "\n";

    $codeContents .= 'EMAIL;TYPE=WORK:' . $email . "\n";
    $codeContents .= 'END:VCARD';
}


?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Generate vCard Qr Code</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/css/bootstrap.min.css" />
</head>

<body>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <form method="POST" class="mt-5">
                    <div class="form-group">
                        <label for="name">Ad:</label>
                        <input type="text" class="form-control" id="name" name="name" placeholder="Ad" value="<?php echo isset($name) ? $name : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="lname">Soyad:</label>
                        <input type="text" class="form-control" id="lname" name="lname" placeholder="Soyad" value="<?php echo isset($lname) ? $lname : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="phone">İş Telefonu:</label>
                        <input type="tel" class="form-control" id="phone" name="phone" placeholder="İş Telefonu" value="<?php echo isset($phone) ? $phone : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="dphone">Masa Telefonu Dahili:</label>
                        <input type="tel" class="form-control" id="dphone" name="dphone" placeholder="Masa Telefonu Dahili" value="<?php echo isset($dphone) ? $dphone : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="mphone">Mobil İş Telefonu:</label>
                        <input type="tel" class="form-control" id="mphone" name="mphone" placeholder="Mobil İş Telefonu" value="<?php echo isset($mphone) ? $mphone : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="email">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" value="<?php echo isset($email) ? $email : '' ?>">
                    </div>

                    <div class="form-group">
                        <label for="orgName">Firma:</label>
                        <input type="text" class="form-control" id="orgName" name="orgName" placeholder="Firma" value="<?php echo isset($orgName) ? $orgName : '' ?>">
                    </div>
                    <div class="form-group">
                        <label for="position">Pozisyon:</label>
                        <input type="text" class="form-control" id="position" name="position" placeholder="Pozisyon" value="<?php echo isset($position) ? $position : '' ?>">
                    </div>


                    <button type="submit" name="generate" class="btn btn-primary mt-2">Oluştur</button>
                </form>
            </div>
            <?php if (isset($_POST['generate'])) { ?>
                <div class="col-md-6 mt-5">
                    <?php
                    $qrCodeOptions = new QROptions([
                        'outputType' => QRCode::OUTPUT_MARKUP_SVG,
                        'eccLevel'   => QRCode::ECC_L,
                    ]);
                    $svgBase64 = (new QRCode($qrCodeOptions))->render($codeContents);
                    $svgString = base64_decode(str_replace('data:image/svg+xml;base64,', '', $svgBase64));
                    $dom = new \DOMDocument();
                    if ($dom->loadXML($svgString)) { 
                        $paths = $dom->getElementsByTagName('path');
                        foreach ($paths as $path) {
                            $class = $path->getAttribute('class');
                            if (strpos($class, 'qr-5632') !== false) {
                                $path->setAttribute('fill', '#e30613');
                            }
                        }
                        // output modified SVG
                        $svgCode = $dom->saveXML();
                        file_put_contents('qr_code.svg', $svgCode);
                        echo '<img src="qr_code.svg" alt="QR Code" class="img-fluid">';
                        echo '<a href="qr_code.svg" download>İndir</a>';
                    } else {
                        echo 'Invalid XML';
                    }
                    ?>
                </div>
            <?php
            } ?>
        </div>

    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap/5.2.3/js/bootstrap.min.js"></script>
</body>

</html>