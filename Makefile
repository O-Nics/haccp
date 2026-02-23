lint:
	@php artisan ide-helper:eloquent
	@php artisan ide-helper:generate
	@php artisan ide-helper:meta
	@php artisan ide-helper:models -W -R -E
	./vendor/bin/pint
	@#npm run typegen

ide:
	@php artisan ide-helper:eloquent
	@php artisan ide-helper:generate
	@php artisan ide-helper:meta
	@php artisan ide-helper:models -W -R -E
	@php bin/fix-ide-imports.php
	./vendor/bin/pint

import:
	@php bin/fix-ide-imports.php

exec_web:
	@docker exec -ti liberlo-phoenix-php-apache-1 bash

db:
	@php artisan migrate:fresh --seed

test:
	@php artisan test --parallel --processes=10
