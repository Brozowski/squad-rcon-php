<?php


namespace SquadSlovenia;


class Team
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Squad[]
     */
    private $squads;

    /**
     * @var Player[]
     */
    private $players;

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @return Squad[]
     */
    public function getSquads()
    {
        return $this->squads;
    }

    /**
     * @return Player[]
     */
    public function getPlayers()
    {
        return $this->players;
    }

}