<?php

namespace DSG\SquadRCON\Tests\Runners;

use DSG\SquadRCON\Contracts\ServerCommandRunner;
use DSG\SquadRCON\Tests\Runners\Responses\ListPlayersResponse;
use DSG\SquadRCON\Tests\Runners\Responses\ListSquadsResponse;
use DSG\SquadRCON\Tests\Runners\Responses\ShowNextMapResponse;

class TestingCommandRunner implements ServerCommandRunner {
    /**
     * ListSquads command. Returns an array
     * of Teams containing Squads. The output
     * can be given to the listPlayers method
     * to add and reference the Player instances.
     *
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listSquads() : string
    {
        return ListSquadsResponse::get();
    }

    /**
     * ListPlayers command, returns an array
     * of Player instances. The output of
     * ListSquads can be piped into it to
     * assign the Players to their Team/Squad.
     *
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listPlayers() : string
    {
        return ListPlayersResponse::get();
    }

    /**
     * ListDisconnectedPlayers command, returns an array
     * of disconnected Player instances.
     *
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function listDisconnectedPlayers() : string
    {
        return ListPlayersResponse::get();
    }

    /**
     * ShowNextMap command.
     * Gets the current and next map.
     * 
     * @return string
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function showNextMap() : string
    {
        return ShowNextMapResponse::get();
    }

    /**
     * AdminBroadcast command.
     * Broadcasts the given message on the server.
     * 
     * @param string $msg
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminBroadcast(string $msg) : bool
    {
        return true;
    }

    /**
     * AdminRestartMatch command.
     * Broadcasts the given message on the server.
     *
     * @return boolean
     */
    function adminRestartMatch() : bool
    {
        return true;
    }

    /**
     * AdminRestartMatch command.
     * Broadcasts the given message on the server.
     *
     * @return boolean
     */
    function adminEndMatch() : bool
    {
        return true;
    }

    /**
     * AdminSetMaxNumPlayers command.
     * Sets the max amount of players (public).
     *
     * @param int $slots How many public slots ther should be.
     * @return boolean
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    function adminSetMaxNumPlayers(int $slots) : bool
    {
        return true;
    }

    /**
     * AdminSetServerPassword command.
     * Sets the password of the server.
     *
     * @param string $password
     * @return boolean
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    function adminSetServerPassword(string $password) : bool
    {
        return true;
    }

    /**
     * AdminChangeMap command
     * Immediately changes the current map to the given map.
     * @param string $map
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminChangeMap(string $map) : bool
    {
        return true;
    }

    /**
     * AdminSetNextMap command.
     * Temporarily overwrites the next map in the
     * MapRotations, effecively changing the next map.
     * 
     * @param string $map
     * @return bool
     * @throws \DSG\SquadRCON\Exceptions\RConException
     */
    public function adminSetNextMap(string $map) : bool
    {
        return true;
    }
}