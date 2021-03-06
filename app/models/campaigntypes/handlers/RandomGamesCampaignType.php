<?php
/**
 * CampaignTypeHandler for QuantityCampaignType. 
 */
class RandomGamesCampaignType extends CampaignTypeHandler {

	/**
	 * See CampaignTypeHandler
	 */
	public function getName() {
		return 'RandomGames';
	}
	
	/**
	 * See CampaignTypeHandler
	 */
	public function getDescription() {
		return 'This campaign is made to create the illusion of a game that will take the user to a random game. The user gets rewarded for this every time the user finishes a game. ';
	}
	
	/**
	 * See CampaignTypeHandler
	 */
	public function getExtrasDiv($extraInfo) {
		$extraInfo = unserialize($extraInfo);
		$label = $extraInfo['label'];
		$divHTML = "";
		$divHTML .= "<label for='data' class='col-sm-2 control-label'>Label:</label>";
		$divHTML .= "<input class='form-control' name='randomGamesCampaignLabel' type='text' value='".$label."' id='randomGamesCampaignLabel'>";
		$divHTML .= "";
		return $divHTML;
	}
	
	/**
	 * See CampaignTypeHandler
	 */
	public function parseExtraInfo($inputs) {
		$extraInfo['label'] = [];
		$extraInfo['label'] = $inputs['randomGamesCampaignLabel'];
		return serialize($extraInfo);
	}
	
	/**
	 * See CampaignTypeHandler
	 */
	public function getThumbnail() {
		return 'img/icons/RandomGame_icon.png';
	}
	
	/**
	 * See CampaignTypeHandler
	 */
	public function getView($campaign) {
		
		//Retrieve an array of all games in this campaign
		$crude_game_array = CampaignGames::select('game_id')->where('campaign_id',$campaign->id)->get()->toArray();
		$game_array = array_column($crude_game_array, 'game_id');
		
		//select the next gameId in this campaign for this user
		$randomGameIndex = array_rand($game_array);
		$gameId = $game_array[$randomGameIndex];
		
		//Put the next consecutive game in the game variable
		$game = Game::find($gameId);
		
		// Get all campaigns that this game is in
		$campaignIdArray = CampaignGames::where('game_id',$gameId)->select('campaign_id')->get()->toArray();
		foreach($campaignIdArray as $key => $campaignId){
			$campaignIdArray[$key] = implode(",", $campaignId);
		}
		
		// Use corresponding game controller to display game.
		$handlerClass = $game->gameType->handler_class;
		$handler = new $handlerClass();
		
		//build the view with all extra info that is in the "extraInfo" column of the game model
		$view = $handler->getView($game);
		foreach(unserialize($game['extraInfo']) as $key=>$value){
			$view = $view->with($key, $value);
		}
		$view = $view->with('campaignMode', true);
		
		if(isset($responseLabel) && $responseLabel != null){
			$view = $view->with('responseLabel', $responseLabel); //to overwrite any responselabel of the non-campaignMode game
		}
		$view = $view->with('campaignIdArray', $campaignIdArray)->with('randomGamesCampaign', true); //campaignIdArray should contain 
		//all campaignId's of all campaigns of which the progress should be updated. This one updates all of them, because this campaign 
		//is meant as a random game instead of an actual campaign. 
		return $view;
	}
	
	/**
	 * See CampaignTypeHandler
	 * The $gameOrigin variable indicates if the user comes from the game menu or the campaign menu. 
	 * This is important because we need to redirect to the correct menu after the response is processed. 
	 * The $done variable indicates if there are more responses to be processed or not. 
	 * If this is the last response to be processed, the $done variable is true and we need to redirect to the correct menu after the response is processed.
	 */
	public function processResponse($campaign,$gameOrigin,$done,$game) {
		//get the currently played campaign id. If it's not there, it's null.
		$currentlyPlayedCampaignId = Input::get('currentlyPlayedCampaignId');
		//Only redirect if $done is true
		if($done){
			//if the user came here from the game menu instead of the campaign menu, redirect to the game the user came from
			//add the campaignScoreTag that was put into the session variable when it exists, so that the score gained is showed in the next game view. 
			if($gameOrigin){
				return Redirect::to('playGame?gameId='.$game->id)->with('campaignScoreTag', Session::pull('campaignScoreTag', null));
			} else { //if a user came here from the campaign menu, figure out what to redirect to
				return Redirect::to('playCampaign?campaignId='.$currentlyPlayedCampaignId)->with('campaignScoreTag', Session::pull('campaignScoreTag', null));
			}
		}
	}
	
	/**
	 * Returns an array of tasks that are in the given game. 
	 */
	function tasksInGame($gameId){		
		$crudeTasksArray = GameHasTask::where('game_id',$gameId)->select('task_id')->get()->toArray();
		$tasksArray = array_column($crudeTasksArray,'task_id');
		return $tasksArray;
	}
	
	/**
	 * See GameTypeHandler
	 */
	public function renderCampaign($game) {
		return "";
	}
	
	/**
	 * See GameTypeHandler
	 */
	public function validateData($data) {
		return "";
	}
}