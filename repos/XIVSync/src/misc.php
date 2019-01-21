<?php
//
// Easily show object data
//
function output($text, $arr = [])
{
	if (!OUTPUT) {
        return;
    }

	$ms = explode('.', microtime(true))[1];
	$ms = str_pad($ms, 4, '0', STR_PAD_RIGHT);

	if (is_array($text)) {
		return print_r($text);
	}

	// get memory
	$memory = str_pad(memory(), 7, ' ', STR_PAD_LEFT);
	$cpu = str_pad(cpu(), 7, ' ', STR_PAD_LEFT);

	$text = vsprintf($text, $arr);
	$string = sprintf("[ %s %s | %s ]  %s\n", $memory, $cpu, date('Y-m-d | H:i:s | ') . $ms, $text);

	print_r($string);

	//global $start;
	//file_put_contents(ROOT . '/log_'. $start .'.txt', $string, FILE_APPEND);
}

function show($data)
{
    if (!DEV) {
        return;
    }

	echo '<pre>'. print_r($data, true) .'</pre>';
}

//
// Timestamp for mysql
//
function timestamp()
{
	return date('Y-m-d H:i:s');
}

//
// Get memory usage
//
function memory()
{
	$size = memory_get_usage(true);
	$unit=array('b','kb','mb','gb','tb','pb');
	return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

//
// Get cpu load average
//
function cpu()
{
	return sys_getloadavg()[0];
}

//
// Print time
//
$startTime = false;
$lastTime = microtime(true);
function printtime($msg)
{
    return;
    global $lastTime, $startTime;
    if (!$startTime) {
        $startTime = microtime(true);
    }

    $finish = microtime(true);
    $difference = $finish - $lastTime;
    $difference = str_pad(round($difference < 0.0001 ? 0 : $difference, 6), 10, '0');
    $lastTime = $finish;
    $duration = $finish - $startTime;
    $duration = str_pad(round($duration < 0.0001 ? 0 : $duration, 6), 10, '0');
    show(sprintf("%s \t---\t Time Overall: %s \t---\t Diff from last: %s", $msg, $duration, $difference));
}