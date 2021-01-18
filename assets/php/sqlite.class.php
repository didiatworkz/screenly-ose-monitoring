<?php
/*
* SQLite3 Database Class
* By Timothy 'TiM' Oliver
*
* An abstraction database to help
* simplify queries made to a SQLite database.
*
* Powered by the PHP Data Object (PDO) framework.
*
* ============================================================================
*
* Copyright (C) 2011 by Tim Oliver
*
* Permission is hereby granted, free of charge, to any person obtaining a copy
* of this software and associated documentation files (the "Software"), to deal
* in the Software without restriction, including without limitation the rights
* to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
* copies of the Software, and to permit persons to whom the Software is
* furnished to do so, subject to the following conditions:
*
* The above copyright notice and this permission notice shall be included in
* all copies or substantial portions of the Software.
*
* THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
* IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
* FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
* AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
* LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
* OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
* THE SOFTWARE.
*
*/

class SQLite3Database
{
	//The target DB file
	var $db_file = '';

	//PDO DB object
	var $db = NULL;

	//Table prefixes
	var $prefix = NULL;

	//The default result handle that cannot be overwritten
	//from an outside call (to absolutely prevent accidental conflicts)
	var $default_result = NULL;

	//Array to store result handles from simultaneous queries
	var $results = NULL;

	/*
	* Class Constructor
	* Init SQLite and connect to server based on supplied arguments
	*
	* $db_file	- (str)			- The SQLite file to load (absolute or relative path)
	* $prefix 	- (array/str)	- Prefix to prepend to all table names (string for 1 entry, array for multiple)
	* $connect 	- (bool)		- Automatically connect to the database on instantiation
	*/
	function __construct( $db_file = '', $prefix = '', $connect = true )
	{
		//init default values across the board
		$this->results = array();

		//check the file exists
		if( !is_file( $db_file ) )
			throw new Exception( 'SQite3Database: File '.$db_file.' wasn\'t found.' );

		//set up database properties
		$this->db_file 	= $db_file;
		$this->prefix	= $prefix;

		if( $connect )
			$this->connect();
	}

	/*
	* Class destructor
	* Close the connection to the database and free up resources as needed
	*/
	function __destruct()
	{
		//destroy the PDO object
		$this->db = NULL;
	}

	/*
	* Connect Class
	* Connects to the SQLite database and throws an exception if it fails
	*/
	function connect()
	{
		try
		{
			//connect to the database file
			$this->db = new MyPDO( 'sqlite:'.$this->db_file );
			}
		catch( PDOException $e )
		{
			throw new Exception( 'SQLite3Database: '.$e->getMessage() );
		}

		//enable verbose error reporting
		$this->db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
	}

	/*
	* Query Function
	* Perform a general query on the SQLite database.
	*
	* Arguments:
	* $query 	- (str) - The SQLite query to execute. (NB: MUST be sanitized beforehand)
	* $handle 	- (str) - A unique string ID that can be used to refer to this query later
	*
	* Returns the result of the query
	*
	*/
	function query($query, $handle = '', $ignore_count = TRUE )
	{
		//if query was a 'SELECT', perform the query,
		//return the number of returned rows
		if( preg_match( '%^(select)%is', $query ) > 0 )
		{
			try
			{
				//perform the query
				$result = $this->db->query( $query ); //PDO query method for result
			}
			catch (PDOException $e )
			{
				//throw general exception
				throw new Exception( 'SQLite3Database: '.$e->getMessage().' Query: '.$query );
			}

			//add count to the result
			if( $ignore_count != TRUE )
				$result->count = $this->db->rowCount();
			else
				$result->count = 1;

			//save the handle of this query
			$this->set_handle( $result, $handle );

			//return number of rows
			return $result->count;
		}
		else //Else query was an operation
		{
			try
			{
				//perform the query
				$result = $this->db->exec( $query ); //PDO exec method
			}
			catch (PDOException $e )
			{
				//throw general exception
				throw new Exception( 'SQLite3Database: '.$e->getMessage().' Query: '.$query );
			}

			//if query was one that affected existing rows (ie UPDATE, DELETE etc), return the number of affected rows
			if( preg_match( '%^(update|replace|delete)%is', $query ) > 0 )
			{
				return $result; //PDO automatically returns the number of affected rows as result
			}
			//if query was an insert, return the new ID
			elseif( preg_match( '%^(insert)%is', $query ) > 0 )
			{
				return $this->db->lastInsertId();
			}
		}
		return TRUE;
	}

