<?php
namespace Childish\query;


/**
 * MySqlProcessor
 *
 * @author    Pu ShaoWei <pushaowei520@gamil.com>
 * @date      2017/12/7
 * @package   Childish
 * @version   1.0
 */
class MySqlProcessor extends Processor
{
    /**
     * Process the results of a column listing query.
     *
     * @param  array  $results
     * @return array
     */
    public function processColumnListing($results)
    {
        return array_map(function ($result) {
            return ((object) $result)->column_name;
        }, $results);
    }
}