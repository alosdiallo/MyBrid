<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');
include_once("interaction.php");
include_once("pictures.php");
include_once("datatype.php");

define("PLATES_PER_BAIT", 3);

class Data extends CI_Controller{
	
	private $transcriptor;
	private $promoter;
	private $pictures;
	private $data;
	private $interaction;
	private $info;
	private $picdata;
	private $positiveSearch;
	private $list_tags = array(-1=>"_error", 0=>"_1-4", 1=>"_5-8", 2=>"_9-12");

	
	public function __construct(){
		parent::__construct();
		$this->transcriptor = "";
		$this->promoter = "";
		
		$promoterData = array();
		$this->tsfData = array();
		
		
		$this->pictures = array();
		$this->picdata = array();
		$this->info = array();
	}
	
	/*
	$this->input->post('promoter')
	$this->input->post('transcriptor')
	$this->input->post('positiveSearch')
	$this->input->post('user_id')
	$this->input->post('project_id')
	*/
	public function cutIntoArray($string){
		if($string == ""){
			return array("");
		} else {
			$pattern = '/,(\s*)/';
			return $this->multi_explode($pattern, trim($string));
		}
	}
	
	public function sortCombinedArray($array, $user, $project){
		$this->load->model('Data_Model');
		$return = array();
		$return['transcriptionFactor'] = array();
		$return['promoter'] = array();

		$list = $this->Data_Model->getTranscriptionFactorList($user, $project);
		//if($query->num_rows > 0){return "There is still promoter data present!";}
		foreach($array as $val){
			//Transcription Factor Check
			$transcriptionFactorQuery = $this->db->query("SELECT * FROM TranscriptorFactor WHERE list = '$list' AND ( coordinate = '$val' OR orf_name = '$val' OR orf_name2 = '$val' OR wb_gene = '$val' OR common_name = '$val' OR info = '$val' )");
			if($transcriptionFactorQuery->num_rows > 0 && $val != ""){
				$return['transcriptionFactor'][] = $val;
			}
			
			//Promoter Check
			$promoterQuery = $this->db->query("SELECT * FROM Promoter WHERE user_id = '$user' AND project_id = '$project' AND (bait_id = '$val' OR bait_name = '$val' OR bait_name2 = '$val' OR bait_name3 = '$val')");
			if($promoterQuery->num_rows > 0 && $val != ""){
				$return['promoter'][] = $val;
			}
		}
		return $return;
	}
	
	/////////////////////////////////////////////////////////////////////////////////////////
	/// Grabs the appropriate transcriptionfactor list using user and project as input
	/// Uses the projects table
	/////////////////////////////////////////////////////////////////////////////////////////
	public function getTranscriptionFactorList($user, $project){
		if( !$this->Config_Model->checkLogin() ) {return array();}
		
		// GRAB THE APPROPRIATE TF LIST
		$query = $this->db->query("SELECT tf_list FROM Projects WHERE user_id = '$user' AND project_id = '$project'");
		if($query){ // IS VALID QUERY
			if($query->num_rows() > 0){ // QUERY HAS RESULTS
				foreach($query->result() as $row){ // ROWS = 1
					if(isset($row->tf_list)){ // TF LIST IS SET
						return $row->tf_list;
					}
				}
			}
		}
		return false;
		// END GRAB LIST
	}
	/////////////////////////////////////////////////////////////////////////////////////////
	
	public function transcriptionFactorSearch($transcriptionFactor, $list, $positiveSearch, $bleedoverSearch){
	
		$transcriptionFactorResult = array(0=>array(), 1=>array(), 2=>array());
		if($positiveSearch == "true"){
			$transcriptionFactorString = "SELECT DISTINCT TranscriptorFactor.*, Interactions.modified_call";
			if($bleedoverSearch == "true"){
				$transcriptionFactorString .= ", Interactions.bleed_over";
			}
			$transcriptionFactorString .= " FROM TranscriptorFactor, Interactions WHERE TranscriptorFactor.coordinate = Interactions.array_coord AND TranscriptorFactor.list = '$list' AND (TranscriptorFactor.coordinate = '$transcriptionFactor' OR TranscriptorFactor.orf_name = '$transcriptionFactor' OR TranscriptorFactor.orf_name2 = '$transcriptionFactor' OR TranscriptorFactor.wb_gene  = '$transcriptionFactor' OR TranscriptorFactor.common_name = '$transcriptionFactor' OR TranscriptorFactor.info = '$transcriptionFactor') AND Interactions.modified_call = 'Positive'";
			if($bleedoverSearch == "true"){
				$transcriptionFactorString .= " AND Interactions.bleed_over = 'BO'";
			}
		} else {
			$transcriptionFactorString = "SELECT DISTINCT * FROM TranscriptorFactor WHERE list = '$list' AND (coordinate = '$transcriptionFactor' OR orf_name = '$transcriptionFactor' OR orf_name2 = '$transcriptionFactor' OR wb_gene  = '$transcriptionFactor' OR common_name = '$transcriptionFactor' OR info = '$transcriptionFactor')";
		}
		
		$transcriptionFactorQuery = $this->db->query($transcriptionFactorString);
		if(!$transcriptionFactorQuery){
			// The query has failed pretty terribly..
			return $transcriptionFactorResult;
		} else {
			if($transcriptionFactorQuery->num_rows() > 0){
				foreach($transcriptionFactorQuery->result() as $row){
					$transcriptionFactorResult[(int)$row->plate_number][] = $row;
				}
			} else {
				// The number of rows = 0
				return $transcriptionFactorResult;
			}
		}
		return $transcriptionFactorResult;
		
	}
	
