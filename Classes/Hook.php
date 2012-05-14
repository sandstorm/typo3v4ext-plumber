<?php

class Tx_SandstormmediaPlumber_Hook implements t3lib_Singleton, t3lib_DB_preProcessQueryHook, t3lib_DB_postProcessQueryHook {
	protected $flow3BaseDirectory;

	protected $profileDirectory;

	protected $profiler;

	protected $run;

	public function __construct() {
		$_extConfig = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sandstormmedia_plumber']);
		$this->flow3BaseDirectory = $_extConfig['flow3Directory'];

		$this->profileDirectory = $this->flow3BaseDirectory . '/Data/Logs/Profiles';

		$mainIncludeFile = $this->flow3BaseDirectory . '/Packages/Application/SandstormMedia.PhpProfiler/main.php';
		if(file_exists($mainIncludeFile)) {
			require_once($mainIncludeFile);

			$this->run = new \SandstormMedia\PhpProfiler\Domain\Model\EmptyProfilingRun();
		}
	}

	public function preprocessRequest() {
		if ($this->run) {
			$profiler = \SandstormMedia\PhpProfiler\Profiler::getInstance();
			$profiler->setConfiguration('profilePath', $this->profileDirectory);
			$this->profiler = $profiler;
			$this->run = $profiler->start();

			require('TimeTrack.php');
			$GLOBALS['TT'] = new Tx_SandstormmediaPlumber_TimeTrack($this->run);
		}
	}

	public function endOfRequest() {
		if ($this->run) {
			$run = $this->profiler->stop();
			if ($run) {
				$this->profiler->save($run);
			}
		}
	}




	public function INSERTquery_preProcessAction(&$table, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->startTimer('DB: INSERT', array('Table' => $table, 'fields' => json_encode($fieldsValues)));
	}

	public function exec_INSERTquery_postProcessAction(&$table, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: INSERT');
	}

	public function INSERTmultipleRows_preProcessAction(&$table, array &$fields, array &$rows, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->startTimer('DB: INSERTmultipleRows', array('Table' => $table));
	}

	public function exec_INSERTmultipleRows_postProcessAction(&$table, array &$fields, array &$rows, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: INSERTmultipleRows');
	}

	public function UPDATEquery_preProcessAction(&$table, &$where, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->startTimer('DB: UPDATE', array('Table' => $table, 'where' => $where, 'fields' => json_encode($fieldsValues)));
	}

	public function exec_UPDATEquery_postProcessAction(&$table, &$where, array &$fieldsValues, &$noQuoteFields, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: UPDATE');
	}


	public function DELETEquery_preProcessAction(&$table, &$where, t3lib_DB $parentObject) {
		if ($this->run) $this->run->startTimer('DB: DELETE', array('Table' => $table));
	}

	public function exec_DELETEquery_postProcessAction(&$table, &$where, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: DELETE');
	}

	public function TRUNCATEquery_preProcessAction(&$table, t3lib_DB $parentObject) {
		if ($this->run) $this->run->startTimer('DB: TRUNCATE', array('Table' => $table));
	}

	public function exec_TRUNCATEquery_postProcessAction(&$table, t3lib_DB $parentObject) {
		if ($this->run) $this->run->stopTimer('DB: TRUNCATE');
	}
}

?>