<?php

if (!defined ("TYPO3_MODE"))    die ("Access denied.");



// Check whether we want to xclass the t3lib_db class (which gives us more hooks)
$plumberEmSettings = unserialize($GLOBALS['TYPO3_CONF_VARS']['EXT']['extConf']['sandstormmedia_plumber']);
if (!empty($plumberEmSettings['t3libDbXclass'])) {
	list($extension, $fileName) = explode('::', $plumberEmSettings['t3libDbXclass']);
	if (!empty($extension) && !empty($fileName)) {
		$GLOBALS['TYPO3_CONF_VARS'][TYPO3_MODE]['XCLASS']['t3lib/class.t3lib_db.php'] = t3lib_extMgm::extPath($extension) . $fileName;
	}
}



// Hook to be called BEFORE TYPO3 starts site rendering (first possible hook to take)
if (!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'])) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'] = array();
}
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = 'EXT:sandstormmedia_plumber/Classes/Hooks/Hook.php:Tx_SandstormmediaPlumber_Hooks_Hook->preprocessRequest';



// Hooks for the t3lib_db calls
if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'] = array();
}
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = 'EXT:sandstormmedia_plumber/Classes/Hooks/Hook.php:Tx_SandstormmediaPlumber_Hooks_Hook';



// Hook to be called AFTER TYPO3 finished site rendering (last possible hook to take)
if (!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'])) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'] = array();
}
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'EXT:sandstormmedia_plumber/Classes/Hooks/Hook.php:Tx_SandstormmediaPlumber_Hooks_Hook->endOfRequest';