<?php

namespace PTS_Standards\AnalyzerRules;

use PhpParser\Node;
use PHPStan\Analyser\Scope;

class RegexRule implements \PHPStan\Rules\Rule
{
    const ERROR_MESSAGE = "Escape regular expression using 'preg_quote()'";

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
			$node->class->toString() !== 'MongoDB\\BSON\\Regex'
		) {
			return [];
		}

		$arg = $node->getArgs()[0]->value;
		if (! $this->isNodeValid($arg)) {
			return [
				\PHPStan\Rules\RuleErrorBuilder::message(self::ERROR_MESSAGE)
					->identifier('ptsStandard.regexRule')
					->build(),
			];
		}

		return [];
    }

	private function isNodeValid(Node $node): bool
	{
		if ($node instanceof Node\Expr\Variable) {
			return false;
		}

		if ($node instanceof Node\Expr\BinaryOp\Concat) {
			if (! $this->isNodeValid($node->right)) {
				return false;
			}

			return $this->isNodeValid($node->left);
		}

		return true;
	}
}
