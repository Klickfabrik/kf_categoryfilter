<?php
if (!defined('TYPO3_MODE')) {
	die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::configurePlugin(
	'KfCategoryFilter.' . $_EXTKEY,
	'Pi1',
	array(
		'Categories' => 'list, list_current, select, isotope'
	),
	// non-cacheable actions
	array(
		'Categories' => '',
		
	)
);