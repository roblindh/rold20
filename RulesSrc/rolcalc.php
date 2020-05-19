<?php

//TODO: Decide whether to use C-style or math-style
// =: Equality
// <>, !=: Inequality
// <, >, <=, >=: Less than, greater than...
// +, -: Addition, subtraction
// -: Negative
// *: Multiplication
// /: Division
// |...|: Absolute
// !: Factorial
// ~: Logical negation
// :=: Definition
// : Logical and
// : Logical or
// : Logical xor
// (): Precedence
// : Binary shift
// : Binary and
// : Binary or
// : Binary xor

define("ERR_NOERROR", 0);
define("ERR_PARENTHESIS", 1); // Unmatched parenthesis
define("ERR_OPERATOR", 2);  // Unknown operator
define("ERR_VARIABLE", 3);  // Undefined variable

define("ANGLE_DEGREES", 0);
define("ANGLE_RADIANS", 1);
define("ANGLE_GRADIENTS", 2);
define("ANGLE_CYCLES", 3);

define("TOKEN_NONE", 0);  // Undefined type
define("TOKEN_PREOP1", 1);  // Unary pre-operator
define("TOKEN_POSTOP1", 2);  // Unary post-operator
define("TOKEN_OP2", 3);   // Binary operator
define("TOKEN_OP3", 4);   // Ternary operator
define("TOKEN_FUNCTION", 5); // Function
define("TOKEN_ASSIGNMENT", 6); // Assignment operator
define("TOKEN_CONSTANT", 7); // Constant operand
define("TOKEN_VARIABLE", 8); // Variable operand
define("TOKEN_PARENTHESIS", 9); // Parenthesis
define("TOKEN_ILLEGAL", 10); // Illegal token

define("PRECISION", 12);

define("DELIMITERS", "([|^\$*/%+-=!&@\\~<>{}?:,");

class cExpressionParser {

    private $angleUnit = ANGLE_DEGREES;
    private $angleFactor = 1.0;
    private $currentExpression = "";
    private $currentPosition = 0;
    private $currentToken = "";
    private $currentTokenType = TOKEN_NONE;
    private $currentError = ERR_NOERROR;
    private $variables = array();

    public function __construct() {
        $this->variables["PI"] = M_PI;
        $this->variables["E"] = M_E;
        $this->variables["NULL"] = 0.0;
        $this->variables["TRUE"] = 1.0;
        $this->variables["FALSE"] = 0.0;
    }

    public function SetAngleUnit($unit) {
        $this->angleUnit = $unit;
        switch ($this->angleUnit) {
            case ANGLE_CYCLES:
                $this->angleFactor = 2.0 * M_PI;
                break;
            case ANGLE_DEGREES:
                $this->angleFactor = M_PI / 180.0;
                break;
            case ANGLE_GRADIENTS:
                $this->angleFactor = M_PI / 200.0;
                break;
            default:
                $this->angleFactor = 1.0;
                break;
        }
    }

    public function Evaluate($expression) {
        $result = NULL;

        $this->currentExpression = $expression;
        $this->currentPosition = 0;
        $this->currentToken = NULL;
        $this->currentTokenType = TOKEN_NONE;
        $this->currentError = ERR_NOERROR;

        if ($this->GetNextToken()) {
            $result = $this->EvaluateAssignment();
        }

        return $result;
    }

    static public function ConvertUnit($fromValue, $sFromUnit, $sToUnit) {
        $result = $fromValue;

        //TODO: Implement generic conversion method.

        return $result;
    }

    static public function ConvertToBase($value, $resultBase) {
        $sResult = "";

        if (($resultBase == 10) || ($value == 0.0)) {
            $sResult = $value;
        } else {
            $intPart = floor(abs($value));
            $fracPart = abs($value) - $intPart;

            while ($intPart > 0.0) {
                $temp = (int) ((int) $intPart % $resultBase);
                $sResult = chr($temp > 9 ? $temp - 10 + ord('A') : $temp + ord('0')) . $sResult;
                $intPart = floor($intPart / $resultBase);
            }

            if ($fracPart > 0.0)
                $sResult .= '.';

            for ($i = 0; ($fracPart != 0.0) && ($i <= PRECISION); $i++) {
                $fracPart *= $resultBase;
                $temp = (int) floor($fracPart);
                $fracPart -= $temp;
                $sResult .= chr($temp > 9 ? $temp - 10 + ord('A') : $temp + ord('0'));
            }

            if ($value < 0.0)
                $sResult = '-' . $sResult;

            $sResult .= "_";
            $sResult .= $resultBase;
        }

        return $sResult;
    }

