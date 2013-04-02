<?php
/***************************************************************
*  Copyright notice
*
*  (c) 2013 Michael Knoll <knoll@punkt.de>, punkt.de GmbH
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


// We probably have no autoloading
require_once(PATH_site . 't3lib/class.t3lib_db.php');


/**
 * Class implements xclassing for t3lib_DB for TYPO3 version 4.5.
 *
 * This class enables some hooks in the database layer used for profiling.
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package Xclass
 * @see
 */
class ux_t3lib_DB extends t3lib_DB {

	/**
	 * Holds hooks for pre-query processing
	 *
	 * @var array
	 */
	protected $preProcessHookObjects = array();



	/**
	 * Holds hooks for post-query processing
	 *
	 * @var array
	 */
	protected $postProcessHookObjects = array();



	// Extending given methods to call the hooks


	/**
	 * Creates and executes a DELETE SQL-statement for $table where $where-clause
	 * Usage count/core: 40
	 *
	 * @param	string	$table	Database tablename
	 * @param	string	$where	WHERE clause, eg. "uid=1". NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself!
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function exec_DELETEquery($table, $where) {
		$res = parent::exec_DELETEquery($table, $where);
		foreach($this->postProcessHookObjects as $postProcessHookObject) { /* @var $postProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPostProcessHookInterface */
			$postProcessHookObject->exec_DELETEquery_postProcessAction($table, $where, $this);
		}
		return $res;
	}



	/**
	 * Creates and executes an INSERT SQL-statement for $table from the array with field/value pairs $fields_values.
	 * Using this function specifically allows us to handle BLOB and CLOB fields depending on DB
	 * Usage count/core: 47
	 *
	 * @param $table
	 * @param $fields_values
	 * @param bool $no_quote_fields
	 * @return    pointer        MySQL result pointer / DBAL object
	 */
	function exec_INSERTquery($table, $fields_values, $no_quote_fields = FALSE) {
		$res = parent::exec_INSERTquery($table, $fields_values, $no_quote_fields);
		foreach($this->postProcessHookObjects as $postProcessHookObject) { /* @var $postProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPostProcessHookInterface */
			$postProcessHookObject->exec_INSERTquery_postProcessAction($table, $fields_values, $no_quote_fields, $this);
		}
		return $res;
	}



	/**
	 * Truncates a table.
	 *
	 * @param	string		Database tablename
	 * @return	mixed		Result from handler
	 */
	public function exec_TRUNCATEquery($table) {
		$res = parent::exec_TRUNCATEquery($table);
		foreach($this->postProcessHookObjects as $postProcessHookObject) { /* @var $postProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPostProcessHookInterface */
			$postProcessHookObject->exec_TRUNCATEquery_postProcessAction($table, $this);
		}
		return $res;
	}



	/**
	 * Creates and executes an UPDATE SQL-statement for $table where $where-clause (typ. 'uid=...') from the array with field/value pairs $fields_values.
	 * Using this function specifically allow us to handle BLOB and CLOB fields depending on DB
	 * Usage count/core: 50
	 *
	 * @param    string $table, Database tablename
	 * @param    string $where WHERE clause, eg. "uid=1". NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself!
	 * @param    array $fields_values Field values as key=>value pairs. Values will be escaped internally. Typically you would fill an array like "$updateFields" with 'fieldname'=>'value' and pass it to this function as argument.
	 * @param bool $no_quote_fields  See fullQuoteArray()
	 * @return    pointer        MySQL result pointer / DBAL object
	 */
	function exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields = FALSE) {
		$res = parent::exec_UPDATEquery($table, $where, $fields_values, $no_quote_fields);
		foreach($this->postProcessHookObjects as $postProcessHookObject) { /* @var $postProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPostProcessHookInterface */
			$postProcessHookObject->exec_UPDATEquery_postProcessAction($table, $where, $fields_values, $no_quote_fields, $this);
		}
		return $res;
	}




	/**
	 * Creates an UPDATE SQL-statement for $table where $where-clause (typ. 'uid=...') from the array with field/value pairs $fields_values.
	 * Usage count/core: 6
	 *
	 * @param	string	$table	See exec_UPDATEquery()
	 * @param	string	$where	See exec_UPDATEquery()
	 * @param	array	$fields_values	See exec_UPDATEquery()
	 * @param	boolean	$no_quote_fields	See fullQuoteArray()
	 * @return	string Full SQL query for UPDATE
	 */
	function UPDATEquery($table, $where, $fields_values, $no_quote_fields = FALSE) {
		foreach($this->preProcessHookObjects as $preProcessHookObject) { /* @var $preProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface */
			$preProcessHookObject->UPDATEquery_preProcessAction($table, $where, $fields_values, $no_quote_fields, $this);
		}
		return parent::UPDATEquery($table, $where, $fields_values, $no_quote_fields);
	}



	/**
	 * Creates a DELETE SQL-statement for $table where $where-clause
	 * Usage count/core: 3
	 *
	 * @param	string	$table	See exec_DELETEquery()
	 * @param	string	$where	See exec_DELETEquery()
	 * @return	string		Full SQL query for DELETE
	 */
	function DELETEquery($table, $where) {
		foreach($this->preProcessHookObjects as $preProcessHookObject) { /* @var $preProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface */
			$preProcessHookObject->DELETEquery_preProcessAction($table, $where, $this);
		}
		return parent::DELETEquery($table, $where);
	}



	/**
	 * Creates an INSERT SQL-statement for $table from the array with field/value pairs $fields_values.
	 * Usage count/core: 4
	 *
	 * @param	string	$table	See exec_INSERTquery()
	 * @param	array	$fields_values	See exec_INSERTquery()
	 * @param	boolean	$no_quote_fields	See fullQuoteArray()
	 * @return	string		Full SQL query for INSERT (unless $fields_values does not contain any elements in which case it will be false)
	 */
	function INSERTquery($table, $fields_values, $no_quote_fields = FALSE) {
		foreach($this->preProcessHookObjects as $preProcessHookObject) { /* @var $preProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface */
			$preProcessHookObject->INSERTquery_preProcessAction($table, $fields_values, $no_quote_fields, $this);
		}
		return parent::INSERTquery($table, $fields_values, $no_quote_fields);
	}



	/**
	 * Creates a TRUNCATE TABLE SQL-statement
	 *
	 * @param	string		See exec_TRUNCATEquery()
	 * @return	string		Full SQL query for TRUNCATE TABLE
	 */
	public function TRUNCATEquery($table) {
		foreach($this->preProcessHookObjects as $preProcessHookObject) { /* @var $preProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface */
			$preProcessHookObject->TRUNCATEquery_preProcessAction($table, $this);
		}
		return parent::TRUNCATEquery($table);
	}



	/**
	 * Creates and executes a SELECT SQL-statement
	 * Using this function specifically allow us to handle the LIMIT feature independently of DB.
	 *
	 * @param	string		List of fields to select from the table. This is what comes right after "SELECT ...". Required value.
	 * @param	string		Table(s) from which to select. This is what comes right after "FROM ...". Required value.
	 * @param	string		additional WHERE clauses put in the end of the query. NOTICE: You must escape values in this argument with $this->fullQuoteStr() yourself! DO NOT PUT IN GROUP BY, ORDER BY or LIMIT!
	 * @param	string		Optional GROUP BY field(s), if none, supply blank string.
	 * @param	string		Optional ORDER BY field(s), if none, supply blank string.
	 * @param	string		Optional LIMIT value ([begin,]max), if none, supply blank string.
	 * @return	pointer		MySQL result pointer / DBAL object
	 */
	function exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy = '', $orderBy = '', $limit = '') {
		// Added to log select queries
		foreach($this->preProcessHookObjects as $preProcessHookObject) { /* @var $preProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface */
			$preProcessHookObject->exec_SELECTquery_preProcessAction($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit, $this);
		}

		$res = parent::exec_SELECTquery($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit);

		// Added to log select queries
		foreach($this->postProcessHookObjects as $postProcessHookObject) { /* @var $postProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPostProcessHookInterface */
			$postProcessHookObject->exec_SELECTquery_postProcessAction($select_fields, $from_table, $where_clause, $groupBy, $orderBy, $limit, $this);
		}

		return $res;
	}



	// Overwriting methods to set up the hook classes

	/**
	 * Connects to database for TYPO3 sites:
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param string $db
	 * @throws UnexpectedValueException
	 * @throws Exception if a hook does not implement the required interface
	 */
	function connectDB($host = TYPO3_db_host, $user = TYPO3_db_username, $password = TYPO3_db_password, $db = TYPO3_db) {
		parent::connectDB($host, $user, $password, $db);

		// Prepare user defined objects (if any) for hooks which extend query methods
		$this->preProcessHookObjects = array();
		$this->postProcessHookObjects = array();
		if (is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'])) {
			foreach ($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'] as $classRef) {
				$hookObject = t3lib_div::getUserObj($classRef);

				if (!($hookObject instanceof t3lib_DB_preProcessQueryHook || $hookObject instanceof t3lib_DB_postProcessQueryHook)) {
					throw new UnexpectedValueException('$hookObject must either implement interface t3lib_DB_preProcessQueryHook or interface t3lib_DB_postProcessQueryHook', 1299158548);
				}
				if ($hookObject instanceof t3lib_DB_preProcessQueryHook) {
					$this->preProcessHookObjects[] = $hookObject;
				}
				if ($hookObject instanceof t3lib_DB_postProcessQueryHook) {
					$this->postProcessHookObjects[] = $hookObject;
				}
			}
		}

		// We check, that we have our own interface that brings the hooks for select queries
		foreach ($this->preProcessHookObjects as $preProcessHookObject) {
			if (!$preProcessHookObject instanceof Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface) {
				throw new Exception('The registered hook ' . get_class($preProcessHookObject) . ' must implement the interface Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface', 1363902554);
			}
		}
		foreach ($this->postProcessHookObjects as $postProcessHookObject) {
			if (!$preProcessHookObject instanceof Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface) {
				throw new Exception('The registered hook ' . get_class($preProcessHookObject) . ' must implement the interface Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface', 1363902554);
			}
		}
	}

}