	/*
	* prepare
	* Sanitizes a PHP query and its arguments to reduce the
	* potential of SQL injection attacks.
	*
	* Arguments:
	* $query (str) - SQLite string to sanitize
	*
	*	Takes the following wildcards for the substitution (a la sprintf style)
	*	%d - integer value
	*	%f - float value
	*	%[0-9]s - string value (No quotes required) [Int indicates rounding]
	*	%[0-9]t - table name (auto-appends prefix) [Int indicates prefix array index]
	*
	* After $query, an arbitrary number of arguments of types int, float, or string can be added which
	* will be substituted for each wildcard in that order.
	* In addition, a single array() containing the values can be used instead.
	*/
	function prepare( $query )
	{
		if( !$query )
			return '';

		//get the arguments
		$args = func_get_args();
		array_shift($args); //remove $query

		//If the first optional argument
		//is an array, then assume that that
		//is the supplied list of arguments
		if( is_array($args[0]) )
			$args = $args[0];

		//loop through each argument
		foreach( $args as $arg )
		{
			//select each argument from left to right, one at a time, retrieving the symbol, and offset
			if( preg_match( '/%([0-9]*?)([dfst])/is', $query, $match, PREG_OFFSET_CAPTURE ) <= 0 )
				break;

			//grab the info from the match
			$tag	= strval($match[0][0]);	//The full match (ie %3t )
			$param 	= intval($match[1][0]);	//The number (if any) after the % (ie 4)
			$offset = intval($match[0][1]); //The location of these chars from the front of the string (ie 15)
			$symbol = strval($match[2][0]); //The particular parameter defined in the query (ie t)

			//prepare the argument for insertion
			switch( strtolower( $symbol ) )
			{
				//parse as an int
				case 'd':
					$arg = intval( $arg );
					break;
				//parse as a float
				case 'f':
					//if an argument was given, round the float off to that set
					if( $param > 0 )
						$arg = round( $arg, $param );

					$arg = floatval( $arg );
					break;
				case 't':
					//if prefix is an array, then param becomes the index
					if( is_array( $this->prefix ) )
						$prefix = $this->prefix[$param-1];
					else
						$prefix = $this->prefix;

					$arg = ($prefix).(strval( $arg ));
					break;
				//parse as a string
				case 's':
				default:
					//if magic quotes are enabled (ie auto slashes), strip them out
					if( get_magic_quotes_gpc() )
						$arg = stripslashes($arg);

					//sanitize single quotes (SQLite escape standard)
					$arg = str_replace( "'", "''", $arg );

					//sanitize with SQLite sanitation function
					$arg = "'".$arg."'";
					break;
			}

			//remove the '%%%' string from the query
			$query = substr_replace( $query, '', $offset, strlen($tag) );

			//insert the sanitized value in its place
			$query = substr_replace( $query, $arg, $offset, 0 );
		}

		return $query;
	}

	/*
	* Fetch Rows
	* Iterates through all of the rows returned from a query and
	* returns one on each call of this method
	*
	* $handle 			- (str) - Unique string ID of the handle to process
	* $return_array 	- (bool) - Return as an associative array instead of an object
	*/
	function fetch_row( $handle = '', $return_array = FALSE )
	{
		$result = $this->get_handle($handle);
		if( !$result )
			return FALSE;

		//init the output
		$row = array();

		//get one instance from the object
		$row = $result->fetch(PDO::FETCH_ASSOC);
		if( $row == NULL )
			return NULL;

		//return an object by default, but also allow for arrays
		if( $return_array )
			return $row;
		else
			return (object)$row;
	}

	/*
	* get_single_row
	* Return a single row from a query, formatted as array or object
	*
	* Args:
	* $query		- (str) - The SQLite query (MUST be properly sanitized beforehand)
	* $return_array - (bool) - Return associative array instead of object
	*/
	function get_row( $query, $return_array = false)
	{
		//perform the query
		$num_rows = $this->query( $query );
		if( $num_rows === FALSE || $num_rows <= 0 )
			return FALSE;

		//get the first row
		$row = $this->fetch_row( '', $return_array );

		return $row;
	}

