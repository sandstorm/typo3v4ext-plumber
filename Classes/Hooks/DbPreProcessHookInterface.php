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
require_once(PATH_site . '/t3lib/interfaces/interface.t3lib_db_preprocessqueryhook.php');


/**
 * Interface for a pre process hook for data base queries.
 *
 * @author Michael Knoll <knoll@punkt.de>
 * @package Hooks
 */
interface Tx_SandstormmediaPlumber_Hooks_DbPreProcessHookInterface extends t3lib_DB_preProcessQueryHook {

	/**
	 * Pre-processor for the SELECTquery method.
	 *
	 * @param string $select_fields Fields to select
	 * @param string $from_table Table to select from
	 * @param string $where_clause Where clause for query
	 * @param string $groupBy Group by clause for query
	 * @param string $orderBy Order by clause for query
	 * @param string $limit Limit clause for query
	 * @param t3lib_DB $parentObject
	 * @return void
	 */
	public function exec_SELECTquery_preProcessAction(&$select_fields, &$from_table, &$where_clause, &$groupBy, &$orderBy, &$limit, t3lib_DB $parentObject);

}