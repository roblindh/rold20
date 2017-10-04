<?php
require_once 'RulesSrc/helpfuncs.php';
require_once 'RulesSrc/rolcalc.php';

echo "<h4>RoL Calc Test</h4>";

echo "<p>";
echo "10101010_2 = " . cExpressionParser::ConvertFromBase("10101010", 2) . "<br/>";
echo "10101010.101_2 = " . cExpressionParser::ConvertFromBase("10101010.101", 2) . "<br/>";
echo "-10101010_2 = " . cExpressionParser::ConvertFromBase("-10101010", 2) . "<br/>";
echo "-10101010.101_2 = " . cExpressionParser::ConvertFromBase("-10101010.101", 2) . "<br/>";
echo "</p>";
	
echo "<p>";
echo "123_8 = " . cExpressionParser::ConvertFromBase("123", 8) . "<br/>";
echo "123.3_8 = " . cExpressionParser::ConvertFromBase("123.3", 8) . "<br/>";
echo "-123_8 = " . cExpressionParser::ConvertFromBase("-123", 8) . "<br/>";
echo "-123.3_8 = " . cExpressionParser::ConvertFromBase("-123.3", 8) . "<br/>";
echo "</p>";
	
echo "<p>";
echo "1C5_16 = " . cExpressionParser::ConvertFromBase("1C5", 16) . "<br/>";
echo "1C5.C_16 = " . cExpressionParser::ConvertFromBase("1C5.C", 16) . "<br/>";
echo "-1C5_16 = " . cExpressionParser::ConvertFromBase("-1C5", 16) . "<br/>";
echo "-1C5.C_16 = " . cExpressionParser::ConvertFromBase("-1C5.C", 16) . "<br/>";
echo "</p>";
	
echo "<p>";
echo "123_10 = " . cExpressionParser::ConvertToBase(123, 2) . "<br/>";
echo "123.375_10 = " . cExpressionParser::ConvertToBase(123.375, 2) . "<br/>";
echo "-123_10 = " . cExpressionParser::ConvertToBase(-123, 2) . "<br/>";
echo "-123.375_10 = " . cExpressionParser::ConvertToBase(-123.375, 2) . "<br/>";
echo "</p>";
	
echo "<p>";
echo "123_10 = " . cExpressionParser::ConvertToBase(123, 8) . "<br/>";
echo "123.375_10 = " . cExpressionParser::ConvertToBase(123.375, 8) . "<br/>";
echo "-123_10 = " . cExpressionParser::ConvertToBase(-123, 8) . "<br/>";
echo "-123.375_10 = " . cExpressionParser::ConvertToBase(-123.375, 8) . "<br/>";
echo "</p>";
	
echo "<p>";
echo "123_10 = " . cExpressionParser::ConvertToBase(123, 16) . "<br/>";
echo "123.375_10 = " . cExpressionParser::ConvertToBase(123.375, 16) . "<br/>";
echo "-123_10 = " . cExpressionParser::ConvertToBase(-123, 16) . "<br/>";
echo "-123.375_10 = " . cExpressionParser::ConvertToBase(-123.375, 16) . "<br/>";
echo "</p>";
	
$parser = new cExpressionParser();
	
echo "<p>";
echo "12+34+56 = " . $parser->Evaluate("12+34+56") . "<br/>";
echo "12-34-56 = " . $parser->Evaluate("12-34-56") . "<br/>";
echo "12--34--56 = " . $parser->Evaluate("12--34--56") . "<br/>";
echo "12*34*56 = " . $parser->Evaluate("12*34*56") . "<br/>";
echo "12/34/56 = " . $parser->Evaluate("12/34/56") . "<br/>";
echo "12/(34/56) = " . $parser->Evaluate("12/(34/56)") . "<br/>";
echo "[12/(34/56)] = " . $parser->Evaluate("[12/(34/56)]") . "<br/>";
echo "|12-34-56| = " . $parser->Evaluate("|12-34-56|") . "<br/>";
echo "56%12 = " . $parser->Evaluate("56%12") . "<br/>";
echo "12^3 = " . $parser->Evaluate("12^3") . "<br/>";
echo "12^.3 = " . $parser->Evaluate("12^.3") . "<br/>";
echo "5! = " . $parser->Evaluate("5!") . "<br/>";
echo "</p>";
	
echo "<p>";
echo "1001_2 & 1100_2 = " . $parser->Evaluate("1001_2 & 1100_2") . "<br/>";
echo "1001_2 @ 1100_2 = " . $parser->Evaluate("1001_2 @ 1100_2") . "<br/>";
echo "1001_2 \\ 1100_2 = " . $parser->Evaluate("1001_2 \\ 1100_2") . "<br/>";
echo "~1001_2 = " . $parser->Evaluate("~1001_2") . "<br/>";
echo "1001_2 << 2 = " . $parser->Evaluate("1001_2 << 2") . "<br/>";
echo "1001_2 >> 2 = " . $parser->Evaluate("1001_2 >> 2") . "<br/>";
echo "</p>";

