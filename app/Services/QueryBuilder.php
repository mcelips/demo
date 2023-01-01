<?php

namespace App\Services;

use Exception;
use mysqli_result;
use mysqli_stmt;

trait QueryBuilder
{

    public    $ORDER_ASC          = 'ASC';
    public    $ORDER_DESC         = 'DESC';
    protected $select             = '*';
    protected $limit              = 0;
    protected $offset             = 0;
    protected $where              = [];
    protected $bindings           = [];
    protected $table              = null;
    protected $isWhereGroup       = false;
    protected $isWhereGroupFirst  = true;
    protected $whereGroupStartKey = '_wgs_';
    protected $whereGroupEndKey   = '_wge_';
    protected $orderBy            = [];
    protected $groupBy            = '';
    protected $having             = '';
    private   $connection         = 'default';
    private   $mysql_functions    = [
        'NOW()', 'SYSDATE()', 'CURRENT_TIMESTAMP()',
    ];

    /**
     * Выводит на экран SQL запрос и данные для него
     *
     * @return void
     */
    public function printSql($die = false)
    {
        $sql = $this->prepareSqlQuery(
            sprintf(
                'SELECT %s FROM %s %s %s %s %s %s',
                $this->select,
                static::getTableName(),
                $this->getSqlWhere(),
                $this->groupBy,
                $this->having,
                $this->getSqlOrderBy(),
                $this->getSqlLimit()
            )
        );

        $query_string = $this->getQueryString($sql);

        if ($die === true) {
            dd([$sql, $this->bindings, $query_string]);
        }
        dump([$sql, $this->bindings, $query_string, $this->connection_config]);
    }

    /**
     * Удаляет лишние пробелы и переносы из SQL запроса, заменяет _table_ на static::$table
     *
     * @param string $sql
     *
     * @return string
     * @throws Exception
     */
    protected function prepareSqlQuery($sql)
    {
        if ($this->table === null) {
            throw new Exception('Unknown table name');
        }

        $sql = str_replace('_table_', $this->escapeMysqliIdentifier($this->table), $sql);

        return trim(preg_replace('/[ \t\r\n]+/', ' ', $sql));
    }

    /**
     * Экранирование значения
     *
     * @param string $field
     *
     * @return string
     */
    protected function escapeMysqliIdentifier($field)
    {
        return "`" . str_replace("`", "``", $field) . "`";
    }

    /**
     * Возвращает обработанное имя таблицы
     *
     * @return string
     */
    protected function getTableName()
    {
        // экранируем имя таблицы
        return $this->escapeMysqliIdentifier($this->table);
    }

    /**
     * Формирует и возвращает SQL запрос для WHERE
     *
     * @return string
     */
    protected function getSqlWhere()
    {
        if (empty($this->where) === false) {
            // формируем SQL запрос
            $sql = 'WHERE ' . implode(' ', $this->where);

            // заменяем ключи начала и конца группировки на скобки
            $sql = str_replace($this->whereGroupStartKey, '(', $sql);

            return str_replace($this->whereGroupEndKey, ')', $sql);
        }

        return '';
    }

    /**
     * Формирует и возвращает SQL запрос для ORDER BY
     *
     * @return string
     */
    protected function getSqlOrderBy()
    {
        if (empty($this->orderBy) === false) {
            return 'ORDER BY ' . implode(', ', $this->orderBy);
        }

        return '';
    }

    /**
     * Формирует и возвращает SQL запрос для LIMIT и OFFSET
     *
     * @return string
     */
    protected function getSqlLimit()
    {
        $res = '';
        if ($this->limit > 0) {
            $res .= 'LIMIT ' . $this->limit;

            if ($this->offset > 0) {
                $res .= ' OFFSET ' . $this->offset;
            }
        }

        return $res;
    }

    /**
     * Собирает SQL запрос, добавляя привязки
     *
     * @param string $sql
     *
     * @return string
     */
    protected function getQueryString($sql)
    {
        $query_string = '';
        foreach (explode('?', $sql) as $key => $value) {
            if (
                empty($value) === false and
                isset($this->bindings[$key]) === true
            ) {
                $query_string .= $value . "'" . $this->mbEscape((string)$this->bindings[$key]) . "'";
            } else {
                $query_string .= $value;
            }
        }

        return $query_string;
    }

