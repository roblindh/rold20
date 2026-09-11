<?php
declare(strict_types=1);

namespace App\Services\Entity;

class TraitEvaluator
{
    /**
     * Parse a RoL d20 trait string into structured trait items.
     * Example input: "AbilMod { Qual=Str; Type=Enh; Value=+4; Target=Wearer; } DefMod { Qual=DeC; Type=Arm; Value=+2; }"
     *
     * @return array<int, array{type: string, params: array<string, string>, raw: string}>
     */
    public static function parse(?string $traits): array
    {
        if (empty($traits)) {
            return [];
        }

        $result = [];
        $rawBlocks = explode('}', $traits);

        foreach ($rawBlocks as $block) {
            $block = trim($block);
            if ($block === '') {
                continue;
            }

            $openPos = strpos($block, '{');
            if ($openPos === false) {
                continue;
            }

            $type = trim(substr($block, 0, $openPos));
            $paramsStr = trim(substr($block, $openPos + 1));

            $params = [
                'Qual' => '',
                'Type' => 'Nil',
                'Value' => '',
                'Target' => 'Wearer',
            ];

            $pairs = explode(';', $paramsStr);
            foreach ($pairs as $pair) {
                $eqPos = strpos($pair, '=');
                if ($eqPos !== false) {
                    $k = trim(substr($pair, 0, $eqPos));
                    $v = trim(substr($pair, $eqPos + 1));
                    $params[$k] = $v;
                }
            }

            $result[] = [
                'type' => $type,
                'params' => $params,
                'raw' => $block . ' }',
            ];
        }

        return $result;
    }

    /**
     * Evaluate an expression using context variables.
     * Context may include: TL, RL, LVL, STR, CON, DEX, INT, WIS, CHA, STRMOD, CONMOD, DEXMOD, INTMOD, WISMOD, CHAMOD, SIZE, etc.
     */
    public static function evaluateExpression(string $expr, array $context = []): float|int|string
    {
        $expr = trim($expr);
        if ($expr === '') {
            return 0;
        }

        // If simple numeric string with optional sign
        if (preg_match('/^[+-]?\d+(\.\d+)?$/', $expr)) {
            return str_contains($expr, '.') ? (float)$expr : (int)$expr;
        }

        // Replace variable tokens
        $cleanExpr = $expr;

        // Replace Skill functions e.g. Skl(Ath) or Skl(12)
        if (isset($context['skills']) && is_array($context['skills'])) {
            $cleanExpr = preg_replace_callback('/Skl\(([^)]+)\)/i', function ($matches) use ($context) {
                $key = trim($matches[1]);
                if (is_numeric($key)) {
                    return (string)($context['skills'][(int)$key] ?? 0);
                }
                return (string)($context['skills'][$key] ?? 0);
            }, $cleanExpr);
        }

        // Replace direct context keys
        uksort($context, fn($a, $b) => strlen((string)$b) <=> strlen((string)$a));
        foreach ($context as $var => $val) {
            if (is_scalar($val) && is_string($var)) {
                $cleanExpr = preg_replace('/\b' . preg_quote($var, '/') . '\b/i', (string)$val, $cleanExpr);
            }
        }

        // Safe mathematical evaluation
        try {
            $sanitized = preg_replace('/[^0-9\+\-\*\/\(\)\.\s]/', '', $cleanExpr);
            if (empty(trim($sanitized))) {
                return $expr;
            }

            // Check if valid arithmetic syntax
            if (preg_match('/^[\d\s\+\-\*\/\(\)\.]+$/', $sanitized)) {
                // Evaluate using safe math parser
                $res = self::calculateMath($sanitized);
                return (floor($res) == $res) ? (int)$res : $res;
            }
        } catch (\Throwable $e) {
            // Fallback to raw string
        }

        return $expr;
    }

