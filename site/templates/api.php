<?php

namespace ProcessWire;

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
	if ($page->template == 'api') {
		if (input()->urlSegment(1)) {
			switch ($page->name) {
				case 'tbot':
					$file = './api/' . $page->name . '/' . $method . '/' . input()->urlSegment(1) . '.php';
					if (file_exists($file)) {
						include($file);
					} else {
						$result = setError('Missing method handler - ' . input()->urlSegment(1), $result);
					}
					break;
				default:
					$result['version'] = $page->version;
					break;
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
