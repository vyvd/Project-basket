<?php

require_once('helpers/RepositoryHelpers.php');

class BaseRepository
{
    const RESULT_FORMAT_RAW = 'raw';
    const RESULT_FORMAT_TO_ARRAY = 'to_array';

    const DATETIME_FORMAT = 'Y-m-d H:i:s';

    protected $repositoryHelpers;

    protected $dbName = DB_NAME;
    protected $tableName;
    protected $customTableName = null;

    protected $select = [];

    protected $where = null;

    protected $whereData = [];

    protected $joins = [];

    protected $groupBy = null;

    protected $orderBy = null;

    protected $limit = null;

    protected $offset = null;

    protected $query = null;

    protected $resultFormat = null;

    protected $orm;

    public function __construct()
    {
        $this->repositoryHelpers = new RepositoryHelpers();
        $this->setOrm(ORM::for_table($this->getTableName()));
    }

    public static function getDateTime(?string $format = null)
    {
        if (empty($format)) {
            $format = self::DATETIME_FORMAT;
        }
        return date($format, time());
    }

    public function fetchTablesByColumn(array $columns = [])
    {
        if (!count($columns)) {
            return false;
        }
        $columnsString = implode(
            ',',
            array_map(function ($col) {
                return "'{$col}'";
            }, $columns)
        );
        $query = "SELECT DISTINCT TABLE_NAME, COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE COLUMN_NAME IN ({$columnsString})
                      AND TABLE_SCHEMA='{$this->dbName}';";
        $results = $this->rawQueryFetch($query);
        if (!$results || !is_array($results)) {
            return false;
        }

        $buildRows = [];
        foreach ($results as $row) {
            if (!isset($row['TABLE_NAME']) || !isset($row['COLUMN_NAME'])) {
                continue;
            }
            $buildRows[$row['TABLE_NAME']] = $row['COLUMN_NAME'];
        }
        return $buildRows;
    }


    public function fetchTablesColumns(ORM $orm, array $destDbOptions, $connection = null)
    {
        $dbname = $this->dbName;
        if ($connection) {
            $dbname = $destDbOptions['db_name'];
        }
        $query = "SELECT DISTINCT TABLE_NAME, COLUMN_NAME
                    FROM INFORMATION_SCHEMA.COLUMNS
                    WHERE TABLE_SCHEMA='{$dbname}';";

        if ($connection) {
            $results = $orm::rawExecute($query, [], $connection);
        } else {
            $results = $orm::rawExecute($query);
        }
        if (!$results) {
            return false;
        }
        $statement = $orm::get_last_statement();
        $rows = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        if (!$rows || !count($rows)) {
            return false;
        }

        return $this->buildDbSchemaColumnData($rows);
    }

    private function buildDbSchemaColumnData(array $data)
    {
        $buildRows = [];
        foreach ($data as $row) {
            if (!isset($row['TABLE_NAME']) || !isset($row['COLUMN_NAME'])) {
                continue;
            }
            if (!array_key_exists($row['TABLE_NAME'], $buildRows)) {
                $buildRows[$row['TABLE_NAME']] = [];
            }
            $buildRows[$row['TABLE_NAME']][] = $row['COLUMN_NAME'];
        }
        return $buildRows;
    }

    public function compareDbs($connection, array $destDbOptions, $webOutput = false)
    {
        $siteOrm = ORM::for_table('accounts');
        $destDbOrm = ORM::for_table('accounts', $connection);
        $siteTableCols = $this->fetchTablesColumns($siteOrm, $destDbOptions);
        $destDbTableCols = $this->fetchTablesColumns($destDbOrm, $destDbOptions, $connection);
        if (!$siteTableCols || !$destDbTableCols) {
            return false;
        }
        $tableNameDifference = array_diff_key($siteTableCols, $destDbTableCols);
        $tableNameDifferenceReverse = array_diff_key($destDbTableCols, $siteTableCols);

        $colDifference = $this->findColumnDifferences($siteTableCols, $destDbTableCols);
        $colDifferenceReverse = $this->findColumnDifferences($destDbTableCols, $siteTableCols);
        $content = $this->dbCompareOutput(
            $tableNameDifference,
            'Table Differences',
            'These current database tables don\'t exist in destination database',
            $webOutput
        );
        $content .= $this->dbCompareOutput(
            $tableNameDifferenceReverse,
            null,
            'These destination database tables don\'t exist in current website database',
            $webOutput
        );
        $content .= $this->dbCompareOutput(
            $colDifference,
            "Table Column Differences",
            "These destination columns don't exist in current database",
            $webOutput
        );
        $content .= $this->dbCompareOutput(
            $colDifferenceReverse,
            null,
            "These current database columns don't exist in destination database",
            $webOutput
        );
        return $content;
    }