	public function promoterSearch($promoter, $user, $project, $positiveSearch, $bleedoverSearch, $transcriptionFactors, $promoterSearch, $andSearch){
		$promoterResult = array();
		$promoterResultsLoop = 0;
		
		$list = $this->Data_Model->getTranscriptionFactorList($user, $project);
		
		if($positiveSearch == "true"){
			$promoterString = "SELECT DISTINCT Promoter.*, Interactions.plate_number, Interactions.array_coord, Interactions.modified_call";
			if($bleedoverSearch == "true"){
				$promoterString .= ", Interactions.bleed_over";
			}
			$promoterString .= " FROM Promoter, Interactions WHERE Interactions.plate_name = Promoter.bait_id AND Interactions.user_id = '$user' AND Promoter.user_id = '$user' AND Interactions.project_id = '$project' AND Promoter.project_id = '$project' AND Interactions.modified_call = 'Positive'";
			if($bleedoverSearch == "true"){
				$promoterString .= " AND Interactions.bleed_over = 'BO'";
			}
			if(strtoupper($promoter) != "ALL"){
				$promoterString .= " AND (Promoter.bait_id = '$promoter' OR Promoter.bait_name = '$promoter' OR Promoter.bait_name2 = '$promoter' OR Promoter.bait_name3 = '$promoter') ";
			}
			$promoterString .= " ORDER BY bait_id, plate_number";
		} else {
			$promoterString = "SELECT DISTINCT * FROM Promoter WHERE user_id = '$user' AND project_id = '$project'"; 
			if(strtoupper($promoter) != "ALL"){
				$promoterString .= " AND (bait_id = '$promoter' OR bait_name = '$promoter' OR bait_name2 = '$promoter' OR bait_name3 = '$promoter')";
			}
		}
		//print_r($transcriptionFactors);
		$promoterQuery = $this->db->query($promoterString);
		// Long Explaination of search
		// Set up a few variables for later on
		$bait_old = "";
		$plateNumber_old = "";
		// Alright, basic check do we have the promoterQuery set, if not something very bad happened
		if(!$promoterQuery){
			// The query has failed pretty terribly..
			return $promoterResult;
		} else {
			// Does the query have any results attached to it or did it come back empty
			if($promoterQuery->num_rows() > 0){
				
				// Is this a positive search? If it is we have to look at the transcriptionData and make sure THAT is positive. We do this by grabbing ALL positives for the plate and then looking through each of them to see if they are the transcriptionData
				// Why do we have to do this?
				if($positiveSearch == "true"){
					// So go through the results
					foreach($promoterQuery->result() as $row){
						// Make sure we only have to go through the results once. Can only be done due to sorting the query before getting to this step.
						if(($bait_old != $row->bait_id) || ($plateNumber_old != $row->plate_number)){
							// The case where the transcription factors are not BLANK, means we only want to know whether the transcription factors are a positive match.
							foreach($transcriptionFactors[$row->plate_number] as $tf){
								// Run through each transcription factor for this plate and see if it is positive. If it is, add it to the transcription factors for the plate
								if(isset($tf->coordinate)){
									if($this->isPositive($row->bait_id, $tf->coordinate, $user, $project)){
										if($bait_old != $row->bait_id || $plateNumber_old != $row->plate_number){
											$promoterResult[$promoterResultsLoop] = clone $row;
											$promoterResult[$promoterResultsLoop]->transcriptionData = array();
											$bait_old = $row->bait_id;
											$plateNumber_old = $row->plate_number;
										}
										array_push($promoterResult[$promoterResultsLoop]->transcriptionData, $tf);
									}
								}
								
								/*if(isset($tf->coordinate)){
									$tfValue = $tf->coordinate;
								} else {
									$tfValue = "NO MATCH";
								}
								if(isset($row->array_coord)){
									if(($row->array_coord == $tfValue)){
										// If the Transcription Data for this plate number is already set, that means that the plate @ this plate number has already been set to be displayed, no need to double up on it.
										
										if(!isset($promoterResult[$promoterResultsLoop]->transcriptionData)){
											//Now that we have the goods, we set promoter data and then attach the appropriate transcription data
											$promoterResult[$promoterResultsLoop] = clone $row;
											// If the search isn't a promoter search just attach the tf data straight onto the promoter data.
											if(!$promoterSearch){
												$promoterResult[$promoterResultsLoop]->transcriptionData = $transcriptionFactors[$row->plate_number];
											}
											$promoterResult[$promoterResultsLoop]->list = $list;
											$bait_old = $row->bait_id;
											$plateNumber_old = $row->plate_number;
										}
									}
								}*/
								
							}
							// The case where the transcription factors are BLANK, means that this is a promoter search and we only want to know if the plate does have a positive.
							if(isset($transcriptionFactors[$row->plate_number][0])){
								if($promoterSearch && $transcriptionFactors[$row->plate_number][0] == "BLANK"){
									//echo "Reached";
									if($this->isPositive($row->bait_id, $row->array_coord, $user, $project)){
										$promoterResult[$promoterResultsLoop] = clone $row;
										$promoterResult[$promoterResultsLoop]->transcriptionData[] = $transcriptionFactors[$row->plate_number];
										$bait_old = $row->bait_id;
										$plateNumber_old = $row->plate_number;
									}
								}
							}

							$promoterResultsLoop++;
						}
					}
				// OK! now assuming we didn't do a positive search, we do not need to confirm positives for the transcription factors so just loop through the results and make sure we add all of the plates represented in the transcriptionData
				} else {
					// For each result
					foreach($promoterQuery->result() as $row){
						//For each possible plate with the result
						for($tfLoop = 0; $tfLoop <= 3; $tfLoop++){
							// If everything is working
							if(isset($transcriptionFactors[$tfLoop])){
								// And there is at least one transcriptionFactor that maps to the plate
								if($transcriptionFactors[$tfLoop]){
									// Add to the results, simple.
									$promoterResult[$promoterResultsLoop] = clone $row;
									$promoterResult[$promoterResultsLoop]->plate_number = $tfLoop;
									$promoterResult[$promoterResultsLoop]->transcriptionData = $transcriptionFactors[$tfLoop];
									$promoterResult[$promoterResultsLoop]->list = $list;
									$promoterResultsLoop++;
								}
							}
						}
					}
				}
			} else {
				// The number of rows = 0
				return $promoterResult;
			}
		}
		return $promoterResult;//*/
	}
	