    static public function ConvertFromBase($sValue, $valueBase) {
        $result = 0.0;
        $power = 1.0;
        $i = 0;

        if ($sValue[$i] == '-') {
            $i++;
            $sign = -1;
        } else {
            $sign = 1;
        }

        for (; $i < strlen($sValue); $i++) {
            if ($sValue[$i] == '.')
                break;
            $digit = ($sValue[$i] >= '0' && $sValue[$i] <= '9') ? (ord($sValue[$i]) - ord('0')) :
                    (($sValue[$i] >= 'A' && $sValue[$i] <= 'Z') ? (ord($sValue[$i]) - ord('A') + 10) : 0);
            $result = $result * $valueBase + $digit;
        }

        for ($i++; $i < strlen($sValue); $i++) {
            $digit = ($sValue[$i] >= '0' && $sValue[$i] <= '9') ? (ord($sValue[$i]) - ord('0')) :
                    (($sValue[$i] >= 'A' && $sValue[$i] <= 'Z') ? (ord($sValue[$i]) - ord('A') + 10) : 0);
            $power /= $valueBase;
            $result += $digit * $power;
        }

        return $sign * $result;
    }

    private function EvaluateAssignment() {
        $result = NULL;

        if ($this->currentTokenType == TOKEN_VARIABLE) {
            $sVarName = $this->currentToken;
            $oldPosition = $this->currentPosition;
            if ($this->GetNextToken() && ($this->currentTokenType == TOKEN_ASSIGNMENT)) {
                switch ($this->currentToken) {
                    case "=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = $this->EvaluateAssignment());
                        break;

                    case "+=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = $this->GetVariable($sVarName) + $this->EvaluateAssignment());
                        break;

                    case "-=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = $this->GetVariable($sVarName) - $this->EvaluateAssignment());
                        break;

                    case "*=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = $this->GetVariable($sVarName) * $this->EvaluateAssignment());
                        break;

                    case "/=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = $this->GetVariable($sVarName) / $this->EvaluateAssignment());
                        break;

                    case "%=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = (int) $this->GetVariable($sVarName) % (int) $this->EvaluateAssignment());
                        break;

                    case "&=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = (int) $this->GetVariable($sVarName) & (int) $this->EvaluateAssignment());
                        break;

                    case "@=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = (int) $this->GetVariable($sVarName) | (int) $this->EvaluateAssignment());
                        break;

                    case "\\=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = (int) $this->GetVariable($sVarName) ^ (int) $this->EvaluateAssignment());
                        break;

                    case "{=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = (int) $this->GetVariable($sVarName) << (int) $this->EvaluateAssignment());
                        break;

                    case "}=":
                        if ($this->GetNextToken())
                            $this->SetVariable($sVarName, $result = (int) $this->GetVariable($sVarName) >> (int) $this->EvaluateAssignment());
                        break;

                    default:
                        $result = NULL;
                        $this->currentError = ERR_OPERATOR;
                        break;
                }
            } else {
                $this->currentToken = $sVarName;
                $this->currentPosition = $oldPosition;
                $this->currentTokenType = TOKEN_VARIABLE;

                $result = $this->EvaluateConditional();
            }
        } else {
            $result = $this->EvaluateConditional();
        }

        return $result;
    }

    private function EvaluateConditional() {
        $result = NULL;

        $result = $this->EvaluateLogicalOr();
        while ($this->currentToken == "?") {
            if ($this->GetNextToken()) {
                if ((int) $result != 0) {
                    $result = $this->EvaluateConditional();
                    if ($this->currentToken == ":") {
                        if ($this->GetNextToken())
                            $this->EvaluateConditional();
                    }
                } else {
                    $this->EvaluateConditional();
                    if ($this->currentToken == ":") {
                        if ($this->GetNextToken())
                            $result = $this->EvaluateConditional();
                    }
                }
            } else
                break;
        }

        return $result;
    }

    private function EvaluateLogicalOr() {
        $result = NULL;

        $result = $this->EvaluateLogicalXor();
        while ($this->currentToken == "OR") {
            if ($this->GetNextToken()) {
                $result = (((int) $result != 0) || ((int) $this->EvaluateLogicalXor() != 0)) ? 1.0 : 0.0;
            } else
                break;
        }

        return $result;
    }

    private function EvaluateLogicalXor() {
        $result = NULL;

        $result = $this->EvaluateLogicalAnd();
        while ($this->currentToken == "XOR") {
            if ($this->GetNextToken()) {
                $result = (((int) $result != 0) ^ ((int) $this->EvaluateLogicalAnd() != 0)) ? 1.0 : 0.0;
            } else
                break;
        }

        return $result;
    }

