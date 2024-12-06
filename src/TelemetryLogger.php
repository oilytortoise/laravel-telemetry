<?php

namespace Oilytortoise\LaravelTelemetry;

use Illuminate\Support\Facades\Log;
use Monolog\Logger;
use Logtail\Monolog\LogtailHandler;
use Throwable;

/**
 * Custom logging service for sending logs to Telemetry.
 *
 * @author OilyTortoise
 * @since 06 Dec 2024
 */
class TelemetryLogger
{
    /**
     * The Monolog Logger instance
     */
    protected $logger;

    public function __construct() {

        if(config('telemetry.enabled')) {
            $this->logger = new Logger('telemetry');
            $this->logger->pushHandler(new LogtailHandler(config('telemetry.sourceToken')));
        } else {
            $this->logger = new Log;
        }

    }

    /**
     * Usage: Use for debugging, tracing, or step-by-step logging that
     * helps developers understand what the code is doing.
     */
    public function debug(string $message = '', array $context = [])
    {
        $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2);
        $caller = $trace[1]; // Caller of this function

        $class = isset($caller['class']) ? $caller['class'] : '';
        $function = isset($caller['function']) ? $caller['function'] : '';

        $context = array_merge($context, [
            'class::function' => "$class::$function",
        ]);

        $this->log('debug', $message, $this->completeContext($context));
    }

    /**
     * Usage: Use for general application flow messages that aren't critical
     * but give insight into the app's normal operation. Often used for
     * routine events such as service starts, database connections, or
     * status updates.
     */
    public function info(string $message = '', array $context = [])
    {
        $this->log('info', $message, $this->completeContext($context));
    }

    /**
     * Usage: Use for events that aren't errors but might be important to
     * monitor, like a system event or a situation that could require
     * investigation later.
     */
    public function notice(string $message = '', array $context = [])
    {
        $this->log('notice', $message, $this->completeContext($context));
    }

    /**
     * Usage: Use for situations that are not errors but may lead to problems
     * if not addressed, such as a deprecated function being used or minor
     * issues that don't affect the app's operation immediately.
     */
    public function warning(string $message = '', array $context = [])
    {
        $this->log('warning', $message, $this->completeContext($context));
    }

    /**
     * Usage: Use when something goes wrong, such as a failed operation, but
     * the application can still continue functioning.
     */
    public function error(string $message = '', array $context = [])
    {
        $this->log('error', $message, $this->completeContext($context));
    }

    /**
     * Usage: Use for critical issues that need immediate attention, such as
     * a service being unavailable or a critical component failing.
     */
    public function critical(string $message = '', array $context = [])
    {
        $this->log('critical', $message, $this->completeContext($context));
    }

    /**
     * Usage: Use for highly critical situations that need immediate intervention,
     * such as system failure, resource exhaustion, or security breaches.
     */
    public function alert(string $message = '', array $context = [])
    {
        $this->log('alert', $message, $this->completeContext($context));
    }

    /**
     * Usage: This level is reserved for situations where the application or system
     * is completely compromised or failing in a way that requires immediate
     * intervention from system administrators.
     */
    public function emergency(string $message = '', array $context = [])
    {
        $this->log('emergency', $message, $this->completeContext($context));
    }

    /**
     * Send the log to the relevant logger.
     */
    protected function log(string $level, string $message, array $context)
    {
        if ($this->logger instanceof Logger) {
            $this->logger->$level($message, $context);
        } else {
            $this->logger::$level($message, $context);
        }
    }

    /**
     * Set the default context to be included in all Telemetry logs.
     */
    protected function defaultContext(): array
    {
        return [
            'environment' => app()->environment(),
            'user' => auth()->user()?->id ?? 'system'
        ];
    }

    /**
     * Get the full context for logging, including the
     * default context.
     */
    protected function completeContext(array $context): array
    {
        return array_merge($this->defaultContext(), $context);
    }

    /**
     * Determine if the exception is one we should ignore.
     */
    public function shouldIgnore(Throwable $e)
    {
        $ignoredExceptions = config('telemetry.ignore_exceptions') ?? [];

        foreach ($ignoredExceptions as $class) {
            if ($e instanceof $class) {
                return true;
            }
        }
        return false;
    }

    /**
     * Determine if the exception is one we should report.
     */
    public function shouldReport(Throwable $e)
    {
        $reportExceptions = config('telemetry.report_exceptions') ?? [];

        foreach ($reportExceptions as $class) {
            if ($e instanceof $class) {
                return true;
            }
        }
        return false;
    }
}
