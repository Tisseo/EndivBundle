<?php

namespace Tisseo\EndivBundle\Doctrine\Query;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\Lexer;
use \Doctrine\ORM\Query\SqlWalker;
use \Doctrine\ORM\Query\Parser;

/**
 * Unaccent plugin DQL wrapper
 *
 * @package Tisseo\OgiveBundle\Doctrine\DQL
 */
class FUnaccent extends FunctionNode
{
    private $string;

    public function getSql(SqlWalker $sqlWalker)
    {
        return 'ogive.f_unaccent(' . $this->string->dispatch($sqlWalker) . ")";
    }

    public function parse(Parser $parser)
    {
        $parser->match(Lexer::T_IDENTIFIER);
        $parser->match(Lexer::T_OPEN_PARENTHESIS);
        $this->string = $parser->StringPrimary();
        $parser->match(Lexer::T_CLOSE_PARENTHESIS);

    }

}