	/*
	* get_rows
	* Return multiple rows from a query as an array of objects
	*
	* Args:
	* $query		- (str) - The SQLite query (MUST be properly sanitized beforehand)
	* $return_array - (bool) - Each row is an array instead of an object
	*/
	function get_rows( $query ='', $return_array = FALSE )
	{
		//perform the query
		$num_rows = $this->query( $query );
		if( $num_rows === FALSE || $num_rows <= 0 )
			return FALSE;

		$result = $this->get_handle();

		$rows = array();

		//loop through and get each object
		while( $row = $result->fetch( PDO::FETCH_ASSOC ) )
		{
			if( $return_array )
				$rows[] = $row;
			else
				$rows[] = (object)$row;
		}

		//return array
		return $rows;
	}

	/*
	* Insert
	*
	* Insert a new row into a table
	*
	* Arguments:
	* $table 	- (array/string) 	- Table to insert to (eg 'table' or array( 'tablename' => '%t2' ) )
	* $data 	- (array) 			- Data to insert into the table (eg array( 'col_name' => 'value' )
	* $format 	- (array)			- Array matching the order of $data, dictating each value data type (eg array('%s', '%d') )
	*
	*/
	function insert( $table = '', $data = NULL, $format = NULL )
	{
		//check all necessary arguments
		if( !$table || !$data )
			return FALSE;

		//prepare an array to store the arg values
		$arg_list = array();

		//start building the query
		$query = 'INSERT INTO';

		//set the table name in the query
		if( is_array( $table ) ) //allow for different prefix
		{
			$query		.= array_shift(array_keys($table));
			$arg_list[]	= array_shift(array_values($table));
		}
		else
		{
			$query 		.= ' %t ';
			$arg_list[] = $table;
		}

		$query .= ' (';

		//add the name of each column
		$i=0;
		foreach( $data as $name => $value )
		{
			$query .= ' '.$name;

			//if not the final value, be sure to append a comma
			if( $i < count( $data ) - 1)
				$query .= ',';

			$i++;
		}

		$query .= ' ) VALUES (';

		//add the value from the data array
		//if possible, use proper formatting
		$i=0;
		foreach( $data as $name => $value )
		{
			if( is_array( $format ) )
				$query .= ' '.$format[$i];
			else
				$query .= ' %s';

			$arg_list[] = $value;

			//if not the final value, be sure to append a comma
			if( $i < count( $data ) - 1)
				$query .= ',';

			$i++;
		}

		//cap off the end
		$query .= ' );';

		//prepare/sanitze the query
		$query = $this->prepare( $query, $arg_list );

		//execute the query and return the results
		return $this->query( $query );
	}


	/*
	* Update
	*
	* Construct a query and then execute
	* to update one or more entries in a table.
	*
	* Arguments:
	* table 		- (array|string) 	 - name of table, and/or formatting (eg 'table' or array( 'tablename' => '%2t' ) )
	* $data			- (array)			 - data to insert into table in name => value format (eg array( 'foo' => 'bar' ) )
	* $where 		- (array) 			 - array stating 1 or more conditions of the update query (eg array('id' => 1) )
	* $format 		- (array) (optional) - array dictating the data type of each data value (eg array( '%s', '%d' ) )
	* $format_where - (array) (optional) - array dictating the data type of each where data value (eg array( '%s', '%d' ) )
	*/
	function update( $table = '', $data = NULL, $where = NULL, $format = NULL, $where_format = NULL, $limit = 0 )
	{
		//check all necessary arguments
		if( !$table || !$data || !$where )
			return FALSE;

		//prepare a list to store the insert args as they come
		$arg_list = array();

		//begin building the query
		$query = 'UPDATE';

		//set the table name in the query
		if( is_array( $table ) ) //allow for different prefix
		{
			$query		.= array_shift(array_keys($table));
			$arg_list[]	= array_shift(array_values($table));
		}
		else
		{
			$query .= ' %t';
			$arg_list[] = $table;
		}

		$query .= ' SET';

		//add each piece of data to the query
		$i=0;
		foreach( $data as $name => $value )
		{
			//if format is specified, use it, else default to string
			if( is_array( $format ) )
				$query .= ' '.$name.' = '.$format[$i];
			else
				$query .= ' '.$name.' = %s';

			//append the value to the arglist
			$arg_list[] = $value;

			//if not the final value, be sure to append a comma
			if( $i < count( $data ) - 1)
				$query .= ',';

			$i++;
		}

		$query .= ' WHERE';

		//add each where condition to the query
		$i=0;
		foreach( $where as $name => $value )
		{
			//if format is specified, use it, else default to string
			if( is_array( $where_format ) )
				$query .= ' '.$name.' = '.$where_format[$i];
			else
				$query .= ' '.$name.' = %s';

			//append the value to the arglist
			$arg_list[] = $value;

			//if not the final value, be sure to append an AND
			if( $i < count( $where ) - 1)
				$query .= 'AND ';

			$i++;
		}

		//append limit if required
		if( $limit > 0 )
			$query .= ' LIMIT '.$limit;

		$query .= ';';

		//prepare/sanitze the query
		$query = $this->prepare( $query, $arg_list );

		//execute the query and return the results
		return $this->query( $query );
	}

