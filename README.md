# Install and start application

You need at least php **5.5.9*** with **SQLite extension** enabled and **Composer**
    
	Dependecies				->	php composer.phar install
	Start mysql && front	->	docker-compose up
	Migrate database		->	php console.php migrations:migrate
    Start REST API			->	php -S 0:9001 -t web/
    
Your api is now available at http://localhost:9001/api/v1.

#### What you will get
The api will respond to

	GET  	->	http://localhost:9001/api/v1/notes
    GET  	->  http://localhost:9001/api/v1/notes/{id}
	POST 	->  http://localhost:9001/api/v1/notes
	PUT 	->  http://localhost:9001/api/v1/notes/{id}
	DELETE 	-> 	http://localhost:9001/api/v1/notes/{id}

Your request should have 'Content-Type: application/json' header.
Your api is CORS compliant out of the box, so it's capable of cross-domain communication.

Try with curl:
	
	#GET (collection)
	curl http://localhost:9001/api/v1/notes -H 'Content-Type: application/json' -w "\n"
	
	#GET (single item with id 1)
    curl http://localhost:9001/api/v1/notes/1 -H 'Content-Type: application/json' -w "\n"

	#POST (insert)
	curl -X POST http://localhost:9001/api/v1/notes -d '{"note":"Hello World!"}' -H 'Content-Type: application/json' -w "\n"

	#PUT (update)
	curl -X PUT http://localhost:9001/api/v1/notes/1 -d '{"note":"Uhauuuuuuu!"}' -H 'Content-Type: application/json' -w "\n"

	#DELETE
	curl -X DELETE http://localhost:9001/api/v1/notes/1 -H 'Content-Type: application/json' -w "\n"

#### Migrations
	Generate new migration  ->   php console.php migrations:generate
	Execute migrations  	->   php console.php migrations:migrate
	Rollback migration  	->   php console.php migrations:execute 20180526170352 --down