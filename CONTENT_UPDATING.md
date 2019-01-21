# FINAL FANTASY XIV Game Data
  
All data is pulled from the source files. The game requires a paid subscription to be able to update, small patches come out every month and usually do not contain many changes, big updates come out every 3-4 months and have significant file structure changes which will require datamining and heading discovery.

The tools to fork and clone are:
- https://github.com/ufx/SaintCoinach
- https://github.com/viion-misc/xivdb-data-tool
- https://github.com/viion/lodestone-php

SaintCoinach needs a modification for maps to work for XIVDB, you can see the change here: https://github.com/viion/SaintCoinach/blob/2d6e5a5b6a6e114f99a7b05177258460357fb4d8/SaintCoinach.Cmd/Commands/MapCommand.cs - Please implement that chain in your forked copy of SaintCoinach.

## FFXIV Game

You can download the game here: **[https://www.finalfantasyxiv.com/playersdownload/eu/](https://www.finalfantasyxiv.com/playersdownload/eu/)**

## SaintCoinach.Cmd
> I am not the author of this tool and cannot provide support or maintenance.

This tool is a C# App and will need compiling, download and open in Visual Studio and compile to build the SaintCoinach.Cmd.exe file.

This tool rips CSV's from the files and if any known headings exist they will also be pulled however XIVDB's headings may not match perfectly as XIVDB data is in a relational structure. You will need to get familiar with how the data works in order to maintain the site. Square-Enix provide no spoiler information and no headings in the game files, a lot of data is also server side.

SaintCoinach.Cmd.exe is the main app to run, it provides a CLI window in which to run commands, the main ones are:

- `rawexd` get all the CSV data
- `ui` get all the icons
- `maps` get all the maps

### xiv-data-tool
> I am not the author of this tool and cannot provide support or maintenance.

This tool rips strings and formats any LUA conditional strings into JSON. It is a Python app, you only need 1 command:
- `python xivdmcli.py extract json` Extract game strings into JSON files

For maintenance, you will have to manage the files:
- https://github.com/viion-misc/xivdb-data-tool/blob/master/xivdm/json/mapping.py
	- Contains a list of mappings for different types of content, these are offset based
- https://github.com/viion-misc/xivdb-data-tool/blob/master/xivdm/json/Manager.py
	- Contains a list of extractable content. A Game Patch may delete files or add new ones, you will get errors when running the script and would need to manage this file accordingly with what has changed in the patch

## lodestone-php

This is a small PHP library that parses The Lodestone and provides data in an object-oriented fashion. 

Please clone this and maintain it for XIVSYNC

## Site Game-Data Content Updates

A new patch has just been released and you need to update the site, a lot of this process is manually driven and will require the site owner to understand how the FFXIV game-data works, if a major patch is released then expect a lot of changes to the CSV structure known as "offset"changes. 

Square-Enix will always do updates between Monday Morning to Tuesday Morning (24 hour maintenance window). The patch is usually available around Midnight CST (a few hours before the maintenance window ends).

Lets begin!

 - Open up the FINAL FANTASY XIV Launcher
 - Login with your account, it must have a fully paid subscription (Google: FFXIV Mog Station for more information about this or follow the launcher information)
 - Once you login, if there is an update it will begin to download right away, if there is no update it will either say "PLAY" or "Maintenance", if maintenance is on and there is no patch yet, you must close and re-log in later to keep checking (there is no auto-refresh)
 - Once the patch has downloaded it is time to get the game data

### Create a Patch

- Open up the dashboard: **dashboard.xivdb.local**
- Click: Game Data
- Click Patches
- Fill in all the required information, the extract folder should be: `extract-XXX` in the format of the patch, for example if the patch is 4.51 the folder would be called `extract-451`. View previous patches for examples
- This will create the folder: `/extracts/<extract-folder-name>/`

Now we need to populate the folders

- In SaintCoinach.Cmd.exe run the command: `rawexd`
	- A folder will appear with the current patch value, eg: `2018-07-05-0000...`
	- Inside there another folder will be visible: `/rawexd`
	- Copy the contents of `/rawexd` and paste it into the sites `/extracts/<extract-folder-name>/saint/exd` folder.

- In the python xivdb-data-tools folder, run the command: `python xivdmcli.py extract json`
	- A folder called `json_lists` should appear
	- Copy that folder to: `/extracts/<extract-folder-name>/python/json_lists`

Now you have the files in place, it's time to see if anything has changed in this patch. You will need a previous patch extraction to compare against. You can use WinMerge or the XIVDB Diff Viewer to see.
- XIVDB Diff Viewer is located at:
	- Open: dashboard.xivdb.local
	- Go to GameData
	- Go to Data Import
	- Click on a piece of content, eg: Achievements

If everything is setup correctly the Left column will be the "new" and the right column will be the "old" and it will show offset differences if stuff has moved. It cannot do this for 0 values, but it gives you an idea.

If you instead use WinMerge you can sort files by differences and open them up, if the entire left side is "Yellow" this means every row has changed and you will need to figure out what the new offsets are. You can do this by opening the old file and the new file in Excel and comparing the differences to figure out where CSV columns have moved to. This will take some time to get familiar with.

You can find all the offset files here: https://github.com/xivdb/site-v2/tree/master/site/src/XIVDB/Apps/GameData/ExtractClasses - These will need adjusting when updates come to contain the new offsets.

You can find the community driven offset file here: https://github.com/ufx/SaintCoinach/blob/master/SaintCoinach/ex.json
- NOTE: The ex.json file has offsets starting from 0 for the first column, this does not include the "ID" column which XIVDB has at offset 0, you will need to increment all offsets by +1 to match what XIVDB uses. Also some names may not match up.

### Libra Data

Libra was a mobile app that Square-Enix launched as a mobile database. It had an SqLite file that contained a lot of useful information such as Dungeon Loot Tables and Monster information, the app has been discontinued for a new one coming soon https://eu.finalfantasyxiv.com/companion/

You will need to investigate to see if data can be extracted from the companion app and integrated into the website.

### Icons and Maps

- run SaintCoinach.Cmd.exe and enter the command: `ui`
- Delete the icon: 000000/000000.png
- Upload the icons to the server. 
- run SaintCoinach.Cmd.exe and enter the command `maps layered`
- Upload the maps to the server.

Once you have all your offsets sorted and feel comfortable to import:

- Open: dashboard.xivdb.local
- Click: Game Data
- Click: Data Import
- Click: Process All

It will go through all content and if there are any issues it will report the error, you can then open that individual one and process it manually to see what the error is and fix it accordingly.

Once all data has been imported, we need to do some clean up

- Open: dashboard.xivdb.local
- Click: Game Data
- Scroll down to the "Actions" section and click "Remove Null Entries"
- Scroll down to the "Actions" section and click "Fix Leve Rewards"
- Once done, scroll up to the "Connections" section and click "Start"
- Once done you can deploy the FFXIV Game tables
	- Run the command: `/server/database/dump_updated.sh` - https://github.com/xivdb/site-v2/blob/master/server/database/dump_updated.sh
	- This should generate two files:
		- `database_updated.sql` all the FFXIV Content
		- `database_patchlist.sql` the updated patch list

### Updating production

- Put the site into maintenance mode
	- Go into the Production Database
	- Go to the table `site_settings`
	- Add a message to the maintenance only row that exists
		- Any message will turn on maintenance mode and the site will be unaccessible.
- Upload the two SQL files to the server and import them into MYSQL
- Clear all caches:
	- If any site changes, deploy the latest code changes
	- Run `bash site/cleartooltips` to delete old tooltips (they regenerate on the fly)
	- Run: `bash site/cleartwig` to clear Twig cache
	- Run: `redis-cli flushall` to empty Redis
- Turn off maintenance mode by deleting the text in the `site_settings` table row.
