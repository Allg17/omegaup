<?php

require_once '../ShowContest.php';

require_once 'NewContestsTest.php';
require_once 'NewProblemInContestTest.php';

require_once 'Utils.php';

class ShowContestTest extends PHPUnit_Framework_TestCase
{    
    public function setUp()
    {        
        Utils::ConnectToDB();
    }
    
    public function tearDown() 
    {
        Utils::cleanup();
    }
    
    public function testShowValidPubicContest()
    {
        // Create a clean contest and get the ID
        $contestCreator = new NewContestsTest();
        $contest_id = $contestCreator->testCreateValidContest(1);
        
        // Create 3 problems in our contest
        $problemCreator = new NewProblemInContestTest();
        $problem_id = array();
        $problem_id[0] = $problemCreator->testCreateValidProblem($contest_id);
        $problem_id[1] = $problemCreator->testCreateValidProblem($contest_id);
        $problem_id[2] = $problemCreator->testCreateValidProblem($contest_id);
        
        // Login as contestant
        $auth_token = Utils::LoginAsContestant();
        
        // Set contest
        $_GET["contest_id"] = $contest_id;
        Utils::SetAuthToken($auth_token);
        
        // Execute API
        $showContest = new ShowContest();
        try
        {
            $return_array = $showContest->ExecuteApi();
        }
        catch(ApiException $e)
        {
            var_dump($e->getArrayMessage());
            $this->fail("Unexpected exception");
        }
        
        // Get contest from DB to validate data       
        $contest = ContestsDAO::getByPK($contest_id);
        
        // Assert that we found our contest       
        $this->assertNotNull($contest);
        $this->assertNotNull($contest->getContestId());
        
        // Assert we are getting correct data
        $this->assertEquals($contest->getDescription(), $return_array["description"]);
        $this->assertEquals($contest->getStartTime(), $return_array["start_time"]);
        $this->assertEquals($contest->getFinishTime(), $return_array["finish_time"]);
        $this->assertEquals($contest->getWindowLength(), $return_array["window_length"]);        
        $this->assertEquals($contest->getToken(), $return_array["token"]);
        $this->assertEquals($contest->getPointsDecayFactor(), $return_array["points_decay_factor"]);
        $this->assertEquals($contest->getPartialScore(), $return_array["partial_score"]);
        $this->assertEquals($contest->getSubmissionsGap(), $return_array["submissions_gap"]);
        $this->assertEquals($contest->getFeedback(), $return_array["feedback"]);
        $this->assertEquals($contest->getPenalty(), $return_array["penalty"]);
        $this->assertEquals($contest->getScoreboard(), $return_array["scoreboard"]);
        $this->assertEquals($contest->getTimeStart(), $return_array["penalty_time_start"]);
        $this->assertEquals($contest->getPenaltyCalcPolicy(), $return_array["penalty_calc_policy"]);
        
        // Assert we have our problems
        $this->assertEquals(count($problem_id), count($return_array["problems"]));
        
        // Assert problem data
        $i = 0;
        foreach($return_array["problems"] as $problem_array)
        {                        
            // Get problem from DB            
            $problem = ProblemsDAO::getByPK($problem_id[$i]);            
            
            // Assert data in DB
            $this->assertEquals($problem->getTitle(), $problem_array["title"]);
            $this->assertEquals($problem->getAlias(), $problem_array["alias"]);
            $this->assertEquals($problem->getValidator(), $problem_array["validator"]);
            $this->assertEquals($problem->getTimeLimit(), $problem_array["time_limit"]);
            $this->assertEquals($problem->getMemoryLimit(), $problem_array["memory_limit"]);
            $this->assertEquals($problem->getVisits(), $problem_array["visits"]);
            $this->assertEquals($problem->getSubmissions(), $problem_array["submissions"]);
            $this->assertEquals($problem->getAccepted(), $problem_array["accepted"]);            
            $this->assertEquals($problem->getOrder(), $problem_array["order"]);
            
            // Get points of problem from Contest-Problem relationship
            $problemInContest = ContestProblemsDAO::getByPK($contest_id, $problem_id[$i]);
            $this->assertEquals($problemInContest->getPoints(), $problem_array["points"]);
                        
            $i++;
        }        
    }
    