    private function EvaluateLogicalAnd() {
        $result = NULL;

        $result = $this->EvaluateBinaryOr();
        while ($this->currentToken == "AND") {
            if ($this->GetNextToken()) {
                $result = (((int) $result != 0) && ((int) $this->EvaluateBinaryOr() != 0)) ? 1.0 : 0.0;
            } else
                break;
        }

        return $result;
    }

    private function EvaluateBinaryOr() {
        $result = NULL;

        $result = $this->EvaluateBinaryXor();
        while ($this->currentToken == "@") {
            if ($this->GetNextToken()) {
                $result = (int) $result | (int) $this->EvaluateBinaryXor();
            } else
                break;
        }

        return $result;
    }

    private function EvaluateBinaryXor() {
        $result = NULL;

        $result = $this->EvaluateBinaryAnd();
        while ($this->currentToken == "\\") {
            if ($this->GetNextToken()) {
                $result = (int) $result ^ (int) $this->EvaluateBinaryAnd();
            } else
                break;
        }

        return $result;
    }

    private function EvaluateBinaryAnd() {
        $result = NULL;

        $result = $this->EvaluateEquality();
        while ($this->currentToken == "&") {
            if ($this->GetNextToken()) {
                $result = (int) $result & (int) $this->EvaluateEquality();
            } else
                break;
        }

        return $result;
    }

    private function EvaluateEquality() {
        $result = NULL;

        $result = $this->EvaluateRelation();
        while (TRUE) {
            if ($this->currentToken == "==") {
                if ($this->GetNextToken()) {
                    $result = ($result == $this->EvaluateRelation()) ? 1.0 : 0.0;
                }
            } else if ($this->currentToken == "<>") {
                if ($this->GetNextToken()) {
                    $result = ($result != $this->EvaluateRelation()) ? 1.0 : 0.0;
                }
            } else
                break;
        }

        return $result;
    }

    private function EvaluateRelation() {
        $result = NULL;

        $result = $this->EvaluateShift();
        while (TRUE) {
            if ($this->currentToken == ">") {
                if ($this->GetNextToken()) {
                    $result = ($result > $this->EvaluateShift()) ? 1.0 : 0.0;
                }
            } else if ($this->currentToken == "<") {
                if ($this->GetNextToken()) {
                    $result = ($result < $this->EvaluateShift()) ? 1.0 : 0.0;
                }
            } else if ($this->currentToken == ">=") {
                if ($this->GetNextToken()) {
                    $result = ($result >= $this->EvaluateShift()) ? 1.0 : 0.0;
                }
            } else if ($this->currentToken == "<=") {
                if ($this->GetNextToken()) {
                    $result = ($result <= $this->EvaluateShift()) ? 1.0 : 0.0;
                }
            } else
                break;
        }

        return $result;
    }

    private function EvaluateShift() {
        $result = NULL;

        $result = $this->EvaluateTerms();
        while (TRUE) {
            if ($this->currentToken == "<<") {
                if ($this->GetNextToken()) {
                    $result = (int) $result << (int) $this->EvaluateTerms();
                }
            } else if ($this->currentToken == ">>") {
                if ($this->GetNextToken()) {
                    $result = (int) $result >> (int) $this->EvaluateTerms();
                }
            } else
                break;
        }

        return $result;
    }

    private function EvaluateTerms() {
        $result = NULL;

        $result = $this->EvaluateFactors();
        while (TRUE) {
            if ($this->currentToken == "+") {
                if ($this->GetNextToken()) {
                    $result += $this->EvaluateFactors();
                }
            } else if ($this->currentToken == "-") {
                if ($this->GetNextToken()) {
                    $result -= $this->EvaluateFactors();
                }
            } else
                break;
        }

        return $result;
    }

    private function EvaluateFactors() {
        $result = NULL;

        $result = $this->EvaluatePowers();
        while (TRUE) {
            if ($this->currentToken == "*") {
                if ($this->GetNextToken()) {
                    $result *= $this->EvaluatePowers();
                }
            } else if ($this->currentToken == "/") {
                if ($this->GetNextToken()) {
                    $result /= $this->EvaluatePowers();
                }
            } else if ($this->currentToken == "%") {
                if ($this->GetNextToken()) {
                    $result %= $this->EvaluatePowers();
                    // Math.Fmod?
                }
            } else
                break;
        }

        return $result;
    }

    private function EvaluatePowers() {
        $result = NULL;

        $result = $this->EvaluateUnary();
        while (TRUE) {
            if ($this->currentToken == "^") {
                if ($this->GetNextToken()) {
                    $result = pow($result, $this->EvaluatePowers());
                }
            } else if ($this->currentToken == "$") {
                if ($this->GetNextToken()) {
                    $temp = (int) $this->EvaluatePowers();
                    $i = ($result == NULL) ? 1 : (int) $result;
                    for ($result = 0.0; $i > 0; $i--)
                        $result += mt_rand(1, $temp);
                }
            } else
                break;
        }

        return $result;
    }

