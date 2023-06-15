<?php
require_once(__DIR__ . '/../BaseRepository.php');

class RepositoryHelpers
{
    /**
     * Reorders the items in an ORM result array from a DB query by their IDs
     * to match the order of an array with id values
     * $reOrderIndexes = true - Reorders the indexes/keys of the returned array
     * @param array $rowSet
     * @param array $values
     * @param bool $reOrderIndexes
     * @return array|false
     */
    public static function sortResultsSetByArrayValues(string $key, array $rowSet, array $values, ?bool $reOrderIndexes = true)
    {
        $results = uasort($rowSet, function ($a, $b) use ($values, $key) {
            if ($a instanceof ORM) {
                $aId = (int)$a->get($key);
            } else {
                $aId = (isset($a[$key]))? (int)$a[$key] : 0;
            }
            $resAId = array_search($aId, $values);
            if ($a instanceof ORM) {
                $bId = (int)$b->get($key);
            } else {
                $bId = (isset($b[$key]))? (int)$b[$key] : 0;
            }
            $resBId = array_search($bId, $values);
            if ($resAId == $resBId) {
                return 0;
            }
            return ($resAId < $resBId) ? -1 : 1;
        });
        if (!$results) {
            return false;
        }
        if ($reOrderIndexes) {
            return array_values($rowSet);
        }
        return $rowSet;
    }

    /**
     * Record a log of admin and customer service actions
     *
     * @param $action
     * @return void
     */
    public static function recordLog($action)
    {


        // attempts to get the IP of the current user
        if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
        } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
        } else {
            $ip = $_SERVER['REMOTE_ADDR'];
        }

        $item = ORM::for_table("blumeLogs")->create();

        $item->userID = CUR_ID;
        $item->set_expr("dateTime", "NOW()");
        $item->ip = $ip;
        $item->action = $action;

        $item->save();

    }

    public static function buildOrmResultsValueArray(string $column, array $ormResults = [], ?bool $isInteger = false) {

        $filterResults = array_filter($ormResults, function(ORM $row) use($column) {
            return $row->get($column);
        });

        return array_map(function (ORM $row) use($column, $isInteger) {
            if ($isInteger) {
                return (int)$row->get($column);
            }
            return $row->get($column);
        }, $filterResults);
    }

    public static function errorLog($data) {
        $encodeData = json_encode($data);
        $date = date('Y-m-d H:i:s', time());
        error_log("{$date}: {$encodeData}" . PHP_EOL);
    }

    public static function createSlug($text)
    {
        // replace non letter or digits by -
        $text = preg_replace('~[^\pL\d]+~u', '-', $text);

        // remove unwanted characters
        $text = preg_replace('~[^-\w]+~', '', $text);

        // trim
        $text = trim($text, '-');

        // remove duplicate -
        $text = preg_replace('~-+~', '-', $text);

        // lowercase
        $text = strtolower($text);

        if (empty($text)) {
            return 'n-a';
        }

        return $text;
    }

}