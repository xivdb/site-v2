//
// XIVDB Map Convert
// - Converts coordinates
//
// Remember, z = y in dats
//
class XIVDBMapsConverterClass
{
	//
	// Convert "in-game" to "2d" format
	//
	gameTo2d(x, y, scale, iconSize)
	{
		// Account for padding on the icon
		iconSize = iconSize;

		// divide scale so it can be used as a multiplication
		scale = scale / 100;

		// force offset
		var offset = 1;

		// calculate x and y
		var x = (x - offset) * 50 * scale;
		var y = (y - offset) * 50 * scale;

		return {
			x: x - (iconSize / 2),
			y: y - (iconSize / 2),
		}
	}

	//
	// Convert "in-game" to "levels" format
 	//
 	gameToLevels(x, y, scale)
	{
		scale = scale / 100;

		x = (x * 50) - 25 - (1024 / scale);
		y = (y * 50) - 25 - (1024 / scale);

		return {
			x: x,
			y: y,
		}
	}

	//
	// Convert "levels" to "in-game" format
	//
	levelsToGame(x, y, scale, offset)
	{
		scale = scale / 100;

		x = (x + offset) * scale;
		y = (y + offset) * scale;

		x = ((41.5 / scale) * ((x + 1024.0) / 2048.0)) + 1;
		y = ((41.5 / scale) * ((y + 1024.0) / 2048.0)) + 1;

		return {
			x: x,
			y: y,
		};
	}
}