    private function EvaluateUnary() {
        $result = NULL;
        $temp = NULL;

        switch ($this->currentTokenType) {
            case TOKEN_PREOP1:
                switch ($this->currentToken) {
                    case "+":
                        if ($this->GetNextToken()) {
                            $result = $this->EvaluateUnary();
                        }
                        break;

                    case "-":
                        if ($this->GetNextToken()) {
                            $result = -$this->EvaluateUnary();
                        }
                        break;

                    case "~":
                        if ($this->GetNextToken()) {
                            $result = ~((int) $this->EvaluateUnary());
                        }
                        break;

                    case "NOT":
                        if ($this->GetNextToken()) {
                            $result = ($this->EvaluateUnary() == 0.0) ? 1.0 : 0.0;
                        }
                        break;

                    default:
                        break;
                }
                break;

            case TOKEN_FUNCTION:
                switch ($this->currentToken) {
                    case "LG":
                        if ($this->GetNextToken()) {
                            $result = log10($this->EvaluateUnary());
                        }
                        break;

                    case "LN":
                        if ($this->GetNextToken()) {
                            $result = log($this->EvaluateUnary());
                        }
                        break;

                    case "SINH":
                        if ($this->GetNextToken()) {
                            $result = sinh($this->EvaluateUnary());
                        }
                        break;

                    case "COSH":
                        if ($this->GetNextToken()) {
                            $result = cosh($this->EvaluateUnary());
                        }
                        break;

                    case "TANH":
                        if ($this->GetNextToken()) {
                            $result = tanh($this->EvaluateUnary());
                        }
                        break;

                    case "COTH":
                        if ($this->GetNextToken()) {
                            $result = 1.0 / tanh($this->EvaluateUnary());
                        }
                        break;

                    case "SIN":
                        if ($this->GetNextToken()) {
                            $result = sin($this->angleFactor * $this->EvaluateUnary());
                        }
                        break;

                    case "COS":
                        if ($this->GetNextToken()) {
                            $result = cos($this->angleFactor * $this->EvaluateUnary());
                        }
                        break;

                    case "TAN":
                        if ($this->GetNextToken()) {
                            $result = tan($this->angleFactor * $this->EvaluateUnary());
                        }
                        break;

                    case "ARCSIN":
                        if ($this->GetNextToken()) {
                            $result = asin($this->EvaluateUnary()) / $this->angleFactor;
                        }
                        break;

                    case "ARCCOS":
                        if ($this->GetNextToken()) {
                            $result = acos($this->EvaluateUnary()) / $this->angleFactor;
                        }
                        break;

                    case "ARCTAN2":
                        if ($this->GetNextToken()) {
                            if ($this->GetNextToken()) {
                                $temp = $this->EvaluateAssignment();
                                if ($this->GetNextToken()) {
                                    $result = atan2($temp, $this->EvaluateAssignment()) / $this->angleFactor;
                                    $this->GetNextToken();
                                }
                            }
                        }
                        break;

                    case "ARCTAN":
                        if ($this->GetNextToken()) {
                            $result = atan($this->EvaluateUnary()) / $this->angleFactor;
                        }
                        break;

                    case "ARCCOT2":
                        if ($this->GetNextToken()) {
                            if ($this->GetNextToken()) {
                                $temp = $this->EvaluateAssignment();
                                if ($this->GetNextToken()) {
                                    $result = atan2($this->EvaluateAssignment(), $temp) / $this->angleFactor;
                                    $this->GetNextToken();
                                }
                            }
                        }
                        break;

                    case "ARCCOT":
                        if ($this->GetNextToken()) {
                            $result = atan(1.0 / $this->EvaluateUnary()) / $this->angleFactor;
                        }
                        break;

                    case "FRAC":
                        if ($this->GetNextToken()) {
                            $result = $this->EvaluateUnary();
                            $result -= floor($result);
                        }
                        break;

                    case "RND":
                        if ($this->GetNextToken()) {
                            $result = round($this->EvaluateUnary());
                        }
                        break;

                    case "RAN":
                        if ($this->GetNextToken()) {
                            $result = mt_rand(1, (int) $this->EvaluateUnary());
                        }
                        break;

                    case "SEC":
                        if ($this->GetNextToken()) {
                            $result = 1.0 / cos($this->angleFactor * $this->EvaluateUnary());
                        }
                        break;

                    case "CSC":
                        if ($this->GetNextToken()) {
                            $result = 1.0 / sin($this->angleFactor * $this->EvaluateUnary());
                        }
                        break;

                    case "COT":
                        if ($this->GetNextToken()) {
                            $result = 1.0 / tan($this->angleFactor * $this->EvaluateUnary());
                        }
                        break;

                    case "CEIL":
                        if ($this->GetNextToken()) {
                            $result = ceil($this->EvaluateUnary());
                        }
                        break;

                    case "FLOOR":
                        if ($this->GetNextToken()) {
                            $result = floor($this->EvaluateUnary());
                        }
                        break;

                    case "SQRT":
                        if ($this->GetNextToken()) {
                            $result = sqrt($this->EvaluateUnary());
                        }
                        break;

                    case "SGN":
                        if ($this->GetNextToken()) {
                            $result = $this->EvaluateUnary();
                            $result = ($result < 0 ? -1 : ($result > 0 ? 1 : 0));
                        }
                        break;

                    case "MAX":
                        if ($this->GetNextToken()) {
                            if ($this->GetNextToken()) {
                                $temp = $this->EvaluateAssignment();
                                if ($this->GetNextToken()) {
                                    $result = max($temp, $this->EvaluateAssignment());
                                    $this->GetNextToken();
                                }
                            }
                        }
                        break;

                    case "MIN":
                        if ($this->GetNextToken()) {
                            if ($this->GetNextToken()) {
                                $temp = $this->EvaluateAssignment();
                                if ($this->GetNextToken()) {
                                    $result = min($temp, $this->EvaluateAssignment());
                                    $this->GetNextToken();
                                }
                            }
                        }
                        break;

                    case "DICE":
                        if ($this->GetNextToken()) {
                            if ($this->GetNextToken()) {
                                $count = (int) $this->EvaluateAssignment();
                                $count = ($count == NULL || $count <= 0) ? 1 : $count;
                                if ($this->GetNextToken()) {
                                    $temp = $this->EvaluateAssignment();
                                    for ($result = 0.0; $count > 0; $count--)
                                        $result += mt_rand(1, (int) $temp);
                                    $this->GetNextToken();
                                }
                            }
                        }
                        break;

                    case "XDICE":
                        $rollCount = 1;
                        $diceCount = 1;
                        $highCount = 1;
                        $minRoll = 1;
                        $lRolls = array();

                        if ($this->GetNextToken()) {
                            if ($this->GetNextToken()) {
                                $rollCount = (int) $this->EvaluateAssignment();
                                $rollCount = ($rollCount == NULL || $rollCount < 0) ? 1 : $rollCount;
                            }
                        } else
                            break;
                        if ($this->GetNextToken()) {
                            $diceCount = (int) $this->EvaluateAssignment();
                            $diceCount = ($diceCount == NULL || $diceCount < 0) ? 1 : $diceCount;
                        } else
                            break;
                        if ($this->GetNextToken()) {
                            $diceSize = (int) $this->EvaluateAssignment();
                            $diceSize = ($diceSize == NULL || $diceSize < 0) ? 1 : $diceSize;
                        } else
                            break;
                        if ($this->GetNextToken()) {
                            $highCount = (int) $this->EvaluateAssignment();
                            $highCount = ($highCount == NULL || $highCount < 0) ? $rollCount : min((int) $highCount, (int) $rollCount);
                        } else
                            break;
                        if ($this->GetNextToken()) {
                            $minRoll = (int) $this->EvaluateAssignment();
                            $minRoll = ($minRoll == NULL || $minRoll < 0) ? 1 : min((int) $minRoll, (int) $diceSize);
                        } else
                            break;
                        $this->GetNextToken();
                        for ($i = 0; $i < $rollCount; $i++) {
                            for ($j = 0, $roll = 0; $j < $diceCount; $roll += mt_rand(1, (int) $diceSize), $j++)
                                ;
                            $lRolls[] = max((int) $minRoll, $roll);
                        }
                        sort($lRolls);
                        for ($i = 0, $result = 0; $i < $highCount; $result += (int) $lRolls[(int) ($rollCount - (++$i))])
                            ;
                        break;

                    default:
                        break;
                }
                break;

            case TOKEN_CONSTANT:
                if ($i = strpos($this->currentToken, '_'))
                    $result = $this->ConvertFromBase(substr($this->currentToken, 0, $i), (int) substr($this->currentToken, $i + 1));
                else {
                    $result = (float) $this->currentToken;
                }
                $this->GetNextToken();
                break;

            case TOKEN_VARIABLE:
                $result = $this->GetVariable($this->currentToken);
                $this->GetNextToken();
                break;

            case TOKEN_PARENTHESIS:
                switch ($this->currentToken) {
                    case "(":
                        if ($this->GetNextToken()) {
                            $result = $this->EvaluateAssignment();
                        }
                        if ($this->currentToken != ")") {
                            $result = NULL;
                            $this->currentError = ERR_PARENTHESIS;
                        } else
                            $this->GetNextToken();
                        break;

                    case "[":
                        if ($this->GetNextToken()) {
                            $result = floor($this->EvaluateAssignment());
                        }
                        if ($this->currentToken != "]") {
                            $result = NULL;
                            $this->currentError = ERR_PARENTHESIS;
                        } else
                            $this->GetNextToken();
                        break;

                    case "|":
                        if ($this->GetNextToken()) {
                            $result = abs($this->EvaluateAssignment());
                        }
                        if ($this->currentToken != "|") {
                            $result = NULL;
                            $this->currentError = ERR_PARENTHESIS;
                        } else
                            $this->GetNextToken();
                        break;

                    default:
                        break;
                }
                break;

            default:
                break;
        }

        while ($this->currentTokenType == TOKEN_POSTOP1) {
            switch ($this->currentToken) {
                case "!":
                    $result = $this->Gamma($result);
                    break;

                default:
                    break;
            }
            $this->GetNextToken();
        }

        return $result;
    }