    private function dbCompareOutput(
        array $data,
        ?string $heading = null,
        ?string $subHeading = null,
        $webOutput = false
    ) {
        $content = '';
        if ($heading) {
            if ($webOutput) {
                $content .= "<h2>{$heading}</h2>";
            } else {
                $content .= "{$heading}" . PHP_EOL . PHP_EOL;
            }
        }
        if ($subHeading) {
            if ($webOutput) {
                $content .= "<h3>{$subHeading}</h3>";
            } else {
                $content .= "{$subHeading}" . PHP_EOL . PHP_EOL;
            }
        }
        if (!count($data)) {
            if ($webOutput) {
                $content .= "<p>No differences!</p>";
            } else {
                $content .= "No differences!" . PHP_EOL . PHP_EOL;
            }
            return $content;
        }
        if ($webOutput) {
            $content .= '<ul>';
        }
        foreach ($data as $table => $cols) {
            if ($webOutput) {
                $content .= '<li>';
                $content .= "<div>Table: {$table}</div>";
                echo sprintf('<div>Columns: %s</div>', implode(', ', $cols));
                $content .= '</li>';
            } else {
                $content .= '--------------------' . PHP_EOL;
                $content .= "Table: {$table}" . PHP_EOL;
                $content .= sprintf('Columns: %s', implode(', ', $cols)) . PHP_EOL;
                $content .= '--------------------' . PHP_EOL . PHP_EOL . PHP_EOL;
            }
        }
        if ($webOutput) {
            $content .= '</ul>';
        }
        return $content;
    }

    private function findColumnDifferences(array $sourceData, array $destData)
    {
        $colDifference = [];
        foreach ($sourceData as $table => $cols) {
            if (!in_array($table, array_keys($destData))) {
                continue;
            }
            $filterCols = array_filter($cols, function ($col) use ($table, $destData) {
                return (!in_array($col, $destData[$table]));
            }, ARRAY_FILTER_USE_BOTH);
            if (!count($filterCols)) {
                continue;
            }
            $colDifference[$table] = $filterCols;
        }
        return $colDifference;
    }

    public function rawQueryFetch($query)
    {
        $results = ORM::rawExecute($query);
        if (!$results) {
            return false;
        }
        $statement = ORM::get_last_statement();
        $rows = [];
        while ($row = $statement->fetch(PDO::FETCH_ASSOC)) {
            $rows[] = $row;
        }
        return $rows;
    }

    public function fetchAll(?array $columns = [])
    {
        return $this->fetchByParamsQuery(
            $columns
        )->findMany();
    }

    public function fetchCountByParams(array $columns, array $params, ?int $limit = null)
    {
        $query = $this->fetchByParamsQuery($columns, $params, $limit);
        return $query->count();
    }

    public function fetchByParamsQuery(array $columns, ?array $params = [], ?int $limit = null)
    {
        $query = ORM::for_table($this->buildTableName());
        foreach ($columns as $column) {
            $query->select($column);
        }
        foreach ($params as $column => $value) {
            $query->where($column, $value);
        };
        if (is_integer($limit)) {
            $query->limit($limit);
        }
        return $query;
    }

    private function buildJoinsQuery()
    {
        if (!count($this->joins)) {
            return false;
        }
        $joinsStringData = array_map(function ($join) {
            return sprintf(
                'LEFT JOIN %s ON %s.%s %s %s.%s',
                $join['join_table'],
                $join['join_table'],
                $join['join_table_column'],
                $join['constraint'],
                $join['right_table'],
                $join['right_table_column']
            );
        }, $this->joins);
        return implode(' ', $joinsStringData);
    }

    private function buildSelectQuery()
    {
        if (!count($this->select)) {
            return false;
        }
        $selectQuery = implode(', ', $this->select);
        return "SELECT {$selectQuery} FROM {$this->buildTableName()}";
    }

    private function buildWhereQuery()
    {
        if (!$this->where) {
            return false;
        }
        return "WHERE {$this->where}";
    }

    private function buildGroupByQuery()
    {
        if (!$this->groupBy) {
            return false;
        }
        return "GROUP BY {$this->groupBy}";
    }

    private function buildOrderByQuery()
    {
        if (!$this->orderBy) {
            return false;
        }
        return "ORDER BY {$this->orderBy}";
    }

    private function buildLimitOffsetQuery()
    {
        if (!is_integer($this->limit)) {
            return false;
        }
        $query = "LIMIT {$this->limit}";
        if (is_integer($this->offset)) {
            $query = "{$query} OFFSET {$this->offset}";
        }
        return $query;
    }

