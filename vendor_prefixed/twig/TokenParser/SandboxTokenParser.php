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
use WPR_Vendor\Twig\Node\IncludeNode;
use WPR_Vendor\Twig\Node\Node;
use WPR_Vendor\Twig\Node\SandboxNode;
use WPR_Vendor\Twig\Node\TextNode;
use WPR_Vendor\Twig\Token;
/**
 * Marks a section of a template as untrusted code that must be evaluated in the sandbox mode.
 *
 *    {% sandbox %}
 *        {% include 'user.html' %}
 *    {% endsandbox %}
 *
 * @see https://twig.symfony.com/doc/api.html#sandbox-extension for details
 */
final class SandboxTokenParser extends \WPR_Vendor\Twig\TokenParser\AbstractTokenParser
{
    public function parse(\WPR_Vendor\Twig\Token $token) : \WPR_Vendor\Twig\Node\Node
    {
        $stream = $this->parser->getStream();
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        $body = $this->parser->subparse([$this, 'decideBlockEnd'], \true);
        $stream->expect(
            /* Token::BLOCK_END_TYPE */
            3
        );
        // in a sandbox tag, only include tags are allowed
        if (!$body instanceof \WPR_Vendor\Twig\Node\IncludeNode) {
            foreach ($body as $node) {
                if ($node instanceof \WPR_Vendor\Twig\Node\TextNode && \ctype_space($node->getAttribute('data'))) {
                    continue;
                }
                if (!$node instanceof \WPR_Vendor\Twig\Node\IncludeNode) {
                    throw new \WPR_Vendor\Twig\Error\SyntaxError('Only "include" tags are allowed within a "sandbox" section.', $node->getTemplateLine(), $stream->getSourceContext());
                }
            }
        }
        return new \WPR_Vendor\Twig\Node\SandboxNode($body, $token->getLine(), $this->getTag());
    }
    public function decideBlockEnd(\WPR_Vendor\Twig\Token $token) : bool
    {
        return $token->test('endsandbox');
    }
    public function getTag() : string
    {
        return 'sandbox';
    }
}
