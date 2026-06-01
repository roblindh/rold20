# RoL d20 Ruleset & Web Companion: Technical Summary

## 1. Overview
**RoL d20** (Robert Lind d20) is a custom tabletop role-playing game (RPG) ruleset and companion web application. It is based heavily on the **d20 system** (D&D 3.5E, with design inspirations from 4E and 5E), but heavily streamlined to replace fixed feats/prestige classes with dynamic **skills**, introduce an **action point (AP) economy**, replace saving throws with passive **defense scores**, and use **damage reduction (DR)** for armor instead of Armor Class (AC).

The project serves both as a digital handbook (15-chapter hyperlinked reference manual) and an interactive character/NPC/item generation tool.

---

## 2. Technology Stack
- **Backend Language**: PHP 8.1+
- **Database**: MySQL 8.0 (with a PDO-based data access layer)
- **Web Server**: Apache (running inside Docker)
- **Frontend Layout**: Semantic HTML5 and Vanilla CSS (in `Styles/`)
- **Testing**: PHPUnit 10.0+
- **Containerization**: Docker & Docker Compose

---

## 3. Directory Structure and Components

```
rold20/
├── RulesSrc/               # Core PHP Rules Engine & Database Layer
├── Styles/                 # CSS stylesheets for modern web layout
├── dbdump/                 # MySQL database snapshots (2020 releases)
├── docker/                 # Custom PHP / Apache configuration files
├── images/                 # Theme icons and dragons illustrations
├── nbproject/              # NetBeans IDE project metadata
├── scripts/                # Development and automation helper scripts
├── sphider/                # Internal Sphider PHP web search spider/indexer
├── tests/                  # PHPUnit test suite (Unit & Integration)
├── vendor/                 # Composer package dependencies
├── Dockerfile              # Web service container configuration
├── docker-compose.yml      # Orchestration file for Web, DB, and phpMyAdmin
├── phpunit.xml             # PHPUnit testing configuration
├── application.data        # Serialized PHP configuration & database cache
├── index.php               # Homepage / Introduction handbook entry point
├── page_template.php       # Common site template, login, header, and TOC
├── hb01_* to hb15_*        # Chapter pages and content for the handbook
└── util_*                  # Utility tools for character, NPC, and item generation
```

---

## 4. Architectural Analysis

### A. The Core Rules Engine (`RulesSrc/`)
The rules engine represents the domain layer of the application, parsing game mechanics, stats, and math:
- **`entity.php` (`cEntity`)**: The heart of the game logic. It computes entity attributes, hit points (HP), stamina points (SP), power points (PP), reach, equipment weight limits, action point costs, hit/critical probabilities, and environment-based condition adjustments.
- **`global.php`**: Handlers for initialization (`application_start()`), global arrays (weapon, armor, vehicle categories), and caching mechanisms to store/load parsed database configurations via `application.data`.
- **`trait.php` (`cTraitDescription` / `cTraitEffects`)**: Handles structural modifiers, bonuses, and special abilities parsed from skills, races, templates, or items.
- **`rolcalc.php` (`cExpressionParser`)**: A math evaluation class that dynamically parses algebraic expressions (e.g. `(lvl+3)/5`) used in weapon and skill calculations.
- **`Database.php`**: A singleton PDO-based abstraction wrapper that uses prepared statements to query database records securely.

### B. Web Interface and Companion Tools
- **Handbook Reference Manual**: Chapters `hb01_intro` through `hb15_index` serve as a complete digitised player handbook. The table of contents (`rol_toc()`) and header (`rol_header()`) are built dynamically from `page_template.php`.
- **Generator Utilities**:
  - `util_chargen.php`: Character creation form.
  - `util_npcgen.php`: Form to dynamically configure and generate NPC mobs.
  - `util_itemgen.php`: Crafting and equipment stats builder.
  - `util_campaign.php`: Master tool for campaign state and tracking.

### C. Search Indexer (`sphider/`)
Uses the lightweight **Sphider** PHP search engine to index local handbook content and allow full-text search capability across the game rules.

---

## 5. Data Model & Cache System
The game database comprises over 50 tables representing game configuration assets:
- **Core Stats**: `abilityscores`, `experiencelevels`, `sizes`, `bodytypes`.
- **Entity Config**: `classes`, `creatures`, `templates`, `cultures`.
- **Rules Components**: `skills`, `skillbenefits`, `spells`, `actions`, `modifiers`.
- **Equipment**: `items`, `materials`, `itemtypes`.

### Application Cache (`application.data`)
To avoid querying all database configuration tables on every single web page request, the application caches all static database tables into a single serialized file: `application.data`. The file is loaded during `application_start()` and updated during `application_end()`.

---

## 6. Verification and Test Suite (`tests/`)
The project contains a production-ready test suite utilizing PHPUnit:
- **Unit Tests**:
  - `DatabaseTest.php`: Validates PDO wrapper, query execution, and transactional singleton behavior.
  - `LoggerTest.php`: Asserts logging levels (INFO, WARNING, ERROR, CRITICAL) and file outputs.
  - `ValidatorTest.php`: Tests strict validation constraints (integers, floats, strings, emails, booleans).
  - `EntityTest.php`: Verifies calculations of ability modifiers, HP, SP, PP, and conditions on `cEntity`.
  - `TraitTest.php`: Asserts trait descriptions, constants, and conversion helpers.
- **Integration Tests**:
  - `DatabaseLoggerTest.php`: Tests logging behavior during database errors.
  - `EntityDatabaseTest.php`: Asserts loading and saving entity profiles from database values.