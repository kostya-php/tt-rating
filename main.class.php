<?php
class Main {
	private $sql_login    = "mckay";
	private $sql_passwd   = "20061990";
	public $sql_database = "mckay";
	private $sql_host     = "localhost";

	public $conn_id;
	public $sql_query = Array();
	public $sql_err = Array();
	public $sql_res = Array();

	function sql_connect () {
		$this->conn_id=mysql_connect($this->sql_host,$this->sql_login,$this->sql_passwd)or die("Не удалось подключиться к базе данных: ".mysql_error());
		//$this->conn_log_id=mysql_connect($this->sql_host,$this->sql_login,$this->sql_passwd);
		mysql_query("SET NAMES 'utf8' COLLATE 'utf8_general_ci'");
		mysql_select_db($this->sql_database);
	}

	function sql_close () {
		mysql_close($this->conn_id);
	}

	function sql_execute ($id) {
		$this->sql_res[$id]=mysql_query($this->sql_query[$id],$this->conn_id);
		$this->sql_err[$id]=mysql_error();
	}
	function real_date_diff($date1, $date2 = NULL){
		$diff = array();
	 
		//Если вторая дата не задана принимаем ее как текущую
		if(!$date2) {
			$cd = getdate();
			$date2 = $cd['year'].'-'.$cd['mon'].'-'.$cd['mday'].' '.$cd['hours'].':'.$cd['minutes'].':'.$cd['seconds'];
		}
		 
		//Преобразуем даты в массив
		$pattern = '/(\d+)-(\d+)-(\d+)(\s+(\d+):(\d+):(\d+))?/';
		preg_match($pattern, $date1, $matches);
		$d1 = array((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[5], (int)$matches[6], (int)$matches[7]);
		preg_match($pattern, $date2, $matches);
		$d2 = array((int)$matches[1], (int)$matches[2], (int)$matches[3], (int)$matches[5], (int)$matches[6], (int)$matches[7]);
	 
		//Если вторая дата меньше чем первая, меняем их местами
		for($i=0; $i<count($d2); $i++) {
			if($d2[$i]>$d1[$i]) break;
			if($d2[$i]<$d1[$i]) {
				$t = $d1;
				$d1 = $d2;
				$d2 = $t;
				break;
			}
		}

		//Вычисляем разность между датами (как в столбик)
		$md1 = array(31, $d1[0]%4||(!($d1[0]%100)&&$d1[0]%400)?28:29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$md2 = array(31, $d2[0]%4||(!($d2[0]%100)&&$d2[0]%400)?28:29, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31);
		$min_v = array(NULL, 1, 1, 0, 0, 0);
		$max_v = array(NULL, 12, $d2[1]==1?$md2[11]:$md2[$d2[1]-2], 23, 59, 59);
		for($i=5; $i>=0; $i--) {
			if($d2[$i]<$min_v[$i]) {
				$d2[$i-1]--;
				$d2[$i]=$max_v[$i];
			}
			$diff[$i] = $d2[$i]-$d1[$i];
			if($diff[$i]<0) {
				$d2[$i-1]--;
				$i==2 ? $diff[$i] += $md1[$d1[1]-1] : $diff[$i] += $max_v[$i]-$min_v[$i]+1;
			}
		}
		 
		//Возвращаем результат
		return $diff;
	}
	function russian_date() {
        $translation = array(
            "am" => "дп",
            "pm" => "пп",
            "AM" => "ДП",
            "PM" => "ПП",
            "Monday" => "Понедельник",
            "Mon" => "Пн",
            "Tuesday" => "Вторник",
            "Tue" => "Вт",
            "Wednesday" => "Среда",
            "Wed" => "Ср",
            "Thursday" => "Четверг",
            "Thu" => "Чт",
            "Friday" => "Пятница",
            "Fri" => "Пт",
            "Saturday" => "Суббота",
            "Sat" => "Сб",
            "Sunday" => "Воскресенье",
            "Sun" => "Вс",
            "January" => "Января",
            "Jan" => "Янв",
            "February" => "Февраля",
            "Feb" => "Фев",
            "March" => "Марта",
            "Mar" => "Мар",
            "April" => "Апреля",
            "Apr" => "Апр",
            "May" => "Мая",
            "May" => "Мая",
            "June" => "Июня",
            "Jun" => "Июн",
            "July" => "Июля",
            "Jul" => "Июл",
            "August" => "Августа",
            "Aug" => "Авг",
            "September" => "Сентября",
            "Sep" => "Сен",
            "October" => "Октября",
            "Oct" => "Окт",
            "November" => "Ноября",
            "Nov" => "Ноя",
            "December" => "Декабря",
            "Dec" => "Дек",
            "st" => "ое",
            "nd" => "ое",
            "rd" => "е",
            "th" => "ое",
        );
        if (func_num_args() > 1) {
            $timestamp = func_get_arg(1);
            return strtr(date(func_get_arg(0), $timestamp), $translation);
        } else {
            return strtr(date(func_get_arg(0)), $translation);
        };
    }

	/*
	Decimal <--> Roman 1.1
	Written by: Rasmus Rimestad
	Email: rasmusr@online.no
	Homepage: http://dikt.cjb.net
	Written: XXIV.VII.MMI

	This file includes two functions

	string dec2roman (integer $number)
	where $number is a number you want to convert to a roman one.

	integer roman2dec (string $linje)
	where $linje is the roman number you want to conver to an integer.
	*/

	function dec2roman($number) {
		$linje = null;
		$oldChunk = null;
		$letterNumber = null;
		$firstNumber = null;
		# Making input compatible with script.
		$number = floor($number);
		if ($number < 0) {
			$linje = "-";
			$number = abs($number);
		}

		# Defining arrays
		$romanNumbers = array(1000, 500, 100, 50, 10, 5, 1);
		$romanLettersToNumbers = array("M" => 1000, "D" => 500, "C" => 100, "L" => 50, "X" => 10, "V" => 5, "I" => 1);
		$romanLetters = array_keys($romanLettersToNumbers);

		# Looping through and adding letters.
		while ($number) {
			for ($pos = 0; $pos <= 6; $pos++) {

				# Dividing the remaining number with one of the roman numbers.
				$dividend = $number / $romanNumbers[$pos];

				# If that division is >= 1, round down, and add that number of letters to the string.
				if ($dividend >= 1) {
					$linje .= str_repeat($romanLetters[$pos], floor($dividend));

					# Reduce the number to reflect what is left to make roman of.
					$number -= floor($dividend) * $romanNumbers[$pos];
				}
			}
		}

		# If I find 4 instances of the same letter, this should be done in a different way.
		# Then, subtract instead of adding (smaller number in front of larger).
		$numberOfChanges = 1;
		while ($numberOfChanges) {
			$numberOfChanges = 0;

			for ($start = 0; $start < strlen($linje); $start++) {
				$chunk = substr($linje, $start, 1);
				if ($chunk == $oldChunk && $chunk != "M") {
					$appearance++;
				} else {
					$oldChunk = $chunk;
					$appearance = 1;
				}

				# Was there found 4 instances.
				if ($appearance == 4) {
					$firstLetter = substr($linje, $start - 4, 1);
					$letter = $chunk;
					$sum = $firstNumber + $letterNumber * 4;

					$pos = array_search($letter, $romanLetters);

					# Are the four digits to be calculated together with the one before? (Example yes: VIIII = IX Example no: MIIII = MIV
					# This is found by checking if the digit before the first of the four instances is the one which is before the digits in the order
					# of the roman number. I.e. MDCLXVI.

					if ($romanLetters[$pos - 1] == $firstLetter) {
						$oldString = $firstLetter . str_repeat($letter, 4);
						$newString = $letter . $romanLetters[$pos - 2];
					} else {
						$oldString = str_repeat($letter, 4);
						$newString = $letter . $romanLetters[$pos - 1];
					}
					$numberOfChanges++;
					$linje = str_replace($oldString, $newString, $linje);
				}
			}
		}
		return $linje;
	}

	function roman2dec($linje) {
		# Fixing variable so it follows my convention
		$linje = strtoupper($linje);

		# Removing all not-roman letters
		$linje = ereg_replace("[^IVXLCDM]", "", $linje);

		print("\$linje    = $linje<br>");

		# Defining variables
		$romanLettersToNumbers = array("M" => 1000, "D" => 500, "C" => 100, "L" => 50, "X" => 10, "V" => 5, "I" => 1);

		$oldChunk = 1001;

		# Looping through line
		for ($start = 0; $start < strlen($linje); $start++) {
			$chunk = substr($linje, $start, 1);

			$chunk = $romanLettersToNumbers[$chunk];

			if ($chunk <= $oldChunk) {
				$calculation .= " + $chunk";
			} else {
				$calculation .= " + " . ($chunk - (2 * $oldChunk));
			}
			$oldChunk = $chunk;
		}

		# Summing it up
		eval("\$calculation = $calculation;");
		return $calculation;
	}
	// валидация даты
	function validateDate($date, $format = 'Y-m-d H:i:s')
	{
		$d = DateTime::createFromFormat($format, $date);
		return $d && $d->format($format) == $date;
	}
	// проверка строки на наличие русских символов
	function check_str($s) {
		if (preg_match("/^[а-яёa-z]+$/iu",$s)) {
			return true;
		} else {
			return false;
		}
	}
	// проверка на положительное число
	function check_num($a) {
		if(($a>0)and(is_numeric($a))) {
			return true;
		} else {
			return false;
		}
	}
	 // Преобразование транслитом + пробелы в знаки нижнего подчеркивания + одинарная кавычка заменена на &apos;
	function rus2translit($string) {
		$converter = array(
			'а' => 'a',   'б' => 'b',   'в' => 'v',
			'г' => 'g',   'д' => 'd',   'е' => 'e',
			'ё' => 'e',   'ж' => 'zh',  'з' => 'z',
			'и' => 'i',   'й' => 'y',   'к' => 'k',
			'л' => 'l',   'м' => 'm',   'н' => 'n',
			'о' => 'o',   'п' => 'p',   'р' => 'r',
			'с' => 's',   'т' => 't',   'у' => 'u',
			'ф' => 'f',   'х' => 'h',   'ц' => 'c',
			'ч' => 'ch',  'ш' => 'sh',  'щ' => 'sch',
			'ь' => "",  'ы' => 'y',   'ъ' => "",
			'э' => 'e',   'ю' => 'yu',  'я' => 'ya',

			'А' => 'A',   'Б' => 'B',   'В' => 'V',
			'Г' => 'G',   'Д' => 'D',   'Е' => 'E',
			'Ё' => 'E',   'Ж' => 'Zh',  'З' => 'Z',
			'И' => 'I',   'Й' => 'Y',   'К' => 'K',
			'Л' => 'L',   'М' => 'M',   'Н' => 'N',
			'О' => 'O',   'П' => 'P',   'Р' => 'R',
			'С' => 'S',   'Т' => 'T',   'У' => 'U',
			'Ф' => 'F',   'Х' => 'H',   'Ц' => 'C',
			'Ч' => 'Ch',  'Ш' => 'Sh',  'Щ' => 'Sch',
			'Ь' => "",  'Ы' => 'Y',   'Ъ' => "",
			'Э' => 'E',   'Ю' => 'Yu',  'Я' => 'Ya',
			' ' => '-',
			);
		return strtr($string, $converter);
	}
	// Проверяем, сыграна ли хоть одна из последующих партий.
	function check_route($t,$n) {
		$result = true;
		
		$this->sql_query[1] = "SELECT * FROM tournaments WHERE id='$t'";
		$this->sql_execute(1);
		$row = mysql_fetch_array($this->sql_res[1]);
		$protocol = $row['protocol'];
		
		switch($protocol) {
			case "krug":
				$result = true;
				break;
			case "vib8":
				// Генерируем массив
				$games = Array(
					"1" => Array(5,7,9,11,12,13,14),
					"2" => Array(5,7,9,11,12,13,14),
					"3" => Array(6,8,10,11,12,13,14),
					"4" => Array(6,8,10,11,12,13,14),
					"5" => Array(9,13,14),
					"6" => Array(10,13,14),
					"7" => Array(9,11,12,13),
					"8" => Array(10,11,12,13),
					"9" => Array(12,13),
					"10" => Array(12,13),
					"11" => Array(),
					"12" => Array(),
					"13" => Array(),
					"14" => Array()
				);
				if(count($games[$n])>0) {
					// Генерируем запрос
					$query = "SELECT * FROM matches WHERE tournament='$t' AND (";
					for($i=0;$i<count($games[$n]);$i++) {
						$game = $games[$n][$i];
						$query.="number='$game'";
						if($i!=(count($games[$n])-1))
							$query.=" OR ";
					}
					$query.=")";
					
					$this->sql_query[1] = $query;
					$this->sql_execute(1);
					while($row = mysql_fetch_array($this->sql_res[1])) {
						$status = $row['status'];
						if($status > 1) $result = false;
					}
				}
				break;
			case "vib16":
				// Генерируем массив
				$games = Array(
					"1" =>	Array(9,13,17,19,23,25,26,27,29,30,31,32,21),
					"2" =>	Array(9,13,17,19,23,25,26,27,29,30,31,32,21),
					"3" =>	Array(10,14,17,19,21,23,25,26,27,29,30,31,32),
					"4" =>	Array(10,14,17,19,21,23,25,26,27,29,30,31,32),
					"5" =>	Array(11,15,18,20,22,24,25,26,27,28,29,30,31,32),
					"6" =>	Array(11,15,18,20,22,24,25,26,27,28,29,30,31,32),
					"7" =>	Array(12,16,18,20,22,24,25,26,27,28,29,30,31,32),
					"8" =>	Array(12,16,18,20,22,24,25,26,27,28,29,30,31,32),
					"9" =>	Array(17,19,29,30,31,32),
					"10" =>	Array(17,19,29,30,31,32),
					"11" =>	Array(18,20,29,30,31,32),
					"12" =>	Array(18,20,29,30,31,32),
					"13" =>	Array(21,23,25,26,27),
					"14" =>	Array(21,23,25,26,27),
					"15" =>	Array(22,24,25,26,27,28),
					"16" =>	Array(22,24,25,26,27,28),
					"17" =>	Array(31,32),
					"18" =>	Array(31,32),
					"19" =>	Array(29,30),
					"20" =>	Array(29,30),
					"21" =>	Array(27,28),
					"22" =>	Array(27,28),
					"23" =>	Array(25,26),
					"24" =>	Array(25,26),
					"25" =>	Array(),
					"26" =>	Array(),
					"27" =>	Array(),
					"28" =>	Array(),
					"29" =>	Array(),
					"30" =>	Array(),
					"31" =>	Array(),
					"32" =>	Array()
				);
				if(count($games[$n])>0) {
					// Генерируем запрос
					$query = "SELECT * FROM matches WHERE tournament='$t' AND (";
					for($i=0;$i<count($games[$n]);$i++) {
						$game = $games[$n][$i];
						$query.="number='$game'";
						if($i!=(count($games[$n])-1))
							$query.=" OR ";
					}
					$query.=")";
					
					$this->sql_query[1] = $query;
					$this->sql_execute(1);
					while($row = mysql_fetch_array($this->sql_res[1])) {
						$status = $row['status'];
						if($status > 1) $result = false;
					}
				}
				break;
		}
		return $result;
	}
}
?>
