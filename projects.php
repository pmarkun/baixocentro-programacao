<?php
if(isset($_GET['id'])) {
	// retrieve single project
	$cache_file = "data/project_" . $_GET['id'] . ".json";
	if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 60))) {
		$output = file_get_contents($cache_file);
	}
	else {
		$project = json_decode(file_get_contents('http://www.texugo.com.br/bxc2013/mapa!projeto.action?codProjeto=' . $_GET['id']), true);
		$output = $project['values'];
		$output['data'] = fixDate($output['data']);
		$output = json_encode($output);
		file_put_contents($cache_file, $output, LOCK_EX);
	}
} else {
	// retrieve all projects
	$cache_file = "data/projects.json";
	if (file_exists($cache_file) && (filemtime($cache_file) > (time() - 60))) {
		$output = file_get_contents($cache_file);
	}
	else {
		$projects = json_decode(file_get_contents('http://www.texugo.com.br/bxc2013/mapa!pins.action'), true);
		$output = $projects['values'];
		// fix date
		foreach($output as &$item) {
			error_log($item['data']);
			$item['data'] = fixDate($item['data']);
		}
		$output = json_encode($output);
		file_put_contents($cache_file, $output, LOCK_EX);
	}

}

function fixDate($date) {

	// clear years (and buggy years)
	$date = str_replace('/2013', '', $date);
	$date = str_replace('/2012/2013', '', $date);
	$date = str_replace('/2018', '', $date);
	$date = str_replace('/2024', '', $date);
	$date = str_replace('/2012', '', $date);

	error_log($date);

	// add 0 prefix to 1 length day/month
	$separatedVals = explode('/', $date);
	if($separatedVals) {
		if(strlen($separatedVals[0]) === 1)
			$separatedVals[0] = '0' . $separatedVals[0];
		if(strlen($separatedVals[1]) === 1)
			$separatedVals[1] = '0' . $separatedVals[1];
		$date = implode('/', $separatedVals);
	}

	return $date;
}

header('Content-type: application/json');
echo $output;
exit;
?>