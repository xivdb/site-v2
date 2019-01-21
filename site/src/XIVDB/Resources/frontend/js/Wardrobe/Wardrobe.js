//
// Wardrobe Class
//
class WardrobeClass
{
	constructor()
	{
		this.cssOpenButton = '[data-tool="wardrobe"]';
		this.cssWardrobePanel = '.tool-wardrobe';
		this.cssSearchLinks = '.search-results a';
		this.cssRemoveItem = '.tlw-remove-button';
		this.cssHqItem = '.tlw-hq-button';
		this.cssCloseMateria = '.tlw-materia-close';
		this.cssCloseMateriaManage = '.tlw-materia-manage-close';
		this.cssAttachMateria = '.tlw-materia-window .tlw-materia-hasitems button';
		this.cssStatsWindowRows = '.tlw-stats-window > div';
		this.cssMateriaIcons = '.tlw-materia';
		this.cssMateriaRemove = '.materia-manage-list button[data-id]';
		this.cssSaveButton = 'button#gs-save-btn';
		this.cssLoadButton = 'button#gs-load-btn';

		this.loadedGearsets = {};
		this.setIsLoading = false;
		this.activeGearset = null;
	}

	//
	// Watch for events
	//
	watch()
	{
		// wardrobe icon clicked, which either opens
		// or closes the tool, based on visibility
		$('html').on('click', this.cssOpenButton, event => {
			$(this.cssWardrobePanel).hasClass('open') ? WardrobeUI.close() : WardrobeUI.open();
		});

		// listen on search results being clicked, if the wardrobe is open, prevent going
		// to the page as it could of been accidental
		$('html').on('click', this.cssSearchLinks, event => {
			if ($(this.cssWardrobePanel).hasClass('open')) {
				event.preventDefault();
			}
		});

		// on clicking remove an item
		$('html').on('click', this.cssRemoveItem, event => {
			var slot = $(event.currentTarget).parents('[data-slot]').attr('data-slot');
			WardrobeItems.unequip(slot);
		});

		// on clicking hq an item
		$('html').on('click', this.cssHqItem, event => {
			var slot = $(event.currentTarget).parents('[data-slot]').attr('data-slot');
			$(event.currentTarget).toggleClass('enabled');
			WardrobeItems.togglehq(slot);
		});

		// on closing materia window
		$('html').on('click', this.cssCloseMateria, event => {
			WardrobeMateria.close();
		});

		// on closing materia manager window
		$('html').on('click', this.cssCloseMateriaManage, event => {
			WardrobeMateria.closeManager();
		});

		// on attaching materia to a slot
		$('html').on('click', this.cssAttachMateria, event => {
			var slot = $(event.currentTarget).attr('data-materia-slot');
			WardrobeMateria.attach(slot);
		});

		// mouse over an attribute
		$('html').on('mouseover', this.cssStatsWindowRows, event => {
			var attrId = $(event.currentTarget).attr('data-id');
			WardrobeStats.highlightStat(attrId);
		});

		// mouse leave an attribute
		$('html').on('mouseout', this.cssStatsWindowRows, event => {
			var attrId = $(event.currentTarget).attr('data-id');
			WardrobeStats.highlightStat(attrId, true);
		});

		// clicking materia to manage
		$('html').on('click', this.cssMateriaIcons, event => {
			var slot = $(event.currentTarget).parents('div[data-slot]').attr('data-slot');
			WardrobeMateria.showManager(slot);
		});

		// Removing materia
		$('html').on('click', this.cssMateriaRemove, event => {
			var id = $(event.currentTarget).attr('data-id'),
				slot = $(event.currentTarget).attr('data-slot');

			WardrobeMateria.remove(slot, id);
			$(event.currentTarget).parents('.tlw-materia-attached').remove();
		});

		// on clicking the save button
		$('html').on('click', this.cssSaveButton, event => {
			$('.tlw-container, .tlw-header').addClass('tlw-blur');
			$('.tlw-save-window').show();
		});

		// close buttons
		$('html').on('click', '#gs-close-btn', event => {
			$('.tlw-container, .tlw-header').removeClass('tlw-blur');
			$('.tlw-save-window').hide(); $('.tlw-load-window').hide();
		});

		// save gearset
		$('html').on('click', '#saveGearset', event => {
			var data = {};

			for(var slot in WardrobeItems.slotToAttributes) {
				var item = WardrobeItems.slotToAttributes[slot];

				// assign slot
				data[slot] = {
					item: item.item_id,
					materia: [],
				};

				// add materia
				if (item.materia) {
					for(var i in item.materia) {
						var attr = item.materia[i];
						data[slot].materia.push(attr.item_id);
					}
				}
			}

			// json stringify
			data = JSON.stringify(data);

			var $response = $('.tlw-response');
			if ($('#gs-name').val().length < 2) {
				$response.html('Please write a longer name!');
				return;
			}

			if ($('#gs-desc').val().length < 2) {
				$response.html('That is a tiny description, expand on it a bit!');
				return;
			}

			this.loading();

			// save
			$.ajax({
				url: '/gearsets/save',
				cache: false,
				data: {
					json: data,
					id: $('#gs-saveid').val(),
					name: $('#gs-name').val(),
					desc: $('#gs-desc').val(),
					type: $('#gs-type').val(),
					classjob: $('#gs-classjob').val(),
				},
				method: 'POST',
				success: (result) => {
					this.load();
					this.loading(true);
				},
				error: (result) => {
					console.log(result);
					this.loading(true);
				},
			});
		});

		// load gearset
		$('html').on('click', '.tlw-loaded-gearsets button[data-setid]', event => {
			var id = $(event.currentTarget).attr('data-setid');
			var set = this.loadedGearsets[id];

			if (set.data.length == 0) {
				console.log('gs empty');
				return;
			}

			this.activeGearset = set;

			// reset
			this.reset();
			this.loadingGearset(set.name);

			// load data
			$('#gs-saveid').val(id);
			$('#gs-name').val(set.name);
			$('#gs-desc').val(set.description);
			$('#gs-type').val(set.type);
			$('#gs-classjob').val(set.class.id);

			// close load window
			$('.tlw-container, .tlw-header').removeClass('tlw-blur');
			$('.tlw-save-window').hide(); $('.tlw-load-window').hide();

			// Load the gearset
			WardrobeLoader.load(set.data);
		});

		// on clicking the reset button
		$('html').on('click', '#gs-reset-btn', event => {
			this.reset();
			this.activeGearset = null;
		});

		// on clicking shopping cart button
		$('html').on('click', '#gs-cart-btn', event => {
			var items = WardrobeItems.slotToItem;

			// if no items, do nothing
			// TODO : Improve this
			if (Object.size(items) == 0) {
				return;
			}

			// build id list
			var list = {};
			for(var i in items) {
				var id = items[i].id;

				if (list[id]) {
					list[id] += 1;
					continue;
				}

				list[id] = 1;
			}

			// build string
			var string = [];
			for(var id in list) {
				var qty = list[id];

				string.push(`${id}x${qty}`);
			}

			// if ids are above 0
			if (string.length > 0) {
				string = string.join('-');
				var url = `/shopping-cart/${string}`;
				window.open(url, '_blank');
			}
		});

		// on clicking menu button
		$('html').on('click', '[data-action="gearsets"]', (event) => {
			$('.dropdown-container').hide();
			WardrobeUI.open();
		});
	}

