<?php

namespace PhpPact\Consumer\Matcher\Generators;

/**
 * Generates a datetime value for the provided format.
 * If no format is provided, ISO format is used.
 * If an expression is given, it will be evaluated to generate the datetime, otherwise 'now' will be used
 *
 * Example format: yyyy-MM-dd'T'HH:mm:ss
 * Example expression: +1 day
 *
 * NOTE: Java's datetime format is used, not PHP's datetime format
 * For Java one, see https://www.digitalocean.com/community/tutorials/java-simpledateformat-java-date-format#patterns
 * For PHP one, see https://www.php.net/manual/en/datetime.format.php#refsect1-datetime.format-parameters
 */
class DateTime extends AbstractDateTime
{
    public function getType(): string
    {
        return 'DateTime';
    }
}