    /**
     * Simple safe arithmetic expression parser.
     */
    protected static function calculateMath(string $math): float
    {
        $math = trim($math);
        if ($math === '') {
            return 0.0;
        }

        // Handle parentheses recursively
        while (preg_match('/\(([^()]+)\)/', $math, $matches)) {
            $subVal = self::calculateMath($matches[1]);
            $math = str_replace($matches[0], (string)$subVal, $math);
        }

        // Evaluate operators in standard precedence: *, / then +, -
        // Tokenize into numbers and operators
        preg_match_all('/([+-]?\d+(?:\.\d+)?|[\+\-\*\/])/', $math, $tokens);
        $rawTokens = $tokens[0] ?? [];

        if (empty($rawTokens)) {
            return 0.0;
        }

        // Consolidate tokens (handle consecutive operators or negative numbers)
        $cleanTokens = [];
        $expectOperand = true;
        foreach ($rawTokens as $t) {
            if ($expectOperand) {
                if ($t === '+' || $t === '-') {
                    $cleanTokens[] = $t . '1';
                    $cleanTokens[] = '*';
                } else {
                    $cleanTokens[] = (float)$t;
                    $expectOperand = false;
                }
            } else {
                $cleanTokens[] = $t;
                $expectOperand = true;
            }
        }

        // First pass: multiplication and division
        $stage1 = [];
        $i = 0;
        while ($i < count($cleanTokens)) {
            $tok = $cleanTokens[$i];
            if ($tok === '*' || $tok === '/') {
                $prev = array_pop($stage1);
                $next = $cleanTokens[++$i] ?? 1.0;
                $val = ($tok === '*') ? ((float)$prev * (float)$next) : (((float)$next != 0) ? ((float)$prev / (float)$next) : 0.0);
                $stage1[] = $val;
            } else {
                $stage1[] = $tok;
            }
            $i++;
        }

        // Second pass: addition and subtraction
        $result = (float)($stage1[0] ?? 0.0);
        $i = 1;
        while ($i < count($stage1)) {
            $op = $stage1[$i];
            $next = (float)($stage1[++$i] ?? 0.0);
            if ($op === '+') {
                $result += $next;
            } elseif ($op === '-') {
                $result -= $next;
            }
            $i++;
        }

        return $result;
    }

    /**
     * Check if prerequisite string is met given entity context.
     * Example: "Str>=13 AND Dex>=15" or "Skl(Ath)>=4" or "Level>=3"
     */
    public static function evaluatePrerequisite(string $req, array $context = []): bool
    {
        $req = trim($req);
        if ($req === '' || $req === '1' || strcasecmp($req, 'true') === 0 || strcasecmp($req, 'none') === 0) {
            return true;
        }

        // Support 'OR' split first, then 'AND'
        $orClauses = preg_split('/\s+OR\s+/i', $req);
        foreach ($orClauses as $orClause) {
            $andClauses = preg_split('/\s+AND\s+/i', $orClause);
            $andPassed = true;

            foreach ($andClauses as $clause) {
                $clause = trim($clause);
                if ($clause === '') {
                    continue;
                }

                if (!self::evaluateSingleCondition($clause, $context)) {
                    $andPassed = false;
                    break;
                }
            }

            if ($andPassed) {
                return true;
            }
        }

        return false;
    }

    /**
     * Evaluate single condition e.g. "Str>=15", "TL>5", "Gender=Male"
     */
    protected static function evaluateSingleCondition(string $cond, array $context): bool
    {
        if (preg_match('/^([a-zA-Z0-9_\(\)]+)\s*(>=|<=|!=|==|=|>|<)\s*(.+)$/', $cond, $m)) {
            $leftExpr = trim($m[1]);
            $op = $m[2];
            $rightExpr = trim($m[3]);

            $leftVal = self::evaluateExpression($leftExpr, $context);
            $rightVal = self::evaluateExpression($rightExpr, $context);

            if (is_numeric($leftVal) && is_numeric($rightVal)) {
                $l = (float)$leftVal;
                $r = (float)$rightVal;
                return match ($op) {
                    '>=' => $l >= $r,
                    '<=' => $l <= $r,
                    '>' => $l > $r,
                    '<' => $l < $r,
                    '==' , '=' => abs($l - $r) < 0.0001,
                    '!=' => abs($l - $r) >= 0.0001,
                    default => false,
                };
            }

            // String comparison
            $lStr = strtolower((string)$leftVal);
            $rStr = strtolower((string)$rightVal);
            return match ($op) {
                '==' , '=' => $lStr === $rStr,
                '!=' => $lStr !== $rStr,
                default => false,
            };
        }

        return true;
    }

    /**
     * Apply parsed traits to the ModifierStackingEngine.
     *
     * @param array $traits Parsed traits list from self::parse()
     * @param ModifierStackingEngine $engine Target modifier engine
     * @param array $context Evaluation context (scores, levels, etc.)
     * @param string $sourceName Name of trait source (e.g. "Longsword +1", "Elven Heritage", "Iron Will")
     * @param string $currentScope Scope context ('wearer', 'wielder', 'carrier', 'item', 'character')
     */
    public static function applyTraitsToEngine(
        array $traits,
        ModifierStackingEngine $engine,
        array $context,
        string $sourceName = '',
        string $currentScope = 'wearer'
    ): void {
        foreach ($traits as $trait) {
            $type = $trait['type'];
            $params = $trait['params'];

            // 1. Check Target Scope
            $target = strtolower($params['Target'] ?? 'wearer');
            if (!self::isScopeApplicable($target, $currentScope)) {
                continue;
            }

            // 2. Check Prerequisites
            if (!empty($params['Req']) && !self::evaluatePrerequisite($params['Req'], $context)) {
                continue;
            }
            if (!empty($params['Prereq']) && !self::evaluatePrerequisite($params['Prereq'], $context)) {
                continue;
            }

            // 3. Evaluate Value
            $rawVal = $params['Value'] ?? '0';
            $val = self::evaluateExpression($rawVal, $context);
            $modType = $params['Type'] ?? 'Nil';
            $qual = $params['Qual'] ?? '';

            // 4. Map trait type & qual to target stat in ModifierStackingEngine
            self::registerTraitModifier($type, $qual, $val, $modType, $sourceName, $engine, $params);
        }
    }