    private function GetNextToken() {
        $oldTokenType = $this->currentTokenType;
        $this->currentToken = "";

        if ($this->currentPosition >= strlen($this->currentExpression)) {
            $this->currentTokenType = TOKEN_NONE;
            return FALSE;
        }
        if ((($this->currentExpression[$this->currentPosition] >= '0') && ($this->currentExpression[$this->currentPosition] <= '9')) || ($this->currentExpression[$this->currentPosition] == '.')) {
            for ($i = $this->currentPosition + 1; ($i < strlen($this->currentExpression)) &&
                    ((($this->currentExpression[$i] >= '0') && ($this->currentExpression[$i] <= '9')) ||
                    (($this->currentExpression[$i] >= 'A') && ($this->currentExpression[$i] <= 'Z')) ||
                    ($this->currentExpression[$i] == '.') || ($this->currentExpression[$i] == '_')); $i++)
                ;
            $this->currentToken = substr($this->currentExpression, $this->currentPosition, $i - $this->currentPosition);
            $this->currentTokenType = TOKEN_CONSTANT;
            $this->currentPosition = $i;
        } else if (($this->currentExpression[$this->currentPosition] >= 'A') && ($this->currentExpression[$this->currentPosition] <= 'Z')) {
            for ($i = $this->currentPosition + 1; ($i < strlen($this->currentExpression)) &&
                    ((($this->currentExpression[$i] >= '0') && ($this->currentExpression[$i] <= '9')) ||
                    (($this->currentExpression[$i] >= 'A') && ($this->currentExpression[$i] <= 'Z'))); $i++)
                ;
            $this->currentToken = substr($this->currentExpression, $this->currentPosition, $i - $this->currentPosition);
            $this->currentTokenType = TOKEN_FUNCTION;
            $this->currentPosition = $i;
            switch ($this->currentToken) {
                case "LN":
                case "LG":
                case "SINH":
                case "COSH":
                case "TANH":
                case "COTH":
                case "SIN":
                case "COS":
                case "TAN":
                case "ARCSIN":
                case "ARCCOS":
                case "ARCTAN2":
                case "ARCTAN":
                case "ARCCOT2":
                case "ARCCOT":
                case "FRAC":
                case "RND":
                case "RAN":
                case "SEC":
                case "CSC":
                case "COT":
                case "CEIL":
                case "FLOOR":
                case "SQRT":
                case "SGN":
                case "MAX":
                case "MIN":
                case "DICE":
                case "XDICE":
                    $this->currentTokenType = TOKEN_FUNCTION;
                    break;
                case "OR":
                case "XOR":
                case "AND":
                    $this->currentTokenType = TOKEN_OP2;
                    break;
                case "NOT":
                    $this->currentTokenType = TOKEN_PREOP1;
                    break;
                default:
                    $this->currentTokenType = TOKEN_VARIABLE;
                    break;
            }
            // Store constants in variable list? Protected against change?
        } else if (($this->currentExpression[$this->currentPosition] == '(') || ($this->currentExpression[$this->currentPosition] == ')') || ($this->currentExpression[$this->currentPosition] == '[') || ($this->currentExpression[$this->currentPosition] == ']') || ($this->currentExpression[$this->currentPosition] == '|')) {
            $this->currentToken = $this->currentExpression[$this->currentPosition++];
            $this->currentTokenType = TOKEN_PARENTHESIS;
        } else {
            $this->currentToken = $this->currentExpression[$this->currentPosition++];
            $this->currentTokenType = TOKEN_OP2;
            switch ($this->currentToken) {
                case "=":
                    switch ($this->currentExpression[$this->currentPosition]) {
                        case '=':
                            $this->currentToken .= $this->currentExpression[$this->currentPosition++];
                            break;
                        default:
                            $this->currentTokenType = TOKEN_ASSIGNMENT;
                            break;
                    }
                    break;
                case "+":
                case "-":
                case "~":
                    switch ($this->currentExpression[$this->currentPosition]) {
                        case '=':
                            $this->currentToken .= $this->currentExpression[$this->currentPosition++];
                            $this->currentTokenType = TOKEN_ASSIGNMENT;
                            break;
                        default:
                            if ($oldTokenType == TOKEN_PREOP1 || $oldTokenType == TOKEN_OP2 ||
                                    $oldTokenType == TOKEN_OP3 || $oldTokenType == TOKEN_ASSIGNMENT ||
                                    $oldTokenType == TOKEN_PARENTHESIS || $oldTokenType == TOKEN_NONE)
                                $this->currentTokenType = TOKEN_PREOP1;
                            break;
                    }
                    break;
                case "*":
                case "/":
                case "%":
                case "&":
                case "@":
                case "\\":
                case "{":
                case "}":
                    switch ($this->currentExpression[$this->currentPosition]) {
                        case '=':
                            $this->currentToken .= $this->currentExpression[$this->currentPosition++];
                            $this->currentTokenType = TOKEN_ASSIGNMENT;
                            break;
                        default:
                            break;
                    }
                    break;
                case "^":
                case "$":
                    break;
                case "<":
                    switch ($this->currentExpression[$this->currentPosition]) {
                        case '>':
                        case '<':
                        case '=':
                            $this->currentToken .= $this->currentExpression[$this->currentPosition++];
                            break;
                        default:
                            break;
                    }
                    break;
                case ">":
                    switch ($this->currentExpression[$this->currentPosition]) {
                        case '>':
                        case '=':
                            $this->currentToken .= $this->currentExpression[$this->currentPosition++];
                            break;
                        default:
                            break;
                    }
                    break;
                case "?":
                    $this->currentTokenType = TOKEN_OP3;
                    break;
                case "!":
                    $this->currentTokenType = TOKEN_POSTOP1;
                    break;
            }
        }

        while (($this->currentPosition < strlen($this->currentExpression)) && ($this->currentExpression[$this->currentPosition] == ' '))
            $this->currentPosition++;

        return TRUE;
    }

