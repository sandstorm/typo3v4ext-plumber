<?php

if (!defined ("TYPO3_MODE"))    die ("Access denied.");



// Determine, which x-class for t3lib_DB should be used for this T3 version.
$xClassFromEmSettings = __getXClassFromEmSettings();
if ($xClassFromEmSettings && __isT3LibDbXClassingActivated()) {
		$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'] = $xClassFromEmSettings;
} else {
	__includeXClassForCurrentT3Version();
}



// Hook to be called BEFORE TYPO3 starts site rendering (first possible hook to take)
if (!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'])) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'] = array();
}
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = 'EXT:sandstormmedia_plumber/Classes/Hooks/Hook.php:Tx_SandstormmediaPlumber_Hooks_Hook->preprocessRequest';



// Hooks for the t3lib_db calls (check whether x-classing is activated)
if (__isT3LibDbXClassingActivated()) {
	if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'])) {
		$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'] = array();
	}
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = 'EXT:sandstormmedia_plumber/Classes/Hooks/Hook.php:Tx_SandstormmediaPlumber_Hooks_Hook';
}



// Hook to be called AFTER TYPO3 finished site rendering (last possible hook to take)
if (!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'])) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'] = array();
}
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'EXT:sandstormmedia_plumber/Classes/Hooks/Hook.php:Tx_SandstormmediaPlumber_Hooks_Hook->endOfRequest';



/**************************************************************************************************************
 *
 * Helper methods
 *
 **************************************************************************************************************/



/**
 * Returns the current T3 version, as we have to include different xclasses depending on version
 * $t3version is a three part version number, eg '4.12.3' -> 4012003
 *
 * @return int
 * @throws Exception if TYPO3 version is below 4.5 (we do not support version prior to 4.5)
 */
function __getT3Version() {
	if (function_exists('t3lib_utility_VersionNumber::convertVersionNumberToInteger')) {
		$t3version = t3lib_utility_VersionNumber::convertVersionNumberToInteger(TYPO3_version);
	} else {
		$t3version = t3lib_div::int_from_ver(TYPO3_version);
	}

	if ($t3version < 4005000) { // We do not support T3 version < 4.5
		throw new Exception('This extension can not be installed with a version prior to 4.5 of TYPO3!', 1364212859);
	}

	return $t3version;
}



/**
 * Returns xclass given in EM configuration of this extension
 *
 * @return null|string
 */
function __getXClassFromEmSettings() {
	$plumberEmSettings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sandstormmedia_plumber']);
	if (!empty($plumberEmSettings['t3libDbXclass'])) {
		list($extension, $fileName) = explode('::', $plumberEmSettings['t3libDbXclass']);
		if (!empty($extension) && !empty($fileName)) {
			return t3lib_extMgm::extPath($extension) . $fileName;
		}
	}

	// No xclass configured --> NULL
	return NULL;
}



/**
 * Returns TRUE, if x-classing for t3lib_DB is activated
 *
 * @return bool
 */
function __isT3LibDbXClassingActivated() {
	$plumberEmSettings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sandstormmedia_plumber']);
	if (!empty($plumberEmSettings['t3libDbXclassingActivated'])) {
		if ($plumberEmSettings['t3libDbXclassingActivated']) {
			return TRUE;
		}
	}
	return FALSE;
}



/**
 * Includes proper xclass for extending t3lib_DB
 */
function __includeXClassForCurrentT3Version() {
	$t3version = __getT3Version();
	if ($t3version < 4006000) {  // Version < 4.6
		$xclass = t3lib_extMgm::extPath('sandstormmedia_plumber') . 'Classes/Xclass/ux_t3lib_DB_4.5.php';

		// Inclusion of required interfaces (those are not available in v 4.5)
		require_once( t3lib_extMgm::extPath('sandstormmedia_plumber') . 'Classes/Hooks/v4.5Compatibility/interface.t3lib_db_postprocessqueryhook.php');
		require_once( t3lib_extMgm::extPath('sandstormmedia_plumber') . 'Classes/Hooks/v4.5Compatibility/interface.t3lib_db_preprocessqueryhook.php');
	} else {   // Version >= 4.6
		$xclass = t3lib_extMgm::extPath('sandstormmedia_plumber') . 'Classes/Xclass/ux_t3lib_DB_4.6.php';

		// Inclusion of required interfaces (we probably have no auto-loading)
		require_once(PATH_site . 't3lib/interfaces/interface.t3lib_db_postprocessqueryhook.php');
		require_once(PATH_site . 't3lib/interfaces/interface.t3lib_db_preprocessqueryhook.php');
	}

	// Check whether x-classing is activated
	if (__isT3LibDbXClassingActivated()) {
		$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'] = $xclass;
	}
}