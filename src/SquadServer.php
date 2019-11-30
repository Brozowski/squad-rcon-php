<?php

namespace SquadSlovenia;

class SquadServer
{

    const SQUAD_SOCKET_TIMEOUT_SECONDS = 0.5;

    private $rcon;

    private static function propSet($object, $prop, $value)
    {
        $objClass = new \ReflectionClass($object);
        $property = $objClass->getProperty($prop);
        $property->setAccessible(true);
        $property->setValue($object, $value);
    }

    private static function propAdd($object, $prop, $value)
    {
        $objClass = new \ReflectionClass($object);
        $property = $objClass->getProperty($prop);
        $property->setAccessible(true);
        $currentArray = $property->getValue($object);
        if (!is_array($currentArray)) {
            $currentArray = array();
        }
        $currentArray[] = $value;
        $property->setValue($object, $currentArray);
    }

    public function __construct($host, $port, $password, $timeout = SquadServer::SQUAD_SOCKET_TIMEOUT_SECONDS)
    {
        $this->rcon = new RCon($host, $port, $password, $timeout);
    }

    public function serverPopulation()
    {
        /** @var Team[] $teams */
        $teams = array();
        /** @var Squad[] $squads */
        $squads = array();
        /** @var Player[] $players */
        $players = array();

        $resSquads = $this->rcon->execute("ListSquads");
        $linesSquads = explode("\n", $resSquads);
        foreach ($linesSquads as $lineSquad) {
            $matches = array();
            if (preg_match('/Team ID: ([1|2]) \(([.*])\)/', $lineSquad, $matches)) {
                $id = intval($matches[1]);
                $name = $matches[2];

                $team = new Team();
                static::propSet($team, 'id', $id);
                static::propSet($team, 'name', $name);
                $teams[$id] = $team;
            } else if (preg_match('/ID: ([0-9]*) | Name: (.*) | Size: ([0-9]) | Locked: (True|False)/', $lineSquad, $matches)) {
                $id = intval($matches[1]);
                $name = $matches[2];
                $size = intval($matches[3]);
                $locked = $matches[4] === "True";

                $squad = new Squad();
                static::propSet($squad, 'id', $id);
                static::propSet($squad, 'name', $name);
                static::propSet($squad, 'size', $size);
                static::propSet($squad, 'locked', $locked);
                $squads[$id] = $squad;
            }
        }

        $resPlayers = $this->rcon->execute("ListPlayers");
        $linesPlayers = explode("\n", $resPlayers);
        foreach ($linesPlayers as $linePlayer) {
            $matches = array();
            if (preg_match('/ID: ([0-9]*) | SteamID: ([0-9]{17}) | Name: (.*) | Team ID: (1|2|N/A) | Squad ID: ([0-9]*)/', $linePlayer, $matches)) {
                $id = intval($matches[1]);
                $steamId = $matches[2];
                $name = $matches[3];
                $teamId = $matches[4];
                $squadId = $matches[5];
                $team = array_key_exists($teamId, $teams) ? $teams[$teamId] : null;
                $squad = array_key_exists($squadId, $squads) ? $squads[$squadId] : null;

                $player = new Player();
                static::propSet($player, 'id', $id);
                static::propSet($player, 'steamId', $steamId);
                static::propSet($player, 'name', $name);
                static::propSet($player, 'team', $team);
                static::propSet($player, 'squad', $squad);
                $players[$id] = $player;
            } else if (preg_match('----- Recently Disconnected Players', $linePlayer)) {
                break;
            }
        }

        foreach ($players as $player) {
            if ($player->getSquad() === null) {
                static::propAdd($player->getTeam(), 'players', $player);
                continue;
            }

            $squad = $player->getSquad();
            if ($squad->getTeam() === null) {
                static::propSet($squad, 'team', $player->getTeam());;
            }

            static::propAdd($squad, 'players', $player);
        }

        return $teams;
    }

    /**
     * @param array $ignored
     * @return array
     * @deprecated
     */
    public function currentPlayers($ignored = array())
    {
        $res = $this->_rcon->execute("ListPlayers");
        $ra = explode("\n", $res);
        $players = array();
        for ($i = 1; $i < count($ra); $i++) {
            $l = trim($ra[$i]);
            if (
                $l == '----- Recently Disconnected Players [Max of 15] -----'
            ) {
                break;
            }
            if (
            empty($l)
            ) {
                continue;
            }
            $pla = explode(' | ', $l);
            $pli = substr($pla[0], 4);
            $pls = substr($pla[1], 9);
            $pln = substr($pla[2], 6);
            if (!in_array($pls, $ignored)) {
                $players[] = array(
                    'id' => $pli,
                    'steam_id' => $pls,
                    'name' => $pln,
                );
            }
        }
        return $players;
    }

    public function currentMap()
    {
        return $this->currentMaps()['current'];
    }

    public function currentNext()
    {
        return $this->currentMaps()['next'];
    }

    /**
     * @return array
     * @deprecated
     */
    public function currentMaps()
    {
        $maps = array(
            'current' => null,
            'next' => null
        );
        $res = $this->_sendCommand("ShowNextMap");
        $arr = explode(', Next map is ', $res);
        if (count($arr) > 1) {
            $next = trim($arr[1]);
            $curr = substr($arr[0], strlen('Current map is '));
            $maps['current'] = $curr;
            $maps['next'] = $next;
        }
        return $maps;
    }

    public function broadcastMessage($msg)
    {
        return $this->_consoleCommand('AdminBroadcast', $msg, 'Message broadcasted');
    }

    public function changeMap($map)
    {
        return $this->_consoleCommand('AdminChangeMap', $map, 'Changed map to');
    }

    public function nextMap($map)
    {
        return $this->_consoleCommand('AdminSetNextMap', $map, 'Set next map to');
    }

    private function _consoleCommand($cmd, $param, $rtn)
    {
        $ret = $this->_sendCommand($cmd . ' ' . $param);
        if (substr($ret, 0, strlen($rtn)) == $rtn)
            return true;
        return false;
    }

    private function _sendCommand($cmd)
    {
        $res = $this->rcon->execute($cmd);
        return $res;
    }

}