	public function isPositive($bait_id, $array_coord, $user, $project){
		$positiveString = "SELECT modified_call FROM Interactions WHERE plate_name = '$bait_id' AND array_coord = '$array_coord' AND user_id = '$user' AND project_id = '$project'";
		$positiveQuery = $this->db->query($positiveString);
		$numberPositive = 0;
		if($positiveQuery){
			// Does the query have any results attached to it or did it come back empty
			if($positiveQuery->num_rows() > 0){
				foreach($positiveQuery->result() as $row){
					if($row->modified_call == "Positive"){
						$numberPositive++;
					}
				}
			} 
		}
		if($numberPositive >= 2){
			return true;
		}
		return false;
	}
	
	public function setImages($data){
		$plateNumber_tags = array(0=>"_1-4", 1=>"_5-8", 2=>"_9-12");
		//print_r($data);
		//print_r($data);
		foreach($data as &$id){
			$str = "SELECT * FROM images WHERE user_id = '".$id->user_id."' AND project_id = '".$id->project_id."' AND image LIKE '".$id->bait_id ."%". $plateNumber_tags[$id->plate_number]."\_%'"; 

			$query = $this->db->query($str);
			if(!$query){
				// THERES BEEN A PROBLEM!
				//return "ERROR: " . $id->bait_id . "did not return a picture in Data_Model:setPictures."; 
			} else {
				if($query->num_rows() > 0){
					foreach($query->result() as $row){
						$id->image = $row->image;
					}
				}
			}
		}
		return $data;
	}
	
