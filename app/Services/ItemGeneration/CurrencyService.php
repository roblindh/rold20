<?php

namespace App\Services\ItemGeneration;

class CurrencyService
{
    public const RATE_CP = 0.1;   // 1 cp = 0.1 sp
    public const RATE_SP = 1.0;   // 1 sp = 1.0 sp (base)
    public const RATE_GP = 10.0;  // 1 gp = 10 sp
    public const RATE_PP = 100.0; // 1 pp = 100 sp (10 gp)

    public const WEIGHT_PER_COIN_KG = 0.01; // 100 coins = 1.0 kg (0.01 kg / 10g per coin)
    public const COINS_PER_KG = 100;

    /**
     * Parse wallet from characters.Coins or fallback to Wealth in silver pieces.
     *
     * @param mixed $coinsData JSON string, array, or null
     * @param int|float|null $wealthSp Fallback wealth in silver pieces
     * @return array{cp: int, sp: int, gp: int, pp: int}
     */
    public static function parseWallet(mixed $coinsData, int|float|null $wealthSp = null): array
    {
        $wallet = ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0];

        if (is_array($coinsData)) {
            $wallet['cp'] = max(0, (int)($coinsData['cp'] ?? 0));
            $wallet['sp'] = max(0, (int)($coinsData['sp'] ?? 0));
            $wallet['gp'] = max(0, (int)($coinsData['gp'] ?? 0));
            $wallet['pp'] = max(0, (int)($coinsData['pp'] ?? 0));
            return $wallet;
        }

        if (is_string($coinsData) && !empty(trim($coinsData))) {
            $decoded = json_decode($coinsData, true);
            if (is_array($decoded)) {
                $wallet['cp'] = max(0, (int)($decoded['cp'] ?? 0));
                $wallet['sp'] = max(0, (int)($decoded['sp'] ?? 0));
                $wallet['gp'] = max(0, (int)($decoded['gp'] ?? 0));
                $wallet['pp'] = max(0, (int)($decoded['pp'] ?? 0));
                return $wallet;
            }
        }

        // Fallback: If no coins array, convert wealthSp into silver coins
        if ($wealthSp !== null && $wealthSp > 0) {
            $wallet['sp'] = (int)round($wealthSp);
        }

