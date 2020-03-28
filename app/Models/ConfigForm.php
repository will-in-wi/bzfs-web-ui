<?php

namespace BzfsWebUi\Models;

use \BzfsWebUi\Lib\ConfigParser;

class ConfigForm {
    protected $body;
    protected $goodFlags;
    protected $badFlags;

    public $errors = [];

    public function __construct($body) {
        $this->body = $body;

        $this->goodFlags = ConfigParser::config('good_flags');
        $this->badFlags = ConfigParser::config('bad_flags');
    }

    public function isValid() : bool {
        $this->errors = [];

        $validators = [
            'num_shots' => new Validators\IntInRange(1, 20),
            'game_type' => new Validators\InArray([
                GameType::FREE_FOR_ALL,
                GameType::RABBIT_HUNT,
                GameType::CAPTURE_THE_FLAG,
            ]),
            'world_type' => new Validators\InArray([
                WorldType::RANDOM,
                WorldType::PREBUILT,
            ]),
            'time_limit' => new Validators\IntInRange(0),
            'team_score_win' => new Validators\IntInRange(0),
            'flags' => new Validators\IntInRange(0),
            'bad_flag_drop_time' => new Validators\IntInRange(0, null, true),
            'bad_flag_drop_kills' => new Validators\IntInRange(0, null, true),
            'total_players' => new Validators\IntInRange(0, null, true),
            'rogue_players' => new Validators\IntInRange(0, null, true),
            'red_players' => new Validators\IntInRange(0, null, true),
            'green_players' => new Validators\IntInRange(0, null, true),
            'blue_players' => new Validators\IntInRange(0, null, true),
            'purple_players' => new Validators\IntInRange(0, null, true),
            'observers' => new Validators\IntInRange(0, null, true),
        ];

        switch ($this->getGameType()) {
            case GameType::RABBIT_HUNT:
                $validators['rh_type'] = new Validators\InArray(['score', 'killer', 'random']);
                break;
            case GameType::CAPTURE_THE_FLAG:
                $validators['team_flags'] = new Validators\IntInRange(1);
                break;
        }

        switch ($this->getWorldType()) {
            case WorldType::RANDOM:
                $validators['density'] = new Validators\IntInRange(0, 10);
                $validators['world_size'] = new Validators\IntInRange(200, 2000);
                break;
            case WorldType::PREBUILT:
                $validators['prebuilt_map'] = new Validators\NotEmpty();
                break;
        }

        foreach (array_merge($this->goodFlags, $this->badFlags) as $flag_id => $flag) {
            $validators[$flag_id . '_num_flags'] = new Validators\IntInRange(0, null, true);
            $validators[$flag_id . '_num_shots'] = new Validators\IntInRange(1, null, true);
        }

        // Validate
        foreach ($validators as $key => $validator) {
            if (!$validator->isValid($this->body[$key], $this)) {
                array_push($this->errors, $key);
            }
        }

        return empty($this->errors);
    }

    public function getNumShots() : int {
        return (integer) $this->body['num_shots'];
    }

    public function getPort() : int {
        // This should be automatic. Too much room for error. TODO
        return (integer) $this->body['port'];
    }

    public function isSpawnBox() : bool {
        return (bool) $this->body['spawn_box'];
    }

    public function isJumping() : bool {
        return (bool) $this->body['jumping'];
    }

    public function isRicochet() : bool {
        return (bool) $this->body['ricochet'];
    }

    public function getGameType() : string {
        return $this->body['game_type'];
    }

    public function getRabbitHuntType() : string {
        return $this->body['rh_type'];
    }

    public function getCtfTeamFlags() : int {
        return (integer) $this->body['team_flags'];
    }

    public function isCtfRandom() : bool {
        return (bool) $this->body['ctf_random'];
    }

    public function isCtfRotateBuildings() : bool {
        return (bool) $this->body['ctf_rotate_buildings'];
    }

    public function getWorldType() : string {
        return $this->body['world_type'];
    }

