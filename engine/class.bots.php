<?php
class MessageBot{

    public $message = '';
    public function prepMessage($array){
        $this->message = "Request from $array[name] ($array[email]), through Resume's Contact form: \n
            <b><i>New request</i></b> \n
            <b>Name:</b> $array[name]
            <b>E-mail:</b> $array[email]
            <b>Contact:</b> $array[contact]
            <b>Message:</b>
            $array[message]";
    }
	function sendToTelegramBot()
	{
        $authData = $this->getAuthData();
        $params = array(
            'chat_id' => $authData['tg_uid'], // id получателя сообщения
            'text' => $this->message, // текст сообщения
            'parse_mode' => 'HTML', // режим отображения сообщения, не обязательный параметр
        );

        $curl = curl_init();
        $options = array(
			CURLOPT_URL => "https://api.telegram.org/bot$authData[tg_bot_token]/sendMessage", // адрес api телеграмм-бота
			CURLOPT_RETURNTRANSFER => true,
            CURLOPT_POST => true,       // отправка данных методом POST
            CURLOPT_TIMEOUT => 10,      // максимальное время выполнения запроса
            CURLOPT_POSTFIELDS => $params   // параметры запроса
            // CURLOPT_NOBODY => true,  // true для исключения тела ответа из вывода.
			// CURLOPT_FOLLOWLOCATION => 1,
			// CURLOPT_CAINFO => $certs,
			// CURLOPT_CAPATH => $certs,
			// CURLOPT_SSL_VERIFYHOST => 0,			# Если сертификаты не подошли.
			// CURLOPT_SSL_VERIFYPEER => 0,
		);
		curl_setopt_array($curl , $options);
        return curl_exec($curl);

	}
    private function getAuthData(){
        require_once $_SERVER['DOCUMENT_ROOT'].'/engine/class.crypt.php';

        $dbAction = new Action();
        $authDataCrypted = $dbAction->getAssoc($dbAction->query('SELECT * FROM '.TABLE_AUTH.' WHERE id != 0 LIMIT 1'));

        $crypt = new Crypt(['value'=>$authDataCrypted['tg_uid'],'key'=>$authDataCrypted['key']]);

        return ['tg_uid' => $crypt->decoded, 'tg_bot_token' => $crypt->decrypt($authDataCrypted['tg_bot_token'])];
    }
}