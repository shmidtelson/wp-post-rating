<?php

/*
 * This file is part of Twig.
 *
 * (c) Fabien Potencier
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace WPR_Vendor\Twig\TokenParser;

use WPR_Vendor\Twig\Error\SyntaxError;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Parser;
use WPR_Vendor\Twig\Token;
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
    public function setParser(\WPR_Vendor\Twig\Parser $parser) : void;
    /**
     * Parses a token and returns a node.
     *
     * @return Node
     *
     * @throws SyntaxError
     */
    public function parse(\WPR_Vendor\Twig\Token $token);
    /**
     * Gets the tag name associated with this token parser.
     *
     * @return string
     */
    public function getTag();
}
