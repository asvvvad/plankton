<?php

namespace Test\Controller;

use Rest\Server\Response;
use Rest\Server\Controller;
use Test\Entity\User;
use Rest\Server\Request;


class APIController extends Controller{
	/**
	 * GET example
	 * @Route(/user)
	 * @Method(GET)
	 */
	public function listUsers(Request $request){
		//list users
		$user1 = new User(1);
		$user2 = new User(2);
		
		//response
		$response = new Response();
		$response
		->setContentType(Response::CONTENT_TYPE_JSON)
		->setCode(200)
		->setContent([
			[ "id" 	=> $user1->getId(), "email" => $user1->getEmail()],
			[ "id" 	=> $user2->getId(), "email" => $user2->getEmail()]
		]);
			
		return $response;
	}
	
	/**
	 * GET example
	 * @Route(/user/{id})
	 * @Method(GET)
	 */
	public function getUser($id, Request $request){
		//get user
		$response = new Response();
		$user = new User($id);
		
		//response
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setContent([
				"id" 	=> $user->getId(), 
				"email" => $user->getEmail()
			]);
			
		return $response;
	}

	/**
	 * POST example
	 * @Route(/user)
	 * @Method(POST)
	 */
	public function createUser(Request $request){
		//create user
		$id = 23; 
		$user = new User($id);
		
		//response
		$response = new Response();
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(201)
			->setLocation("/user/{$user->getId()}");
			
		return $response;
	}
	
	/**
	 * PUT example
	 * @Route(/user/{id})
	 * @Method(PUT)
	 */
	public function putUser($id, Request $request){
		//update user
		$user = new User($id);
		$user->setEmail($request->getData("email"));
		
		//response
		$response = new Response();
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setContent([
				"id" 	=> $user->getId(),
				"email" => $user->getEmail()
			]);
			
		return $response;
	}
	
	/**
	 * PATCH example
	 * @Route(/user/{id})
	 * @Method(PATCH)
	 */
	public function patchUser($id, Request $request){
		//patch user
		$user = new User($id);
		$user->setEmail($request->getData("email"));
		
		//response
		$response = new Response();
		$response
			->setContentType(Response::CONTENT_TYPE_JSON)
			->setCode(200)
			->setContent([
				"id" 	=> $user->getId(),
				"email" => $user->getEmail()
			]);
			
		return $response;
	}
	
	/**
	 * DELETE example
	 * @Route(/user/{id})
	 * @Method(DELETE)
	 */
	public function deleteUser($id){
		//delete user
		//...
	
		//response
		$response = new Response();
		$response->setCode(204);
			
		return $response;
	}
}
