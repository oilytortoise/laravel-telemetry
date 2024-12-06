# laravel-telemetry
Integration between Laravel and Betterstack's Telemetry feature (for logging and metrics)  
BetterStack: [https://betterstack.com/]  
</br>
Basically, the documentation for setting up Telemetry with laravel recommends a method that requires instantiating and setting up the logger object every time you want to log something which, imo, is combersome and not very performant.

This package does not exclusively log to Telemetry. If the `ENABLE_TELEMETRY` environment variable is set to `false` the package will return to logging to the default Laravel log channel as configured in your `logging.php` config file.
</br>
This package is built to make the logging process much simpler and implement some other handy logging / debugging features.

# Installation

```
composer require oilytortoise/laravel-telemetry
```

# Setup BetterStack
1. Sign up for betterstack
2. Go to the "Telemetry" tab
3. Navigate to "Sources" and create a new source for a PHP app  

# Setup Laravel Application
*This is a recommended setup - feel free to use the package however you like!*

1. publish the package's config file:
``` cli
php artisan vendor:publish --tag=telemetry-config
```
</br>

2. In your `AppServiceProvider` add the following in the `boot()` function to create a singleton you can utilize throughout your application whenever you want to log an event:  
```
use Oilytortoise\LaravelTelemetry\TelemetryLogger;
.
.
.
public function boot()
{
    $this->app->singleton('telemetry', function ($app) {
        return new TelemetryLogger();
    });
}
```
*note: The benefit of using a singleton is that your application will load the class only once when it is booted, then you can use that instance of the telemetry logger service instead of creating a new instance every time you want to log something. This reduces the development effort and makes the process more performant.*  
</br>
You may tag the singleton however you want. I use `'telemetry'` in the example above but you could use `'log'`, or `'TelemetryLogger::class'` or anything you want as long as it doesn't clash with another singleton in the app.  
</br>

3. In your `.env` file, add the following:
```
TELEMETRY_ENABLED=true
TELEMETRY_SOURCE_TOKEN=<your-telemetry-source-token>
```
</br>

4. To log an event you can use the logger as shown below:
```
app('telemetry')->info('Example log event', ['context_field' => 'example context value']);
```

The syntax for logging is essentially the same as the default `\Illuminate\Support\Facades\Log` service provided by Laravel. The same `debug`, `info`, `notice`, `warning`, `error`, `critical`, `alert`, `emergency` functions act as the log level. The first parameter passed in is the log message, the second parameter is the context array.  
</br>

5. To log exceptions to telemetry, there are two options:  
To report all exceptions EXCEPT for the ones in your configured `exception_blacklist`, add the following to the `register()` function in your `App\Exceptions\Handler` class:
```
if (! app('telemetry')->shouldIgnore($e)) {
    app('telemetry')->error($e->getMessage()); // or however you'd like to log the errors.
}
```
</br>
To report only the exceptions that appear in your configured `exception_whitelist`:
```
if (! app('telemetry')->shouldReport($e)) {
    app('telemetry')->error($e->getMessage()); // or however you'd like to log the errors.
}
```
</br>
The choice is yours whether you want to report all exceptions except for the ones blacklisted in the `telemetry.php` config file, or only report the exceptions whitelisted in the config file.

# Features
There are a few extended features included in this package:

## Default Context
By default, whenever you log an event, the package will always include the app environment, and the authorized user ID in the context field. If there is no active user session (for example during a scheduled job or some third party webhook request) the user ID will be set to "system".
</br>

## Debug Context
Whenever you make a debug log, the package will automatically add a "class::function" context field, the value of which will tell you the class and function the log was sent from. This can be helpful when you are attempting to debug an issue in a live environment and you don't know where the bug is occuring.
</br>

## Enable Switch