    /**
     * Экранирует специальные символы в строке для использования в SQL-выражении, используя текущий набор символов
     * соединения
     * Специальные символы: NUL (ASCII 0), \n, \r, \, ', ", ctrl-Z.
     *
     * @param string $string Исходная строка
     *
     * @return string
     *
     * @author Trevor Herselman
     */
    protected function mbEscape($string)
    {
        return preg_replace('~[\x00\x0A\x0D\x1A\x22\x27\x5C]~u', '\\\$0', $string);
    }

    /**
     * Выполняет SQL запрос
     * Если в запросе вместо названия таблицы прописать _table_, то название впишется автоматически на основе
     * static::$table
     *
     * @param string $sql
     * @param array  $data
     * @param bool   $need_cast
     *
     * @return array|null
     */
    public function query($sql, $data = [], $need_cast = true)
    {
        // выполняем запрос
        $result = $this->run($sql, $data, $need_cast);

        // сбрасываем данные
        $this->reset();

        // если данных нет
        if (empty($result) === true) {
            return null;
        }

        // возвращаем результат
        return $result;
    }

    /**
     * Подготавливает и выполняет запрос
     *
     * @param string $sql
     * @param array  $data
     * @param bool   $need_cast
     *
     * @return array|null
     */
    protected function run($sql, $data, $need_cast = true)
    {
        $result = $this->raw($sql, $data);

        if ($result) {
            $result = ($need_cast === true)
                ? $this->castQueryResults($result)
                : $result->fetch_assoc();
        }

        return $result;
    }

    /**
     * Выполняет SQL запрос
     * Если в запросе вместо названия таблицы прописать _table_, то название впишется автоматически на основе
     * static::$table
     *
     * @param string $sql
     * @param array  $data
     *
     * @return null|mysqli_result
     */
    public function raw($sql, $data = [])
    {
        $sql = $this->prepareSqlQuery($sql);

        try {
            // соединение с БД
            $connection = $this->conn();

            // подготавливаем и выполняем запрос
            if ($stmt = $this->preparedQuery($sql, array_values($data), $connection)) {
                if ($stmt->errno > 0) {
                    throw new Exception($stmt->error);
                }

                if ($stmt->insert_id > 0) {
                    return $stmt->insert_id;
                }

                $result = $stmt->get_result();

                if (empty($result) === true) {
                    return null;
                }

                return $result;
            }
        } catch (Exception $e) {
            $this->log($sql, $data, $e->getMessage());

            return null;
        }

        $this->log($sql, $data);

        return null;
    }

    /**
     * Указывает в качестве подключения другие настройки
     */
    protected function conn()
    {
        // название конфигурации
        $db_config_name = 'database.' . str_replace('database.', '', $this->connection);

        // получаем конфигурацию
        $db_cfg = config($db_config_name);

        // если конфигурация не найдена или указана по-умолчанию
        if (
            empty($db_cfg) === true or
            $db_cfg === 'database.default'
        ) {
            return $this;
        }

        // устанавливаем соединение
        $connection = mysqli_connect(
            $db_cfg['host'],
            $db_cfg['username'],
            $db_cfg['password'],
            $db_cfg['database']
        ) or validate_error_and_die('DataBase connect Error!');
        mysqli_query($connection, 'set names ' . $db_cfg['charset']);

        return $connection;
    }

    /**
     * Подготовка и выполнение запроса
     *
     * @param string $sql
     * @param array  $params
     * @param        $connection
     *
     * @return null|mysqli_stmt
     * @throws Exception
     */
    function preparedQuery($sql, $params, $connection)
    {
        // Типы добавляемых данных (по умолчанию s - string)
        $types = str_repeat("s", count($params));

        // Подготовка запроса
        $stmt = $connection->prepare($sql);

        //если ошибка - выбрасываем исключение с сообщением об ошибке.
        if ($stmt === false) {
            throw new Exception("SQL Error: {$connection->errno} - {$connection->error}");
        }

        /** @var mysqli_stmt $stmt */

        // Привязка параметров
        if (count($params) > 0) {
            // $this->bindParams($stmt, $types, $params);
            $stmt->bind_param($types, ...$params);
        }

        // Выполнение запроса
        $stmt->execute();

        // Возврат результата выполнения запроса
        return $stmt;
    }

