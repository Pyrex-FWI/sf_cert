<?php

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Cpyree\AdminLTEBundle\Form\DataTransformer;

use Cpyree\AdminLTEBundle\Form\DataObject\Range;
use Symfony\Component\Form\DataTransformerInterface;
use Symfony\Component\Form\Exception\TransformationFailedException;
use Symfony\Component\Form\Exception\UnexpectedTypeException;

/**
 * Transforms between a date string and a DateTime object
 *
 * @author Bernhard Schussek <bschussek@gmail.com>
 * @author Florian Eckerstorfer <florian@eckerstorfer.org>
 */
class RangeToStringTransformer implements DataTransformerInterface
{

    const SEPARATOR = ':';
    /**
     * Format used for generating strings
     * @var string
     */
    private $generateFormat;

    /**
     * Format used for parsing strings
     *
     * Different than the {@link $generateFormat} because formats for parsing
     * support additional characters in PHP that are not supported for
     * generating strings.
     *
     * @var string
     */
    private $parseFormat;

    /**
     * Whether to parse by appending a pipe "|" to the parse format.
     *
     * This only works as of PHP 5.3.7.
     *
     * @var bool
     */
    private $parseUsingPipe;

    /**
     * Transforms a \DateTime instance to a string
     *
     * @see \DateTime::format() for supported formats
     *
     * @param string  $inputTimezone  The name of the input timezone
     * @param string  $outputTimezone The name of the output timezone
     * @param string  $format         The date format
     * @param bool    $parseUsingPipe Whether to parse by appending a pipe "|" to the parse format
     *
     * @throws UnexpectedTypeException if a timezone is not a string
     */
    public function __construct($inputTimezone = null, $outputTimezone = null, $format = 'Y-m-d H:i:s', $parseUsingPipe = null)
    {


    }

    /**
     * Transforms a Range object into a date string with the configured format
     * and timezone
     *
     * @param \Cpyree\AdminLTEBundle\Form\DataObject\Range $value A DateTime object
     *
     * @return string A value as produced by PHP's date() function
     *
     * @throws TransformationFailedException If the given value is not a \DateTime
     *                                       instance or if the output timezone
     *                                       is not supported.
     */
    public function transform($value)
    {
        if (null === $value) {
            return '';
        }

        if (!$value instanceof \Cpyree\AdminLTEBundle\Form\DataObject\Range) {
            throw new TransformationFailedException('Expected a \Cpyree\AdminLTEBundle\Form\DataObject\Range.');
        }

        /** @var \Cpyree\AdminLTEBundle\Form\DataObject\Range $value */

        return $value->getMin() . self::SEPARATOR . $value->getMax();
    }

    /**
     * Transforms a range string in the configured timezone into a Range object.
     *
     * @param string $value A value as produced by PHP's date() function
     *
     * @return \Cpyree\AdminLTEBundle\Form\DataObject\Range An instance of \Cpyree\AdminLTEBundle\Form\DataObject\Range
     *
     * @throws TransformationFailedException If the given value is not a string,
     *                                       if the date could not be parsed or
     *                                       if the input timezone is not supported.
     */
    public function reverseTransform($value)
    {
        if (empty($value)) {
            return;
        }

        if (!is_string($value)) {
            throw new TransformationFailedException('Expected a string.');
        }

        $bounds = explode(self::SEPARATOR, $value);
        //test is bounds are available
        $range = new Range(isset($bounds[0])? $bounds[0] : 0, isset($bounds[1])? $bounds[1] : 1);


        return $range;
    }
}
