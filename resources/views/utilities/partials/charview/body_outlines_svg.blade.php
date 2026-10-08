<!-- SVG Body Type Outlines for Character Equipment Paperdoll -->
<div class="w-full h-full flex items-center justify-center">

    <!-- Shared Gradient Definitions -->
    <svg class="absolute w-0 h-0" aria-hidden="true" focusable="false">
        <defs>
            <linearGradient id="bodyOutlineGrad" x1="0%" y1="0%" x2="100%" y2="100%">
                <stop offset="0%" stop-color="#475569" stop-opacity="0.85" />
                <stop offset="50%" stop-color="#334155" stop-opacity="0.9" />
                <stop offset="100%" stop-color="#1e293b" stop-opacity="0.95" />
            </linearGradient>
            <linearGradient id="bodyAccentGrad" x1="0%" y1="0%" x2="0%" y2="100%">
                <stop offset="0%" stop-color="#818cf8" stop-opacity="0.4" />
                <stop offset="100%" stop-color="#6366f1" stop-opacity="0.1" />
            </linearGradient>
            <filter id="nodeGlow" x="-20%" y="-20%" width="140%" height="140%">
                <feGaussianBlur stdDeviation="2" result="blur" />
                <feComposite in="SourceGraphic" in2="blur" operator="over" />
            </filter>
        </defs>
    </svg>

    <!-- 0: Generic Humanoid -->
    <template x-if="characterBodyType === 0">
        <svg viewBox="0 0 240 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Head -->
            <circle cx="120" cy="40" r="20" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Torso -->
            <path d="M92 68 C105 64 135 64 148 68 L154 130 C150 155 138 168 120 170 C102 168 90 155 86 130 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Left Arm -->
            <path d="M90 70 C75 95 62 130 55 160 C52 175 60 182 66 178 C74 172 82 145 92 120 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Right Arm -->
            <path d="M150 70 C165 95 178 130 185 160 C188 175 180 182 174 178 C166 172 158 145 148 120 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Waist / Belt -->
            <path d="M98 170 L142 170 L138 184 L102 184 Z" fill="#6366f1" fill-opacity="0.3" stroke="#818cf8" stroke-width="1.5" />
            <!-- Left Leg -->
            <path d="M102 184 L96 250 L92 290 L110 290 L118 250 L120 188 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Right Leg -->
            <path d="M138 184 L144 250 L148 290 L130 290 L122 250 L120 188 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Feet -->
            <ellipse cx="101" cy="293" rx="12" ry="5" fill="#475569" stroke="#94a3b8" stroke-width="1.5" />
            <ellipse cx="139" cy="293" rx="12" ry="5" fill="#475569" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 1: Biped (Humanoid) -->
    <template x-if="characterBodyType === 1">
        <svg viewBox="0 0 240 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Head & Neck -->
            <ellipse cx="120" cy="38" rx="19" ry="23" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M112 60 L128 60 L130 68 L110 68 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1" />
            <!-- Shoulders & Chest -->
            <path d="M88 68 C104 62 136 62 152 68 L158 135 C154 160 140 172 120 174 C100 172 86 160 82 135 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Left Arm & Hand -->
            <path d="M86 70 C72 96 58 135 52 165 C48 180 56 186 63 182 C72 176 80 148 90 125 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <ellipse cx="55" cy="184" rx="7" ry="9" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Right Arm & Hand -->
            <path d="M154 70 C168 96 182 135 188 165 C192 180 184 186 177 182 C168 176 160 148 150 125 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <ellipse cx="185" cy="184" rx="7" ry="9" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Waist / Belt Band -->
            <path d="M96 174 L144 174 L140 190 L100 190 Z" fill="#6366f1" fill-opacity="0.3" stroke="#818cf8" stroke-width="1.5" />
            <!-- Left Leg -->
            <path d="M100 190 L94 255 L90 292 L109 292 L117 255 L120 194 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M85 292 L111 292 C111 297 85 299 85 292 Z" fill="#334155" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Right Leg -->
            <path d="M140 190 L146 255 L150 292 L131 292 L123 255 L120 194 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M129 292 L155 292 C155 297 129 299 129 292 Z" fill="#334155" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 2: Biped, winged (Winged Humanoid / Avariel) -->
    <template x-if="characterBodyType === 2">
        <svg viewBox="0 0 260 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Left Wing -->
            <path d="M100 85 C65 35 25 30 10 65 C0 95 20 150 55 180 C80 200 100 185 105 145 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M10 65 C25 85 45 140 75 165" stroke="#818cf8" stroke-width="1" />
            <!-- Right Wing -->
            <path d="M160 85 C195 35 235 30 250 65 C260 95 240 150 205 180 C180 200 160 185 155 145 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M250 65 C235 85 215 140 185 165" stroke="#818cf8" stroke-width="1" />
            <!-- Head -->
            <ellipse cx="130" cy="40" rx="18" ry="22" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Torso -->
            <path d="M102 70 C116 64 144 64 158 70 L164 135 C160 160 148 172 130 174 C112 172 100 160 96 135 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Arms -->
            <path d="M98 72 C85 98 72 135 68 165 C65 178 72 184 78 180 C86 174 94 148 102 125 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M162 72 C175 98 188 135 192 165 C195 178 188 184 182 180 C174 174 166 148 158 125 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Belt & Legs -->
            <path d="M108 174 L152 174 L148 188 L112 188 Z" fill="#6366f1" fill-opacity="0.3" stroke="#818cf8" stroke-width="1.5" />
            <path d="M112 188 L106 255 L102 292 L120 292 L127 255 L130 192 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M148 188 L154 255 L158 292 L140 292 L133 255 L130 192 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 3: Quadruped (Horse / Canine / Feline) -->
    <template x-if="characterBodyType === 3">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Head & Muzzle -->
            <path d="M45 75 C45 60 70 50 85 68 L105 110 L75 118 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <polygon points="75,55 82,40 88,54" fill="#475569" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Neck & Body -->
            <path d="M85 70 C105 85 125 110 135 125 C175 120 215 125 240 145 C248 170 245 200 230 215 L105 210 C85 190 75 150 85 115 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Saddle Placement Zone Indicator -->
            <path d="M145 122 C165 118 195 118 215 125 L210 160 C190 165 165 165 145 160 Z" fill="#f59e0b" fill-opacity="0.25" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="3 3" />
            <!-- Forelegs -->
            <path d="M98 210 L90 285 L84 300 L102 300 L112 285 L118 210 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M120 210 L115 285 L110 300 L126 300 L134 285 L138 210 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Hindlegs -->
            <path d="M210 215 L200 285 L195 300 L212 300 L222 285 L232 215 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M230 215 L225 285 L220 300 L236 300 L244 285 L248 215 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Tail -->
            <path d="M242 150 C260 175 270 220 260 265 C255 250 250 210 240 185" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 4: Quadruped, winged (Pegasus / Griffon / Dragon) -->
    <template x-if="characterBodyType === 4">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Outspread Wings -->
            <path d="M140 115 C110 40 55 25 25 55 C10 80 40 120 85 140 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M170 115 C200 40 255 25 275 55 C285 80 255 120 210 140 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <!-- Head & Muzzle -->
            <path d="M45 80 C45 65 70 55 85 72 L105 115 L75 122 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Quad Body -->
            <path d="M85 75 C105 90 125 115 135 130 C175 125 215 130 240 150 C248 175 245 205 230 220 L105 215 C85 195 75 155 85 120 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Legs -->
            <path d="M98 215 L90 290 L84 302 L102 302 L112 290 L118 215 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M210 220 L200 290 L195 302 L212 302 L222 290 L232 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Tail -->
            <path d="M242 155 C260 180 270 225 260 270" stroke="#94a3b8" stroke-width="2" />
        </svg>
    </template>

    <!-- 5: Multiped (Arachnid / Insectoid / Crab) -->
    <template x-if="characterBodyType === 5">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Cephalothorax -->
            <ellipse cx="140" cy="115" rx="35" ry="40" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Abdomen -->
            <ellipse cx="140" cy="210" rx="48" ry="55" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Pedipalps / Mandibles -->
            <path d="M125 80 L115 60 L122 55" stroke="#94a3b8" stroke-width="2" />
            <path d="M155 80 L165 60 L158 55" stroke="#94a3b8" stroke-width="2" />
            <!-- 8 Articulated Legs -->
            <!-- Left Legs -->
            <path d="M115 100 L60 70 L25 105" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M110 115 L50 110 L15 155" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M110 130 L55 160 L20 215" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M115 145 L65 205 L35 275" stroke="#94a3b8" stroke-width="2" fill="none" />
            <!-- Right Legs -->
            <path d="M165 100 L220 70 L255 105" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M170 115 L230 110 L265 155" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M170 130 L225 160 L260 215" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M165 145 L215 205 L245 275" stroke="#94a3b8" stroke-width="2" fill="none" />
        </svg>
    </template>

    <!-- 6: Multiped, winged (Winged Insectoid) -->
    <template x-if="characterBodyType === 6">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Insect Wings -->
            <path d="M125 100 C75 35 25 35 15 75 C10 110 50 140 110 130 Z" fill="#818cf8" fill-opacity="0.2" stroke="#818cf8" stroke-width="1.5" />
            <path d="M155 100 C205 35 255 35 265 75 C270 110 230 140 170 130 Z" fill="#818cf8" fill-opacity="0.2" stroke="#818cf8" stroke-width="1.5" />
            <!-- Body -->
            <ellipse cx="140" cy="115" rx="30" ry="35" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <ellipse cx="140" cy="200" rx="38" ry="50" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Legs -->
            <path d="M115 110 L50 110 L20 160" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M115 130 L60 175 L35 240" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M165 110 L230 110 L260 160" stroke="#94a3b8" stroke-width="2" fill="none" />
            <path d="M165 130 L220 175 L245 240" stroke="#94a3b8" stroke-width="2" fill="none" />
        </svg>
    </template>

    <!-- 7: Blob (Ooze / Slime / Amorphous) -->
    <template x-if="characterBodyType === 7">
        <svg viewBox="0 0 260 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <path d="M130 65 C185 55 225 100 220 160 C215 220 190 270 130 275 C70 275 40 220 40 160 C40 95 75 75 130 65 Z" fill="url(#bodyOutlineGrad)" stroke="#10b981" stroke-width="2.5" stroke-dasharray="6 3" />
            <!-- Pseudopods & Inner Nucleus -->
            <circle cx="130" cy="165" r="28" fill="#10b981" fill-opacity="0.3" stroke="#34d399" stroke-width="2" />
            <ellipse cx="75" cy="130" rx="14" ry="10" fill="#10b981" fill-opacity="0.25" />
            <ellipse cx="185" cy="190" rx="16" ry="12" fill="#10b981" fill-opacity="0.25" />
            <ellipse cx="130" cy="225" rx="20" ry="10" fill="#10b981" fill-opacity="0.25" />
        </svg>
    </template>

    <!-- 8: Centauroid (Centaur / Drider) -->
    <template x-if="characterBodyType === 8">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Upper Humanoid Torso -->
            <circle cx="105" cy="35" r="16" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M82 58 C92 54 118 54 128 58 L132 110 C128 128 118 135 105 135 C92 135 82 128 78 110 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Upper Arms -->
            <path d="M80 60 C68 80 58 110 52 130 C48 142 55 146 60 144 C67 140 74 120 82 102 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M130 60 C142 80 152 110 158 130 C162 142 155 146 150 144 C143 140 136 120 128 102 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Lower Equine Body -->
            <path d="M105 135 C130 135 190 135 230 155 C242 178 240 208 225 225 L105 220 C85 200 78 165 105 135 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Saddle Zone -->
            <path d="M150 135 C170 132 195 132 215 138 L210 170 C190 174 170 174 150 170 Z" fill="#f59e0b" fill-opacity="0.25" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="3 3" />
            <!-- Forelegs & Hindlegs -->
            <path d="M98 220 L92 288 L86 300 L104 300 L112 288 L118 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M210 225 L200 288 L195 300 L212 300 L222 288 L230 225 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <!-- Tail -->
            <path d="M235 160 C255 185 265 230 255 270" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 9: Centauroid, winged (Winged Centaur) -->
    <template x-if="characterBodyType === 9">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Wings -->
            <path d="M105 70 C70 15 20 15 10 50 C5 75 35 110 80 125 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <path d="M130 70 C165 15 215 15 235 50 C245 75 215 110 170 125 Z" fill="url(#bodyOutlineGrad)" fill-opacity="0.75" stroke="#818cf8" stroke-width="1.5" stroke-dasharray="4 2" />
            <!-- Upper Torso -->
            <circle cx="105" cy="35" r="16" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M82 58 C92 54 118 54 128 58 L132 110 C128 128 118 135 105 135 C92 135 82 128 78 110 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Lower Body -->
            <path d="M105 135 C130 135 190 135 230 155 C242 178 240 208 225 225 L105 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Legs -->
            <path d="M98 220 L92 288 L86 300 L104 300 L112 288 L118 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
            <path d="M210 225 L200 288 L195 300 L212 300 L222 288 L230 225 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 10: Ichtyoid (Aquatic / Fish / Shark / Mer-form) -->
    <template x-if="characterBodyType === 10">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Dorsal Fin -->
            <path d="M125 105 C140 60 170 55 185 85 L160 120 Z" fill="url(#bodyOutlineGrad)" stroke="#0ea5e9" stroke-width="1.5" />
            <!-- Streamlined Torso -->
            <path d="M40 160 C75 110 165 105 210 150 L245 125 L238 160 L245 195 L210 170 C165 215 75 210 40 160 Z" fill="url(#bodyOutlineGrad)" stroke="#0ea5e9" stroke-width="2" />
            <!-- Pectoral Fin -->
            <path d="M100 175 C115 210 135 230 150 225 L135 180 Z" fill="url(#bodyOutlineGrad)" stroke="#0ea5e9" stroke-width="1.5" />
            <!-- Eye -->
            <circle cx="65" cy="150" r="4" fill="#38bdf8" />
            <!-- Saddle zone -->
            <ellipse cx="140" cy="155" rx="30" ry="18" fill="#f59e0b" fill-opacity="0.2" stroke="#f59e0b" stroke-width="1.5" stroke-dasharray="3 3" />
        </svg>
    </template>

    <!-- 11: Avian (Bird / Raptor / Roc) -->
    <template x-if="characterBodyType === 11">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Left Wing -->
            <path d="M125 110 C80 60 25 75 10 120 C18 160 65 180 115 170 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M10 120 C30 145 70 165 115 170" stroke="#818cf8" stroke-width="1" />
            <!-- Right Wing -->
            <path d="M155 110 C200 60 255 75 270 120 C262 160 215 180 165 170 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M270 120 C250 145 210 165 165 170" stroke="#818cf8" stroke-width="1" />
            <!-- Head & Curved Beak -->
            <ellipse cx="140" cy="65" rx="16" ry="18" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <path d="M152 65 L168 72 L150 78 Z" fill="#f59e0b" stroke="#d97706" stroke-width="1.5" />
            <!-- Body -->
            <path d="M124 82 C134 80 146 80 156 82 L162 180 C156 215 148 230 140 232 C132 230 124 215 118 180 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="2" />
            <!-- Taloned Legs -->
            <path d="M128 232 L120 275 L110 285" stroke="#f59e0b" stroke-width="2.5" fill="none" />
            <path d="M152 232 L160 275 L170 285" stroke="#f59e0b" stroke-width="2.5" fill="none" />
            <!-- Fan Tail -->
            <path d="M130 220 L115 270 L140 260 L165 270 L150 220 Z" fill="url(#bodyOutlineGrad)" stroke="#94a3b8" stroke-width="1.5" />
        </svg>
    </template>

    <!-- 12: Ophidian (Serpent / Snake / Naga) -->
    <template x-if="characterBodyType === 12">
        <svg viewBox="0 0 260 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Hood & Head -->
            <ellipse cx="130" cy="55" rx="18" ry="22" fill="url(#bodyOutlineGrad)" stroke="#10b981" stroke-width="2" />
            <path d="M112 55 C98 68 98 95 114 110 C124 100 124 75 112 55 Z" fill="#10b981" fill-opacity="0.3" stroke="#34d399" stroke-width="1.5" />
            <path d="M148 55 C162 68 162 95 146 110 C136 100 136 75 148 55 Z" fill="#10b981" fill-opacity="0.3" stroke="#34d399" stroke-width="1.5" />
            <!-- Coiling Muscular Body -->
            <path d="M120 75 C120 120 140 145 155 170 C175 200 170 240 130 250 C85 260 60 220 75 185 C85 160 115 170 110 190 C105 210 125 220 140 215 C155 210 155 190 140 175 C125 160 100 140 100 95 Z" fill="url(#bodyOutlineGrad)" stroke="#10b981" stroke-width="2" />
            <!-- Tail Tip -->
            <path d="M130 250 C155 255 190 270 205 285 L215 280" stroke="#34d399" stroke-width="2" fill="none" />
        </svg>
    </template>

    <!-- 13: Ophidian, winged (Couatl / Feathered Serpent) -->
    <template x-if="characterBodyType === 13">
        <svg viewBox="0 0 280 320" class="w-full h-full max-h-[300px] drop-shadow-lg" fill="none" xmlns="http://www.w3.org/2000/svg">
            <!-- Radiant Wings -->
            <path d="M115 90 C70 30 20 40 10 85 C18 125 65 145 105 135 Z" fill="url(#bodyOutlineGrad)" stroke="#818cf8" stroke-width="2" />
            <path d="M145 90 C190 30 240 40 250 85 C242 125 195 145 155 135 Z" fill="url(#bodyOutlineGrad)" stroke="#818cf8" stroke-width="2" />
            <!-- Serpent Body -->
            <ellipse cx="130" cy="55" rx="16" ry="20" fill="url(#bodyOutlineGrad)" stroke="#10b981" stroke-width="2" />
            <path d="M120 75 C120 120 145 150 160 180 C175 210 165 245 130 255 C85 265 65 225 80 190 C90 165 120 175 115 195 C110 215 130 225 145 220 C160 215 155 190 140 175 C125 160 100 140 100 95 Z" fill="url(#bodyOutlineGrad)" stroke="#10b981" stroke-width="2" />
        </svg>
    </template>

</div>
