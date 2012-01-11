<?php

/**
 * MK_Filter
 *
 * Filtrowanie danych
 *
 * @category MK
 * @package	MK_Filter
 */
class MK_Filter {

    /**
     * Przeksztalca wybrane wartosci pól w tablicy na wielkie litery
     *
     * @param $fields
     * @param $field
     * @param $word
     * @internal param \field $string
     * @internal param \word $string
     *
     * @return string
     */
	public static function convertToUcWord($fields, $field, $word) {
		if (in_array($field, $fields)) {
			if (function_exists('mb_convert_case')) {
				$word = mb_convert_case($word, MB_CASE_TITLE, 'UTF-8');
			} else {
				$word = ucwords($word);
			}
		}
		return $word;
	}

	/**
	 * Zamienia przeformatowaną kwotę na float, np.
	 *  "1 234 567,89" na 1234567.89
	 *  "1.234.567,-" na 1234567
	 *
	 * @param string $argName - nazwa sprawdzanego argumentu
	 * @param array $args - tablica argumentów w której znajduje się interesujący nas argument
	 *
	 * @return float
	 */
	public static function getFloatFromStringArgument($argName, array $args) {
		if (array_key_exists($argName, $args)) {
			return (float) str_replace(array(' ', '.', ',', ',-'), array('', '', '.', ''), $args[$argName]);
		} else {
			return null;
		}
	}

	/**
	 * Usunięcie niechcianych znaków z tekstu (filtrowanie).
	 * ZERO WIDTH SPACE: U200B (dec: 8203)
	 *
	 * @param string $string
	 *
	 * @return string
	 */
	public static function removeUnwantedChars($string) {
		return str_replace(html_entity_decode('&#8203;', ENT_NOQUOTES, 'UTF-8'), '', $string);
	}

	/**
	 * Formatowanie liczby do waluty
	 *
	 * @param float $amount
	 * @param boolean $withCurrency
	 *
	 * @return string
	 */
	public static function getCurrency($amount, $withCurrency=true) {
		if (empty($amount)) {
			$amount = 0;
		}
		if (!is_numeric($amount)) {
			return $amount;
		}
		$amount = number_format($amount, MK_PRECISION_NUMBER, ',', ' ');

		if ($withCurrency) {
			$amount .= ' zł.';
		}

		return $amount;
	}

	/**
	 * Formatowanie liczby do kwot pieniężnych urzędowych postaci '4.500.000,00' lub '4.500.000,00 zł.'
	 *
	 * @param float $amount
	 * @param boolean $withCurrency
	 *
	 * @return string
	 */
	public static function getMoneyAmount($amount, $withCurrency=true) {
		if (!is_numeric($amount)) {
			return $amount;
		}

		$amount = number_format($amount, MK_PRECISION_NUMBER, ',', '.');

		if ($withCurrency) {
			$amount .= ' zł.';
		}

		return $amount;
	}

	/**
	 * Pobranie wartości elementu, jeżeli istnieje i jest typu string.
	 * W przeciwnym wypadku zwraca pusty ciąg znaków (domyślnie).
	 *
	 * @param string $argName
	 * @param array $args
	 * @param mixed $defaultValue = ''
	 * @return string
	 */
	public static function stringValue($argName, array $args, $defaultValue='') {
		return MK_Validator::stringArgument($argName, $args) ? $args[$argName] : $defaultValue;
	}

	/**
	 * Pobranie wartości elementu, jeżeli istnieje i jest typu integer.
	 * W przeciwnym wypadku zwraca 0 (domyślnie).
	 *
	 * @param string $argName
	 * @param array $args
	 * @param mixed $defaultValue = 0
	 * @param boolean $canBeZero = false
	 * @return integer
	 */
	public static function integerValue($argName, array $args, $defaultValue=0, $canBeZero=false) {
		return MK_Validator::integerArgument($argName, $args, $canBeZero) ? intval($args[$argName]) : $defaultValue;
	}

	/**
	 * Pobranie wartości elementu, jeżeli istnieje i jest typu float.
	 * W przeciwnym wypadku zwraca 0 (domyślnie).
	 *
	 * @param string $argName
	 * @param array $args
	 * @param mixed $defaultValue = 0
	 * @return float
	 */
	public static function floatValue($argName, array $args, $defaultValue=0) {
		$value = MK_Validator::isDefined($argName, $args) ? $args[$argName] : $defaultValue;
		return floatval(is_string($value) ? str_replace('.', '', $value) : $value);
	}

	/**
	 * Pobranie wartości elementu, jeżeli istnieje i ma postać zbliżoną do JSON-a.
	 * W przeciwnym wypadku zwraca pustą tablicę (domyślnie).
	 *
	 * @param string $argName
	 * @param array $args
	 * @param mixed $defaultValue = array()
	 * @return array
	 */
	public static function jsonValue($argName, array $args, $defaultValue=array()) {
		return MK_Validator::isDefined($argName, $args) ? ( ($args[$argName][0] == '{') ? json_decode($args[$argName], true) : $args[$argName] ) : $defaultValue;
	}

	/**
	 * Formatowanie liczby do wartości procentowej z dokładnością do kilku miejsc po przecinku
	 * Jako wartość należy podać współczynnik, np. 1 = 100%, 0.5 = 50%, 0.1234 = 12,34%
	 *
	 * @param float $value
	 * @return string
	 */
	public static function getPercentage($value) {
		if (!is_numeric($value)) {
			return $value;
		}

		$value = number_format($value * 100, MK_PRECISION_PERCENT, ',', '');

		return $value . '%';
	}

	/**
	 * Formatowanie daty do urzędowej formy.
	 * Z "2011-06-29 03:52:92" na "29 czerwca 2011 roku".
	 *
	 * @param String $dateText
	 * @return String
	 */
	public static function getFormatDate($dateText) {
		$dateTime = strtotime($dateText);
		$monthText = array('', 'stycznia', 'lutego', 'marca', 'kwietnia', 'maja', 'czerwca', 'lipca', 'sierpnia', 'września', 'października', 'listopada', 'grudnia');
		return date('d', $dateTime) . ' ' . $monthText[date('n', $dateTime)] . ' ' . date('Y', $dateTime) . ' roku';
	}

}