<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\Extension;

use WPR_Vendor\Twig\ExpressionParser;
use WPR_Vendor\Twig\Node\Expression\Binary\AddBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\AndBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\BitwiseAndBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\BitwiseOrBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\BitwiseXorBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\ConcatBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\DivBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\EndsWithBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\EqualBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\FloorDivBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\GreaterBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\GreaterEqualBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\InBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\LessBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\LessEqualBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\MatchesBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\ModBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\MulBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\NotEqualBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\NotInBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\OrBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\PowerBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\RangeBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\SpaceshipBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\StartsWithBinary;
use WPR_Vendor\Twig\Node\Expression\Binary\SubBinary;
use WPR_Vendor\Twig\Node\Expression\Filter\DefaultFilter;
use WPR_Vendor\Twig\Node\Expression\NullCoalesceExpression;
use WPR_Vendor\Twig\Node\Expression\Test\ConstantTest;
use WPR_Vendor\Twig\Node\Expression\Test\DefinedTest;
use WPR_Vendor\Twig\Node\Expression\Test\DivisiblebyTest;
use WPR_Vendor\Twig\Node\Expression\Test\EvenTest;
use WPR_Vendor\Twig\Node\Expression\Test\NullTest;
use WPR_Vendor\Twig\Node\Expression\Test\OddTest;
use WPR_Vendor\Twig\Node\Expression\Test\SameasTest;
use WPR_Vendor\Twig\Node\Expression\Unary\NegUnary;
use WPR_Vendor\Twig\Node\Expression\Unary\NotUnary;
use WPR_Vendor\Twig\Node\Expression\Unary\PosUnary;
use WPR_Vendor\Twig\NodeVisitor\MacroAutoImportNodeVisitor;
use WPR_Vendor\Twig\TokenParser\ApplyTokenParser;
use WPR_Vendor\Twig\TokenParser\BlockTokenParser;
use WPR_Vendor\Twig\TokenParser\DeprecatedTokenParser;
use WPR_Vendor\Twig\TokenParser\DoTokenParser;
use WPR_Vendor\Twig\TokenParser\EmbedTokenParser;
use WPR_Vendor\Twig\TokenParser\ExtendsTokenParser;
use WPR_Vendor\Twig\TokenParser\FlushTokenParser;
use WPR_Vendor\Twig\TokenParser\ForTokenParser;
use WPR_Vendor\Twig\TokenParser\FromTokenParser;
use WPR_Vendor\Twig\TokenParser\IfTokenParser;
use WPR_Vendor\Twig\TokenParser\ImportTokenParser;
use WPR_Vendor\Twig\TokenParser\IncludeTokenParser;
use WPR_Vendor\Twig\TokenParser\MacroTokenParser;
use WPR_Vendor\Twig\TokenParser\SetTokenParser;
use WPR_Vendor\Twig\TokenParser\UseTokenParser;
use WPR_Vendor\Twig\TokenParser\WithTokenParser;
use WPR_Vendor\Twig\TwigFilter;
use WPR_Vendor\Twig\TwigFunction;
use WPR_Vendor\Twig\TwigTest;
final class CoreExtension extends \WPR_Vendor\Twig\Extension\AbstractExtension
{
    private $dateFormats = ['F j, Y H:i', '%d days'];
    private $numberFormat = [0, '.', ','];
    private $timezone = null;
    /**
     * Sets the default format to be used by the date filter.
     *
     * @param string $format             The default date format string
     * @param string $dateIntervalFormat The default date interval format string
     */
    public function setDateFormat($format = null, $dateIntervalFormat = null)
    {
        if (null !== $format) {
            $this->dateFormats[0] = $format;
        }
        if (null !== $dateIntervalFormat) {
            $this->dateFormats[1] = $dateIntervalFormat;
        }
    }
    /**
     * Gets the default format to be used by the date filter.
     *
     * @return array The default date format string and the default date interval format string
     */
    public function getDateFormat()
    {
        return $this->dateFormats;
    }
    /**
     * Sets the default timezone to be used by the date filter.
     *
     * @param \DateTimeZone|string $timezone The default timezone string or a \DateTimeZone object
     */
    public function setTimezone($timezone)
    {
        $this->timezone = $timezone instanceof \DateTimeZone ? $timezone : new \DateTimeZone($timezone);
    }
    /**
     * Gets the default timezone to be used by the date filter.
     *
     * @return \DateTimeZone The default timezone currently in use
     */
    public function getTimezone()
    {
        if (null === $this->timezone) {
            $this->timezone = new \DateTimeZone(\date_default_timezone_get());
        }
        return $this->timezone;
    }
    /**
     * Sets the default format to be used by the number_format filter.
     *
     * @param int    $decimal      the number of decimal places to use
     * @param string $decimalPoint the character(s) to use for the decimal point
     * @param string $thousandSep  the character(s) to use for the thousands separator
     */
    public function setNumberFormat($decimal, $decimalPoint, $thousandSep)
    {
        $this->numberFormat = [$decimal, $decimalPoint, $thousandSep];
    }
    /**
     * Get the default format used by the number_format filter.
     *
     * @return array The arguments for number_format()
     */
    public function getNumberFormat()
    {
        return $this->numberFormat;
    }
    public function getTokenParsers() : array
    {
        return [new \WPR_Vendor\Twig\TokenParser\ApplyTokenParser(), new \WPR_Vendor\Twig\TokenParser\ForTokenParser(), new \WPR_Vendor\Twig\TokenParser\IfTokenParser(), new \WPR_Vendor\Twig\TokenParser\ExtendsTokenParser(), new \WPR_Vendor\Twig\TokenParser\IncludeTokenParser(), new \WPR_Vendor\Twig\TokenParser\BlockTokenParser(), new \WPR_Vendor\Twig\TokenParser\UseTokenParser(), new \WPR_Vendor\Twig\TokenParser\MacroTokenParser(), new \WPR_Vendor\Twig\TokenParser\ImportTokenParser(), new \WPR_Vendor\Twig\TokenParser\FromTokenParser(), new \WPR_Vendor\Twig\TokenParser\SetTokenParser(), new \WPR_Vendor\Twig\TokenParser\FlushTokenParser(), new \WPR_Vendor\Twig\TokenParser\DoTokenParser(), new \WPR_Vendor\Twig\TokenParser\EmbedTokenParser(), new \WPR_Vendor\Twig\TokenParser\WithTokenParser(), new \WPR_Vendor\Twig\TokenParser\DeprecatedTokenParser()];
    }
    public function getFilters() : array
    {
        return [
            // formatting filters
            new \WPR_Vendor\Twig\TwigFilter('date', '\\WPR_Vendor\\twig_date_format_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('date_modify', '\\WPR_Vendor\\twig_date_modify_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('format', 'sprintf'),
            new \WPR_Vendor\Twig\TwigFilter('replace', '\\WPR_Vendor\\twig_replace_filter'),
            new \WPR_Vendor\Twig\TwigFilter('number_format', '\\WPR_Vendor\\twig_number_format_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('abs', 'abs'),
            new \WPR_Vendor\Twig\TwigFilter('round', '\\WPR_Vendor\\twig_round'),
            // encoding
            new \WPR_Vendor\Twig\TwigFilter('url_encode', '\\WPR_Vendor\\twig_urlencode_filter'),
            new \WPR_Vendor\Twig\TwigFilter('json_encode', 'json_encode'),
            new \WPR_Vendor\Twig\TwigFilter('convert_encoding', '\\WPR_Vendor\\twig_convert_encoding'),
            // string filters
            new \WPR_Vendor\Twig\TwigFilter('title', '\\WPR_Vendor\\twig_title_string_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('capitalize', '\\WPR_Vendor\\twig_capitalize_string_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('upper', '\\WPR_Vendor\\twig_upper_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('lower', '\\WPR_Vendor\\twig_lower_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('striptags', 'strip_tags'),
            new \WPR_Vendor\Twig\TwigFilter('trim', '\\WPR_Vendor\\twig_trim_filter'),
            new \WPR_Vendor\Twig\TwigFilter('nl2br', 'nl2br', ['pre_escape' => 'html', 'is_safe' => ['html']]),
            new \WPR_Vendor\Twig\TwigFilter('spaceless', '\\WPR_Vendor\\twig_spaceless', ['is_safe' => ['html']]),
            // array helpers
            new \WPR_Vendor\Twig\TwigFilter('join', '\\WPR_Vendor\\twig_join_filter'),
            new \WPR_Vendor\Twig\TwigFilter('split', '\\WPR_Vendor\\twig_split_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('sort', '\\WPR_Vendor\\twig_sort_filter'),
            new \WPR_Vendor\Twig\TwigFilter('merge', '\\WPR_Vendor\\twig_array_merge'),
            new \WPR_Vendor\Twig\TwigFilter('batch', '\\WPR_Vendor\\twig_array_batch'),
            new \WPR_Vendor\Twig\TwigFilter('column', '\\WPR_Vendor\\twig_array_column'),
            new \WPR_Vendor\Twig\TwigFilter('filter', '\\WPR_Vendor\\twig_array_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('map', '\\WPR_Vendor\\twig_array_map', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('reduce', '\\WPR_Vendor\\twig_array_reduce', ['needs_environment' => \true]),
            // string/array filters
            new \WPR_Vendor\Twig\TwigFilter('reverse', '\\WPR_Vendor\\twig_reverse_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('length', '\\WPR_Vendor\\twig_length_filter', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('slice', '\\WPR_Vendor\\twig_slice', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('first', '\\WPR_Vendor\\twig_first', ['needs_environment' => \true]),
            new \WPR_Vendor\Twig\TwigFilter('last', '\\WPR_Vendor\\twig_last', ['needs_environment' => \true]),
            // iteration and runtime
            new \WPR_Vendor\Twig\TwigFilter('default', '\\WPR_Vendor\\_twig_default_filter', ['node_class' => \WPR_Vendor\Twig\Node\Expression\Filter\DefaultFilter::class]),
            new \WPR_Vendor\Twig\TwigFilter('keys', '\\WPR_Vendor\\twig_get_array_keys_filter'),
        ];
    }
    public function getFunctions() : array
    {
        return [new \WPR_Vendor\Twig\TwigFunction('max', 'max'), new \WPR_Vendor\Twig\TwigFunction('min', 'min'), new \WPR_Vendor\Twig\TwigFunction('range', 'range'), new \WPR_Vendor\Twig\TwigFunction('constant', 'twig_constant'), new \WPR_Vendor\Twig\TwigFunction('cycle', 'twig_cycle'), new \WPR_Vendor\Twig\TwigFunction('random', 'twig_random', ['needs_environment' => \true]), new \WPR_Vendor\Twig\TwigFunction('date', 'twig_date_converter', ['needs_environment' => \true]), new \WPR_Vendor\Twig\TwigFunction('include', 'twig_include', ['needs_environment' => \true, 'needs_context' => \true, 'is_safe' => ['all']]), new \WPR_Vendor\Twig\TwigFunction('source', 'twig_source', ['needs_environment' => \true, 'is_safe' => ['all']])];
    }
    public function getTests() : array
    {
        return [new \WPR_Vendor\Twig\TwigTest('even', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\EvenTest::class]), new \WPR_Vendor\Twig\TwigTest('odd', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\OddTest::class]), new \WPR_Vendor\Twig\TwigTest('defined', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\DefinedTest::class]), new \WPR_Vendor\Twig\TwigTest('same as', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\SameasTest::class, 'one_mandatory_argument' => \true]), new \WPR_Vendor\Twig\TwigTest('none', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\NullTest::class]), new \WPR_Vendor\Twig\TwigTest('null', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\NullTest::class]), new \WPR_Vendor\Twig\TwigTest('divisible by', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\DivisiblebyTest::class, 'one_mandatory_argument' => \true]), new \WPR_Vendor\Twig\TwigTest('constant', null, ['node_class' => \WPR_Vendor\Twig\Node\Expression\Test\ConstantTest::class]), new \WPR_Vendor\Twig\TwigTest('empty', 'twig_test_empty'), new \WPR_Vendor\Twig\TwigTest('iterable', 'twig_test_iterable')];
    }
    public function getNodeVisitors() : array
    {
        return [new \WPR_Vendor\Twig\NodeVisitor\MacroAutoImportNodeVisitor()];
    }
    public function getOperators() : array
    {
        return [['not' => ['precedence' => 50, 'class' => \WPR_Vendor\Twig\Node\Expression\Unary\NotUnary::class], '-' => ['precedence' => 500, 'class' => \WPR_Vendor\Twig\Node\Expression\Unary\NegUnary::class], '+' => ['precedence' => 500, 'class' => \WPR_Vendor\Twig\Node\Expression\Unary\PosUnary::class]], ['or' => ['precedence' => 10, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\OrBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'and' => ['precedence' => 15, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\AndBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'b-or' => ['precedence' => 16, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\BitwiseOrBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'b-xor' => ['precedence' => 17, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\BitwiseXorBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'b-and' => ['precedence' => 18, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\BitwiseAndBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '==' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\EqualBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '!=' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\NotEqualBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '<=>' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\SpaceshipBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '<' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\LessBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '>' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\GreaterBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '>=' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\GreaterEqualBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '<=' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\LessEqualBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'not in' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\NotInBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'in' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\InBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'matches' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\MatchesBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'starts with' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\StartsWithBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'ends with' => ['precedence' => 20, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\EndsWithBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '..' => ['precedence' => 25, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\RangeBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '+' => ['precedence' => 30, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\AddBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '-' => ['precedence' => 30, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\SubBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '~' => ['precedence' => 40, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\ConcatBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '*' => ['precedence' => 60, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\MulBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '/' => ['precedence' => 60, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\DivBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '//' => ['precedence' => 60, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\FloorDivBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '%' => ['precedence' => 60, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\ModBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'is' => ['precedence' => 100, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], 'is not' => ['precedence' => 100, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_LEFT], '**' => ['precedence' => 200, 'class' => \WPR_Vendor\Twig\Node\Expression\Binary\PowerBinary::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_RIGHT], '??' => ['precedence' => 300, 'class' => \WPR_Vendor\Twig\Node\Expression\NullCoalesceExpression::class, 'associativity' => \WPR_Vendor\Twig\ExpressionParser::OPERATOR_RIGHT]]];
    }
}
namespace WPR_Vendor;

use WPR_Vendor\Twig\Environment;
use WPR_Vendor\Twig\Error\LoaderError;
use WPR_Vendor\Twig\Error\RuntimeError;
use WPR_Vendor\Twig\Extension\CoreExtension;
use WPR_Vendor\Twig\Extension\SandboxExtension;
use WPR_Vendor\Twig\Markup;
use WPR_Vendor\Twig\Source;
use WPR_Vendor\Twig\Template;
use WPR_Vendor\Twig\TemplateWrapper;
/**
 * Cycles over a value.
 *
 * @param \ArrayAccess|array $values
 * @param int                $position The cycle position
 *
 * @return string The next value in the cycle
 */
function twig_cycle($values, $position)
{
    if (!\is_array($values) && !$values instanceof \ArrayAccess) {
        return $values;
    }
    return $values[$position % \count($values)];
}
/**
 * Returns a random value depending on the supplied parameter type:
 * - a random item from a \Traversable or array
 * - a random character from a string
 * - a random integer between 0 and the integer parameter.
 *
 * @param \Traversable|array|int|float|string $values The values to pick a random item from
 * @param int|null                            $max    Maximum value used when $values is an int
 *
 * @throws RuntimeError when $values is an empty array (does not apply to an empty string which is returned as is)
 *
 * @return mixed A random value from the given sequence
 */
function twig_random(\WPR_Vendor\Twig\Environment $env, $values = null, $max = null)
{
    if (null === $values) {
        return null === $max ? \mt_rand() : \mt_rand(0, $max);
    }
    if (\is_int($values) || \is_float($values)) {
        if (null === $max) {
            if ($values < 0) {
                $max = 0;
                $min = $values;
            } else {
                $max = $values;
                $min = 0;
            }
        } else {
            $min = $values;
            $max = $max;
        }
        return \mt_rand($min, $max);
    }
    if (\is_string($values)) {
        if ('' === $values) {
            return '';
        }
        $charset = $env->getCharset();
        if ('UTF-8' !== $charset) {
            $values = \WPR_Vendor\twig_convert_encoding($values, 'UTF-8', $charset);
        }
        // unicode version of str_split()
        // split at all positions, but not after the start and not before the end
        $values = \preg_split('/(?<!^)(?!$)/u', $values);
        if ('UTF-8' !== $charset) {
            foreach ($values as $i => $value) {
                $values[$i] = \WPR_Vendor\twig_convert_encoding($value, $charset, 'UTF-8');
            }
        }
    }
    if (!\WPR_Vendor\twig_test_iterable($values)) {
        return $values;
    }
    $values = \WPR_Vendor\twig_to_array($values);
    if (0 === \count($values)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError('The random function cannot pick from an empty array.');
    }
    return $values[\array_rand($values, 1)];
}
/**
 * Converts a date to the given format.
 *
 *   {{ post.published_at|date("m/d/Y") }}
 *
 * @param \DateTimeInterface|\DateInterval|string $date     A date
 * @param string|null                             $format   The target format, null to use the default
 * @param \DateTimeZone|string|false|null         $timezone The target timezone, null to use the default, false to leave unchanged
 *
 * @return string The formatted date
 */
function twig_date_format_filter(\WPR_Vendor\Twig\Environment $env, $date, $format = null, $timezone = null)
{
    if (null === $format) {
        $formats = $env->getExtension(\WPR_Vendor\Twig\Extension\CoreExtension::class)->getDateFormat();
        $format = $date instanceof \DateInterval ? $formats[1] : $formats[0];
    }
    if ($date instanceof \DateInterval) {
        return $date->format($format);
    }
    return \WPR_Vendor\twig_date_converter($env, $date, $timezone)->format($format);
}
/**
 * Returns a new date object modified.
 *
 *   {{ post.published_at|date_modify("-1day")|date("m/d/Y") }}
 *
 * @param \DateTimeInterface|string $date     A date
 * @param string                    $modifier A modifier string
 *
 * @return \DateTimeInterface
 */
function twig_date_modify_filter(\WPR_Vendor\Twig\Environment $env, $date, $modifier)
{
    $date = \WPR_Vendor\twig_date_converter($env, $date, \false);
    return $date->modify($modifier);
}
/**
 * Converts an input to a \DateTime instance.
 *
 *    {% if date(user.created_at) < date('+2days') %}
 *      {# do something #}
 *    {% endif %}
 *
 * @param \DateTimeInterface|string|null  $date     A date or null to use the current time
 * @param \DateTimeZone|string|false|null $timezone The target timezone, null to use the default, false to leave unchanged
 *
 * @return \DateTimeInterface
 */
function twig_date_converter(\WPR_Vendor\Twig\Environment $env, $date = null, $timezone = null)
{
    // determine the timezone
    if (\false !== $timezone) {
        if (null === $timezone) {
            $timezone = $env->getExtension(\WPR_Vendor\Twig\Extension\CoreExtension::class)->getTimezone();
        } elseif (!$timezone instanceof \DateTimeZone) {
            $timezone = new \DateTimeZone($timezone);
        }
    }
    // immutable dates
    if ($date instanceof \DateTimeImmutable) {
        return \false !== $timezone ? $date->setTimezone($timezone) : $date;
    }
    if ($date instanceof \DateTimeInterface) {
        $date = clone $date;
        if (\false !== $timezone) {
            $date->setTimezone($timezone);
        }
        return $date;
    }
    if (null === $date || 'now' === $date) {
        return new \DateTime($date, \false !== $timezone ? $timezone : $env->getExtension(\WPR_Vendor\Twig\Extension\CoreExtension::class)->getTimezone());
    }
    $asString = (string) $date;
    if (\ctype_digit($asString) || !empty($asString) && '-' === $asString[0] && \ctype_digit(\substr($asString, 1))) {
        $date = new \DateTime('@' . $date);
    } else {
        $date = new \DateTime($date, $env->getExtension(\WPR_Vendor\Twig\Extension\CoreExtension::class)->getTimezone());
    }
    if (\false !== $timezone) {
        $date->setTimezone($timezone);
    }
    return $date;
}
/**
 * Replaces strings within a string.
 *
 * @param string             $str  String to replace in
 * @param array|\Traversable $from Replace values
 *
 * @return string
 */
function twig_replace_filter($str, $from)
{
    if (!\WPR_Vendor\twig_test_iterable($from)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The "replace" filter expects an array or "Traversable" as replace values, got "%s".', \is_object($from) ? \get_class($from) : \gettype($from)));
    }
    return \strtr($str, \WPR_Vendor\twig_to_array($from));
}
/**
 * Rounds a number.
 *
 * @param int|float $value     The value to round
 * @param int|float $precision The rounding precision
 * @param string    $method    The method to use for rounding
 *
 * @return int|float The rounded number
 */
function twig_round($value, $precision = 0, $method = 'common')
{
    if ('common' === $method) {
        return \round($value, $precision);
    }
    if ('ceil' !== $method && 'floor' !== $method) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError('The round filter only supports the "common", "ceil", and "floor" methods.');
    }
    return $method($value * 10 ** $precision) / 10 ** $precision;
}
/**
 * Number format filter.
 *
 * All of the formatting options can be left null, in that case the defaults will
 * be used.  Supplying any of the parameters will override the defaults set in the
 * environment object.
 *
 * @param mixed  $number       A float/int/string of the number to format
 * @param int    $decimal      the number of decimal points to display
 * @param string $decimalPoint the character(s) to use for the decimal point
 * @param string $thousandSep  the character(s) to use for the thousands separator
 *
 * @return string The formatted number
 */
function twig_number_format_filter(\WPR_Vendor\Twig\Environment $env, $number, $decimal = null, $decimalPoint = null, $thousandSep = null)
{
    $defaults = $env->getExtension(\WPR_Vendor\Twig\Extension\CoreExtension::class)->getNumberFormat();
    if (null === $decimal) {
        $decimal = $defaults[0];
    }
    if (null === $decimalPoint) {
        $decimalPoint = $defaults[1];
    }
    if (null === $thousandSep) {
        $thousandSep = $defaults[2];
    }
    return \number_format((float) $number, $decimal, $decimalPoint, $thousandSep);
}
/**
 * URL encodes (RFC 3986) a string as a path segment or an array as a query string.
 *
 * @param string|array $url A URL or an array of query parameters
 *
 * @return string The URL encoded value
 */
function twig_urlencode_filter($url)
{
    if (\is_array($url)) {
        return \http_build_query($url, '', '&', \PHP_QUERY_RFC3986);
    }
    return \rawurlencode($url);
}
/**
 * Merges an array with another one.
 *
 *  {% set items = { 'apple': 'fruit', 'orange': 'fruit' } %}
 *
 *  {% set items = items|merge({ 'peugeot': 'car' }) %}
 *
 *  {# items now contains { 'apple': 'fruit', 'orange': 'fruit', 'peugeot': 'car' } #}
 *
 * @param array|\Traversable $arr1 An array
 * @param array|\Traversable $arr2 An array
 *
 * @return array The merged array
 */
function twig_array_merge($arr1, $arr2)
{
    if (!\WPR_Vendor\twig_test_iterable($arr1)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The merge filter only works with arrays or "Traversable", got "%s" as first argument.', \gettype($arr1)));
    }
    if (!\WPR_Vendor\twig_test_iterable($arr2)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The merge filter only works with arrays or "Traversable", got "%s" as second argument.', \gettype($arr2)));
    }
    return \array_merge(\WPR_Vendor\twig_to_array($arr1), \WPR_Vendor\twig_to_array($arr2));
}
/**
 * Slices a variable.
 *
 * @param mixed $item         A variable
 * @param int   $start        Start of the slice
 * @param int   $length       Size of the slice
 * @param bool  $preserveKeys Whether to preserve key or not (when the input is an array)
 *
 * @return mixed The sliced variable
 */
function twig_slice(\WPR_Vendor\Twig\Environment $env, $item, $start, $length = null, $preserveKeys = \false)
{
    if ($item instanceof \Traversable) {
        while ($item instanceof \IteratorAggregate) {
            $item = $item->getIterator();
        }
        if ($start >= 0 && $length >= 0 && $item instanceof \Iterator) {
            try {
                return \iterator_to_array(new \LimitIterator($item, $start, null === $length ? -1 : $length), $preserveKeys);
            } catch (\OutOfBoundsException $e) {
                return [];
            }
        }
        $item = \iterator_to_array($item, $preserveKeys);
    }
    if (\is_array($item)) {
        return \array_slice($item, $start, $length, $preserveKeys);
    }
    $item = (string) $item;
    return (string) \mb_substr($item, $start, $length, $env->getCharset());
}
/**
 * Returns the first element of the item.
 *
 * @param mixed $item A variable
 *
 * @return mixed The first element of the item
 */
function twig_first(\WPR_Vendor\Twig\Environment $env, $item)
{
    $elements = \WPR_Vendor\twig_slice($env, $item, 0, 1, \false);
    return \is_string($elements) ? $elements : \current($elements);
}
/**
 * Returns the last element of the item.
 *
 * @param mixed $item A variable
 *
 * @return mixed The last element of the item
 */
function twig_last(\WPR_Vendor\Twig\Environment $env, $item)
{
    $elements = \WPR_Vendor\twig_slice($env, $item, -1, 1, \false);
    return \is_string($elements) ? $elements : \current($elements);
}
/**
 * Joins the values to a string.
 *
 * The separators between elements are empty strings per default, you can define them with the optional parameters.
 *
 *  {{ [1, 2, 3]|join(', ', ' and ') }}
 *  {# returns 1, 2 and 3 #}
 *
 *  {{ [1, 2, 3]|join('|') }}
 *  {# returns 1|2|3 #}
 *
 *  {{ [1, 2, 3]|join }}
 *  {# returns 123 #}
 *
 * @param array       $value An array
 * @param string      $glue  The separator
 * @param string|null $and   The separator for the last pair
 *
 * @return string The concatenated string
 */
function twig_join_filter($value, $glue = '', $and = null)
{
    if (!\WPR_Vendor\twig_test_iterable($value)) {
        $value = (array) $value;
    }
    $value = \WPR_Vendor\twig_to_array($value, \false);
    if (0 === \count($value)) {
        return '';
    }
    if (null === $and || $and === $glue) {
        return \implode($glue, $value);
    }
    if (1 === \count($value)) {
        return $value[0];
    }
    return \implode($glue, \array_slice($value, 0, -1)) . $and . $value[\count($value) - 1];
}
/**
 * Splits the string into an array.
 *
 *  {{ "one,two,three"|split(',') }}
 *  {# returns [one, two, three] #}
 *
 *  {{ "one,two,three,four,five"|split(',', 3) }}
 *  {# returns [one, two, "three,four,five"] #}
 *
 *  {{ "123"|split('') }}
 *  {# returns [1, 2, 3] #}
 *
 *  {{ "aabbcc"|split('', 2) }}
 *  {# returns [aa, bb, cc] #}
 *
 * @param string $value     A string
 * @param string $delimiter The delimiter
 * @param int    $limit     The limit
 *
 * @return array The split string as an array
 */
function twig_split_filter(\WPR_Vendor\Twig\Environment $env, $value, $delimiter, $limit = null)
{
    if (\strlen($delimiter) > 0) {
        return null === $limit ? \explode($delimiter, $value) : \explode($delimiter, $value, $limit);
    }
    if ($limit <= 1) {
        return \preg_split('/(?<!^)(?!$)/u', $value);
    }
    $length = \mb_strlen($value, $env->getCharset());
    if ($length < $limit) {
        return [$value];
    }
    $r = [];
    for ($i = 0; $i < $length; $i += $limit) {
        $r[] = \mb_substr($value, $i, $limit, $env->getCharset());
    }
    return $r;
}
// The '_default' filter is used internally to avoid using the ternary operator
// which costs a lot for big contexts (before PHP 5.4). So, on average,
// a function call is cheaper.
/**
 * @internal
 */
function _twig_default_filter($value, $default = '')
{
    if (\WPR_Vendor\twig_test_empty($value)) {
        return $default;
    }
    return $value;
}
/**
 * Returns the keys for the given array.
 *
 * It is useful when you want to iterate over the keys of an array:
 *
 *  {% for key in array|keys %}
 *      {# ... #}
 *  {% endfor %}
 *
 * @param array $array An array
 *
 * @return array The keys
 */
function twig_get_array_keys_filter($array)
{
    if ($array instanceof \Traversable) {
        while ($array instanceof \IteratorAggregate) {
            $array = $array->getIterator();
        }
        if ($array instanceof \Iterator) {
            $keys = [];
            $array->rewind();
            while ($array->valid()) {
                $keys[] = $array->key();
                $array->next();
            }
            return $keys;
        }
        $keys = [];
        foreach ($array as $key => $item) {
            $keys[] = $key;
        }
        return $keys;
    }
    if (!\is_array($array)) {
        return [];
    }
    return \array_keys($array);
}
/**
 * Reverses a variable.
 *
 * @param array|\Traversable|string $item         An array, a \Traversable instance, or a string
 * @param bool                      $preserveKeys Whether to preserve key or not
 *
 * @return mixed The reversed input
 */
function twig_reverse_filter(\WPR_Vendor\Twig\Environment $env, $item, $preserveKeys = \false)
{
    if ($item instanceof \Traversable) {
        return \array_reverse(\iterator_to_array($item), $preserveKeys);
    }
    if (\is_array($item)) {
        return \array_reverse($item, $preserveKeys);
    }
    $string = (string) $item;
    $charset = $env->getCharset();
    if ('UTF-8' !== $charset) {
        $item = \WPR_Vendor\twig_convert_encoding($string, 'UTF-8', $charset);
    }
    \preg_match_all('/./us', $item, $matches);
    $string = \implode('', \array_reverse($matches[0]));
    if ('UTF-8' !== $charset) {
        $string = \WPR_Vendor\twig_convert_encoding($string, $charset, 'UTF-8');
    }
    return $string;
}
/**
 * Sorts an array.
 *
 * @param array|\Traversable $array
 *
 * @return array
 */
function twig_sort_filter($array, $arrow = null)
{
    if ($array instanceof \Traversable) {
        $array = \iterator_to_array($array);
    } elseif (!\is_array($array)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The sort filter only works with arrays or "Traversable", got "%s".', \gettype($array)));
    }
    if (null !== $arrow) {
        \uasort($array, $arrow);
    } else {
        \asort($array);
    }
    return $array;
}
/**
 * @internal
 */
function twig_in_filter($value, $compare)
{
    if ($value instanceof \WPR_Vendor\Twig\Markup) {
        $value = (string) $value;
    }
    if ($compare instanceof \WPR_Vendor\Twig\Markup) {
        $compare = (string) $compare;
    }
    if (\is_string($compare)) {
        if (\is_string($value) || \is_int($value) || \is_float($value)) {
            return '' === $value || \false !== \strpos($compare, (string) $value);
        }
        return \false;
    }
    if (!\is_iterable($compare)) {
        return \false;
    }
    if (\is_object($value) || \is_resource($value)) {
        if (!\is_array($compare)) {
            foreach ($compare as $item) {
                if ($item === $value) {
                    return \true;
                }
            }
            return \false;
        }
        return \in_array($value, $compare, \true);
    }
    foreach ($compare as $item) {
        if (0 === \WPR_Vendor\twig_compare($value, $item)) {
            return \true;
        }
    }
    return \false;
}
/**
 * Compares two values using a more strict version of the PHP non-strict comparison operator.
 *
 * @see https://wiki.php.net/rfc/string_to_number_comparison
 * @see https://wiki.php.net/rfc/trailing_whitespace_numerics
 *
 * @internal
 */
function twig_compare($a, $b)
{
    // int <=> string
    if (\is_int($a) && \is_string($b)) {
        $bTrim = \trim($b, " \t\n\r\v\f");
        if (!\is_numeric($bTrim)) {
            return (string) $a <=> $b;
        }
        if ((int) $bTrim == $bTrim) {
            return $a <=> (int) $bTrim;
        } else {
            return (float) $a <=> (float) $bTrim;
        }
    }
    if (\is_string($a) && \is_int($b)) {
        $aTrim = \trim($a, " \t\n\r\v\f");
        if (!\is_numeric($aTrim)) {
            return $a <=> (string) $b;
        }
        if ((int) $aTrim == $aTrim) {
            return (int) $aTrim <=> $b;
        } else {
            return (float) $aTrim <=> (float) $b;
        }
    }
    // float <=> string
    if (\is_float($a) && \is_string($b)) {
        if (\is_nan($a)) {
            return 1;
        }
        $bTrim = \trim($b, " \t\n\r\v\f");
        if (!\is_numeric($bTrim)) {
            return (string) $a <=> $b;
        }
        return $a <=> (float) $bTrim;
    }
    if (\is_string($a) && \is_float($b)) {
        if (\is_nan($b)) {
            return 1;
        }
        $aTrim = \trim($a, " \t\n\r\v\f");
        if (!\is_numeric($aTrim)) {
            return $a <=> (string) $b;
        }
        return (float) $aTrim <=> $b;
    }
    // fallback to <=>
    return $a <=> $b;
}
/**
 * Returns a trimmed string.
 *
 * @return string
 *
 * @throws RuntimeError When an invalid trimming side is used (not a string or not 'left', 'right', or 'both')
 */
function twig_trim_filter($string, $characterMask = null, $side = 'both')
{
    if (null === $characterMask) {
        $characterMask = " \t\n\r\0\v";
    }
    switch ($side) {
        case 'both':
            return \trim($string, $characterMask);
        case 'left':
            return \ltrim($string, $characterMask);
        case 'right':
            return \rtrim($string, $characterMask);
        default:
            throw new \WPR_Vendor\Twig\Error\RuntimeError('Trimming side must be "left", "right" or "both".');
    }
}
/**
 * Removes whitespaces between HTML tags.
 *
 * @return string
 */
function twig_spaceless($content)
{
    return \trim(\preg_replace('/>\\s+</', '><', $content));
}
function twig_convert_encoding($string, $to, $from)
{
    if (!\function_exists('iconv')) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError('Unable to convert encoding: required function iconv() does not exist. You should install ext-iconv or symfony/polyfill-iconv.');
    }
    return \iconv($from, $to, $string);
}
/**
 * Returns the length of a variable.
 *
 * @param mixed $thing A variable
 *
 * @return int The length of the value
 */
function twig_length_filter(\WPR_Vendor\Twig\Environment $env, $thing)
{
    if (null === $thing) {
        return 0;
    }
    if (\is_scalar($thing)) {
        return \mb_strlen($thing, $env->getCharset());
    }
    if ($thing instanceof \Countable || \is_array($thing) || $thing instanceof \SimpleXMLElement) {
        return \count($thing);
    }
    if ($thing instanceof \Traversable) {
        return \iterator_count($thing);
    }
    if (\method_exists($thing, '__toString') && !$thing instanceof \Countable) {
        return \mb_strlen((string) $thing, $env->getCharset());
    }
    return 1;
}
/**
 * Converts a string to uppercase.
 *
 * @param string $string A string
 *
 * @return string The uppercased string
 */
function twig_upper_filter(\WPR_Vendor\Twig\Environment $env, $string)
{
    return \mb_strtoupper($string, $env->getCharset());
}
/**
 * Converts a string to lowercase.
 *
 * @param string $string A string
 *
 * @return string The lowercased string
 */
function twig_lower_filter(\WPR_Vendor\Twig\Environment $env, $string)
{
    return \mb_strtolower($string, $env->getCharset());
}
/**
 * Returns a titlecased string.
 *
 * @param string $string A string
 *
 * @return string The titlecased string
 */
function twig_title_string_filter(\WPR_Vendor\Twig\Environment $env, $string)
{
    if (null !== ($charset = $env->getCharset())) {
        return \mb_convert_case($string, \MB_CASE_TITLE, $charset);
    }
    return \ucwords(\strtolower($string));
}
/**
 * Returns a capitalized string.
 *
 * @param string $string A string
 *
 * @return string The capitalized string
 */
function twig_capitalize_string_filter(\WPR_Vendor\Twig\Environment $env, $string)
{
    $charset = $env->getCharset();
    return \mb_strtoupper(\mb_substr($string, 0, 1, $charset), $charset) . \mb_strtolower(\mb_substr($string, 1, null, $charset), $charset);
}
/**
 * @internal
 */
function twig_call_macro(\WPR_Vendor\Twig\Template $template, string $method, array $args, int $lineno, array $context, \WPR_Vendor\Twig\Source $source)
{
    if (!\method_exists($template, $method)) {
        $parent = $template;
        while ($parent = $parent->getParent($context)) {
            if (\method_exists($parent, $method)) {
                return $parent->{$method}(...$args);
            }
        }
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('Macro "%s" is not defined in template "%s".', \substr($method, \strlen('macro_')), $template->getTemplateName()), $lineno, $source);
    }
    return $template->{$method}(...$args);
}
/**
 * @internal
 */
function twig_ensure_traversable($seq)
{
    if ($seq instanceof \Traversable || \is_array($seq)) {
        return $seq;
    }
    return [];
}
/**
 * @internal
 */
function twig_to_array($seq, $preserveKeys = \true)
{
    if ($seq instanceof \Traversable) {
        return \iterator_to_array($seq, $preserveKeys);
    }
    if (!\is_array($seq)) {
        return $seq;
    }
    return $preserveKeys ? $seq : \array_values($seq);
}
/**
 * Checks if a variable is empty.
 *
 *    {# evaluates to true if the foo variable is null, false, or the empty string #}
 *    {% if foo is empty %}
 *        {# ... #}
 *    {% endif %}
 *
 * @param mixed $value A variable
 *
 * @return bool true if the value is empty, false otherwise
 */
function twig_test_empty($value)
{
    if ($value instanceof \Countable) {
        return 0 === \count($value);
    }
    if ($value instanceof \Traversable) {
        return !\iterator_count($value);
    }
    if (\is_object($value) && \method_exists($value, '__toString')) {
        return '' === (string) $value;
    }
    return '' === $value || \false === $value || null === $value || [] === $value;
}
/**
 * Checks if a variable is traversable.
 *
 *    {# evaluates to true if the foo variable is an array or a traversable object #}
 *    {% if foo is iterable %}
 *        {# ... #}
 *    {% endif %}
 *
 * @param mixed $value A variable
 *
 * @return bool true if the value is traversable
 */
function twig_test_iterable($value)
{
    return $value instanceof \Traversable || \is_array($value);
}
/**
 * Renders a template.
 *
 * @param array        $context
 * @param string|array $template      The template to render or an array of templates to try consecutively
 * @param array        $variables     The variables to pass to the template
 * @param bool         $withContext
 * @param bool         $ignoreMissing Whether to ignore missing templates or not
 * @param bool         $sandboxed     Whether to sandbox the template or not
 *
 * @return string The rendered template
 */
function twig_include(\WPR_Vendor\Twig\Environment $env, $context, $template, $variables = [], $withContext = \true, $ignoreMissing = \false, $sandboxed = \false)
{
    $alreadySandboxed = \false;
    $sandbox = null;
    if ($withContext) {
        $variables = \array_merge($context, $variables);
    }
    if ($isSandboxed = $sandboxed && $env->hasExtension(\WPR_Vendor\Twig\Extension\SandboxExtension::class)) {
        $sandbox = $env->getExtension(\WPR_Vendor\Twig\Extension\SandboxExtension::class);
        if (!($alreadySandboxed = $sandbox->isSandboxed())) {
            $sandbox->enableSandbox();
        }
        foreach (\is_array($template) ? $template : [$template] as $name) {
            // if a Template instance is passed, it might have been instantiated outside of a sandbox, check security
            if ($name instanceof \WPR_Vendor\Twig\TemplateWrapper || $name instanceof \WPR_Vendor\Twig\Template) {
                $name->unwrap()->checkSecurity();
            }
        }
    }
    try {
        $loaded = null;
        try {
            $loaded = $env->resolveTemplate($template);
        } catch (\WPR_Vendor\Twig\Error\LoaderError $e) {
            if (!$ignoreMissing) {
                throw $e;
            }
        }
        return $loaded ? $loaded->render($variables) : '';
    } finally {
        if ($isSandboxed && !$alreadySandboxed) {
            $sandbox->disableSandbox();
        }
    }
}
/**
 * Returns a template content without rendering it.
 *
 * @param string $name          The template name
 * @param bool   $ignoreMissing Whether to ignore missing templates or not
 *
 * @return string The template source
 */
function twig_source(\WPR_Vendor\Twig\Environment $env, $name, $ignoreMissing = \false)
{
    $loader = $env->getLoader();
    try {
        return $loader->getSourceContext($name)->getCode();
    } catch (\WPR_Vendor\Twig\Error\LoaderError $e) {
        if (!$ignoreMissing) {
            throw $e;
        }
    }
}
/**
 * Provides the ability to get constants from instances as well as class/global constants.
 *
 * @param string      $constant The name of the constant
 * @param object|null $object   The object to get the constant from
 *
 * @return string
 */
function twig_constant($constant, $object = null)
{
    if (null !== $object) {
        $constant = \get_class($object) . '::' . $constant;
    }
    return \constant($constant);
}
/**
 * Checks if a constant exists.
 *
 * @param string      $constant The name of the constant
 * @param object|null $object   The object to get the constant from
 *
 * @return bool
 */
function twig_constant_is_defined($constant, $object = null)
{
    if (null !== $object) {
        $constant = \get_class($object) . '::' . $constant;
    }
    return \defined($constant);
}
/**
 * Batches item.
 *
 * @param array $items An array of items
 * @param int   $size  The size of the batch
 * @param mixed $fill  A value used to fill missing items
 *
 * @return array
 */
function twig_array_batch($items, $size, $fill = null, $preserveKeys = \true)
{
    if (!\WPR_Vendor\twig_test_iterable($items)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The "batch" filter expects an array or "Traversable", got "%s".', \is_object($items) ? \get_class($items) : \gettype($items)));
    }
    $size = \ceil($size);
    $result = \array_chunk(\WPR_Vendor\twig_to_array($items, $preserveKeys), $size, $preserveKeys);
    if (null !== $fill && $result) {
        $last = \count($result) - 1;
        if ($fillCount = $size - \count($result[$last])) {
            for ($i = 0; $i < $fillCount; ++$i) {
                $result[$last][] = $fill;
            }
        }
    }
    return $result;
}
/**
 * Returns the attribute value for a given array/object.
 *
 * @param mixed  $object            The object or array from where to get the item
 * @param mixed  $item              The item to get from the array or object
 * @param array  $arguments         An array of arguments to pass if the item is an object method
 * @param string $type              The type of attribute (@see \Twig\Template constants)
 * @param bool   $isDefinedTest     Whether this is only a defined check
 * @param bool   $ignoreStrictCheck Whether to ignore the strict attribute check or not
 * @param int    $lineno            The template line where the attribute was called
 *
 * @return mixed The attribute value, or a Boolean when $isDefinedTest is true, or null when the attribute is not set and $ignoreStrictCheck is true
 *
 * @throws RuntimeError if the attribute does not exist and Twig is running in strict mode and $isDefinedTest is false
 *
 * @internal
 */
function twig_get_attribute(\WPR_Vendor\Twig\Environment $env, \WPR_Vendor\Twig\Source $source, $object, $item, array $arguments = [], $type = 'any', $isDefinedTest = \false, $ignoreStrictCheck = \false, $sandboxed = \false, int $lineno = -1)
{
    // array
    if ('method' !== $type) {
        $arrayItem = \is_bool($item) || \is_float($item) ? (int) $item : $item;
        if ((\is_array($object) || $object instanceof \ArrayObject) && (isset($object[$arrayItem]) || \array_key_exists($arrayItem, (array) $object)) || $object instanceof \ArrayAccess && isset($object[$arrayItem])) {
            if ($isDefinedTest) {
                return \true;
            }
            return $object[$arrayItem];
        }
        if ('array' === $type || !\is_object($object)) {
            if ($isDefinedTest) {
                return \false;
            }
            if ($ignoreStrictCheck || !$env->isStrictVariables()) {
                return;
            }
            if ($object instanceof \ArrayAccess) {
                $message = \sprintf('Key "%s" in object with ArrayAccess of class "%s" does not exist.', $arrayItem, \get_class($object));
            } elseif (\is_object($object)) {
                $message = \sprintf('Impossible to access a key "%s" on an object of class "%s" that does not implement ArrayAccess interface.', $item, \get_class($object));
            } elseif (\is_array($object)) {
                if (empty($object)) {
                    $message = \sprintf('Key "%s" does not exist as the array is empty.', $arrayItem);
                } else {
                    $message = \sprintf('Key "%s" for array with keys "%s" does not exist.', $arrayItem, \implode(', ', \array_keys($object)));
                }
            } elseif ('array' === $type) {
                if (null === $object) {
                    $message = \sprintf('Impossible to access a key ("%s") on a null variable.', $item);
                } else {
                    $message = \sprintf('Impossible to access a key ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
                }
            } elseif (null === $object) {
                $message = \sprintf('Impossible to access an attribute ("%s") on a null variable.', $item);
            } else {
                $message = \sprintf('Impossible to access an attribute ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
            }
            throw new \WPR_Vendor\Twig\Error\RuntimeError($message, $lineno, $source);
        }
    }
    if (!\is_object($object)) {
        if ($isDefinedTest) {
            return \false;
        }
        if ($ignoreStrictCheck || !$env->isStrictVariables()) {
            return;
        }
        if (null === $object) {
            $message = \sprintf('Impossible to invoke a method ("%s") on a null variable.', $item);
        } elseif (\is_array($object)) {
            $message = \sprintf('Impossible to invoke a method ("%s") on an array.', $item);
        } else {
            $message = \sprintf('Impossible to invoke a method ("%s") on a %s variable ("%s").', $item, \gettype($object), $object);
        }
        throw new \WPR_Vendor\Twig\Error\RuntimeError($message, $lineno, $source);
    }
    if ($object instanceof \WPR_Vendor\Twig\Template) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError('Accessing \\Twig\\Template attributes is forbidden.', $lineno, $source);
    }
    // object property
    if ('method' !== $type) {
        if (isset($object->{$item}) || \array_key_exists((string) $item, (array) $object)) {
            if ($isDefinedTest) {
                return \true;
            }
            if ($sandboxed) {
                $env->getExtension(\WPR_Vendor\Twig\Extension\SandboxExtension::class)->checkPropertyAllowed($object, $item, $lineno, $source);
            }
            return $object->{$item};
        }
    }
    static $cache = [];
    $class = \get_class($object);
    // object method
    // precedence: getXxx() > isXxx() > hasXxx()
    if (!isset($cache[$class])) {
        $methods = \get_class_methods($object);
        \sort($methods);
        $lcMethods = \array_map(function ($value) {
            return \strtr($value, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz');
        }, $methods);
        $classCache = [];
        foreach ($methods as $i => $method) {
            $classCache[$method] = $method;
            $classCache[$lcName = $lcMethods[$i]] = $method;
            if ('g' === $lcName[0] && 0 === \strpos($lcName, 'get')) {
                $name = \substr($method, 3);
                $lcName = \substr($lcName, 3);
            } elseif ('i' === $lcName[0] && 0 === \strpos($lcName, 'is')) {
                $name = \substr($method, 2);
                $lcName = \substr($lcName, 2);
            } elseif ('h' === $lcName[0] && 0 === \strpos($lcName, 'has')) {
                $name = \substr($method, 3);
                $lcName = \substr($lcName, 3);
                if (\in_array('is' . $lcName, $lcMethods)) {
                    continue;
                }
            } else {
                continue;
            }
            // skip get() and is() methods (in which case, $name is empty)
            if ($name) {
                if (!isset($classCache[$name])) {
                    $classCache[$name] = $method;
                }
                if (!isset($classCache[$lcName])) {
                    $classCache[$lcName] = $method;
                }
            }
        }
        $cache[$class] = $classCache;
    }
    $call = \false;
    if (isset($cache[$class][$item])) {
        $method = $cache[$class][$item];
    } elseif (isset($cache[$class][$lcItem = \strtr($item, 'ABCDEFGHIJKLMNOPQRSTUVWXYZ', 'abcdefghijklmnopqrstuvwxyz')])) {
        $method = $cache[$class][$lcItem];
    } elseif (isset($cache[$class]['__call'])) {
        $method = $item;
        $call = \true;
    } else {
        if ($isDefinedTest) {
            return \false;
        }
        if ($ignoreStrictCheck || !$env->isStrictVariables()) {
            return;
        }
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('Neither the property "%1$s" nor one of the methods "%1$s()", "get%1$s()"/"is%1$s()"/"has%1$s()" or "__call()" exist and have public access in class "%2$s".', $item, $class), $lineno, $source);
    }
    if ($isDefinedTest) {
        return \true;
    }
    if ($sandboxed) {
        $env->getExtension(\WPR_Vendor\Twig\Extension\SandboxExtension::class)->checkMethodAllowed($object, $method, $lineno, $source);
    }
    // Some objects throw exceptions when they have __call, and the method we try
    // to call is not supported. If ignoreStrictCheck is true, we should return null.
    try {
        $ret = $object->{$method}(...$arguments);
    } catch (\BadMethodCallException $e) {
        if ($call && ($ignoreStrictCheck || !$env->isStrictVariables())) {
            return;
        }
        throw $e;
    }
    return $ret;
}
/**
 * Returns the values from a single column in the input array.
 *
 * <pre>
 *  {% set items = [{ 'fruit' : 'apple'}, {'fruit' : 'orange' }] %}
 *
 *  {% set fruits = items|column('fruit') %}
 *
 *  {# fruits now contains ['apple', 'orange'] #}
 * </pre>
 *
 * @param array|Traversable $array An array
 * @param mixed             $name  The column name
 * @param mixed             $index The column to use as the index/keys for the returned array
 *
 * @return array The array of values
 */
function twig_array_column($array, $name, $index = null) : array
{
    if ($array instanceof \Traversable) {
        $array = \iterator_to_array($array);
    } elseif (!\is_array($array)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The column filter only works with arrays or "Traversable", got "%s" as first argument.', \gettype($array)));
    }
    return \array_column($array, $name, $index);
}
function twig_array_filter(\WPR_Vendor\Twig\Environment $env, $array, $arrow)
{
    if (!\WPR_Vendor\twig_test_iterable($array)) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The "filter" filter expects an array or "Traversable", got "%s".', \is_object($array) ? \get_class($array) : \gettype($array)));
    }
    if (!$arrow instanceof \Closure && $env->hasExtension('WPR_Vendor\\Twig\\Extension\\SandboxExtension') && $env->getExtension('WPR_Vendor\\Twig\\Extension\\SandboxExtension')->isSandboxed()) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError('The callable passed to "filter" filter must be a Closure in sandbox mode.');
    }
    if (\is_array($array)) {
        return \array_filter($array, $arrow, \ARRAY_FILTER_USE_BOTH);
    }
    // the IteratorIterator wrapping is needed as some internal PHP classes are \Traversable but do not implement \Iterator
    return new \CallbackFilterIterator(new \IteratorIterator($array), $arrow);
}
function twig_array_map(\WPR_Vendor\Twig\Environment $env, $array, $arrow)
{
    if (!$arrow instanceof \Closure && $env->hasExtension('WPR_Vendor\\Twig\\Extension\\SandboxExtension') && $env->getExtension('WPR_Vendor\\Twig\\Extension\\SandboxExtension')->isSandboxed()) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError('The callable passed to the "map" filter must be a Closure in sandbox mode.');
    }
    $r = [];
    foreach ($array as $k => $v) {
        $r[$k] = $arrow($v, $k);
    }
    return $r;
}
function twig_array_reduce(\WPR_Vendor\Twig\Environment $env, $array, $arrow, $initial = null)
{
    if (!$arrow instanceof \Closure && $env->hasExtension('WPR_Vendor\\Twig\\Extension\\SandboxExtension') && $env->getExtension('WPR_Vendor\\Twig\\Extension\\SandboxExtension')->isSandboxed()) {
        throw new \WPR_Vendor\Twig\Error\RuntimeError('The callable passed to the "reduce" filter must be a Closure in sandbox mode.');
    }
    if (!\is_array($array)) {
        if (!$array instanceof \Traversable) {
            throw new \WPR_Vendor\Twig\Error\RuntimeError(\sprintf('The "reduce" filter only works with arrays or "Traversable", got "%s" as first argument.', \gettype($array)));
        }
        $array = \iterator_to_array($array);
    }
    return \array_reduce($array, $arrow, $initial);
}
