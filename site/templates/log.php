<?php

namespace ProcessWire;

error_reporting(E_ERROR | E_PARSE);

$valid_passwords = array ("adm_log" => "2EJwE82vuLp2Z");
$valid_users = array_keys($valid_passwords);
$user = $_SERVER['PHP_AUTH_USER'];
$pass = $_SERVER['PHP_AUTH_PW'];
$validated = (in_array($user, $valid_users)) && ($pass == $valid_passwords[$user]);
if (!$validated) {
  header('WWW-Authenticate: Basic realm="My Realm"');
  header('HTTP/1.0 401 Unauthorized');
  die ("Not authorized");
}

/**
 * Установка переменных для возврата в случае ошибки.
 *
 * @param string $error Текст ошибки
 * @param array $out Массив с данными которые возвращает запрос
 * @return array
 */
function setError($error, $out, $errorcode = 400) {
	http_response_code($errorcode);
	$out['statusCode'] = strval($errorcode);
	$out['error'] = $error;
	return $out;
}


http_response_code(200);
$result = [
	'statusCode' => '200',
	'error' => 'none'
];
$method = input()->requestMethod();

//urlSegment(1) - Папка или метод
//urlSegment(2) - Метод

if ($method) {
	if ($page->template == 'log') {
		if (input()->urlSegment(1)) {
			$file = 'log_' . input()->urlSegment(1) . '.txt';
			// $text = file_get_contents(__DIR__ . '/' . $file, true);
			$text = file_get_contents(__DIR__ . '\\' . $file, true);
			if (file_exists($file)) {
				$result = nl2br($text);
			} else {
				$result = setError('Missing method handler - ' . input()->urlSegment(1), $result);
			}
		} else {
			$result['version'] = $page->version;
		}
	} else {
		$result = setError('Entry point (' . $page->name . ') not defined.', $result);
	}
} else {
	$result = setError('No method.', $result);
}

if (is_array($result)) {
	echo json_encode($result, JSON_HEX_QUOT | JSON_HEX_TAG | JSON_HEX_AMP | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
} else {
	echo $result;
}
