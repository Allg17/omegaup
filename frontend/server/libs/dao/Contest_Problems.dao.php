<?php

require_once("base/Contest_Problems.dao.base.php");
require_once("base/Contest_Problems.vo.base.php");
/** Page-level DocBlock .
  * 
  * @author alanboy
  * @package docs
  * 
  */
/** ContestProblems Data Access Object (DAO).
  * 
  * Esta clase contiene toda la manipulacion de bases de datos que se necesita para 
  * almacenar de forma permanente y recuperar instancias de objetos {@link ContestProblems }. 
  * @author alanboy
  * @access public
  * @package docs
  * 
  */
class ContestProblemsDAO extends ContestProblemsDAOBase
{
	/*
	 * 
	 * Get relevant problems including contest alias
	 */
	public static final function GetRelevantProblems($contest_id)
	{

		// Build SQL statement
		$sql = "SELECT Problems.problem_id, alias from Problems INNER JOIN ( SELECT Contest_Problems.problem_id from Contest_Problems WHERE ( Contest_Problems.contest_id = ? ) ) ProblemsContests ON Problems.problem_id = ProblemsContests.problem_id ";
		$val = array($contest_id);

		global $conn;
		$rs = $conn->Execute($sql, $val);

		$ar = array();
		foreach ($rs as $foo) {
			$bar =  new Problems($foo);
			array_push( $ar,$bar);
		}

		return $ar;
	}
}