	/*
	* Delete
	*
	* Delete 1 or more rows from a table
	*
	* table 	- (array|string) 	 - name of table, and/or formatting (eg 'table' or array( 'tablename' => '%2t' ) )
	* $where 	- (array) 			 - array stating 1 or more conditions of the update query (eg array('id' => 1) )
	* $format 	- (array) (optional) - array dictating the data type of each data value (eg array( '%s', '%d' ) )
	*/
	function delete( $table = '', $where = NULL, $format = NULL, $limit = 0 )
	{
		//check all necessary arguments
		if( !$table || !$where )
			return FALSE;

		//prepare a list to store the insert args as they come
		$arg_list = array();

		//begin building the query
		$query = 'DELETE FROM';

		//set the table name in the query
		if( is_array( $table ) ) //allow for different prefix
		{
			$query		.= array_shift(array_keys($table));
			$arg_list[]	= array_shift(array_values($table));
		}
		else
		{
			$query .= ' %t';
			$arg_list[] = $table;
		}

		$query .= ' WHERE';

		//add each where condition to the query
		$i=0;
		foreach( $where as $name => $value )
		{
			//if format is specified, use it, else default to string
			if( is_array( $format ) )
				$query .= ' '.$name.' = '.$format[$i];
			else
				$query .= ' '.$name.' = %s';

			//append the value to the arglist
			$arg_list[] = $value;

			//if not the final value, be sure to append a comma
			if( $i < count( $where ) - 1)
				$query .= ' AND ';

			$i++;
		}

		//append limit if specified
		if( $limit > 0 )
			$query .= ' LIMIT '.$limit;

		$query .= ';';

		//prepare/sanitze the query
		$query = $this->prepare( $query, $arg_list );

		//execute the query and return the results
		return $this->query( $query );
	}

	/*
	* num_rows
	* Get the number of rows returned from the query
	*/
	function num_rows( $handle = '' )
	{
		$result = $this->get_handle( $handle );
		return $result->count;
	}

	/*
	* insert_id
	* Get the insert ID from the last query made
	*/
	function insert_id()
	{
		return $this->db->lastInsertId();
	}

	/*
	* Get Result Handle
	*
	* When arbitrarily called, returns the reference pointer
	* to where a SQLite result variable will be stored.
	*
	* Args:
	* $handle - (str) - Unique string ID of the handle to retrieve
	*
	* Returns: PDOStatement Object
	*/
	private function get_handle( $handle = '' )
	{
		//no handle specified, use the main hardcoded block
		if( !$handle )
			return $this->default_result;
		else
			return $this->results[strval($handle)];
	}

	/*
	* Set Result Handle
	*
	* Stores a query result that can be retrieved later
	* for simultaneous query handling.
	* If no handle is supplied, the default store is used
	*
	* $result - (PDOStatement Object) - Query result object
	* $handle - (string) - Unique ID to refer to this result object
	*/
	private function set_handle( $result = NULL, $handle = '' )
	{
		if( !$result )
			return false;

		if( !$handle )
			$this->default_result = $result;
		else
			$this->results[strval($handle)] = $result;

		return true;
	}
}

/*
* MyPDO Extended Class
*
* A class that inherits from PDO, but
* fixes the functionality of the rowCount method using
* the COUNT(*) query hack.
*
* Written by Eli Sand. Sourced from PHP.net:
* http://www.php.net/manual/en/pdostatement.rowcount.php#87110
*/
class MyPDO extends PDO {
	private $queryString;

	public function query(/* ... */) {
		$args = func_get_args();
		$this->queryString = func_get_arg(0);

		return call_user_func_array(array(&$this, 'parent::query'), $args);
	}

	public function rowCount() {
		$regex = '/^SELECT\s+(?:ALL\s+|DISTINCT\s+)?(?:.*?)\s+FROM\s+(.*)$/i';
		if (preg_match($regex, $this->queryString, $output) > 0) {
			$stmt = parent::query("SELECT COUNT(*) FROM {$output[1]}", PDO::FETCH_NUM);

			return $stmt->fetchColumn();
		}

		return false;
	}
}
?>
