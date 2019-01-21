//
// Wardrobe Stats
//
class WardrobeStatsClass
{
	constructor()
	{
		this.stats = {};
		this.statsToSlot = {};
		this.statsToName = {};
		this.slotToStats = {};
		this.highestStat = 0;
	}

	//
	// Reset
	//
	reset()
	{
		this.stats = {};
		this.statsToSlot = {};
		this.statsToName = {};
		this.slotToStats = {};
		this.highestStat = 0;
	}

	//
	// Build stats
	//
	build()
	{
		this.reset();

		// get slot items
		var slotItems = WardrobeItems.slotToAttributes;

		// build stats
		for(var slot in slotItems) {
			var data = slotItems[slot];
			this.slotToStats[slot] = [];

			// add up stats
			for(var i in data.stats) {
				var attr = data.stats[i];

				// Add stat
				this.addStat(slot, attr, data.is_hq);
			}

			// is there materia?
			if (data.materia) {
				// loop through attached materia
				for(var j in data.materia) {
					// add up stats
					for(var i in data.materia[j].stats) {
						var attr = data.materia[j].stats[i];
						this.addStat(slot, attr, data.is_hq);
					}
				}
			}
		}

		// order stats
		this.stats = sortIt(this.stats);
		this.printStats();
	}

	//
	// Add a stat from an attribute
	//
	addStat(slot, attr, isHq)
	{
		// add to slot
		if (!this.statsToSlot[attr.id]) {
			this.statsToSlot[attr.id] = [];
		}

		// add attribute
		this.statsToSlot[attr.id].push(slot);

		// assign stats to its name counterpart
		this.statsToName[attr.id] = attr.name;

		// add to list of slots
		this.slotToStats[slot].push(attr.name);

		// set values based on if this is stored before or not
		if (this.stats[attr.id]) {
			this.stats[attr.id]
				= this.stats[attr.id]
				+ (isHq ? attr.value_hq : attr.value);
		} else {
			this.stats[attr.id]
				= (isHq ? attr.value_hq : attr.value);
		}

		if (this.stats[attr.id] > this.highestStat) {
			this.highestStat = this.stats[attr.id];
		}
	}

	//
	// Print the stats
	//
	printStats()
	{
		var $window = $('.tlw-stats-window');
		$window.html('');

		var speed = 150;

		for (const [i, stat] of this.stats.entries()) {
			var id = stat[0],
				value = parseInt(stat[1]),
				name = this.statsToName[id],
				percent = (value / this.highestStat) * 100;

			$window.append(`
				<div class="tlw-stats-line" data-id="${id}">
					<div class="tlw-stats-values">
						<em>${value}</em> ${name}
					</div>
					<div class="tlw-stats-graph">
						<span style="width:${percent}%"></span>
					</div>
				</div>
			`);

			$window.find(`[data-id="${id}"]`).stop(true, true).animate({
				opacity: 1,
				left: 0,
			}, (speed * i));
		}
	}

	//
	// Highlight which slots have this stat
	//
	highlightStat(attrId, removeHighlight)
	{
		for(var i in this.statsToSlot[attrId]) {
			var slot = this.statsToSlot[attrId][i],
				$slot = $(`.tlw-slots-icons > div[data-slot="${slot}"]`);

			removeHighlight ? $slot.removeClass('highlight') : $slot.addClass('highlight');
		}
	}
}

var WardrobeStats = new WardrobeStatsClass();