    public function getRandomWorldDensity() : int {
        return (integer) $this->body['density'];
    }

    public function isRandomWorldRandomHeight() : bool {
        return (bool) $this->body['random_height'];
    }

    public function isRandomWorldTeleports() : bool {
        return (bool) $this->body['teleports'];
    }

    public function getRandomWorldSize() : int {
        return (integer) $this->body['world_size'];
    }

    public function getPrebuiltMap() : string {
        return $this->body['prebuilt_map'];
    }

    public function isOneGame() : bool {
        return (bool) $this->body['one_game'];
    }

    public function isCountdown() : bool {
        return (bool) $this->body['countdown'];
    }

    public function getTimeLimit() : int {
        return (integer) $this->body['time_limit'];
    }

    public function getTeamScoreWin() : int {
        return (integer) $this->body['team_score_win'];
    }

    public function isFlagsOnBuildings() : bool {
        return (bool) $this->body['flags_buildings'];
    }

    public function isBadFlags() : bool {
        return (bool) $this->body['bad_flags'];
    }

    public function getTotalFlagCount() : int {
        return (integer) $this->body['flags'];
    }

    public function getFlagCount($flagId) : int {
        return (integer) $this->body[$flagId . '_num_flags'];
    }

    public function getFlagNumShots($flagId) : int {
        return (integer) $this->body[$flagId . '_num_shots'];
    }

    public function getBadFlagDropTime() : int {
        return (integer) $this->body['bad_flag_drop_time'];
    }

    public function getBadFlagDropKills() : int {
        return (integer) $this->body['bad_flag_drop_kills'];
    }

    public function isBadFlagAntidote() : bool {
        return (bool) $this->body['antidote'];
    }

    public function isAutoTeam() : bool {
        return (bool) $this->body['auto_team'];
    }

    public function isDisableBots() : bool {
        return (bool) $this->body['disable_bots'];
    }

    public function isHandicap() : bool {
        return (bool) $this->body['handicap'];
    }

    public function isNoTeamkill() : bool {
        return (bool) $this->body['no_teamkill'];
    }

    public function getTotalPlayerCount() : int {
        return (int) $this->body['total_players'];
    }

    public function getRoguePlayerCount() : int {
        return (int) $this->body['rogue_players'];
    }

    public function getRedPlayerCount() : int {
        return (int) $this->body['red_players'];
    }

    public function getGreenPlayerCount() : int {
        return (int) $this->body['green_players'];
    }

    public function getBluePlayerCount() : int {
        return (int) $this->body['blue_players'];
    }

    public function getPurplePlayerCount() : int {
        return (int) $this->body['purple_players'];
    }

    public function getObserverCount() : int {
        return (int) $this->body['observers'];
    }

    public function getBanIps() : array {
        if (empty($this->body['ban_ip'])) {
            return [];
        }

        return explode("\n", $this->body['ban_ip']);
    }

    public function getLoginMessages() : array {
        if (empty($this->body['login_message'])) {
            return [];
        }

        return explode("\n", $this->body['login_message']);
    }

    public function getExtraOptions() {
        return $this->body['extra_opts'];
    }