	/// ALL Search needs to be reimplemented
	public function browseInteractionsSearch_2(){
		
		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->model('Data_Model');
		$this->load->model('Config_Model');
		$this->Config_Model->checkLogin();
		
		$queryData['promoterSearch'] = FALSE;
		
		//$queryData['interactionSearch'] = TRUE;
		$andSearch = $this->input->post('andSearch');
		$rawCombinedString = $this->input->post('combined');
		$rawPromoterString = $this->input->post('promoter');
		$rawTranscriptionFactorString = $this->input->post('transcriptor');
		
		// If the search is blank that is a bad search and return error NOSEARCH
		if($rawCombinedString == "" && $rawPromoterString == "" && $rawTranscriptionFactorString == ""){
			echo "NOSEARCH";
			return;
		}
		
		$positiveSearch = $this->input->post('positiveSearch');
		$bleedoverSearch = $this->input->post('bleedoverSearch');
		
		///////////////////////////
		// Grabs the project id and user id from post
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user_id'), $this->input->post('project_id'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}
		
		/*print_r($users);
		print_r($projects);*/
		//////ALL SEARCH!//////////////////////////////////////////////////////////
		// 	Sets up the specific parameters that are involved in an all search.
		// If the all search is ambiguous such as when the combined box or the transcriptionfactor box has all in it set up a basic all search
		// Also no matter the all search make sure we're not also doing an and search, no reason to be forced to display plates twice.
		/// If Promoter = all then query = all

		if(strtoupper($rawPromoterString) == "ALL" ){
			$andSearch = "FALSE";		
		}

		if(strtoupper($rawTranscriptionFactorString) == "ALL" ){
			$rawPromoterString = "ALL";
			$rawTranscriptionFactorString = "";

			$andSearch = "FALSE";	
		}

		if(strtoupper($rawCombinedString) == "ALL"){
			$rawPromoterString = "ALL";
			$rawTranscriptionFactorString = "";
			$rawCombinedString = "";

			$andSearch = "FALSE";	
		}
	


		////////////////////////////////////////////////////////////////
		
		// Initialize values
		$promoterData = array();
		$promoterFinal = array();
		$proj_loop_count = 0;
		
		$rawCombinedArray = $this->cutIntoArray($rawCombinedString);
		$rawPromoterArray = $this->cutIntoArray($rawPromoterString);
		$rawTranscriptionFactorArray = $this->cutIntoArray($rawTranscriptionFactorString);
	
	
		$transcriptionFactorDataResults = array();
		
		
		$transcriptionFactorDataResults_Final = array();
		$promoterDataResults_Final = array();
		
		
		foreach($users as $user){
			//$user = $users[$proj_loop_count];
			$project = $projects[$proj_loop_count];
			$list = $this->getTranscriptionFactorList($user, $project);
			
			// Grab the combined array then combine it with the raw promoter and transcription data
			$combined = $this->sortCombinedArray($rawCombinedArray, $user, $project);
			if($rawPromoterArray[0] == ""){
				if($combined['promoter']){
					$promoters = $combined['promoter'];
				} else {
					$promoters = $rawPromoterArray;
				}
			} else {
				$promoters = array_merge($combined['promoter'], $rawPromoterArray);
			}
			
			if($rawTranscriptionFactorArray[0] == ""){
				if($combined['transcriptionFactor']){
					$transcriptionFactors = $combined['transcriptionFactor'];
				} else {
					$transcriptionFactors = $rawTranscriptionFactorArray;
				}
			} else {
				$transcriptionFactors = array_merge($combined['transcriptionFactor'], $rawTranscriptionFactorArray);
			}
			/////////////
			if($promoters[0] == ""){
				$promoterSearch = false;
			} else {
				$promoterSearch = true;
			}
			
			//////////////
			$transcriptionFactorData = array(0=>array(), 1=>array(), 2=>array());
			foreach($transcriptionFactors as $transcriptionFactor){
				if($transcriptionFactor == ""){
					// Do Nothing?
				} else {
					// Add to transcription Factor Array
					$transcriptionFactorRawData = $this->transcriptionFactorSearch($transcriptionFactor, $list, $positiveSearch, $bleedoverSearch);
					$transcriptionFactorData[0] = array_merge($transcriptionFactorData[0], $transcriptionFactorRawData[0]);
					$transcriptionFactorData[1] = array_merge($transcriptionFactorData[1], $transcriptionFactorRawData[1]);
					$transcriptionFactorData[2] = array_merge($transcriptionFactorData[2], $transcriptionFactorRawData[2]);
				}
			}
			
			// If it's not an and search go ahead and search for transcriptionFactor Data Results now
			// That is to say that if we are doing an or search, we should search through the transcription factors now
			// If we're doing an and search we can skip this and just search through the promoters.
			if($andSearch == "FALSE"){
				$transcriptionFactorDataResults = $this->promoterSearch("ALL", $user, $project, $positiveSearch, $bleedoverSearch, $transcriptionFactorData, false, $andSearch);
			}
			
			// We need a special case in here, if we're doing a or search OR have no transcription factor string and doing an and search. we need to set up dummy data.
			if($andSearch == "FALSE" || ($rawTranscriptionFactorString == "" && $andSearch == "TRUE") ){
				// So set up blanks in the transcription factor array
				for($transcriptionFactorDataLoop = 0; $transcriptionFactorDataLoop <= 2; $transcriptionFactorDataLoop++){
					if(!$transcriptionFactorData[$transcriptionFactorDataLoop]){
						$transcriptionFactorData[$transcriptionFactorDataLoop][] = "BLANK";
					}
				}
			}
			
			//$dummyTranscriptionFactorData = array(0=>array(0), 1=>array(0), 2=>array(0));
			$promoterDataResults = array();
			foreach($promoters as $promoter){
				//var_dump($promoter);
				if(!$promoter){
					//echo $andSearch;
					// We need a special case, if we're doing an and search and the promoter is empty but the transcriptionfactor has data, we need to still do the search
					if($andSearch == "TRUE"){
						$promoterDataResults = array_merge($promoterDataResults, $this->promoterSearch("ALL", $user, $project, $positiveSearch, $bleedoverSearch, $transcriptionFactorData, true, $andSearch));
					}
				} else {
					$promoterDataResults = array_merge($promoterDataResults, $this->promoterSearch($promoter, $user, $project, $positiveSearch, $bleedoverSearch, $transcriptionFactorData, true, $andSearch));
					// Grab the three plates
				}
			}
			
			$transcriptionFactorDataResults_Final = array_merge($transcriptionFactorDataResults_Final, $transcriptionFactorDataResults);
			$promoterDataResults_Final = array_merge($promoterDataResults_Final, $promoterDataResults);
			$proj_loop_count++;
			//echo $proj_loop_count."\n";
		}	// End foreach($users as $user){
		
		$dataResults_Final = $this->setImages(array_merge($promoterDataResults_Final, $transcriptionFactorDataResults_Final));
		
		if(!$dataResults_Final){
			echo "QUERYFAILED";
			return;
		}
		echo json_encode($dataResults_Final);
	}
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	
	public function browseInteractionsSearch(){
		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->model('Data_Model');
		$this->load->model('Config_Model');
		$this->Config_Model->checkLogin();
		
		$queryData['promoterSearch'] = FALSE;
		//$queryData['interactionSearch'] = TRUE;
		
		$rawCombinedString = $this->input->post('combined');
		$rawPromoterString = $this->input->post('promoter');
		$rawTranscriptionFactorString = $this->input->post('transcriptor');
		
		
		///////////////////////////
		// Grabs the project id and user id from post
		$project_info = $this->Data_Model->getMetaProjects($this->input->post('user_id'), $this->input->post('project_id'));
		
		if($project_info['users'] && $project_info['projects']){
			$users = $project_info['users'];
			$projects = $project_info['projects'];
		} else {
			$users = array(0 => "NULL");
			$projects = array(0 => "NULL");
		}
		/////////////////////////
		
		$this->setTranscriptor(stripslashes($this->input->post('transcriptor')));
		$this->setPromoter(stripslashes($this->input->post('promoter')));
		
		
		

		// Grabs user control data: User list, project list and current user
		$tags['user_controls']['current_user'] = $this->session->userdata['username'];
		
		////////////////////////////////////////////////////////////////
		
		/*//////////////////////////////////////////////////////////////
		 * 	Sets the Transcriptor and the Promoter Values
		 *			Takes the values from post and will make them into an array		
		 */
		/// If Promoter = all then query = all
		if(strtoupper($this->input->post('promoter')) == "ALL" || strtoupper($this->input->post('transcriptor')) == "ALL" ){
			$queryData['ALL'] = "TRUE";
		} else {
			$queryData['ALL'] = "FALSE";
		}
		
		////////////////////////////////////////////////////////////////
		$queryData['positive']                 = $this->input->post('positiveSearch');
		
		// Keeps track of search failures
		$tags['queryFail'] = false; // TRUE when query is bad
		$tags['noSearch']  = false; // TRUE when no query is present
		$tags['notLoggedIn']  = false; // TRUE when user is not logged in
		////////////////////////////////////////////////////////////////
		
		/*///////////////////////////////////////////////////////////////
		 * Check to see if the user is logged in
		 */
		if(!$this->session->userdata('is_logged_in')) {$tags['notLoggedIn'] = true;}
		///////////////////////////////////////////////////////////////
		/*///////////////////////////////////////////////////////////////
		 * Set up defaults for transcription factor Data and promoter data
		 */
		
		$this->tsfData = false;

		// Initialize values
		$promoterData = array();
		$promoterFinal = array();
		$proj_loop_count = 0;
		///////////////////////////////////////////////////////////////

		$rawCombinedArray = $this->cutIntoArray($rawCombinedString);
		$rawPromoterArray = $this->cutIntoArray($rawPromoterString);
		$rawTranscriptionFactorArray = $this->cutIntoArray($rawTranscriptionFactorString);

		foreach($users as $user){
			// Initialize values for loop
			$j = 0;
			$queryData['userId'] = $users[$proj_loop_count];
			$queryData['projectId'] = $projects[$proj_loop_count];
			
			// Grab the combined array then combine it with the raw promoter and transcription data
			$combined = $this->sortCombinedArray($rawCombinedArray, $queryData['userId'], $queryData['projectId']);
			if($rawPromoterArray[0] == ""){
				if($combined['promoter']){
					$promoters = $combined['promoter'];
				} else {
					$promoters = $rawPromoterArray;
				}
			} else {
				$promoters = array_merge($combined['promoter'], $rawPromoterArray);
			}
			
			if($rawTranscriptionFactorArray[0] == ""){
				if($combined['transcriptionFactor']){
					$transcriptionFactors = $combined['transcriptionFactor'];
				} else {
					$transcriptionFactors = $rawTranscriptionFactorArray;
				}
			} else {
				$transcriptionFactors = array_merge($combined['transcriptionFactor'], $rawTranscriptionFactorArray);
			}
			/////////////
			
			foreach($promoters as $prom){ // $prom = promoter
				// If there is a valid promoter that was search, set promoter search == true
				if($prom){
					$queryData['promoterSearch'] = TRUE;
				}
				$queryData['promoter'] = $prom;
				/*
				 * Initialize values for the transcriptor Loopthrough
				 */
				$i = 0;
				$this->tsfData = array();
				 //
				 
				$promoterSearched = false;		// Keeps track if the promoter is searched, so it isn't done multiple times
				
				/*
				 * Set up a few variables for positive only searches
				 */
				$plateNumber = 0;
				$plateSuffix = 0;
				$orf_name = 0;
				 //
				 
				foreach($transcriptionFactors as $transfact){ // $tsf = transcriptionfactor
					//$queryData['transcriptionFactor'] = $tsf;
					
					/*
					 * Check to see if the query is a valid one
					 */
					if($prom || $transfact){ // first check: has a promoter or transcriptor been searched
						/*
						 * second check: Is the tsf valid
						 */
						if($transfact){
							$singleTsfData = $this->Data_Model->getTranscriptionFactorData($transfact, $queryData['userId'], $queryData['projectId']);
							if(!$singleTsfData){$tags['queryFail'] = true;}
							
							foreach($singleTsfData as $tf){
								/*
								 * parseCoordinates for good transcriptors
								 */
								$this->Data_Model->parseCoordinates($tf);
								
								$queryData['plateNumber']         = $tf->position['plate_num'];
								$queryData['transcriptionFactor'] = $tf->coordinate;
								
								/*
								 * Positive only search variables.
								 * Also grab the plateNumber out of position
								 */
								$plateNumber = $tf->position['plate_num'];
								/*
								 * Add the singleTSFDATA onto the tsfData with plateNumber indexed
								 */
								$this->tsfData[$tf->position['plate_num']][] = $tf;
							}
						}
						
					} else {
						$tags['noSearch'] = true;
					}
					
					$i++;
				}// end foreach transcriptor
				
				/*
				 * If there is not supposed to be transcription data, Still add some transcriptionData
				 */
				if (!$transcriptionFactors[0] && $promoters[$j] ) { // Transcription Factor Does Not Exist, Promoter Exists	
					/*
					 * Set plate Numbers
					 */
					$this->tsfData[0][0]->position['plate_num'] = 0;	// platenum 0
					$this->tsfData[1][0]->position['plate_num'] = 1;	// platenum 1
					$this->tsfData[2][0]->position['plate_num'] = 2;	// platenum 2
				}
					
				/*
				 * Check to see if the query is a valid one
				 */
				if($prom || $transfact){ // first check: has a promoter or transcriptor been searched	
					//$singlePromoterData = $this->Data_Model->getPromoterData($prom, $ids, $this->tsfData, $positiveSearch);
					$singlePromoterData = $this->Data_Model->getPromoterData($queryData, $this->tsfData);
					if(!$singlePromoterData && strtoupper($this->input->post('user_id')) != "ALL") {$tags['queryFail'] = true;}	// Did the promoter search fail?
				}
				if( isset($singlePromoterData) ){ // Ensure success before continuing
					/*
					 * Merge the new data with the promoter Data
					 */
					$promoterData	= array_merge($promoterData, $singlePromoterData);
				}
					
				$j++;
			} // end foreach promoter
			/*
			* Merge the data collected for the project with the data for all projects
			*/
			$promoterFinal = array_merge($promoterFinal, $promoterData);
			$promoterData = array();
			$proj_loop_count++;
		}
		//print_r($promoterFinal);
		/*///////////////////////////////////////////////////////////////
		 * Load in the data view, all information regarding it is complete
		 */
		//	$this->load->view('data.php', $tags);
		///////////////////////////////////////////////////////////////
		
		/*///////////////////////////////////////////////////////////////
		 * Terminate Bad Searches
		 */
		if($tags['notLoggedIn'] == true){
			echo "NOTLOGGEDIN";
			return;
		} 	
		if($tags['queryFail']   == true){
			echo "QUERYFAILED";
			return;
		}
		if($tags['noSearch']    == true){
			echo "NOSEARCH";
			return;
		}
		///////////////////////////////////////////////////////////////
		
		
		// AFTER THIS POINT IN CODE: a valid search should be confirmed
		
		
		/*///////////////////////////////////////////////////////////////
		 * Set up the pictures
		 */
		$this->Data_Model->setPictures($promoterFinal, FALSE);
		///////////////////////////////////////////////////////////////
		
		/*///////////////////////////////////////////////////////////////
		 * Load up the data
		 */
		$loadData['promoterData'] = $promoterFinal;
		$loadData['userId'] = $queryData['userId'];
		$loadData['projectId'] = $queryData['projectId'];
		///////////////////////////////////////////////////////////////
		
		
		
		
		/*///////////////////////////////////////////////////////////////
		 * Display View Results
		 */
		 
		echo json_encode($promoterFinal);
		//$this->load->view('results.php', $loadData)                    ;
		///////////////////////////////////////////////////////////////
	
	
	
	
	
	
	
	
	}
	
	
	
	
	
