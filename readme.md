# Simple staging Janitor jobs
Plugin for very simple staging setup for https://github.com/bnomei/kirby3-janitor/ (required). Beta quality - use at your own risk. It's working without problems for me but any comments and suggestions for improvements are welcome.
There are two variants pure php and rsync.

## Requirements
Plugin assumes flat structure and while you can define the destination (live site) as you want i would suggest structure like
```
websites/
	staging.mysite.com
	mysite.com
```
## PHP job vs Rsync job
Both jobs are different.
The *PHP job* should be more compatible but it naive, takes longer, it alwyas copies everything and it requires you to have space for 3 versions of your site. It works like this
1. Copy staging.mysite.com to __staging_mysite.com
2. Rename mysite.com to __tobedeleted_mysite.com
3. Rename __staging_mysite.com to mysite.com
4. Delete __tobedeleted_mysite.com

*Rsync job* requires you to have rsync available on your server but it syncs changes from staging to live directly. This makes it much more efficient and you have only 2 versions of site at the time. Rsync also has better include/exclude options. Many shared hostings with ssh have rsync available but check if you allow php to run exec();.

You probably want to use *Rsync job* if you can.

## Install
```composer require floriankarsten/simple-staging```

config.php
```php
// Required loads the jobs
'bnomei.janitor.jobs-extends' => [
	'floriankarsten.simplestaging.jobs'
],
// this is required name of final destination of website
// for example if i have site folders staging.test.com and test.com i set test.com as destination
'floriankarsten.simplestaging' => [
	'destination' => 'test.com',
	'base' => '/users/floriankarsten/bestwebsite/' // Not required. Absolute path to base of our website. By default its parent of index/public folder which is what you probably want.
]
```

In your blueprint
```yaml
// PHP job
pushlive:
  type: janitor
  label: Deploy to Live site rsync
  progress: Deploying...
  job: deploylive

// Rsync job
pushlive:
  type: janitor
  label: Deploy to Live site rsync
  progress: Deploying...
  job: deployliversync
```


## Configuration PHP job
```php
'floriankarsten.simplestaging' => [
	'basic' => [ // 'basic' is namespace for our 'php job'
		'excludedir' => ['vendor', 'node_modules'] // basic job can only exclude directories. Empty by default.
	],
]
```

## Configuration Rsync job
```php
'floriankarsten.simplestaging' => [
	'rsync' => [
		'executable' => '/usr/lib/rsync' // path to rsync library. Default 'rsync'
		'flags' => ['a', 'r'] // overwrite default rsync flags. Default ['a', 'r']
		// include/exclude work as if they were passed directly to rsync (so things like *.jpg etc.). If you use only include without exclude we assume and exclude '*'
		'exclude' => ['vendor', 'node_modules'], // Empty by default.
		'include' => ['content'], // Empty by default.
	],
]
```




This plugin wouldn't happen without [@garethworld](https://github.com/garethworld) who kindly hired me to make it and then wanted to have it released to Kirby community. Thanks GARETH
