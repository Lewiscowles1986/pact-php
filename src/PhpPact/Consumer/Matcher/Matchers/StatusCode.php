<?php

namespace PhpPact\Consumer\Matcher\Matchers;

use PhpPact\Consumer\Matcher\Exception\InvalidHttpStatusException;
use PhpPact\Consumer\Matcher\Generators\RandomInt;
use PhpPact\Consumer\Matcher\HttpStatus;

/**
 * Matches the response status code.
 */
class StatusCode extends GeneratorAwareMatcher
{
    public function __construct(private string $status, private ?int $value = null)
    {
        if (!in_array($status, HttpStatus::all())) {
            throw new InvalidHttpStatusException(sprintf("Status '%s' is not supported. Supported status are: %s", $status, implode(', ', HttpStatus::all())));
        }

        if ($value === null) {
            [$min, $max] = match($status) {
                HttpStatus::INFORMATION => [100, 199],
                HttpStatus::SUCCESS => [200, 299],
                HttpStatus::REDIRECT => [300, 399],
                HttpStatus::CLIENT_ERROR => [400, 499],
                HttpStatus::SERVER_ERROR => [500, 599],
                HttpStatus::NON_ERROR => [100, 399],
                HttpStatus::ERROR => [400, 599],
                default => [100, 199], // Can't happen, just to make PHPStan happy
            };

            $this->setGenerator(new RandomInt($min, $max));
        }
    }

    public function getType(): string
    {
        return 'statusCode';
    }

    /**
     * @return array<string, string>
     */
    protected function getAttributesData(): array
    {
        return ['status' => $this->status];
    }

    protected function getValue(): ?int
    {
        return $this->value;
    }
}
