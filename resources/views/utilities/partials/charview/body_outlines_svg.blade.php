<!-- SVG Body Type Outlines for Character Equipment Paperdoll -->
<div class="w-full h-full flex items-center justify-center">

    <!-- Shared Gradient & Filter Definitions -->
    <svg class="absolute w-0 h-0" aria-hidden="true" focusable="false">
        <defs>
            <!-- Base Silhouette Gradient -->
            <linearGradient id="bodyOutlineGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#475569" stop-opacity="0.9" />
                <stop offset="50%" stop-color="#334155" stop-opacity="0.95" />
                <stop offset="100%" stop-color="#1e293b" stop-opacity="0.98" />
            </linearGradient>

            <!-- Equipped Helm / Headpiece Gradient (Cyan / Steel-Blue) -->
            <linearGradient id="bodyEquippedHead" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#38bdf8" />
                <stop offset="50%" stop-color="#0284c7" />
                <stop offset="100%" stop-color="#0369a1" />
            </linearGradient>

            <!-- Equipped Eyes / Visor Glow -->
            <linearGradient id="bodyEquippedEyes" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#67e8f9" />
                <stop offset="100%" stop-color="#06b6d4" />
            </linearGradient>

            <!-- Equipped Necklace / Gold Jewellery Gradient -->
            <linearGradient id="bodyEquippedNecklace" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fef08a" />
                <stop offset="50%" stop-color="#f59e0b" />
                <stop offset="100%" stop-color="#b45309" />
            </linearGradient>

            <!-- Equipped Cloak / Cape Gradient (Mystic Purple / Indigo) -->
            <linearGradient id="bodyEquippedCloak" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#a855f7" />
                <stop offset="50%" stop-color="#7c3aed" />
                <stop offset="100%" stop-color="#4338ca" />
            </linearGradient>

            <!-- Equipped Armor / Chestplate Gradient (Cobalt / Steel-Plate) -->
            <linearGradient id="bodyEquippedArmor" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#60a5fa" />
                <stop offset="50%" stop-color="#2563eb" />
                <stop offset="100%" stop-color="#1e3a8a" />
            </linearGradient>

            <!-- Equipped Clothing / Tunic Gradient (Emerald / Forest) -->
            <linearGradient id="bodyEquippedClothing" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#34d399" />
                <stop offset="50%" stop-color="#059669" />
                <stop offset="100%" stop-color="#064e3b" />
            </linearGradient>

            <!-- Equipped Bracers / Vambraces Gradient -->
            <linearGradient id="bodyEquippedBracers" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#38bdf8" />
                <stop offset="100%" stop-color="#0284c7" />
            </linearGradient>

            <!-- Equipped Gloves / Gauntlets Gradient (Amber / Leather) -->
            <linearGradient id="bodyEquippedGloves" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fbbf24" />
                <stop offset="50%" stop-color="#d97706" />
                <stop offset="100%" stop-color="#92400e" />
            </linearGradient>

            <!-- Equipped Belt / Cinch Gradient -->
            <linearGradient id="bodyEquippedBelt" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#f59e0b" />
                <stop offset="50%" stop-color="#d97706" />
                <stop offset="100%" stop-color="#78350f" />
            </linearGradient>

            <!-- Equipped Boots / Greaves Gradient -->
            <linearGradient id="bodyEquippedBoots" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#d97706" />
                <stop offset="50%" stop-color="#b45309" />
                <stop offset="100%" stop-color="#78350f" />
            </linearGradient>

            <!-- Equipped Weapon Gradient (Radiant Amber / Crimson) -->
            <linearGradient id="bodyEquippedWeapon" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fef08a" />
                <stop offset="50%" stop-color="#f59e0b" />
                <stop offset="100%" stop-color="#ef4444" />
            </linearGradient>

            <!-- Equipped Shield Gradient (Azure Crest) -->
            <linearGradient id="bodyEquippedShield" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#67e8f9" />
                <stop offset="50%" stop-color="#0284c7" />
                <stop offset="100%" stop-color="#1e3a8a" />
            </linearGradient>

            <!-- Equipped Saddle / Barding Gradient -->
            <linearGradient id="bodyEquippedSaddle" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#fbbf24" />
                <stop offset="50%" stop-color="#d97706" />
                <stop offset="100%" stop-color="#78350f" />
            </linearGradient>

            <!-- Glowing Filters -->
            <filter id="nodeGlow" x="-30%" y="-30%" width="160%" height="160%">
                <feGaussianBlur stdDeviation="2" result="blur" />
                <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>
            <filter id="equippedGlow" x="-20%" y="-20%" width="140%" height="140%">
                <feDropShadow dx="0" dy="1" stdDeviation="2" flood-color="#38bdf8" flood-opacity="0.4" />
            </filter>
        </defs>
    </svg>

    <!-- 0: Generic Humanoid -->
    <template x-if="characterBodyType === 0">
        <svg viewBox="0 0 240 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Cloak / Cape (Behind Body) -->
            <path x-show="isSlotEquipped('cloak')" d="M76 72 C62 110 50 170 42 245 C80 255 160 255 198 245 C190 170 178 110 164 72 Z" fill="url(#bodyEquippedCloak)" stroke="#c084fc" stroke-width="2" />
            
            <!-- Head & Helmet -->
            <circle cx="120" cy="40" r="20" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            
            <!-- Eyes / Lenses -->
            <g x-show="isSlotEquipped('eyes')">
                <circle cx="113" cy="38" r="4" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
                <circle cx="127" cy="38" r="4" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
                <line x1="117" y1="38" x2="123" y2="38" stroke="#67e8f9" stroke-width="1.5" />
            </g>

            <!-- Neck / Necklace -->
            <path d="M112 60 L128 60 L130 68 L110 68 Z" :fill="isSlotEquipped('necklace') ? 'url(#bodyEquippedNecklace)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('necklace') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />
            <circle x-show="isSlotEquipped('necklace')" cx="120" cy="70" r="3.5" fill="#fbbf24" stroke="#fef08a" stroke-width="1" filter="url(#nodeGlow)" />

            <!-- Torso (Armor / Clothing) -->
            <path d="M92 68 C105 64 135 64 148 68 L154 130 C150 155 138 168 120 170 C102 168 90 155 86 130 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : (isSlotEquipped('clothing') ? '#34d399' : '#94a3b8')" stroke-width="2" />
            <path x-show="isSlotEquipped('armor')" d="M102 75 Q120 86 138 75 M120 86 L120 145" fill="none" stroke="#93c5fd" stroke-width="1.5" stroke-linecap="round" opacity="0.8" />

            <!-- Left Arm & Bracer -->
            <path d="M90 70 C75 95 62 130 55 160 C52 175 60 182 66 178 C74 172 82 145 92 120 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />
            <path x-show="isSlotEquipped('bracers')" d="M72 130 C68 144 62 160 59 170 L67 166 C72 154 77 140 81 128 Z" fill="url(#bodyEquippedBracers)" stroke="#38bdf8" stroke-width="1.5" />

            <!-- Right Arm & Bracer -->
            <path d="M150 70 C165 95 178 130 185 160 C188 175 180 182 174 178 C166 172 158 145 148 120 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />
            <path x-show="isSlotEquipped('bracers')" d="M168 130 C172 144 178 160 181 170 L173 166 C168 154 163 140 159 128 Z" fill="url(#bodyEquippedBracers)" stroke="#38bdf8" stroke-width="1.5" />

            <!-- Hands & Gloves & Rings -->
            <ellipse cx="60" cy="178" rx="6" ry="8" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />
            <circle x-show="isSlotEquipped('ring_1')" cx="58" cy="178" r="3" fill="#fbbf24" stroke="#fef08a" stroke-width="1.5" filter="url(#nodeGlow)" />
            
            <ellipse cx="180" cy="178" rx="6" ry="8" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />
            <circle x-show="isSlotEquipped('ring_2')" cx="182" cy="178" r="3" fill="#fbbf24" stroke="#fef08a" stroke-width="1.5" filter="url(#nodeGlow)" />

            <!-- Off-Hand Shield / Weapon -->
            <g x-show="isSlotEquipped('off_hand')" filter="url(#equippedGlow)">
                <path d="M40 148 C56 144 68 144 80 148 C80 180 72 204 60 218 C48 204 40 180 40 148 Z" fill="url(#bodyEquippedShield)" stroke="#38bdf8" stroke-width="1.5" />
                <circle cx="60" cy="160" r="2.5" fill="#fef08a" />
            </g>

            <!-- Main-Hand Weapon -->
            <g x-show="isSlotEquipped('main_hand')" filter="url(#equippedGlow)">
                <path d="M184 175 L212 90 L217 88 L218 92 L188 179 Z" fill="url(#bodyEquippedWeapon)" stroke="#f59e0b" stroke-width="1" />
                <line x1="180" y1="178" x2="190" y2="176" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" />
            </g>

            <!-- Waist / Belt -->
            <path d="M98 170 L142 170 L138 184 L102 184 Z" :fill="isSlotEquipped('belt') ? 'url(#bodyEquippedBelt)' : '#6366f1'" :fill-opacity="isSlotEquipped('belt') ? '1' : '0.3'" :stroke="isSlotEquipped('belt') ? '#fbbf24' : '#818cf8'" stroke-width="1.5" />
            <rect x-show="isSlotEquipped('belt')" x="115" y="173" width="10" height="8" rx="2" fill="#fef08a" stroke="#d97706" stroke-width="1" />

            <!-- Legs -->
            <path d="M102 184 L96 250 L92 290 L110 290 L118 250 L120 188 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M138 184 L144 250 L148 290 L130 290 L122 250 L120 188 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />

            <!-- Feet & Boots -->
            <ellipse cx="101" cy="293" rx="12" ry="5" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#475569'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />
            <ellipse cx="139" cy="293" rx="12" ry="5" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#475569'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 1: Biped (Humanoid - Primary Body Type) -->
    <template x-if="characterBodyType === 1">
        <svg viewBox="0 0 240 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Cloak / Cape (Draped Behind) -->
            <path x-show="isSlotEquipped('cloak')" d="M72 75 C60 110 50 170 42 245 C80 255 160 255 198 245 C190 170 180 110 168 75 C145 70 95 70 72 75 Z" fill="url(#bodyEquippedCloak)" stroke="#c084fc" stroke-width="2" />

            <!-- Head & Helmet -->
            <ellipse cx="120" cy="38" rx="19" ry="23" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <path x-show="isSlotEquipped('head')" d="M104 35 Q120 30 136 35 L133 42 Q120 38 107 42 Z" fill="#bae6fd" opacity="0.8" />

            <!-- Eyes / Lenses -->
            <g x-show="isSlotEquipped('eyes')">
                <ellipse cx="112" cy="36" rx="4.5" ry="3.5" fill="#22d3ee" stroke="#67e8f9" stroke-width="1" filter="url(#nodeGlow)" />
                <ellipse cx="128" cy="36" rx="4.5" ry="3.5" fill="#22d3ee" stroke="#67e8f9" stroke-width="1" filter="url(#nodeGlow)" />
                <line x1="116.5" y1="36" x2="123.5" y2="36" stroke="#67e8f9" stroke-width="1.5" />
            </g>

            <!-- Neck / Necklace -->
            <path d="M112 60 L128 60 L130 68 L110 68 Z" :fill="isSlotEquipped('necklace') ? 'url(#bodyEquippedNecklace)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('necklace') ? '#fbbf24' : '#94a3b8'" stroke-width="1" />
            <circle x-show="isSlotEquipped('necklace')" cx="120" cy="69" r="3.5" fill="#fbbf24" stroke="#fef08a" stroke-width="1" filter="url(#nodeGlow)" />

            <!-- Shoulders & Chest / Torso (Armor / Clothing) -->
            <path d="M88 68 C104 62 136 62 152 68 L158 135 C154 160 140 172 120 174 C100 172 86 160 82 135 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : (isSlotEquipped('clothing') ? '#34d399' : '#94a3b8')" stroke-width="2" />
            <path x-show="isSlotEquipped('armor')" d="M102 74 Q120 86 138 74 M120 86 L120 145 M100 120 Q120 135 140 120" fill="none" stroke="#93c5fd" stroke-width="1.5" stroke-linecap="round" opacity="0.8" />

            <!-- Left Arm & Bracer -->
            <path d="M86 70 C72 96 58 135 52 165 C48 180 56 186 63 182 C72 176 80 148 90 125 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />
            <path x-show="isSlotEquipped('bracers')" d="M68 135 C64 148 58 165 55 174 L65 170 C70 158 74 144 77 132 Z" fill="url(#bodyEquippedBracers)" stroke="#38bdf8" stroke-width="1.5" />

            <!-- Right Arm & Bracer -->
            <path d="M154 70 C168 96 182 135 188 165 C192 180 184 186 177 182 C168 176 160 148 150 125 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />
            <path x-show="isSlotEquipped('bracers')" d="M172 135 C176 148 182 165 185 174 L175 170 C170 158 166 144 163 132 Z" fill="url(#bodyEquippedBracers)" stroke="#38bdf8" stroke-width="1.5" />

            <!-- Left Hand & Glove & Ring 1 -->
            <ellipse cx="55" cy="184" rx="7" ry="9" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />
            <circle x-show="isSlotEquipped('ring_1')" cx="53" cy="184" r="3" fill="#fbbf24" stroke="#fef08a" stroke-width="1.5" filter="url(#nodeGlow)" />

            <!-- Right Hand & Glove & Ring 2 -->
            <ellipse cx="185" cy="184" rx="7" ry="9" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />
            <circle x-show="isSlotEquipped('ring_2')" cx="187" cy="184" r="3" fill="#fbbf24" stroke="#fef08a" stroke-width="1.5" filter="url(#nodeGlow)" />

            <!-- Off-Hand Shield / Weapon -->
            <g x-show="isSlotEquipped('off_hand')" filter="url(#equippedGlow)">
                <path d="M36 150 C52 146 64 146 76 150 C76 182 68 206 56 220 C44 206 36 182 36 150 Z" fill="url(#bodyEquippedShield)" stroke="#38bdf8" stroke-width="1.5" />
                <path d="M46 160 L66 160 M56 155 L56 195" stroke="#93c5fd" stroke-width="1" stroke-linecap="round" opacity="0.7" />
                <circle cx="56" cy="160" r="2.5" fill="#fef08a" />
            </g>

            <!-- Main-Hand Weapon -->
            <g x-show="isSlotEquipped('main_hand')" filter="url(#equippedGlow)">
                <path d="M188 180 L212 95 L217 93 L218 97 L192 184 Z" fill="url(#bodyEquippedWeapon)" stroke="#f59e0b" stroke-width="1" />
                <line x1="184" y1="183" x2="194" y2="181" stroke="#fbbf24" stroke-width="2.5" stroke-linecap="round" />
                <line x1="188" y1="186" x2="185" y2="196" stroke="#b45309" stroke-width="2" stroke-linecap="round" />
                <circle cx="184.5" cy="197" r="1.8" fill="#fbbf24" />
            </g>

            <!-- Waist / Belt Band -->
            <path d="M96 174 L144 174 L140 190 L100 190 Z" :fill="isSlotEquipped('belt') ? 'url(#bodyEquippedBelt)' : '#6366f1'" :fill-opacity="isSlotEquipped('belt') ? '1' : '0.3'" :stroke="isSlotEquipped('belt') ? '#fbbf24' : '#818cf8'" stroke-width="1.5" />
            <rect x-show="isSlotEquipped('belt')" x="115" y="177" width="10" height="10" rx="2" fill="#fef08a" stroke="#d97706" stroke-width="1" />

            <!-- Left Leg -->
            <path d="M100 190 L94 255 L90 292 L109 292 L117 255 L120 194 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Left Boot -->
            <path d="M94 255 L90 292 L109 292 L117 255 Z" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#334155'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />
            <path d="M85 292 L111 292 C111 297 85 299 85 292 Z" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#334155'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />

            <!-- Right Leg -->
            <path d="M140 190 L146 255 L150 292 L131 292 L123 255 L120 194 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Right Boot -->
            <path d="M146 255 L150 292 L131 292 L123 255 Z" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#334155'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />
            <path d="M129 292 L155 292 C155 297 129 299 129 292 Z" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#334155'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 2: Biped, winged (Winged Humanoid / Avariel) -->
    <template x-if="characterBodyType === 2">
        <svg viewBox="0 0 260 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Left Wing -->
            <path d="M100 85 C65 35 25 30 10 65 C0 95 20 150 55 180 C80 200 100 185 105 145 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M10 65 C25 85 45 140 75 165" stroke="#818cf8" stroke-width="1" />
            <!-- Right Wing -->
            <path d="M160 85 C195 35 235 30 250 65 C260 95 240 150 205 180 C180 200 160 185 155 145 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M250 65 C235 85 215 140 185 165" stroke="#818cf8" stroke-width="1" />

            <!-- Cloak -->
            <path x-show="isSlotEquipped('cloak')" d="M85 75 C75 110 68 170 60 245 C95 255 165 255 200 245 C192 170 185 110 175 75 Z" fill="url(#bodyEquippedCloak)" stroke="#c084fc" stroke-width="2" />

            <!-- Head & Helmet -->
            <ellipse cx="130" cy="40" rx="18" ry="22" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <!-- Eyes -->
            <g x-show="isSlotEquipped('eyes')">
                <circle cx="123" cy="38" r="4" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
                <circle cx="137" cy="38" r="4" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
            </g>
            <!-- Neck -->
            <path x-show="isSlotEquipped('necklace')" d="M122 62 L138 62 L136 68 L124 68 Z" fill="url(#bodyEquippedNecklace)" stroke="#fbbf24" stroke-width="1.5" />

            <!-- Torso -->
            <path d="M102 70 C116 64 144 64 158 70 L164 135 C160 160 148 172 130 174 C112 172 100 160 96 135 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : (isSlotEquipped('clothing') ? '#34d399' : '#94a3b8')" stroke-width="2" />
            
            <!-- Arms & Hands -->
            <path d="M98 72 C85 98 72 135 68 165 C65 178 72 184 78 180 C86 174 94 148 102 125 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M162 72 C175 98 188 135 192 165 C195 178 188 184 182 180 C174 174 166 148 158 125 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />
            
            <ellipse cx="68" cy="180" rx="6" ry="8" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />
            <ellipse cx="192" cy="180" rx="6" ry="8" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />

            <!-- Belt & Legs -->
            <path d="M108 174 L152 174 L148 188 L112 188 Z" :fill="isSlotEquipped('belt') ? 'url(#bodyEquippedBelt)' : '#6366f1'" :fill-opacity="isSlotEquipped('belt') ? '1' : '0.3'" :stroke="isSlotEquipped('belt') ? '#fbbf24' : '#818cf8'" stroke-width="1.5" />
            <path d="M112 188 L106 255 L102 292 L120 292 L127 255 L130 192 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M148 188 L154 255 L158 292 L140 292 L133 255 L130 192 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />

            <!-- Boots -->
            <path d="M106 255 L102 292 L120 292 L127 255 Z" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#334155'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />
            <path d="M154 255 L158 292 L140 292 L133 255 Z" :fill="isSlotEquipped('boots') ? 'url(#bodyEquippedBoots)' : '#334155'" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#94a3b8'" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 3: Quadruped (Horse / Canine / Feline) -->
    <template x-if="characterBodyType === 3">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Head & Muzzle -->
            <path d="M45 75 C45 60 70 50 85 68 L105 110 L75 118 Z" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <polygon points="75,55 82,40 88,54" fill="#475569" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Eyes -->
            <circle x-show="isSlotEquipped('eyes')" cx="72" cy="72" r="3.5" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
            <!-- Neck & Body -->
            <path d="M85 70 C105 85 125 110 135 125 C175 120 215 125 240 145 C248 170 245 200 230 215 L105 210 C85 190 75 150 85 115 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : (isSlotEquipped('clothing') ? '#34d399' : '#94a3b8')" stroke-width="2" />
            <!-- Saddle Placement Zone Indicator -->
            <path d="M145 122 C165 118 195 118 215 125 L210 160 C190 165 165 165 145 160 Z" :fill="isSlotEquipped('saddle') ? 'url(#bodyEquippedSaddle)' : '#f59e0b'" :fill-opacity="isSlotEquipped('saddle') ? '0.9' : '0.25'" :stroke="isSlotEquipped('saddle') ? '#fef08a' : '#f59e0b'" stroke-width="1.5" :stroke-dasharray="isSlotEquipped('saddle') ? 'none' : '3 3'" />
            <!-- Forelegs & Hindlegs -->
            <path d="M98 210 L90 285 L84 300 L102 300 L112 285 L118 210 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M120 210 L115 285 L110 300 L126 300 L134 285 L138 210 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M210 215 L200 285 L195 300 L212 300 L222 285 L232 215 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M230 215 L225 285 L220 300 L236 300 L244 285 L248 215 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Boots / Horseshoes -->
            <path x-show="isSlotEquipped('boots')" d="M84 290 L102 290 L102 302 L84 302 Z M110 290 L126 290 L126 302 L110 302 Z M195 290 L212 290 L212 302 L195 302 Z M220 290 L236 290 L236 302 L220 302 Z" fill="url(#bodyEquippedBoots)" stroke="#f59e0b" stroke-width="1" />
            <!-- Tail -->
            <path d="M242 150 C260 175 270 220 260 265 C255 250 250 210 240 185" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 4: Quadruped, winged (Pegasus / Griffon / Dragon) -->
    <template x-if="characterBodyType === 4">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Wings -->
            <path d="M140 115 C110 40 55 25 25 55 C10 80 40 120 85 140 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M170 115 C200 40 255 25 275 55 C285 80 255 120 210 140 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <!-- Head & Muzzle -->
            <path d="M45 80 C45 65 70 55 85 72 L105 115 L75 122 Z" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <!-- Quad Body -->
            <path d="M85 75 C105 90 125 115 135 130 C175 125 215 130 240 150 C248 175 245 205 230 220 L105 215 C85 195 75 155 85 120 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : (isSlotEquipped('clothing') ? '#34d399' : '#94a3b8')" stroke-width="2" />
            <!-- Saddle Zone -->
            <path d="M145 127 C165 123 195 123 215 130 L210 165 C190 170 165 170 145 165 Z" :fill="isSlotEquipped('saddle') ? 'url(#bodyEquippedSaddle)' : '#f59e0b'" :fill-opacity="isSlotEquipped('saddle') ? '0.9' : '0.25'" :stroke="isSlotEquipped('saddle') ? '#fef08a' : '#f59e0b'" stroke-width="1.5" />
            <!-- Legs -->
            <path d="M98 215 L90 290 L84 302 L102 302 L112 290 L118 215 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M210 220 L200 290 L195 302 L212 302 L222 290 L232 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Tail -->
            <path d="M242 155 C260 180 270 225 260 270" stroke="#94a3b8" stroke-width="2" />
        </svg>
    </template>

    <!-- 5: Multiped (Arachnid / Insectoid / Crab) -->
    <template x-if="characterBodyType === 5">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Cephalothorax -->
            <ellipse cx="140" cy="115" rx="35" ry="40" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <!-- Eyespots -->
            <g x-show="isSlotEquipped('eyes')">
                <circle cx="132" cy="95" r="3" fill="#22d3ee" stroke="#67e8f9" stroke-width="1" />
                <circle cx="148" cy="95" r="3" fill="#22d3ee" stroke="#67e8f9" stroke-width="1" />
            </g>
            <!-- Abdomen / Carapace -->
            <ellipse cx="140" cy="210" rx="48" ry="55" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#94a3b8'" stroke-width="2" />
            <!-- Saddle Zone -->
            <ellipse x-show="isSlotEquipped('saddle')" cx="140" cy="180" rx="28" ry="20" fill="url(#bodyEquippedSaddle)" stroke="#fef08a" stroke-width="1.5" />
            <!-- Pedipalps / Mandibles (Main Hand / Off Hand) -->
            <path d="M125 80 L115 60 L122 55" :stroke="isSlotEquipped('off_hand') ? '#38bdf8' : '#94a3b8'" stroke-width="2.5" />
            <path d="M155 80 L165 60 L158 55" :stroke="isSlotEquipped('main_hand') ? '#f59e0b' : '#94a3b8'" stroke-width="2.5" />
            <!-- 8 Articulated Legs -->
            <path d="M115 100 L60 70 L25 105" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M110 115 L50 110 L15 155" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M110 130 L55 160 L20 215" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M115 145 L65 205 L35 275" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M165 100 L220 70 L255 105" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M170 115 L230 110 L265 155" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M170 130 L225 160 L260 215" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M165 145 L215 205 L245 275" stroke="#94a3b8" stroke-width="2" fill="none" />
        </svg>
    </template>

    <!-- 6: Multiped, winged (Winged Insectoid) -->
    <template x-if="characterBodyType === 6">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Insect Wings -->
            <path d="M125 100 C75 35 25 35 15 75 C10 110 50 140 110 130 Z" fill="#818cf8" fill-opacity="0.2" stroke="#818cf8" stroke-width="1.5" />
            <path d="M155 100 C205 35 255 35 265 75 C270 110 230 140 170 130 Z" fill="#818cf8" fill-opacity="0.2" stroke="#818cf8" stroke-width="1.5" />
            <!-- Body -->
            <ellipse cx="140" cy="115" rx="30" ry="35" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <ellipse cx="140" cy="200" rx="38" ry="50" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#94a3b8'" stroke-width="2" />
            <!-- Legs -->
            <path d="M115 110 L50 110 L20 160" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M115 130 L60 175 L35 240" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M165 110 L230 110 L260 160" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M165 130 L220 175 L245 240" stroke="#94a3b8" stroke-width="2" fill="none" />
        </svg>
    </template>

    <!-- 7: Blob (Ooze / Slime / Amorphous) -->
    <template x-if="characterBodyType === 7">
        <svg viewBox="0 0 260 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M130 65 C185 55 225 100 220 160 C215 220 190 270 130 275 C70 275 40 220 40 160 C40 95 75 75 130 65 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#10b981'" stroke-width="2.5" stroke-dasharray="6 3" />
            <!-- Pseudopods & Inner Nucleus -->
            <circle cx="130" cy="165" r="28" :fill="isSlotEquipped('ring_1') || isSlotEquipped('necklace') ? 'url(#bodyEquippedNecklace)' : '#10b981'" fill-opacity="0.4" :stroke="isSlotEquipped('ring_1') || isSlotEquipped('necklace') ? '#fbbf24' : '#34d399'" stroke-width="2" />
            <ellipse cx="75" cy="130" rx="14" ry="10" :fill="isSlotEquipped('off_hand') ? 'url(#bodyEquippedShield)' : '#10b981'" fill-opacity="0.3" />
            <ellipse cx="185" cy="190" rx="16" ry="12" :fill="isSlotEquipped('main_hand') ? 'url(#bodyEquippedWeapon)' : '#10b981'" fill-opacity="0.3" />
            <ellipse cx="130" cy="225" rx="20" ry="10" fill="#10b981" fill-opacity="0.25" />
        </svg>
    </template>

    <!-- 8: Centauroid (Centaur / Drider) -->
    <template x-if="characterBodyType === 8">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Cloak -->
            <path x-show="isSlotEquipped('cloak')" d="M70 45 C58 90 52 145 45 220 C80 230 140 230 170 220 C162 145 150 90 140 45 Z" fill="url(#bodyEquippedCloak)" stroke="#c084fc" stroke-width="2" />

            <!-- Upper Humanoid Torso & Head -->
            <circle cx="105" cy="35" r="16" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <circle x-show="isSlotEquipped('necklace')" cx="105" cy="52" r="3" fill="#fbbf24" stroke="#fef08a" stroke-width="1" filter="url(#nodeGlow)" />
            
            <path d="M82 58 C92 54 118 54 128 58 L132 110 C128 128 118 135 105 135 C92 135 82 128 78 110 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : (isSlotEquipped('clothing') ? '#34d399' : '#94a3b8')" stroke-width="2" />
            
            <!-- Upper Arms & Hands -->
            <path d="M80 60 C68 80 58 110 52 130 C48 142 55 146 60 144 C67 140 74 120 82 102 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M130 60 C142 80 152 110 158 130 C162 142 155 146 150 144 C143 140 136 120 128 102 Z" :fill="isSlotEquipped('clothing') && !isSlotEquipped('armor') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)'" stroke="#94a3b8" stroke-width="1.5" />

            <ellipse cx="54" cy="142" rx="5" ry="7" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />
            <ellipse cx="156" cy="142" rx="5" ry="7" :fill="isSlotEquipped('gloves') ? 'url(#bodyEquippedGloves)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('gloves') ? '#fbbf24' : '#94a3b8'" stroke-width="1.5" />

            <!-- Main Hand / Off Hand Icons -->
            <g x-show="isSlotEquipped('main_hand')" filter="url(#equippedGlow)">
                <path d="M158 140 L185 85 L189 88 L163 145 Z" fill="url(#bodyEquippedWeapon)" stroke="#f59e0b" stroke-width="1" />
            </g>
            <g x-show="isSlotEquipped('off_hand')" filter="url(#equippedGlow)">
                <path d="M38 120 C50 116 60 116 70 120 C70 145 64 165 54 175 C44 165 38 145 38 120 Z" fill="url(#bodyEquippedShield)" stroke="#38bdf8" stroke-width="1.5" />
            </g>

            <!-- Lower Equine Body -->
            <path d="M105 135 C130 135 190 135 230 155 C242 178 240 208 225 225 L105 220 C85 200 78 165 105 135 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Belt Band -->
            <path d="M85 132 L125 132 L122 142 L88 142 Z" :fill="isSlotEquipped('belt') ? 'url(#bodyEquippedBelt)' : '#6366f1'" :fill-opacity="isSlotEquipped('belt') ? '1' : '0.3'" :stroke="isSlotEquipped('belt') ? '#fbbf24' : '#818cf8'" stroke-width="1.5" />
            <!-- Saddle Zone -->
            <path d="M150 135 C170 132 195 132 215 138 L210 170 C190 174 170 174 150 170 Z" :fill="isSlotEquipped('saddle') ? 'url(#bodyEquippedSaddle)' : '#f59e0b'" :fill-opacity="isSlotEquipped('saddle') ? '0.9' : '0.25'" :stroke="isSlotEquipped('saddle') ? '#fef08a' : '#f59e0b'" stroke-width="1.5" />
            <!-- Forelegs & Hindlegs -->
            <path d="M98 220 L92 288 L86 300 L104 300 L112 288 L118 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M210 225 L200 288 L195 300 L212 300 L222 288 L230 225 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Boots -->
            <path x-show="isSlotEquipped('boots')" d="M86 288 L104 288 L104 302 L86 302 Z M195 288 L212 288 L212 302 L195 302 Z" fill="url(#bodyEquippedBoots)" stroke="#f59e0b" stroke-width="1" />
            <!-- Tail -->
            <path d="M235 160 C255 185 265 230 255 270" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 9: Centauroid, winged (Winged Centaur) -->
    <template x-if="characterBodyType === 9">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Wings -->
            <path d="M105 70 C70 15 20 15 10 50 C5 75 35 110 80 125 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M130 70 C165 15 215 15 235 50 C245 75 215 110 170 125 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <!-- Upper Torso & Head -->
            <circle cx="105" cy="35" r="16" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <path d="M82 58 C92 54 118 54 128 58 L132 110 C128 128 118 135 105 135 C92 135 82 128 78 110 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#94a3b8'" stroke-width="2" />
            <!-- Lower Body -->
            <path d="M105 135 C130 135 190 135 230 155 C242 178 240 208 225 225 L105 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Legs -->
            <path d="M98 220 L92 288 L86 300 L104 300 L112 288 L118 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M210 225 L200 288 L195 300 L212 300 L222 288 L230 225 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Boots -->
            <path x-show="isSlotEquipped('boots')" d="M86 288 L104 288 L104 302 L86 302 Z M195 288 L212 288 L212 302 L195 302 Z" fill="url(#bodyEquippedBoots)" stroke="#f59e0b" stroke-width="1" />
        </svg>
    </template>

    <!-- 10: Ichtyoid (Aquatic / Fish / Shark / Mer-form) -->
    <template x-if="characterBodyType === 10">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Dorsal Fin -->
            <path d="M125 105 C140 60 170 55 185 85 L160 120 Z" fill="url(#bodyOutlineGrad)" stroke="#0ea5e9" stroke-width="1.5" />
            <!-- Streamlined Torso -->
            <path d="M40 160 C75 110 165 105 210 150 L245 125 L238 160 L245 195 L210 170 C165 215 75 210 40 160 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#0ea5e9'" stroke-width="2" />
            <!-- Pectoral Fin -->
            <path d="M100 175 C115 210 135 230 150 225 L135 180 Z" fill="url(#bodyOutlineGrad)" stroke="#0ea5e9" stroke-width="1.5" />
            <!-- Eye -->
            <circle cx="65" cy="150" r="4" :fill="isSlotEquipped('eyes') ? '#22d3ee' : '#38bdf8'" :stroke="isSlotEquipped('eyes') ? '#67e8f9' : '#0284c7'" stroke-width="1.5" />
            <!-- Head / Helm Zone -->
            <path x-show="isSlotEquipped('head')" d="M40 160 C55 135 85 130 95 145 L85 175 C70 180 50 175 40 160 Z" fill="url(#bodyEquippedHead)" stroke="#38bdf8" stroke-width="1.5" />
            <!-- Saddle zone -->
            <ellipse cx="140" cy="155" rx="30" ry="18" :fill="isSlotEquipped('saddle') ? 'url(#bodyEquippedSaddle)' : '#f59e0b'" :fill-opacity="isSlotEquipped('saddle') ? '0.85' : '0.2'" :stroke="isSlotEquipped('saddle') ? '#fef08a' : '#f59e0b'" stroke-width="1.5" :stroke-dasharray="isSlotEquipped('saddle') ? 'none' : '3 3'" />
        </svg>
    </template>

    <!-- 11: Avian (Bird / Raptor / Roc) -->
    <template x-if="characterBodyType === 11">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Left Wing -->
            <path d="M125 110 C80 60 25 75 10 120 C18 160 65 180 115 170 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M10 120 C30 145 70 165 115 170" stroke="#818cf8" stroke-width="1" />
            <!-- Right Wing -->
            <path d="M155 110 C200 60 255 75 270 120 C262 160 215 180 165 170 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M270 120 C250 145 210 165 165 170" stroke="#818cf8" stroke-width="1" />
            <!-- Head & Curved Beak -->
            <ellipse cx="140" cy="65" rx="16" ry="18" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#94a3b8'" stroke-width="2" />
            <path d="M152 65 L168 72 L150 78 Z" fill="#f59e0b" stroke="#d97706" stroke-width="1.5" />
            <!-- Eyes -->
            <circle x-show="isSlotEquipped('eyes')" cx="146" cy="62" r="3.5" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
            <!-- Body -->
            <path d="M124 82 C134 80 146 80 156 82 L162 180 C156 215 148 230 140 232 C132 230 124 215 118 180 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#94a3b8'" stroke-width="2" />
            <!-- Taloned Legs & Boots -->
            <path d="M128 232 L120 275 L110 285" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#f59e0b'" stroke-width="2.5" fill="none" />
            <path d="M152 232 L160 275 L170 285" :stroke="isSlotEquipped('boots') ? '#f59e0b' : '#f59e0b'" stroke-width="2.5" fill="none" />
            <!-- Fan Tail -->
            <path d="M130 220 L115 270 L140 260 L165 270 L150 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 12: Ophidian (Serpent / Snake / Naga) -->
    <template x-if="characterBodyType === 12">
        <svg viewBox="0 0 260 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Hood & Head -->
            <ellipse cx="130" cy="55" rx="18" ry="22" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#10b981'" stroke-width="2" />
            <path d="M112 55 C98 68 98 95 114 110 C124 100 124 75 112 55 Z" fill="#10b981" fill-opacity="0.3" stroke="#34d399" stroke-width="1.5" />
            <path d="M148 55 C162 68 162 95 146 110 C136 100 136 75 148 55 Z" fill="#10b981" fill-opacity="0.3" stroke="#34d399" stroke-width="1.5" />
            <!-- Eyes -->
            <g x-show="isSlotEquipped('eyes')">
                <circle cx="123" cy="52" r="3.5" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
                <circle cx="137" cy="52" r="3.5" fill="#22d3ee" stroke="#67e8f9" stroke-width="1.5" filter="url(#nodeGlow)" />
            </g>
            <!-- Coiling Muscular Body -->
            <path d="M120 75 C120 120 140 145 155 170 C175 200 170 240 130 250 C85 260 60 220 75 185 C85 160 115 170 110 190 C105 210 125 220 140 215 C155 210 155 190 140 175 C125 160 100 140 100 95 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#10b981'" stroke-width="2" />
            <!-- Belt Zone -->
            <path x-show="isSlotEquipped('belt')" d="M115 140 L145 140 L140 152 L110 152 Z" fill="url(#bodyEquippedBelt)" stroke="#fbbf24" stroke-width="1.5" />
            <!-- Tail Tip -->
            <path d="M130 250 C155 255 190 270 205 285 L215 280" stroke="#34d399" stroke-width="2" fill="none" />
        </svg>
    </template>

    <!-- 13: Ophidian, winged (Couatl / Feathered Serpent) -->
    <template x-if="characterBodyType === 13">
        <svg viewBox="0 0 280 320" class="w-full h-full drop-shadow-lg" style="width: 100%; height: 100%; max-height: 270px; display: block;" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Radiant Wings -->
            <path d="M115 90 C70 30 20 40 10 85 C18 125 65 145 105 135 Z" fill="url(#bodyOutlineGrad)" stroke="#818cf8" stroke-width="2" />
            <path d="M145 90 C190 30 240 40 250 85 C242 125 195 145 155 135 Z" fill="url(#bodyOutlineGrad)" stroke="#818cf8" stroke-width="2" />
            <!-- Serpent Body & Head -->
            <ellipse cx="130" cy="55" rx="16" ry="20" :fill="isSlotEquipped('head') ? 'url(#bodyEquippedHead)' : 'url(#bodyOutlineGrad)'" :stroke="isSlotEquipped('head') ? '#38bdf8' : '#10b981'" stroke-width="2" />
            <path d="M120 75 C120 120 145 150 160 180 C175 210 165 245 130 255 C85 265 65 225 80 190 C90 165 120 175 115 195 C110 215 130 225 145 220 C160 215 155 190 140 175 C125 160 100 140 100 95 Z" :fill="isSlotEquipped('armor') ? 'url(#bodyEquippedArmor)' : (isSlotEquipped('clothing') ? 'url(#bodyEquippedClothing)' : 'url(#bodyOutlineGrad)')" :stroke="isSlotEquipped('armor') ? '#60a5fa' : '#10b981'" stroke-width="2" />
        </svg>
    </template>

</div>
