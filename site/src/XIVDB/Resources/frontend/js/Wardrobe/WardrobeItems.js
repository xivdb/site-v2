//
// Wardrobe Items
//
class WardrobeItemsClass
{
	constructor()
	{
		this.slotToItem = {};
		this.slotToAttributes = {};
		this.slotToMateriaCount = {};
		this.slotToName = {};

		// css elements
		this.cssDropZone = '.tool-wardrobe-items';

		//
		// slots link based on: slot_equip
		//
		this.mainHandSlots = [ 1 ];
		this.offHandSlots = [ 2 ];
		this.soulSlots = [ 13 ];
		this.gearSlots = [
			3, 4, 5, 6, 7, 8,
			9, 10, 11, 121, 122, 13
		];

		//
		// slots link based on: item_ui_category
		//
		this.medicineSlots = [
			44,
		];
		this.foodSlots = [
			46,47
		];
		this.materiaSlots = [
			58,
		];
	}

	//
	// Reset
	//
	reset()
	{
		this.slotToItem = {};
		this.slotToAttributes = {};
		this.slotToMateriaCount = {};
		this.slotToName = {};
	}

	//
	// Add an item to the wardrobe
	//
	add(itemId, callback)
	{
		Wardrobe.loading();

		// hide dropzone title
		$(this.cssDropZone).find('em').hide();

		// get item data
		WardrobeAPI.getItem(itemId, (data) => {
			Wardrobe.loading(true);

			if (callback) {
				callback(data);
			}

		    // if item doesn't have a slot, pass it on and skip
			if (data.slot_equip == 0) {
				this.addExtra(data);
				return;
			}

			// If item is materia, we need to handle it differently
			if (data.slot_equip == 16) {
				WardrobeMateria.add(data);
				return;
			}

			// If its a ring, modify the slot based on which ring is
			// empty, if the left one is empty, its slot 121, otherwise
			// set it to 122, it will always overwrite 122 until 121 is empty
			if (data.slot_equip == 12) {
				data.slot_equip
					= ($('.tlw-slots div[data-slot="121"] .tlw-slot').attr('data-equipped') == 0)
					? 121 : 122;
			}

			// set slot name
			this.slotToName[data.slot_equip] = data.slot_name;

			// add to object
			this.slotToItem[data.slot_equip] = data;

			// initialize new slot stat
			this.slotToAttributes[data.slot_equip] = {
				item_id: data.id,
				name: data.name,
				icon: data.icon,
				slot: data.slot_equip,
				level_equip: data.level_equip,
				level_item: data.level_item,
				materia_count: data.materia_slot_count,
				is_hq: false,
				stats: [],
				materia: {},
			};

			// Build base stats, doing manuallish as its a single array mixing hq/nq
			//
			// DPS is specifically ignored because it will be misrepresented based on
			// all other stats included.
			var baseParams = [
				'damage', 'magic_damage', 'defense', 'magic_defense',
				'block_strength', 'block_rate', 'auto_attack', 'delay'
			];

			// Build stats from base parameters
			for(var i in baseParams) {
				var key = baseParams[i],
					value = data.attributes_base[key],
					value_hq = data.attributes_base[key + '_hq'];

				if (value > 0) {
					this.slotToAttributes[data.slot_equip].stats.push({
						id: key,
						name: this.getBaseAttributeName(key),
						value: value,
						value_hq: value_hq
					});
				}
			}

			// Build stats from attribute parameters
			for(var i in data.attributes_params) {
				var param = data.attributes_params[i];

				if (param.value > 0) {
					this.slotToAttributes[data.slot_equip].stats.push({
						id: param.id,
						name: param.name,
						value: param.value,
						value_hq: param.value_hq
					});
				}
			}

			// equip item
			this.equip(data);

			// add materia if any
			this.activateMateriaSlots(data.materia_slot_count, data.slot_equip);
			this.slotToMateriaCount[data.slot_equip] = data.materia_slot_count;

			// Build gear
			WardrobeStats.build();
		});
	}

	//
	// Handle extras, such as medcine, materia, etc
	//
	addExtra(item)
	{
		//console.log(item);
	}

	//
	// Convert placeholder to translation
	//
	getBaseAttributeName(key)
	{
		var data = {
			'damage' : languages.params(12),
			'magic_damage' : languages.params(13),
			'defense' : languages.params(21),
			'magic_defense' : languages.params(24),
			'block_strength' : languages.params(18),
			'block_rate' : languages.params(17),
			'auto_attack' : languages.params(20),
			'dps' : languages.custom(329),
			'delay' : languages.params(14),
		};

		return data[key];
	}

	//
	// Equip item
	//
	equip(item)
	{
		// get slot
		var $slot = $(`.tlw-slots div[data-slot="${item.slot_equip}"]`);

		// add icon
		$slot.find('.tlw-slot').html(`<img src="${item.icon}" data-tooltip-id="item/${item.id}" data-xivdb-replace="1">`).attr('data-equipped', 1);

		// enable delete button
		$slot.find('.tlw-remove-button').addClass('active');

		// if it can be hq'd, enable HQ button
		if (typeof item.attributes_params[0] != 'undefined') {
			var nqValue = item.attributes_params[0].value,
				hqValue = item.attributes_params[0].value_hq;

			if (hqValue > 0 && nqValue != hqValue) {
				$slot.find('.tlw-hq-button').addClass('active');
			}
		}

		XIVDBTooltips.get();
	}

	//
	// Remove an item
	//
	unequip(slot)
	{
		var $slot = $(`.tlw-slots div[data-slot="${slot}"]`);

		// remove all materia
		$slot.find('.tlw-materia span[data-materia-id]').each((i, element) => {
			WardrobeMateria.remove(slot, $(element).attr('data-materia-id'));
		});

		// delete slots
		delete this.slotToItem[slot];
		delete this.slotToAttributes[slot];

		// get slot
		$slot.find('.tlw-slot img').remove();
		$slot.find('.tlw-slot').attr('data-equipped', 0);
		$slot.find('.tlw-remove-button').removeClass('active');
		$slot.find('.tlw-hq-button').removeClass('active');
		$slot.find('.tlw-materia').removeClass('active');
		$slot.find('.tlw-materia span').removeClass('active');

		// Build gear
		WardrobeStats.build();

		// drop tooltips
		ttdrop();
	}

	//
	// Toggle HQ items
	//
	togglehq(slot)
	{
		this.slotToAttributes[slot].is_hq = !this.slotToAttributes[slot].is_hq;
		WardrobeStats.build();
	}

	//
	// Attach materia
	//
	activateMateriaSlots(count, slot)
	{
		var $materia = $(`.tlw-slots div[data-slot="${slot}"] .tlw-materia`);

		// remove active states
		$materia.removeClass('active');
		$materia.find('> span').removeClass('active');

		// if materia, make active!
		if (count > 0) {
			// set the materia window active
			$materia.addClass('active');

			// add materia slots
			for (var i = 1; i <= count; i++) {
				$materia.find(`[data-materia="${i}"]`).addClass('active');
			}
		}
	}
}

var WardrobeItems = new WardrobeItemsClass();
