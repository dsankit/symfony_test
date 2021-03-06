<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

use App\Entity\Team;
use App\Entity\League;

class SecurityController extends Controller
{


    public function getEm() {
        return $this->getDoctrine()->getManager();
    }

    /*
    * Function is used to validate token
    */
    public function isValidToken(Request $request) {
        $inputToken = $request->headers->get('authorization');
		$token = $this->get('lexik_jwt_authentication.encoder')->decode($inputToken);
	
		if($token){
			return true;
		} else {
			return false;
		}
    }

	/**
	* @Route("/token", name="fetch_token")
	* @Method("POST")
	*/
	public function fetchToken(Request $request)
	{

		$em = $this->getEm();
		$team_name = $request->request->get('team_name');

		// check team
		$team = $em->getRepository('App\Entity\Team')->findOneBy(['name' => $team_name]);
		if (!$team) {
			return new JsonResponse(array('code' => 400, 'message' => 'Requested team not found.'));
		}

		$token = $this->get('lexik_jwt_authentication.encoder')->encode(['team_name' => $team->getName()]);
		return new JsonResponse(
			array(
				'token' => $token,
				'exp' => time() + 3600
			)
		);
	}

	/**
	* @Route("/create-team", name="create_team")
	* @Method("POST")
	*/
	public function createTeam(Request $request)
	{


		$em = $this->getEm();

		$responseArr = array();
		$team_name = $request->request->get('name');
		$strip     = $request->request->get('strip');

		$parameters = $request->request->all();
		if($team_name == '') return new JsonResponse(array( 'status' => 400, 'message' => "Invalid team name"));
		if($strip == '') return new JsonResponse(array( 'status' => 400, 'message' => "Invalid strip"));

		// checking for unique team name
		$tmp = $em->getRepository('App\Entity\Team')->findOneBy(['name' => $team_name]);
		if($tmp){
			return new JsonResponse(array( 'status' => 400, 'message' => "Team Name already exists. Please try any other name."));
		}

		// saving new team
		$team      = new Team();
		$team->setName($team_name);
		$team->setStrip($strip);

		$em->persist($team);
		$em->flush();

		$responseArr = array('code' => 200, 'message' => 'Team created successfully !');

		return new JsonResponse($responseArr);
	}

	/**
	* @Route("/create-league", name="create_league")
	* @Method("POST")
	*/
	public function createLeague(Request $request)
	{


		$em = $this->getEm();

		$responseArr = array();
		$league_name = $request->request->get('name');

		if($league_name == '') return new JsonResponse(array( 'status' => 400, 'message' => "Invalid league name"));


		// saving new league
		$league    = new League();
		$league->setName($league_name);

		$em->persist($league);
		$em->flush();

		$responseArr = array('code' => 200, 'message' => 'League created successfully !');

		return new JsonResponse($responseArr);
	}


	/**
	* @Route("/fetch-all-teams", name="fetch_all_teams")
	* @Method("get")
	*/
	public function fetchAllTeams(Request $request)
	{
		if(!$this->isValidToken($request)){
			return new JsonResponse(array('code' => 403, 'message' => 'Invalid Token'));
		}

		$em = $this->getEm();
		$teams = $em->getRepository('App\Entity\Team')->findAll();

		$tmpArr = array();
		foreach ($teams as $key => $value) {
			$tmpArr[] = array(
				'id' => $value->getId(),
				'name' => $value->getName(),
				'strip' => $value->getStrip(),
			);
		}

		return new JsonResponse($tmpArr);
	}


	/**
	* @Route("/edit-team/{id}", name="edit_team")
	* @Method("POST")
	*/
	public function editTeam(Request $request, $id)
	{
		if(!$this->isValidToken($request)){
			return new JsonResponse(array('code' => 403, 'message' => 'Invalid Token'));
		}

		$em = $this->getEm();
		// checking team existance
		$team = $em->getRepository('App\Entity\Team')->findOneBy(['id' => $id]);
		if(!$team){
			return new JsonResponse(array('code' => 400, 'message' => 'Requested team not found.'));
		}


		$team_name = $request->request->get('name');
		$strip     = $request->request->get('strip');
		// validating parameters
		if($team_name == ''){
			return new JsonResponse(array( 'status' => 400, 'message' => "Invalid team name"));
		}

		if($strip == ''){
			return new JsonResponse(array( 'status' => 400, 'message' => "Invalid strip"));
		}

		// checking for team name not exists
		$tmp = $em->getRepository('App\Entity\Team')->findOneBy(['name' => $team_name]);
		if($tmp && $tmp->getId() != $id){
			return new JsonResponse(array( 'status' => 400, 'message' => "Team Name already exists. Please try any other name."));
		}

		// updating team
		$team->setName($team_name);
		$team->setStrip($strip);
		$em->persist($team);
		$em->flush();

		$responseArr = array('code' => 200, 'message' => 'Team updated successfully !');

		return new JsonResponse($responseArr);
	}


	/**
	* @Route("/get-team-for-league/{id}", name="test")
	* @Method("GET")
	*/
	public function test(Request $request, $id)
	{

		$em = $this->getEm();
		$league = $em->getRepository('App\Entity\League')->findOneBy(['id' => $id]);

		// check league exists
		if(!$league){
			return new JsonResponse(array('code' => 400, 'message' => 'Requested league not found.'));
		}

		$leagueData = array();
		$teamArr = [];
		foreach ($league->getTeams() as $key => $team) {
			$teamArr[] = array(
				'id' => $team->getTeam()->getId(),
				'name' => $team->getTeam()->getName(),
				'strip' => $team->getTeam()->getStrip(),
			);
		}
		$leagueData[] = array(
			'id' => $league->getId(),
			'league_name' => $league->getName(),
			'teams' => $teamArr,
		);

		return new JsonResponse($leagueData);
	}


	/**
	* @Route("/delete-league/{id}", name="delete_league")
	* @Method("GET")
	*/
	public function deleteLeague(Request $request, $id)
	{

		$em = $this->getEm();
		$league = $em->getRepository('App\Entity\League')->find($id);

		// check league exists
		if(!$league){
			return new JsonResponse(array('code' => 400, 'message' => 'Requested league not found.'));
		}

		// remove league
		$em->remove($league);
   		$em->flush(); 

   		$responseArr = array('code' => 200, 'message' => 'League deleted successfully !');
		return new JsonResponse($responseArr);
	}
}