    private function GetVariable($sVarName) {
        return $this->variables[$sVarName];
    }

    private function SetVariable($sVarName, $value) {
        $this->variables[$sVarName] = $value;
    }

    static private $goobie = 0.9189385332046727417803297;
    static private $gammaM = 6;
    static private $gammaN = 8;
    static private $gammaP1 = array(
        0.83333333333333101837e-1,
        -.277777777735865004e-2,
        0.793650576493454e-3,
        -.5951896861197e-3,
        0.83645878922e-3,
        -.1633436431e-2
    );
    static private $gammaP2 = array(
        -.42353689509744089647e5,
        -.20886861789269887364e5,
        -.87627102978521489560e4,
        -.20085274013072791214e4,
        -.43933044406002567613e3,
        -.50108693752970953015e2,
        -.67449507245925289918e1,
        0.0
    );
    static private $gammaQ2 = array(
        -.42353689509744090010e5,
        -.29803853309256649932e4,
        0.99403074150827709015e4,
        -.15286072737795220248e4,
        -.49902852662143904834e3,
        0.18949823415702801641e3,
        -.23081551524580124562e2,
        0.10000000000000000000e1
    );

    static public function Gamma($arg) {
        return ($arg == 0.0 ? 1.0 :
                ($arg < 0.0 ? exp(cExpressionParser::GammaNeg($arg)) * $arg :
                ($arg > 8.0 ? exp(cExpressionParser::GammaAsym($arg)) * $arg :
                (cExpressionParser::GammaGPos($arg) * $arg))));
    }