	//
	// Reset!
	//
	reset()
	{
		$('#gs-saveid').val('');
		$('#gs-name').val('');
		$('#gs-desc').val('');
		$('#gs-type').val('');
		$('#gs-classjob').val('');

		WardrobeItems.reset()
		WardrobeStats.reset();
		WardrobeMateria.reset();

		$('.tlw-slots').each((i, element) => {
			$(element).find('.tlw-remove-button, .tlw-hq-button').removeClass('active');
			$(element).find('.tlw-slot').html('').attr('data-equipped', 0);
			$(element).find('.tlw-materia').removeClass('active');
			$(element).find('.tlw-materia span')
				.removeClass('active').css({ 'background-image': 'none' })
				.removeAttr('data-tt').removeAttr('data-materia-id');
		});

		// Generate new stats
		WardrobeStats.build();
	}

	//
	// Is wardrobe open?
	//
	isOpen()
	{
		return $(this.cssWardrobePanel).hasClass('open');
	}

	//
	// Load gearsets
	//
	load()
	{
		// only load gearsets when on app
		if (APP != 'app') {
			return;
		}

		var $loaded = $('.tlw-loaded-gearsets');
		$loaded.html('');

		this.loading();

		// save
		$.ajax({
			url: '/gearsets/load',
			cache: false,
			success: (results) => {
				this.loading(true);

				if (results) {
					for(var i in results) {
						var set = results[i];
						this.loadedGearsets[set.id] = set;
						$loaded.append(`
							<div>
								<button data-setid="${set.id}">
									<img src="/img/classes/set2/${set.class.icon}.png" height="20">
									${set.name}
								</button>
							</div>
						`);
					}
				}
			},
			error: (result) => {
				console.log(result);
			},
		});
	}

	//
	// Toggle loading
	//
	loading(hide)
	{
		if (this.setIsLoading) {
			return;
		}

		if (hide) {
			$('.tlw-loading').hide();
			return;
		}

		$('.tlw-loading').show();
	}

	//
	// Toggle loading set
	//
	loadingGearset(name)
	{
		if (!name) {
			$('.tlw-loading-set').hide();
			this.setIsLoading = false;
			return;
		}

		this.setIsLoading = true;
		$('.tlw-loading-set').find('em').html(name);
		$('.tlw-loading-set').show();
	}
}

// Watch for events
var Wardrobe = new WardrobeClass();
$(() => {
	Wardrobe.watch();
	Wardrobe.load();
 });