    /**
     * The bzfs command
     *
     * @return string
     */
    public function toCommand() : string {
        $cmd = " -a 0 0";
        $cmd .= " -ms " . $this->getNumShots();
        $cmd .= " -p " . $this->getPort();
        if ($this->isSpawnBox()) {
            $cmd .= " -sb";
        }
        if ($this->isJumping()) {
            $cmd .= " -j";
        }
        if ($this->isRicochet()) {
            $cmd .= " +r";
        }

        switch ($this->getGameType()) {
            case GameType::RABBIT_HUNT:
                $cmd .= " -rabbit " . $this->getRabbitHuntType();
                break;
            case GameType::CAPTURE_THE_FLAG:
                if ($this->isCtfRandom()) {
                    $cmd .= " -cr";
                } else {
                    $cmd .= " -c";
                    if ($this->isCtfRotateBuildings()) {
                        $cmd .= " -b";
                    }
                }
                if ($this->getCtfTeamFlags() != 1) {
                    $cmd .= " +f team{" . $this->getCtfTeamFlags() . "}";
                }
               break;
        }

        switch ($this->getWorldType()) {
            case WorldType::RANDOM:
                $cmd .= " -density " . $this->getRandomWorldDensity();
                if ($this->isRandomWorldRandomHeight()) {
                    $cmd .= " -h";
                }
                if ($this->isRandomWorldTeleports()) {
                    $cmd .= " -t";
                }
                $cmd .= " -worldsize " . $this->getRandomWorldSize();
                break;
            case WorldType::PREBUILT:
                $maps = ConfigParser::config('maps');
                // TODO: General settings.
                $cmd .= ' -world ' . $general_settings['map_path'] . '/' . $maps[$this->getPrebuiltMap()]['file'];
                break;
        }

        if ($this->isOneGame()) {
            $cmd .= " -g";
        }

        if ($this->getTimeLimit() != 0) {
            $cmd .= " -time " . $this->getTimeLimit();
            if ($this->isCountdown()) {
                $cmd .= " -timemanual";
            }
        }

        // TODO: Handle team_score_win

        if ($this->isFlagsOnBuildings()) {
            $cmd .= " -fb";
        }

        $cmd .= " +s " . $this->getTotalFlagCount();

        if (!$this->isBadFlags()) {
            $cmd .= " -f bad";
        }

        if ($this->getBadFlagDropKills() !== 0) {
            $cmd .= " -sw " . $this->getBadFlagDropKills();
        }

        if ($this->getBadFlagDropTime() !== 0) {
            $cmd .= " -st " . $this->getBadFlagDropTime();
        }

        if ($this->isBadFlagAntidote()) {
            $cmd .= " -sa";
        }

        foreach (array_merge($this->goodFlags, $this->badFlags) as $flagId => $flag) {
            if ($this->getFlagCount($flagId) !== 0) {
                $cmd .= " +f " . $flagId . "{" . $this->getFlagCount($flagId) . "}";
            }

            if ($this->getFlagNumShots($flagId) !== 0) {
                $cmd .= " -sl " . $flagId . " " . $this->getFlagNumShots($flagId);
            }
        }

        if ($this->isAutoTeam()) {
            $cmd .= " -autoTeam";
        }

        if ($this->isDisableBots()) {
            $cmd .= " -disableBots";
        }

        if ($this->isHandicap()) {
            $cmd .= " -handicap";
        }

        if ($this->isNoTeamkill()) {
            $cmd .= " -tk";
        }


        if ($this->getRoguePlayerCount() === 0 &&
            $this->getRedPlayerCount() === 0 &&
            $this->getGreenPlayerCount() === 0 &&
            $this->getBluePlayerCount() === 0 &&
            $this->getPurplePlayerCount() === 0 &&
            $this->getObserverCount() === 0 &&
            $this->getTotalPlayerCount() !== 0) {
            $cmd .= " -mp " . $this->getTotalPlayersCount();
        } elseif ($this->getRoguePlayerCount() !== 0 ||
                  $this->getRedPlayerCount() !== 0 ||
                  $this->getGreenPlayerCount() !== 0 ||
                  $this->getBluePlayerCount() !== 0 ||
                  $this->getPurplePlayerCount() !== 0 ||
                  $this->getObserverCount() !== 0) {
            $cmd .= " -mp " . implode(',', [
                $this->getRoguePlayerCount(),
                $this->getRedPlayerCount(),
                $this->getGreenPlayerCount(),
                $this->getBluePlayerCount(),
                $this->getPurplePlayerCount(),
                $this->getObserverCount(),
            ]);
        }

        if (!empty($this->getBanIps())) {
            $cmd .= " -ban " . implode(",", $this->getBanIps());
        }

        foreach ($this->getLoginMessages() as $msg) {
            $cmd .= " -srvmsg \"" . $msg . "\"";
        }

        $cmd .= " " . $this->getExtraOptions();

        return $cmd;
    }
}
