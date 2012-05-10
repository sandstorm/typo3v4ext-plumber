<?php
if (!defined ("TYPO3_MODE"))    die ("Access denied.");

if (!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'])) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'] = array();
}
$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/index_ts.php']['preprocessRequest'][] = 'EXT:sandstormmedia_plumber/Classes/Hook.php:Tx_SandstormmediaPlumber_Hook->preprocessRequest';

if (!is_array($TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'])) {
	$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'] = array();
}

$TYPO3_CONF_VARS['SC_OPTIONS']['tslib/class.tslib_fe.php']['hook_eofe'][] = 'EXT:sandstormmedia_plumber/Classes/Hook.php:Tx_SandstormmediaPlumber_Hook->endOfRequest';

if (!is_array($GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'])) {
	$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'] = array();
}
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['t3lib/class.t3lib_db.php']['queryProcessors'][] = 'EXT:sandstormmedia_plumber/Classes/Hook.php:Tx_SandstormmediaPlumber_Hook';
?>