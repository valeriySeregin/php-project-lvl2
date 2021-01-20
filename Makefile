install:
	composer install

lint:
	composer exec --verbose phpcs -- --standard=PSR12 bin src

lint-fix:
	composer exec --verbose phpcbf -- --standard=PSR12 bin src 
