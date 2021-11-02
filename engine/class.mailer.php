<?php
require $_SERVER['DOCUMENT_ROOT'].'/libs/phpmailer/PHPMailer.php';
require $_SERVER['DOCUMENT_ROOT'].'/libs/phpmailer/SMTP.php';
require $_SERVER['DOCUMENT_ROOT'].'/libs/phpmailer/Exception.php';

class Mailer{
    private $message = [];
    private $mail;
    public  $status = [];
    public function __construct($array){

        $this->prepMessage($array);
        $this->prepMailer();

        if (!empty($_FILES['files']['name'][0])) {
            $this->addFiles($_FILES['files']);
        }
    }
    public function prepMailer(){
        $this->mail = new PHPMailer\PHPMailer\PHPMailer();
        
        $this->mail->isSMTP();   
        $this->mail->CharSet = "UTF-8";
        $this->mail->SMTPAuth   = true;
        // $this->mail->SMTPDebug = 2;
        $this->mail->Debugoutput = function($str, $level) {$GLOBALS['status'][] = $str;};

        $authData = $this->getAuthData();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->Username   = $authData['email'];
        $this->mail->Password   = $authData['password'];
        $this->mail->SMTPSecure = 'ssl';
        $this->mail->Port       = 465;
        $this->mail->setFrom($this->message['email'], $array['name']);

        $this->mail->addAddress($authData['email']);
        // $this->mail->addAddress('yourMainEmail@gmail.com'); // If you need send message from your main tech email to your main email - you can change it here
        $this->mail->isHTML(true);
    }
    public function prepMessage($array){
        $this->message = [
            'name'  => $array['name'],
            'email' => $array['email'],
            'text'  => $array['message'],
            'title' => "Request from $array[name] < $array[email] >, through Resume's Contact form",
            'body'  => "<h2>New request</h2>
                        <b>Name:</b> $array[name]<br>
                        <b>E-mail:</b> $array[email]<br>
                        <b>Contact:</b> $array[contact]<br>
                        <b>Message:</b><br>$array[message]"
        ];
    }
    public function addFiles($files){
        for ($ct = 0; $ct < count($files['tmp_name']); $ct++) {
            $uploadfile = tempnam(sys_get_temp_dir(), sha1($files['name'][$ct]));
            $filename = $files['name'][$ct];
            if (move_uploaded_file($file['tmp_name'][$ct], $uploadfile)) {
                $this->mail->addAttachment($uploadfile, $filename);
                $this->status['fileResult'] = "Файл $filename прикреплён";
            } else {
                $this->status['fileResult'] = "Не удалось прикрепить файл $filename";
            }
        }
    }
    public function send(){
        $this->mail->Subject = $this->message['title'];
        $this->mail->Body = $this->message['body'];
        return $this->mail->send(); 
    }
    private function getAuthData(){
        require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.crypt.php';
        $dbAction = new Action();
        $authDataCrypted = $dbAction->getAssoc($dbAction->query('SELECT * FROM '.TABLE_AUTH.' WHERE id != 0 LIMIT 1'));
        $crypt = new Crypt(['value'=>$authDataCrypted['login'],'key'=>$authDataCrypted['key']]);
        return ['email' => $crypt->decoded, 'password' => $crypt->decrypt($authDataCrypted['password'])];
    }
}