echo "<p>";
echo "5==3 = " . $parser->Evaluate("5==3") . "<br/>";
echo "5<>3 = " . $parser->Evaluate("5<>3") . "<br/>";
echo "5<3 = " . $parser->Evaluate("5<3") . "<br/>";
echo "5>3 = " . $parser->Evaluate("5>3") . "<br/>";
echo "5<=3 = " . $parser->Evaluate("5<=3") . "<br/>";
echo "5>=3 = " . $parser->Evaluate("5>=3") . "<br/>";
echo "1 AND 0 = " . $parser->Evaluate("1 AND 0") . "<br/>";
echo "1 OR 0 = " . $parser->Evaluate("1 OR 0") . "<br/>";
echo "1 XOR 0 = " . $parser->Evaluate("1 XOR 0") . "<br/>";
echo "NOT 1 = " . $parser->Evaluate("NOT 1") . "<br/>";
echo "5>3 ? 999 : 0 = " . $parser->Evaluate("5>3 ? 999 : 0") . "<br/>";
echo "MAX(5,3) = " . $parser->Evaluate("MAX(5,3)") . "<br/>";
echo "MIN(5,3) = " . $parser->Evaluate("MIN(5,3)") . "<br/>";
echo "</p>";

echo "<p>";
echo "3$6 = " . $parser->Evaluate("3$6") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "$2 = " . $parser->Evaluate("$2") . "<br/>";
echo "RAN(2) = " . $parser->Evaluate("RAN(2)") . "<br/>";
echo "DICE(3,6) = " . $parser->Evaluate("DICE(3,6)") . "<br/>";
echo "XDICE(5,1,6,3,2) /* Roll 1d6 5 times, add 3 highest, count 1s as 2s */ = " . $parser->Evaluate("XDICE(5,1,6,3,2)") . "<br/>";
echo "</p>";

echo "<p>";
echo "LG(10) = " . $parser->Evaluate("LG(10)") . "<br/>";
echo "LN(10) = " . $parser->Evaluate("LN(10)") . "<br/>";
echo "SINH(10) = " . $parser->Evaluate("SINH(10)") . "<br/>";
echo "COSH(10) = " . $parser->Evaluate("COSH(10)") . "<br/>";
echo "TANH(10) = " . $parser->Evaluate("TANH(10)") . "<br/>";
echo "COTH(10) = " . $parser->Evaluate("COTH(10)") . "<br/>";
echo "SIN(10) = " . $parser->Evaluate("SIN(10)") . "<br/>";
echo "COS(10) = " . $parser->Evaluate("COS(10)") . "<br/>";
echo "TAN(10) = " . $parser->Evaluate("TAN(10)") . "<br/>";
echo "ARCSIN(10) = " . $parser->Evaluate("ARCSIN(10)") . "<br/>";
echo "ARCCOS(10) = " . $parser->Evaluate("ARCCOS(10)") . "<br/>";
echo "ARCTAN(10) = " . $parser->Evaluate("ARCTAN(10)") . "<br/>";
echo "ARCTAN2(5,10) = " . $parser->Evaluate("ARCTAN2(5,10)") . "<br/>";
echo "ARCCOT(10) = " . $parser->Evaluate("ARCCOT(10)") . "<br/>";
echo "ARCCOT2(5,10) = " . $parser->Evaluate("ARCCOT2(5,10)") . "<br/>";
echo "SEC(10) = " . $parser->Evaluate("SEC(10)") . "<br/>";
echo "CSC(10) = " . $parser->Evaluate("CSC(10)") . "<br/>";
echo "COT(10) = " . $parser->Evaluate("COT(10)") . "<br/>";
echo "FRAC(12.34) = " . $parser->Evaluate("FRAC(12.34)") . "<br/>";
echo "RND(12.34) = " . $parser->Evaluate("RND(12.34)") . "<br/>";
echo "CEIL(12.34) = " . $parser->Evaluate("CEIL(12.34)") . "<br/>";
echo "FLOOR(12.34) = " . $parser->Evaluate("FLOOR(12.34)") . "<br/>";
echo "SQRT(10) = " . $parser->Evaluate("SQRT(10)") . "<br/>";
echo "SGN(-10) = " . $parser->Evaluate("SGN(-10)") . "<br/>";
echo "</p>";
	
echo "<p>";
echo "A=12 = " . $parser->Evaluate("A=12") . "<br/>";
echo "(A) = " . $parser->Evaluate("(A)") . "<br/>";
echo "B=A+34 = " . $parser->Evaluate("B=A+34") . "<br/>";
echo "A+B = " . $parser->Evaluate("A+B") . "<br/>";
echo "PI = " . $parser->Evaluate("PI") . "<br/>";
echo "E = " . $parser->Evaluate("E") . "<br/>";
echo "</p>";
	
echo "<p>";
echo "A=3 = " . $parser->Evaluate("A=3") . "<br/>";
echo "A+=4 = " . $parser->Evaluate("A+=4") . "<br/>";
echo "A-=5 = " . $parser->Evaluate("A-=5") . "<br/>";
echo "A*=6 = " . $parser->Evaluate("A*=6") . "<br/>";
echo "A/=7 = " . $parser->Evaluate("A/=7") . "<br/>";
echo "</p>";
	
echo "<p>";
echo "A=13 = " . $parser->Evaluate("A=13") . "<br/>";
echo "A%=10 = " . $parser->Evaluate("A%=10") . "<br/>";
echo "A&=6 = " . $parser->Evaluate("A&=6") . "<br/>";
echo "A@=4 = " . $parser->Evaluate("A@=4") . "<br/>";
echo "A\\=7 = " . $parser->Evaluate("A\\=7") . "<br/>";
echo "A{=4 = " . $parser->Evaluate("A{=4") . "<br/>";
echo "A}=3 = " . $parser->Evaluate("A}=3") . "<br/>";
echo "</p>";
?>
