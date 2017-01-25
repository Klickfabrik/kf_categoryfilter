<?php
namespace KfCategoryFilter\KfCategoryfilter\Controller;

/***************************************************************
 *
 *  Copyright notice
 *
 *  (c) 2017 Marc Finnern <typo3@klickfabrik.net>, Klickfabrik
 *
 *  All rights reserved
 *
 *  This script is part of the TYPO3 project. The TYPO3 project is
 *  free software; you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation; either version 3 of the License, or
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

/**
 * CategoriesController
 */
class CategoriesController extends \TYPO3\CMS\Extbase\Mvc\Controller\ActionController
{

    /**
     * categoriesRepository
     *
     * @var \KfCategoryFilter\KfCategoryfilter\Domain\Repository\CategoriesRepository
     * @inject
     */
    protected $categoriesRepository = NULL;


    private $levelCategoryUids;
    private $currentPage;
    private $options;



    public function __getData(){
        $this->levelCategoryUids    = \TYPO3\CMS\Core\Utility\GeneralUtility::trimExplode(',', $this->settings['parentCategories']);
        $this->currentPage          = intval($GLOBALS['TSFE']->id);
        $this->options              = $this->settings;
    }

    public function __getCategoryData($levelUids,$newOptions = array()){
        $options    = !empty($newOptions) ? $newOptions : $this->options;

        if(!empty($levelUids[0]) && is_array($levelUids)) {
            $this->view->assign('levelCategoryUids', $levelUids);

            // loop
            $find       = array();
            foreach ($levelUids as $uid) {
                $getCats = $this->categoriesRepository->getCategoryData($uid,$options);
                if(!empty($getCats['result'])){
                    foreach ($getCats['result'] as $curData){
                        $find[] = array(
                            "catId"     => $uid,
                            "catTitle"  => $this->categoriesRepository->getField($curData, "title"),
                            "puid"      => $this->categoriesRepository->getField($curData, "kf_categoryfilter_link"),
                            "catParent" => $this->categoriesRepository->getField($curData, "parent")
                        );
                    }
                }
            }
            $this->view->assign('categories', $find);
        } else {
            $this->showArray($levelUids);
            $this->view->assign('error', "Please select something");
        }
    }

    /**
     * action list
     *
     * @return void
     */
    public function listAction() {
        self::__getData();

        // basics
        $levelUids      = $this->levelCategoryUids;

        // parse CategoryData
        self::__getCategoryData($levelUids);
    }

    /**
     * action list_current
     *
     * @return void
     */
    public function list_currentAction(){
        self::__getData();

        // basics
        $levelUids      = is_array($this->currentPage) ? $this->currentPage : array($this->currentPage);

        // set rekursiv default true
        $myOptions = $this->options;
        $myOptions['recursive'] = 1;

        // parse CategoryData
        $result = $this->categoriesRepository->getPageCategories($levelUids,$myOptions);
        if(!empty($result['result'][0]) && is_array($result['result'])) {
            // loop
            $find       = array();
            foreach ($result['result'] as $uid) {
                $find[] = array(
                    "catId"     => $uid['uid'],
                    "catTitle"  => $uid['title'],
                    "puid"      => $uid['kf_categoryfilter_link'],
                    "class"     => $uid['kf_categoryfilter_class'],
                    "catParent" => $uid['parent'],
                );
            }
            $this->view->assign('categories', $find);
        } else {
            $this->view->assign('error', "Please select something");
        }
    }
    