    public function testShowValidPrivateContest()
    {
        // Create a clean contest and get the ID
        $contestCreator = new NewContestsTest();
        $contest_id = $contestCreator->testCreateValidContest(0);
        
        // Create 3 problems in our contest
        $problemCreator = new NewProblemInContestTest();
        $problem_id = array();
        $problem_id[0] = $problemCreator->testCreateValidProblem($contest_id);
        $problem_id[1] = $problemCreator->testCreateValidProblem($contest_id);
        $problem_id[2] = $problemCreator->testCreateValidProblem($contest_id);
        
        // Login as contestant
        $auth_token = Utils::LoginAsJudge();
        
        // Set contest
        $_GET["contest_id"] = $contest_id;
        Utils::SetAuthToken($auth_token);
        
        // Execute API
        $showContest = new ShowContest();
        try
        {
            $return_array = $showContest->ExecuteApi();
        }
        catch(ApiException $e)
        {
            var_dump($e->getArrayMessage());
            $this->fail("Unexpected exception");
        }
        
        // Get contest from DB to validate data       
        $contest = ContestsDAO::getByPK($contest_id);
        
        // Assert that we found our contest       
        $this->assertNotNull($contest);
        $this->assertNotNull($contest->getContestId());
        
        // Assert we are getting correct data
        $this->assertEquals($contest->getDescription(), $return_array["description"]);
        $this->assertEquals($contest->getStartTime(), $return_array["start_time"]);
        $this->assertEquals($contest->getFinishTime(), $return_array["finish_time"]);
        $this->assertEquals($contest->getWindowLength(), $return_array["window_length"]);        
        $this->assertEquals($contest->getToken(), $return_array["token"]);
        $this->assertEquals($contest->getPointsDecayFactor(), $return_array["points_decay_factor"]);
        $this->assertEquals($contest->getPartialScore(), $return_array["partial_score"]);
        $this->assertEquals($contest->getSubmissionsGap(), $return_array["submissions_gap"]);
        $this->assertEquals($contest->getFeedback(), $return_array["feedback"]);
        $this->assertEquals($contest->getPenalty(), $return_array["penalty"]);
        $this->assertEquals($contest->getScoreboard(), $return_array["scoreboard"]);
        $this->assertEquals($contest->getTimeStart(), $return_array["penalty_time_start"]);
        $this->assertEquals($contest->getPenaltyCalcPolicy(), $return_array["penalty_calc_policy"]);
        
        // Assert we have our problems
        $this->assertEquals(count($problem_id), count($return_array["problems"]));
        
        // Assert problem data
        $i = 0;
        foreach($return_array["problems"] as $problem_array)
        {                        
            // Get problem from DB            
            $problem = ProblemsDAO::getByPK($problem_id[$i]);            
            
            // Assert data in DB
            $this->assertEquals($problem->getTitle(), $problem_array["title"]);
            $this->assertEquals($problem->getAlias(), $problem_array["alias"]);
            $this->assertEquals($problem->getValidator(), $problem_array["validator"]);
            $this->assertEquals($problem->getTimeLimit(), $problem_array["time_limit"]);
            $this->assertEquals($problem->getMemoryLimit(), $problem_array["memory_limit"]);
            $this->assertEquals($problem->getVisits(), $problem_array["visits"]);
            $this->assertEquals($problem->getSubmissions(), $problem_array["submissions"]);
            $this->assertEquals($problem->getAccepted(), $problem_array["accepted"]);            
            $this->assertEquals($problem->getOrder(), $problem_array["order"]);
            
            // Get points of problem from Contest-Problem relationship
            $problemInContest = ContestProblemsDAO::getByPK($contest_id, $problem_id[$i]);
            $this->assertEquals($problemInContest->getPoints(), $problem_array["points"]);
                        
            $i++;
        }      
        
    }
    
    public function testShowInvalidPrivateContest()
    {
        // Create a clean contest and get the ID
        $contestCreator = new NewContestsTest();
        $contest_id = $contestCreator->testCreateValidContest(0);
        
        // Create 3 problems in our contest
        $problemCreator = new NewProblemInContestTest();
        $problem_id = array();
        $problem_id[0] = $problemCreator->testCreateValidProblem($contest_id);
        $problem_id[1] = $problemCreator->testCreateValidProblem($contest_id);
        $problem_id[2] = $problemCreator->testCreateValidProblem($contest_id);
        
        // Login as contestant
        $auth_token = Utils::LoginAsContestant();
        
        // Set contest
        $_GET["contest_id"] = $contest_id;
        Utils::SetAuthToken($auth_token);
        
        // Execute API
        $showContest = new ShowContest();
        try
        {
            $return_array = $showContest->ExecuteApi();
        }
        catch(ApiException $e)
        {
            // Validate error
            $exception_message = $e->getArrayMessage();            
            $this->assertEquals("User is not allowed to view this content.", $exception_message["error"]);
            $this->assertEquals("error", $exception_message["status"]);
            $this->assertEquals("HTTP/1.1 403 FORBIDDEN", $exception_message["header"]); 
            
            // We're OK
            return;
        }
        
        $this->fail("User was allowed to see private content.");
        var_dump($return_array);        
    }
                
}
?>