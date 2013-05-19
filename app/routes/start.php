<?php

$app->get('/start', function () use ($app) {
    $good_flags = config('good_flags');
    $bad_flags = config('bad_flags');
    $maps = config('maps');

	$data = array(
		'good_flags' => $good_flags,
		'bad_flags' => $bad_flags,
		'maps' => $maps,
	);

    $app->render('start_server.html.twig', $data);
});


/**
 * Generate the command line and execute it.
 */
$app->post('/start', function () use ($app) {
	$general_settings = config('general');

    $cmd = " -a 0 0";

	// /****** General Tab **************/

	// Validate
	$num_shots = $app->request()->post('num_shots');
	if (filter_var($num_shots, FILTER_VALIDATE_INT) === false || $num_shots < 1 || $num_shots > 20) {
		throw new Exception('Invalid number of shots');
	}

	$port = (integer) $app->request()->post('port'); // This should be automatic. Too much room for error. TODO

	$spawn_box = (boolean) $app->request()->post('spawn_box');
	$jumping = (boolean) $app->request()->post('jumping');
	$ricochet = (boolean) $app->request()->post('ricochet');

	// Generate command line
	$cmd .= " -ms " . (integer) $num_shots;

	$cmd .= " -p " . $port;

	if ($spawn_box) {
		$cmd .= " -sb";
	}

	if ($jumping) {
		$cmd .= " -j";
	}

	if ($ricochet) {
		$cmd .= " +r";
	}


	/****** Game Type *****************/

	// Validate
	$game_type = $app->request()->post('game_type');
	switch ($game_type) {
		case "ffa":
			// No options.
			break;
		case "rh":
			$rh_type = $app->request()->post('rh_type');
			if ($rh_type != "score" && $rh_type != "killer" && $rh_type != "random") {
				throw new Exception("Invalid Rabbit Hunt mode");
			}
			break;
		case "ctf":
			$team_flags = (integer) $app->request()->post('team_flags');
			if ($team_flags < 1) {
				throw new Exception("Invalid number of colored flags.");
			}
			$ctf_random = (boolean) $app->request()->post('ctf_random');
			$ctf_rotate_buildings = (boolean) $app->request()->post('ctf_rotate_buildings');
			break;
		default:
			throw new Exception("Invalid game type");
	}

	// Generate command line
	switch ($game_type) {
		case "rh":
			$cmd .= " -rabbit " . $rh_type;
			break;
		case "ctf":
			if ($ctf_random) {
				$cmd .= " -cr";
			} else {
				$cmd .= " -c";
				if ($ctf_rotate_buildings) {
					$cmd .= " -b";
				}
			}
			if ($team_flags != 1) {
				$cmd .= " +f team{" . $team_flags . "}";
			}
		break;
	}


	// /*********** World Tab ********/

	// Validate
	$world_type = $app->request()->post('world_type');
	if ($world_type == 'random') {
		$density = (integer) $app->request()->post('density');
		if ($density > 10 || $density < 0) {
			throw new Exception('Invalid density');
		}
		$random_height = (boolean) $app->request()->post('random_height');
		$teleports = (boolean) $app->request()->post('teleports');

		$world_size = (integer) $app->request()->post('world_size');
		if ($world_size > 2000 || $world_size < 200) {
			throw new Exception('Invalid world size. Must be between 200 and 2000');
		}
	} elseif ($world_type == 'prebuilt') {
		$prebuilt_map = $app->request()->post('prebuilt_map');
		if (!$prebuilt_map || empty($prebuilt_map)) {
			throw new Exception('Prebuilt map invalid');
		}
	} else {
		throw new Exception('Invalid world type.');
	}

	// Generate command line
	switch ($world_type) {
		case "random":
			$cmd .= " -density " . $density;
			if ($random_height) {
				$cmd .= " -h";
			}
			if ($teleports) {
				$cmd .= " -t";
			}
			$cmd .= " -worldsize " . $world_size;
			break;
		case "prebuilt":
			$maps = config('maps');
			$cmd .= " -world maps/" . $maps[$prebuilt_map]['file'];
			break;
	}

	/************ Game Length Tab **********/

	// Validate
	$one_game = (boolean) $app->request()->post('one_game');
	$countdown = (boolean) $app->request()->post('countdown');

	$time_limit = (integer) $app->request()->post('time_limit');
	if ($time_limit < 0) {
		throw new Exception("Invalid time limit");
	}

	$player_score_win = (integer) $app->request()->post('player_score_win');
	if ($player_score_win < 0) {
		throw new Exception("Invalid player win score");
	}

	$team_score_win = (integer) $app->request()->post('team_score_win');
	if ($team_score_win < 0) {
		throw new Exception("Invalid team win score");
	}

	// Generate command line
	if ($one_game) {
		$cmd .= " -g";
	}
	if ($time_limit != 0) {
		$cmd .= " -time " . $time_limit;
		if ($p["countdown"]) {
			$cmd .= " -timemanual";
		}
	}

	/************ Flags Tab ***************/

	// Validate
	$flags_buildings = (boolean) $app->request()->post('flags_buildings');
	$bad_flags = (boolean) $app->request()->post('bad_flags');

	$total_flags = (integer) $app->request()->post('flags');
	if ($total_flags < 0) {
		throw new Exception("Invalid flag count");
	}

	// Validate advanced page
	$good_flags = config('good_flags');
    $bad_flags = config('bad_flags');

	$flags = array_merge($good_flags, $bad_flags);
	foreach ($flags as $flag_id => $flag) {
		$post_num_flag = $app->request()->post($flag_id . '_num_flags');
		if (!empty($post_num_flag)) {
			$post_num_flag = (integer) $post_num_flag;
			if ($post_num_flag < 0) {
				throw new Exception("Invalid number of flags for " . $flag_id);
			}
		}
		$post_num_shots = $app->request()->post($flag_id . '_num_shots');
		if (!empty($post_num_shots)) {
			$post_num_shots = (integer) $post_num_shots;
			if ($post_num_shots < 1) {
				throw new Exception("Invalid number of shots for " . $flag_id);
			}
		}
	}
	$bad_flag_drop_time = $app->request()->post('bad_flag_drop_time');
	if (!empty($bad_flag_drop_time)) {
		$bad_flag_drop_time = (integer) $bad_flag_drop_time;
		if ($bad_flag_drop_time < 0) {
			throw new Exception("Invalid bad flag drop time");
		}
	}

	$bad_flag_drop_kills = $app->request()->post('bad_flag_drop_kills');
	if (!empty($bad_flag_drop_kills)) {
		$bad_flag_drop_kills = (integer) $bad_flag_drop_kills;
		if ($bad_flag_drop_kills < 0) {
			throw new Exception("Invalid bad flag drop kills");
		}
	}

	$antidote = (boolean) $app->request()->post('antidote');


	// Generate command line
	if ($flags_buildings) {
		$cmd .= " -fb";
	}

	$cmd .= " +s " . $total_flags;

	if (!$bad_flags) {
		$cmd .= " -f bad";
	}

	if (!empty($bad_flag_drop_kills)) {
		$cmd .= " -sw " . $bad_flag_drop_kills;
	}

	if (!empty($bad_flag_drop_time)) {
		$cmd .= " -st " . $bad_flag_drop_time;
	}

	if ($antidote) {
		$cmd .= " -sa";
	}

	foreach ($flags as $flag_id => $flag) {
		$post_num_flag = $app->request()->post($flag_id . '_num_flags');
		if (!empty($post_num_flag)) {
			$cmd .= " +f " . $flag_id . "{" . $post_num_flag . "}";
		}

		$post_num_shots = $app->request()->post($flag_id . '_num_shots');
		if (!empty($post_num_shots)) {
			$cmd .= " -sl " . $flag_id . " " . $post_num_shots;
		}
	}

	/************** Teams Tab *****************/

	// Validate
	$auto_team = (boolean) $app->request()->post('auto_team');
	$disable_bots = (boolean) $app->request()->post('disable_bots');
	$handicap = (boolean) $app->request()->post('handicap');
	$no_teamkill = (boolean) $app->request()->post('no_teamkill');

	$total_players = $app->request()->post('total_players');
	if (!empty($total_players)) {
		$total_players = (integer) $total_players;
		if ($total_players < 0) {
			throw new Exception("Invalid player total");
		}
	}

	$rogue_players = $app->request()->post('rogue_players');
	if (!empty($rogue_players)) {
		$rogue_players = (integer) $rogue_players;
		if ($rogue_players < 0) {
			throw new Exception("Invalid rogue player total");
		}
	}

	$red_players = $app->request()->post('red_players');
	if (!empty($red_players)) {
		$red_players = (integer)$red_players;
		if ($red_players < 0) {
			throw new Exception("Invalid red player total");
		}
	}

	$green_players = $app->request()->post('green_players');
	if (!empty($green_players)) {
		$green_players = (integer)$green_players;
		if ($green_players < 0) {
			throw new Exception("Invalid green player total");
		}
	}

	$blue_players = $app->request()->post('blue_players');
	if (!empty($blue_players)) {
		$blue_players = (integer)$blue_players;
		if ($blue_players < 0) {
			throw new Exception("Invalid blue player total");
		}
	}

	$purple_players = $app->request()->post('purple_players');
	if (!empty($purple_players)) {
		$purple_players = (integer)$purple_players;
		if ($purple_players < 0) {
			throw new Exception("Invalid purple player total");
		}
	}

	$observers = $app->request()->post('observers');
	if (!empty($observers)) {
		$observers = (integer)$observers;
		if ($observers < 0) {
			throw new Exception("Invalid observer total");
		}
	}

	// Generate command line
	if ($auto_team) {
		$cmd .= " -autoTeam";
	}
	if ($disable_bots) {
		$cmd .= " -disableBots";
	}
	if ($handicap) {
		$cmd .= " -handicap";
	}
	if ($no_teamkill) {
		$cmd .= " -tk";
	}

	if ($rogue_players == "" && $red_players == "" && $green_players == "" && $blue_players == "" && $purple_players == "" && $observers == "" && $total_players != "") {
		$cmd .= " -mp " . $total_players;
	} elseif ($rogue_players != "" || $red_players != "" || $green_players != "" || $blue_players != "" || $purple_players != "" || $observers != "") {
		$cmd .= " -mp " . $rogue_players . "," . $red_players . "," . $green_players . "," . $blue_players . "," . $purple_players . "," . $observers;
	}

	/************* Advanced Tab **************/

	// Validate

	// TODO: validate ip addresses

	// TODO: check permissions on extra opts

	// Generate command line
	$ban_ip = $app->request()->post('ban_ip');
	if ($ban_ip != "") {
		$banned_addr = explode("\n", $ban_ip);
		$cmd .= " -ban " . implode(",", $banned_addr);
	}

	$login_message = $app->request()->post('login_message');
	if ($login_message != "") {
		$login_msgs = explode("\n", $login_message);
		foreach ($login_msgs as $msg) {
			$cmd .= " -srvmsg \"" . $msg . "\"";
		}
	}

	$cmd .= " " . $app->request()->post('extra_opts');

	/***************** Finish ***************/

	$cmd .= " -printscore";

	$tmp = $general_settings['temp_path'];

	$uid = rand(1000, 9999);
	$pid_file = $tmp . "/bzfs." . $uid;
	$cmd .= " -pidfile " . $pid_file;

	$cmd .= " > " . $tmp . "/bzfs-scores." . $uid . " 2> " . $tmp . '/bzfs-error.' . $uid;

	$cmd = $general_settings['bzflag_path'] . "/bzfs " . $cmd . " &";

	exec($cmd);

	// Give files some time to create.
	sleep(1);

	try {
		$error_output = file_get_contents($tmp . '/bzfs-error.' . $uid);
		$app->flash('launch_error', $error_output);
	} catch (Exception $e) {}

	// Collect pid
	try {
		$pid = (integer) file_get_contents($pid_file);
		$app->flash('pid_success', 'The PID is ' . $pid . '. The server is started!');
	} catch (Exception $e) {
		$app->flash('pid_error', 'PID File Not Found. The server is probably not started.');
		$app->flash('command_line', $cmd);
	}
	
	// Add server to list
	// add_srv($uid, $pid, $cmd);

	$app->redirect(BASE_URL);
	// header('Location: index.php?id=' . $uid);


    // var_dump($cmd);
});