    /**
     * Костыльный метод для PHP 5.4
     *
     * @param mysqli_stmt $stmt
     * @param string      $types
     * @param array       $params
     *
     * @return void
     */
    protected function bindParams($stmt, $types, $params)
    {
        switch (count($params)) {
            case 1:
                $stmt->bind_param($types, $params[0]);
                break;
            case 2:
                $stmt->bind_param($types, $params[0], $params[1]);
                break;
            case 3:
                $stmt->bind_param($types, $params[0], $params[1], $params[2]);
                break;
            case 4:
                $stmt->bind_param($types, $params[0], $params[1], $params[2], $params[3]);
                break;
            case 5:
                $stmt->bind_param($types, $params[0], $params[1], $params[2], $params[3], $params[4]);
                break;
            case 6:
                $stmt->bind_param($types, $params[0], $params[1], $params[2], $params[3], $params[4], $params[5]);
                break;
            case 7:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6]
                );
                break;
            case 8:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7]
                );
                break;
            case 9:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8]
                );
                break;
            case 10:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8],
                    $params[9]
                );
                break;
            case 11:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8],
                    $params[9],
                    $params[10]
                );
                break;
            case 12:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8],
                    $params[9],
                    $params[10],
                    $params[11]
                );
                break;
            case 13:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8],
                    $params[9],
                    $params[10],
                    $params[11],
                    $params[12]
                );
                break;
            case 14:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8],
                    $params[9],
                    $params[10],
                    $params[11],
                    $params[12],
                    $params[13]
                );
                break;
            case 15:
                $stmt->bind_param(
                    $types,
                    $params[0],
                    $params[1],
                    $params[2],
                    $params[3],
                    $params[4],
                    $params[5],
                    $params[6],
                    $params[7],
                    $params[8],
                    $params[9],
                    $params[10],
                    $params[11],
                    $params[12],
                    $params[13],
                    $params[14]
                );
                break;
        }
    }

    /**
     * Сохраняет лог ошибки и выводит сообщение об ошибке, если включен флаг DEBUG
     *
     * @param string $sql
     * @param array  $data
     * @param string $message
     *
     * @return void
     */
    protected function log($sql, $data, $message = '-')
    {
        $error_message = sprintf(
            "Ошибка во время исполнения запроса. \n\tSQL: %s\n\tДанные: %s . \n\tТекст ошибки: %s",
            $this->getQueryString($sql),
            json_encode($data),
            $message,
            "\nConnection: " . $this->connection
        );

        // сохраняем ошибку в лог
        log_error($error_message);

        // выводим ошибку
        if (defined(DEBUG) and DEBUG === true) {
            if (is_ajax()) {
                json_response_error($error_message);
            } else {
                dd($error_message);
            }
        }
    }

    /**
     * Приводит данные к типу полей базы данных
     *
     * @param $result
     *
     * @return array
     */
    function castQueryResults($result)
    {
        $fields = mysqli_fetch_fields($result);
        $data   = [];
        $types  = [];

        // получаем типы полей
        foreach ($fields as $field) {
            switch ($field->type) {
                case 1:     // tinyint | bool
                case 2:     // smallint
                case 3:     // int
                case 8:     // bigint
                case 9:     // mediumint
                    $types[$field->name] = 'int';
                    break;
                case 4:     // float
                case 5:     // double
                case 246:   // decimal
                    $types[$field->name] = 'float';
                    break;
                default:
                    $types[$field->name] = 'string';
                    break;
            }
        }

        while ($row = mysqli_fetch_assoc($result)) {
            $data[] = $row;
        }

        for ($i = 0; $i < count($data); $i++) {
            foreach ($types as $name => $type) {
                settype($data[$i][$name], $type);
            }
        }

        return $data;
    }

    /**
     * Сбрасывает значения данных для формирования SQL запроса
     */
    protected function reset()
    {
        // сбрасываем переменные
        $this->connection = 'default';

        $this->select   = '*';
        $this->where    = [];
        $this->bindings = [];
        $this->orderBy  = [];
        $this->groupBy  = '';
        $this->having   = '';
        $this->limit    = $this->offset = 0;
    }

    /**
     * Выполняет SQL запрос и возвращает 1 запись
     * Если в запросе вместо названия таблицы прописать _table_, то название впишется автоматически на основе
     * static::$table
     *
     * @param string $sql
     * @param array  $data
     * @param bool   $need_cast
     *
     * @return array|null
     */
    public function queryOne($sql, $data = [], $need_cast = true)
    {
        // выполняем запрос
        $result = $this->run($sql, $data, $need_cast);

        // сбрасываем данные
        $this->reset();

        // возвращаем результат
        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * Возвращает записи из базы данных
     *
     * @return array|null
     */
    public function get()
    {
        // формируем запрос
        $sql = $this->prepareSqlQuery(
            sprintf(
                'SELECT %s FROM %s %s %s %s %s %s',
                $this->select,
                static::getTableName(),
                $this->getSqlWhere(),
                $this->groupBy,
                $this->having,
                $this->getSqlOrderBy(),
                $this->getSqlLimit()
            )
        );

        // подготавливаем и выполняем запрос
        $result = $this->run($sql, $this->bindings);

        // сбрасываем данные
        $this->reset();

        // если данных нет
        if (empty($result) === true) {
            return null;
        }

        // возвращаем результат
        return $result;
    }

    /**
     * Возвращает одну запись из таблицы
     *
     * @return null|mixed
     */
    public function getOne()
    {
        // формируем запрос
        $sql = $this->prepareSqlQuery(
            sprintf(
                'SELECT %s FROM %s %s %s %s %s LIMIT 1',
                $this->select,
                static::getTableName(),
                $this->getSqlWhere(),
                $this->groupBy,
                $this->having,
                $this->getSqlOrderBy()
            )
        );

        // подготавливаем и выполняем запрос
        $result = $this->run($sql, $this->bindings);

        // сбрасываем данные
        $this->reset();

        // возвращаем результат
        return isset($result[0]) ? $result[0] : null;
    }

    /**
     * Возвращает количество записей в таблице
     *
     * @param string $column
     *
     * @return int
     */
    public function count($column = 'id')
    {
        // формируем запрос
        $sql = $this->prepareSqlQuery(
            sprintf(
                'SELECT COUNT(%s) AS `count` FROM %s %s %s LIMIT 1',
                $this->escapeMysqliIdentifier($column),
                static::getTableName(),
                $this->getSqlWhere(),
                $this->getSqlOrderBy()
            )
        );

        // подготавливаем и выполняем запрос
        $result = $this->run($sql, $this->bindings, false);
        $data   = isset($result['count']) ? $result['count'] : 0;

        // сбрасываем данные
        $this->reset();

        // возвращаем результат
        return (int)$data;
    }

    /**
     * Возвращает сумму записей в таблице
     *
     * @param string $column
     *
     * @return float
     */
    public function sum($column = 'id')
    {
        // формируем запрос
        $sql = $this->prepareSqlQuery(
            sprintf(
                'SELECT SUM(%s) AS `sum` FROM %s %s %s',
                $this->escapeMysqliIdentifier($column),
                static::getTableName(),
                $this->getSqlWhere(),
                $this->getSqlOrderBy()
            )
        );

        // подготавливаем и выполняем запрос
        $result = $this->run($sql, $this->bindings, false);
        $data   = isset($result['sum']) ? $result['sum'] : 0;

        // сбрасываем данные
        $this->reset();

        // возвращаем результат
        return (float)$data;
    }

    /**
     * Возвращает случайные записи
     *
     * @return array|null
     */
    public function random()
    {
        // формируем запрос
        $sql = $this->prepareSqlQuery(
            sprintf(
                'SELECT %s FROM %s %s ORDER BY RAND() %s',
                $this->select,
                static::getTableName(),
                $this->getSqlWhere(),
                $this->getSqlLimit()
            )
        );

        // подготавливаем и выполняем запрос
        $result = $this->run($sql, $this->bindings);

        // сбрасываем данные
        $this->reset();

        // если данных нет
        if (empty($result) === true) {
            return null;
        }

        // возвращаем результат
        return $result;
    }

    public function select($columns)
    {
        if ($columns === ['*']) {
            return $this;
        }

        $select = [];
        foreach ($columns as $column) {
            if (is_array($column) === true) {
                // подготавливаем переменные
                $column_name = $this->escapeMysqliIdentifier($column[0]);
                $placeholder = $this->escapeMysqliIdentifier($column[1]);

                // проверяем наличие функции COUNT, AVG и т.п.
                if (
                    ($left_bracket = strpos($column[0], '(')) !== false and
                    ($right_bracket = strpos($column[0], ')')) !== false
                ) {
                    // получаем чистое название колонки
                    $column_name_clear = trim(substr($column[0], $left_bracket, $right_bracket), "()`");

                    // название функции
                    $func = substr($column[0], 0, $left_bracket);

                    // обновляем название колонки
                    $column_name = "$func(`$column_name_clear`)";
                }

                // добавляем в SELECT
                $select[] = "$column_name AS $placeholder";
            } else {
                $select[] = $this->escapeMysqliIdentifier($column);
            }
        }

        $this->select = implode(', ', $select);

        return $this;
    }

    public function orWhere($key, $operator, $value = null)
    {
        return $this->where($key, $operator, $value, true);
    }

    public function where($key, $operator, $value = null, $isOr = false)
    {
        if ($value === null) {
            $value    = $operator;
            $operator = '=';
        }

        $prefix = $this->getWhereGroupPrefix();

        // если в качестве значения указана mysql функция
        if (in_array($value, $this->mysql_functions, true)) {
            $where_string = "$prefix`$key` $operator $value";
        } else {
            $where_string     = "$prefix`$key` $operator ?";
            $this->bindings[] = $value;
        }

        // добавляем в массив WHERE условие
        if (empty($this->where) === true) {
            $this->where[] = $where_string;
        } else {
            if ($isOr === true) {
                $this->where[] = "OR $where_string";
            } else {
                $this->where[] = "AND $where_string";
            }
        }

        return $this;
    }

    /**
     * Добавляет ключ начала группировки условия WHERE
     *
     * @return string
     */
    protected function getWhereGroupPrefix()
    {
        if ($this->isWhereGroup and $this->isWhereGroupFirst) {
            $this->isWhereGroupFirst = false;

            return $this->whereGroupStartKey;
        }

        return '';
    }

    public function orWhereNull($key)
    {
        return $this->whereNull($key, 'IS NULL', true);
    }

    public function whereNull($key, $operator = 'IS NULL', $isOr = false)
    {
        $prefix = $this->getWhereGroupPrefix();
        if (empty($this->where) === true) {
            $this->where[] = "$prefix`$key` $operator";
        } else {
            if ($isOr === true) {
                $this->where[] = "OR $prefix`$key` $operator";
            } else {
                $this->where[] = "AND $prefix`$key` $operator";
            }
        }

        return $this;
    }

    public function whereNotNull($key)
    {
        return $this->whereNull($key, 'IS NOT NULL');
    }

    public function orWhereNotNull($key)
    {
        return $this->whereNull($key, 'IS NOT NULL', true);
    }

    public function whereTrue($key)
    {
        return $this->whereNull($key, 'IS TRUE');
    }

    public function orWhereTrue($key)
    {
        return $this->whereNull($key, 'IS TRUE', true);
    }

    public function whereFalse($key)
    {
        return $this->whereNull($key, 'IS TRUE');
    }

    public function orWhereFalse($key)
    {
        return $this->whereNull($key, 'IS FALSE', true);
    }

    public function orWhereNIn($key, array $values)
    {
        return $this->whereIn($key, $values, 'IN', true);
    }

    public function whereIn($key, $values, $operator = 'IN', $isOr = false)
    {
        $prefix = $this->getWhereGroupPrefix();

        $placeholders = implode(',', array_fill(0, count($values), '?'));

        if (empty($this->where) === true) {
            $this->where[]  = "$prefix`$key` $operator($placeholders)";
            $this->bindings = array_merge($this->bindings, $values);
        } else {
            if ($isOr === true) {
                $this->where[]  = "OR $prefix`$key` $operator($placeholders)";
                $this->bindings = array_merge($this->bindings, $values);
            } else {
                $this->where[]  = "AND $prefix`$key` $operator($placeholders)";
                $this->bindings = array_merge($this->bindings, $values);
            }
        }

        return $this;
    }

    public function whereNotIn($key, $values)
    {
        return $this->whereIn($key, $values, 'NOT IN');
    }

    public function orWhereNotIn($key, $values)
    {
        return $this->whereIn($key, $values, 'NOT IN', true);
    }

    /**
     * Указывает начала группировки условия WHERE
     *
     * @return static
     */
    public function whereGroupStart()
    {
        $this->isWhereGroup = true;

        return $this;
    }

    /**
     * Указывает конец группировки условия WHERE
     *
     * @return static
     */
    public function whereGroupEnd()
    {
        // добавляем ключ закрытия группировки условия
        if (
            $this->isWhereGroup === true and
            empty($this->where) === false
        ) {
            $this->where[array_key_last($this->where)] .= $this->whereGroupEndKey;
        }

        $this->isWhereGroupFirst = true;
        $this->isWhereGroup      = false;

        return $this;
    }

    /**
     * @param string|array $column
     *
     * @return static
     */
    public function groupBy($column)
    {
        if (is_array($column) === true) {
            $groupBy = [];
            foreach ($column as $value) {
                $groupBy[] = $this->escapeMysqliIdentifier($value);
            }
            $column = implode(', ', $groupBy);
        } else {
            $column = $this->escapeMysqliIdentifier($column);
        }
        $this->groupBy = 'GROUP BY ' . $column;

        return $this;
    }

    /**
     * Используется в сочетании с предложением GROUP BY, чтобы ограничить группы возвращаемых строк только теми, для
     * которых условие истинно.
     * Разрешенные функции: SUM, COUNT, MIN, MAX, AVG.
     *
     * @param string           $key
     * @param string           $operator
     * @param int|float|string $value
     *
     * @return static
     */
    public function having($key, $operator, $value)
    {
        $allowed_func = ['SUM', 'COUNT', 'MIN', 'MAX', 'AVG'];

        // проверяем наличие функции COUNT, AVG и т.п.
        if (
            ($left_bracket = strpos($key, '(')) !== false and
            ($right_bracket = strpos($key, ')')) !== false
        ) {
            // название функции
            $func = substr($key, 0, $left_bracket);

            // если функция разрешена
            if (in_array($func, $allowed_func)) {
                // получаем чистое название колонки
                $column_name = trim(substr($key, $left_bracket, $right_bracket), "()`");

                // обновляем название колонки
                $this->having = "HAVING $func(`$column_name`) $operator ?";

                //
                $this->bindings[] = $value;
            }
        }

        return $this;
    }

    public function orderBy($column, $direction = 'ASC')
    {
        $this->orderBy[] = "`$column` $direction";

        return $this;
    }

    public function limit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    public function offset($offset)
    {
        $this->offset = $offset;

        return $this;
    }

    /**
     * Добавление данных в базу данных.
     *
     * @param array $data Массив данных [ключ => значение].
     *
     * @return int|null В случае успеха возвращает ID добавленной записи.
     */
    public function insert($data)
    {
        // получаем ключи массива данных для формирования списка полей и привязок
        $keys = array_keys($data);

        // формируем список полей для заполнения
        $fields = implode(",", $keys);

        // формируем привязки вставляемых данных
        $placeholders = [];
        foreach ($data as $data_key => $data_value) {
            // если в качестве значения указана mysql функция
            if (in_array($data_value, $this->mysql_functions, true)) {
                $placeholders[] = $data_value;
                unset($data[$data_key]);
            } else {
                $placeholders[] = '?';
            }
        }

        // добавляем данные в массив привязок
        $this->bindings = array_merge(array_values($data), $this->bindings);

        // SQL запрос
        $sql = sprintf(
            'INSERT INTO %s (%s) VALUES (%s)',
            static::getTableName(),
            $fields,
            implode(',', $placeholders)
        );

        $result = (int)$this->raw($sql, $this->bindings);

        $this->reset();

        return ($result > 0) ? $result : null;
    }

    /**
     * Обновления данных в базе данных
     *
     * @param array $data Массив данных для обновления
     *
     * @return bool
     */
    public function update($data)
    {
        // массив данных пуст
        if (empty($data) === true) {
            json_response_error('Data to update is empty!');
        }

        // не указаны условия для выборки
        if (empty($this->where) === true) {
            json_response_error('Where can\'t be empty!');
        }

        // формируем список полей с привязками для обновления
        $fields = [];
        foreach ($data as $data_key => $data_value) {
            $field = $this->escapeMysqliIdentifier($data_key);

            // если в качестве значения указана MYSQL функция
            if (in_array($data_value, $this->mysql_functions, true)) {
                $fields[] = "$field = $data_value";
                unset($data[$data_key]);
            } else {
                $fields[] = "$field = ?";
            }
        }

        // добавляем данные в массив привязок
        $this->bindings = array_merge(array_values($data), $this->bindings);

        // SQL запрос
        $sql = sprintf(
            'UPDATE %s SET %s %s',
            static::getTableName(),
            implode(', ', $fields),
            $this->getSqlWhere()
        );

        $this->raw($sql, $this->bindings);

        $this->reset();

        return true;
    }

    /**
     * Удаляет строки из таблицы
     *
     * @return bool
     */
    public function delete()
    {
        // не указаны условия для выборки
        if (empty($this->where) === true) {
            json_response_error('Where can\'t be empty!');
        }

        // SQL запрос
        $sql = sprintf(
            'DELETE FROM %s %s %s %s',
            static::getTableName(),
            $this->getSqlWhere(),
            $this->getSqlOrderBy(),
            $this->getSqlLimit()
        );

        $this->raw($sql, $this->bindings);

        $this->reset();

        return true;
    }

}
