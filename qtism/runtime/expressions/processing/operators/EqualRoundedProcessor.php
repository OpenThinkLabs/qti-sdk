<?php

namespace qtism\runtime\expressions\processing\operators;

use qtism\data\expressions\ExpressionCollection;
use qtism\data\expressions\BaseValue;
use qtism\data\expressions\operators\RoundTo;
use qtism\runtime\common\Utils as RuntimeUtils;
use qtism\data\expressions\operators\EqualRounded;
use qtism\data\expressions\Expression;
use qtism\runtime\expressions\processing\Utils;
use \InvalidArgumentException;

/**
 * The EqualRoundedProcessor class aims at processing EqualRounded operators.
 * 
 * From IMS QTI:
 * 
 * The equalRounded operator takes two sub-expressions which must both have single 
 * cardinality and have a numerical base-type. The result is a single boolean with 
 * a value of true if the two expressions are numerically equal after rounding and 
 * false if they are not. If either sub-expression is NULL then the operator results 
 * in NULL.
 * 
 * Numbers are rounded to a given number of significantFigures or decimalPlaces.
 * 
 * The number of figures to round to. If roundingMode= "significantFigures", the 
 * value of figures must be a non-zero positive integer. If roundingMode="decimalPlaces",
 * the value of figures must be an integer greater than or equal to zero.
 * 
 * For example, if significantFigures mode is used with figures=3, and the values 
 * are 3.175 and 3.183, the result is true, but for 3.175 and 3.1749, the result 
 * is false; if decimalPlaces mode is used with figures=2, 1.68572 and 1.69 the 
 * result is true, but for 1.68572 and 1.68432, the result is false.
 * 
 * @author Jérôme Bogaerts <jerome@taotesting.com>
 *
 */
class EqualRoundedProcessor extends OperatorProcessor {
	
	public function setExpression(Expression $expression) {
		if ($expression instanceof EqualRounded) {
			parent::setExpression($expression);
		}
		else {
			$msg = "The EqualRoundedProcessor class only processes EqualRounded QTI Data Model objects.";
			throw new InvalidArgumentException($msg);
		}
	}
	
	/**
	 * Process the EqualRounded operator.
	 * 
	 * @return boolean|null A boolean with a value of true if the two expressions are numerically equal after rounding and false if they are not. If either sub-expression is NULL, the operator results in NULL.
	 * @throws OperatorProcessingException
	 */
	public function process() {
		$operands = $this->getOperands();
		
		if ($operands->containsNull()) {
			return null;
		}
		
		if ($operands->exclusivelySingle() === false) {
			$msg = "The EqualRounded operator only accepts operands with a single cardinality.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_CARDINALITY);
		}
		
		if ($operands->exclusivelyNumeric() === false) {
			$msg = "The EqualRounded operator only accepts operands with an integer or float baseType.";
			throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_BASETYPE);
		}
		
		// delegate the rounding to the RoundTo operator.
		$expression = $this->getExpression();
		$roundingMode = $expression->getRoundingMode();
		$figures = $expression->getFigures();
		
		if (is_string($figures)) {
			// Variable reference to deal with.
			$state = $this->getState();
			$varName = Utils::sanitizeVariableRef($figures);
			$varValue = $state[$varName];
			
			if (is_null($varValue) === true) {
				$msg = "The variable with name '${varName}' could not be resolved.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::NONEXISTENT_VARIABLE);
			}
			else if (is_int($varValue) === false) {
				$msg = "The variable with name '${varName}' is not an integer.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::WRONG_VARIABLE_BASETYPE);
			}
			
			$figures = $varValue;
		}
		
		$rounded = new OperandsCollection(); // will contain the rounded operands.
		
		foreach ($operands as $operand) {
			$baseType = RuntimeUtils::inferBaseType($operand);
			$subExpression = new BaseValue($baseType, $operand);
			$roundToExpression = new RoundTo(new ExpressionCollection(array($subExpression)), $figures, $roundingMode);
			$roundToProcessor = new RoundToProcessor($roundToExpression, new OperandsCollection(array($operand)));
			
			try {
				$rounded[] = $roundToProcessor->process();
			}
			catch (OperatorProcessingException $e) {
				$msg = "An error occured while rounding '${operand}'.";
				throw new OperatorProcessingException($msg, $this, OperatorProcessingException::LOGIC_ERROR, $e);
			}
		}
		
		return $rounded[0] == $rounded[1];
	}
}