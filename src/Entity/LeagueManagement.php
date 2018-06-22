<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * LeagueManagement
 *
 * @ORM\Table(name="league_management")
 * @ORM\Entity
 */
class LeagueManagement
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var int
     *
     * @ORM\Column(name="league_id", type="integer", nullable=false, options={"comment"="id of the league"})
     */
    private $leagueId;

    /**
     *
     * @ORM\ManyToOne(targetEntity="League", inversedBy="leagues", cascade={"persist"})
     */
    private $league;

    /**
     * @var int
     *
     * @ORM\Column(name="team_id", type="integer", nullable=false, options={"comment"="id of the team"})
     */
    private $teamId;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="league", cascade={"persist"})
     */
    private $team;

    function __construct() {
        $this->team = new \Doctrine\Common\Collections\ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getLeagueId(): ?int
    {
        return $this->leagueId;
    }

    public function setLeagueId(int $leagueId): self
    {
        $this->leagueId = $leagueId;

        return $this;
    }

    public function getTeamId(): ?int
    {
        return $this->teamId;
    }

    public function setTeamId(int $teamId): self
    {
        $this->teamId = $teamId;

        return $this;
    }

    /**
     * Get team
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getTeams() {
        return $this->team;
    }

    /**
     * Get league
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getLeague() {
        return $this->league;
    }


}