    static private function GammaAsym($arg) {
        for ($argSq = 1.0 / ($arg * $arg), $n = 0.0, $i = gammaM - 1; $i >= 0; $n = $n * $argSq + cExpressionParser::$gammaP1[$i--])
            ;

        return (($arg - 0.5) * log($arg) - $arg + $this->goobie + $n / $arg);
    }

    static private function GammaNeg($arg) {
        $arg = -$arg;
        $temp = sin(M_PI * $arg);
        if ($temp == 0.0) {
            return NULL;
        }
        $temp = abs($temp);

        return (-log($arg * cExpressionParser::GammaGPos($arg) * $temp / M_PI));
    }

    static private function GammaGPos($arg) {
        if ($arg < 2.0)
            return (cExpressionParser::GammaGPos($arg + 1.0) / $arg);
        if ($arg > 3.0)
            return (($arg - 1.0) * cExpressionParser::GammaGPos($arg - 1.0));
        $s = $arg - 2.0;
        for ($n = $d = 0.0, $i = cExpressionParser::$gammaN - 1; $i >= 0; $n = $n * $s + cExpressionParser::$gammaP2[$i], $d = $d * $s + cExpressionParser::$gammaQ2[$i--])
            ;

        return ($n / $d);
    }

}

/*
  short chexpr(void)
  {
  short otype;

  otype=type=OPTR;
  while(*strp) {
  if (*strp=='(' || *strp=='[' || *strp=='|')
  if (type==OPTR || type==UNOPTR)
  if (chparen()) return(1);
  else otype=OPND;
  else if (*strp=='|') {
  nparen--;
  return(0);
  } else
  return(1);
  getnxt();
  if (type==NONE) break;
  switch(otype) {
  case OPND:
  case VAR:	if (type!=OPTR && type!=ILGL)
  return(1);
  break;
  case UNOPTR:
  case OPTR:	if (type!=UNOPTR && type!=OPND && type!=VAR)
  return(1);
  break;
  }
  switch(type) {
  case OPND:	if (chnum()) return(1);
  case VAR:	if (*strp=='!') strp++;
  case UNOPTR: break;
  case OPTR:	if (*(strp-1)==',') return(0);
  if (token[strlen(token)-1]=='(') {
  if (!strncmp(token,"XDICE(",(size_t) 6)) {
  if (chexpr() || *(strp-1)!=',') return(1);
  getnxt();
  if ((type==OPND || type==VAR) && *strp==-16)
  strp++;
  else if (token[0]!=-16)
  return(1);
  getnxt(); if ((type!=OPND && type!=VAR) || *strp!=',') return(1);
  strp++; if (chexpr() || *(strp-1)!=',') return(1);
  if (chexpr() || *(strp-1)!=')') return(1);
  otype=OPND;
  } else {
  strp--;
  if (chparen()) return(1);
  else otype=OPND;
  }
  if (*strp=='!') strp++;
  }
  break;
  case ILGL:	if (*(strp-1)==')' || *(strp-1)==']') {
  nparen--;
  if (otype==OPND || otype==VAR)
  return(0);
  else
  return(1);
  } else return(1);
  break;
  }
  otype=type;
  }
  if (otype==OPND || otype==VAR)
  return(0);
  return(1);
  }

  short chparen(void)
  {
  if (*strp=='(') {
  strp++; nparen++;
  if (chexpr()) return(1);
  if (*(strp-1)!=')')
  return(1);
  if (*strp=='!') strp++;
  return(0);
  } else if (*strp=='[') {
  strp++; nparen++;
  if (chexpr()) return(1);
  if (*(strp-1)!=']')
  return(1);
  if (*strp=='!') strp++;
  return(0);
  } else if (*strp=='|') {
  strp++; nparen++;
  if (chexpr()) return(1);
  if (*strp!='|')
  return(1);
  strp++;
  if (*strp=='!') strp++;
  return(0);
  } else {
  getnxt();
  if (*strp=='!') strp++;
  if (type==VAR) return(0);
  if (type==OPND) return(chnum());
  return(1);
  }
  }

  short chnum(void)
  {
  char *tmpp,chr;
  short base,i;
  struct {
  unsigned dec : 1;
  unsigned exp : 1;
  unsigned bas : 1;
  } flags;

  flags.dec=flags.exp=flags.bas=0;

  if (tmpp=strrchr(token,'_')) {
  tmpp++; base=atoi(tmpp);
  if (base<2 || base>36) return(1);
  } else
  base=10;

  for (i=0;i<strlen(token);i++) {
  chr=token[i];
  if (chr=='.')
  if (flags.dec || flags.exp || flags.bas)
  return(1);
  else
  flags.dec=1;
  else if (chr=='E')
  if (flags.bas || flags.exp)
  return(1);
  else
  flags.exp=1;
  else if (chr=='_')
  if (flags.bas || flags.exp)
  return(1);
  else
  flags.bas=1;
  else if (chr=='+' || chr=='-') {
  if (token[i-1]!='E')
  return(1);
  } else if (chr>='0' && chr<='9') {
  if (chr-'0'>base-1 && !flags.bas)
  return(1);
  } else if (chr>='A' && chr<='Z') {
  if (chr-'A'>base-11 && !flags.bas)
  return(1);
  } else return(1);
  }

  return(0);
  }
 */
?>