    /**
     * action select
     *
     * @return void
     */
    public function selectAction() {
        self::__getData();

        $find           = array();

        // basics
        $options        = $this->options;
        $levelUids      = $this->levelCategoryUids;
        $foundPages     = array();


        if(!empty($levelUids[0]) && is_array($levelUids)) {
            $this->view->assign('levelCategoryUids', $this->levelCategoryUids);

            // loop
            foreach ($this->levelCategoryUids as $uid) {
                $getCats = $this->categoriesRepository->getCategoryData($uid, $options);
                if (!empty($getCats['result'])) {
                    foreach ($getCats['result'] as $curData) {
                        $uid    = $this->categoriesRepository->getField($curData, "uid");
                        $result = $this->categoriesRepository->getPageUids($uid,$options);
                        if(!empty($result['result'])){

                            // skip double pages
                            foreach ($result['result'] as $page){
                                if(!in_array($page['puid'],$foundPages)){
                                    $foundPages[] = $page['puid'];
                                    $find[$uid] = $result;
                                }
                            }
                        }
                    }
                }
            }
            $this->view->assign('selectAction', $find);

        } else {
            $this->view->assign('error', "Please select something");
        }
    }


    public function isotopeAction(){
        self::__getData();


        // get Categories
        $findCategories = array();

        // get Pages
        $getPageUids    = array();
        $getPageCats    = array();

        // basics
        $options        = $this->settings;
        $levelUids      = $this->levelCategoryUids;
        $newLevelUids   = array();


        if(!empty($levelUids[0]) && is_array($levelUids)) {
            $this->view->assign('levelCategoryUids', $this->levelCategoryUids);

            // loop
            foreach ($levelUids as $uid) {
                $getCats = $this->categoriesRepository->getCategoryData($uid, $options);
                if (!empty($getCats['result'])) {
                    foreach ($getCats['result'] as $curData) {
                        $uid    = $this->categoriesRepository->getField($curData, "uid");
                        $title  = $this->categoriesRepository->getField($curData, "title");
                        $findCategories[$uid] = array(
                            "catId"         => $uid,
                            "catTitle"      => $this->categoriesRepository->getField($curData, "title"),
                            "puid"          => $this->categoriesRepository->getField($curData, "kf_categoryfilter_link"),
                            "class"         => $this->categoriesRepository->getField($curData, "kf_categoryfilter_class"),
                            "catParent"     => $this->categoriesRepository->getField($curData, "parent"),
                            "class_button"  => $this->sonderzeichen($title),
                        );

                        $newLevelUids[] = $uid;
                    }
                }
            }

            // get Pages
            foreach ($newLevelUids as $uid){
                $result = $this->categoriesRepository->getPageUids($uid,$options);
                if(isset($result['result']) && !empty($result['result'])){
                    $getPageUids[] = $result;

                    foreach ($result['result'] as $page){
                        $getPageCats[$page['puid']][] = $findCategories[$uid]['catTitle'];
                    }

                } else {
                    unset($findCategories[$uid]);
                }
            }


            // search unquie Pages
            if(!empty($getPageUids) && is_array($getPageUids)){
                $uniquePages = array(
                    "pages" => array(),
                    "data"  => array()
                );
                foreach ($getPageUids as $pageUid){
                    foreach ($pageUid['result'] as $page){
                        if(!in_array($page['puid'],$uniquePages['pages'])){
                            $uniquePages['pages'][] = $page['puid'];

                            $data = $page;
                            $data['catTitle'] = $findCategories[$page['cuid']]['catTitle'];
                            $data['class_button'] = $this->sonderzeichen($findCategories[$page['cuid']]['catTitle']);
                            $data['class_grid'] = $this->sonderzeichen(join(" ",$getPageCats[$page['puid']]));
                            $uniquePages['data'][] = $data;
                        }
                    }
                }
                $findPageContent = $this->categoriesRepository->getPageContent($uniquePages['data'],$options);

                $this->view->assign('selectAction', $findPageContent);
            }

            $this->view->assign('categories', $findCategories);
        } else {
            $this->view->assign('error', "Please select something");
        }
    }






    private function sonderzeichen($string) {
        $search = array("Ä", "Ö", "Ü", "ä", "ö", "ü", "ß", "´", " ");
        $replace = array("Ae", "Oe", "Ue", "ae", "oe", "ue", "ss", "", "_");
        return strtolower(str_replace($search, $replace, $string));
    }


    private function showArray($arr){
        echo '<pre>'.print_r($arr,true).'</pre>';
    }

}