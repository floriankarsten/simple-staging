# Simple staging Janitor job
This job simply copies whole staging > live site.

## Install
## Config
```php
// This option loads the job
'bnomei.janitor.jobs-extends' => [
	'floriankarsten.simplestaging.jobs'
],
// this is required name of final destination of website
// for example if i have site folders staging.test.com and test.com i set test.com as destination
'floriankarsten.simplestaging' => [
	'destination' => 'test.com'
]
```