    /**
     * Check if a trait target applies to the current equipment/entity context.
     */
    public static function isScopeApplicable(string $traitTarget, string $currentScope): bool
    {
        $traitTarget = strtolower($traitTarget);
        $currentScope = strtolower($currentScope);

        if ($currentScope === 'character') {
            return true;
        }

        if ($traitTarget === 'item' && $currentScope === 'item') {
            return true;
        }

        if ($currentScope === 'wielder') {
            return in_array($traitTarget, ['wielder', 'wearer', 'carrier', 'owner', 'all']);
        }

        if ($currentScope === 'wearer') {
            return in_array($traitTarget, ['wearer', 'carrier', 'owner', 'all']);
        }

        if ($currentScope === 'carrier') {
            return in_array($traitTarget, ['carrier', 'owner', 'all']);
        }

        return false;
    }

    /**
     * Map trait properties into engine modifier calls.
     */
    protected static function registerTraitModifier(
        string $traitType,
        string $qual,
        float|int|string $val,
        string $modType,
        string $sourceName,
        ModifierStackingEngine $engine,
        array $params
    ): void {
        $numVal = is_numeric($val) ? (float)$val : 0.0;

        switch ($traitType) {
            case 'AbilMod':
                $stat = match (strtoupper($qual)) {
                    'STR' => 'Str',
                    'CON' => 'Con',
                    'DEX' => 'Dex',
                    'INT' => 'Int',
                    'WIS' => 'Wis',
                    'CHA' => 'Cha',
                    default => $qual,
                };
                if ($stat) {
                    $engine->addModifier($stat, $numVal, $modType, $sourceName);
                }
                break;

            case 'HeaMod':
                if (in_array(strtoupper($qual), ['HP', 'SP', 'PP'])) {
                    $engine->addModifier(strtoupper($qual), $numVal, $modType, $sourceName);
                }
                break;

            case 'DefMod':
                $stat = match (strtoupper($qual)) {
                    'DEC' => 'DeC',
                    'FORT' => 'Fort',
                    'REF' => 'Ref',
                    'WILL' => 'Will',
                    'DR' => 'DR',
                    'MR' => 'MR',
                    'PARRY' => 'Par',
                    'ALL' => ['DeC', 'Fort', 'Ref', 'Will'],
                    'NDD' => ['Fort', 'Ref', 'Will'],
                    'ACIDRES' => 'AcidRes',
                    'COLDRES' => 'ColdRes',
                    'ELECTRICRES', 'ELECRES' => 'ElectricRes',
                    'FIRERES' => 'FireRes',
                    'NECROTICRES', 'NECRORES' => 'NecroticRes',
                    'RADIANTRES' => 'RadiantRes',
                    'SONICRES' => 'SonicRes',
                    default => $qual,
                };

                if (is_array($stat)) {
                    foreach ($stat as $s) {
                        $engine->addModifier($s, $numVal, $modType, $sourceName);
                    }
                } elseif ($stat) {
                    $engine->addModifier($stat, $numVal, $modType, $sourceName);
                }
                break;

            case 'AttMod':
                $stat = match (strtoupper($qual)) {
                    'ATTACK', 'ATT' => 'Att',
                    'DAMAGE', 'DMG' => 'Dmg',
                    'ATTSPD', 'SPEED' => 'AttSpd',
                    'MULTIATTACKPENRED' => 'MultiAttackPenRed',
                    'DMGDICE' => 'DmgDice',
                    default => 'Att_' . $qual,
                };
                $engine->addModifier($stat, $numVal, $modType, $sourceName);
                break;

            case 'SklMod':
                if (!empty($qual)) {
                    $engine->addModifier('Skill_' . $qual, $numVal, $modType, $sourceName);
                }
                break;

            case 'SpecMod':
                if (!empty($qual)) {
                    $engine->addModifier('Spec_' . $qual, $numVal, $modType, $sourceName);
                }
                break;

            case 'SpeedMod':
                $engine->addModifier('Speed', $numVal, $modType, $sourceName);
                break;

            case 'InitMod':
                $engine->addModifier('Init', $numVal, $modType, $sourceName);
                break;

            case 'Defense':
                if (str_ends_with(strtolower($qual), 'res')) {
                    $engine->addModifier($qual, $numVal, $modType, $sourceName);
                } elseif (strcasecmp($qual, 'Dodge') === 0) {
                    $engine->addModifier('Dodge', $numVal, 'Ddg', $sourceName);
                } elseif (strcasecmp($qual, 'CritRes') === 0) {
                    $engine->addModifier('CritRes', $numVal, $modType, $sourceName);
                }
                break;
        }
    }
}
