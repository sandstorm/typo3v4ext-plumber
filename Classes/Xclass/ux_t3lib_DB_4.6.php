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
 * Class implements xclassing for t3lib_DB for TYPO3 version 4.6.
 *
 * This class enables profiling of sql SELECT queries which is not possible with the standard hooks
 * in t3lib_DB of TYPO3.
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package Xclass
 * @see
 */
class ux_t3lib_DB extends t3lib_DB {

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



	/**
	 * Executes query
	 * mysql_query() wrapper function
	 * Beware: Use of this method should be avoided as it is experimentally supported by DBAL. You should consider
	 *         using exec_SELECTquery() and similar methods instead.
	 *
	 * @param	string		Query to execute
	 * @return	pointer		Result pointer / DBAL object
	 */
	function sql_query($query) {
		// Added to log select queries
		foreach($this->preProcessHookObjects as $preProcessHookObject) { /* @var $preProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface */
			$preProcessHookObject->sql_query_preProcessAction($query);
		}

		$pointer = parent::sql_query($query);

		// Added to log select queries
		foreach($this->postProcessHookObjects as $postProcessHookObject) { /* @var $postProcessHookObject Tx_SandstormmediaPlumber_Hooks_DbPostProcessHookInterface */
			$postProcessHookObject->sql_query_postProcessAction($query);
		}

		return $pointer;
	}



	/**
	 * Connects to database for TYPO3 sites:
	 *
	 * @param string $host
	 * @param string $user
	 * @param string $password
	 * @param string $db
	 * @throws Exception if registered hooks are not implementing the respected interfaces
	 */
	function connectDB($host = TYPO3_db_host, $user = TYPO3_db_username, $password = TYPO3_db_password, $db = TYPO3_db) {
		parent::connectDB($host, $user, $password, $db);

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