	public function index(){
		$this->load->helper('url');
		$this->load->helper('html');
		$this->load->model('Data_Model');
		$this->load->model('Config_Model');
		$this->Config_Model->checkLogin();
		$this->load->view('data.php');
	} // end function index
	

	/*
	 * Adds an Interaction matrix to the data passed to it. Needs a
	 * plate number in order to work
	 */ 
	private function setInteractions(&$da, $pnum = -1)
	{
		foreach($da as &$id)
		{
			/*
			 * if pnum is -1, set the matrix for all 3 plate numbers
			 * otherwise just set the one plate number to save
			 * computation
			 */
			if($pnum == -1)
			{
				$interaction = new Interaction($id->pictures[0]);
				$id->interaction_matrix[0] = $interaction->getMatrix();
				$interaction = new Interaction($id->pictures[1]);
				$id->interaction_matrix[1] = $interaction->getMatrix();
				$interaction = new Interaction($id->pictures[2]);
				$id->interaction_matrix[2] = $interaction->getMatrix();
			} else {
				$interaction = new Interaction($id->pictures[$pnum]);
				$id->interaction_matrix[$pnum] = $interaction->getMatrix();
			}
			
		} // end foreach $da
		unset($id);
		unset($da);
	} // end function setInteractions

	public function getData(){
		$this->load->model('Data_Model');
		$this->setValues($this->input->post("transcriptor"), $this->input->post("promoter"));
		
		$data = $this->Data_Model->getData($this->getTranscriptor(0), $this->getPromoter(0));
		$bait = $this->getBait($data);
		$images = $this->Data_Model->getPictures($bait);
		$dataArray = $this->createDatatype($images, $data);
		
		echo json_encode($dataArray);
	}
	
