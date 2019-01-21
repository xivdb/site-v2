<?php
/**
 * Functions Trait
 *
 * @version 1.0
 * @author 
 */

namespace XIVDB\Apps\Site;

trait FunctionsTrait
{
    /**
     * Random string, based on length
     *
     * @param $length (64 default)
     * @param $simplified (if true, doesn't include symbols)
     * @return String - strength of length $length
     */
    public function generateRandomString($length = 64, $simplified = false)
    {
        $characters = $simplified
            ? '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ'
            : '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ!@$%^&*()_+-={}[],.<>;:';

        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }

        return $randomString;
    }

    //
    // Get the date
    //
    protected function getDate($format = 'Y-m-d H:i:s', $time = false)
    {
        $time = $time ? $time : time();
        return date($format, $time);
    }

    /**
     * Convert memory to a human value
     *
     * @param $size - the size in bytes
     * @return String - the actual size, eg: 32 mb
     */
    public function convertSize($size)
    {
        $unit=array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }

    /**
     * Removes numeric indexes from array (usually for SQLite data)
     *
     * @param $data - the array of data to remove numeric indexes from
     * @return Array - the new array!
     */
    public function removeNumericIndexes($data)
    {
        foreach($data as $k => $v)
            if (is_numeric($k))
                unset($data[$k]);

        return $data;
    }

    /**
     * Formats an icon folder path from its number
     *
     * @param $number - the icon number
     * @param $ishq - if the icon is a hq icon
     * @param $banner - if its a banner (ups a folder number)
     * @return String - the icon path
     */
    public function iconize($number, $ishq = false)
    {
        $number = intval($number);

        if ($ishq) {
            $number = explode('/', $number);
            if (!isset($number[4])) {
                return false;
            }

            $number[4] = 'hq/'. $number[4];
            $number = implode('/', $number);

            if (!file_exists(ROOT_WEB . $number)) {
                return false;
            }

            return $number;
        }

        $path = [];

        if (strlen($number) >= 6)
        {
            $icon = str_pad($number, 5, "0", STR_PAD_LEFT);
            $path[] = $icon[0] . $icon[1] . $icon[2] .'000';
        }
        else
        {
            $icon = '0' . str_pad($number, 5, "0", STR_PAD_LEFT);
            $path[] = '0'. $icon[1] . $icon[2] .'000';
        }

        $path[] = $icon;
        $icon = implode('/', $path);
        $icon = str_ireplace('{icon}', $icon, ICON);

        // Return
        return $icon;
    }

    /**
     * Parse link in a big block of text and will also
     * cut down long links to the maximum length. It is a bit
     * hacky but does ths job
     *
     * @param $string - the string to parse
     * @return $string - the string with links in them (trimmed)
     */
    public function parseLinks($string, $addBlankTarget = true)
    {
        $string = html_entity_decode($string);
        $string = explode("\n", $string);

        foreach($string as $i => $line)
        {
            $line = explode(" ", trim($line));
            foreach($line as $j => $word)
            {
                if (stripos($word, 'http://') !== false || stripos($word, 'https://') !== false) {
                    $text = substr($word, 0, 100);
                    if (strlen($text) == 100) {
                        $text = $text . '...';
                    }

                    $word = sprintf('<a href="%s" %s class="detected-link">%s</a> ',
                        $word,
                        $addBlankTarget ? 'target="_blank"' : '',
                        $text
                    );
                }

                $line[$j] = $word;
            }

            $string[$i] = trim(implode("&nbsp;", $line));
        }

        $string = (implode("\n", $string));
        return $string;
    }

    /**
     * Get all files from a directory (plus sub directories)
     *
     * @param $path - the path to search
     * @return Array - array of files + sub directories
     */
    public function getFiles($path, $list = [], $recurrsive = 1)
    {
        $directory = scandir($path);

        // loop through files
        foreach ($directory as $file)
        {
            // ignore dots
            if ($file != '.' && $file != '..')
            {
                $folder = $path . $file .'/';

                // if its a directory, recurrsive call
                if (is_dir($folder) && $recurrsive)
                {
                    $list = $this->getfiles($folder, $list);
                }
                else
                {
                    $list[] = $path . $file;
                }
            }
        }

        // return list
        return $list;
    }

    /**
     * Rounds down a value to the nearest decimal place
     *
     * @param $value - the value to round down
     * @param $precision - the number of decimal places
     * @return double - a number back
     */
    public function roundDown($value, $precision)
    {
        $mult = pow(10, $precision);
        return floor($value * $mult) / $mult;
    }

    /**
     * Converts a csv line to an array, also trims all values
     *
     * @param $csv - the csv line
     * @param $full - if true, it does a full file not a single line
     * @return Array OR false - false if index [1] is empty
     */
    public function csvToArray($csv, $full = false)
    {
        if ($full)
        {
            $realcsv = [];
            $csv = explode("\n", $csv);
            foreach($csv as $i => $line)
            {
                $line = $this->csvToArray($line);
                if ($line)
                {
                    $id = $line[0];
                    $realcsv[$id] = $line;
                }
            }

            return $realcsv;
        }

        // parse csv
        $csv = str_getcsv($csv);

        // if first entry is empty, ignore and continue
        if (!isset($csv[1])) {
            return false;
        }

        // trim stuff
        foreach($csv as $i => $line)
        {
            $csv[$i] = trim($csv[$i]);
        }

        // return
        return $csv;
    }

    //
    // Get CSV data from a file
    //
    public function getCsv($filename)
    {
        if (file_exists($filename) && $csvdata = file_get_contents($filename))
        {
            $csvdata = $this->csvToArray($csvdata, true);

            // remove non int indexes
            foreach($csvdata as $index => $block) {
                if (!is_int($index)) {
                    unset($csvdata[$index]);
                }
            }

            return $csvdata;
        }

        return false;
    }

    //
    // Get JSON data from a file
    //
    public function getJson($filename)
    {
        // get data
        if (file_exists($filename) && $json = file_get_contents($filename)) {
            return json_decode($json, true);
        }

        return false;
    }

    /**
     * Insert a value to a specific array index
     *
     * @param $array - array to insert ini_restore
     * @param $index - index to insert into
     * @param $value - the value to insert
     * @return Array - array
     */
    public function insertAt($array, $index, $value)
    {
        array_splice($array, ($index-1), 0, $value);
        return $array;
    }

    /**
     * Fix enemy id, the enemy id from libra comes out
     * weird, this will fix it
     *
     * @param $id - the id from libra
     * @return Integer - the id fixed
     */
    public function fixEnemyId($id)
    {
        $lastZero = strpos($id, '0000');
        $id = intval(substr($id, $lastZero));
        return $id;
    }

    /**
     * Restructures an array to a particular key
     */
    public function restructure($data, $field)
    {
        $temp = [];
        foreach($data as $d) {
            $temp[$d[$field]] = $d;
        }

        ksort($temp);
        return $temp;
    }

    /**
     * Sort an array by a subkey value
     *
     * @param $array
     * @param $subkey
     * @param $sort - ascending or not
     */
    public function sksort(&$array, $subkey, $sort_ascending = false)
    {
        if ($array)
        {
            if (count($array)) {
                $temp_array[key($array)] = array_shift($array);
            }

            foreach($array as $key => $val) {
                $offset = 0;
                $found = false;
                foreach($temp_array as $tmp_key => $tmp_val)
                {
                    if(!$found and strtolower($val[$subkey]) > strtolower($tmp_val[$subkey]))
                    {
                        $temp_array = array_merge((array)array_slice($temp_array,0,$offset),
                            array($key => $val),
                            array_slice($temp_array,$offset)
                        );
                        $found = true;
                    }
                    $offset++;
                }

                if(!$found) {
                    $temp_array = array_merge($temp_array, array($key => $val));
                }
            }

            if ($sort_ascending)
                $array = array_reverse($temp_array);
            else
                $array = $temp_array;

            $array = array_values($array);
        }
    }

    // recurrsively sort an array
    public function ksortRecursive(&$array, $sort_flags = SORT_REGULAR) {
        if (!is_array($array)) return false;
        ksort($array, $sort_flags);
        foreach ($array as &$arr) {
            $this->ksortRecursive($arr, $sort_flags);
        }
        return true;
    }

    /**
     * Get a random color
     *
     * @param $min - decimal value
     * @param $max - decimal value
     * @return Hex - 6 digit hex
     */
    public function getRandomColor($min, $max)
    {
        // Make sure the parameters will result in valid colours
        $min = $min < 0 || $min > 255 ? 0 : $min;
        $max = $max < 0 || $max > 255 ? 255 : $max;

        // Generate 3 values
        $r = mt_rand($min, $max);
        $g = mt_rand($min, $max);
        $b = mt_rand($min, $max);

        // Return a hex colour ID string
        return sprintf('#%02X%02X%02X', $r, $g, $b);
    }

    /**
     * Quick function to access ParseDown
     * and render some MarkDown language.
     *
     * @param $text - text to parse
     * @param String - text parsed into html
     */
    public function markdown($text)
    {
        $ParseDown = new \Parsedown();

        $text = $ParseDown
                ->setMarkupEscaped(true)
                ->setUrlsLinked(true)
                ->setBreaksEnabled(true)
                ->text($text);

        $text = preg_replace("/<a(.*?)>/", "<a$1 target=\"_blank\">", $text);

        return $text;
    }

    /**
     * Generate a random guid v4 value
     *
     * http://stackoverflow.com/questions/2040240/php-function-to-generate-v4-uuid
     */
    public function guidv4()
    {
        $data = openssl_random_pseudo_bytes(16);

        assert(strlen($data) == 16);

        $data[6] = chr(ord($data[6]) & 0x0f | 0x40); // set version to 0100
        $data[8] = chr(ord($data[8]) & 0x3f | 0x80); // set bits 6-7 to 10

        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }

    /**
     * Render a twig layout
     */
    public function twigit($filename, $data)
    {
        $loader = new \Twig_Loader_Filesystem(ROOT_VIEWS);
        $twig = new \Twig_Environment($loader);
        $template = $twig->loadTemplate($filename);

        return $template->render($data);
    }

    //
    // Parse a PHP doc
    //
    public function parsePhpDoc($string)
    {
        if (preg_match_all('/@(\w+)\s+(.*)\r?\n/m', $string, $matches)){
            return array_combine($matches[1], $matches[2]);
        }

        return false;
    }

    /**
     * https://pngquant.org/php.html
     *
     * Optimizes PNG file with pngquant 1.8 or later (reduces file size of 24-bit/32-bit PNG images).
     *
     * You need to install pngquant 1.8 on the server (ancient version 1.0 won't work).
     * There's package for Debian/Ubuntu and RPM for other distributions on http://pngquant.org
     *
     * @param $path_to_png_file string - path to any PNG file, e.g. $_FILE['file']['tmp_name']
     * @param $max_quality int - conversion quality, useful values from 1 to 100 (smaller number = smaller file)
     * @return string - content of PNG file after conversion
     */
    public function compresspng($path_to_png_file, $max_quality = 100)
    {
        $size = filesize($path_to_png_file);

        if (!file_exists($path_to_png_file)) {
            die("File does not exist: $path_to_png_file");
        }

        // guarantee that quality won't be worse than that.
        $min_quality = 1;

        // '-' makes it use stdout, required to save to $compressed_png_content variable
        // '<' makes it read from the given file path
        // escapeshellarg() makes this safe to use with any path
        $compressed_png_content = shell_exec("pngquant --quality=$min_quality-$max_quality - < ".escapeshellarg(    $path_to_png_file));

        if (!$compressed_png_content) {
            die("Conversion to compressed PNG failed. Is pngquant 1.8+ installed on the server?");
        }

        // save compressed image
        file_put_contents($path_to_png_file, $compressed_png_content);
        return intval($size - strlen($compressed_png_content));
    }

    //
    // Is round or not?
    //
    public function isRound($value) {
        return is_numeric($value) && intval($value) == $value;
    }

    //
    // Get the class for a keyword
    //
    public function getContentClass($type)
    {
        $class = false;

        // Get class based on type
        switch($type)
        {
            case 'item': $class = new \XIVDB\Apps\Content\Item(); break;
            case 'achievement': $class = new \XIVDB\Apps\Content\Achievement(); break;
            case 'action': $class = new \XIVDB\Apps\Content\Action(); break;
            case 'gathering': $class = new \XIVDB\Apps\Content\Gathering(); break;
            case 'instance': $class = new \XIVDB\Apps\Content\Instance(); break;
            case 'npc': $class = new \XIVDB\Apps\Content\NPC(); break;
            case 'enemy': $class = new \XIVDB\Apps\Content\Enemy(); break;
            case 'emote': $class = new \XIVDB\Apps\Content\Emote(); break;
            case 'placename': $class = new \XIVDB\Apps\Content\Placename(); break;
            case 'status': $class = new \XIVDB\Apps\Content\Status(); break;
            case 'title': $class = new \XIVDB\Apps\Content\Title(); break;
            case 'recipe': $class = new \XIVDB\Apps\Content\Recipe(); break;
            case 'quest': $class = new \XIVDB\Apps\Content\Quest(); break;
            case 'shop': $class = new \XIVDB\Apps\Content\Shop(); break;
            case 'minion': $class = new \XIVDB\Apps\Content\Minion(); break;
            case 'mount': $class = new \XIVDB\Apps\Content\Mount(); break;
            case 'weather': $class = new \XIVDB\Apps\Content\Weather(); break;
            case 'fate': $class = new \XIVDB\Apps\Content\Fate(); break;
            case 'leve': $class = new \XIVDB\Apps\Content\Leve(); break;
            case 'special-shop': $class = new \XIVDB\Apps\Content\SpecialShop(); break;
        }

        // if no class
        if (!$class) die('Critical content error (2)');

        return $class;
    }

    //
    // Generate a url based on a type, id and (optional) name
    //
    public function url($type, $id, $name = null)
    {
        $array = [$type, $id];

        if ($name) {
            $name = str_ireplace(' ', '+', $name);
            $array = [$type, $id, $name];
        }

        return strtolower('/'. implode('/', $array));
    }

    //
    // Empty an entire folder
    //
    public function deleteDirectory($directory)
    {
        $it = new \RecursiveDirectoryIterator($directory, \RecursiveDirectoryIterator::SKIP_DOTS);
        $files = new \RecursiveIteratorIterator($it, \RecursiveIteratorIterator::CHILD_FIRST);

        foreach($files as $file) {
            if ($file->isDir()){
                rmdir($file->getRealPath());
            } else {
                unlink($file->getRealPath());
            }
        }

        rmdir($directory);
    }

    //
    // Copy an entire folder
    //
    public function copyDirectory($src, $dst)
    {
        $dir = opendir($src);
        @mkdir($dst);
        while(false !== ( $file = readdir($dir)) ) {
            if (( $file != '.' ) && ( $file != '..' )) {
                if ( is_dir($src . '/' . $file) ) {
                    $this->copyDirectory($src . '/' . $file,$dst . '/' . $file);
                }
                else
                {
                    // only copy if it does not exist
                    if (!file_exists($dst . '/' . $file)) {
                        copy($src . '/' . $file,$dst . '/' . $file);
                    }
                }
            }
        }
        closedir($dir);
    }

    //
    // Fix colors on things
    //
    public function colors($text)
    {
        $replace =
        [
            '/stronggreen' => '/span',
            '/strongred' => '/span',
            '/strongyellow' => '/span',

            'stronggreen' => 'span style="font-weight:bold;color:#4CAF50;"',
            'strongred' => 'span style="font-weight:bold;color:#F44336;"',
            'strongyellow' => 'span style="font-weight:bold;color:#FFEB3B;"',
        ];

        $text = str_ireplace(array_keys($replace), $replace, $text);

        return $text;
    }

    //
    // Get ip of client
    //
    public function getClientIp()
    {
        return $_SERVER['REMOTE_ADDR'];
    }

    /**
     * Generate an array of string dates between 2 dates
     * http://stackoverflow.com/questions/4312439/php-return-all-dates-between-two-dates-in-an-array
     *
     * @param string $start Start date
     * @param string $end End date
     * @param string $format Output format (Default: Y-m-d)
     *
     * @return array
     */
    function getDatesFromRange($start, $end, $interval = 'P1D', $format = 'Y-m-d') {
        $array = array();
        $interval = new \DateInterval($interval);

        $realEnd = new \DateTime($end);
        $realEnd->add($interval);

        $period = new \DatePeriod(new \DateTime($start), $interval, $realEnd);
        $temp = [];

        foreach($period as $date) {
            $a = $date->format($format);

            if (!isset($temp[$a])) {
                $temp[$a] = true;
                $array[] = $a;
            }
        }

        return $array;
    }

    //
	// levels X/Y to in-game X/Y formula by Clorifex
	// https://github.com/viion/XIV-Datamining/blob/master/research/map_coordinates.md
	//
	public function convertIngamePositionCoordinate($val, $scale, $offset)
    {
        $size = $scale / 100;
        $val = ($val + $offset) * $size;
        return ((41.5 / $size) * (($val + 1024.0) / 2048.0)) + 1;
    }
}
