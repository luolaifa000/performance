<?php
namespace Langyi\Performance\DataSource;

use Langyi\Performance\Helpers\Serializer;
use Langyi\Performance\Helpers\StackTrace;

use Illuminate\Database\ConnectionResolverInterface;
use Illuminate\Contracts\Events\Dispatcher as EventDispatcher;
use Illuminate\Http\Request;

/**
 * Data source for Eloquent (Laravel ORM), provides database queries
 */
class EloquentDataSource extends DataSource
{
	/**
	 * Database manager
	 */
	protected $databaseManager;

	/**
	 * Internal array where queries are stored
	 */
	protected $queries = [];

	/**
	 * Model name to associate with the next executed query, used to map queries to models
	 */
	public $nextQueryModel;

	/**
	 * Create a new data source instance, takes a database manager and an event dispatcher as arguments
	 */
	public function __construct(ConnectionResolverInterface $databaseManager, EventDispatcher $eventDispatcher)
	{
		$this->databaseManager = $databaseManager;
		$this->eventDispatcher = $eventDispatcher;
	}

	/**
	 * Start listening to eloquent queries
	 */
	public function listenToEvents()
	{
	    $this->eventDispatcher->listen(\Illuminate\Database\Events\QueryExecuted::class, [ $this, 'registerQuery' ]);
	}

	/**
	 * Log the query into the internal store
	 */
	public function registerQuery($event)
	{
	    
		$trace = StackTrace::get()->resolveViewName();
		$caller = $trace->firstNonVendor([ 'laravel', 'illuminate' ]);

		$this->queries[] = [
			'query'      => $event->sql,
			'bindings'   => $event->bindings,
			'time'       => $event->time,
			'connection' => $event->connectionName,
			'file'       => $caller->shortPath,
			'line'       => $caller->line,
			'trace'      => $this->collectStackTraces ? (new Serializer)->trace($trace->framesBefore($caller)) : null,
			'model'      => $this->nextQueryModel
		];
		
		$this->nextQueryModel = null;
	}


	/**
	 * Adds ran database queries to the request
	 */
	public function resolve(Request $request)
	{
		return $request;
	}
	
	/**
	 * Takes a query binding and a connection name, returns a quoted binding value
	 */
	protected function quoteBinding($binding, $connection)
	{
	    $connection = $this->databaseManager->connection($connection);
	    
	    if ($connection->getPdo()->getAttribute(\PDO::ATTR_DRIVER_NAME) === 'odbc') {
	        // PDO_ODBC driver doesn't support the quote method, apply simple MSSQL style quoting instead
	        return "'" . str_replace("'", "''", $binding) . "'";
	    }
	    
	    return $connection->getPdo()->quote($binding);
	}
	
	
	protected function createRunnableQuery($query, $bindings, $connection)
	{
	    // add bindings to query
	    $bindings = $this->databaseManager->connection($connection)->prepareBindings($bindings);
	    
	    foreach ($bindings as $binding) {
	        $binding = $this->quoteBinding($binding, $connection);
	        
	        // convert binary bindings to hexadecimal representation
	        if (! preg_match('//u', $binding)) $binding = '0x' . bin2hex($binding);
	        
	        // escape backslashes in the binding (preg_replace requires to do so)
	        $binding = str_replace('\\', '\\\\', $binding);
	        
	        $query = preg_replace('/\?/', $binding, $query, 1);
	    }
	    
	    // highlight keywords
	    $keywords = [
	        'select', 'insert', 'update', 'delete', 'where', 'from', 'limit', 'is', 'null', 'having', 'group by',
	        'order by', 'asc', 'desc'
	    ];
	    $regexp = '/\b' . implode('\b|\b', $keywords) . '\b/i';
	    
	    $query = preg_replace_callback($regexp, function ($match) { return strtoupper($match[0]); }, $query);
	    
	    return $query;
	}

	

	/**
	 * Returns an array of runnable queries and their durations from the internal array
	 */
	public function getDatabaseQueries()
	{
	   
		return array_map(function ($query) {
			return [
				'query'      => $this->createRunnableQuery($query['query'], $query['bindings'], $query['connection']),
				'duration'   => $query['time'],
				'connection' => $query['connection'],
				'file'       => $query['file'],
				'line'       => $query['line'],
				'trace'      => $query['trace'],
				'model'      => $query['model']
			];
		}, $this->queries);
	}

}
