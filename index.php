<?php
require_once 'libraries/PHPMailer/PHPMailerAutoload.php';

if (isset($_POST) && !empty($_POST)) {

    $userId = $_POST['userId'];
    $busRoute = $_POST['busRoute'];
    $position = $_POST['position'];
    $participant = $_POST['participant'];
    $gender = $_POST['gender'];
    $age = $_POST['age'];
    $height = $_POST['height'];
    $weight = $_POST['weight'];
    $ride = $_POST['ride'];
    $road = $_POST['road'];
    $driving = $_POST['driving'];
    $vehicle = $_POST['vehicle'];
    $unitHeight = $_POST['unitHeight'];
    $unitWeight = $_POST['unitWeight'];

    $arrCSV = array(
        array('Bus route: ' . $busRoute, 'Position: ' . $position),
        array('Participant: ' . $participant, 'Gender: ' . $gender, 'Age: ' . $age, 'Height: ' . $height . ' ' . $unitHeight, 'Weight: ' . $weight . ' ' . $unitWeight),
        array('Entire trip:', 'Ride (' . $ride . ')', 'Road (' . $road . ')', 'Driving (' . $driving . ')', 'Vehicle (' . $vehicle . ')'),
        array(''),
        array(''),
        array('Date & time', 'Comfort level', 'GPS longitude', 'GPS latitude'),
    );

    $feelsDetail = json_decode($_POST['feelsDetail'], true);

    $i = 7;
    foreach ($feelsDetail as $key => $row) {
        $arrCSV[count($arrCSV) - 1] = array($row['time'], $row['feel'], $row['lng'], $row['lat']);
    }

    $full_path = md5((new DateTime())->getTimestamp()) . '_' . md5($userId) . '_' . md5($participant) . '_' . md5($participant) . '_' . md5($gender) . '_' . md5($age) . '_' . md5($busRoute) . '.csv';

    $fp = fopen($full_path, 'w');
    foreach ($arrCSV as $fields) {
        fputcsv($fp, $fields);
    }

    fclose($fp);

    chmod($full_path, 0777);

    $mail = new PHPMailer;
    $mail->CharSet = 'UTF-8';
    $mail->SMTPDebug = 1;
    $mail->isSMTP();
    $mail->Host = 'smtp.gmail.com';
    $mail->Port = 587;
    $mail->SMTPAuth = true;
    $mail->Username = 'echatpro@gmail.com';
    $mail->Password = '@developers13';

    $mail->From = 'echatpro@gmail.com';
    $mail->FromName = 'Dataloger';
    $mail->addAddress('nghianv.dev@gmail.com', 'nghianv');

    $mail->WordWrap = 50;
    $mail->addAttachment($full_path, 'Excel file');
    $mail->isHTML(true);

    $mail->Subject = 'Timestamp Datalogger - ' . $participant;
    $mail->Body = 'New data logger from <span style="color: orange; font-size: 20px">' . $participant . '</span>';
    $mail->AltBody = 'Datalogger app new data';

    if (!$mail->send()) {
        unlink($full_path);
        echo json_encode(array(error => true, message => $mail->ErrorInfo));
    } else {
        unlink($full_path);
        echo json_encode(array(error => false, message => "Push data and send mail success!"));
    }
}
?>