        return $wallet;
    }

    /**
     * Convert wallet array to total Silver Pieces (sp).
     *
     * @param array $coins
     * @return float
     */
    public static function coinsToSp(array $coins): float
    {
        $cp = max(0, (int)($coins['cp'] ?? 0));
        $sp = max(0, (int)($coins['sp'] ?? 0));
        $gp = max(0, (int)($coins['gp'] ?? 0));
        $pp = max(0, (int)($coins['pp'] ?? 0));

        $total = ($cp * self::RATE_CP) + ($sp * self::RATE_SP) + ($gp * self::RATE_GP) + ($pp * self::RATE_PP);
        return round($total, 2);
    }

    /**
     * Convert total SP into structured coins.
     *
     * @param float $sp
     * @param bool $optimal If true, breaks into largest denominations (pp -> gp -> sp -> cp)
     * @return array{cp: int, sp: int, gp: int, pp: int}
     */
    public static function spToCoins(float $sp, bool $optimal = true): array
    {
        if ($sp <= 0) {
            return ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0];
        }

        if (!$optimal) {
            $wholeSp = (int)floor($sp);
            $fractionSp = $sp - $wholeSp;
            $cp = (int)round($fractionSp * 10);
            return ['cp' => $cp, 'sp' => $wholeSp, 'gp' => 0, 'pp' => 0];
        }

        $copperUnits = (int)round($sp * 10); // 10 copper units per 1 sp

        $pp = (int)intdiv($copperUnits, 1000);
        $rem1 = $copperUnits % 1000;

        $gp = (int)intdiv($rem1, 100);
        $rem2 = $rem1 % 100;

        $spVal = (int)intdiv($rem2, 10);
        $cp = $rem2 % 10;

        return [
            'cp' => $cp,
            'sp' => $spVal,
            'gp' => $gp,
            'pp' => $pp,
        ];
    }

    /**
     * Calculate total number of coins in wallet.
     *
     * @param array $coins
     * @return int
     */
    public static function countTotalCoins(array $coins): int
    {
        return max(0, (int)($coins['cp'] ?? 0))
             + max(0, (int)($coins['sp'] ?? 0))
             + max(0, (int)($coins['gp'] ?? 0))
             + max(0, (int)($coins['pp'] ?? 0));
    }

    /**
     * Calculate total weight of coins in kilograms.
     *
     * @param array $coins
     * @return float Weight in kg (e.g. 100 coins = 1.0 kg)
     */
    public static function calculateCoinWeight(array $coins): float
    {
        return round(self::countTotalCoins($coins) * self::WEIGHT_PER_COIN_KG, 2);
    }

    /**
     * Add coins to a wallet.
     *
     * @param array $wallet
     * @param array $coinsToAdd
     * @return array{cp: int, sp: int, gp: int, pp: int}
     */
    public static function addCoins(array $wallet, array $coinsToAdd): array
    {
        $w = self::parseWallet($wallet);
        $a = self::parseWallet($coinsToAdd);

        return [
            'cp' => $w['cp'] + $a['cp'],
            'sp' => $w['sp'] + $a['sp'],
            'gp' => $w['gp'] + $a['gp'],
            'pp' => $w['pp'] + $a['pp'],
        ];
    }

    /**
     * Condense wallet coins into highest denominations (saves weight).
     *
     * @param array $wallet
     * @return array{cp: int, sp: int, gp: int, pp: int}
     */
    public static function optimizeWallet(array $wallet): array
    {
        $totalSp = self::coinsToSp($wallet);
        return self::spToCoins($totalSp, true);
    }

    /**
     * Deduct cost in silver pieces from a wallet with automatic change-making.
     *
     * @param array $wallet Current coin purse
     * @param float $costSp Cost to spend in silver pieces
     * @param bool $autoBreak Whether to break larger coins if exact change isn't available
     * @return array{
     *     success: bool,
     *     wallet: array{cp: int, sp: int, gp: int, pp: int},
     *     spent: array{cp: int, sp: int, gp: int, pp: int},
     *     change: array{cp: int, sp: int, gp: int, pp: int},
     *     error: ?string
     * }
     */
    public static function deductCost(array $wallet, float $costSp, bool $autoBreak = true): array
    {
        $w = self::parseWallet($wallet);
        $totalAvailableSp = self::coinsToSp($w);

        if ($costSp <= 0) {
            return [
                'success' => true,
                'wallet' => $w,
                'spent' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                'change' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                'error' => null,
            ];
        }

        if ($totalAvailableSp < $costSp) {
            return [
                'success' => false,
                'wallet' => $w,
                'spent' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                'change' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                'error' => "Insufficient funds: required {$costSp} sp, available {$totalAvailableSp} sp.",
            ];
        }

        $costCu = (int)round($costSp * 10); // in copper units (1 sp = 10 Cu)
        $spent = ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0];
        $change = ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0];

        // Fast path: Exact single-denomination payment if available
        if ($costSp >= 100.0 && fmod($costSp, 100.0) === 0.0 && $w['pp'] >= (int)($costSp / 100.0)) {
            $ppUse = (int)($costSp / 100.0);
            $w['pp'] -= $ppUse;
            $spent['pp'] += $ppUse;
            return [
                'success' => true,
                'wallet' => $w,
                'spent' => $spent,
                'change' => $change,
                'error' => null,
            ];
        }

        if ($costSp >= 10.0 && fmod($costSp, 10.0) === 0.0 && $w['gp'] >= (int)($costSp / 10.0)) {
            $gpUse = (int)($costSp / 10.0);
            $w['gp'] -= $gpUse;
            $spent['gp'] += $gpUse;
            return [
                'success' => true,
                'wallet' => $w,
                'spent' => $spent,
                'change' => $change,
                'error' => null,
            ];
        }

        if ($costSp == floor($costSp) && $w['sp'] >= (int)$costSp) {
            $spUse = (int)$costSp;
            $w['sp'] -= $spUse;
            $spent['sp'] += $spUse;
            return [
                'success' => true,
                'wallet' => $w,
                'spent' => $spent,
                'change' => $change,
                'error' => null,
            ];
        }

        if ($costSp < 1.0 && $w['cp'] >= (int)round($costSp * 10)) {
            $cpUse = (int)round($costSp * 10);
            $w['cp'] -= $cpUse;
            $spent['cp'] += $cpUse;
            return [
                'success' => true,
                'wallet' => $w,
                'spent' => $spent,
                'change' => $change,
                'error' => null,
            ];
        }

        $remCu = $costCu;

        // 1. Try to pay using exact coins from highest denomination downwards
        // Platinum (1 pp = 1000 Cu)
        $ppNeed = (int)intdiv($remCu, 1000);
        $ppUse = min($w['pp'], $ppNeed);
        $spent['pp'] += $ppUse;
        $w['pp'] -= $ppUse;
        $remCu -= ($ppUse * 1000);

        // Gold (1 gp = 100 Cu)
        $gpNeed = (int)intdiv($remCu, 100);
        $gpUse = min($w['gp'], $gpNeed);
        $spent['gp'] += $gpUse;
        $w['gp'] -= $gpUse;
        $remCu -= ($gpUse * 100);

        // Silver (1 sp = 10 Cu)
        $spNeed = (int)intdiv($remCu, 10);
        $spUse = min($w['sp'], $spNeed);
        $spent['sp'] += $spUse;
        $w['sp'] -= $spUse;
        $remCu -= ($spUse * 10);

        // Copper (1 cp = 1 Cu)
        $cpUse = min($w['cp'], $remCu);
        $spent['cp'] += $cpUse;
        $w['cp'] -= $cpUse;
        $remCu -= $cpUse;

        // If paid completely without breaking higher coins
        if ($remCu <= 0) {
            return [
                'success' => true,
                'wallet' => $w,
                'spent' => $spent,
                'change' => $change,
                'error' => null,
            ];
        }

        if (!$autoBreak) {
            return [
                'success' => false,
                'wallet' => self::parseWallet($wallet),
                'spent' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                'change' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                'error' => "Exact change not available and autoBreak is disabled.",
            ];
        }

        // 2. Break the smallest available higher denomination coin to cover $remCu
        // Check silver (10 Cu)
        if ($remCu <= 10 && $w['sp'] >= 1) {
            $w['sp'] -= 1;
            $spent['sp'] += 1;
            $changeCu = 10 - $remCu;
            $w['cp'] += $changeCu;
            $change['cp'] += $changeCu;
            $remCu = 0;
        }
        // Check gold (100 Cu)
        elseif ($remCu <= 100 && $w['gp'] >= 1) {
            $w['gp'] -= 1;
            $spent['gp'] += 1;
            $changeCu = 100 - $remCu;
            $changeCoins = self::spToCoins($changeCu / 10.0, true);
            $w = self::addCoins($w, $changeCoins);
            $change = self::addCoins($change, $changeCoins);
            $remCu = 0;
        }
        // Check platinum (1000 Cu)
        elseif ($remCu <= 1000 && $w['pp'] >= 1) {
            $w['pp'] -= 1;
            $spent['pp'] += 1;
            $changeCu = 1000 - $remCu;
            $changeCoins = self::spToCoins($changeCu / 10.0, true);
            $w = self::addCoins($w, $changeCoins);
            $change = self::addCoins($change, $changeCoins);
            $remCu = 0;
        } else {
            // General fallback: convert entire remaining wallet to copper, deduct, and reconstruct
            $remainingWalletCu = (int)round(self::coinsToSp($w) * 10);
            if ($remainingWalletCu < $remCu) {
                return [
                    'success' => false,
                    'wallet' => self::parseWallet($wallet),
                    'spent' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                    'change' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                    'error' => "Unexpected error: insufficient broken funds.",
                ];
            }

            // Pay from remaining
            $finalRemainingCu = $remainingWalletCu - $remCu;
            $newWalletCoins = self::spToCoins($finalRemainingCu / 10.0, true);
            $spentTotalCoins = self::spToCoins($costSp, true);

            return [
                'success' => true,
                'wallet' => $newWalletCoins,
                'spent' => $spentTotalCoins,
                'change' => ['cp' => 0, 'sp' => 0, 'gp' => 0, 'pp' => 0],
                'error' => null,
            ];
        }

        return [
            'success' => true,
            'wallet' => $w,
            'spent' => $spent,
            'change' => $change,
            'error' => null,
        ];
    }

    /**
     * Format wallet or coins array for readable display.
     *
     * @param array $coins
     * @return string e.g. "1 pp, 4 gp, 5 sp, 2 cp"
     */
    public static function formatCoins(array $coins): string
    {
        $w = self::parseWallet($coins);
        $parts = [];

        if ($w['pp'] > 0) {
            $parts[] = number_format($w['pp']) . ' pp';
        }
        if ($w['gp'] > 0) {
            $parts[] = number_format($w['gp']) . ' gp';
        }
        if ($w['sp'] > 0) {
            $parts[] = number_format($w['sp']) . ' sp';
        }
        if ($w['cp'] > 0) {
            $parts[] = number_format($w['cp']) . ' cp';
        }

        if (empty($parts)) {
            return '0 sp';
        }

        return implode(', ', $parts);
    }
}
