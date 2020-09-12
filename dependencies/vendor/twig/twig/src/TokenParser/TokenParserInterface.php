<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR\Vendor\Twig\TokenParser;

use WPR\Vendor\Twig\Error\SyntaxError;
use WPR\Vendor\Twig\Node\Node;
use WPR\Vendor\Twig\Parser;
use WPR\Vendor\Twig\Token;
/**
 * Interface implemented by token parsers.
 *
 * @author Fabien Potencier <fabien@symfony.com>
 */
interface TokenParserInterface
{
    /**
     * Sets the parser associated with this token parser.
     */
    public function setParser(\WPR\Vendor\Twig\Parser $parser) : void;
    /**
     * Parses a token and returns a node.
     *
     * @return Node
     *
     * @throws SyntaxError
     */
    public function parse(\WPR\Vendor\Twig\Token $token);
    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string
     */
    public function getTag();
}
