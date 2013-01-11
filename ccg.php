<?php

/**
 * Code Completion Generator
 * By EthaiZone.com <ethaizone@hotmail.com>
 * Code task from http://forums.laravel.io/viewtopic.php?id=742&p=2
 * Thanks - https://github.com/danielboendergaard/laravel-helpers/blob/master/ide_helper.php
 */


class Ccg_Task extends Task
{
	/**
	 * Header strings to add with head of generate code.
	 * @var type array
	 */
	private static $header = array(
		'<?php',
		'
/**
 * Code completion code.
 * Generated by ccg Artisan command.
 */

/**
 * Short-cut for constructor method chaining.
 *
 * @param  mixed  $object
 * @return object
 */
function with($object)
{
	return $object;
}

class Asset_Container extends Laravel\Asset_Container{};
class Query extends Laravel\Database\Query{};
',
	);

	/**
	 * Footer string to add with tail of generate code.
	 * @var type array
	 */
	private static $footer = array(
		'// End of code generation'
	);

	/**
	 * Abstract classes.
	 * @var type array
	 */
	private static $abstracts = array( 'Authenticator' );

	/**
	 * Extra codes to add as function definitions.
	 *
	 * When not enough to complition on specific classes,
	 * you can add any function want to complite.
	 *
	 * @var type array
	 */
	private static $extra = array(
		'DB' => array(
			'
	//From /laravel/database/connection.php
	/**
	 * Execute a callback wrapped in a database transaction.
	 *
	 * @param  callback  $callback
	 * @return void
	 */
	public function transaction($callback) {}

	/**
	 * Execute a SQL query and return an array of StdClass objects.
	 *
	 * @param  string  $sql
	 * @param  array   $bindings
	 * @return array
	 */
	public function query($sql, $bindings = array()) {}
			',
		),
		'Eloquent' => array(
			'	
	//Dummy variable - common use
	
	/**
	 * Timestamp of record when created. 
	 *
	 * @var string
	 */
	public $created_at;
	
	/**
	 * Timestamp of record when updated. 
	 *
	 * @var string
	 */
	public $updated_at;
	
	//Dummy method - I can\'t find real method.

	/**
	 * Eager load table that you want to join
	 *
	 * <code>
	 *	Book::with("author")->get();
	 *	Book::with(array("author", "author.contacts"))->get();
	 *	User::with(array("posts" => function($query)
	 *	{
	 *	    $query->where("title", "like", "%first%");
	 *	}))->get();
	 * </code>
	 *
	 * @param mixed $column
	 * @return array
	 */
	public static function with($column) {}

	/**
	 * Return total records that you want query
	 *
	 * @return int
	 */
	public static function count() {}
			',	
		),
		'Log' => array(
			'
	//Common used.

	/**
	 * Write an "error" message to the log file
	 *
	 * @param string $message
	 * @return void
	 */
	public static function error($message) {}

	/**
	 * Write an "warning" message to the log file
	 *
	 * @param string $message
	 * @return void
	 */
	public static function warning($message) {}

	/**
	 * Write an "info" message to the log file
	 *
	 * @param string $message
	 * @return void
	 */
	public static function info($message) {}
			',
		),
		//Navbar from Bootstrapper Bundle
		'Navbar' => array(
			'
	/**
	 * Create a new Navbar instance with dark color.
	 *
	 * @param array $attributes An array of attributes for the current navbar
	 * @param const $type       The type of Navbar to create
	 *
	 * @return Navbar
	 */
	function inverse($attributes = array(), $type = Navbar::STATIC_BAR) {}
			',
		),
	);

	/**
	 * Display C.C. code
	 *
	 * Usage:
	 * 		php artisan ccg
	 *
	 * @return integer Execution result code
	 */
	public function run()
	{

		echo $this->generator();

		exit(0);
	}

	/**
	 * Generate C.C. code and save to file as php.
	 *
	 * Usage:
	 * 		// save to application/ccc.php
	 * 			php artisan ccg:save
	 *
	 * 		// save file specified by parameter
	 * 			php artisan ccg:save application/cc_helper.php
	 *
	 * @param string $outfile Output file name
	 * @return integer Execution result code
	 */
	public function save($arguments)
	{
		$outfile = count($arguments) == 0 ? $outfile = path('app')."ccg.php" : $arguments[0];

		$codes = $this->generator();

		File::put($outfile, $codes);

		exit(0);
	}