	public function createDatatype($images, $data){
		$dataArray = array();
		
		$i = 0;
		foreach($images as $image){
			
			foreach($image as $im){
				$picture = "http://csbio.cs.umn.edu/UMassProject/dev/ci2/images/" . $im->image;
				$thumbnail = "http://csbio.cs.umn.edu/UMassProject/dev/ci2/thumbs/" . $im->image . "._thumb.JPG";
				
				$interaction = new Interaction($im->image);
				$matrix = $interaction->getMatrix();
				
				$datatype = new Datatype($picture, $thumbnail, $data[$i], $matrix);
				array_push($dataArray, $datatype);
			}
			$i++;
		}
		
		return $dataArray;
	}
	public function getBait($data){
		
		$bait = array();
		foreach($data as $line){
			array_push($bait, $line->idbait);
		}
		
		return $bait;
	}
	////// getIntensityData //////
	// gets intensity data from an ajax request
	//////
	public function getIntensityData(){
		$this->load->model('Data_Model');
		
		/*
		 * Grab the plateNumber, remember promoter only searches have -1
		 * as a platenumber and that needs to be handled. In promoter 
		 * only searches the platenumber is the index
		 */
		$promData     = $this->input->post("promData");
		$userId       = $this->input->post("user_id");
		$projectId    = $this->input->post("project_id");
		//print_r($promData);
		$plateNumber = $promData['plate_number'];
		
		$intensity_data = $this->Data_Model->getIntensityData($promData, $plateNumber, $userId, $projectId );
		echo json_encode($intensity_data);
	}
	
