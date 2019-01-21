//
// Wardrobe Materia
//
class WardrobeMateriaClass
{
	constructor()
	{
		this.slotToMateria = {};
		this.activeMateria = null;
	}

	//
	// Reset
	//
	reset()
	{
		this.slotToMateria = {};
		this.activeMateria = null;
	}

	//
	// Open materia window
	//
	open()
	{
		$('.tlw-materia-window').show();
		$('.tlw-container').addClass('tlw-blur');
	}

	//
	// Close materia window
	//
	close()
	{
		$('.tlw-materia-window').hide();
		$('.tlw-container').removeClass('tlw-blur');
	}

	//
	// Set the current active materia
	//
	setActive(materia)
	{
		this.activeMateria = materia;
		return this;
	}

	//
	// Add materia to an item
	//
	add(materia)
	{
		// Set active materia
		this.activeMateria = materia;

		// Set preview
		var $preview = $('.tlw-materia-preview'), param = materia.attributes_params[0];
		$preview.find('> div:nth-child(1)').html(`<img src="${materia.icon}" height="24" width="24">`);
		$preview.find('> div:nth-child(2)').html(materia.name);
		$preview.find('> div:nth-child(3)').html(`<em>+${param.value}</em> ${param.name}`)

		// show number of items with materia
		var $itemSection = $('.tlw-materia-items');
		$itemSection.html('');

		// go through slots
		var hasSlots = false;
		for(var slot in WardrobeItems.slotToMateriaCount) {
			var count = WardrobeItems.slotToMateriaCount[slot];

			// if this slot has materia count
			if (count > 0) {
				hasSlots = true;

				// get slot item and name
				var slotItem = WardrobeItems.slotToItem[slot],
					slotName = WardrobeItems.slotToName[slot];

				$itemSection.append(`
					<button data-materia-slot="${slot}">
						<div>${slotName}</div>
						<img src="${slotItem.icon}" height="42" width="42">
					</button>
				`);
			}
		}

		// if slots, show slots, otherwise show no slot message
		if (hasSlots) {
			$('.tlw-materia-hasitems').show();
			$('.tlw-materia-noitems').hide();
		} else {
			$('.tlw-materia-hasitems').hide();
			$('.tlw-materia-noitems').show();
		}

		// Open materia window
		this.open();
	}

	//
	// Attach the current active materia to a slot
	//
	attach(slot)
	{
		var materia = this.activeMateria,
			attrSlot = WardrobeItems.slotToAttributes[slot],
			materiaCount = Object.size(attrSlot.materia),
			materiaSlotIndex = materiaCount + 1,
			materiaSlotIndexAutoSet = false;

		// Find an empty slot
		$(`div[data-slot="${slot}"] .tlw-materia span.active`).each((i, element) => {
			if (!materiaSlotIndexAutoSet && !$(element).attr('data-materia-id')) {
				materiaSlotIndexAutoSet = true;
				materiaSlotIndex = $(element).attr('data-materia');
				return;
			}
		});

		// if the maximum number of materia added,
		// remove the last one
		if (materiaCount >= attrSlot.materia_count) {
			// remove the last entry
			var idToDelete = Object.getLastKey(attrSlot.materia);
			this.remove(slot, idToDelete);

			// reduce slot index
			materiaSlotIndex--;
		}

		// materia id
		var id = generateRandomHash(8),
			materiaData = {
				id: id,
				item_id: materia.id,
				name: materia.name,
				icon: materia.icon,
				stats: [],
			};

		// Add materia to item
		WardrobeItems.slotToAttributes[slot].materia[id] = materiaData;

		// assign slot to materia
		if (!this.slotToMateria[slot]) {
			this.slotToMateria[slot] = {};
		}
		this.slotToMateria[slot][id] = materiaData;

		// Add materia stats
		for(var i in materia.attributes_params) {
			var param = materia.attributes_params[i]

			if (param.value > 0) {
				WardrobeItems.slotToAttributes[slot].materia[id].stats.push({
					id: param.id,
					name: param.name,
					value: param.value,
					value_hq: param.value_hq
				});
			}
		}

		// update slot display
		var $slot = $(`div[data-slot="${slot}"] .tlw-materia span[data-materia="${materiaSlotIndex}"]`);
		$slot.css({ 'background-image': `url(${materia.icon})` })
			 .attr('data-tt', `(${materia.name}) &nbsp; +${materia.attributes_params[0].value} ${materia.attributes_params[0].name}`)
			 .attr('data-materia-id', id);

		// close materia window
		this.close();

		// Generate new stats
		WardrobeStats.build();
	}

	//
	// Remove some materia
	//
	remove(slot, id)
	{
		// update slot display
		var $slot = $(`div[data-slot="${slot}"] .tlw-materia span[data-materia-id="${id}"]`);
		$slot.css({ 'background-image': 'none' }).removeAttr('data-tt').removeAttr('data-materia-id');

		// delete materia
		delete WardrobeItems.slotToAttributes[slot].materia[id];
		delete this.slotToMateria[slot][id];

		// Generate new stats
		WardrobeStats.build();
	}

	//
	// Show the materia manage
	//
	showManager(slot)
	{
		$('.tlw-materia-manage').show();
		$('.tlw-container').addClass('tlw-blur');

		// get materia
		var materia = this.slotToMateria[slot];

		// if materia, show list, otherwise show error
		if (materia) {
			$('.tlw-materia-manage-hasitems').show();
			$('.tlw-materia-manage-noitems').hide();

			var $list = $('.materia-manage-list');
			$list.html('');

			for(var i in materia) {
				var mat = materia[i];

				$list.append(`
					<div class="tlw-materia-attached">
						<div><img src="${mat.icon}" width="24" height="24"></div>
						<div>${mat.name}</div>
						<div><em>+${mat.stats[0].value}</em> ${mat.stats[0].name}</div>
						<div>
							<button class="red small" data-slot="${slot}" data-id="${mat.id}">${languages.custom(330)}</button>
						</div>
					</div>
				`);
			}
		} else {
			$('.tlw-materia-manage-noitems').show();
			$('.tlw-materia-manage-hasitems').hide();
		}
	}

	//
	// Close the materia manager
	//
	closeManager()
	{
		$('.tlw-materia-manage').hide();
		$('.tlw-container').removeClass('tlw-blur');
	}
}
var WardrobeMateria = new WardrobeMateriaClass();