    public function fetchWithJoinsQuery()
    {
        $query = '';
        if ($select = $this->buildSelectQuery()) {
            $query .= $select . PHP_EOL;
        } else {
            return false;
        }

        if ($joins = $this->buildJoinsQuery()) {
            $query .= $joins . PHP_EOL;
        }

        if ($where = $this->buildWhereQuery()) {
            $query .= $where . PHP_EOL;
        }

        if ($groupBy = $this->buildGroupByQuery()) {
            $query .= $groupBy . PHP_EOL;
        }

        if ($orderBy = $this->buildOrderByQuery()) {
            $query .= $orderBy . PHP_EOL;
        }

        if ($limitOffset = $this->buildLimitOffsetQuery()) {
            $query .= $limitOffset;
        }
        $this->query = $query;
        return ORM::for_table($this->buildTableName())
            ->rawQuery($query, $this->whereData);
    }

    protected function buildResultsArray(array $results, array $fields)
    {
        return array_map(function (ORM $row) use ($fields) {
            $buildRow = [];
            foreach ($fields as $field) {
                $hasPeriod = strpos($field, '.');
                $hasAs = strpos($field, ' as ');
                if ($hasPeriod !== false && $hasAs !== false) {
                    $splitField = explode(' ', $field);
                    $field = $splitField[array_key_last($splitField)];
                } elseif ($hasPeriod !== false) {
                    $splitField = explode('.', $field);
                    $field = $splitField[array_key_last($splitField)];
                } elseif ($hasAs !== false) {
                    $splitField = explode(' ', $field);
                    $field = $splitField[array_key_last($splitField)];
                }
                $buildRow[$field] = $row->get($field);
            }
            return $buildRow;
        }, $results);
    }

    protected function resultsHandler($results, ?array $fields = [])
    {
        switch ($this->getResultFormat()) {
            case BaseRepository::RESULT_FORMAT_TO_ARRAY:
                return $this->buildResultsArray($results, $fields);
            case BaseRepository::RESULT_FORMAT_RAW:
            default:
                return $results;
        }
    }

    private function buildTableName()
    {
        $customTableName = $this->getCustomTableName();
        return (!$customTableName) ? $this->getTableName() : $customTableName;
    }

    public function cleanup()
    {
        $this->setCustomTableName(null);
        $this->setWhere(null);
        $this->setJoins([]);
        $this->setLimit(null);
        $this->setSelect([]);
        $this->setWhereData([]);
        $this->setGroupBy(null);
        $this->setOrderBy(null);
        $this->setOffset(null);
        $this->setResultFormat(self::RESULT_FORMAT_RAW);
    }

    /**
     * @return string
     */
    public function getTableName(): string
    {
        return $this->tableName;
    }

    /**
     * @return string|null
     */
    public function getCustomTableName(): ?string
    {
        return $this->customTableName;
    }

    /**
     * @param string|null $customTableName
     * @return BaseRepository
     */
    public function setCustomTableName(?string $customTableName): self
    {
        $this->customTableName = $customTableName;
        return $this;
    }

    /**
     * @param array $select
     * @return BaseRepository
     */
    public function setSelect(array $select): self
    {
        $this->select = $select;
        return $this;
    }

    /**
     * @param string|null $where
     * @return BaseRepository
     */
    public function setWhere(?string $where): self
    {
        $this->where = $where;
        return $this;
    }

    /**
     * @param array $whereData
     * @return BaseRepository
     */
    public function setWhereData(array $whereData): self
    {
        $this->whereData = $whereData;
        return $this;
    }

    /**
     * @param array $joins
     * @return BaseRepository
     */
    public function setJoins(array $joins): self
    {
        $this->joins = $joins;
        return $this;
    }

    /**
     * @param string|null $groupBy
     * @return BaseRepository
     */
    public function setGroupBy(?string $groupBy): self
    {
        $this->groupBy = $groupBy;
        return $this;
    }

    /**
     * @param string|null $orderBy
     * @return BaseRepository
     */
    public function setOrderBy(?string $orderBy): self
    {
        $this->orderBy = $orderBy;
        return $this;
    }

    /**
     * @param int|null $limit
     * @return BaseRepository
     */
    public function setLimit(?int $limit): self
    {
        $this->limit = $limit;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getOffset(): ?int
    {
        return $this->offset;
    }

    /**
     * @param int|null $offset
     * @return BaseRepository
     */
    public function setOffset(?int $offset): self
    {
        $this->offset = $offset;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getResultFormat(): ?string
    {
        if (!$this->resultFormat) {
            return self::RESULT_FORMAT_RAW;
        }
        return $this->resultFormat;
    }

    /**
     * @param string|null $resultFormat
     * @return BaseRepository
     */
    public function setResultFormat(?string $resultFormat): self
    {
        $this->resultFormat = $resultFormat;
        return $this;
    }

    /**
     * @return RepositoryHelpers
     */
    public function getRepositoryHelpers(): RepositoryHelpers
    {
        return $this->repositoryHelpers;
    }

    /**
     * @return null
     */
    public function getQuery()
    {
        return $this->query;
    }

    public function setOrm(ORM $orm): void
    {
        $this->orm = $orm;
    }

    public function getOrm(): ORM
    {
        return $this->orm;
    }
}