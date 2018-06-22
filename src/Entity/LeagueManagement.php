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
     *
     * @ORM\ManyToOne(targetEntity="League", inversedBy="teams")
     * @ORM\JoinColumn(name="league_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $league;

    /**
     * @var int
     *
     * @ORM\Column(name="league_id", type="integer", nullable=false, options={"comment"="id of the league"})
     */
    private $leagueId;

    /**
     * @ORM\ManyToOne(targetEntity="Team", inversedBy="leagues")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="id", onDelete="SET NULL")
     */
    private $team;

    /**
     * @var int
     *
     * @ORM\Column(name="team_id", type="integer", nullable=false, options={"comment"="id of the team"})
     */
    private $teamId;


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
     * Set league
     *
     * @param \App\Entity\League $league
     * @return ProductAspect
     */
    public function setLeague(\App\Entity\League $league = null)
    {
        $this->league = $league;

        return $this;
    }

    /**
     * Get league
     *
     * @return \App\Entity\League 
     */
    public function getLeague()
    {
        return $this->league;
    }

    /**
     * Set team
     *
     * @param \App\Entity\Team $team
     * @return LeagueManagement
     */
    public function setTeam(\App\Entity\Team $team = null)
    {
        $this->team = $team;

        return $this;
    }

    /**
     * Get team
     *
     * @return \App\Entity\Team 
     */
    public function getTeam()
    {
        return $this->team;
    }
}
