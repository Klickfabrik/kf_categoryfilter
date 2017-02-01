<?php
if (!defined('TYPO3_MODE')) {
    die('Access denied.');
}

\TYPO3\CMS\Extbase\Utility\ExtensionUtility::registerPlugin(
    'KfCategoryFilter.' . $_EXTKEY,
    'Pi1',
    'kf Category Filter'
);


/**
 * Pluginnames
 */

$pluginSignature    = str_replace('_','',$_EXTKEY) . '_pi1';
$txpluginSignature  = 'tx_' . str_replace('_','',$_EXTKEY);

// flexform
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_addlist'][$pluginSignature] = 'pi_flexform';
$GLOBALS['TCA']['tt_content']['types']['list']['subtypes_excludelist'][$pluginSignature]='layout,select_key,pages,recursive';

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addPiFlexFormValue($pluginSignature, 'FILE:EXT:' . $_EXTKEY . '/Configuration/FlexForms/flexform_pi1.xml');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addStaticFile($_EXTKEY, 'Configuration/TypoScript', 'KF - Category Filter');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addLLrefForTCAdescr($txpluginSignature.'_domain_model_categories', 'EXT:kf_categoryfilter/Resources/Private/Language/locallang_csh_tx_kfcategoryfilter_domain_model_categories.xlf');
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::allowTableOnStandardPages($txpluginSignature.'_domain_model_categories');



/**
 * Add plugin to new element wizard
 */
if (TYPO3_MODE == 'BE') {
    $TBE_MODULES_EXT['xMOD_db_new_content_el']['addElClasses'][$txpluginSignature . '_wizicon'] =
        \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extPath($_EXTKEY) .
        'Wizicon/class.'.$txpluginSignature.'_wizicon.php';
}


// Hooks
$GLOBALS['TYPO3_CONF_VARS']['SC_OPTIONS']['cms/layout/class.tx_cms_layout.php']['tt_content_drawItem'][] = 'EXT:kf_categoryfilter/Classes/Hooks/DrawItem.php:KfCategoryFilter\\KfCategoryfilter\\Hooks\\PageLayoutViewDrawItemHook';


// add tca
$langFile = "LLL:EXT:kf_categoryfilter/Resources/Private/Language/locallang_db.xlf";

/**
 * add tab to pages
 * add field to pages
 */
$pageColumns = array(
    'kf_categoryfilter_text' => array(
        'exclude' => 0,
        'label' => "{$langFile}:pages.text", //'LLL:EXT:kf_categoryfilter/Resources/Private/Language/locallang_db.xlf:pages.text',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ),
    ),
    'kf_categoryfilter_hover_text' => array(
        'exclude' => 0,
        'label' => "{$langFile}:pages.hover_text", //'LLL:EXT:kf_categoryfilter/Resources/Private/Language/locallang_db.xlf:pages.hover_text',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ),
    ),
    'kf_categoryfilter_hover_color' => array(
        'exclude' => 0,
        'label' => "{$langFile}:pages.hover_color", //'LLL:EXT:kf_categoryfilter/Resources/Private/Language/locallang_db.xlf:pages.hover_color',
        'config' => array(
            'type' => 'input',
            'size' => 30,
            'eval' => 'trim'
        ),
    ),
);

$insertPageArray = array();
foreach ($pageColumns as $insertName => $insertValue){
    $insertPageArray[] = $insertName;
}

\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages',$pageColumns,1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages',"--div--;{$langFile}:pages.tabname,".join(",",$insertPageArray));

# multilanguage
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('pages_language_overlay',$pageColumns,1);
\TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('pages_language_overlay',"--div--;{$langFile}:pages.tabname,".join(",",$insertPageArray));



/**
 * add field to sys_category
 */
if (!isset($TCA['sys_category']['ctrl']['type'])) {
    $typeName = 'kf_categoryfilter_link';
    $tempColumns = array(
        $typeName => array(
            'exclude' => 0,
            'label' => "{$langFile}:tt_content.link",
            'config' => array(
                'type' => 'input',
                'size' => '30',
                'softref' => 'typolink',
                'wizards' => array(
                    '_PADDING' => 2,
                    'link' => array(
                        'type' => 'popup',
                        'title' => 'Link',
                        'icon' => 'actions-wizard-link',
                        'module' => array(
                            'name' => 'wizard_element_browser',
                            'urlParameters' => array(
                                'mode' => 'wizard'
                            ) ,
                        ) ,
                        'JSopenParams' => 'height=600,width=500,status=0,menubar=0,scrollbars=1'
                    )
                )
            )
        )
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $tempColumns, 1);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_category', $typeName);
    $TCA['sys_category']['ctrl']['type'] = $typeName;


    $typeName = 'kf_categoryfilter_class';
    $tempColumns = array(
        $typeName => array(
            'exclude' => 0,
            'label' => "{$langFile}:tt_content.class",
            'config' => array(
                'type' => 'input',
                'size' => '30',
            )
        )
    );
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addTCAcolumns('sys_category', $tempColumns, 1);
    \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::addToAllTCAtypes('sys_category', $typeName);
    $TCA['sys_category']['ctrl']['type'] = $typeName;
}
