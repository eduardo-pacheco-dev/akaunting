<?php

namespace Illuminate\Session;

use Illuminate\Support\InteractsWithTime;
use Illuminate\Support\Str;
use SessionHandlerInterface;

/**
 * Overrides the framework handler to implement the methods that PHP 8.6 will
 * require from SessionHandlerInterface implementations. Without them PHP
 * raises an E_WARNING while loading the class, which Laravel's error handler
 * turns into an ErrorException (used by the array session driver in tests).
 *
 * The implementation is otherwise identical to the framework's handler.
 */
class ArraySessionHandler implements SessionHandlerInterface
{
    use InteractsWithTime;

    /**
     * The array of stored values.
     *
     * @var array
     */
    protected $storage = [];

    /**
     * The number of minutes the session should be valid.
     *
     * @var int
     */
    protected $minutes;

    /**
     * Create a new array driven handler instance.
     *
     * @param  int  $minutes
     * @return void
     */
    public function __construct($minutes)
    {
        $this->minutes = $minutes;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function open($savePath, $sessionName): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function close(): bool
    {
        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return string|false
     */
    public function read($sessionId): string|false
    {
        if (! isset($this->storage[$sessionId])) {
            return '';
        }

        $session = $this->storage[$sessionId];

        $expiration = $this->calculateExpiration($this->minutes * 60);

        if (isset($session['time']) && $session['time'] >= $expiration) {
            return $session['data'];
        }

        return '';
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function write($sessionId, $data): bool
    {
        $this->storage[$sessionId] = [
            'data' => $data,
            'time' => $this->currentTime(),
        ];

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return bool
     */
    public function destroy($sessionId): bool
    {
        if (isset($this->storage[$sessionId])) {
            unset($this->storage[$sessionId]);
        }

        return true;
    }

    /**
     * {@inheritdoc}
     *
     * @return int
     */
    public function gc($lifetime): int
    {
        $expiration = $this->calculateExpiration($lifetime);

        $deletedSessions = 0;

        foreach ($this->storage as $sessionId => $session) {
            if ($session['time'] < $expiration) {
                unset($this->storage[$sessionId]);
                $deletedSessions++;
            }
        }

        return $deletedSessions;
    }

    /**
     * Generate a new session ID.
     *
     * @return string
     */
    public function create_sid(): string
    {
        return Str::random(40);
    }

    /**
     * Validate a session ID supplied by the client.
     *
     * @param  string  $sessionId
     * @return bool
     */
    public function validateId(string $sessionId): bool
    {
        return preg_match('/^[a-zA-Z0-9]{40}$/', $sessionId) === 1;
    }

    /**
     * Get the expiration time of the session.
     *
     * @param  int  $seconds
     * @return int
     */
    protected function calculateExpiration($seconds)
    {
        return $this->currentTime() - $seconds;
    }
}
