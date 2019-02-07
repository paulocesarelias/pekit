<?php

namespace Pekit;

class Utilities
{
		/**
		 * Gerar UUID
		 *
		 * @param $data
		 * @return string
		 */
		public static function uuid($data = "")
		{
				if (!$data)
						$data = openssl_random_pseudo_bytes(16);

				assert(strlen($data) == 16);

				$data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
				$data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

				return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
		}

		/**
		 * A prettier dd
		 *
		 * @param $value
		 */
		public static function dd($value)
		{
				echo '<pre>';
				echo var_dump($value);
				echo '</pre>';
				exit;
		}

		/**
		 * Valida um NIF PT
		 *
		 * @param $nif
		 * @param bool $ignoreFirst
		 * @return bool
		 */
		public static function validateNIF($nif, $ignoreFirst = true)
		{
				$nif = trim($nif);

				if (!is_numeric($nif) || strlen($nif) != 9)
						return false;
				else {
						$nifSplit = str_split($nif);
						//O primeiro digíto tem de ser 1, 2, 5, 6, 8 ou 9
						//Ou não, se optarmos por ignorar esta "regra"
						if (
							in_array($nifSplit[0], array(1, 2, 5, 6, 7, 8, 9))
							||
							$ignoreFirst
						) {
								//Calculamos o dígito de controlo
								$checkDigit = 0;
								for ($i = 0; $i < 8; $i++) {
										$checkDigit += $nifSplit[$i] * (10 - $i - 1);
								}
								$checkDigit = 11 - ($checkDigit % 11);
								//Se der 10 então o dígito de controlo tem de ser 0
								if ($checkDigit >= 10) $checkDigit = 0;
								//Comparamos com o último dígito
								if ($checkDigit == $nifSplit[8])
										return true;
								else
										return false;
						} else
								return false;
				}
		}

		/**
		 * A prettier json_encode().
		 *
		 * @param mixed $data
		 * @return string
		 */
		public static function jsonEncode($data)
		{
				return json_encode($data, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
		}

		/**
		 * Escreve no ficheiro Env
		 *
		 * @param $key
		 * @param $value
		 */
		public static function writeInEnv($key, $value)
		{
				$pattern = "#(^\s*$key\s*=\s*)(.*)#m";
				$envFile = '.env';
				$string = file_get_contents($envFile);

				if ($value) $strValue = 'true';
				else $strValue = 'false';

				$fileString = preg_replace($pattern,"$1$strValue",$string);
				file_put_contents($envFile,$fileString);
		}

		/**
		 * Faz truncate a um valor
		 *
		 * @example truncate(-1.49999, 2); // returns -1.49
		 * @example truncate(.49999, 3); // returns 0.499
		 * @param float $val
		 * @param string $f
		 * @return float
		 */
		public static function truncateValue($val, $f="0")
		{
				if(($p = strpos($val, '.')) !== false) {
						$val = floatval(substr($val, 0, $p + 1 + $f));
				}
				return $val;
		}
}