lint:
	@php artisan ide-helper:eloquent
	@php artisan ide-helper:generate
	@php artisan ide-helper:meta
	@php artisan ide-helper:models -W -R -E
	make fix-import
	./vendor/bin/pint

fix-import:
	@php bin/fix-ide-imports.php

db:
	@php artisan migrate:fresh --seed

test:
	@php artisan test --parallel --processes=10
