<?php

/***************************************************************
 *  Copyright notice
 *
 *  (c) 2013 Sebastian Kurfürst,
 *           Michael Knoll <knoll@punkt.de>, punkt.de GmbH
 *
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 2 of the License, or
 *  (at your option) any later version.
 *
 *  The GNU General Public License can be found at
 *  http://www.gnu.org/copyleft/gpl.html.
 *
 *  This script is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  This copyright notice MUST APPEAR in all copies of the script!
 ***************************************************************/

// We have to do this manually, as auto-loading is probably not available
require_once('DbPostProcessHookInterface.php');
require_once('DbPreProcessHookInterface.php');



/**
 * Class implements a hook for logging profiler data with xhprof.
 *
 * @author Sebastian Kurfürst
 * @author Michael Knoll <knoll@punkt.de>
 */
class Tx_SandstormmediaPlumber_Hooks_Hook implements t3lib_Singleton, Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface, Tx_SandstormmediaPlumber_Hooks_DbPostProcessHookInterface {

	/**
	 * Instance of profiler
	 *
	 * @var \Sandstorm\PhpProfiler\Profiler
	 */
	protected $profiler;



	/**
	 * Current profiler run
	 *
	 * @var Sandstorm\PhpProfiler\Domain\Model\EmptyProfilingRun
	 */
	protected $run;



	/**
	 * Holds current mysql process list as string. At the beginning, this is NULL. The first request
	 * to be profiled will query this value and set it.
	 *
	 * @var string
	 */
	protected $processList = NULL;



	/**
	 * Holds number of MySQL processes
	 *
	 * @var int
	 */
	protected $numOfProcesses = 0;



	public function __construct() {
		$_extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sandstormmedia_plumber']);

		$samplingRate = 1;
		if (isset($_extConfig['samplingRate'])) {
			$samplingRate = floatval($_extConfig['samplingRate']);
		}

		$currentSampleValue = mt_rand() / mt_getrandmax();

		if (isset($_extConfig['profileDirectory']) && $currentSampleValue <= $samplingRate) {
			require_once(__DIR__ . '/../../Resources/Private/PHP/PhpProfiler/main.php');
			require_once(__DIR__ . '/../TimeTrack.php');

			$this->profiler = \Sandstorm\PhpProfiler\Profiler::getInstance();
			$this->profiler->setConfigurationProvider(function() use ($_extConfig) {
				return array('plumber' => array('profilePath' => $_extConfig['profileDirectory']));
			});
			$this->run = new \Sandstorm\PhpProfiler\Domain\Model\EmptyProfilingRun();
		}
	}



	/**
	 * Method to be called before TYPO3 site rendering starts.
	 *
	 * This method starts xhprof profiling.
	 */
	public function preprocessRequest() {
		if ($this->run) {
			$this->run = $this->profiler->start();
			$this->run->setOption('requestUri', t3lib_div::getIndpEnv('REQUEST_URI'));

			$GLOBALS['TT'] = new Tx_SandstormmediaPlumber_TimeTrack($this->run);
		}
	}



	/**
	 * Method to be called after TYPO3 site rendering is finished.
	 *
	 * This method stops and saves xhprof profiling.
	 */
	public function endOfRequest() {
		if ($this->run) {
			$run = $this->profiler->stop();
			if ($run) {
				$this->profiler->save($run);
			}
		}
	}



	/************************************************************************************************************************
	 * Methods for database profiling
	 ************************************************************************************************************************/

	public function INSERTquery_preProcessAction(&$table, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		// TODO think about how we get actual query in here
		if ($this->run) $this->run->startTimer('DB: INSERT', array('Query' => 'INSERT INTO ' . $table, 'Table' => $table, 'fields' => json_encode($fieldsValues)));
	}



	public function exec_INSERTquery_postProcessAction(&$table, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: INSERT');
	}



	public function INSERTmultipleRows_preProcessAction(&$table, array &$fields, array &$rows, &$noQuoteFields, t3lib_DB $parentObject) {
		// TODO think about how we get actual query in here
		if ($this->run) $this->run->startTimer('DB: INSERTmultipleRows', array('Table' => $table));
	}



	public function exec_INSERTmultipleRows_postProcessAction(&$table, array &$fields, array &$rows, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: INSERTmultipleRows');
	}



	public function UPDATEquery_preProcessAction(&$table, &$where, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		// TODO think about how we get actual query in here
		if ($this->run) $this->run->startTimer('DB: UPDATE', array('Table' => $table, 'where' => $where, 'fields' => json_encode($fieldsValues)));
	}



	public function exec_UPDATEquery_postProcessAction(&$table, &$where, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: UPDATE');
	}



	public function DELETEquery_preProcessAction(&$table, &$where, t3lib_DB $parentObject) {
		// TODO think about how we get actual query in here
		if ($this->run) $this->run->startTimer('DB: DELETE', array('Table' => $table));
	}



	public function exec_DELETEquery_postProcessAction(&$table, &$where, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: DELETE');
	}



	public function TRUNCATEquery_preProcessAction(&$table, t3lib_DB $parentObject) {
		// TODO think about how we get actual query in here
		if ($this->run) $this->run->startTimer('DB: TRUNCATE', array('Table' => $table));
	}



	public function exec_TRUNCATEquery_postProcessAction(&$table, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: TRUNCATE');
	}



	public function exec_SELECTquery_preProcessAction(&$select_fields, &$from_table, &$where_clause, &$groupBy, &$orderBy, &$limit, t3lib_DB $parentObject) {
		if ($this->run) {

			// Log some MySQL statistics -- IF NOT DONE YET
			if ($this->processList === NULL) {
				// TODO make this configurable in EM settings
				$this->run->setOption('mysql_ProcessList', $this->getMySqlProcessList());
				$this->run->setOption('mysql_Processes', print_r($this->numOfProcesses, true)); // If we set this as integer, it won't be displayed in frontend!
				$this->run->setOption('mysql_Status', $this->getMySqlStatus());
			}

			// Log runtime of current query
			$this->run->startTimer('DB: SELECT', array('Query' => $parentObject->SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit)));

		}
	}



	public function exec_SELECTquery_postProcessAction(&$select_fields, &$from_table, &$where_clause, &$groupBy, &$orderBy, &$limit, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: SELECT');
	}



	public function sql_query_postProcessAction($query) {
		if ($this->run) $this->run->startTimer('DB: SELECT', array('Query' => $query));
	}



	public function sql_query_preProcessAction($query) {
		if ($this->run) $this->run->stopTimer('DB: SELECT');
	}



	/************************************************************************************************************************
	 * Private methods
	 ************************************************************************************************************************/

	private function getMySqlProcessList() {
		$result = mysql_query('SHOW FULL PROCESSLIST', $GLOBALS['TYPO3_DB']->link);
		$processList = '';
		$numOfProcesses = 0;
		while ($row = mysql_fetch_assoc($result)){
			$processList .= sprintf("%s %s %s %s %s %s\n", $row["Id"], $row["Host"], $row["db"], $row["Command"], $row["Time"], $row["Info"]);
			$numOfProcesses++;
		}
		mysql_free_result($result);
		$this->processList = $processList;
		$this->numOfProcesses = $numOfProcesses;
		return $processList;
	}



	private function getMySqlStatus() {
		$result = mysql_query('SHOW STATUS', $GLOBALS['TYPO3_DB']->link);
		$status = '';
		while ($row = mysql_fetch_assoc($result)) {
			$status .= $row['Variable_name'] . ' = ' . $row['Value'] . "\n";
		}
		return $status;
	}
}