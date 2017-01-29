<?php

/*
 * This file is part of the TYPO3 CMS project.
 *
 * It is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License, either version 2
 * of the License, or any later version.
 *
 * For the full copyright and license information, please read the
 * LICENSE.txt file that was distributed with this source code.
 *
 * The TYPO3 project - inspiring people to share!
 */

/**
 * Wizard Icon for Backend
 */
class tx_kfcategoryfilter_wizicon {

    /**
     * Processing the wizard items array
     *
     * @param array $wizardItems The wizard items
     * @return array Modified array with wizard items
     */
    function proc($wizardItems)	 {

        // source file
        $lll = "LLL:EXT:kf_categoryfilter/Resources/Private/Language/locallang.xlf";

        $wizardItems['plugins_tx_kfcategoryfilter_pi1'] = array(
            'icon' => \TYPO3\CMS\Core\Utility\ExtensionManagementUtility::extRelPath('kf_categoryfilter') . 'Resources/Public/Icons/pi1.gif',
            'title' => $GLOBALS['LANG']->sL("{$lll}:kf_categoryfilter_wizard_title"),
            'description' => $GLOBALS['LANG']->sL("{$lll}:kf_categoryfilter_wizard_description"),
            'params' => '&defVals[tt_content][CType]=list&&defVals[tt_content][list_type]=kfcategoryfilter_pi1'
        );

        return $wizardItems;
    }

    private function showArray($arr){
        echo '<pre>'.print_r($arr,true).'</pre>';
    }
}