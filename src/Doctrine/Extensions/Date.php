<?php

declare(strict_types=1);

namespace App\Doctrine\Extensions;

use Doctrine\ORM\Query\AST\Functions\FunctionNode;
use Doctrine\ORM\Query\AST\Node;
use Doctrine\ORM\Query\Parser;
use Doctrine\ORM\Query\SqlWalker;
use Doctrine\ORM\Query\TokenType;

final class Date extends FunctionNode
{
    private Node|string|null $value;

    public function getSql(SqlWalker $sqlWalker): string
    {
        return 'DATE(' . $sqlWalker->walkArithmeticPrimary($this->value) . ')';
    }

    public function parse(Parser $parser): void
    {
        $parser->match(TokenType::T_IDENTIFIER);
        $parser->match(TokenType::T_OPEN_PARENTHESIS);
        $this->value = $parser->ArithmeticPrimary();
        $parser->match(TokenType::T_CLOSE_PARENTHESIS);
    }
}