	/**
	 * Code generator
	 *
	 * @return string Generated code
	 */
	private function generator()
	{
		$codeline = array( );
		$codeline[] = self::$header;

		$aliases = Config::get('application.aliases');

		foreach ( $aliases as $alias => $namespace ) {
			switch ( $alias ) {
				case 'Auth' :
					switch ( Config::get('auth.driver') ) {
						case 'eloquent' :
							$codeline[] = $this->format_code($alias, 'Laravel\Auth\Drivers\Eloquent');
							break;
						case 'fluent' :
							$codeline[] = $this->format_code($alias, 'Laravel\Auth\Drivers\Fluent');
							break;
						default :
							// throw new \Exception("Bad Auth driver [$alias] specified.");
							$codeline[] = "// Auth driver [$alias] is not supported by this generator. Please set this by your hand.";
							$codeline[] = '// class '.$alias.' extends '.$namespace.'{};';
					}
					break;
				/*
				case 'DB' :
					switch ( Config::get('database.default') ) {
						case 'sqlite':
							$namespace = 'Laravel\Database\Query\Grammars\SQLite';
							break;
						case 'mysql':
							$namespace = 'Laravel\Database\Query\Grammars\MySQL';
							break;
						case 'pgsql':
							$namespace = 'Laravel\Database\Query\Grammars\Postgres';
							break;
						case 'sqlsrv':
							$namespace = 'Laravel\Database\Query\Grammars\SQLServer';
							break;
						default:
							// throw new \Exception("Bad database default driver [$alias] specified.");
							$codeline[] = "// DB default driver [$alias] is not supported by this generator. Please set this by your hand.";
							$codeline[] = '// class '.$alias.' extends '.$namespace.'{};';
					}
					$codeline[] = $this->format_code($alias, $namespace);
					break;
				 * 
				 */
				case 'Cache' :
					switch ( Config::get('cache.driver') ) {
						case 'apc':
							$codeline[] = $this->format_code($alias, 'Laravel\Cache\Drivers\APC');
							break;
						case 'file':
							$codeline[] = $this->format_code($alias, 'Laravel\Cache\Drivers\File');
							break;
						case 'memcached':
							$codeline[] = $this->format_code($alias, 'Laravel\Cache\Drivers\Memcached');
							break;
						case 'memory':
							$codeline[] = $this->format_code($alias, 'Laravel\Cache\Drivers\Memory');
							break;
						case 'redis':
							$codeline[] = $this->format_code($alias, 'Laravel\Cache\Drivers\Redis');
							break;
						case 'database':
							$codeline[] = $this->format_code($alias, 'Laravel\Cache\Drivers\Database');
							break;
						default:
							// throw new \Exception("Bad Cache driver [$alias] specified.");
							$codeline[] = "// Cache driver [$alias] is not supported by this generator. Please set this by your hand.";
							$codeline[] = '// class '.$alias.' extends '.$namespace.'{};';
					}
					break;
				case 'Session' :
					$add = $this->get_method_data('session/payload.php', 'Payload');
					$codeline[] = $this->format_code($alias, "Laravel\Session", $add);
					break;
				/*
				case 'Asset' :
					$add = $this->get_method_data('asset.php', 'Asset_Container');
					$codeline[] = $this->format_code($alias, "Laravel\Asset", $add);
					break;
				 */
				/*
				case 'Eloquent' :
					$add = $this->get_method_data('database/query.php', 'Query');
					$codeline[] = $this->format_code($alias, "Laravel\Database\Eloquent\Model", $add);
					break;*/
				default :
					$codeline[] = $this->format_code($alias, $namespace);
			}
		}

		$codeline[] = self::$footer;
		$codeline[] = '';

		$flatcode = array( );
		array_walk_recursive($codeline, function($val) use (&$flatcode) {
				$flatcode[] = $val;
			});

		return implode(PHP_EOL, $flatcode);
	}


	
	private function get_method_data($filename, $class)
	{
		$file = path('sys').$filename;
		if(! file_exists($file)) return array("// Failed to read driver soruce file:[/laravel/$filename]");
		
		$script = trim(strtr(File::get($file), '\\', '/'));		
		
		preg_match('#class '.$class.' \{(.+)\}#s', $script, $match);
		
		if(empty($match[1])) return array("// Failed to read driver soruce file:[/laravel/$filename]");
		
		$script = $match[1];
		

		
		$regex_replace = array(
			array('#/\*.+?@var.+?\$.+?;#s', ''),
				
			array('#\{[^\{\}]+\}#s', ''),
			array('#\{[^\{\}]+\}#s', ''),
			array('#\{[^\{\}]+\}#s', ''),
			array('#\{[^\{\}]+\}#s', ''),
			array('#(function.+\))\n#', "\\1 {}\n"),
			array('#public function __construct[^\n]*\([^\n]*\) \{\}#', ''),
			array('#protected (static )?function[^\n]*\([^\n]*\) \{\}#', ''),
			array('#private (static )?function[^\n]*\([^\n]*\) \{\}#', '')
			//array('#/\*\*[^/]+\*/[\s]+protected (static )?function[^\n]+\([^\n]*\) \{\}#s', ''),
			//array('#/\*\*[^/]+\*/[\s]+private (static )?function[^\n]+\([^\n]*\) \{\}#s', ''),
			//array('#/\*\*[^/]+\*/[\s]+public function __construct[^\n]*\([^\n]*\) \{\}#s', ''),
			//array('#\n[\s]+?\n#s', "\n"),

		);
		
		foreach ($regex_replace as $regex)
		{
			$script = preg_replace($regex[0], $regex[1], $script);
		}
		
		//Delete old phpdoc that method deleted
		//I must delete by pointer  not regex because I tested many regex to do it but result isn't good.
		$script = trim($script);
		$delete_list = array();
		$offset = 0;
		while($offset !== FALSE)
		{
			$start = strpos($script, "/**", $offset);
			//var_dump($start);die();
			if($start !== FALSE)
			{
				$stop = strpos($script, "*/", $start+3);
				
				//var_dump($stop);
				if($stop !== FALSE)
				{
					$next = strpos($script, "/**", $stop+2);
					//var_dump($next);
					//echo "\n";
				
					if($next !== FALSE)
					{
						$between_phpdocs = substr($script, $stop+2, $next-$stop-3);
						$look = strpbrk($between_phpdocs, implode('', range('a', 'z')));
						//var_dump($between_phpdocs );die();

						if($look === FALSE)
						{
							//delete old phpdoc
							$delete_list[] = array($start, ($stop-$start)+2);
							$offset = $stop+2;
							//echo 'start:'.$start.' - stop:'.$stop.' - next:'.$next."\n";

						} else {
							$offset = $next-3;
						}
					} else {
						$between_phpdocs = substr($script, $stop, strlen($script)-$stop);
						$look = strpbrk($between_phpdocs, implode('', range('a', 'z')));

						//Catch last old phpdoc/func
						if($look === FALSE)
						{
							$delete_list[] = array($start, ($stop-$start)+2);
							//echo 'start:'.$start.' - stop:'.$stop.' - next:'.$next."\n";
						}
						$offset = FALSE;
					}
				} else {
					$offset = FALSE;
				}
			} else {
				$offset = FALSE;
			}
		}
		
		//Delete old phpdoc - reverse order.
		//var_dump($delete_list);die();
		$delete_list = array_reverse($delete_list);
		foreach ($delete_list as $k => $delete) {
			//File::put($k.'.txt', substr($script, $delete[0], $delete[1]));
			$script = substr_replace($script, '', $delete[0], $delete[1]);			

		}
		
		//Delete blank line.
		$script = preg_replace('#\n[\s]+\n#s', "\n\n", $script);
		
		return "\t//Methods from /laravel/$filename - Class $class\n\n\t".trim($script);		
	}


	/**
	 * Format class extends
	 *
	 * @param string $alias Alias name
	 * @param string $namespace Namespace
	 * @return array
	 */
	private function format_code($alias, $namespace, $addcodes = array( ))
	{
		$abs = in_array($alias, self::$abstracts) ? 'abstract ' : '';

		if ( key_exists($alias, self::$extra) || !empty($addcodes) ) {
			$ret = array( );
			$ret[] = $abs.'class '.$alias.' extends '.$namespace.'{';
			if ( key_exists($alias, self::$extra) )
				$ret[] = self::$extra[$alias];
			if ( !empty($addcodes) )
			{
				if(!is_array($addcodes)) $addcodes = explode(PHP_EOL, $addcodes);
				$ret[] = $addcodes;
			}
			$ret[] = '};';
			return $ret;
		}
		else {
			return array(
				$abs.'class '.$alias.' extends '.$namespace.'{};'
			);
		}
	}

}
