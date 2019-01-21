# XIVSync

Parse lodestone!

## Changelog - March 31st 2017

- SE updated lodestone http://eu.finalfantasyxiv.com/lodestone/special/update_log/
- Sync broke, now fixed, new stuff:
  - Gear now includes the character ID for crafted gear signatures.
  - Gear now includes information about Materia (sadly, not the ID's at the moment).
  - Gear now includes the ID for the dye used.
  - Gear now includes the ID for the glamor used.
  - Search has been implemented.
  
## Deploy a new server:

- Create node, use snapshot characters
- Add mysql user and update config.php
- Run benchmark


## Update priority queues

3 Servers running 4 queues (100 characters per queue)
Total Queues: 12

- Priority 1 = Active in the last 3 days (2 queues)
- Priority 2 = Active in the last 7 days (3 queues)
- Priority 3 = Active in the last 14 days (3 queues)
- Priority 4 = Active in the last 30 days (2 queues)
- Priority 5 = Active over 30 days (2 queues)

## API Routes

> /character/add/{id}

Add a character

> /character/update/{id}

Request character update (put at front of queue)

> /character/update/{id}/force

Forcefully update a character

> /character/parse/{id}

Parse a character live from lodestone

> /character/search - name, server

Search for characters via name/server

> /lodestone/topics

> /lodestone/notices

> /lodestone/maintenance

> /lodestone/updates

> /lodestone/status

> /lodestone/worldstatus

Get information from lodestone homepage


---

## Active Hash

Generated on:

- I do not include images, SE likes to mess with these
- I do not include FC. Being kicked from an FC does not make you "Active"

```json
   {
        "id": "730968",
        "name": "Premium Virtue",
        "server": "Phoenix",
        "title": null,
        "biography": "4f77e6744e",
        "race": "Hyur",
        "clan": "Midlander",
        "gender": "female",
        "nameday": "2nd Sun of the 6th Umbral Moon",
        "guardian": {
            "name": "Azeyma, the Warden"
        },
        "city": {
            "name": "Limsa Lominsa"
        },
        "grand_company": {
            "name": "Immortal Flames",
            "rank": "Second Flame Lieutenant"
        },
        "classjobs": {
            "gladiator": {
                "name": "Gladiator",
                "level": 50,
                "exp": {
                    "current": 479419,
                    "max": 864000
                }
            },
            "pugilist": {
                "name": "Pugilist",
                "level": 50,
                "exp": {
                    "current": 0,
                    "max": 864000
                }
            },
            "marauder": {
                "name": "Marauder",
                "level": 52,
                "exp": {
                    "current": 841365,
                    "max": 1267200
                }
            },
            "lancer": {
                "name": "Lancer",
                "level": 50,
                "exp": {
                    "current": 0,
                    "max": 864000
                }
            },
            "archer": {
                "name": "Archer",
                "level": 60,
                "exp": {
                    "current": 0,
                    "max": 0
                }
            },
            "rogue": {
                "name": "Rogue",
                "level": 33,
                "exp": {
                    "current": 48426,
                    "max": 203500
                }
            },
            "conjurer": {
                "name": "Conjurer",
                "level": 50,
                "exp": {
                    "current": 678081,
                    "max": 864000
                }
            },
            "thaumaturge": {
                "name": "Thaumaturge",
                "level": 50,
                "exp": {
                    "current": 0,
                    "max": 864000
                }
            },
            "arcanist": {
                "name": "Arcanist",
                "level": 50,
                "exp": {
                    "current": 0,
                    "max": 864000
                }
            },
            "dark knight": {
                "name": "Dark Knight",
                "level": 46,
                "exp": {
                    "current": 390821,
                    "max": 437600
                }
            },
            "machinist": {
                "name": "Machinist",
                "level": 30,
                "exp": {
                    "current": 19583,
                    "max": 162500
                }
            },
            "astrologian": {
                "name": "Astrologian",
                "level": 30,
                "exp": {
                    "current": 0,
                    "max": 162500
                }
            },
            "carpenter": {
                "name": "Carpenter",
                "level": 9,
                "exp": {
                    "current": 4237,
                    "max": 9930
                }
            },
            "blacksmith": {
                "name": "Blacksmith",
                "level": 20,
                "exp": {
                    "current": 45043,
                    "max": 56600
                }
            },
            "armorer": {
                "name": "Armorer",
                "level": 50,
                "exp": {
                    "current": 0,
                    "max": 864000
                }
            },
            "goldsmith": {
                "name": "Goldsmith",
                "level": 17,
                "exp": {
                    "current": 7417,
                    "max": 40500
                }
            },
            "leatherworker": {
                "name": "Leatherworker",
                "level": 22,
                "exp": {
                    "current": 9722,
                    "max": 71400
                }
            },
            "weaver": {
                "name": "Weaver",
                "level": 17,
                "exp": {
                    "current": 6400,
                    "max": 40500
                }
            },
            "alchemist": {
                "name": "Alchemist",
                "level": 50,
                "exp": {
                    "current": 0,
                    "max": 864000
                }
            },
            "culinarian": {
                "name": "Culinarian",
                "level": 28,
                "exp": {
                    "current": 120735,
                    "max": 140200
                }
            },
            "miner": {
                "name": "Miner",
                "level": 50,
                "exp": {
                    "current": 769013,
                    "max": 864000
                }
            },
            "botanist": {
                "name": "Botanist",
                "level": 42,
                "exp": {
                    "current": 170950,
                    "max": 356800
                }
            },
            "fisher": {
                "name": "Fisher",
                "level": 26,
                "exp": {
                    "current": 23558,
                    "max": 109800
                }
            }
        },
        "stats": {
            "core": {
                "HP": "914",
                "MP": "309",
                "TP": "1000"
            },
            "attributes": {
                "Strength": 117,
                "Dexterity": 166,
                "Vitality": 126,
                "Intelligence": 67,
                "Mind": 75,
                "Piety": 67
            },
            "offensive": {
                "Accuracy": 217,
                "Critical Hit Rate": 197,
                "Determination": 113
            },
            "defensive": {
                "Defense": 128,
                "Parry": 194,
                "Magic Defense": 128
            },
            "mental": {
                "Attack Magic Potency": 67,
                "Healing Magic Potency": 75,
                "Spell Speed": 194
            },
            "resistances": {
                "Slow Resistance": 0,
                "Silence Resistance": 0,
                "Blind Resistance": 0,
                "Poison Resistance": 0,
                "Stun Resistance": 0,
                "Sleep Resistance": 0,
                "Bind Resistance": 0,
                "Heavy Resistance": 0,
                "Slashing": 100,
                "Piercing": 100,
                "Blunt": 100
            }
        },
        "mounts": [
            "Company Chocobo",
            "Legacy Chocobo",
            "Black Chocobo",
            "Fat Chocobo",
            "Magitek Armor",
            "Goobbue",
            "Coeurl",
            "Ahriman",
            "Behemoth",
            "Griffin",
            "Bennu",
            "Manacutter",
            "Unicorn",
            "Midgardsormr"
        ],
        "minions": [
            "Wayward Hatchling",
            "Black Chocobo Chick",
            "Princely Hatchling",
            "Chocobo Chick Courier",
            "Midgardsormr",
            "Cherry Bomb",
            "Baby Behemoth",
            "Baby Bun",
            "Wide-eyed Fawn",
            "Baby Raptor",
            "Wolf Pup",
            "Coeurl Kitten",
            "Dust Bunny",
            "Pudgy Puk",
            "Buffalo Calf",
            "Smallshell",
            "Infant Imp",
            "Beady Eye",
            "Fledgling Dodo",
            "Coblyn Larva",
            "Goobbue Sproutling",
            "Bite-sized Pudding",
            "Demon Brick",
            "Accompaniment Node",
            "Poro Roggo",
            "Mammet #001",
            "Cait Sith Doll",
            "Gravel Golem",
            "Wind-up Moogle",
            "Wind-up Goblin",
            "Wind-up Cursor",
            "Wind-up Airship",
            "Minion Of Light",
            "Wind-up Leader",
            "Wind-up Odin",
            "Wind-up Gilgamesh",
            "Wind-up Warrior Of Light",
            "Wind-up Firion",
            "Wind-up Kain",
            "Wind-up Alphinaud",
            "Wind-up Cid",
            "Wind-up Nanamo",
            "Wind-up Haurchefant",
            "Wind-up Aymeric"
        ],
        "gear": {
            "mainhand": {
                "id": "4b8535c5c6d",
                "name": "Steel Daggers",
                "mirage_id": false,
                "creator_id": false,
                "dye_id": false,
                "materia": []
            },
            "head": {
                "id": "d8e4fe7808a",
                "name": "Velveteen Turban",
                "mirage_id": "223e1752ce8",
                "creator_id": false,
                "dye_id": false,
                "materia": []
            },
            "body": {
                "id": "6effea1bca4",
                "name": "Velveteen Shirt",
                "mirage_id": "a6c6256304e",
                "creator_id": "10996997",
                "dye_id": "0c0c7f94f09",
                "materia": []
            },
            "hands": {
                "id": "78a6ccd0c5f",
                "name": "Cotton Bracers",
                "mirage_id": "9c320478ad3",
                "creator_id": "9173315",
                "dye_id": "0c0c7f94f09",
                "materia": []
            },
            "waist": {
                "id": "29eb768dca1",
                "name": "Toadskin Hunting Belt",
                "mirage_id": false,
                "creator_id": "14994637",
                "dye_id": false,
                "materia": []
            },
            "legs": {
                "id": "f2b77145ab5",
                "name": "Cotton Kecks",
                "mirage_id": "caacc5f2a0b",
                "creator_id": "12416276",
                "dye_id": "0c0c7f94f09",
                "materia": []
            },
            "feet": {
                "id": "2efd3123a7d",
                "name": "Iron-plated Jackboots",
                "mirage_id": "8878c87eb42",
                "creator_id": "16549138",
                "dye_id": false,
                "materia": []
            },
            "earrings": {
                "id": "36ca2a6ca8e",
                "name": "Brass Ear Cuffs",
                "mirage_id": false,
                "creator_id": false,
                "dye_id": false,
                "materia": []
            },
            "necklace": {
                "id": "980219512af",
                "name": "Silver Gorget",
                "mirage_id": false,
                "creator_id": "6151052",
                "dye_id": false,
                "materia": []
            },
            "bracelets": {
                "id": "78643d85686",
                "name": "Goatskin Wristbands",
                "mirage_id": false,
                "creator_id": "7386506",
                "dye_id": false,
                "materia": []
            },
            "ring1": {
                "id": "83d84a9493d",
                "name": "Silver Ring",
                "mirage_id": false,
                "creator_id": false,
                "dye_id": false,
                "materia": []
            },
            "ring2": {
                "id": "83d84a9493d",
                "name": "Silver Ring",
                "mirage_id": false,
                "creator_id": false,
                "dye_id": false,
                "materia": []
            }
        },
        "active_class": {
            "id": 29,
            "level": "33",
            "name": "Rogue"
        }
    }
```
