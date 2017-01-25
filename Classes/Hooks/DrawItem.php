<?php
namespace KfCategoryFilter\KfCategoryfilter\Hooks;

use TYPO3\CMS\Backend\View\PageLayoutView;
use TYPO3\CMS\Backend\View\PageLayoutViewDrawItemHookInterface;
use TYPO3\CMS\Core\Utility\GeneralUtility;

/**
 * Hook to render preview widget of custom content elements in page module
 * @see \TYPO3\CMS\Backend\View\PageLayoutView::tt_content_drawItem()
 */
class PageLayoutViewDrawItemHook implements PageLayoutViewDrawItemHookInterface {


    public $pluginTitle = "ContentFilter";
    public $pluginName  = "kfcategoryfilter_pi1";

    /**
     * Rendering for custom content elements
     *
     * @param PageLayoutView $parentObject
     * @param bool $drawItem
     * @param string $headerContent
     * @param string $itemContent
     * @param array $row
     */
    public function preProcess(PageLayoutView &$parentObject, &$drawItem, &$headerContent, &$itemContent, array &$row) {

        if(!empty($row['pi_flexform']) && $row['pi_flexform'] != null && $row['list_type'] == $this->pluginName){
            $drawItem = false;
            $headerContent = '<strong>Plugin: ' . $this->pluginTitle . '</strong><br />';

            // Sammelt die Flexform-Einstellungen und entfernt bestimmte Array-Keys ("data", "sDEF", "lDEF", "vDEF") zur besseren Nutzung in Fluid
            $search     = array("settings",".","switchableControllerActions");
            $replace    = array("","","actions");
            $flexform = $this->cleanUpArray(GeneralUtility::xml2array($row['pi_flexform']), array('data', 'sDEF', 'lDEF', 'vDEF'), array($search,$replace));

            // sort
            ksort($flexform);

            // Festlegen der Template-Datei
            /** @var \TYPO3\CMS\Fluid\View\StandaloneView $fluidTemplate */
            $fluidTmplFilePath = GeneralUtility::getFileAbsFileName('typo3conf/ext/kf_categoryfilter/Resources/Private/Templates/BackendTemplate.html');
            $fluidTmpl = GeneralUtility::makeInstance('TYPO3\CMS\Fluid\View\StandaloneView');
            $fluidTmpl->setTemplatePathAndFilename($fluidTmplFilePath);
            $fluidTmpl->assign('flex', $flexform);

            // Rendern
            $itemContent = $parentObject->linkEditContent($fluidTmpl->render(), $row);
        }
    }

    /**
     * @param array $cleanUpArray
     * @param array $notAllowed
     * @return array|mixed
     */
    public function cleanUpArray(array $cleanUpArray, array $notAllowed, array $removeKeyString) {
        $cleanArray = array();
        foreach ($cleanUpArray as $key => $value) {
            if(in_array($key, $notAllowed)) {
                return is_array($value) ? $this->cleanUpArray($value, $notAllowed, $removeKeyString) : $value;
            } else if(is_array($value)) {
                $curKey = str_replace($removeKeyString[0],$removeKeyString[1],$key);
                $cleanArray[$curKey] = $this->cleanUpArray($value, $notAllowed, $removeKeyString);
            }
        }
        return $cleanArray;
    }
}