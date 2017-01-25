<?php
namespace KfCategoryFilter\KfCategoryfilter\Domain\Repository;


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
 * The repository for Categories
 */
//class CategoriesRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {
class CategoriesRepository extends \TYPO3\CMS\Extbase\Persistence\Repository {

    /**
     * The table name collections are stored to
     *
     * @var string
     */
    protected static $storageTableName = 'sys_category';


    /**
     * @param $categoryId
     * @return mixed
     */
    public function getCategoryData($categoryId,$options=array()){
        $table          = self::$storageTableName;
        $db             = self::getDatabaseConnection();
        $content        = array();

        // where query
        $select         = join(",",array(
            "uid",
            "title",
            "description",
            "parent",
            "items",
            "kf_categoryfilter_link",
            "kf_categoryfilter_class",
        ));
        $whereField     = (isset($options['recursive']) && $options['recursive'] == 1) ? "parent" : "uid";
        $where          = self::$storageTableName . ".{$whereField} = " . (int)$categoryId;

        $res = $db->exec_SELECTquery($select,$table,$where,'','','');
        while ($row = $db->sql_fetch_assoc($res)) {
            $content[] = $row;
        }

        return array(
            "result"    => $content,
            "query"     => $db->debug_lastBuiltQuery,
        );
    }

    /**
     * @param $array
     * @param $field
     * @return mixed|string
     */
    public function getField($array, $field){
        if(!is_array($array)){
            return "need array";
        }

        return $array[$field];
    }

    public function getPageUids($uid,$options){
        return self::searchPageWithCategory($uid,$options);
    }

    public function getPageContent($uid,$options){
        return self::searchFileWithPage($uid,$options);
    }

    public function getPageCategories($uid,$options){
        return self::searchPageCategories($uid,$options);
    }










    /**
     * @return mixed
     */
    protected static function getDatabaseConnection() {
        return $GLOBALS['TYPO3_DB'];
    }


    private function searchPageWithCategory($uid=null,$options=array()){
        $db             = self::getDatabaseConnection();
        $content        = Array();



        $select_fields  = "pages.uid as puid, pages.title, sys_category.uid as cuid";
        $from_table     = "pages";
        $from_table    .= " LEFT JOIN sys_category_record_mm ON pages.uid = sys_category_record_mm.uid_foreign JOIN sys_category ON sys_category.uid = sys_category_record_mm.uid_local";
        $where_clause   = "sys_category_record_mm.tablenames = 'pages' AND sys_category_record_mm.uid_local = {$uid}";
        $groupBy        = "";
        $orderByType    = isset($options['orderByType']) && !empty($options['orderByType']) ? "pages.{$options['orderByType']}" : 'pages.uid';
        $orderBy        = isset($options['orderBy']) && !empty($options['orderBy']) ? "{$orderByType} {$options['orderBy']}" : '';
        $limit          = isset($options['limit']) && !empty($options['limit']) ? $options['limit'] : '';

        $res = $db->exec_SELECTquery($select_fields,$from_table,$where_clause,$groupBy,$orderBy,$limit);
        while ($row = $db->sql_fetch_assoc($res)) {
            $content[] = $row;
        }

        return array(
            "result"    => $content,
            "query"     => $db->debug_lastBuiltQuery,
        );
    }

    private function searchFileWithPage($uid=null,$options=array()){
        $db             = self::getDatabaseConnection();
        $content        = Array();

        $select_fields  = "identifier,extension";
        $from_table     = "sys_file";
        $from_table    .= " LEFT JOIN sys_file_reference ON sys_file.uid = sys_file_reference.uid_local";
        $from_table    .= " JOIN pages ON pages.uid = sys_file_reference.uid_foreign";
        $groupBy        = "";
        $orderByType    = isset($options['orderByType']) && !empty($options['orderByType']) ? "pages.{$options['orderByType']}" : 'pages.uid';
        $orderBy        = isset($options['orderBy']) && !empty($options['orderBy']) ? "{$orderByType} {$options['orderBy']}" : '';
        $limit          = isset($options['limit']) && !empty($options['limit']) ? $options['limit'] : '';


        if(is_string($uid)){
            $uids[] = $uid;
        } else {
            $uids = $uid;
        }

        foreach ($uids as $id){
            $where_clause   = "pages.uid = {$id['puid']} AND pages.deleted = 0 AND pages.hidden = 0 AND sys_file_reference.tablenames = 'pages' ";

            $res = $db->exec_SELECTquery($select_fields,$from_table,$where_clause,$groupBy,$orderBy,$limit);
            while ($row = $db->sql_fetch_assoc($res)) {
                $content[] = array_merge($row,$id);
            }
        }

        return array(
            "result"    => $content,
            "query"     => $db->debug_lastBuiltQuery,
        );
    }
    private function searchPageCategories($uid=null,$options=array()){
        $db             = self::getDatabaseConnection();
        $content        = Array();

        $table_1        = "sys_category";
        $table_2        = "sys_category_record_mm";
        $table_3        = "pages";

        /*
        SELECT
            sys_category.title,
            sys_category.description,
            sys_category.parent,
            sys_category.uid,
            sys_category.kf_categoryfilter_link,
            sys_category.kf_categoryfilter_class
        FROM
            `sys_category`
        LEFT JOIN `sys_category_record_mm`
            ON(sys_category.uid = sys_category_record_mm.uid_local)
        JOIN `pages`
            ON pages.uid = sys_category_record_mm.uid_foreign
        WHERE
            sys_category_record_mm.uid_foreign = 1
            AND sys_category_record_mm.tablenames = 'pages'
            AND pages.deleted = 0 AND pages.hidden = 0
         */


        $uids   = is_string($uid) ? array($uid) : $uid;
        $search = join(",",$uids);

        $select_fields  = join(",",array(
            "{$table_1}.uid",
            "{$table_1}.title","{$table_1}.description",
            "{$table_1}.parent",
            "{$table_1}.kf_categoryfilter_link",
            "{$table_1}.kf_categoryfilter_class"
        ));
        $from_table     = "{$table_1}";
        $from_table    .= " LEFT JOIN {$table_2} ON {$table_1}.uid = {$table_2}.uid_local";
        $from_table    .= " JOIN {$table_3} ON {$table_3}.uid = {$table_2}.uid_foreign";
        $groupBy        = "";
        $orderByType    = isset($options['orderByType']) && !empty($options['orderByType']) ? "pages.{$options['orderByType']}" : 'pages.uid';
        $orderBy        = isset($options['orderBy']) && !empty($options['orderBy']) ? "{$orderByType} {$options['orderBy']}" : '';
        $limit          = isset($options['limit']) && !empty($options['limit']) ? $options['limit'] : '';


        $where_clause   = "{$table_2}.uid_foreign in ({$search}) ";
        $where_clause   .= "AND {$table_3}.deleted = 0 AND {$table_3}.hidden = 0  ";
        $where_clause   .= "AND {$table_2}.tablenames = 'pages' ";

        $res = $db->exec_SELECTquery($select_fields,$from_table,$where_clause,$groupBy,$orderBy,$limit);
        while ($row = $db->sql_fetch_assoc($res)) {
            $content[] = $row;
        }

        return array(
            "result"    => $content,
            "query"     => $db->debug_lastBuiltQuery,
        );
    }




    private function showArray($arr){
        echo '<pre>'.print_r($arr,true).'</pre>';
    }

}