	////// updateModifiedCall
	// Performs a Human Call from an Ajax Request
	//////
	public function updateModifiedCall(){
		$this->load->model('Data_Model');
		


		$plateName = $this->input->post("plate_name");
		$plateNumber = $this->input->post("plate_number");
		$xCoord = $this->input->post("x_coord");
		$yCoord = $this->input->post("y_coord");
		$newCall = $this->input->post("new_val");
		$userId       = $this->input->post("user_id");
		$projectId    = $this->input->post("project_id");
		
		if(!($this->session->userdata('admin') == 1) && (strtoupper($this->session->userdata['username']) != strtoupper($userId))){
			echo "ERROR: Hello, you don't seem to be the original user for this data or this project. I'm only allowing the person who originally uploaded the data to edit the data for now.\n\nContact whoever uploaded the data or go bug the developers to make it so that the uploader can give permission to other people to edit data";
			return 0;
		} else {
		
			$table = $this->Data_Model->getInteractionTable();
			$data  = array('modified_call' => $newCall);
			$where = "plate_name = '$plateName' AND plate_number = '$plateNumber' AND x_coord = '$xCoord' AND y_coord = '$yCoord' AND user_id = '$userId' AND project_id = '$projectId'";
			
			//echo $where;
			$success = $this->Data_Model->update($table, $data, $where);

			
			
			
			echo "Modified Call\n";                                              
			if($success == True) echo "Hello, your modified call successfully updated. \n\nNew call in database is: $newCall\n\nNote: Refresh the page when you are done with the plate and double check your calls. \n\nYou can push OK and carry on now. \n\nDebug Vars: \nplateName = $plateName \nx_val = $xCoord \ny_val = $yCoord";
			if($success == False) echo "ERROR: Modified call could not update. \n I wouldn't trust the information displayed on the page anymore. \n\nDebug Query: $query";
		}
	}

