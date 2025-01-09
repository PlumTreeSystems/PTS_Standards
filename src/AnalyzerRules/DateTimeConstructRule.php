<?php

namespace PTS_Standards\AnalyzerRules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

class DateTimeConstructRule implements \PHPStan\Rules\Rule
{
    const ERROR_MESSAGE = "Do not pass timestamp to DateTime constructor. Use 'setTimestamp()' instead";

    public function getNodeType(): string
	{
		return Node\Expr\New_::class;
	}

	public function processNode(Node $node, Scope $scope): array
	{
		if (!$node->class instanceof Node\Name) {
			return [];
		}

		if (
			$node->class->toString() == \DateTime::class
			|| $node->class->toString() == \DateTimeImmutable::class
		) {
			$datetimeArg = $node->getArgs()[0]->value;

			if ($datetimeArg instanceof Node\Expr\BinaryOp\Concat) {
				if ($datetimeArg->left->value == '@') {
					return [
						\PHPStan\Rules\RuleErrorBuilder::message(self::ERROR_MESSAGE)
							->identifier('dateTimeConstruct.timestamp')
							->build(),
					];
				}
			}
		}

		return [];
    }
}