	////// updateModifiedCall
	// Performs a Human Call from an Ajax Request
	//////
	public function updateModifiedCallEntirePlate(){
		$this->load->model('Data_Model');

		$plateName = $this->input->post("plate_name");
		$plateNumber = $this->input->post("plate_number");
		$newCall = $this->input->post("new_val");
		$userId       = $this->input->post("user_id");
		$projectId    = $this->input->post("project_id");
		
		if(!$this->session->userdata('admin')){
			if(strtoupper($this->session->userdata['username']) != strtoupper($userId)) {
				echo "Hello, you don't seem to be the original user for this data or this project. I'm only allowing the person who originally uploaded the data to edit the data for now.\n\nContact whoever uploaded the data or go bug the developers to make it so that the uploader can give permission to other people to edit data";
				return 0;
			}
		}
		
		$table = $this->Data_Model->getInteractionTable();
		$data  = array('modified_call' => $newCall);
		$where = "plate_name = '$plateName' AND plate_number = '$plateNumber' AND user_id = '$userId' AND project_id = '$projectId'";
		
		$success = $this->Data_Model->update($table, $data, $where);

		
		
		
		echo "Modified Call\n";                                              
		if($success == True) echo "Hello, your modified call for the entire plate successfully updated. \n\nNew call in database is: $newCall\n\nNote: Refresh the page when you are done with the plate and double check your calls. \n\nYou can push OK and carry on now. \n\nDebug Vars: \nplateName = $plateName";
		if($success == False) echo "Modified call could not update. \n I wouldn't trust the information displayed on the page anymore.";
	}
	
	/*
	*** SetPromotor, setTranscriptor
	* checks to make sure that the transcriptor or promotor does not 
	* equal the default value
	* 
	*** getPromotor, getTranscriptor
	* retrieve the promotor or the transcriptor
	**/

	
	
	public function setPromoter($promoter){
		if($promoter == ""){
			$this->promoter = array("");
		} else{
			$pattern = '/,(\s*)/';
			$this->promoter = $this->multi_explode( $pattern, trim($promoter) );
		} 
	}
	
	public function setTranscriptor($transcriptor){
		if($transcriptor == ""){
			$this->transcriptor = array("");
		} else{
			$pattern = '/,(\s*)/'; /// old : '/[,:\|\\\\\/\s]/'
			$this->transcriptor = $this->multi_explode( $pattern, trim($transcriptor) );
		} 
	}
	
	public function getPromoter($index){
		if( isset($this->promoter[$index]) ) return $this->promoter[$index];
		return "";
	}
	public function getTranscriptor($index){
		if( isset($this->transcriptor[$index]) ) return $this->transcriptor[$index];
		return "";
	}
	public function getPromoterArray(){
		return $this->promoter;
	}
	public function getTranscriptorArray(){
		return $this->transcriptor;
	}
	
	// function to explode on multiple delimiters
	public function multi_explode($pattern, $string, $standardDelimiter = ':')
	{
	    // replace delimiters with standard delimiter, also removing redundant delimiters
	    $string = trim(preg_replace(array($pattern, "/{$standardDelimiter}+/s"), $standardDelimiter, $string), ":");
	
	    // return the results of explode
	    return explode($standardDelimiter, $string);
	}
	
	public function array_extend($a, $b) {
	    foreach($b as $k=>$v) {
	        if( is_array($v) ) {
	            if( !isset($a[$k]) ) {
	                $a[$k] = $v;
	            } else {
	                $a[$k] = array_extend($a[$k], $v);
	            }
	        } else {
	            $a[$k] = $v;
	        }
	    }
	    return $a;
	}
}
